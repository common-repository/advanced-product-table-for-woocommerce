<!-- title -->
<div iwptp-model-key="[container] .iwptp-mini-cart .iwptp-cart-header, .iwptp-cart-widget">
    <!-- font size -->
    <div class="iwptp-editor-option-row">
        <label><?php esc_html_e('Title Font size', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <div class="iwptp-diw">
            <input type="text" iwptp-model-key="font-size" style="width: 100% !important;" placeholder="14px" />
            <div class="iwptp-diw-tray"></div>
        </div>
    </div>

    <!-- font family -->
    <div class="iwptp-editor-option-row">
        <label>
            <?php esc_html_e('Font family', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
            <span class="iwptp-font-family-sample-text"><?php esc_html_e('Sample Text', 'ithemeland-woocommerce-product-table-pro-lite'); ?></span>
        </label>
        <select iwptp-model-key="font-family" class="iwptp-select2 iwptp-font-family-dropdown">
            <?php include IWPTPL_PLUGIN_PATH . "editor/partials/element-editor/partials/font-family/font-family-options.php"; ?>
        </select>
    </div>
</div>

<!-- meta -->
<div iwptp-model-key="[container] .iwptp-mini-cart .iwptp-mini-cart-meta, .iwptp-cart-widget .iwptp-cw-qty-total, .iwptp-cart-widget .iwptp-cw-separator, .iwptp-cart-widget .iwptp-cw-price-total, .iwptp-mini-cart-inline-mode-container .iwptp-mini-cart-meta">
    <!-- font size -->
    <div class="iwptp-editor-option-row">
        <label><?php esc_html_e('Meta Font size', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <div class="iwptp-diw">
            <input type="text" iwptp-model-key="font-size" style="width: 100% !important;" placeholder="14px" />
            <div class="iwptp-diw-tray"></div>
        </div>
    </div>

    <!-- font family -->
    <div class="iwptp-editor-option-row">
        <label>
            <?php esc_html_e('Font family', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
            <span class="iwptp-font-family-sample-text"><?php esc_html_e('Sample Text', 'ithemeland-woocommerce-product-table-pro-lite'); ?></span>
        </label>
        <select iwptp-model-key="font-family" class="iwptp-select2 iwptp-font-family-dropdown">
            <?php include IWPTPL_PLUGIN_PATH . "editor/partials/element-editor/partials/font-family/font-family-options.php"; ?>
        </select>
    </div>
</div>

<div iwptp-model-key=".iwptp-cart-widget">

    <div class="iwptp-editor-option-row">
        <label><?php esc_html_e('Font weight (For Default type)', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <select iwptp-model-key="font-weight">
            <option value=""></option>
            <option value="normal"><?php esc_html_e('Normal', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
            <option value="bold"><?php esc_html_e('Bold', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
            <option value="light"><?php esc_html_e('Light', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
            <option value="100">100</option>
            <option value="200">200</option>
            <option value="300">300</option>
            <option value="400">400</option>
            <option value="500">500</option>
            <option value="600">600</option>
            <option value="700">700</option>
            <option value="800">800</option>
            <option value="900">900</option>
        </select>
    </div>

</div>