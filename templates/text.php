<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!$text) {
    return;
}

echo '<span class="iwptp-text ' . esc_attr($html_class) . '">' . wp_kses(iwptp_esc_tag($text), iwptp_allowed_html_tags()) . '</span>';
