<?php
$data = iwptp_get_table_data();
$checklist = array(
    'query_selected' => !(empty($data['query']['category']) && empty($data['query']['skus']) && empty($data['query']['ids'])),
    'column_element_created' => !(empty($data['columns']['laptop']) || iwptp_device_columns_empty($data['columns']['laptop'])),
    'saved' => (!empty($data['id']) && get_post_status($data['id']) == 'publish'),
);

if (count(array_filter(array_values($checklist))) == 3) {
    return;
}

?>
<div class="iwptp-notice-checklist">
    <span class="iwptp-notice-ck-label">
        <?php echo wp_kses(iwptp_icon('chevron-right'), iwptp_allowed_html_tags()); ?>
        <?php esc_html_e('Checklist:', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
    </span>
    <span class="iwptp-notice-ck-item <?php echo $checklist['query_selected'] ? 'iwptp-done' : '' ?>" data-iwptp-ck="query_selected">
        <?php echo wp_kses(iwptp_icon('check'), iwptp_allowed_html_tags()); ?>
        <?php echo wp_kses(iwptp_icon('info'), iwptp_allowed_html_tags()); ?>
        <?php esc_html_e('Create a query', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        <span class="iwptp-tltp-content">
            <?php esc_html_e('Go to the \'Query\' tab below and select some criteria for which products to display. If you leave these settings empty all products in your shop will be displayed. Remember, you can also use shortcode attributes to modify a table\'s query on the fly.', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        </span>
    </span>
    <span class="iwptp-notice-ck-item <?php echo $checklist['column_element_created'] ? 'iwptp-done' : '' ?>" data-iwptp-ck="column_element_created">
        <?php echo wp_kses(iwptp_icon('check'), iwptp_allowed_html_tags()); ?>
        <?php echo wp_kses(iwptp_icon('info'), iwptp_allowed_html_tags()); ?>
        <?php esc_html_e('Add columns & elements', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        <span class="iwptp-tltp-content">
            <?php esc_html_e('Go to the \'Columns\' tab below and create at least 1 column in the Laptop Columns section. Then create at least 1 element in this column. Without any settings for column and elements, the table has nothing to display.', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        </span>
    </span>
    <span class="iwptp-notice-ck-item <?php echo $checklist['saved'] ? 'iwptp-done' : '' ?>" data-iwptp-ck="saved">
        <?php echo wp_kses(iwptp_icon('check'), iwptp_allowed_html_tags()); ?>
        <?php echo wp_kses(iwptp_icon('info'), iwptp_allowed_html_tags()); ?>
        <?php esc_html_e('Save the table settings', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        <span class="iwptp-tltp-content">
            <?php esc_html_e('You will find the \'Save settings\' button at the bottom of the editor. Press it now to save this table, and move it from \'draft\' to \'publish\' state. Please remember to save your table settings whenever you make changes. You can use Ctrl + s on windows and Cmd + s to conveniently save your settings at any time.', 'ithemeland-woocommerce-product-table-pro-lite'); ?>
        </span>
    </span>
</div>