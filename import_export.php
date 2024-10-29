<?php
add_action('admin_notices', 'iwptp_handle_import_export_errors');
function iwptp_handle_import_export_errors()
{
    if (empty($GLOBALS['iwptp_import_export_error'])) {
        if (!empty($_REQUEST['iwptp_import_export_nonce'])) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
?>
            <div class="notice notice-success">
                <p><strong>IWPTPL:</strong> Uploaded data was imported successfully!</p>
            </div>
    <?php
        }

        return;
    }
    ?>
    <div class="notice notice-error">
        <p><strong>IWPTPL Error:</strong> Import failed! <?php echo esc_html($GLOBALS['iwptp_import_export_error']); ?></p>
    </div>
<?php
}

add_action('admin_init', 'iwptp_handle_import_export');
function iwptp_handle_import_export()
{
    if (
        empty($_POST['iwptp_import_export_nonce']) ||
        !wp_verify_nonce($_POST['iwptp_import_export_nonce'], 'iwptp_import_export') ||
        empty($_POST['iwptp_context']) ||
        empty($_POST['iwptp_action'])
    ) {
        return;
    }

    $errors = [];

    // export
    if ($_POST['iwptp_action'] === 'export') {

        // export tables
        if ($_POST['iwptp_context'] === 'tables') {
            $filename = 'iwptp_tables.json';
            $data = array(
                'context' => 'tables'
            );

            $args = array(
                'posts_per_page' => -1,
                'post_type' => 'iwptp_product_table',
                'post_status' => 'publish',
            );

            if (!empty($_REQUEST['iwptp_export_id'])) {
                $args['post__in'] = array_map('intval', explode(',', $_REQUEST['iwptp_export_id']));
            }

            $query = new WP_Query($args);

            if ($query->have_posts()) {
                while ($query->have_posts()) {
                    $query->the_post();

                    $id = get_the_id();
                    $table_settings = iwptp_get_table_data($id);
                    $table_settings['title'] = get_the_title();
                    if (!empty($table_settings['query']['category'])) {
                        // note slugs to help locate them during import
                        $table_settings['query']['category_export'] = [];
                        foreach ($table_settings['query']['category'] as $category_id) {
                            $term = get_term($category_id, 'product_cat');
                            $table_settings['query']['category_export'][] = $term->slug;
                        }
                    }
                    $data[] = $table_settings;
                }
            }

            // export settings
        } else {
            $filename = 'iwptp_settings.json';
            $data = iwptp_get_settings_data();

            // note each table's slug, to use it during import as IDs will be useless
            if (!empty($data['archive_override'])) {
                iwptp_export_helper__replace_table_ids($data['archive_override']);
            }

            $data['context'] = 'settings';
        }

        $content = wp_json_encode($data);

        $handle = fopen($filename, 'w'); //phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen
        fwrite($handle, $content); //phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite
        fclose($handle); //phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . $filename);
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filename));
        readfile($filename); //phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_readfile
        ignore_user_abort(true);
        wp_delete_file($filename);

        exit;

        // import
    } else {

        if (
            empty($_FILES['iwptp_import_file']) ||
            $_FILES['iwptp_import_file']['error'] !== UPLOAD_ERR_OK ||
            !is_uploaded_file($_FILES['iwptp_import_file']['tmp_name'])
        ) {
            $GLOBALS['iwptp_import_export_error'] = 'Please upload the import file using the import form.';
            return;
        }

        $content = trim(file_get_contents($_FILES['iwptp_import_file']['tmp_name'])); //phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

        if (!$content) {
            $GLOBALS['iwptp_import_export_error'] = 'The import file was empty.';
            return;
        }

        $data = iwptp_sanitize_array(json_decode($content, true));

        if (json_last_error() !== JSON_ERROR_NONE) {
            $GLOBALS['iwptp_import_export_error'] = 'The import file is corrupted.';
            return;
        }

        // import tables
        if ($_POST['iwptp_context'] === 'tables') {

            if ($data['context'] !== 'tables') {
                $GLOBALS['iwptp_import_export_error'] = 'Import failed! You are using the wrong file to import table data here. You need to use the "iwptp_tables.json" file to import tables here. Instead you are using the "iwptp_settings.json" file which is meant to import the plugin\'s overall settings. Please use the correct file and try again.';
                return;
            } else {
                unset($data['context']);
            }

            foreach ($data as $table_settings) {
                $id = wp_insert_post(array(
                    'post_title'  => wp_strip_all_tags(!empty($table_settings['title']) ? $table_settings['title'] : ''),
                    'post_status' => 'publish',
                    'post_type'   => 'iwptp_product_table',
                ));

                if (!empty($table_settings['query']['category_export'])) {
                    $table_settings['query']['category'] = [];
                    foreach ($table_settings['query']['category_export'] as $cat_slug) {
                        if ($term = get_term_by('slug', $cat_slug, 'product_cat')) {
                            $table_settings['query']['category'][] = (int) $term->term_taxonomy_id;
                        }
                    }
                }

                if ($id && !is_wp_error($id)) {
                    update_post_meta($id, 'iwptp_data', addslashes(wp_json_encode($table_settings)));
                }
            }

            // import settings
        } else {
            if ($data['context'] !== 'settings') {
                $GLOBALS['iwptp_import_export_error'] = 'Import failed! You are using the wrong file to import plugin settings here. You need to use the "iwptp_settings.json" file to import the overall settings here. Instead you are using the "iwptp_tables.json" file which is meant to import the tables. Please use the correct file and try again.';
                return;
            } else {
                unset($data['context']);
            }

            // use the table slugs to discover IDs
            if (!empty($data['archive_override'])) {
                iwptp_import_helper__replace_table_slugs($data['archive_override']);
            }

            $content = addslashes(wp_json_encode($data));
            update_option('iwptp_settings', apply_filters('iwptp_global_settings', $content));
        }
    }
}

// recursively iterate over settings and replace table IDs with slugs in _export keys
function iwptp_export_helper__replace_table_ids(&$arr)
{
    $remove = [];
    foreach ($arr as $key => &$val) {
        if (
            in_array($key, array('default', 'table_id', 'search', 'shop')) &&
            is_numeric($val)
        ) {
            $post = get_post((int) $val);
            $arr[$key . '_export'] = $post->post_name;

            $remove[] = $key;
        } else if (gettype($val) == 'array') {
            iwptp_export_helper__replace_table_ids($val);
        }
    }

    foreach ($remove as $remove_key) {
        unset($arr[$remove_key]);
    }
}

// recursively iterate over settings and replace table slugs in _export keys with IDs
function iwptp_import_helper__replace_table_slugs(&$arr)
{
    $remove = [];
    foreach ($arr as $key => &$val) {
        if (substr($key, -7) === '_export') {
            if ($post = get_page_by_path($val, OBJECT, 'iwptp_product_table')) {
                $arr[substr($key, 0, -7)] = $post->ID;
            }
            $remove[] = $key;
        } else if (gettype($val) == 'array') {
            iwptp_import_helper__replace_table_slugs($val);
        }
    }

    foreach ($remove as $remove_key) {
        unset($arr[$remove_key]);
    }
}
