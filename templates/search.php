<?php
if (!defined('ABSPATH')) {
    exit;
}

++$GLOBALS['iwptp_search_count'];

$keyword = '';
$table_id = $GLOBALS['iwptp_table_data']['id'];
$param = $table_id . '_search_' . $GLOBALS['iwptp_search_count'];
$char_limit = 100;

if (!empty($_GET[$param])) {
    $keyword =  sanitize_text_field(substr(stripslashes($_GET[$param]), 0, $char_limit));

    $filter_info = array(
        'filter' => 'search',
        'values' => array($keyword),
        'match_type' => isset($match_type) ? $match_type : 'LIKE',
        'searches' => array(
            array(
                'keyword' => $keyword,
                'target' => isset($target) ? $target : '',
                'custom_fields' => empty($custom_fields) ? [] : $custom_fields,
                'attributes' => empty($attributes) ? [] : $attributes,
                'keyword_separator' => isset($keyword_separator) ? $keyword_separator : ' ',
            )
        )
    );

    if ($prev_search = iwptp_get_nav_filter('search')) {
        $filter_info['searches'] = array_merge($filter_info['searches'], $prev_search['searches']);
    }

    if (!empty($clear_label)) {
        $filter_info['clear_labels_2'] = array(
            // $keyword => str_replace( '[kw]', htmlentities( $keyword ), $clear_label ),
            $keyword => str_replace('[kw]', esc_html($keyword), $clear_label),
        );
    } else {
        $filter_info['clear_labels_2'] = array(
            // $keyword => __('Search') . ' : ' . htmlentities( $keyword ),
            $keyword => __('Search') . ' : ' . esc_html($keyword),
        );
    }

    $single = false;

    iwptp_update_user_filters($filter_info, $single);
}

$search_label = '';
$placeholder = !empty($placeholder) ? $placeholder : __('Search', 'ithemeland-woocommerce-product-table-pro');


if (!empty($heading) && !empty($heading_separate_line)) {
    $html_class .= ' iwptp-search-heading-separate-line ';
}

if (!isset($reset_others) || $reset_others) {
    $html_class .= ' iwptp-search--reset-others ';
}

?>
<div class="iwptp-search-wrapper <?php echo esc_attr($html_class); ?>">
    <?php if (!empty($heading)) : ?>
        <div class="iwptp-search-heading"><?php echo wp_kses(iwptp_parse_2($heading), iwptp_allowed_html_tags()); ?></div>
    <?php endif; ?>
    <div class="iwptp-search <?php if (!empty($keyword)) echo 'iwptp-active'; ?>" data-iwptp-table-id="<?php echo esc_attr($GLOBALS['iwptp_table_data']['id']); ?>">

        <!-- input -->
        <input class="iwptp-search-input" type="search" name="<?php echo esc_attr($param); ?>" data-iwptp-value="<?php echo esc_attr(htmlentities($keyword)); ?>" placeholder="<?php echo esc_attr(do_shortcode($placeholder)); ?>" value="<?php echo esc_attr(htmlentities($keyword)); ?>" autocomplete="off" spellcheck="false" />

        <!-- submit -->
        <span class="iwptp-search-submit">
            <?php echo wp_kses(iwptp_icon('search', 'iwptp-search-submit-icon'), iwptp_allowed_html_tags()); ?>
        </span>

        <!-- clear -->
        <?php if (!empty($keyword)) { ?>
            <span href="javascript:void(0)" class="iwptp-search-clear">
                <?php echo wp_kses(iwptp_icon('x', 'iwptp-search-clear-icon'), iwptp_allowed_html_tags()); ?>
            </span>
        <?php } ?>

    </div>
</div>