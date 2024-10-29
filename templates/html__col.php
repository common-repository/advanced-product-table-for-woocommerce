<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!$html) {
    return;
}

echo '<span class="iwptp-html ' . esc_attr($html_class) . '">' . wp_kses(iwptp_general_placeholders__parse($html), iwptp_allowed_html_tags()) . '</span>';
