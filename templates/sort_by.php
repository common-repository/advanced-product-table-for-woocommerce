<?php
if (!defined('ABSPATH')) {
    exit;
}

$table_data = iwptp_get_table_data();
$table_id = $table_data['id'];

$field_name_orderby = $table_id . '_orderby';

// get default orderby params
$default_params = iwptp_get_nav_filter('orderby');

// ensure drop down options
if (empty($dropdown_options)) {
    $dropdown_options = json_decode('[{"orderby":"popularity","order":"DESC","meta_key":"","label":"Sort by Popularity"},{"orderby":"rating","order":"DESC","meta_key":"","label":"Sort by Rating"},{"orderby":"price","order":"ASC","meta_key":"","label":"Sort by Price low to high"},{"orderby":"price-desc","order":"DESC","meta_key":"","label":"Sort by Price high to low"},{"orderby":"date","order":"DESC","meta_key":"","label":"Sort by Newness"},{"orderby":"title","order":"ASC","meta_key":"","label":"Sort by Name A - Z"},{"orderby":"title","order":"DESC","meta_key":"","label":"Sort by Name Z - A"}]', true);
}

// maybe add search relevance option
$relevance_option = iwptp_maybe_apply_sortby_relevance();

foreach (iwptp_sanitize_array($_GET) as $key => $val) {
    if (
        strpos($key, $table_id . '_search') !== false &&
        $val
    ) {
        $relevance_option = true;
        break;
    }
}

// and keep it hidden until search is used
if ($relevance_option) {
    // add the option so it can be auto-selected
    $relevance_params = array(
        'label' => __('Relevance', 'woocommerce'),
        'orderby' => 'relevance',
        'order' => 'DESC',
    );

    $settings = iwptp_get_settings_data();
    if (!empty($settings['search_localization']['relevance_label'])) {
        $locale = get_locale();
        $translations = [];
        $translation = false;

        foreach (preg_split('/$\R?^/m', trim($settings['search_localization']['relevance_label'])) as $translation_rule) {
            $array = explode(':', $translation_rule);

            // rule with locale code: translation
            if (!empty($array[1])) {
                $translations[trim($array[0])] = stripslashes(trim($array[1]));

                // rule with just default translation
            } else {
                $translations['default'] = stripslashes(trim($array[0]));
            }
        }

        // maybe use defaults
        if (empty($translations[$locale])) {

            if (!empty($translations['default'])) {
                $translation = $translations['default'];
            } else if (!empty($translations['en_US'])) {
                $translation = $translations['en_US'];
            }
        } else {
            $translation = $translations[$locale];
        }
    }

    if (!empty($translation)) {
        $relevance_params['label'] = $translation;
    }

    $dropdown_options[] = $relevance_params;
    $relevance_index = count($dropdown_options) - 1;

    if (
        empty($_GET[$field_name_orderby]) &&
        empty($table_data['query']['sc_attrs']['search_orderby'])
        // ( // nor search archive with a search orderby
        //   empty( $table_data['query']['sc_attrs']['_archive'] ) ||
        //   empty( $table_data['query']['sc_attrs']['_search'] ) ||
        //   empty( $table_data['query']['sc_attrs']['search_orderby'] )
        // )
    ) {
        $_GET[$field_name_orderby] = 'relevance';
    }
} else if (
    !empty($_GET[$field_name_orderby]) &&
    $_GET[$field_name_orderby] === 'relevance'
) {
    // option to sort by relevance should not exist
    $_GET[$field_name_orderby] = '';
}

$default_index = iwptp_sortby_get_matching_option_index($default_params, $dropdown_options);

// get current orderby

//-- none selected
if (empty($_GET[$field_name_orderby])) {

    $selected_index = $default_index;

    // unique case of sku
    if (
        false === $selected_index &&
        !empty($default_params['meta_key']) &&
        $default_params['meta_key'] == '_sku'
    ) {
        $arr = array(
            'orderby' => $default_params['orderby'] === 'meta_value' ? 'sku' : 'sku_num',
            'order' => $default_params['order'],
        );
        $selected_index = iwptp_sortby_get_matching_option_index($arr, $dropdown_options);
    }

    // matching option found
    if ($selected_index !== false) {
        $current_params = $dropdown_options[$selected_index];

        // no option here matching the default option
    } else {

        // add default option
        $current_params = array(
            'label' => __('Sort by ', 'ithemeland-woocommerce-product-table-pro-lite'),
            'orderby' => $default_params['orderby'],
            'order' => $default_params['order'],
            'meta_key' => $default_params['meta_key'],
        );

        $dropdown_options[] = $current_params;
        $selected_index = count($dropdown_options) - 1;
    }

    //-- user selected
} else {

    $orderby = sanitize_text_field($_GET[$field_name_orderby]);

    //-- -- column sort
    if (substr($orderby, 0, 7) == 'column_') {

        $data = &$GLOBALS['iwptp_table_data'];
        $col_index = (int) substr($orderby, 7);
        $device = empty($_GET[$table_id . '_device']) ? 'laptop' : sanitize_text_field($_GET[$table_id . '_device']);

        $column_sorting = iwptp_get_column_sorting_info($col_index, $device);

        $current_order = sanitize_text_field($_GET[$table_id . '_order']);
        $column_sorting['order'] = $current_order;
        if ($column_sorting['orderby'] == 'price' && strtolower($current_order) == 'desc') {
            $column_sorting['orderby'] = 'price-desc';
        }

        $selected_index = iwptp_sortby_get_matching_option_index($column_sorting, $dropdown_options);

        // matching option found
        if ($selected_index !== false) {
            $current_params = $dropdown_options[$selected_index];

            // no matching option
        } else {

            // add the column as an option

            $label = __('Sort by ', 'ithemeland-woocommerce-product-table-pro-lite');
            $label_prefix = '';

            if (in_array($column_sorting['orderby'], array('meta_value', 'meta_value_num'))) {
                $label_prefix = $column_sorting['meta_key'];
            } else {
                $label_prefix = $column_sorting['orderby'];
            }

            $label_prefix = strtoupper($label_prefix[0]) . substr($label_prefix, 1);

            $current_params = array(
                'label' => $label . $label_prefix,
                'orderby' => $column_sorting['orderby'],
                'order' => $column_sorting['order'],
                'meta_key' => $column_sorting['meta_key'],
            );

            $dropdown_options[] = $current_params;
            $selected_index = count($dropdown_options) - 1;
        }

        //-- -- manual
    } else if (is_numeric($orderby)) {

        $selected_index = $orderby - 1;
        $current_params = $dropdown_options[$selected_index];

        $current_params['filter'] = 'orderby';
        iwptp_update_user_filters($current_params, true);

        //-- -- dropdown
    } else if (substr($orderby, 0, 7) == 'option_') {

        $selected_index = (int) substr($orderby, 7);

        if (empty($dropdown_options[$selected_index])) {
            $dropdown_options[$selected_index] = array(
                'label' => __('Sort by ', 'ithemeland-woocommerce-product-table-pro-lite'),
                'orderby' => $default_params['orderby'],
                'order' => $default_params['order'],
                'meta_key' => $default_params['meta_key'],
            );
        }

        $current_params = $dropdown_options[$selected_index];

        $current_params['filter'] = 'orderby';
        iwptp_update_user_filters($current_params, true);

        //-- -- search relevance
    } else if ($orderby === 'relevance') {
        $selected_index = $relevance_index;
        $current_params = $dropdown_options[$relevance_index];

        $current_params['filter'] = 'orderby';
        iwptp_update_user_filters($current_params, true);
    }
}

if (
    empty($display_type) ||
    (!empty($position) && $position === 'left_sidebar')
) {
    $display_type = 'dropdown';
}

if ($display_type == 'dropdown') {
    $container_html_class = 'iwptp-dropdown iwptp-filter ' . $html_class;
    $heading_html_class = 'iwptp-dropdown-label';
    $options_container_html_class = 'iwptp-dropdown-menu';
    $single_option_container_html_class = 'iwptp-dropdown-option';
} else {
    $container_html_class = 'iwptp-options-row iwptp-filter ' . $html_class;
    $heading_html_class = 'iwptp-options-heading';
    $options_container_html_class = 'iwptp-options';
    $single_option_container_html_class = 'iwptp-option';
}

// heading row
if (empty($heading)) {
    $heading = '';
}

if (!$heading = iwptp_parse_2($heading)) {
    $container_html_class .= ' iwptp-no-heading iwptp-filter-open';
}

if (!empty($accordion_always_open)) {
    $container_html_class .= ' iwptp-filter-open';
}

?>
<div class="<?php echo esc_attr($container_html_class); ?>" data-iwptp-filter="sort_by" data-iwptp-heading_format__op_selected="only_selected">

    <?php
    ob_start();
    ?>

    <!-- options menu -->
    <div class="<?php echo esc_attr($options_container_html_class); ?>">
        <?php
        $selected_label = '';
        foreach ($dropdown_options as $option_index => $option) {

            if ($selected_index == $option_index) {
                $checked = ' checked="checked" ';
                $selected_label = $option['label'];
                $active = ' iwptp-active ';
            } else {
                $checked = '';
                $active = '';
            }

            $value = $option['orderby'] == 'relevance' ? 'relevance' : 'option_' . $option_index;

        ?>
            <label class="<?php echo esc_attr($single_option_container_html_class) . esc_attr($active) . (($default_index === $option_index) ? ' iwptp-default-option' : ''); ?>">
                <input type="radio" name="<?php echo esc_attr($field_name_orderby); ?>" <?php echo esc_attr($checked); ?> value="<?php echo esc_attr($value); ?>" class="iwptp-filter-radio"><span><?php echo esc_html($option['label']); ?></span>
            </label>
        <?php
        }

        ?>
    </div>

    <?php
    $dropdown_menu = ob_get_clean();
    ?>

    <div class="iwptp-filter-heading">
        <!-- label -->
        <span class="<?php echo esc_attr($heading_html_class); ?>">
            <span>
                <?php echo $display_type == 'dropdown' ? esc_html($selected_label) : esc_html($heading); ?>
            </span>
        </span>
        <!-- icon -->
        <?php echo wp_kses(iwptp_icon('chevron-down'), iwptp_allowed_html_tags()); ?>
    </div>

    <?php echo wp_kses($dropdown_menu, iwptp_allowed_html_tags()); ?>

</div>