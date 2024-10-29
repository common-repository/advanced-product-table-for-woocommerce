<?php
if (!defined('ABSPATH')) {
    exit;
}

$product_id = $product->get_id();

// if( 
// 	$product->get_type() == 'variable' &&
// 	$default_variation = iwptp_get_default_variation( $product )
// ){
// 	$product_variation = wc_get_product( $default_variation['variation_id']);
// 	$product_id = $product_variation->get_id();
// }

if (!empty($variable_switch)) {
    $html_class .= ' iwptp-variable-switch ';
}

echo '<span class="iwptp-product-id ' . esc_attr($html_class) . '" data-iwptp-product-id="' . esc_attr($product->get_id()) . '">' . esc_html($product_id)  . '</span>';
