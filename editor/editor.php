<?php

defined('ABSPATH') || exit;

// presets
if (iwptp_preset__required()) {
    echo wp_kses(iwptp_presets__get_grid_markup(), iwptp_allowed_html_tags());
    return;
}

require_once "left-sidebar.php";
require_once "live-preview-modal.php";

?>
<div id="iwptp-loading" class="iwptp-loading">
    <?php esc_html_e('Loading ...', 'ithemeland-woocommerce-product-table-pro-lite') ?>
</div>
<div id="iwptp-main">
    <input type="hidden" id="iwptp-last-modal-opened" value="">
    <div id="iwptp-product-table-shortcode" style="opacity: 0; position: fixed; top:0; z-index:-99;"><?php echo esc_html('[it_product_table id="' . intval($post->ID) . '"]'); ?></div>
    <div class="iwptp-body">
        <div class="iwptp-tabs iwptp-tabs-main iwptp-editor" iwptp-model-key="data">
            <div class="iwptp-tabs-navigation">
                <nav class="iwptp-tabs-navbar">
                    <ul class="iwptp-tabs-list" data-content-id="iwptp-main-tabs-contents">
                        <li><a class="selected" data-content="query" data-type="main-tab" href="#"><span class="dashicons dashicons-database"></span><?php esc_html_e('Query', 'ithemeland-woocommerce-product-table-pro-lite'); ?></a></li>
                        <li>
                            <a data-content="columns" data-type="main-tab" class="iwptp-columns-has-sub-tab" href="#"><span class="dashicons dashicons-editor-table"></span> <?php esc_html_e('Columns', 'ithemeland-woocommerce-product-table-pro-lite'); ?> <i style="margin-top: -5px;" class="dashicons dashicons-arrow-down"></i></a>
                            <ul class="iwptp-columns-sub-tabs">
                                <li>
                                    <a class="iwptp-columns-sub-tab-item" href="javascript:;" type="button" data-iwptp-device="laptop">
                                        <img src="<?php echo esc_url(IWPTPL_PLUGIN_URL . '/assets/feather/square.svg'); ?>">
                                        <?php esc_html_e('Laptop', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                                    </a>
                                </li>
                                <li>
                                    <a class="iwptp-columns-sub-tab-item" href="javascript:;" type="button" data-iwptp-device="tablet">
                                        <img src="<?php echo esc_url(IWPTPL_PLUGIN_URL . '/assets/feather/tablet.svg'); ?>">
                                        <?php esc_html_e('Tablet', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                                    </a>
                                </li>
                                <li>
                                    <a class="iwptp-columns-sub-tab-item" href="javascript:;" type="button" data-iwptp-device="phone">
                                        <img src="<?php echo esc_url(IWPTPL_PLUGIN_URL . '/assets/feather/smartphone.svg'); ?>">
                                        <?php esc_html_e('Phone', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li><a data-content="navigation" data-type="main-tab" href="#"><span class="dashicons dashicons-layout"></span><?php esc_html_e('Navigation', 'ithemeland-woocommerce-product-table-pro-lite'); ?></a></li>
                        <li><a data-content="settings" data-type="main-tab" href="#"><span class="dashicons dashicons-admin-settings"></span><?php esc_html_e('Settings', 'ithemeland-woocommerce-product-table-pro-lite'); ?></a></li>
                        <li>
                            <a data-content="style" data-type="main-tab" class="iwptp-style-has-sub-tab" href="#"><span class="dashicons dashicons-admin-customizer"></span><?php esc_html_e('Style', 'ithemeland-woocommerce-product-table-pro-lite'); ?> <i style="margin-top: -5px;" class="dashicons dashicons-arrow-down"></i></a>
                            <ul class="iwptp-style-sub-tabs">
                                <li>
                                    <a class="iwptp-style-sub-tab-item" href="javascript:;" type="button" data-section-id="presets">
                                        <?php esc_html_e('Presets', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                                    </a>
                                </li>
                                <li>
                                    <a class="iwptp-style-sub-tab-item" href="javascript:;" type="button" data-section-id="laptop">
                                        <?php esc_html_e('Laptop', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                                    </a>
                                </li>
                                <li>
                                    <a class="iwptp-style-sub-tab-item" href="javascript:;" type="button" data-section-id="tablet">
                                        <?php esc_html_e('Tablet', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                                    </a>
                                </li>
                                <li>
                                    <a class="iwptp-style-sub-tab-item" href="javascript:;" type="button" data-section-id="phone">
                                        <?php esc_html_e('Phone', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                                    </a>
                                </li>
                                <li>
                                    <a class="iwptp-style-sub-tab-item" href="javascript:;" type="button" data-section-id="navigation">
                                        <?php esc_html_e('Navigation', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                                    </a>
                                </li>
                                <li>
                                    <a class="iwptp-style-sub-tab-item" href="javascript:;" type="button" data-section-id="mini-cart">
                                        <?php esc_html_e('Mini cart', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </nav>
                <div class="iwptp-tabs-right-side">
                    <div class="iwptp-editor-save-table-clear"></div>
                    <div class="iwptp-editor-save-table" style="float: right;">
                        <form class="iwptp-save-data" action="iwptp_save_table_settings" method="post" style="float: right; margin-right: 10px;">
                            <input name="post_id" type="hidden" value="<?php echo intval($post->ID); ?>" />
                            <input name="nonce" type="hidden" value="<?php echo esc_attr(wp_create_nonce("iwptp")); ?>">
                            <input name="title" type="hidden" value="<?php echo (!empty($post->post_title) ? esc_attr($post->post_title) : esc_attr__("Untitled table", "ithemeland-woocommerce-product-table-pro-lite")); ?>" />
                            <button type="submit" class="iwptp-editor-save-button iwptp-button iwptp-button-sm iwptp-button-blue iwptp-float-right iwptp-mr5" title="Save"><i style="margin-top: -2px;" class="lni lni-save"></i></button>
                        </form>
                    </div>
                    <button type="button" id="iwptp-editor-preview" data-toggle="modal" data-target="#iwptp-modal-live-preview" class="iwptp-button iwptp-button-sm iwptp-button-transparent" style="background: #b017f2; border-color: #8528ad; color: #fff;" value="<?php echo intval($post->ID); ?>" title="Live Preview">
                        <i class="dashicons dashicons-visibility"></i>
                    </button>
                    <button type="button" class="iwptp-button iwptp-button-sm iwptp-button-transparent iwptp-product-table-shortcode-copy" title="<?php echo esc_html('[it_product_table id="' . intval($post->ID) . '"]'); ?>">
                        <i class="dashicons dashicons-shortcode"></i>
                        <span class="iwptp-copied"><?php esc_html_e('Copied to Clipboard', 'ithemeland-woocommerce-product-table-pro-lite'); ?></span>
                    </button>
                    <input type="text" class="iwptp-table-title" style="border-bottom: 1p #e3e3e3 solid;" name="" value="<?php echo (!empty($post->post_title) ? esc_attr($post->post_title) : esc_attr__("Untitled table", "ithemeland-woocommerce-product-table-pro-lite")); ?>" placeholder="Enter Title" />
                </div>
            </div>
            <div id="iwptp-main-tabs-contents" class="iwptp-tabs-contents">
                <div class="iwptp-tab-content-item iwptp-editor-tab-products iwptp-tab-content" data-iwptp-tab="products" iwptp-model-key="query" data-content="query">
                    <?php require_once 'partials/query.php' ?>
                </div>

                <!-- columns tab -->
                <div class="iwptp-tab-content-item iwptp-editor-tab-columns iwptp-tab-content" data-iwptp-tab="columns" iwptp-model-key="columns" data-content="columns">
                    <div class="iwptp-100p">
                        <div>
                            <?php
                            $icons = [
                                'laptop' => IWPTPL_PLUGIN_URL . '/assets/feather/square.svg',
                                'tablet' => IWPTPL_PLUGIN_URL . '/assets/feather/tablet.svg',
                                'phone' => IWPTPL_PLUGIN_URL . '/assets/feather/smartphone.svg',
                            ];

                            foreach (array('laptop', 'tablet', 'phone') as $device) {
                            ?>
                                <!-- <?php echo esc_html($device) ?> -->
                                <div class="iwptp-editor-columns-container iwptp-sortable" style="display: <?php echo ($device == 'laptop') ? 'block' : 'none'; ?>;" data-iwptp-device="<?php echo esc_attr($device); ?>" iwptp-model-key="<?php echo esc_attr($device); ?>" iwptp-connect-with="[iwptp-controller='device_columns']" iwptp-controller="device_columns">
                                    <h2 class="iwptp-editor-light-heading iwptp-editor-light-heading-fixed">
                                        <span class="iwptp-columns-section-title">
                                            <img class="<?php echo esc_attr($device); ?>" src="<?php echo esc_url($icons[$device]); ?>">
                                            <?php echo esc_html(ucfirst($device)); ?>
                                        </span>
                                        <div class="iwptp-column-links"></div>
                                        <div style="float: right; width: 21%">
                                            <div class="iwptp-device-columns-toggle">
                                                <a href="#" class="iwptp-device-columns-toggle__expand"><?php esc_html_e('Expand', 'ithemeland-woocommerce-product-table-pro-lite'); ?></a> /
                                                <a href="#" class="iwptp-device-columns-toggle__contract"><?php esc_html_e('Contract', 'ithemeland-woocommerce-product-table-pro-lite'); ?></a>
                                                all
                                            </div>
                                            <?php if ($device == 'tablet') : ?>
                                                <div class="iwptp-device-import-columns">
                                                    <a href="javascript:;" id="iwptp-import-laptop-cols" title="Import laptop cols"><i class="dashicons dashicons-download"></i></a>
                                                </div>
                                            <?php endif; ?>
                                            <?php if ($device == 'phone') : ?>
                                                <div class="iwptp-device-import-columns">
                                                    <a href="javascript:;" id="iwptp-import-tablet-cols" title="Import tablet cols"><i class="dashicons dashicons-download"></i></a>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </h2>
                                    <?php require 'partials/columns.php'; ?>
                                </div>
                            <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>

                <!-- settings tab -->
                <div class="iwptp-tab-content-item iwptp-editor-tab-settings iwptp-tab-content" data-iwptp-tab="settings" iwptp-model-key="settings" data-content="settings">
                    <?php require_once 'partials/settings.php' ?>
                </div>

                <!-- style tab -->
                <div class="iwptp-tab-content-item iwptp-editor-tab-style iwptp-tab-content" data-iwptp-tab="style" iwptp-model-key="style" data-content="style">
                    <?php require_once 'partials/style.php' ?>
                </div>

                <!-- navigation tab -->
                <div class="iwptp-tab-content-item iwptp-editor-tab-navigation iwptp-tab-content" data-iwptp-tab="navigation" iwptp-model-key="navigation" iwptp-initial-data="navigation" data-content="navigation">
                    <div class="iwptp-100p">
                        <?php require_once 'partials/navigation.php' ?>
                    </div>
                </div>
            </div>
            <!-- save data -->

        </div>
        <div class="iwptp-created-by">
            <a href="https://ithemelandco.com" target="_blank">Created by iThemelandCo</a>
        </div>
    </div>
    <!-- footer -->
    <div class="iwptp-editor-footer"></div>
</div>
<!-- icon templates -->
<?php
$icons = array('trash', 'sliders', 'copy', 'x', 'check', 'arrow-left');
foreach ($icons as $icon_name) {
?>
    <script type="text/template" id="iwptp-icon-<?php echo esc_attr($icon_name); ?>">
        <?php echo wp_kses(iwptp_icon($icon_name), iwptp_allowed_html_tags()); ?>
    </script>
<?php
}
?>

<!-- element partials -->
<?php require_once 'partials/element-editor/element-partials.php'; ?>

<!-- required js vars -->
<?php
$attributes = wc_get_attribute_taxonomies();
?>
<script>
    iwptp_attributes = <?php echo wp_json_encode($attributes) ?>;
</script>
<script>
    var iwptp_icons_url = "<?php echo esc_url(IWPTPL_PLUGIN_URL . '/assets/feather'); ?>";
</script>

<!-- embedded style -->
<?php
$svg_cross_path = plugin_dir_url(__FILE__) . 'assets/css/cross.svg';
?>
<style media="screen">
    .iwptp-block-editor-lightbox-screen {
        cursor: url('<?php echo esc_url($svg_cross_path); ?>'), auto;
    }
</style>