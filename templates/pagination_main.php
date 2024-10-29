<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (empty($products)) {
    if (isset($GLOBALS['iwptp_products'])) {
        $products = $GLOBALS['iwptp_products'];
    } else {
        return;
    }
}

if (!empty($GLOBALS['iwptp_pagination_type'])) {
    $pagination_type = esc_html($GLOBALS['iwptp_pagination_type']);
}

if (empty($pagination_type)) {
    $pagination_type = 'number';
}

if (empty($table_id)) {
    $table_id = $GLOBALS['iwptp_table_data']['id'];
}

?>
<div class="iwptp-pagination <?php if ($products->max_num_pages <= 1) {
                                    echo " iwptp-hide ";
                                }
                                ?>">
    <?php

    $locale = get_locale();
    $next_text = '';
    $prev_text = '';

    $next_text_translations = (!empty($GLOBALS['iwptp_pagination_next_text'])) ? preg_split('/$\R?^/m', trim($GLOBALS['iwptp_pagination_next_text'])) : '';
    if (!empty($next_text_translations) && is_array($next_text_translations)) {
        $next_translations = [];

        foreach ($next_text_translations as $translation_rule) {
            $array = explode(':', $translation_rule);
            if (!empty($array[1])) {
                $next_translations[trim($array[0])] = stripslashes(trim($array[1]));
            } else {
                $next_translations['default'] = stripslashes(trim($array[0]));
            }
        }

        if (empty($next_translations[$locale])) {
            if (!empty($next_translations['default'])) {
                $next_text = $next_translations['default'];
            } else if (!empty($next_translations['en_US'])) {
                $next_text = $next_translations['en_US'];
            }
        } else {
            $next_text = $next_translations[$locale];
        }
    }

    $prev_text_translations = (!empty($GLOBALS['iwptp_pagination_prev_text'])) ? preg_split('/$\R?^/m', trim($GLOBALS['iwptp_pagination_prev_text'])) : '';
    if (!empty($prev_text_translations) && is_array($prev_text_translations)) {
        $prev_translations = [];

        foreach ($prev_text_translations as $translation_rule) {
            $array = explode(':', $translation_rule);
            if (!empty($array[1])) {
                $prev_translations[trim($array[0])] = stripslashes(trim($array[1]));
            } else {
                $prev_translations['default'] = stripslashes(trim($array[0]));
            }
        }

        if (empty($prev_translations[$locale])) {
            if (!empty($prev_translations['default'])) {
                $prev_text = $prev_translations['default'];
            } else if (!empty($prev_translations['en_US'])) {
                $prev_text = $prev_translations['en_US'];
            }
        } else {
            $prev_text = $prev_translations[$locale];
        }
    }

    if (!empty($next_text)) {
        $next_text = '<span style="padding-left: 10px;">' . $next_text . '</span>';
    }

    if (!empty($prev_text)) {
        $next_text = '<span style="padding-right: 10px;">' . $prev_text . '</span>';
    }

    $args = array(
        'format' => '?' . $table_id . '_paged=%#%',
        'total' => $products->max_num_pages,
        'current' => max(1, $products->query_vars['paged']),
        'prev_next' => true,
        'prev_text' => wp_kses(iwptp_get_icon('chevron-left'), iwptp_allowed_html_tags()) . $prev_text,
        'next_text' => $next_text . wp_kses(iwptp_get_icon('chevron-right'), iwptp_allowed_html_tags()),
        'type' => 'array',
        'end_size' => 1,
        'mid_size' => 1,
        'before_page_number' => '',
        'after_page_number' => '',
        'add_args' => false,
    );

    if ($pagination_type == 'next_prev') {
        $args['end_size'] = 0;
        $args['mid_size'] = 0;
    }

    $items = paginate_links(apply_filters('iwptp_pagination_options', $args));
    $output = '';
    if (!empty($items) && is_array($items)) {
        foreach ($items as $item) {
            if ($pagination_type == 'next_prev') {
                if (strpos($item, 'prev') !== false || strpos($item, 'next') !== false) {
                    $output .= str_replace('page-numbers', 'iwptp-page-numbers', $item);
                }
            } else {
                $output .= str_replace('page-numbers', 'iwptp-page-numbers', $item);
            }
        }
    }

    echo wp_kses(str_replace(' current', ' current iwptp-active', $output), iwptp_allowed_html_tags());
    ?>
</div>