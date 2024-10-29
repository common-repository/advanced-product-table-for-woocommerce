<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!WC()->cart) {
    return;
}

$iwptp_settings = iwptp_get_settings_data();
$settings = $iwptp_settings['cart_widget'];

if (empty($cost_source)) {
    $cost_source = 'subtotal';
}

if (empty($link)) {
    $link = 'cart';
}

$total_qty = apply_filters('iwptp_cart_total_quantity', WC()->cart->cart_contents_count);
$total_price = apply_filters('iwptp_cart_total_price', $cost_source == 'subtotal' ? WC()->cart->get_cart_subtotal() : WC()->cart->get_total());

if (!$total_qty && !$total_price) {
    return;
}

$labels = !empty($settings['labels']) ? $settings['labels'] : [];
$locale = get_locale();

$strings = [];

if (!empty($labels)) {
    foreach ($labels as $key => $translations) {
        $strings[$key] = [];
        $translations = preg_split('/$\R?^/m', $translations);
        foreach ($translations as $translation) {
            $array = explode(':', $translation);
            if (!empty($array[1])) {
                $strings[$key][trim($array[0])] = stripslashes(trim($array[1]));
            } else {
                $strings[$key]['default'] = stripslashes(trim($array[0]));
            }
        }
    }
}

switch ($link) {
    case 'checkout':
        $link_url = wc_get_checkout_url();
        break;
    case 'custom_url':
        $link_url = !empty($custom_url) ? esc_url($custom_url) : wc_get_cart_url();
        break;
    default:
        $link_url = wc_get_cart_url();
        break;
}

$hide = $total_qty ? false : true;

// maybe use defaults
foreach ($strings as $item => &$translations) {
    if (empty($translations[$locale])) {
        if (!empty($translations['default'])) {
            $translations[$locale] = $translations['default'];
        } else if (!empty($translations['en_US'])) {
            $translations[$locale] = $translations['en_US'];
        }
    }
}

if (!empty($hide_on_zero) && $hide_on_zero == 'enable' && empty(WC()->cart->cart_contents_count)) {
    $hide_mini_cart = true;
}

$mini_cart_settings = wp_json_encode([
    'mini_cart_type' => $mini_cart_type,
    'mini_cart_subtotal' => $mini_cart_subtotal,
    'show_total' => (!empty($show_total) && $show_total == 'enable') ? 'enable' : 'disable',
    'empty_cart_button' => $empty_cart_button,
    'view_checkout_button' => $view_checkout_button,
    'view_cart_button' => $view_cart_button,
    'title' => (isset($title) && $title != '') ? $title : '',
    'position' => (!empty($position)) ? $position : '',
    'hide_on_zero' => (!empty($hide_on_zero) && $hide_on_zero == 'enable') ? 'enable' : 'disable',
    'float_position' => (!empty($float_position)) ? $float_position : '',
    'size' => (!empty($size)) ? $size : '',
    'button_text' => (!empty($button_text)) ? $button_text : '',
]);

$position_class = '';

if (!empty($side_position)) {
    $position_class .= 'iwptp-mini-cart-side-' . $side_position;
}

if (!empty($float_position)) {
    $position_class .= ' iwptp-mini-cart-button-' . $float_position;
}

$template_file = IWPTPL_PLUGIN_PATH . 'templates/mini_cart_' . $mini_cart_type . '.php';

if (file_exists($template_file)) {
    include $template_file;
}
