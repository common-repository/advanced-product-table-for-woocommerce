<?php
if (!defined('ABSPATH')) {
    exit;
}

if (empty($label)) {
    return;
}

?>
<a class="iwptp-rn-button iwptp-rn-filter <?php echo esc_attr($html_class); ?>" href="javascript:void(0)" data-iwptp-modal="filter">
    <?php echo wp_kses(iwptp_parse_2($label), iwptp_allowed_html_tags()); ?>
</a>