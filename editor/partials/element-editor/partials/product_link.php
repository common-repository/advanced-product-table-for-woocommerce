<div class="iwptp-element-settings-content-item active" data-content="general">

    <!-- content -->
    <div class="iwptp-editor-row-option">
        <label>
            <?php esc_html_e('Content', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        </label>
        <div iwptp-block-editor iwptp-be-add-element-partial="add-product-link-element" iwptp-model-key="template"></div>

        <label>
            <?php iwptp_general_placeholders__print_placeholders(); ?>
        </label>
    </div>

    <!-- target -->
    <div class="iwptp-editor-row-option">
        <label><?php esc_html_e('Open on', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <select iwptp-model-key="target">
            <option value="_self"><?php esc_html_e('Same page', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
            <option value="_blank"><?php esc_html_e('New page', 'ithemeland-woocommerce-product-table-pro-lite'); ?></option>
        </select>
    </div>

    <!-- suffix -->
    <div class="iwptp-editor-row-option">
        <label>
            <?php esc_html_e('Link Suffix', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        </label>
        <input type="text" iwptp-model-key="suffix" />
    </div>

    <?php include('html-class.php'); ?>

    <?php include('condition/outer.php'); ?>
</div>

<div class="iwptp-element-settings-content-item" data-content="style" iwptp-model-key="style">
    <?php include('style/common.php'); ?>
</div>