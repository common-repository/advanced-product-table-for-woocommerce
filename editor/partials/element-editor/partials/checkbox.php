<div class="iwptp-element-settings-content-item active" data-content="general">
    <div class="iwptp-editor-row-option">
        <label>
            <input type="checkbox" iwptp-model-key="heading_enabled" />
            <?php esc_html_e('Enable checkbox in column heading', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        </label>
    </div>

    <div class="iwptp-editor-row-option">
        <label><?php esc_html_e('Toggle', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <select iwptp-model-key="toggle">
            <option value="disabled"><?php esc_html_e('Disabled', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
            <option value="enabled"><?php esc_html_e('Enabled', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
        </select>
    </div>

    <div class="iwptp-editor-row-option">
        <label><?php esc_html_e('Responsive toggle', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <select iwptp-model-key="r_toggle">
            <option value="disabled"><?php esc_html_e('Disabled', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
            <option value="enabled"><?php esc_html_e('Enabled', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
        </select>
    </div>

    <div class="iwptp-editor-row-option">
        <label><?php esc_html_e('Redirect to', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <select iwptp-model-key="link">
            <option value=""><?php esc_html_e('None', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
            <option value="cart"><?php esc_html_e('Cart', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
            <option value="checkout"><?php esc_html_e('Checkout', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
            <option value="refresh"><?php esc_html_e('Refresh page', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
        </select>
    </div>

    <?php include 'condition/outer.php'; ?>
</div>

<div class="iwptp-element-settings-content-item" data-content="style" iwptp-model-key="style">
    <div iwptp-model-key=".iwptp-cart-checkbox-trigger">

        <div class="iwptp-editor-option-row">
            <label>
                <?php esc_html_e('Background color', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
            </label>
            <input type="text" iwptp-model-key="background-color" />
        </div>

        <div class="iwptp-editor-option-row">
            <label>
                <?php esc_html_e('Border color', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
            </label>
            <input type="text" iwptp-model-key="border-color" />
        </div>

        <div class="iwptp-editor-option-row">
            <label>
                <?php esc_html_e('Font color', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
            </label>
            <input type="text" iwptp-model-key="color" />
        </div>

        <div class="iwptp-editor-option-row">
            <label>
                <?php esc_html_e('Font size', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
            </label>
            <input type="text" iwptp-model-key="font-size" />
        </div>

        <div class="iwptp-editor-option-row">
            <label>
                <?php esc_html_e('Width', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
            </label>
            <input type="text" iwptp-model-key="width" />
        </div>

    </div>
</div>