<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!WC()->cart || empty($mini_cart_type)) {
    return;
}

$template_file = IWPTPL_PLUGIN_PATH . 'templates/mini_cart_' . $mini_cart_type . '_items.php';

if (file_exists($template_file)) {
    include $template_file;
}
