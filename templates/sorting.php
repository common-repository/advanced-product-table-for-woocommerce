<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// only loop
if (!empty($GLOBALS['iwptp_table_data']['query']['sc_attrs']['_only_loop'])) {
    return;
}

if (empty($orderby)) {
    return;
}

if (empty($meta_key)) {
    $meta_key = false;
}

if (empty($orderby_attribute)) {
    $orderby_attribute = false;
}

if (empty($orderby_taxonomy)) {
    $orderby_taxonomy = false;
}

extract(iwptp_get_sorting_html_classes($orderby, $meta_key, $orderby_attribute, $orderby_taxonomy));

?>
<div class="iwptp-sorting-icons <?php echo esc_attr($html_class) . ' ' . esc_attr($sorting_class); ?>" data-iwptp-orderby="<?php echo esc_attr($orderby); ?>" data-iwptp-meta-key="<?php echo esc_attr($meta_key); ?>">
    <div class="iwptp-sorting-asc-icon iwptp-sorting-icon <?php echo esc_attr($sorting_class_asc); ?>"></div>
    <div class="iwptp-sorting-desc-icon iwptp-sorting-icon <?php echo esc_attr($sorting_class_desc); ?>"></div>
</div>