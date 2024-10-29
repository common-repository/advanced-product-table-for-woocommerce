<?php
if (!defined('ABSPATH')) {
    exit;
}

// default
if (!empty($use_default_template)) {
    echo '<div class="woocommerce">';
    woocommerce_template_loop_add_to_cart();
    echo '</div>';
    return;
}

// label
if (empty($label)) {
    $label = '';
} else {
    $label = '<span class="iwptp-button-label">' . iwptp_parse_2($label) . '</span>';
}

// link
if (empty($link)) {
    $link = 'product_link';
}

if (
    in_array($product->get_type(), array('external', 'grouped')) &&
    (empty($link) ||
        in_array($link, array('cart_ajax', 'cart_refresh', 'cart_redirect', 'cart_checkout', 'cart_custom',))
    )
) {
    $link = 'product_link';
}

switch ($link) {
    case 'cart_checkout':
        $href = wc_get_checkout_url();
        break;

    case 'product_link':
        $href = get_permalink($product->get_id());
        break;

    case 'external_link':
        if ($product->get_type() !== 'external' || !$product->get_product_url()) {
            return;
        } else {
            $href = $product->get_product_url();
        }
        break;

    case 'cart_refresh':
        $href = '';
        break;

    case 'custom_field':
        if (empty($custom_field) || !$href = get_post_meta($product->get_id(), $custom_field, true)) {
            if (
                !empty($custom_field_empty_relabel) &&
                $empty_relabel = iwptp_parse_2($custom_field_empty_relabel)
            ) {
                echo '<span class="iwptp-button-cf-empty">' . esc_html($empty_relabel) . '</span>';
            }
            return;
        }
        break;

    case 'custom_field_media_id':
        if (empty($custom_field) || !($field_value = get_post_meta($product->get_id(), $custom_field, true))) {
            if (
                !empty($custom_field_empty_relabel) &&
                $empty_relabel = iwptp_parse_2($custom_field_empty_relabel)
            ) {
                echo '<span class="iwptp-button-cf-empty">' . esc_html($empty_relabel) . '</span>';
            }
            return;
        }

        $href = wp_get_attachment_url($field_value);

        break;

    case 'custom_field_acf':
        if (
            function_exists('get_field_object') &&
            $acf_field_object = get_field_object($custom_field)
        ) {

            if (empty($acf_field_object['value'])) {
                return;
            }

            if (
                $acf_field_object['type'] == 'file' &&
                !empty($acf_field_object['return_format'])
            ) {
                switch ($acf_field_object['return_format']) {
                    case 'array':
                        $href = $acf_field_object['value']['url'];
                        break;

                    case 'url':
                        $href = $acf_field_object['value'];
                        break;

                    case 'id':
                        $href = wp_get_attachment_url($acf_field_object['value']);
                        break;
                }
            } else if (in_array(gettype($acf_field_object['value']), array('boolean', 'integer', 'double', 'string'))) {
                $href = $acf_field_object['value'];
            }
        }

        if (empty($href)) {
            return;
        }

        break;

    case 'custom':
        if (empty($custom_url)) {
            $custom_url = '';
        }

        $href = iwptp_general_placeholders__parse($custom_url);

        break;

    case 'cart_custom':
        if (empty($custom_url)) {
            $custom_url = '';
        }

        $href = iwptp_general_placeholders__parse($custom_url);

        break;

    default:
        $href = wc_get_cart_url();
        break;
}

// target / download
if (empty($target)) {
    $target = ' target="_self" ';
} else if ($target === 'download') {
    $target = ' download="' . basename($href) . '" ';
} else {
    $target = ' target="' . $target . '" ';
}

// disabled class
if (
    !in_array($link, array('product_link', 'external_link', 'custom_field', 'custom_field_media_id', 'custom_field_acf', 'custom')) &&
    in_array($product->get_type(), array('simple', 'variation')) &&
    (!$product->is_purchasable() || !$product->is_in_stock())
) {
    $disabled_class = ' iwptp-disabled iwptp-out-of-stock';
} else {
    $disabled_class = '';
}

// no follow
$nofollow = '';

echo '<a class="iwptp-button iwptp-noselect iwptp-button-' . esc_attr($link) . ' ' . esc_attr($html_class) . esc_attr($disabled_class) . '" data-iwptp-link-code="' . esc_attr($link) . '" href="' . esc_url($href) . '" ' . esc_attr($target) . ' ' . esc_attr($nofollow) . ' >' . wp_kses($label, iwptp_allowed_html_tags()) . '</a>';
