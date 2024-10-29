<div class="iwptp-element-settings-content-item active" data-content="general">
    <div class="iwptp-editor-row-option">
        <label><?php esc_html_e('HTML', 'ithemeland-woocommerce-product-table-pro-lite'); ?></label>
        <textarea iwptp-model-key="html"></textarea>
        <label>
            <?php iwptp_general_placeholders__print_placeholders(); ?>
        </label>
    </div>

    <?php include('html-class.php'); ?>

    <?php include('condition/outer.php'); ?>
</div>

<div class="iwptp-element-settings-content-item" data-content="style" iwptp-model-key="style">
    <?php include('style/common.php'); ?>
</div>