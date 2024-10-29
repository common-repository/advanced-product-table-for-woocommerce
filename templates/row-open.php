<?php
if (!defined('ABSPATH')) {
    exit;
}

// product variation
if (in_array($product->get_type(), array('variation', 'subscription_variation'))) {
    $product_id_data = ' data-iwptp-product-id="' . wp_get_post_parent_id($product->get_id()) . '" ';
    $variation_id_data = ' data-iwptp-variation-id="' . $product->get_id() . '" ';
    $variation_attributes_data = '';
    $variation_attributes = $product->get_variation_attributes();
    if ($variation_attributes) {
        $variation_attributes_data = ' data-iwptp-variation-attributes="' . esc_attr(wp_json_encode($variation_attributes)) . '" ';
    }

    // other product type
} else {
    $product_id_data = ' data-iwptp-product-id="' . $product->get_id() . '" ';
    $variation_id_data = '';
    $variation_attributes_data = '';
}

$product_type_html_class = 'iwptp-product-type-' . $product->get_type();

$in_cart = iwptp_get_cart_item_quantity($product->get_id());

$stock = $product->get_stock_quantity();

$html_class = ' iwptp-row '; // main row class

global $iwptp_products;
$html_class .= ' iwptp-' . ($iwptp_products->current_post % 2 ? 'even' : 'odd')  . ' '; // even / odd class

$html_class .= ' ' . $product_type_html_class . ' '; // product type

if ($product->get_type() == 'variable-subscription') {
    $html_class .= ' iwptp-product-type-variable ';
}

if (
    $product->get_type() == 'variable' &&
    iwptp_all_variations_out_of_stock($product->get_id())
) {
    $html_class .= ' iwptp-all-variations-out-of-stock ';
}

if ($product->get_sold_individually()) {
    $html_class .= ' iwptp-sold-individually ';
}

$html_class = apply_filters('iwptp_product_row_html_class', $html_class, $product);

$attributes = apply_filters(
    'iwptp_product_row_attributes',
    // attribute list start
    ' ' . $variation_id_data . ' 
	' . $variation_attributes_data . ' 
	' . $product_id_data . ' 
	data-iwptp-type="' . $product->get_type() . '" 
	data-iwptp-in-cart="' . $in_cart . '" 
	data-iwptp-stock="' . $stock . '"
	data-iwptp-type="' . $product->get_type() . '" 
	data-iwptp-in-cart="' . $in_cart . '" 
	data-iwptp-stock="' . $stock . '"
	data-iwptp-price="' . iwptp_get_price_to_display($product) . '" ', // attribute list end
    $product
);

echo '<tr ' . wp_kses($attributes, iwptp_allowed_html_tags()) . ' class="' . esc_attr($html_class) . '">';
