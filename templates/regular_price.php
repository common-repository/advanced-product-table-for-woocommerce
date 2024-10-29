<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if ($product->get_type() == 'variable') {
    $prices                 = $product->get_variation_prices(true);
    $regular_price     = current($prices['regular_price']);
} else {
    $regular_price = wc_get_price_to_display($product, array(
        'qty' => 1,
        'price' => $product->get_regular_price(),
    ));
}

$regular_price = apply_filters('iwptp_product_get_regular_price', $regular_price, $product);

if (!$regular_price) {
    return apply_filters('woocommerce_empty_price_html', '', $product);
}

?>
<span class="iwptp-regular-price <?php echo esc_attr($html_class) ?>"><?php echo wp_kses(iwptp_price($regular_price), iwptp_allowed_html_tags()); ?></span>