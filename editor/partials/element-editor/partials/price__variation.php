<div class="iwptp-element-settings-content-item active" data-content="general">
    <?php include('html-class.php'); ?>
</div>

<!-- style -->

<div class="iwptp-element-settings-content-item" data-content="style" iwptp-model-key="style">
    <?php include('style/common.php'); ?>

    <div class="iwptp-editor-row-option iwptp-toggle-options iwptp-row-accordion" iwptp-model-key="[id] ins">
        <span class="iwptp-toggle-label">
            <?php esc_html_e('Style for Sale Price', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
            <?php echo wp_kses(iwptp_icon('chevron-down'), iwptp_allowed_html_tags()); ?>
        </span>
        <?php require('style/common-props.php'); ?>
    </div>
</div>