<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (empty($template)) {
    return;
}

if (!empty($use_default_template)) {
    if (
        !empty($variable_switch) &&
        in_array($product->get_type(), array('variable-subscription', 'variable'))
    ) {
?>
        <div class="iwptp-variable-price-default-woocommerce-template iwptp-variable-switch <?php echo esc_attr($html_class); ?>">
            <div class="iwptp-variable-switch__default">
                <?php echo wp_kses($product->get_price_html(), iwptp_allowed_html_tags()); ?>
            </div>
        </div>
<?php
    } else {
        echo wp_kses($product->get_price_html(), iwptp_allowed_html_tags());
    }

    return;
}

$on_sale_class = '';
$sale_price = '';

$regular_template = $template;


$variable_switch_class = '';

// variable product
if ($product->get_type() === 'variable') {

    $prices = $product->get_variation_prices(true);

    if (!empty($variable_switch)) {
        $variable_switch_class = 'iwptp-variable-switch';
    }

    if (empty($prices['price'])) {
        return apply_filters('woocommerce_variable_empty_price_html', '', $product);
    } else {
        $min_price     = apply_filters('iwptp_product_get_lowest_price', current($prices['price']), $product);
        $max_price     = apply_filters('iwptp_product_get_highest_price', end($prices['price']), $product);
        $min_reg_price = current($prices['regular_price']);
        $max_reg_price = apply_filters('iwptp_product_get_highest_price', end($prices['regular_price']), $product);

        if ($min_price !== $max_price) {
            $template = $variable_template;
        } elseif (
            apply_filters('iwptp_product_is_on_sale', $product->is_on_sale(), $product) &&
            $min_reg_price === $max_reg_price
        ) {
            $on_sale_class = 'iwptp-product-on-sale';
            $template = $sale_template;
        } else {
            // regular template
            // already assigned by default
        }
    }

    // sale - simple product
} else if (apply_filters('iwptp_product_is_on_sale', $product->is_on_sale(), $product)) {
    $on_sale_class = 'iwptp-product-on-sale';
    $template = $sale_template;
}

// grouped product
if ($product->get_type() === 'grouped') {

    $prices = iwptp_get_grouped_product_price();

    if (gettype($prices) == 'string') {
        $template = $prices;
    } else if ($prices['max_price'] !== $prices['min_price']) {
        $template = $variable_template;
    }
}

?>
<span class="iwptp-price <?php echo esc_attr($html_class . ' ' . $on_sale_class . ' ' . $variable_switch_class); ?>" <?php if ($product->get_type() == 'variable') : ?> data-iwptp-element-id="<?php echo esc_attr($id); ?>" data-iwptp-lowest-price="<?php echo esc_attr(iwptp_price_decimal($min_price)); ?>" data-iwptp-highest-price="<?php echo esc_attr(iwptp_price_decimal($max_price)); ?>" data-iwptp-regular-price="<?php echo esc_attr(iwptp_price_decimal($max_reg_price)); ?>" data-iwptp-sale-price="<?php echo esc_attr(iwptp_price_decimal($min_price)); ?>" data-iwptp-variable-template="<?php echo $on_sale_class ? 'sale' : ($min_price !== $max_price ? 'variable' : 'regular'); ?>" <?php endif; ?>>
    <?php echo wp_kses(iwptp_parse_2($template, $product), iwptp_allowed_html_tags()); ?>
</span>
<?php

// print all templates
$table_data = iwptp_get_table_data();
$table_id = $table_data['id'];

if ($product->get_type() == 'variable') {
    if (empty($GLOBALS['iwptp_' . $table_id . '_price_templates'])) {
        $GLOBALS['iwptp_' . $table_id . '_price_templates'] = [];
    }

    if (empty($GLOBALS['iwptp_' . $table_id . '_price_templates'][$id])) {
        $GLOBALS['iwptp_' . $table_id . '_price_templates'][$id] = array(
            'regular' => iwptp_parse_2($regular_template, $product),
            'sale' => iwptp_parse_2($sale_template, $product),
            'variable' => iwptp_parse_2($variable_template, $product),
        );
    }
}
