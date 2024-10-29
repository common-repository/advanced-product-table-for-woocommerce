<div class="iwptp-element-settings-content-item active" data-content="general">

    <!-- product link -->
    <div class="iwptp-editor-row-option">
        <label>
            <input type="checkbox" iwptp-model-key="product_link_enabled" />
            <?php esc_html_e('Link SKU to the product\'s page', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        </label>
    </div>

    <!-- product link: new page -->
    <div class="iwptp-editor-row-option" iwptp-panel-condition="prop" iwptp-condition-prop="product_link_enabled" iwptp-condition-val="true">
        <label>
            <input type="checkbox" iwptp-model-key="target_new_page" />
            <?php esc_html_e('Open the product link on a new page', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        </label>
    </div>

    <!-- variable switch -->
    <div class="iwptp-editor-row-option">
        <?php iwptp_checkbox('true', 'Switch SKU based on selected variation', 'variable_switch'); ?>
    </div>

    <?php include('html-class.php'); ?>

    <?php include('condition/outer.php'); ?>
</div>

<div class="iwptp-element-settings-content-item" data-content="style" iwptp-model-key="style">
    <?php include('style/common.php'); ?>
</div>