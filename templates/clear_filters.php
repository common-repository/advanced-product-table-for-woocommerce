<?php
if (!defined('ABSPATH')) {
    exit;
}
// no relevant user filter set
if (empty($_GET)) {
    return;
} else {
    $table_id = (string) $GLOBALS['iwptp_table_data']['id'];
    $esc = true;
    foreach (iwptp_sanitize_array($_GET) as $key => $val) {
        if (
            empty($val) ||
            (is_array($val) && !implode($val)) ||
            (strlen($key) <  (strlen($table_id) + 2)) || // key too short to be filter
            in_array(substr($key, strlen($table_id) + 1), array('availability', 'orderby', 'order', 'paged', 'device', 'sc_attrs', 'results_per_page', 'filtered', 'from_shop')) ||
            (substr($key, strlen($table_id) + 1) == 'product_cat' &&
                !empty($_GET['iwptp_category_redirect'])
            )
        ) {
            continue;
        }
        if (substr($key, 0, strlen($table_id)) == $table_id) {
            $esc = false;
            break;
        }
    }

    if ($esc) {
        return;
    }
}

if (empty($GLOBALS['iwptp_nav_later_flag'])) {
    // defer this elm post $nav, to its filter hook
    $placeholder = '{' . $elm_tpl . '-' . wp_rand(0, 10000) . '}';
    $GLOBALS['iwptp_nav_later'][] = array(
        'placeholder' => $placeholder,
        'element' => $element,
        'elm_tpl' => $elm_tpl,
        'elm_type' => $elm_type,
        'product' => $product,
    );
    echo esc_html($placeholder);

    return;
}

if (empty($GLOBALS['iwptp_user_filters'])) {
    return;
}

// skip if only sorting
if (count($GLOBALS['iwptp_user_filters']) == 1 && $GLOBALS['iwptp_user_filters'][0]['filter'] == 'orderby') {
    return;
}

ob_start();

foreach ($GLOBALS['iwptp_user_filters'] as $filter_info) {
    if (
        (empty($filter_info['values'])) ||
        $filter_info['filter'] == 'orderby' ||
        ($filter_info['filter'] == 'price_range' && empty($filter_info['min_price']) && empty($filter_info['max_price']))
    ) {
        continue;
    }

    foreach ($filter_info['values'] as $key => $option) {
        if (empty($filter_info['category_redirect']) && (!empty($filter_info['clear_labels_2']) || !empty($filter_info['clear_label']))) {

?>
            <div class="iwptp-clear-filter" data-iwptp-filter="<?php echo esc_attr($filter_info['filter']); ?>" data-iwptp-taxonomy="<?php echo isset($filter_info['taxonomy']) ? esc_attr($filter_info['taxonomy']) : ''; ?>" data-iwptp-meta-key="<?php echo isset($filter_info['meta_key']) ? esc_attr($filter_info['meta_key']) : ''; ?>" data-iwptp-value="<?php echo esc_attr($option); ?>" <?php if ($filter_info['filter'] == 'search') {
                                                                                                                                                                                                                                                                                                                                                                                                        $name = $table_id . '_search_' . ($key + 1);
                                                                                                                                                                                                                                                                                                                                                                                                        echo 'data-iwptp-search-name="' . esc_attr($name) . '"';
                                                                                                                                                                                                                                                                                                                                                                                                    } ?>>
                <!-- x icon -->
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>

                <?php
                if (!empty($filter_info['clear_labels_2']) && !empty($filter_info['clear_labels_2'][$option])) {
                    echo '<span class="iwptp-filter-label">' . esc_html($filter_info['clear_labels_2'][$option]) . '</span>';
                } else {
                    if (in_array($filter_info['filter'], array('attribute', 'category', 'taxonomy'))) {
                        $term = get_term_by('term_taxonomy_id', $option);
                        $label = $term->name;
                    } else {
                        $label = $option;
                    }

                ?>
                    <span class="iwptp-filter-label"><?php echo esc_attr($filter_info['clear_label']); ?></span><span class="iwptp-separator iwptp-colon">:</span>
                    <span class="iwptp-selected-filter"><?php echo esc_html($label); ?></span>
                <?php
                }
                ?>

            </div>
    <?php
        }
    }
}

$markup = trim(ob_get_clean());
if ($markup) {
    if (empty($reset_label)) {
        $reset_label = 'Clear all';
    }
    ?>
    <div class="iwptp-clear-filters-wrapper <?php echo esc_attr($html_class); ?>">
        <?php if (empty($hide_clear_all)) : ?>
            <a href="javascript:void(0)" class="iwptp-clear-all-filters iwptp-small-device-only"><?php echo esc_html($reset_label); ?></a>
        <?php endif; ?>
        <?php echo wp_kses($markup, iwptp_allowed_html_tags()); ?>
        <?php if (empty($hide_clear_all)) : ?>
            <a href="javascript:void(0)" class="iwptp-clear-all-filters iwptp-big-device-only"><?php echo esc_html($reset_label); ?></a>
        <?php endif; ?>
    </div>
<?php
}
?>