<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (isset($text)) {
    echo '<span class="iwptp-text ' . esc_attr($html_class) . '">' . esc_html(iwptp_esc_tag(iwptp_general_placeholders__parse($text))) . '</span>';
}
