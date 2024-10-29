<div class="iwptp-editor-row-option iwptp-toggle-options iwptp-row-accordion">
    <span class="iwptp-toggle-label">
        <?php esc_html_e('Style for Column', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        <?php echo wp_kses(iwptp_icon('chevron-down'), iwptp_allowed_html_tags()); ?>
    </span>

    <div class="iwptp-editor-row-option" iwptp-model-key="[id].iwptp-options-column > .iwptp-options">
        <!-- height -->
        <div class="iwptp-editor-row-option">
            <label>
                <?php esc_html_e('Height', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                <small><?php esc_html_e('Use can use \'auto\'', 'ithemeland-woocommerce-product-table-pro-lite'); ?> </small>
            </label>
            <input type="text" iwptp-model-key="height" />
        </div>

        <!-- width -->
        <div class="iwptp-editor-row-option">
            <label><?php esc_html_e('Width', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
            <input type="text" iwptp-model-key="width" />
        </div>

    </div>

    <div class="iwptp-editor-row-option" iwptp-model-key="[id]">
        <!-- border color -->
        <div class="iwptp-editor-row-option">
            <label><?php esc_html_e('Border color', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
            <input type="text" iwptp-model-key="border-color" class="iwptp-color-picker">
        </div>

        <!-- background color -->
        <div class="iwptp-editor-row-option">
            <label><?php esc_html_e('Background color', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
            <input type="text" iwptp-model-key="background-color" class="iwptp-color-picker">
        </div>

        <!-- font-size -->
        <div class="iwptp-editor-row-option">
            <label><?php esc_html_e('Font size', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
            <input type="text" iwptp-model-key="font-size" />
        </div>

        <!-- margin -->
        <div class="iwptp-editor-row-option">
            <label><?php esc_html_e('Margin', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
            <input type="text" iwptp-model-key="margin-top" placeholder="<?php esc_attr_e('top', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
            <input type="text" iwptp-model-key="margin-right" placeholder="<?php esc_attr_e('right', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
            <input type="text" iwptp-model-key="margin-bottom" placeholder="<?php esc_attr_e('bottom', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
            <input type="text" iwptp-model-key="margin-left" placeholder="<?php esc_attr_e('left', 'ithemeland-woocommerce-product-table-pro-lite'); ?>">
        </div>

    </div>

</div>