<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (empty($display_type)) {
    $display_type = 'radio_single';
}

if (!$product->is_type('variable')) {
    if (
        in_array($display_type, array('radio_multiple', 'dropdown')) &&
        !empty($non_variable_template) &&
        $mkp = iwptp_parse_2($non_variable_template)
    ) {
        echo '<div class="iwptp-non-variation-output">' . wp_kses($mkp, iwptp_allowed_html_tags()) . '</div>';
    }

    return;
}

if (!$available_variations = iwptp_get_variations($product)) {
    return;
}

$price_format = get_woocommerce_price_format();
$currency_symbol =  get_woocommerce_currency_symbol();

$default_variation = iwptp_get_default_variation($product);

// dropdown
if ($display_type == 'dropdown') {

    ob_start();
?><div class="iwptp-select-variation-dropdown-wrapper <?php echo esc_attr($html_class); ?>"><select class="iwptp-select-variation-dropdown">
            <?php
            if (
                empty($hide_select) ||
                !$default_variation
            ) {
            ?>
                <option value=""><?php echo !empty($select_label) ? esc_html($select_label) : esc_html__('Select', 'woocommerce'); ?></option>
            <?php
            }
            ?>

            <?php
            $out_of_stock_options = [];
            foreach ($available_variations as $variation) {
                $label = '';
                if (!strlen(implode(array_values($variation['attributes'])))) { // no terms
                    continue;
                }
                foreach ($variation['attributes'] as $attr => $term) {
                    if (!$term) {
                        continue;
                    }
                    $taxonomy = substr($attr, 10);
                    $term_obj = get_term_by('slug', $term, $taxonomy);
                    $term_label = $term_obj ? $term_obj->name : $term;

                    if (empty($hide_attributes)) {
                        $label .= wc_attribute_label($taxonomy);
                        $label .= $attribute_term_separator;
                    }
                    $label .= $term_label;
                    $label .= $attribute_separator;
                }

                $out_of_stock = !$variation['is_in_stock'];

                $label = substr($label, 0, -strlen($attribute_separator));

                $variation_price = apply_filters('iwptp_select_variation_price', $variation['display_price'], $variation);

                if (
                    empty($hide_price) &&
                    '' !== $variation_price
                ) {
                    $label .= ' &mdash; ' . sprintf($price_format, $currency_symbol, $variation_price);
                }

                if (
                    !$out_of_stock &&
                    empty($hide_stock)
                ) {
                    $availability = wp_strip_all_tags($variation['availability_html']);
                    if (!empty($availability)) {
                        $label .= ' (' . trim($availability) . ')';
                    }
                }

                $selected = '';
                if ($default_variation && $default_variation['variation_id'] == $variation['variation_id']) {
                    $selected = ' selected ';
                }

                $variation_json = wp_json_encode($variation);
                $variation_json__html_esc = function_exists('wc_esc_json') ? wc_esc_json($variation_json) : _wp_specialchars($variation_json, ENT_QUOTES, 'UTF-8', true);


                $output = '<option class="' . (iwptp_is_incomplete_variation($product, $variation) ? 'iwptp-partial_match' : 'iwptp-complete_match') . '" value="' . $variation['variation_id'] . '" data-variation-id="' . $variation['variation_id'] . '" ' . $selected . ' data-iwptp-attributes="' . esc_attr(wp_json_encode($variation['attributes'])) . '" data-iwptp-variation="' . $variation_json__html_esc . '" ' . ($out_of_stock ? ' disabled ' : '') . '>' . apply_filters('iwptp_select_variation_label_for_dropdown_option', $label, $variation, $element) . '</option>';

                if ($out_of_stock) {
                    $out_of_stock_options[] = $output;
                } else {
                    echo wp_kses($output, iwptp_allowed_html_tags());
                }
            }

            if (count($out_of_stock_options)) {
                echo '<optgroup label="' . esc_attr__('Out of stock', 'woocommerce') . ':">';
                foreach ($out_of_stock_options as $option) {
                    echo wp_kses($option, iwptp_allowed_html_tags());
                }
                echo '</optgroup>';
            }

            ?>
        </select></div><?php

                        echo wp_kses(ob_get_clean(), iwptp_allowed_html_tags());

                        return;
                    }

                    // radio multiple
                    if ($display_type == 'radio_multiple') {

                        ob_start();
                        $rand_name = wp_rand(0, 100000000);
                        ?><div class="iwptp-select-variation-radio-multiple-wrapper <?php echo esc_attr($html_class); ?>">
        <?php
                        foreach ($available_variations as $variation) {
                            $label = '';
                            if (!strlen(implode(array_values($variation['attributes'])))) { // no terms
                                continue;
                            }
                            foreach ($variation['attributes'] as $attr => $term) {
                                if (!$term) {
                                    continue;
                                }
                                $taxonomy = substr($attr, 10);
                                $term_obj = get_term_by('slug', $term, $taxonomy);
                                $term_label = $term_obj ? $term_obj->name : $term;

                                if (empty($hide_attributes)) {
                                    $label .= wc_attribute_label($taxonomy);
                                    $label .= $attribute_term_separator;
                                }
                                $label .= $term_label;
                                $label .= $attribute_separator;
                            }

                            if (!$variation['is_in_stock']) {
                                $out_of_stock = ' iwptp-variation-out-of-stock ';
                            } else {
                                $out_of_stock = '';
                            }

                            $label = substr($label, 0, -strlen($attribute_separator));

                            $variation_price = apply_filters('iwptp_select_variation_price', $variation['display_price'], $variation);

                            if (
                                empty($hide_price) &&
                                '' !== $variation_price
                            ) {
                                $label .= ' &mdash; ' . sprintf($price_format, $currency_symbol, $variation_price);
                            }

                            if (
                                !$out_of_stock &&
                                empty($hide_stock)
                            ) {
                                $availability = wp_strip_all_tags($variation['availability_html']);
                                if (!empty($availability)) {
                                    $label .= ' (' . trim($availability) . ')';
                                }
                            }

                            $selected = '';
                            if ($default_variation && $default_variation['variation_id'] == $variation['variation_id']) {
                                $selected = ' checked="checked" ';
                            }

                            $variation_json = wp_json_encode($variation);
                            $variation_json__html_esc = function_exists('wc_esc_json') ? wc_esc_json($variation_json) : _wp_specialchars($variation_json, ENT_QUOTES, 'UTF-8', true);

        ?>
            <label class="iwptp-select-variation <?php echo esc_attr($out_of_stock); ?> <?php echo iwptp_is_incomplete_variation($product, $variation) ? 'iwptp-partial_match' : 'iwptp-complete_match'; ?> <?php echo $selected ? 'iwptp-selected' : ''; ?>" data-variation-id="<?php echo esc_attr($variation['variation_id']); ?>" data-iwptp-attributes="<?php echo esc_attr(wp_json_encode($variation['attributes'])); ?>" data-iwptp-variation="<?php echo esc_attr($variation_json__html_esc); ?>" title="<?php echo $out_of_stock ? esc_attr__('Out of stock', 'woocommerce') : ''; ?>">
                <input class="iwptp-variation-radio" type="radio" value="<?php echo esc_attr($variation['variation_id']); ?>" <?php echo esc_attr($selected); ?> <?php echo $out_of_stock ? 'disabled' : ''; ?> name="<?php echo esc_attr($rand_name); ?>">
                <?php echo wp_kses(apply_filters('iwptp_select_variation_label_for_input_radio', $label, $variation, $element), iwptp_allowed_html_tags()); ?>
            </label>
        <?php
                            if (!empty($separate_lines)) {
                                echo '<br>';
                            }
                        }
        ?>
    </div><?php

                        echo wp_kses(ob_get_clean(), iwptp_allowed_html_tags());

                        return;
                    }

                    // radio single
                    if (empty($template) || empty($attribute_terms)) {
                        return;
                    }

                    $attributes = [];
                    foreach ($attribute_terms as $key => $value) {
                        $attributes['attribute_' . $value['taxonomy']] = $value['term'];
                    }

                    $match = iwptp_find_closests_matching_product_variation($product, $attributes);

                    if ($match) { // found variation, let's use it

                        $variation_id = $match['variation_id'];
                        $variation_attributes = $match['variation_attributes'];
                        $variation = wc_get_product($variation_id);
                        $template = str_replace('[variation_name]', $variation_name, iwptp_parse_2($template, $variation));

                        if ($template) {
                            if (!$variation->is_in_stock()) {
                                $out_of_stock = ' iwptp-variation-out-of-stock ';
                            } else {
                                $out_of_stock = '';
                            }

                            // default variation
                            if ($product->get_default_attributes()) {

                                $default_attributes = [];
                                foreach ($product->get_default_attributes() as $key => $value) {
                                    $default_attributes['attribute_' . $key] = $value;
                                }

                                $default_variation_match = iwptp_find_closests_matching_product_variation($product, $default_attributes);

                                if (
                                    $default_variation_match &&
                                    $default_variation_match['variation_id'] == $variation_id
                                ) {
                                    $html_class .= ' iwptp-selected ';
                                }
                            }

                            $variation_json = wp_json_encode($match['variation']);
                            $variation_json__html_esc = function_exists('wc_esc_json') ? wc_esc_json($variation_json) : _wp_specialchars($variation_json, ENT_QUOTES, 'UTF-8', true);
                            echo '<label class="iwptp-select-variation iwptp-' . esc_attr($match['type']) . ' ' . esc_attr($out_of_stock) . ' ' . esc_attr($html_class) . '" data-iwptp-attributes="' . esc_attr(wp_json_encode($variation_attributes)) . '" data-variation-id="' . esc_attr($variation->get_id()) . '" data-iwptp-variation="' . esc_attr($variation_json__html_esc) . '" >' . wp_kses($template, iwptp_allowed_html_tags()) . '</label>';
                        }

                        return;
                    }

                    if (!empty($not_exist_template)) {
                        if ($parsed = iwptp_parse_2($not_exist_template)) {
                            echo '<span class="iwptp-variation-not-exist">' . wp_kses($parsed, iwptp_allowed_html_tags()) . '</span>';
                        }
                    }
