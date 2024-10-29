<?php

defined('ABSPATH') || exit;

require_once "header.php";
?>

<div id="iwptp-loading" class="iwptp-loading">
    <?php esc_html_e('Loading ...', 'ithemeland-woocommerce-product-table-pro-lite') ?>
</div>

<div class="iwptp-tabs iwptp-tabs-main">
    <div class="iwptp-tabs-navigation">
        <nav class="iwptp-tabs-navbar">
            <ul class="iwptp-tabs-list" data-content-id="iwptp-main-tabs-contents">
                <li>
                    <a data-content="general" data-type="main-tab" href="#" data-el="true" data-disabled="false" class="selected">
                        <?php esc_html_e('General', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                    </a>
                </li>
                <li>
                    <a data-content="localization" data-type="main-tab" href="#" data-el="true" data-disabled="false" class="">
                        <?php esc_html_e('Localization', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    <div id="iwptp-main-tabs-contents" class="iwptp-tabs-contents">
        <div class="iwptp-settings" iwptp-model-key="data">
            <div class="iwptp-tab-content-item" data-content="general">
                <?php include "settings-general.php"; ?>
            </div>

            <div class="iwptp-tab-content-item" data-content="localization">
                <?php include "settings-localization.php"; ?>
            </div>
        </div>
    </div>

    <?php require_once "footer.php"; ?>

    <?php require_once('settings-partials/import-export.php'); ?>

    <?php
    $icons = array('trash', 'sliders', 'copy', 'x', 'check');
    foreach ($icons as $icon_name) {
    ?>
        <script type="text/template" id="iwptp-icon-<?php echo esc_attr($icon_name); ?>">
            <?php echo wp_kses(iwptp_icon($icon_name), iwptp_allowed_html_tags()); ?>
    </script>
    <?php
    }
    ?>

    <?php require_once('partials/element-editor/element-partials.php'); ?>

    <script>
        var iwptp_icons_url = "<?php echo esc_url(IWPTPL_PLUGIN_URL . 'assets/feather'); ?>";
    </script>