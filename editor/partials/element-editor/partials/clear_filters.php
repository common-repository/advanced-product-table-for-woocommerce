<div class="iwptp-element-settings-content-item active" data-content="general">
    <!-- clear all label -->
    <div class="iwptp-editor-row-option">
        <label>
            <input type="checkbox" iwptp-model-key="hide_clear_all" />
            <?php esc_html_e('Hide "Clear All" button', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        </label>

    </div>

    <!-- clear all label -->
    <div class="iwptp-editor-row-option" iwptp-panel-condition="prop" iwptp-condition-prop="hide_clear_all" iwptp-condition-val="false">
        <label><?php esc_html_e('"Clear All" button label', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <input type="text" iwptp-model-key="reset_label" />
    </div>

    <?php include('html-class.php'); ?>
</div>

<div class="iwptp-element-settings-content-item" data-content="style">
    <div class="iwptp-editor-row-option" iwptp-model-key="style">
        <div class="iwptp-editor-row-option iwptp-toggle-options iwptp-row-accordion" iwptp-model-key="[id] > .iwptp-clear-filter">
            <span class="iwptp-toggle-label">
                <?php esc_html_e('Style for Buttons', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                <?php echo wp_kses(iwptp_icon('chevron-down'), iwptp_allowed_html_tags()); ?>
            </span>
            <!-- font-size -->
            <div class="iwptp-editor-row-option">
                <label><?php esc_html_e('Font size', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <input type="text" iwptp-model-key="font-size" placeholder="16px">
            </div>

            <!-- font-color -->
            <div class="iwptp-editor-row-option">
                <label><?php esc_html_e('Font color', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <input type="text" iwptp-model-key="color" placeholder="#000" class="iwptp-color-picker">
            </div>

            <!-- background-color -->
            <div class="iwptp-editor-row-option">
                <label><?php esc_html_e('Background color', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <input type="text" iwptp-model-key="background-color" class="iwptp-color-picker">
            </div>
        </div>
    </div>
    <div class="iwptp-editor-row-option" iwptp-model-key="style">
        <div class="iwptp-editor-row-option iwptp-toggle-options iwptp-row-accordion" iwptp-model-key="[id] > .iwptp-clear-filter:hover">
            <span class="iwptp-toggle-label">
                <?php esc_html_e('Style for Buttons : Hover', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                <?php echo wp_kses(iwptp_icon('chevron-down'), iwptp_allowed_html_tags()); ?>
            </span>
            <!-- font-color -->
            <div class="iwptp-editor-row-option">
                <label><?php esc_html_e('Font color', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <input type="text" iwptp-model-key="color" placeholder="#000" class="iwptp-color-picker">
            </div>
            <!-- background-color -->
            <div class="iwptp-editor-row-option">
                <label><?php esc_html_e('Background color', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <input type="text" iwptp-model-key="background-color" class="iwptp-color-picker">
            </div>
        </div>
    </div>
    <div class="iwptp-editor-row-option" iwptp-model-key="style">
        <div class="iwptp-editor-row-option iwptp-toggle-options iwptp-row-accordion" iwptp-model-key="[id] > a.iwptp-clear-all-filters">
            <span class="iwptp-toggle-label">
                <?php esc_html_e('Style for \'Clear All\'', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                <?php echo wp_kses(iwptp_icon('chevron-down'), iwptp_allowed_html_tags()); ?>
            </span>
            <!-- font-size -->
            <div class="iwptp-editor-row-option">
                <label><?php esc_html_e('Font size', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <input type="text" iwptp-model-key="font-size" placeholder="16px">
            </div>
            <!-- font-color -->
            <div class="iwptp-editor-row-option">
                <label><?php esc_html_e('Font color', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <input type="text" iwptp-model-key="color" placeholder="#000" class="iwptp-color-picker">
            </div>
        </div>
    </div>
    <div class="iwptp-editor-row-option" iwptp-model-key="style">
        <div class="iwptp-editor-row-option iwptp-toggle-options iwptp-row-accordion" iwptp-model-key="[id] > a.iwptp-clear-all-filters:hover">
            <span class="iwptp-toggle-label">
                <?php esc_html_e(' Style for \'Clear All\' : Hover', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                <?php echo wp_kses(iwptp_icon('chevron-down'), iwptp_allowed_html_tags()); ?>
            </span>
            <!-- color -->
            <div class="iwptp-editor-row-option">
                <label><?php esc_html_e('Font color', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <input type="text" iwptp-model-key="color" class="iwptp-color-picker">
            </div>
        </div>
    </div>
</div>