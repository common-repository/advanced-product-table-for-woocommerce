<div class="iwptp-element-settings-content-item active" data-content="general">
    <!-- use default template -->
    <div class="iwptp-editor-row-option">
        <label>
            <input type="checkbox" iwptp-model-key="use_default_template">
            <?php esc_html_e('Use the default WooCommerce price template', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        </label>
    </div>

    <div class="iwptp-editor-row-option" iwptp-panel-condition="prop" iwptp-condition-prop="use_default_template" iwptp-condition-val="false">
        <!-- regular / non-sale price template -->
        <div class="iwptp-editor-row-option">
            <label>
                <?php esc_html_e('Price template', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
            </label>
            <div iwptp-block-editor iwptp-be-add-element-partial="add-price-element" iwptp-model-key="template"></div>
        </div>

        <!-- sale price template -->
        <div class="iwptp-editor-row-option">
            <label>
                <?php esc_html_e('Sale price template', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
            </label>
            <div iwptp-block-editor iwptp-be-add-element-partial="add-price-sale-element" iwptp-model-key="sale_template"></div>
        </div>

        <!-- variable / grouped product price template -->
        <div class="iwptp-editor-row-option">
            <label>
                <?php esc_html_e('Variable / grouped product price template', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
            </label>
            <div iwptp-block-editor iwptp-be-add-element-partial="add-price-variable-element" iwptp-model-key="variable_template"></div>
        </div>

    </div>

    <!-- variable price switch -->
    <div class="iwptp-editor-row-option">
        <?php iwptp_checkbox('true', 'Switch price based on selected variation', 'variable_switch'); ?>
    </div>

    <?php include('html-class.php'); ?>

    <?php include('condition/outer.php'); ?>
</div>

<div class="iwptp-element-settings-content-item" data-content="style" iwptp-model-key="style">
    <?php include('style/common.php'); ?>
</div>