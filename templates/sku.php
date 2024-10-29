<?php
if (!defined('ABSPATH')) {
    exit;
}

$sku = $product->get_sku();

// if( 
// 	$product->get_type() == 'variable' &&
// 	$default_variation = iwptp_get_default_variation( $product )
// ){
// 	$product_variation = wc_get_product( $default_variation['variation_id']);
// 	if( $product_variation->get_sku() ){
// 		$sku = $product_variation->get_sku();
// 	}
// }

if (!empty($variable_switch)) {
    $html_class .= ' iwptp-variable-switch ';
}

if (!empty($product_link_enabled)) {
    $target = empty($target_new_page) ? '_self' : '_blank';

    echo '<a href="' . esc_url(get_permalink()) . '" target="' . esc_attr($target) . '" class="iwptp-sku ' . esc_attr($html_class) . '" data-iwptp-sku="' . esc_attr($product->get_sku()) . '">' . esc_html($sku) . '</a>';
} else {
    echo '<span class="iwptp-sku ' . esc_attr($html_class) . '" data-iwptp-sku="' . esc_attr($product->get_sku()) . '">' . esc_html($sku) . '</span>';
}
