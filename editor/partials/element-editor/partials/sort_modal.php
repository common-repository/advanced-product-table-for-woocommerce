<div class="iwptp-element-settings-content-item active" data-content="general">
    <div class="iwptp-editor-row-option">
        <label><?php esc_html_e('Button label', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <div iwptp-model-key="label" iwptp-block-editor iwptp-be-add-row="0"></div>
    </div>
</div>

<div class="iwptp-element-settings-content-item" data-content="style">
    <div class="iwptp-editor-row-style-options iwptp-editor-row-option" iwptp-model-key="style">
        <div class="iwptp-wrapper iwptp-editor-row-option" iwptp-model-key="[id]">
            <!-- font-size -->
            <div class="iwptp-editor-row-option">
                <label><?php esc_html_e('Font size', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <input type="text" iwptp-model-key="font-size" placeholder="16px">
            </div>

            <!-- line-height -->
            <div class="iwptp-editor-row-option">
                <label><?php esc_html_e('Line height', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <input type="text" iwptp-model-key="line-height" placeholder="1.2em">
            </div>

            <!-- font-color -->
            <div class="iwptp-editor-row-option">
                <label><?php esc_html_e('Font color', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <input type="text" iwptp-model-key="color" placeholder="#000" class="iwptp-color-picker">
            </div>

            <!-- font-weight -->
            <div class="iwptp-editor-row-option">
                <label><?php esc_html_e('Font weight', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <select iwptp-model-key="font-weight">
                    <option value="normal"><?php esc_html_e('Normal', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                    <option value="bold"><?php esc_html_e('Bold', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                    <option value="lighter"><?php esc_html_e('Light', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
                </select>
            </div>

            <!-- background-color -->
            <div class="iwptp-editor-row-option">
                <label><?php esc_html_e('Background color', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
                <input type="text" iwptp-model-key="background-color" class="iwptp-color-picker">
            </div>
        </div>
    </div>
</div>