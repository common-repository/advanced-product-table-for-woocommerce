<?php

function iwptp_print_styles($data = [])
{
    if (!empty($data['id'])) {
        $google_fonts = get_post_meta(intval($data['id']), 'iwptp_fonts', true);
        if (!empty($fonts = iwptp_sanitize_array(json_decode($google_fonts, true)))) {
            foreach ($fonts as $font) {
                if (!empty($font)) {
                    $font_url = 'https://fonts.googleapis.com/css?family=' . esc_attr($font) . ':100,100italic,200,200italic,300,300italic,400,400italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic';
                    echo wp_kses('<link href="' . esc_url($font_url) . '" rel="stylesheet" type="text/css">', iwptp_allowed_html_tags()); //phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet
                }
            }
        }
    }

    ob_start();
    echo '<style>';
    echo wp_kses(iwptp_parse_media_query_toggle($data), iwptp_allowed_html_tags());
    echo wp_kses(iwptp_parse_style($data), iwptp_allowed_html_tags());
    echo wp_kses(iwptp_parse_elements_style($data), iwptp_allowed_html_tags());

    echo wp_kses(iwptp_parse_columns_style($data), iwptp_allowed_html_tags());
    echo wp_kses(iwptp_parse_css($data), iwptp_allowed_html_tags());

    echo '</style>';
    echo wp_kses(str_replace(array('\r', '\n', '\t', '  ', '   '), array(''), ob_get_clean()), iwptp_allowed_html_tags());
}

$iwptp_breakpoints = array(
    'tablet' => '1199',
    'phone' => '749',
);

// giving extra room for screen orientation change
$iwptp_device = iwptp_get_device();
if ($iwptp_device == 'phone') {
    $iwptp_breakpoints = array(
        'tablet' => '1199',
        'phone' => '849',
    );
} else if ($iwptp_device == 'tablet') {
    $iwptp_breakpoints = array(
        'tablet' => '1399',
        'phone' => '699',
    );
}

function iwptp_parse_style($data = [])
{
    $data = (!empty($data)) ? $data : iwptp_get_table_data();
    $style = $data['style'];

    $css_string = '';

    $divisions = array(
        'laptop',
        'tablet',
        'phone',
        'navigation',
        'mini_cart',
        'checkbox_trigger',
    );

    foreach ($divisions as $division) {

        if (empty($style[$division])) {
            continue; // device has no selectors
        }

        $division_style_string = '';

        // special styling needs
        extract(apply_filters(
            'iwptp_style_division',
            array(
                'style_division' => $style[$division],
                'division_style_string' => $division_style_string,
            )
        ));

        // add in :hover & :selected
        foreach ($style_division as $selector => &$props) {

            if (empty($props) || !is_array($props)) {
                continue;
            }

            // append selectors for :hover :selected props
            foreach ($props as $prop => $val) {
                // collect hover
                if (
                    strlen($prop) > 6 &&
                    ':hover' == substr($prop, -6)
                ) {
                    if (empty($style_division[$selector . ':hover'])) {
                        $style_division[$selector . ':hover'] = [];
                    }
                    $style_division[$selector . ':hover'][substr($prop, 0, -6)] = $val;

                    // collect selected
                } else if (
                    strlen($prop) > 9 &&
                    ':selected' == substr($prop, -9)
                ) {
                    if (empty($style_division[$selector . '.iwptp-active'])) {
                        $style_division[$selector . '.iwptp-active'] = [];
                    }
                    $style_division[$selector . '.iwptp-active'][substr($prop, 0, -9)] = $val;
                } else {
                    continue;
                }

                // remove :hover :selected pseudo props
                unset($props[$prop]);
            }
        }
        unset($props);

        // for the device iterate over all its selectors
        foreach ($style_division as $selector => $props) {

            if (empty($props) || !is_array($props)) {
                // selector has no props or not a selector but special prop like inheritance
                continue;
            }

            // build selector props string
            $props_string = '';
            foreach ($props as $prop => $val) {
                extract(apply_filters('iwptp_style_prop_val', array(
                    'prop' => $prop,
                    'val' => $val,
                    'selector' => $selector,
                )));

                if ($val && gettype($val) == 'string') {
                    $props_string .= $prop . ':' . $val . ';';
                }
            }

            // deliver selector props string
            if ($props_string) {
                $division_style_string .= ' ' . $selector . '{' . $props_string . '}';
            }
        }

        $division_style_string = str_replace('[device]', '', $division_style_string);

        // media query
        // wrapping the style string inside a media query

        //-- laptop
        if ($division == 'laptop') {

            $min_width = '0';

            // apply above phone breakpoint
            if (empty($style['phone']['inherit_tablet_style'])) {
                $min_width = (int) $GLOBALS['iwptp_breakpoints']['phone'] + 1;
            }

            // apply above tablet breakpoint
            if (empty($style['tablet']['inherit_laptop_style'])) {
                $min_width = (int) $GLOBALS['iwptp_breakpoints']['tablet'] + 1;
            }

            $division_style_string = '@media(min-width:' . $min_width . 'px){' . $division_style_string . '}';

            //-- tablet
        } else if ($division == 'tablet') {

            $max_width = $GLOBALS['iwptp_breakpoints']['tablet'];
            $min_width = '0';

            // apply above phone breakpoint
            if (empty($style['phone']['inherit_tablet_style'])) {
                $min_width = (int) $GLOBALS['iwptp_breakpoints']['phone'] + 1;
            }

            $division_style_string = '@media(max-width: ' . $max_width . 'px) and (min-width:' . $min_width . 'px){' . $division_style_string . '}';

            //-- phone
        } else if ($division == 'phone') {

            $max_width = $GLOBALS['iwptp_breakpoints']['phone'];

            // apply upto phone breakpoint
            $division_style_string = '@media(max-width:' . $max_width . 'px){' . $division_style_string . '}';
        }

        // deliver device style string
        $css_string .= $division_style_string;
    }

    $css_string = str_replace(
        array(
            '[container]',
            '[id]',
        ),
        array(
            '#iwptp-' . $data['id'],
            $data['id'],
        ),
        $css_string
    );

    return $css_string;
}

// based on current device width, using media query, show the appropriate table
function iwptp_parse_media_query_toggle($data = [])
{

    $table_data = (!empty($data)) ? $data : iwptp_get_table_data();
    $table_id = $table_data['id'];

    ob_start();

    // device columns
    $laptop_columns = iwptp_get_device_columns_2('laptop', $table_data);
    $tablet_columns = iwptp_get_device_columns_2('tablet', $table_data);
    $phone_columns = iwptp_get_device_columns_2('phone', $table_data);

    $breakpoints = $GLOBALS['iwptp_breakpoints'];

    // tablet
    if ($tablet_columns) {
        echo '@media (max-width: ' . esc_attr($breakpoints['tablet']) . 'px) and (min-width: ' . esc_attr($breakpoints['phone']) . 'px){
                #iwptp-' . esc_attr($table_id) . ' .iwptp-device-tablet {
                    display: block;
                }

                #iwptp-' . esc_attr($table_id) . ' .iwptp-device-laptop,
                #iwptp-' . esc_attr($table_id) . ' .iwptp-device-phone {
                    display: none;
                }
            }';
    }

    // phone
    if ($phone_columns) {
        echo '@media (max-width: ' . esc_attr($breakpoints['phone']) . 'px) {
                #iwptp-' . esc_attr($table_id) . ' .iwptp-device-phone {
                    display: block;
                }

                #iwptp-' . esc_attr($table_id) . ' .iwptp-device-tablet,
                #iwptp-' . esc_attr($table_id) . ' .iwptp-device-laptop {
                    display: none;
                }
            }';
    }

    return ob_get_clean();
}

// css shortcodes facility
function iwptp_parse_css($data = [])
{
    $data = (!empty($data)) ? $data : iwptp_get_table_data();

    if (empty($data['style']['css'])) {
        return;
    }

    $iwptp_selector = '#iwptp-' . $data['id'];
    $arr = array(
        '[container]' => $iwptp_selector,
        '[id]' => $data['id'],
        '[table]' => $iwptp_selector . ' .iwptp-table',
        '[heading_row]' => $iwptp_selector . ' .iwptp-heading-row',

        '[heading_cell]' => $iwptp_selector . ' .iwptp-heading-row .iwptp-heading',
        '[heading_cell_even]' => $iwptp_selector . ' .iwptp-heading-row .iwptp-heading:nth-child(even)',
        '[heading_cell_odd]' => $iwptp_selector . ' .iwptp-heading-row .iwptp-heading:nth-child(odd)',

        '[row]' => $iwptp_selector . ' .iwptp-row',
        '[row_even]' => $iwptp_selector . ' .iwptp-row:nth-child(even)',
        '[row_odd]' => $iwptp_selector . ' .iwptp-row:nth-child(odd)',

        '[cell]' => $iwptp_selector . ' .iwptp-cell',
        '[cell_even]' => $iwptp_selector . ' .iwptp-cell:nth-child(even)',
        '[cell_odd]' => $iwptp_selector . ' .iwptp-cell:nth-child(odd)',

        '[tablet]' => ' @media(max-width: 1199px){',
        '[/tablet]' => '} ',

        '[phone]' => ' @media(max-width: 749px){',
        '[/phone]' => '} ',
    );

    $search = array_keys($arr);
    $replace = array_values($arr);

    return str_replace($search, $replace, $data['style']['css']);
}

// pull out css from the elements
function iwptp_parse_elements_style($data = [])
{
    $data = (!empty($data)) ? $data : iwptp_get_table_data();
    $iwptp_selector = '#iwptp-' . $data['id'];
    $elements = iwptp_get_column_elements($data);
    $css = '';

    foreach ($elements as $element_type => $element_rows) {
        foreach ($element_rows as $element_settings) {
            if (!empty($element_settings['style'])) {
                $element_settings_style = '';

                $css_string = '';
                $style = &$element_settings['style'];
                foreach ($style as $selector => $props) {
                    if (empty($props)) {
                        continue;
                    }

                    $string = '';
                    // collect style props for selector
                    foreach ($props as $prop => $val) {
                        extract(apply_filters('iwptp_style_prop_val', array(
                            'prop' => $prop,
                            'val' => $val,
                            'selector' => $selector,
                        )));

                        if ($val != '') {
                            $string .= $prop . ':' . $val . ';';
                        }
                    }

                    if ($string) {
                        $css_string .= ' ' . $selector . '{' . $string . '}';
                    }
                }

                if ($css_string && $element_settings['settings']) {
                    $container_selector = $iwptp_selector . ' .iwptp-' . $element_type . '-' . sanitize_title($element_settings['settings']);
                    $css_string = str_replace('[container]', $container_selector, $css_string);

                    $css .= $css_string;
                }
            }
        }
    }

    return $css;
}

// pull out css from the columns
function iwptp_parse_columns_style($data = [])
{
    $data = (!empty($data)) ? $data : iwptp_get_table_data();
    $iwptp_selector = '#iwptp-' . $data['id'];
    $devices = array(
        'laptop' => '',
        'tablet' => '1199px',
        'phone' => '749px',
    );

    ob_start();

    foreach ($devices as $device => $max_width) {

        $device_columns = iwptp_get_device_columns($device, $data);

        if (!$device_columns) {
            continue;
        }

        if ($max_width) {
            echo ' @media(max-width:' . esc_attr($max_width) . '){';
        }

        foreach ($device_columns as $column_key => $column) {
            if (empty($column['style'])) {
                continue;
            }

            echo ' ' . esc_attr($iwptp_selector) . ' .iwptp-cell:nth-child(' . (intval($column_key) + 1) . ') {';

            foreach ($column['style'] as $prop => $val) {
                extract(apply_filters('iwptp_style_prop_val', array(
                    'prop' => $prop,
                    'val' => $val,
                    'selector' => $selector,
                )));

                echo esc_attr($prop) . ':' . esc_attr($val) . ';';
            }

            echo '}';
        }

        if ($max_width) {
            echo '}';
        }
    }

    return ob_get_clean();
}

// append "px" to style vals
add_filter('iwptp_style_prop_val', 'iwptp_style_prop_val_filter');
function iwptp_style_prop_val_filter($arr)
{
    if (is_numeric($arr['val']) && !in_array($arr['prop'], array('opacity', 'font-weight'))) {
        $arr['val'] .= 'px';
    }

    return $arr;
}

// parse element and row style
function iwptp_parse_style_2($item, $important = false)
{
    if (empty($item['style'])) {
        return;
    }

    // image width fix
    if (
        !empty($item['type']) &&
        $item['type'] == 'product_image' &&
        !empty($item['style']) &&
        !empty($item['style']['[id]']) &&
        !empty($item['style']['[id]']['max-width'])
    ) {
        $item['style']['[id]']['min-width'] = $item['style']['[id]']['max-width'];
    }

    $id = '.iwptp-' . $item['id'];
    foreach ($item['style'] as $selector => $props) {
        if (!isset($GLOBALS['iwptp_table_data']['style_items'][$selector])) { // elm not already parsed
            $props_string = '';
            $hover_style = [];
            $selected_style = [];

            // border-solid property fix
            if (!empty($props['border-color']) || !empty($props['border-width']) && empty($props['border-style'])) {
                $props['border-style'] = 'solid';
            } else if (!empty($props['border-style']) && empty($props['border-width']) && empty($props['border-color'])) {
                $props['border-style'] = '';
            }

            foreach ($props as $prop => $val) {
                // collect hover state props
                if (strlen($prop) > 6 && ':hover' == substr($prop, -6)) {
                    $hover_style[substr($prop, 0, -6)] = $val;

                    // collect selected state props
                } else if (
                    strlen($prop) > 9 &&
                    ':selected' == substr($prop, -9)
                ) {
                    $selected_style[substr($prop, 0, -9)] = $val;

                    // process normal state props
                } else {
                    extract(apply_filters('iwptp_style_prop_val', array(
                        'prop' => $prop,
                        'val' => $val,
                        'selector' => $selector,
                    )));

                    if ($val && gettype($val) == 'string') {
                        $props_string .= $prop . ':' . $val . ($important ? ' !important' : '') . ';';
                    }
                }
            }

            $selector = str_replace("[id]", $id, $selector);
            $GLOBALS['iwptp_table_data']['style_items'][$selector] = ' ' . $selector . '{' . $props_string . '} ';

            // parse hover
            if (count($hover_style)) {
                iwptp_parse_style_2(array( // dummy elm with bare info
                    'id' => $item['id'],
                    'style' => array(
                        $selector . ':hover' => $hover_style,
                    ),
                ), $important);
            }

            // parse selected
            if (count($selected_style)) {
                iwptp_parse_style_2(array( // dummy elm with bare info
                    'id' => $item['id'],
                    'style' => array(
                        $selector . '.iwptp-active' => $selected_style,
                    ),
                ), $important);
            }
        }
    }
}

function iwptp_item_styles($data = [])
{
    $data = (!empty($data)) ? $data : iwptp_get_table_data();
    if (empty($data['style_items'])) {
        return;
    }

    $style_markup = '<style>';
    foreach ($data['style_items'] as $itm_selector => $itm_style_props) {
        $style_markup .= ' #iwptp-' . $data['id'] . ' ' . sanitize_text_field($itm_style_props);
        $style_markup .= ' body ' . sanitize_text_field($itm_style_props);
    }
    $style_markup .= '</style>';

    echo wp_kses($style_markup, iwptp_allowed_html_tags());
}

// transfer search element width to input wrapper
add_filter('iwptp_element', 'iwptp_style__search_bar_width');
function iwptp_style__search_bar_width($elm)
{
    if (
        !empty($elm['type']) &&
        $elm['type'] == 'search' &&
        !empty($elm['style']) &&
        !empty($elm['style']['[id]']) &&
        !empty($elm['style']['[id]']['width'])
    ) {
        $elm['style']['[id] > .iwptp-search']['width'] = $elm['style']['[id]']['width'];
        unset($elm['style']['[id]']['width']);
    }

    return $elm;
}

// For nav filters, make dropdown heading retain hover props when dropdown menu is hovered
add_filter('iwptp_element', 'iwptp_style__filter_heading_hover');
function iwptp_style__filter_heading_hover($elm)
{
    $heading_selector = '.iwptp-navigation:not(.iwptp-left-sidebar) [id].iwptp-dropdown.iwptp-filter > .iwptp-filter-heading';
    $hover_selector = '.iwptp-navigation:not(.iwptp-left-sidebar) [id].iwptp-dropdown.iwptp-filter:hover > .iwptp-filter-heading';

    if (
        !empty($elm['type']) &&
        in_array($elm['type'], array(
            'sort_by',
            'results_per_page',
            'category_filter',
            'price_filter',
            'attribute_filter',
            'custom_field_filter',
            'taxonomy_filter',
            'availability_filter',
            'on_sale_filter',
            'rating_filter',
        )) &&
        !empty($elm['style']) &&
        !empty($elm['style'][$heading_selector])
    ) {
        $props = array('color', 'background-color', 'border-color');
        foreach ($props as $prop) {
            if (!empty($elm['style'][$heading_selector][$prop . ':hover'])) {
                $val = $elm['style'][$heading_selector][$prop . ':hover'];

                if (empty($elm['style'][$hover_selector])) {
                    $elm['style'][$hover_selector] = [];
                }

                $elm['style'][$hover_selector][$prop] = $val;
            }
        }
    }

    return $elm;
}

add_filter('iwptp_style_division', 'iwptp_style_division');
function iwptp_style_division($arr)
{
    extract($arr); // $style_division, $division_style_string

    // sidebar
    $sidebar_selector = '[container] .iwptp-left-sidebar.iwptp-navigation';
    if (!empty($style_division[$sidebar_selector])) {

        // width
        if (!empty($style_division[$sidebar_selector]['width'])) {
            $width = (float) $style_division[$sidebar_selector]['width'];
            $gap = 30;
            if (!empty($style_division[$sidebar_selector]['gap'])) {
                $gap = (float) $style_division[$sidebar_selector]['gap'];
            }

            ob_start();

            echo '[container] .iwptp-left-sidebar + .iwptp-header,
            [container] .iwptp-left-sidebar + .iwptp-header + .iwptp-responsive-navigation + .iwptp-nav-modal-tpl + .iwptp-table-scroll-wrapper-outer,
            [container] .iwptp-left-sidebar + .iwptp-header + .iwptp-responsive-navigation + .iwptp-nav-modal-tpl + .iwptp-required-but-missing-nav-filter-message,
            [container] .iwptp-left-sidebar + .iwptp-header + .iwptp-responsive-navigation + .iwptp-nav-modal-tpl + .iwptp-no-results.iwptp-device-laptop,
            [container] .iwptp-left-sidebar + .iwptp-header + .iwptp-responsive-navigation + .iwptp-nav-modal-tpl + .iwptp-table-scroll-wrapper-outer + .iwptp-pagination,
            [container] .iwptp-left-sidebar + .iwptp-header + .iwptp-responsive-navigation + .iwptp-nav-modal-tpl + .iwptp-table-scroll-wrapper-outer + .iwptp-in-footer,
            [container] .iwptp-left-sidebar + .iwptp-header + .iwptp-responsive-navigation + .iwptp-nav-modal-tpl + .iwptp-table-scroll-wrapper-outer + .iwptp-in-footer + .iwptp-pagination {
            width: calc(100% - ' . ((float) $style_division[$sidebar_selector]['width'] + $gap) . 'px)
            }';

            $division_style_string .= ' ' . ob_get_clean() . ' ';
        }

        if (!empty($style_division[$sidebar_selector]['gap'])) {
            unset($style_division[$sidebar_selector]['gap']);
        }

        // position
        if (!empty($style_division[$sidebar_selector]['float'])) {
            $float = $style_division[$sidebar_selector]['float'];
            if ($float == 'right') {
                ob_start();

                echo '[container] .iwptp-left-sidebar + .iwptp-header,
                [container] .iwptp-left-sidebar + .iwptp-header + .iwptp-responsive-navigation + .iwptp-nav-modal-tpl + .iwptp-table-scroll-wrapper-outer,
                [container] .iwptp-left-sidebar + .iwptp-header + .iwptp-responsive-navigation + .iwptp-nav-modal-tpl + .iwptp-required-but-missing-nav-filter-message,
                [container] .iwptp-left-sidebar + .iwptp-header + .iwptp-responsive-navigation + .iwptp-nav-modal-tpl + .iwptp-no-results.iwptp-device-laptop,
                [container] .iwptp-left-sidebar + .iwptp-header + .iwptp-responsive-navigation + .iwptp-nav-modal-tpl + .iwptp-table-scroll-wrapper-outer + .iwptp-pagination,
                [container] .iwptp-left-sidebar + .iwptp-header + .iwptp-responsive-navigation + .iwptp-nav-modal-tpl + .iwptp-table-scroll-wrapper-outer + .iwptp-in-footer,
                [container] .iwptp-left-sidebar + .iwptp-header + .iwptp-responsive-navigation + .iwptp-nav-modal-tpl + .iwptp-table-scroll-wrapper-outer + .iwptp-in-footer + .iwptp-pagination {
                    float: left;
                }';

                $division_style_string .= ' ' . ob_get_clean() . ' ';
            }
        }
    }

    // header
    $header_selector = '[container] .iwptp-header.iwptp-navigation';
    if (!empty($style_division[$header_selector])) {
        if (!empty($style_division[$header_selector]['row_gap'])) {
            $row_gap = (float) $style_division[$header_selector]['row_gap'];

            ob_start();
            echo '[container] .iwptp-header > .iwptp-filter-row {
                margin: ' . esc_attr($row_gap) . 'px 0;
            }';

            $division_style_string .= ' ' . ob_get_clean() . ' ';
            unset($style_division[$header_selector]['row_gap']);
        }
    }

    // dropdown heading
    // make dropdown heading retain hover props even when dropdown menu is being hovered
    $dropdown_heading_selector = '[container] .iwptp-header.iwptp-navigation .iwptp-dropdown.iwptp-filter > .iwptp-filter-heading';
    $dropdown_heading_active_selector = 'body [container] .iwptp-header.iwptp-navigation .iwptp-dropdown.iwptp-filter.iwptp-filter--active > .iwptp-filter-heading';
    $dropdown_heading_hover_selector = '[container] .iwptp-header.iwptp-navigation .iwptp-dropdown.iwptp-filter:hover > .iwptp-filter-heading';
    $dropdown_heading_open_selector = '[container] .iwptp-header.iwptp-navigation .iwptp-dropdown.iwptp-filter.iwptp-open > .iwptp-filter-heading';

    if (!empty($style_division[$dropdown_heading_selector])) {
        $props = array('color', 'background-color', 'border-color');

        // apply :active rules to .iwptp-filter--open as well
        foreach ($props as $prop) {
            if (!empty($style_division[$dropdown_heading_selector][$prop . ':active'])) {
                $val = $style_division[$dropdown_heading_selector][$prop . ':active'];

                // add the prop to dropdown active > heading
                if (empty($style_division[$dropdown_heading_active_selector])) {
                    $style_division[$dropdown_heading_active_selector] = [];
                }

                $style_division[$dropdown_heading_active_selector][$prop] = $val;
                unset($style_division[$dropdown_heading_selector][$prop . ':active']);
            }
        }

        // apply :hover rules to .iwptp-open as well
        foreach ($props as $prop) {
            if (!empty($style_division[$dropdown_heading_selector][$prop . ':hover'])) {
                $val = $style_division[$dropdown_heading_selector][$prop . ':hover'];

                // add the prop to dropdown hover > heading
                if (empty($style_division[$dropdown_heading_hover_selector])) {
                    $style_division[$dropdown_heading_hover_selector] = [];
                }

                $style_division[$dropdown_heading_hover_selector][$prop] = $val;

                // add the prop to dropdown open > heading
                if (empty($style_division[$dropdown_heading_open_selector])) {
                    $style_division[$dropdown_heading_open_selector] = [];
                }

                $style_division[$dropdown_heading_open_selector][$prop] = $val;
            }
        }
    }

    return array(
        'style_division' => $style_division,
        'division_style_string' => $division_style_string,
    );
}
