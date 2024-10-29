<div id="iwptp-editor-left-sidebar" class="">
    <div class="iwptp-editor-left-sidebar-wrapper">
        <div class="iwptp-editor-left-sidebar-plugin-name">
            <span><?php esc_html_e('iT Product Table', 'ithemeland-woocommerce-product-table-pro-lite'); ?></span>
            <i class="iwptp-editor-left-sidebar-help dashicons dashicons-editor-help" title="Help"></i>
            <div class="iwptp-dark-theme-switch">
                <label class="iwptp-switch">
                    <?php $theme_name = get_option('iwptp_editor_theme', 'iwptp-dark-theme'); ?>
                    <input type="checkbox" id="iwptp-is-dark-theme" <?php echo ($theme_name == 'iwptp-dark-theme') ? 'checked="checked"' : ''; ?>>
                    <span class="iwptp-switch-slider iwptp-switch-round"></span>
                    <span class="light" style="display: <?php echo ($theme_name == 'iwptp-light-theme') ? 'block' : 'hide'; ?>">Light</span>
                    <span class="dark" style="display: <?php echo ($theme_name == 'iwptp-dark-theme') ? 'block' : 'hide'; ?>">Dark</span>
                </label>
            </div>
        </div>
        <div class="iwptp-tabs">
            <div class="iwptp-editor-left-sidebar-middle-content">
                <div class="iwptp-element-settings-container">
                    <div class="iwptp-left-sidebar-help">
                        <div class="iwptp-left-sidebar-help-icon">
                            <i class="dashicons dashicons-editor-help"></i>
                        </div>
                        <?php include "left-sidebar-help.php"; ?>
                    </div>
                    <div class="iwptp-element-settings"></div>
                </div>
            </div>
            <div class="iwptp-left-sidebar-bottom">
                <a href="<?php echo esc_url(admin_url("edit.php?post_type=iwptp_product_table")) ?>" class="iwptp-exit-to-dashboard iwptp-float-left" title="<?php esc_attr_e('Exit', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
                    <i class="dashicons dashicons-arrow-left-alt2"></i>
                </a>
                <button type="button" class="iwptp-full-screen iwptp-float-left" id="iwptp-full-screen" title="<?php esc_attr_e('Show WordPress Menu', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
                    <i class="dashicons dashicons-fullscreen-alt"></i>
                </button>
                <a href="<?php echo esc_url(admin_url("edit.php?post_type=iwptp_product_table&page=iwptp-edit")); ?>" class="iwptp-add-new iwptp-float-left" title="<?php esc_attr_e('Add new', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
                    <i class="dashicons dashicons-plus"></i>
                </a>
                <?php if (!empty($_GET['post_id'])) : ?>
                    <a href="<?php echo esc_url(wp_nonce_url('admin.php?action=iwptp_duplicate_post_as_draft&post=' . intval($_GET['post_id']), 'iwptp_post_nonce', 'duplicate_nonce')); ?>" class="iwptp-duplicate iwptp-float-left" id="iwptp-duplicate" title="<?php esc_attr_e('Duplicate', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
                        <i class="dashicons dashicons-admin-page"></i>
                    </a>
                    <button type="button" class="iwptp-move-to-trash iwptp-float-left" id="iwptp-move-to-trash" title="<?php esc_attr_e('Move to trash', 'ithemeland-woocommerce-product-table-pro-lite'); ?>" data-url="<?php echo esc_url(admin_url("post.php?post=" . intval($_GET['post_id']))); ?>&action=trash&_wpnonce=<?php echo (!empty($trash_nonce)) ? esc_attr($trash_nonce) : ''; ?>">
                        <i class="dashicons dashicons-trash"></i>
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <button type="button" id="iwptp-left-sidebar-toggle" class="iwptp-left-sidebar-toggle"><i class="dashicons dashicons-arrow-left-alt2"></i></button>
</div>