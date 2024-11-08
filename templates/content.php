<?php
if (!defined('ABSPATH')) {
    exit;
}

if ($product->get_type() == 'variation') {
    $content = $product->get_description();
} else {
    $content = get_the_content();
}

if (!empty($shortcode_action)) {
    if ($shortcode_action === 'process') {
        remove_shortcode('it_product_table');
        $content = do_shortcode($content);
        add_shortcode('it_product_table', 'iwptp_shortcode_product_table');
    } else if ($shortcode_action === 'strip') {
        $content = strip_shortcodes($content);
    }
}

$content = apply_filters('iwptp_content', $content);

$truncation_symbol_content = "…";
if (!empty($truncation_symbol)) {
    if ($truncation_symbol == 'hide') {
        $truncation_symbol_content = '';
    } else if ($truncation_symbol == 'custom') {
        $truncation_symbol_content = $custom_truncation_symbol;
    }
}

// common b/w excerpt and content
// -- begin

if (!$content) {
    return;
}

if (empty($toggle_enabled)) {

    if (
        empty($read_more_label) ||
        !iwptp_parse_2($read_more_label)
    ) {
        $read_more = false;
    } else {
        $read_more = '<a class="iwptp-read-more" href="' . $product->get_permalink() . '">' . iwptp_parse_2($read_more_label) . '</a>';
    }

    $content__truncated = $content;

    if (!empty($limit)) {
        $content = wp_filter_nohtml_kses($content);
        $content__truncated = iwptp_truncate_string($content, $limit);
    }

    if (
        $read_more ||
        strlen($content) > strlen($content__truncated)
    ) {
        $content = rtrim(rtrim($content__truncated), ' ,.') . $truncation_symbol_content;
    }

    $content = $content . '%wcpct_readmore%';

    $content = wpautop($content);

    $content = str_replace('%wcpct_readmore%', ' ' . preg_replace('/\<[\/]{0,1}div[^\>]*\>/i', '', $read_more), $content);
} else { // toggle enabled
    if (
        empty($limit) &&
        $limit !== '0'
    ) {
        $limit = 15;
    }

    $content = wp_filter_nohtml_kses($content);
    $content__truncated = iwptp_truncate_string($content, $limit);

    $content__truncated = rtrim($content__truncated, ' ,.') . (strlen($content__truncated) ? $truncation_symbol_content : '');

    $content = wpautop($content);
    $content__truncated = wpautop($content__truncated);

    if (strlen($content) > strlen($content__truncated)) {
        $html_class .= ' iwptp-toggle-enabled ';

        if (empty($show_more_label)) {
            $show_more_label = 'show more (+)';
        }

        if (empty($show_less_label)) {
            $show_less_label = 'show less (-)';
        }

        $pre_toggle = '<span class="iwptp-pre-toggle">' . $content__truncated . ' <span href="#" class="iwptp-toggle-trigger">' . iwptp_parse_2($show_more_label) . '</span></span>';
        $post_toggle = '<span class="iwptp-post-toggle">' . $content . ' <span class="iwptp-toggle-trigger">' . iwptp_parse_2($show_less_label) . '</span></span>';

        $content = $pre_toggle . $post_toggle;
    }
}

// -- end

echo '<div class="iwptp-content ' . esc_attr($html_class) . '">';
echo wp_kses(stripslashes($content), iwptp_allowed_html_tags());
echo '</div>';
