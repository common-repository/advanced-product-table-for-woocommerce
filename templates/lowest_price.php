<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if ($product->get_type() == 'variable') {
    $prices         = $product->get_variation_prices(true);
    $min_price     = current($prices['price']);
} else if ($product->get_type() == 'grouped') {
    $prices = iwptp_get_grouped_product_price();
    $min_price = $prices['min_price'];
}

$min_price = apply_filters('iwptp_product_get_lowest_price', $min_price, $product);

echo '<span class="iwptp-lowest-price ' . esc_attr($html_class) . '">' . wp_kses(iwptp_price($min_price), iwptp_allowed_html_tags()) . '</span>';
