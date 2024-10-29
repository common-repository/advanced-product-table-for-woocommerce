<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

$parent_id = $product->get_parent_id();
$parent = wc_get_product($parent_id);

$checked = '';
if ($parent->get_default_attributes()) {

    $default_attributes = [];
    foreach ($parent->get_default_attributes() as $key => $value) {
        $default_attributes['attribute_' . $key] = $value;
    }

    $default_variation_match = iwptp_find_closests_matching_product_variation($parent, $default_attributes);

    if (
        $default_variation_match &&
        $default_variation_match['variation_id'] == $product->get_id()
    ) {
        $checked = ' checked="checked" ';
    }
}

if (!$product->is_in_stock()) {
    $checked = '';
    $disabled = ' disabled ';
} else {
    $disabled = '';
}

?>
<input type="radio" class="iwptp-variation-radio <?php echo esc_attr($html_class); ?> <?php  ?>" name="<?php echo esc_attr($GLOBALS['iwptp_row_rand']) . '-' . esc_attr($parent_id); ?>" value="<?php echo esc_attr($product->get_id()); ?>" <?php echo esc_attr($checked); ?> <?php echo esc_attr($disabled); ?> />