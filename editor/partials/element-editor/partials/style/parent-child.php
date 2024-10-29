<div class="iwptp-editor-row-option" iwptp-model-key="style">
    <!-- Container -->
    <div class="iwptp-editor-row-option iwptp-toggle-options iwptp-row-accordion" iwptp-model-key="[id]">
        <span class="iwptp-toggle-label">
            <?php esc_html_e('Style for Container', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
            <?php echo wp_kses(iwptp_icon('chevron-down'), iwptp_allowed_html_tags()); ?>
        </span>
        <?php require('common-props.php'); ?>
    </div>

    <!-- Terms -->
    <div class="iwptp-editor-row-option iwptp-toggle-options iwptp-row-accordion" iwptp-model-key="[id] > div:not(.iwptp-term-separator)">
        <span class="iwptp-toggle-label">
            <?php esc_html_e('Style for Terms', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
            <?php echo wp_kses(iwptp_icon('chevron-down'), iwptp_allowed_html_tags()); ?>
        </span>
        <?php require('common-props.php'); ?>
    </div>
</div>