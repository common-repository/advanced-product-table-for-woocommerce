<?php

add_action('admin_enqueue_scripts', 'iwptp_presets_enqueue_scripts', 99);

function iwptp_presets_enqueue_scripts()
{
    if (!iwptp_preset__required()) {
        return;
    }

    wp_enqueue_script(
        'iwptp-presets',
        IWPTPL_PLUGIN_URL . 'presets/js.js',
        array('jquery'),
        IWPTPL_VERSION,
        true
    );

    wp_enqueue_style(
        'iwptp-presets',
        IWPTPL_PLUGIN_URL . 'presets/css.css',
        false,
        IWPTPL_VERSION
    );
}

// presets grid markup
function iwptp_presets__get_grid_markup()
{
    $table_presets = iwptp_get_table_presets();
    $style_presets = iwptp_get_style_presets();

    ob_start();
    require_once IWPTPL_PLUGIN_PATH . "editor/header.php";
?>
    <div class="iwptp-page-content" style="display: inline-block;">
        <div class="iwptp-preset-outer">
            <h2 class="iwptp-new-template-section-title">
                <?php esc_html_e('Predefined Templates', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
            </h2>
            <div class="iwptp-presets">
                <div class="iwptp-presets__item iwptp-predefined-preset-item selected" data-preset-slug="blank">
                    <div class="iwptp-presets__item__image">
                        <img src="<?php echo esc_url(IWPTPL_IMAGES_URL . 'blank.png'); ?>">
                    </div>
                    <span class="iwptp-presets__item__name">
                        <?php esc_html_e('Blank', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                        <span class="iwptp-presets__item__name__selected"><?php esc_html_e('Selected', 'ithemeland-woocommerce-product-table-pro-lite'); ?></span>
                        <span class="iwptp-presets__item__name__select"><?php esc_html_e('Select', 'ithemeland-woocommerce-product-table-pro-lite'); ?></span>
                    </span>
                </div>
                <?php
                if (!empty($table_presets)) :
                    foreach ($table_presets as $slug => $table_preset) :
                ?>
                        <div class="iwptp-presets__item iwptp-predefined-preset-item iwptp-disabled">
                            <div class="iwptp-presets__item__image">
                                <img src="<?php echo esc_url($table_preset['image_url']); ?>">
                            </div>
                            <span class="iwptp-presets__item__name">
                                <?php echo esc_html($table_preset['name']); ?>
                            </span>
                            <div class="iwptp-presets-pro-badge">
                                <span><?php esc_html_e('Premium Version', 'ithemeland-woocommerce-product-table-pro-lite'); ?></span>
                            </div>
                        </div>
                <?php
                    endforeach;
                endif;
                ?>
            </div>
        </div>

        <div class="iwptp-preset-outer">
            <h2 class="iwptp-new-template-section-title">
                <?php esc_html_e('Colored Templates', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
            </h2>
            <div class="iwptp-presets">
                <div class="iwptp-presets__item iwptp-colored-preset-item selected" data-preset-slug="blank">
                    <div class="iwptp-presets__item__image">
                        <img src="<?php echo esc_url(IWPTPL_IMAGES_URL . 'blank.png'); ?>">
                    </div>
                    <span class="iwptp-presets__item__name">
                        <?php esc_html_e('Blank', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                        <span class="iwptp-presets__item__name__selected"><?php esc_html_e('Selected', 'ithemeland-woocommerce-product-table-pro-lite'); ?></span>
                        <span class="iwptp-presets__item__name__select"><?php esc_html_e('Select', 'ithemeland-woocommerce-product-table-pro-lite'); ?></span>
                    </span>
                </div>

                <?php
                if (!empty($style_presets)) :
                    foreach ($style_presets as $slug => $style_preset) :
                ?>
                        <div class="iwptp-presets__item iwptp-colored-preset-item <?php echo (empty($style_preset['data'])) ? 'iwptp-disabled' : ''; ?>" <?php if ((!empty($style_preset['data']))) : ?> data-preset-slug="<?php echo esc_attr($slug); ?>" <?php endif; ?>>
                            <div class="iwptp-presets__item__image">
                                <img src="<?php echo esc_url($style_preset['image_url']); ?>">
                                <?php if (!empty($style_preset['data'])) : ?>
                                    <div class="iwptp-presets__item__image__cover" data-toggle="modal" data-target="#iwptp-modal-preset-image-preview">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                            <path d="M13 10h-3v3h-2v-3h-3v-2h3v-3h2v3h3v2zm8.172 14l-7.387-7.387c-1.388.874-3.024 1.387-4.785 1.387-4.971 0-9-4.029-9-9s4.029-9 9-9 9 4.029 9 9c0 1.761-.514 3.398-1.387 4.785l7.387 7.387-2.828 2.828zm-12.172-8c3.859 0 7-3.14 7-7s-3.141-7-7-7-7 3.14-7 7 3.141 7 7 7z" />
                                        </svg>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <span class="iwptp-presets__item__name">
                                <?php echo esc_html($style_preset['name']); ?>
                                <?php if (!empty($style_preset['data'])) : ?>
                                    <span class="iwptp-presets__item__name__selected"><?php esc_html_e('Selected', 'ithemeland-woocommerce-product-table-pro-lite'); ?></span>
                                    <span class="iwptp-presets__item__name__select"><?php esc_html_e('Select', 'ithemeland-woocommerce-product-table-pro-lite'); ?></span>
                                <?php endif; ?>
                            </span>

                            <?php if (empty($style_preset['data'])) : ?>
                                <div class="iwptp-presets-pro-badge">
                                    <span><?php esc_html_e('Premium Version', 'ithemeland-woocommerce-product-table-pro-lite'); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                <?php
                    endforeach;
                endif;
                ?>
            </div>
        </div>

        <input type="hidden" id="iwptp-predefined-template" value="blank">
        <input type="hidden" id="iwptp-colored-template" value="blank">
        <button type="button" class="iwptp-button iwptp-button-blue iwptp-new-template-get-start-button"><i class="dashicons dashicons-arrow-right-alt2"></i> <?php esc_html_e('Get Start', 'ithemeland-woocommerce-product-table-pro-lite'); ?></button>
    </div>
    <input type="hidden" id="iwptp-last-modal-opened" value="">
<?php
    require "image-preview-modal.php";

    require_once IWPTPL_PLUGIN_PATH . "editor/footer.php";

    return ob_get_clean();
}

// set preset required meta flag
add_action('admin_init', 'iwptp_presets__set_preset_required_meta_flag');
function iwptp_presets__set_preset_required_meta_flag()
{
    if (!iwptp_preset__is_table_editor()) {
        return;
    }

    // if table is new (no data) then set preset requrired meta flag
    $post_id = intval($_GET['post_id']);
    $table_data = get_post_meta($post_id, 'iwptp_data', true);

    if (!$table_data) {
        update_post_meta($post_id, 'iwptp_preset_required', true);
    }
}


// duplicate a preset to table
add_action('admin_init', 'iwptp_presets__duplicate_preset_to_table');
function iwptp_presets__duplicate_preset_to_table()
{
    if (!iwptp_preset__is_table_editor()) {
        return;
    }

    if (empty($_GET['iwptp_predefined_preset']) || empty($_GET['iwptp_predefined_preset'])) {
        return;
    }

    $post_id = intval($_GET['post_id']);
    $predefined_slug = sanitize_text_field($_GET['iwptp_predefined_preset']);
    $colored_slug = sanitize_text_field($_GET['iwptp_colored_preset']);

    // preset already applied on this table
    if (!iwptp_preset__required($post_id)) {
        return;
    }

    // apply the preset
    update_post_meta($post_id, 'iwptp_preset_required', false); // turn off 'preset required' flag

    wp_update_post(array(
        'ID' => $post_id,
        'post_title' => $predefined_slug == 'blank' ? 'New table' : ucwords(str_replace('-', ' ', $predefined_slug)),
        'post_status' => 'publish',
    ));

    // predefined
    $table_data = iwptp_get_default_table_data();

    if ($predefined_slug !== 'blank') {
        // get data from json preset file
        $table_preset = iwptp_get_table_preset($predefined_slug);
        $table_data = $table_preset['data'];
        iwptp_new_ids($table_data);
        $table_data['id'] = $post_id;

        update_post_meta($post_id, 'iwptp_preset_applied__message_required', true);
        update_post_meta($post_id, 'iwptp_preset_applied__slug', $predefined_slug);
    }

    if ($colored_slug !== 'blank') {
        $style_preset = iwptp_get_style_preset($colored_slug);
        if (!empty($style_preset) && !empty($style_preset['data'])) {
            $table_data['style'] = $style_preset['data'];
        }
    }

    if (!empty($table_data)) {
        update_post_meta($post_id, 'iwptp_data', addslashes(wp_json_encode($table_data)));
    }
}

function iwptp_preset__maybe_display_message($post_id = false)
{
    if (!$post_id) {
        if (empty($_GET['post_id'])) {
            return false;
        }
        $post_id = intval($_GET['post_id']);
    }

    if (!get_post_meta($post_id, 'iwptp_preset_applied__message_required', true)) {
        return false;
    }

    $preset_slug = get_post_meta($post_id, 'iwptp_preset_applied__slug', true);
    $preset_name = ucwords(str_replace('-', ' ', $preset_slug));
?>
    <div class="iwptp-preset-applied-message">
        <span class="iwptp-preset-applied-message__dismiss"><?php echo wp_kses(iwptp_icon('x'), iwptp_allowed_html_tags()) ?></span>
        <h2 class="iwptp-preset-heading">Preset applied!</h2>
        <ul class="iwptp-preset-applied-message__list">
            <li>You selected the '<?php echo esc_html($preset_name); ?>' preset.</li>
            <li>Your new product table is ready 👍</li>
            <li>You can show it on your website right now. <br>
                <input type="text" class="iwptp-preset-applied-message__shortcode" value="<?php echo esc_attr('[it_product_table id="' . $post_id . '"]'); ?>">
                <button class="iwptp-preset-applied-message__shortcode-copy-button">Copy</button> <br>
                Just copy the above shortcode and paste it on a <a href="/wp-admin/post-new.php?post_type=page&iwptp_id=<?php echo esc_attr($post_id); ?>" target="_blank">new page <?php echo wp_kses(iwptp_icon('external-link', 'iwptp-preset-applied-message__new-page-icon'), iwptp_allowed_html_tags()); ?></a>.<br>
            </li>
            <li>You can fully customize your new product table using the table editor.<br>
                This includes category, styling, columns and filters.<br>
                <a href="https://www.youtube.com/watch?v=xoR97WwUmqA" target="_blank"><?php echo wp_kses(iwptp_icon('youtube', 'iwptp-preset-applied-message__youtube-icon'), iwptp_allowed_html_tags()); ?> Video: How to customize my new product table</a>
            </li>
        </ul>
    </div>
<?php

    update_post_meta($post_id, 'iwptp_preset_applied__message_required', false);

    return true;
}

// check if presets required
function iwptp_preset__required($post_id = false)
{
    if (!$post_id) {
        if (empty($_GET['post_id'])) {
            return false;
        }
        $post_id = intval($_GET['post_id']);
    }

    return get_post_meta($post_id, 'iwptp_preset_required', true);
}

function iwptp_preset__is_table_editor()
{
    return  !empty($_GET['post_type']) &&
        $_GET['post_type'] === 'iwptp_product_table' &&
        !empty($_GET['page']) &&
        $_GET['page'] === 'iwptp-edit';
}
