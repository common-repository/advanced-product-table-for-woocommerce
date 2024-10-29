<div class="iwptp-column-settings iwptp-toggle-column-expand" iwptp-controller="column_settings" iwptp-model-key="[]" iwptp-model-key-index="0" iwptp-row-template="column_settings_<?php echo esc_attr($device); ?>" iwptp-initial-data="column_settings">
    <button type="button" class="iwptp-columns-add-new-row iwptp-middle-cols-button" iwptp-add-row-template="column_settings_<?php echo esc_attr($device); ?>"><i class="dashicons dashicons-insert"></i></button>

    <div class="iwptp-column-settings-content">
        <div class="iwptp-column-toggle-capture"></div>

        <?php ob_start(); ?>
        <i class="iwptp-editor-row-expand" iwptp-expand title="Expand column">
            <?php echo wp_kses(iwptp_icon('maximize-2'), iwptp_allowed_html_tags()); ?>
        </i>
        <i class="iwptp-editor-row-contract" iwptp-expand title="Contract column">
            <?php echo wp_kses(iwptp_icon('minimize-2'), iwptp_allowed_html_tags()); ?>
        </i>
        <?php iwptp_corner_options(array('prepend' => ob_get_clean())); ?>

        <!-- column index -->
        <span class="iwptp-column-index">
            <span class="iwptp-column-device-icon-container"></span>
            <span><?php esc_html_e('Column', 'ithemeland-woocommerce-product-table-pro-lite'); ?></span>
            <i>1</i>
            <?php echo wp_kses(iwptp_icon('edit', 'iwptp-column-name-edit'), iwptp_allowed_html_tags()); ?>
            <input class="iwptp-column-name" type="text" iwptp-model-key="name" placeholder="<?php esc_html_e('Column name', 'ithemeland-woocommerce-product-table-pro-lite'); ?>" />
            <?php echo wp_kses(iwptp_icon('x', 'iwptp-close-column-name-input'), iwptp_allowed_html_tags()); ?>
        </span>

        <!-- heading -->
        <div class="iwptp-tabs" iwptp-model-key="heading">
            <div class="iwptp-tab-triggers">
                <div class="iwptp-tab-trigger">
                    <?php esc_html_e('Heading', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                </div>
                <div class="iwptp-tab-trigger">
                    <?php esc_html_e('Design', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                </div>
            </div>

            <!-- heading editor -->
            <div class="iwptp-tab-content">
                <div class="iwptp-block-editor iwptp-column-heading-editor" iwptp-model-key="content"></div>
            </div>

            <!-- design options -->
            <div class="iwptp-tab-content">
                <?php include('element-editor/partials/column-heading-style.php'); ?>
            </div>

        </div>

        <!-- template -->
        <div class="iwptp-tabs" iwptp-model-key="cell">
            <div class="iwptp-tab-triggers">
                <div class="iwptp-tab-trigger">
                    <?php esc_html_e('Cell Content', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                </div>
                <div class="iwptp-tab-trigger">
                    <?php esc_html_e('Design', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                </div>
            </div>

            <!-- template editor -->
            <div class="iwptp-tab-content">
                <div class="iwptp-block-editor iwptp-column-template-editor" iwptp-model-key="template"></div>
            </div>

            <!-- design options -->
            <div class="iwptp-tab-content">
                <?php include('element-editor/partials/column-cell-style.php'); ?>
            </div>
        </div>
    </div>
</div>

<button class="iwptp-button iwptp-button-blue iwptp-columns-add-row-button-main" iwptp-add-row-template="column_settings_<?php echo esc_attr($device); ?>">
    <?php esc_html_e('+ Add a Column', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
</button>
<button class="iwptp-hide iwptp-button iwptp-button-blue iwptp-columns-add-row-without-animation" iwptp-add-row-template="column_settings_<?php echo esc_attr($device); ?>"></button>