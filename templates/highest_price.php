<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if ($product->get_type() == 'variable') {
    $prices         = $product->get_variation_prices(true);
    $max_price     = end($prices['price']);
} else if ($product->get_type() == 'grouped') {
    $prices = iwptp_get_grouped_product_price();
    $max_price = $prices['max_price'];
}

$max_price = apply_filters('iwptp_product_get_highest_price', $max_price, $product);

echo '<span class="iwptp-highest-price ' . esc_attr($html_class) . '">' . wp_kses(iwptp_price($max_price), iwptp_allowed_html_tags()) . '</span>';
