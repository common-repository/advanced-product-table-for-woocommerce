<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// don't exit this script if there is no sale price for the product because the parsed template for sale element is still needed by variation switch 

$on_sale = apply_filters('iwptp_product_is_on_sale', $product->is_on_sale(), $product);

if (!$on_sale) {
    $sale_price = 0;
} else {
    $sale_price = wc_get_price_to_display($product, array(
        'qty' => 1,
        'price' => $product->get_sale_price(),
    ));
}

$sale_price = apply_filters('iwptp_product_get_sale_price', $sale_price, $product);

echo '<span class="iwptp-sale-price ' . esc_attr($html_class) . '">' . wp_kses(iwptp_price($sale_price), iwptp_allowed_html_tags()) . '</span>';
