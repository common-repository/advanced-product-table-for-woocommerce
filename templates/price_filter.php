<?php
if (!defined('ABSPATH')) {
    exit;
}

// min & max inputs
$table_id = $GLOBALS['iwptp_table_data']['id'];

$input_field_name = $table_id . '_price_range';

$min_input_field_name = $input_field_name . '_min';
$max_input_field_name = $input_field_name . '_max';

$selected_min = !empty($_GET[$min_input_field_name]) ? sanitize_text_field($_GET[$min_input_field_name]) : '';
$selected_max = !empty($_GET[$max_input_field_name]) ? sanitize_text_field($_GET[$max_input_field_name]) : '';

// pre-selected

if ($pre_selected = iwptp_get_nav_filter('price_range')) {
    if (empty($_GET[$table_id . '_filtered'])) {
        // apply
        //-- min
        $_GET[$min_input_field_name] = $_REQUEST[$min_input_field_name] = isset($pre_selected['min_price']) ? $pre_selected['min_price'] : '';
        //-- max
        $_GET[$max_input_field_name] = $_REQUEST[$max_input_field_name] = isset($pre_selected['max_price']) ? $pre_selected['max_price'] : '';
    } else {
        // remove
        iwptp_clear_nav_filter('price_range');
    }
}

if (
    empty($display_type) ||
    (!empty($position) && $position === 'left_sidebar')
) {
    $display_type = 'dropdown';
}

if ($display_type == 'dropdown') {
    $container_html_class = 'iwptp-dropdown iwptp-filter iwptp-range-filter ' . $html_class;
    $heading_html_class = 'iwptp-dropdown-label';
    $options_container_html_class = 'iwptp-dropdown-menu';
    $single_option_container_html_class = 'iwptp-dropdown-option';

    if (empty($heading)) {
        $heading = __('Price range', 'ithemeland-woocommerce-product-table-pro-lite');
    }
} else { // row
    $container_html_class = 'iwptp-options-row iwptp-filter iwptp-range-filter ' . $html_class;
    $heading_html_class = 'iwptp-options-heading';
    $options_container_html_class = 'iwptp-options';
    $single_option_container_html_class = 'iwptp-option';

    if (empty($heading)) {
        $heading = '';
    }

    if (!empty($heading_separate_line)) {
        $container_html_class .= ' iwptp-heading-separate-line ';
    }
}

if (empty($range_options)) {
    $range_options = [];
}

foreach ($range_options as $option) {
    if (empty($option['min'])) {
        $option['min'] = '';
    }

    if (empty($option['max'])) {
        $option['max'] = '';
    }

    // get selected
    if ($selected_min == $option['min'] && $selected_max == $option['max']) {
        $selected_range_option = $option;

        if (empty($selected_range_option['clear_label'])) {
            if (empty($option['min'])) {
                $selected_range_option['clear_label'] = 'Price range: Upto ' . iwptp_price($option['max'], false, false);
            } else if (empty($option['max'])) {
                $selected_range_option['clear_label'] = 'Price range: ' . iwptp_price($option['min'], false, false) . '+';
            } else {
                $selected_range_option['clear_label'] = 'Price range: ' . iwptp_price($option['min'], false, false) . ' - ' . iwptp_price($option['max'], false, false);
            }
        }
    }
}

// heading row
if (!$heading = iwptp_parse_2($heading)) {
    $container_html_class .= ' iwptp-no-heading iwptp-filter-open';
}

$compare = 'BETWEEN';

// min max 
if ($compare == 'BETWEEN') {
    if (
        !isset($min) ||
        trim($min) === '' ||
        !isset($max) ||
        trim($max) === ''
    ) {
        $range = iwptp_get_post_meta_min_max('_price');

        if (
            !isset($min) ||
            trim($min) === ''
        ) {
            $min = $range['min'];
        }

        if (
            !isset($max) ||
            trim($max) === ''
        ) {
            $max = $range['max'];
        }
    }
}

// open filter accordion
$input_field_name = $table_id . '_price_range';
if (!empty($_REQUEST[$input_field_name])) {
    $container_html_class .= ' iwptp-filter-open';
}

if (
    $compare == 'BETWEEN' &&
    (
        (!empty($_REQUEST[$table_id . '_price_range_min']) &&
            $_REQUEST[$table_id . '_price_range_min'] != $min
        ) ||
        (!empty($_REQUEST[$table_id . '_price_range_max']) &&
            $_REQUEST[$table_id . '_price_range_max'] != $max
        )
    )
) {
    $container_html_class .= ' iwptp-filter-open';
}

if (!empty($accordion_always_open)) {
    $container_html_class .= ' iwptp-filter-open';
}

// heading format when option is selected 
if (empty($heading_format__op_selected)) {
    $heading_format__op_selected = 'only_heading';
}

?>
<div class="<?php echo esc_attr($container_html_class); ?>" data-iwptp-heading_format__op_selected="<?php echo esc_attr($heading_format__op_selected); ?>" data-iwptp-filter="price_range">

    <div class="iwptp-filter-heading">
        <!-- label -->
        <span class="<?php echo esc_attr($heading_html_class); ?>"><?php echo wp_kses(iwptp_parse_2($heading), iwptp_allowed_html_tags()); ?></span>
        <!-- icon -->
        <?php echo wp_kses(iwptp_icon('chevron-down'), iwptp_allowed_html_tags()); ?>
    </div>

    <!-- options menu -->
    <div class="<?php echo esc_attr($options_container_html_class); ?>">
        <?php

        $min_input_field_name = $input_field_name . '_min';
        $max_input_field_name = $input_field_name . '_max';

        $selected_min = !empty($_GET[$min_input_field_name]) ? sanitize_text_field($_GET[$min_input_field_name]) : '';
        $selected_max = !empty($_GET[$max_input_field_name]) ? sanitize_text_field($_GET[$max_input_field_name]) : '';

        $value = ($selected_min ? iwptp_price($selected_min, false, false) : 0) . ($selected_max ? ' - ' . iwptp_price($selected_max, false, false) : '+');

        // use filter in query
        $filter_info = array(
            'filter'        => 'price_range',
            'values'        => array($value),
            'min_price'     => $selected_min,
            'max_price'     => $selected_max,
            'clear_label'   => 'Price range',
        );

        // clear label
        if (!empty($selected_range_option)) {
            $filter_info['clear_labels_2'] = array(
                $value => $selected_range_option['clear_label'],
            );
        } else {
            $filter_info['clear_labels_2'] = array(
                $value => 'Price range : ' . $value,
            );

            if (empty($selected_min)) {
                if (empty($no_min_clear_label)) {
                    $no_min_clear_label = '[filter] : Upto [max]';
                }
                $clear_label = $no_min_clear_label;
            } else if (empty($selected_max)) {
                if (empty($no_max_clear_label)) {
                    $no_max_clear_label = '[filter] : [min]+';
                }
                $clear_label = $no_max_clear_label;
            } else {
                if (empty($min_max_clear_label)) {
                    $min_max_clear_label = '[filter] : [min] - [max]';
                }
                $clear_label = $min_max_clear_label;
            }

            $clear_label =  str_replace(
                array('[filter]', '[min]', '[max]'),
                array('Price range', $selected_min ? iwptp_price($selected_min, false, false) : '', $selected_max ? iwptp_price($selected_max, false, false) : ''),
                $clear_label
            );

            $filter_info['clear_labels_2'] = array(
                $value => $clear_label,
            );
        }

        iwptp_update_user_filters($filter_info, true);

        // show all option
        if (
            !empty($range_options) &&
            !iwptp_is_template_empty($show_all_label)
        ) {

            if (
                (empty($_GET[$input_field_name]) ||
                    ($_GET[$input_field_name] === array('') ||
                        $_GET[$input_field_name] === "option_1"
                    )
                ) &&
                (
                    (empty($selected_min) &&
                        empty($selected_max)
                    ) ||
                    (isset($selected_min) &&
                        isset($selected_max) &&

                        $selected_min == $min &&
                        $selected_max == $max
                    )
                )
            ) {
                $checked = ' checked="checked" ';
            } else {
                $checked = '';
            }

        ?>
            <label class="<?php echo esc_attr($single_option_container_html_class); ?>">
                <!-- radio -->
                <input type="radio" value="" class="iwptp-filter-radio" <?php echo esc_attr($checked); ?> name="<?php echo esc_attr($input_field_name); ?>" data-iwptp-range-min="" data-iwptp-range-max="">
                <!-- option label -->
                <span><?php echo wp_kses(iwptp_parse_2($show_all_label), iwptp_allowed_html_tags()); ?></span>
            </label>
            <?php
        }

        // range options

        if (!empty($range_options)) {

            foreach ($range_options as $option_index => $option) {

                if (empty($option['min'])) {
                    $option['min']  = '';
                }

                if (empty($option['max'])) {
                    $option['max']  = '';
                }

                // option was selected or not?
                if (
                    (float) $option['min'] == (float) $selected_min &&
                    (float) $option['max'] == (float) $selected_max
                ) {
                    $selected = ' checked="checked" ';
                } else {
                    $selected = '';
                }

            ?>
                <label class="<?php echo esc_attr($single_option_container_html_class); ?>">
                    <!-- radio -->
                    <input type="radio" value="option_<?php echo esc_attr($option_index); ?>" class="iwptp-filter-radio" <?php echo esc_attr($selected); ?> name="<?php echo esc_attr($input_field_name); ?>" data-iwptp-range-min="<?php echo !empty($option['min']) ? esc_attr($option['min']) : ''; ?>" data-iwptp-range-max="<?php echo !empty($option['max']) ? esc_attr($option['max']) : ''; ?>">
                    <!-- option label -->
                    <span><?php echo !empty($option['label']) ? wp_kses(iwptp_parse_2($option['label']), iwptp_allowed_html_tags()) : 'Range option'; ?></span>
                </label>
        <?php
            }
        }

        // min-max input option
        $html_maybe_hide_class = '';

        if (empty($step)) {
            $step = 1;
        }

        // remove unnecesarily applied range nav filter
        if (
            isset($_GET[$min_input_field_name]) &&
            $_GET[$min_input_field_name] == $min &&
            isset($_GET[$max_input_field_name]) &&
            $_GET[$max_input_field_name] == $max
        ) {
            unset($_GET[$min_input_field_name]);
            unset($_GET[$max_input_field_name]);

            iwptp_clear_nav_filter('price_range');
        }

        if (empty($custom_min_max_enabled)) {
            $html_maybe_hide_class = 'iwptp-hide';
        } else {

            if (!empty($range_slider_enabled)) {

                if ($selected_min === '') {
                    $selected_min = $min;
                }

                if ($selected_max === '') {
                    $selected_max = $max;
                }
            }
        }

        $actual_max = $max;
        if ( // max fix
            !empty($min) &&
            is_float($min)
        ) {
            $max = $max + 1;
        }

        ?>
        <div class="iwptp-range-options-main <?php echo esc_attr($single_option_container_html_class . ' ' . $html_maybe_hide_class); ?>">
            <input type="number" class="iwptp-range-input-min" name="<?php echo esc_attr($min_input_field_name); ?>" value="<?php echo esc_attr($selected_min); ?>" placeholder="<?php echo !empty($min_label) ? esc_attr($min_label) : 'Min'; ?>" min="<?php echo esc_attr($min); ?>" max="<?php echo esc_attr($max); ?>" data-iwptp-actual-max="<?php echo esc_attr($actual_max); ?>" step="<?php echo esc_attr($step); ?>">
            <span class="iwptp-range-input-separator">
                <?php echo !empty($to_label) ? esc_html($to_label) : 'to'; ?>
            </span>
            <input type="number" class="iwptp-range-input-max" name="<?php echo esc_attr($max_input_field_name); ?>" value="<?php echo esc_attr($selected_max); ?>" placeholder="<?php echo !empty($max_label) ? esc_attr($max_label) : 'Max'; ?>" min="<?php echo esc_attr($min); ?>" max="<?php echo esc_attr($max); ?>" data-iwptp-actual-max="<?php echo esc_attr($actual_max); ?>" step="<?php echo esc_attr($step); ?>">
            <span class="iwptp-range-submit-button">
                <?php echo !empty($go_label) ? esc_html($go_label) : 'GO'; ?>
            </span>

            <?php if (!empty($range_slider_enabled)) : ?>
                <div class="iwptp-range-slider-wrapper">
                    <input type="range" class="iwptp-range-slider" min="<?php echo esc_attr($min); ?>" max="<?php echo esc_attr($actual_max); ?>" step="<?php echo esc_attr($step); ?>" value="<?php echo esc_attr($selected_min . ',' . $selected_max); ?>" data-iwptp-initial-value="<?php echo esc_attr($selected_min . ',' . $selected_max); ?>" />
                </div>
            <?php endif; ?>

        </div>

    </div>

</div>