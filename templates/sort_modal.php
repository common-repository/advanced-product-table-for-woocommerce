<?php
if (!defined('ABSPATH')) {
    exit;
}

if (empty($label)) {
    return;
}

?>
<a class="iwptp-rn-button iwptp-rn-sort <?php echo esc_attr($html_class); ?>" href="javascript:void(0)" data-iwptp-modal="sort">
    <?php echo wp_kses(iwptp_parse_2($label), iwptp_allowed_html_tags()); ?>
</a>