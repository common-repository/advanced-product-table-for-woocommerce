<div class="iwptp-editor-row-option" iwptp-panel-condition="prop" iwptp-condition-prop="position" iwptp-condition-val="header">
    <div class="iwptp-editor-row-option" iwptp-panel-condition="prop" iwptp-condition-prop="display_type" iwptp-condition-val="dropdown">
        <div class="iwptp-editor-row-option" iwptp-panel-condition="prop" iwptp-condition-prop="single" iwptp-condition-val="true">
            <div class="iwptp-editor-row-option">
                <label>
                    <?php esc_html_e('Heading format when option is selected', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                </label>
                <label>
                    <input type="radio" iwptp-model-key="heading_format__op_selected" value="only_heading">
                    <?php esc_html_e('Only show filter heading', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                </label>
                <label>
                    <input type="radio" iwptp-model-key="heading_format__op_selected" value="heading_and_selected">
                    <?php esc_html_e('Show filter heading and selected option', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                </label>
                <label>
                    <input type="radio" iwptp-model-key="heading_format__op_selected" value="only_selected">
                    <?php esc_html_e('Replace heading with selected option', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
                </label>
            </div>
        </div>
    </div>
</div>