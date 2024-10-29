<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (empty($target)) {
    $target = "_self";
}

$title = get_the_title($product->get_id());
$url      = get_permalink($product->get_id());
$sku      = $product->get_sku();

if (empty($template)) {
    $template = get_the_title($product->get_id());
} else {
    $template = iwptp_parse_2($template);
}

$template = iwptp_general_placeholders__parse($template);

echo '<a class="iwptp-product-link ' . esc_attr($html_class) . '" href="' . esc_url($url) . esc_attr($suffix) . '" target="' . esc_attr($target) . '">' . wp_kses($template, iwptp_allowed_html_tags()) . '</a>';
