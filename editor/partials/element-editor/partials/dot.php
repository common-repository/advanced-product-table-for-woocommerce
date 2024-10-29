<div class="iwptp-element-settings-content-item active" data-content="general">
    <?php include('html-class.php'); ?>
</div>

<div class="iwptp-element-settings-content-item" data-content="style">
    <div class="iwptp-editor-row-style-options iwptp-editor-row-option" iwptp-model-key="style">
        <div class="iwptp-wrapper iwptp-editor-row-option" iwptp-model-key="[id]:after">
            <!-- font-size -->
            <div class="iwptp-editor-row-option">
                <label><?php esc_html_e('Size', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <input type="text" iwptp-model-key="font-size">
            </div>

            <!-- background-color -->
            <div class="iwptp-editor-row-option">
                <label><?php esc_html_e('Color', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <input type="text" iwptp-model-key="background-color" placeholder="#000" class="iwptp-color-picker">
            </div>

        </div>

        <div class="iwptp-wrapper iwptp-editor-row-option" iwptp-model-key="[id]">

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
</div>