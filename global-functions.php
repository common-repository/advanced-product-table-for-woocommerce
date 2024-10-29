<?php
// shortcode

function iwptp_shortcode_product_table($attrs = [], $table_data = [])
{
    foreach ($attrs as $key => &$val) {
        if (!in_array($key, $GLOBALS['iwptp_permitted_shortcode_attributes'])) {
            unset($attrs[$key]);
        }
    }

    if (empty($attrs['id'])) {
        $post_title = !empty($attrs['name']) ? $attrs['name'] : (!empty($attrs['title']) ? $attrs['title'] : '');
        $attrs['id'] = iwptp_get_table_id_from_name($post_title);
    }

    if (empty($attrs['id'])) {
        return;
    }

    $attrs = apply_filters('iwptp_shortcode_attributes', (array) $attrs);

    // gets table data, applies filters and caches in global variable

    $GLOBALS['iwptp_table_data'] = (!empty($table_data)) ? $table_data : iwptp_get_table_data($attrs['id'], 'view');

    if ($error_message = iwptp_sc_error_checks($GLOBALS['iwptp_table_data'], $attrs)) {
        $markup = $error_message;
    } else {
        if (!empty($GLOBALS['iwptp_table_instance'])) {
            $prev_global__iwptp_table_instance = $GLOBALS['iwptp_table_instance'];
        }
        if (!empty($GLOBALS['iwptp_table_data'])) {
            $prev_global__iwptp_table_data = $GLOBALS['iwptp_table_data'];
        }
        if (!empty($GLOBALS['product'])) {
            $prev_global__product = $GLOBALS['product'];
        }
        if (!empty($GLOBALS['post'])) {
            $prev_global__post = $GLOBALS['post'];
        }

        require_once(IWPTPL_PLUGIN_PATH . 'class-wc-shortcode-product-table.php');
        $GLOBALS['iwptp_table_instance'] = new IWPTPL_WC_Shortcode_Product_Table($attrs, 'it_product_table', $table_data);
        $content = iwptp_remove_product_table_shortcode($GLOBALS['iwptp_table_instance']->get_content());
        $markup = apply_filters('iwptp_markup', do_shortcode($content));
        unset($GLOBALS['iwptp_table_data']);
        unset($GLOBALS['iwptp_table_instance']);

        if (!empty($prev_global__iwptp_table_instance)) {
            $GLOBALS['iwptp_table_instance'] = $prev_global__iwptp_table_instance;
        }
        if (!empty($prev_global__iwptp_table_data)) {
            $GLOBALS['iwptp_table_data'] = $prev_global__iwptp_table_data;
        }
        if (!empty($prev_global__product)) {
            $GLOBALS['product'] = $prev_global__product;
        }
        if (!empty($prev_global__post)) {
            $GLOBALS['post'] = $prev_global__post;
        }
    }

    return $markup;
}

function iwptp_license_hash_generate($license_data)
{
    return (empty($license_data) || !isset($license_data['license_key']) || !isset($license_data['email']))
        ? md5(wp_rand(100000, 999999))
        : md5($license_data['license_key'] . 'iwptp' . $license_data['email'] . $license_data['domain']);
}

// get / cache global settings
function iwptp_get_settings_data($ctx = 'view')
{
    global $iwptp_settings;

    if (empty($iwptp_settings)) {
        iwptp_ensure_default_settings();

        if (!$iwptp_settings = iwptp_update_settings_data()) {
            $data = iwptp_sanitize_array(json_decode(stripslashes(get_option('iwptp_settings', '')), true));
            $iwptp_settings = apply_filters('iwptp_settings', $data, $ctx);
        }
    }

    return $iwptp_settings;
}

/* upload initial plugin data */
function iwptp_ensure_default_settings()
{
    if (!get_option('iwptp_settings')) {
        update_option('iwptp_settings', addslashes(wp_json_encode(array(
            'version' => IWPTPL_VERSION,
            'timestamp' => time(),

            'archive_override' => array(
                'default' => '',
                'shop' => 'default',
                'search' => '',

                'category' => array(
                    'default' => 'default',
                    'other_rules' => array(
                        array(
                            'category' => [],
                            'table_id' => '',
                        ),
                    ),
                ),

                'attribute' => array(
                    'default' => 'default',
                    'other_rules' => array(
                        array(
                            'attribute' => [],
                            'table_id' => '',
                        ),
                    ),
                ),

                'tag' => array(
                    'default' => 'default',
                    'other_rules' => array(
                        array(
                            'tag' => [],
                            'table_id' => '',
                        ),
                    ),
                ),

            ),

            'cart_widget' => array(
                'toggle' => 'enabled',
                'r_toggle' => 'enabled',
                'link' => 'cart',
                'cost_source' => 'subtotal',
                'labels' => array(
                    'item'          => "en_US: Item\r\nfr_FR: Article",
                    'items'         => "en_US: Items\r\nfr_FR: Articles",
                    'view_cart'     => "en_US: View Cart\r\nfr_FR: Voir le panier",
                    'extra_charges'  => "en_US: Extra charges may apply\r\nfr_FR: Les taxes peuvent s'appliquer",
                ),
                'style' => array(
                    'background-color' => '#4CAF50',
                    'border-color' => 'rgba(0, 0, 0, .1)',
                    'bottom' => '50',
                ),
            ),

            'modals' => array(
                'labels' => array(
                    'filters'   => "en_US: Filters\r\nfr_FR: Filtres",
                    'sort'      => "en_US: Sort results\r\nfr_FR: Trier les résultats",
                    'reset'     => "en_US: Reset\r\nfr_FR: Rafraîchir",
                    'apply'     => "en_US: Apply\r\nfr_FR: Appliquer",
                ),
            ),

            'no_results' => array(
                'label' => 'No results found. [link]Clear filters[/link] and try again?',
            ),

            'search' => $GLOBALS['IWPTPL_SEARCH_DATA'],
            'search_localization' => [
                'relevance_label'   => "en_US: Sort by Relevance\r\nfr_FR: Trier par pertinence",
            ],
            'checkbox_trigger' => $GLOBALS['IWPTPL_CHECKBOX_TRIGGER_DATA'],
        ))));
    }
}

function iwptp_sanitize_array($array)
{
    $sanitized = null;
    if (is_array($array)) {
        if (count($array) > 0) {
            foreach ($array as $key => $value) {
                if (is_array($value)) {
                    $sanitized[$key] = iwptp_sanitize_array($value);
                } else {
                    $sanitized[$key] = (!empty($value)) ? wp_kses(stripslashes($value), iwptp_allowed_html_tags()) : '';
                }
            }
        } else {
            $sanitized = [];
        }
    } else {
        $sanitized = wp_kses(stripslashes($array), iwptp_allowed_html_tags());
    }

    return $sanitized;
}

function iwptp_array_flatten($array, $sanitize = null)
{
    if (!is_array($array)) {
        return false;
    }
    $result = [];
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            $result = array_merge($result, iwptp_array_flatten($value, $sanitize));
        } else {
            switch ($sanitize) {
                case 'int':
                    $result = array_merge($result, array($key => intval($value)));
                    break;
                default:
                    $result = array_merge($result, array($key => $value));
                    break;
            }
        }
    }
    return $result;
}

/* create table editor page */
function iwptp_editor_page()
{
    if (!class_exists('WooCommerce')) return;

    if (!empty($_GET['post_id'])) {
        $post_id = intval($_GET['post_id']);
    } else {
        $post_id = wp_insert_post(array('post_type' => 'iwptp_product_table'));
        wp_redirect(admin_url('edit.php?post_type=iwptp_product_table&page=iwptp-edit&post_id=' . $post_id));
    }

    $post = get_post(intval($post_id));
    if (!($post instanceof \WP_Post) || $post->post_status == 'trash') {
        wp_redirect(admin_url('edit.php?post_type=iwptp_product_table'));
        die();
    }

    if (get_post_meta($post_id, 'iwptp_data', true)) {
        // previously saved table data
        $GLOBALS['iwptp_table_data'] = iwptp_get_table_data($post_id, 'edit');
    } else {
        // starter data
        $GLOBALS['iwptp_table_data'] = iwptp_get_default_table_data();
    }

    $presets = iwptp_get_style_presets();
    $trash_nonce = wp_create_nonce('trash-post_' . intval($post_id));
    $user_roles = wp_roles();
    $product_types = wc_get_product_types();
    $current_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

    $product_ids = [];
    $products_list = [];
    if (!empty($GLOBALS['iwptp_table_data']['query']['include_products'])) {
        $product_ids[] = array_map('intval', $GLOBALS['iwptp_table_data']['query']['include_products']);
    }
    if (!empty($GLOBALS['iwptp_table_data']['query']['exclude_products'])) {
        $product_ids[] = array_map('intval', $GLOBALS['iwptp_table_data']['query']['exclude_products']);
    }
    if (!empty($product_ids)) {
        $product_type_keys = array_keys($product_types);
        $product_type_keys[] = 'variation';
        $products = wc_get_products([
            'type' => $product_type_keys,
            'include' => iwptp_array_flatten($product_ids),
            'orderby' => 'include',
            'limit' => -1,
        ]);

        if (!empty($products)) {
            foreach ($products as $product) {
                if ($product instanceof \WC_Product) {
                    $products_list[$product->get_id()] = $product->get_name();
                }
            }
        }
    }

    $taxonomy_ids = [];
    $taxonomies_list = [];
    if (!empty($GLOBALS['iwptp_table_data']['query']['include_taxonomies'])) {
        foreach ($GLOBALS['iwptp_table_data']['query']['include_taxonomies'] as $item) {
            if (strpos($item, '{iwptp-tax-id}') !== false) {
                $taxonomy = explode('{iwptp-tax-id}', $item);
                if (!empty($taxonomy) && is_array($taxonomy) && !empty($taxonomy[0]) && !empty($taxonomy[1])) {
                    $taxonomy_ids[] = sanitize_text_field($taxonomy[1]);
                }
            }
        }
    }
    if (!empty($GLOBALS['iwptp_table_data']['query']['exclude_taxonomies'])) {
        foreach ($GLOBALS['iwptp_table_data']['query']['exclude_taxonomies'] as $item) {
            if (strpos($item, '{iwptp-tax-id}') !== false) {
                $taxonomy = explode('{iwptp-tax-id}', $item);
                if (!empty($taxonomy) && is_array($taxonomy) && !empty($taxonomy[0]) && !empty($taxonomy[1])) {
                    $taxonomy_ids[] = sanitize_text_field($taxonomy[1]);
                }
            }
        }
    }
    if (!empty($taxonomy_ids)) {
        $terms = get_terms([
            'include' => iwptp_array_flatten($taxonomy_ids),
            'hide_empty' => false,
        ]);
        if (!empty($terms)) {
            foreach ($terms as $term) {
                if ($term instanceof \WP_Term) {
                    $key = $term->taxonomy . '{iwptp-tax-id}' . ((strpos($term->taxonomy, 'pa_') === 0) ? $term->slug : $term->term_id);
                    $taxonomies_list[$key] = $term->taxonomy . ': ' . $term->name;
                }
            }
        }
    }

    $user_ids = (!empty($GLOBALS['iwptp_table_data']['query']['include_users'])) ? array_map('intval', $GLOBALS['iwptp_table_data']['query']['include_users']) : [];
    $users_list = [];
    if (!empty($user_ids)) {
        $users = get_users([
            'include' => iwptp_array_flatten($user_ids),
        ]);
        if (!empty($users)) {
            foreach ($users as $user) {
                if ($user instanceof \WP_User) {
                    $users_list[$user->ID] = $user->display_name;
                }
            }
        }
    }

    $select2_data = [
        'products' => $products_list,
        'taxonomies' => $taxonomies_list,
        'users' => $users_list
    ]

?>
    <script>
        var iwptp = {
            model: {},
            view: {},
            controller: {},
            data: <?php echo wp_json_encode($GLOBALS['iwptp_table_data']); ?>,
        };

        var iwptpSelect2Data = <?php echo wp_json_encode($select2_data); ?>
    </script>
<?php
    // editor template
    require(IWPTPL_PLUGIN_PATH . 'editor/editor.php');
}


/* esc data fields */
function iwptp_esc_attr(&$info)
{
    foreach ($info as $key => &$val) {
        if (is_string($val) && !in_array($key, array("heading", "css"))) {
            $val = esc_attr($val);
        } else if (is_array($val)) {
            iwptp_esc_attr($val);
        }
    }
}


/* create plugin settings page */
function iwptp_settings_page()
{
    if (!class_exists('WooCommerce')) {
        return;
    }

    if (!empty($_GET['iwptp_reset_global_settings'])) {
        do_action('iwptp_reset_global_settings');
        delete_option('iwptp_settings');
        wp_safe_redirect(admin_url('edit.php?post_type=iwptp_product_table&page=iwptp-settings'));
    }

    $settings = iwptp_get_settings_data('edit');
?>
    <script>
        var iwptp = {
            model: {},
            view: {},
            controller: {},
            data: <?php echo wp_json_encode($settings); ?>,
        };
    </script>
<?php
    // settings page template
    require(IWPTPL_PLUGIN_PATH . 'editor/settings.php');
}


function iwptp_woocommerce_photoswipe()
{
    wc_get_template('single-product/photoswipe.php');
}

// removes other woocommerce arguments from the pagination links
function iwptp_paginate_links($link)
{
    $remove = array('add-to-cart', 'variation_id', 'product_id', 'quantity');
    foreach (iwptp_sanitize_array($_GET) as $key => $val) {
        if (substr($key, 0, 10) === 'attribute_') {
            $remove[] = sanitize_text_field($key);
        }
    }
    return remove_query_arg($remove, $link);
}

// helper
function iwptp_woo_hack_invoke_private_method($class_name, $methodName)
{
    if (version_compare(phpversion(), '5.3', '<')) {
        throw new Exception('PHP version does not support ReflectionClass::setAccessible()', __LINE__);
    }

    $args = func_get_args();
    unset($args[0], $args[1]);
    $reflection = new ReflectionClass($class_name);
    $method = $reflection->getMethod($methodName);
    $method->setAccessible(true);

    $args = array_merge(array(new $class_name), $args);

    return call_user_func_array(array($method, 'invoke'), $args);
}


function iwptp_get_product_details_in_cart_including_variations($product_id)
{
    $result = array(
        "quantity" => 0,
        "cart_item_keys_arr" => [],
    );
    $cart_contents = WC()->cart->get_cart();
    foreach ($cart_contents as $cart_item_key => $item_details) {
        if ($product_id === $item_details["product_id"]) {
            $result["cart-item-keys-arr"][] = $cart_item_key;
            $result["quantity"] += $item_details["quantity"];
        }
    }
    return $result;
}

function iwptp_get_table_id_from_name($post_title)
{
    $id = '';

    $loop = new WP_Query(array(
        'posts_per_page' => 1,
        'post_type' => 'iwptp_product_table',
        'post_status' => 'publish',
        'title' => $post_title,
        'fields' => 'ids',
    ));

    if ($loop->have_posts()) {
        $id = $loop->posts[0];
    }

    wp_reset_postdata();

    return $id;
}

function iwptp_remove_product_table_shortcode($content)
{
    if (FALSE === strpos($content, '[it_product_table')) {
        return $content;
    }

    return preg_replace('/\[it_product_table(.*?)\]/s', '', $content);
}

/**
 * Parse tpl with shortcodes
 */
function iwptp_parse_2($template, $product = false)
{
    $products = [];

    if (gettype($template) !== 'array') {
        return $template;
    }

    if (!$product && isset($GLOBALS['product'])) {
        $product = $GLOBALS['product'];
    }

    if (isset($GLOBALS['iwptp_products'])) {
        $products = $GLOBALS['iwptp_products'];
    }

    $markup = '';
    // parse rows
    foreach ($template as $row) {
        // row condition
        if (!empty($row['condition']) && !iwptp_condition($row['condition'])) {
            continue;
        }

        // row condition
        if (empty($row['html_class'])) {
            $row['html_class']  = '';
        }

        $row_markup = '';
        if (!empty($row['elements']) && gettype($template) == 'array') {
            foreach ($row['elements'] as $element) {
                if (!$element = apply_filters('iwptp_element', $element)) {
                    continue;
                }

                $template_file_name = $element['type'] . '.php';
                $template_path = '';

                iwptp_parse_style_2($element);

                $template_path = IWPTPL_PLUGIN_PATH . 'templates/' . $template_file_name;
                $template_path = apply_filters('iwptp_template', $template_path, $template_file_name);
                $element_markup = iwptp_parse_ctx_2($element, $template_path, $element['type'], $product, $products);

                $row_markup .= apply_filters('iwptp_element_markup', $element_markup, $element);
            }
        }

        // parse elements
        if ($row_markup) {
            $markup .= '<div class="iwptp-item-row iwptp-' . $row['id'] . ' ' . $row['html_class'] . '">' . $row_markup . '</div>';
            iwptp_parse_style_2($row);
        }
    }

    return $markup;
}

function iwptp_parse_ctx_2($element, $elm_tpl, $elm_type, $product = false)
{
    if (!$elm_tpl || !file_exists($elm_tpl)) {
        return;
    }

    extract($element);
    ob_start();

    if (empty($html_class)) {
        $html_class = '';
    }

    $html_class .= ' iwptp-' . $element['id'];

    include $elm_tpl;
    return ob_get_clean();
}

/**
 * Error checking for shortcode - in case user has made some mistake
 */
function iwptp_sc_error_checks($table_data, $atts)
{
    $message = '';

    if (empty($table_data)) {
        $message = __('No product table settings were found for this id. Please try clicking the "Save settings" button at the bottom of the table editor page to save your table settings.', 'ithemeland-woocommerce-product-table-pro-lite');
    } else if ((empty($table_data['columns']['laptop']) || !is_array($table_data['columns']['laptop']) || !count($table_data['columns']['laptop'])) && (empty($atts['form_mode']))) {
        $message = __('It looks like you haven\'t set any laptop columns for your product table. So, without any columns, your table has no content to display.', 'ithemeland-woocommerce-product-table-pro-lite');
    } else if (iwptp_device_columns_empty($table_data['columns']['laptop'])  && empty($atts['form_mode'])) {
        $message = __('While you have created at least one column in the Laptop Columns section for this table, it seems you have not created any elements in the columns. Please create at least one element in at least one Laptop Column for this table, then save your table settings and reload this page to see your table.', 'ithemeland-woocommerce-product-table-pro-lite');
    }

    if ($message && (!$table_data || current_user_can('edit_iwptp_product_table', intval($table_data['id'])))) {
        return '<div class="iwptp-notice"><span class="iwptp-notice-heading">' . IWPTPL_LABEL . '</span>' . $message . '</div>';
    } else {
        return false;
    }
}

/**
 * Error checking for shortcode - in case user has made some mistake
 */
function iwptp_device_columns_empty($device_columns)
{
    $no_element = true;
    foreach ($device_columns as $column) {
        // iterate rows
        //-- heading
        foreach ($column['heading']['content'] as $row) {
            if (!empty($row['elements']) && count($row['elements'])) {
                $no_element = false;
            }
        }
        //-- cell
        foreach ($column['cell']['template'] as $row) {
            if (!empty($row['elements']) && count($row['elements'])) {
                $no_element = false;
            }
        }
    }

    return $no_element;
}

function iwptp_get_cheapest_variation($product, $available_variations)
{

    $lowest_price = false;
    $variation_id = false;

    foreach ($available_variations as $variation_details) {
        if (false === $lowest_price || $variation_details['display_price'] < $lowest_price) {
            $lowest_price = $variation_details['display_price'];
            $variation_id = $variation_details['variation_id'];
        }
    }

    return wc_get_product($variation_id);
}

function iwptp_get_most_expensive_variation($product, $available_variations)
{

    $highest_price = false;
    $variation_id = false;

    foreach ($available_variations as $variation_details) {
        if (false === $highest_price || $variation_details['display_price'] > $highest_price) {
            $highest_price = $variation_details['display_price'];
            $variation_id = $variation_details['variation_id'];
        }
    }

    return wc_get_product($variation_id);
}

function iwptp_woocommerce_available_variation_filter($variation_details, $product, $variation)
{
    global $iwptp_table_data;

    foreach ($iwptp_table_data['columns'] as $key => $column) {
        $variation_details['column_' . $key] = iwptp_parse_2($column['template'], $product, $variation, $variation_details);
    }

    return $variation_details;
}

function iwptp_update_user_filters($new_filter, $single = true)
{
    $found_filter = false;

    foreach ($GLOBALS['iwptp_user_filters'] as &$filter_info) {
        if ($filter_info['filter'] !== $new_filter['filter']) {
            continue;
        }

        if (in_array($new_filter['filter'], array('orderby', 'price_range', 'search', 'on_sale', 'rating', 'availability'))) {
            $found_filter = true;
            break;
        }

        // taxonomy
        if (
            in_array($filter_info['filter'], array('taxonomy', 'attribute', 'category')) &&
            $filter_info['taxonomy'] == $new_filter['taxonomy']
        ) {
            $found_filter = true;
            break;
        }

        // custom field
        if (
            $filter_info['filter'] == 'custom_field' &&
            strtolower($filter_info['meta_key']) == strtolower($new_filter['meta_key'])
        ) {
            $found_filter = true;
            break;
        }
    }

    if ($found_filter) {
        foreach ($new_filter as $key => $val) {
            // add value
            if ($key == 'values') {
                if (!$single) {
                    if (!is_array($filter_info['values'])) {
                        $filter_info['values'] = [];
                    }

                    if ($filter_info['filter'] == 'custom_field') { // avoid duplicates
                        $new_filter['values'] = array_map('strtolower', $new_filter['values']);
                        $filter_info['values'] = array_map('strtolower', $filter_info['values']);
                    }

                    $diff = array_diff($new_filter['values'], $filter_info['values']);
                    $filter_info['values'] = array_merge($filter_info['values'], $diff);
                } else {
                    $filter_info['values'] = $val;
                }

                // add clear label
            } else if ($key == 'clear_labels_2') {
                if (!$single) {
                    if (!is_array($filter_info['clear_labels_2'])) {
                        $filter_info['clear_labels_2'] = [];
                    }
                    if ($new_filter['clear_labels_2']) {
                        foreach ($new_filter['clear_labels_2'] as $key => $val) {
                            if (empty($filter_info['clear_labels_2'][$key]) || $filter_info['clear_labels_2'][$key] !== $val) {
                                $filter_info['clear_labels_2'][$key] = $val;
                            }
                        }
                    }
                } else {
                    $filter_info['clear_labels_2'] = $val;
                }

                // other key
            } else {
                $filter_info[$key] = $val;
            }
        }
    } else {
        $GLOBALS['iwptp_user_filters'][] = $new_filter;
    }
}

// Relabel items
function iwptp_relabel_items(&$items, $relabels = [])
{
    foreach ($items as &$item) {
        foreach ($relabels as $relabel) {
            if (strtolower($item['item']) === strtolower($relabel['item'])) {
                $item['label'] = iwptp_parse_2($relabel['label']);
            }
        }
    }

    return $items;
}

// iwptp price
function iwptp_price($price, $trim_zeros = false, $safari_fix = true)
{

    $num = number_format(
        (float) $price,
        wc_get_price_decimals(),
        wc_get_price_decimal_separator(),
        wc_get_price_thousand_separator()
    );

    if (
        $trim_zeros &&
        strstr($num, '.')
    ) {
        $num = rtrim(rtrim($num, '0'), '.');
    }

    $price = '<span class="iwptp-amount">' . $num . '</span>';
    $currency_symbol = esc_attr(get_woocommerce_currency_symbol());
    // safari $ fix
    if (
        iwptp_get_the_browser() == "Safari" &&
        $safari_fix
    ) {
        $html_class = '';
        if ($currency_symbol == '&#036;') {
            $currency_symbol = wp_kses(iwptp_icon('dollar-sign'), iwptp_allowed_html_tags());
            $html_class = 'iwptp-safari-currency';
        }
        $currency = '<span class="iwptp-currency ' . $html_class . '">' . $currency_symbol . '</span>';
    } else {
        $currency = '<span class="iwptp-currency">' . $currency_symbol . '</span>';
    }
    return str_replace(array('%1$s', '%2$s'), array($currency, $price), get_woocommerce_price_format());
}


// product form modal 
// triggered for: variable, addons, measurement, name your price

function iwptp_include_descendant_slugs($slugs = [], $taxonomy = null)
{

    if (!$slugs) {
        return [];
    }

    if (!$taxonomy) {
        $taxonomy = 'product_cat';
    }

    // convert slugs to term ids
    $term_ids = get_terms(array(
        'taxonomy' => $taxonomy,
        'fields' => 'ids',
        'slug' => $slugs
    ));

    // -- include children
    foreach ($term_ids as $term_id) {

        $child_slugs = get_terms(array(
            'taxonomy' => $taxonomy,
            'fields' => 'slugs',
            'child_of' => $term_id
        ));

        if ($child_slugs) {
            $slugs = array_merge($slugs, $child_slugs);
        }
    }

    return $slugs;
}

// icon
function iwptp_icon($icon_name, $html_class = '', $style = null, $tooltip = '', $title = '', $attrs = [])
{
    $output = '';

    $icon_file = esc_url(IWPTPL_PLUGIN_PATH . 'assets/feather/' . $icon_name . '.svg');
    if (file_exists($icon_file)) {
        if ($style) {
            $style = ' style="' . $style . '"';
        }

        $tooltip_html_class = '';
        if ($tooltip) {
            $tooltip_html_class = 'iwptp-tooltip';
        }

        if ($title) {
            $title = 'title="' . htmlentities($title) . '"';
        }

        $attr_string = '';
        if ($attrs) {
            $attr_string = ' ';
            foreach ($attrs as $key => $val) {
                $attr_string .= $key . '="' . $val . '" ';
            }
        }

        $output = '<span class="iwptp-icon iwptp-icon-' . esc_attr($icon_name) . ' ' . esc_attr($html_class) . ' ' . esc_attr($tooltip_html_class) . '" ' . esc_attr($style) . ' ' . esc_attr($title) . ' ' . esc_attr($attr_string) . '>';

        if ($tooltip) {
            $output .= '<span class="iwptp-tooltip-content">' . esc_html($tooltip) . '</span>';
        }

        ob_start();
        include($icon_file);
        $output .= ob_get_clean();
        $output .= '</span>';
    }

    return $output;
}

function iwptp_get_icon($icon_name, $html_class = '', $style = null, $tooltip = '', $title = '', $attrs = [])
{
    return iwptp_icon($icon_name, $html_class, $style, $tooltip, $title, $attrs);
}

function iwptp_get_column_by_index($column_index = 0, $device = 'laptop', &$data = false)
{

    $device_columns = iwptp_get_device_columns($device, $data);

    if (!$device_columns) {
        return false;
    } else {
        return $device_columns[$column_index];
    }
}

function iwptp_sortby_get_matching_option_index($match_user_filter, $available_options)
{
    if (!$available_options) {
        return false;
    }

    foreach ($available_options as $option_index => $option) {
        if (iwptp_check_sort_match($option, $match_user_filter)) {
            return $option_index;
        }
    }
    return false;
}

function iwptp_check_sort_match($option, $current_sorting)
{

    // match begins from 'orderby'
    if ($option['orderby'] !== $current_sorting['orderby']) {
        return false;
    }

    // no other params needs to match for these (there is no optional 'order' for these)
    if (in_array($option['orderby'], array('price', 'price-desc', 'rating', 'popularity', 'rand'))) {
        return true;
    }

    // order must also match for remaining - title, custom field, sku, ID, etc
    if (strtolower($option['order']) != strtolower($current_sorting['order'])) {
        return false;
    }

    // enough match for these
    if (in_array($option['orderby'], array('title', 'menu_order', 'id', 'sku', 'sku_num', 'date'))) {
        return true;
    }

    // custom field
    if (
        !empty($option['meta_key']) &&
        !empty($current_sorting['meta_key']) &&
        $option['meta_key'] == $current_sorting['meta_key']
    ) {
        return true;
    }

    // category
    $current_sorting__focus_category = empty($current_sorting['orderby_focus_category']) ? '' : $current_sorting['orderby_focus_category'];
    $current_sorting__ignore_category = empty($current_sorting['orderby_ignore_category']) ? '' : $current_sorting['orderby_ignore_category'];

    $option__focus_category = empty($option['orderby_focus_category']) ? '' : $option['orderby_focus_category'];
    $option__ignore_category = empty($option['orderby_ignore_category']) ? '' : $option['orderby_ignore_category'];

    if (
        $option['orderby'] == 'category' &&
        $current_sorting__focus_category == $option__focus_category &&
        $current_sorting__ignore_category == $option__ignore_category
    ) {
        return true;
    }

    // attribute
    $current_sorting__attribute = empty($current_sorting['orderby_attribute']) ? '' : $current_sorting['orderby_attribute'];
    $current_sorting__focus_attribute_terms = empty($current_sorting['orderby_focus_attribute_terms']) ? '' : $current_sorting['orderby_focus_attribute_terms'];
    $current_sorting__ignore_attribute_terms = empty($current_sorting['orderby_ignore_attribute_terms']) ? '' : $current_sorting['orderby_ignore_attribute_terms'];

    $option__attribute = empty($option['orderby_attribute']) ? '' : $option['orderby_attribute'];
    $option__focus_attribute_terms = empty($option['orderby_focus_attribute_terms']) ? '' : $option['orderby_focus_attribute_terms'];
    $option__ignore_attribute_terms = empty($option['orderby_ignore_attribute_terms']) ? '' : $option['orderby_ignore_attribute_terms'];

    if (
        in_array($option['orderby'], array('attribute', 'attribute_num')) &&
        $current_sorting__attribute == $option__attribute &&
        $current_sorting__focus_attribute_terms == $option__focus_attribute_terms &&
        $current_sorting__ignore_attribute_terms == $option__ignore_attribute_terms
    ) {
        return true;
    }


    // taxonomy
    $current_sorting__taxonomy = empty($current_sorting['orderby_taxonomy']) ? '' : $current_sorting['orderby_taxonomy'];
    $current_sorting__focus_taxonomy_terms = empty($current_sorting['orderby_focus_taxonomy_terms']) ? '' : $current_sorting['orderby_focus_taxonomy_terms'];
    $current_sorting__ignore_taxonomy_terms = empty($current_sorting['orderby_ignore_taxonomy_terms']) ? '' : $current_sorting['orderby_ignore_taxonomy_terms'];

    $option__taxonomy = empty($option['orderby_taxonomy']) ? '' : $option['orderby_taxonomy'];
    $option__focus_taxonomy_terms = empty($option['orderby_focus_taxonomy_terms']) ? '' : $option['orderby_focus_taxonomy_terms'];
    $option__ignore_taxonomy_terms = empty($option['orderby_ignore_taxonomy_terms']) ? '' : $option['orderby_ignore_taxonomy_terms'];

    if (
        $option['orderby'] == 'taxonomy' &&
        $current_sorting__focus_taxonomy_terms == $option__focus_taxonomy_terms &&
        $current_sorting__ignore_taxonomy_terms == $option__ignore_taxonomy_terms
    ) {
        return true;
    }
}

function iwptp_get_column_sort_filter_info()
{

    $field_name_prefix = $GLOBALS['iwptp_table_data']['id'] . '_';

    $column_index = (int) substr(sanitize_text_field($_GET[$field_name_prefix . 'orderby']), 7);
    $device = sanitize_text_field($_GET[$field_name_prefix . 'device']);
    $order = sanitize_text_field($_GET[$field_name_prefix . 'order']);

    $column = iwptp_get_column_by_index($column_index, $device);

    $filter_info = array(
        'filter' => 'orderby',
    );

    if ($column['sorting_enabled']) {
        $filter_info['orderby'] = $column['orderby'];
        $filter_info['order'] = $order;
        if ($column['orderby'] == 'meta_value' || $column['orderby'] == 'meta_value_num') {
            $filter_info['meta_key'] = $column['meta_key'];
        }

        // special case price-desc
        if ($column['orderby'] == 'price' && $order == 'DESC') {
            $filter_info['orderby'] = 'price-desc';
        }
    }

    return $filter_info;
}

function iwptp_get_nav_filter($name, $second = false)
{
    foreach ($GLOBALS['iwptp_user_filters'] as $filter_info) {
        if ($filter_info['filter'] == $name) {
            if (!$second) {
                return $filter_info;
            } else {
                switch ($name) {
                    case 'custom_field':
                        if (strtolower($filter_info['meta_key']) == strtolower($second)) {
                            return $filter_info;
                        }
                        break;

                    default: // attribute / taxonomy / product_cat
                        if ($filter_info['taxonomy'] == $second) {
                            return $filter_info;
                        }
                        break;
                }
            }
        }
    }

    return false;
}

function iwptp_clear_nav_filter($name, $second = false)
{
    foreach ($GLOBALS['iwptp_user_filters'] as $key => &$filter_info) {
        if ($filter_info['filter'] == $name) {
            if (!$second) {
                unset($GLOBALS['iwptp_user_filters'][$key]);
            } else {
                switch ($name) {
                    case 'custom_field':
                        if (strtolower($filter_info['meta_key']) == strtolower($second)) {
                            unset($GLOBALS['iwptp_user_filters'][$key]);
                        }
                        break;

                    case 'search':
                        if (
                            $second == 'native' &&
                            !empty($GLOBALS['iwptp_user_filters'][$key]['searches'])
                        ) {
                            foreach ($GLOBALS['iwptp_user_filters'][$key]['searches'] as $key2 => &$search) {
                                if (!empty($search['native'])) {
                                    unset($GLOBALS['iwptp_user_filters'][$key]['searches'][$key2]);
                                }
                            }
                        }
                        break;

                    default: // attribute / taxonomy / product_cat
                        if (!empty($filter_info['taxonomy']) && $filter_info['taxonomy'] == $second) {
                            unset($GLOBALS['iwptp_user_filters'][$key]);
                        }
                        break;
                }
            }
        }
    }
}

function iwptp_get_sorting_html_classes($col_orderby, $col_meta_key = false, $col_orderby_attribute = false, $col_orderby_taxonomy = false)
{

    extract(iwptp_get_current_sorting());

    $col_sorted = false;

    if ($current_orderby == $col_orderby) {
        if (in_array($current_orderby, array('meta_value', 'meta_value_num'))) {
            if ($current_meta_key == $col_meta_key) {
                $col_sorted = true;
            }
        } else if (in_array($current_orderby, array('attribute', 'attribute_num'))) {
            if (
                !empty($current_orderby_attribute) &&
                !empty($col_orderby_attribute) &&
                $current_orderby_attribute == $col_orderby_attribute
            ) {
                $col_sorted = true;
            }
        } else if (in_array($current_orderby, array('rating'))) {
            // fixed order
            $current_order = 'desc';
            $col_sorted = true;
        } else {
            $col_sorted = true;
        }
    } else if ($current_orderby == 'price-desc' && $col_orderby == 'price') {
        $current_order = 'desc';
        $col_sorted = true;
    } else if (in_array($current_orderby, array('meta_value', 'meta_value_num')) && $current_meta_key == '_sku' && in_array($col_orderby, array('sku', 'sku_num'))) {
        $col_sorted = true;
    }

    if ($col_sorted) {

        // if( $col_orderby == 'rating' || $col_orderby == 'date' ){
        if ($col_orderby == 'rating') {
            return array(
                'sorting_class' => 'iwptp-sorting-' . $current_order,
                'sorting_class_asc' => 'iwptp-hide',
                'sorting_class_desc' => $current_order == 'desc' ? 'iwptp-active' : 'iwptp-inactive',
            );
        }

        return array(
            'sorting_class' => 'iwptp-sorting-' . $current_order,
            'sorting_class_asc' => $current_order == 'asc' ? 'iwptp-active' : 'iwptp-inactive',
            'sorting_class_desc' => $current_order == 'desc' ? 'iwptp-active' : 'iwptp-inactive',
        );
    }

    // column not sorted
    return array(
        'sorting_class' => '',
        'sorting_class_asc' => ($col_orderby == 'rating') ? 'iwptp-hide' : 'iwptp-inactive',
        'sorting_class_desc' => 'iwptp-inactive',
    );
}

function iwptp_get_current_sorting()
{
    $sorting = iwptp_get_nav_filter('orderby');

    $current_sorting = [];
    foreach (iwptp_get_nav_filter('orderby') as $key => $val) {
        $current_sorting['current_' . $key] = ($key == 'order') ? strtolower($val) : $val;
    }

    return $current_sorting;
}

function iwptp_get_column_sorting_info($col_index, $device = 'laptop')
{
    $col_index = (int) $col_index;
    if (!in_array($device, array('laptop', 'tablet', 'phone'))) {
        $device = 'laptop';
    }

    // rows
    if (!empty($GLOBALS['iwptp_table_data']['columns'][$device][$col_index]['heading']['content'])) {
        foreach ($GLOBALS['iwptp_table_data']['columns'][$device][$col_index]['heading']['content'] as $row) {
            // elements
            foreach ($row['elements'] as $element) {
                if ($element['type'] == 'sorting') {
                    return $element;
                }
            }
        }
    }

    return NULL;
}

/* get table data from post or cache */
function iwptp_get_table_data($id = false, $context = 'view')
{
    if ($id) {
        // get true iwptp post id
        $true_id = $id;
        if (FALSE !== strpos($id, '-')) {
            $true_id = substr($id, 0, strpos($id, '-'));
        }

        if (get_post_type($id) !== 'iwptp_product_table') {
            return false;
        }

        $table_data = iwptp_sanitize_array(json_decode(get_post_meta($true_id, 'iwptp_data', true), true));
        $table_data['id'] = $id;

        return apply_filters('iwptp_data', $table_data, $context);
    } else {
        // return current cached table
        return !empty($GLOBALS['iwptp_table_data']) ? $GLOBALS['iwptp_table_data'] : false;
    }
}

// get price with filters applied
function iwptp_get_price_to_display($product = null)
{
    if (!$product) {
        global $product;
    }

    if (apply_filters('iwptp_product_is_on_sale', $product->is_on_sale(), $product)) {
        $price = apply_filters('iwptp_product_get_sale_price', $product->get_sale_price(), $product);
    } else {
        $price = apply_filters('iwptp_product_get_regular_price', $product->get_regular_price(), $product);
    }

    return $price;
}


/* columns related */
function iwptp_get_device_columns($device, &$data = false)
{
    if (!$data) {
        $data = &$GLOBALS['iwptp_table_data'];
    }

    return !empty($data['columns'][$device]) ? $data['columns'][$device] : false;
}

/* columns related */
function iwptp_get_device_columns_2($device, &$data = false)
{
    if (!$data) {
        $data = &$GLOBALS['iwptp_table_data'];
    }

    return !empty($data['columns'][$device]) ? $data['columns'][$device] : false;
}

/* elements related */
function iwptp_get_shortcode_element_manager($shortcode_tag)
{
    if ('_filter' == substr($shortcode_tag, -7) || in_array($shortcode_tag, array('sort_by', 'result_count'))) {
        return 'navigation';
    } else {
        return 'column';
    }
}

function iwptp_get_column_elements($data = false)
{
    if (!$data) {
        $data = iwptp_get_table_data();
    }
    return $data['elements']['column'];
}

function iwptp_get_navigation_elements($data = false)
{
    if (!$data) {
        $data = iwptp_get_table_data();
    }
    return $data['elements']['navigation'];
}

// returns references for specific nav filter type 
function iwptp_get_nav_elms_ref($type = false, &$data = false)
{

    if (!$data) {
        $data = iwptp_get_table_data();
    }

    $navigation = &$data['navigation']['laptop'];
    $rows = array(&$navigation['left_sidebar'][0]); // single BE row

    if (!empty($navigation['header']['rows']) && is_array($navigation['header']['rows'])) {
        foreach ($navigation['header']['rows'] as &$header_row) {
            foreach ($header_row['columns'] as &$column) {
                $rows[] = &$column['template'][0]; // append header BE rows
            }
        }
    }

    // iterate combined rows from sidebar and header
    $elements = [];
    foreach ($rows as &$row) {
        if (!empty($row['elements'])) {
            foreach ($row['elements'] as &$element) {
                if (
                    $type &&
                    $type !== $element['type']
                ) {
                    continue;
                }

                $elements[] = &$element;
            }
        }
    }

    return $elements;
}

// returns references for column elements of a type 
function iwptp_get_col_elms_ref($type, &$data)
{
    $elements = [];
    foreach ($data['columns'] as &$device) {
        if (empty($device)) {
            continue;
        }

        foreach ($device as &$column) {
            foreach ($column['cell']['template'] as &$template_row) {
                foreach ($template_row['elements'] as &$element) {
                    if (
                        $type &&
                        $type !== $element['type']
                    ) {
                        continue;
                    }

                    $elements[] = &$element;
                }
            }
        }
    }

    return $elements;
}

// global settings for plugin
function iwptp_update_settings_data()
{
    $data = iwptp_sanitize_array(json_decode(stripslashes(get_option('iwptp_settings', '')), true));
    $data['version'] = IWPTPL_VERSION;
    $data['timestamp'] = time();

    // update meta
    update_option('iwptp_settings', addslashes(wp_json_encode($data)));
    return $data;
}

$iwptp_device = null;
function iwptp_get_device()
{
    global $iwptp_device;
    if (!empty($iwptp_device)) {
        return $iwptp_device;
    }

    if (!class_exists('Mobile_Detect')) {
        require(IWPTPL_PLUGIN_PATH . 'vendor/Mobile_Detect.php');
    }

    $mobile_detect = new Mobile_Detect;

    $device = 'laptop';

    if (
        method_exists($mobile_detect, 'isTablet') &&
        $mobile_detect->isTablet()
    ) {
        $device = 'tablet';
    } else if ($mobile_detect->isMobile()) {
        $device = 'phone';
    }

    $iwptp_device = $device;
    return $iwptp_device;
}

function iwptp_get_shop_table_id()
{
    $shop_table_id = false;
    $shop_vars = iwptp_archive_override__get_table_vars('shop');
    if ($shop_vars['iwptp_table_id'] == 'custom') {
        $shop_table_id = iwptp_extract_id_from_shortcode($shop_vars['iwptp_custom_table']);
    } else {
        $shop_table_id = $shop_vars['iwptp_table_id'];
    }

    return $shop_table_id;
}

function iwptp_get_the_browser()
{
    global $iwptp_browser;
    if (!empty($iwptp_browser)) {
        return $iwptp_browser;
    }

    if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false)
        $iwptp_browser = 'Internet explorer';
    elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Trident') !== false)
        $iwptp_browser = 'Internet explorer';
    elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Firefox') !== false)
        $iwptp_browser = 'Mozilla Firefox';
    elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome') !== false)
        $iwptp_browser = 'Google Chrome';
    elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mini') !== false)
        $iwptp_browser = "Opera Mini";
    elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Opera') !== false)
        $iwptp_browser = "Opera";
    elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Safari') !== false)
        $iwptp_browser = "Safari";
    else
        $iwptp_browser = 'Other';

    return $iwptp_browser;
}

/* ajax add to cart */
add_action('wc_ajax_iwptp_cart', 'iwptp_cart');
add_action('wp_ajax_iwptp_cart', 'iwptp_cart');
add_action('wp_ajax_nopriv_iwptp_cart', 'iwptp_cart');
function iwptp_cart()
{
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'iwptp_ajax_nonce')) {
        die();
    }

    $in_cart = [];
    $in_cart_total = [];

    foreach (WC()->cart->get_cart() as $item => $values) {
        if (!$values['quantity'] || !apply_filters('iwptp_permit_item_in_cart_count', TRUE, $values)) {
            continue;
        }

        // variation
        if (!empty($values['variation_id'])) {
            if (empty($in_cart[$values['product_id']])) {
                $in_cart[$values['product_id']] = [];
                $in_cart_total[$values['product_id']] = [];
            }

            if (empty($in_cart[$values['product_id']][$values['variation_id']])) {
                $in_cart[$values['product_id']][$values['variation_id']] = (string) $values['quantity'];
            } else {
                $in_cart[$values['product_id']][$values['variation_id']] = (string)((float) $in_cart[$values['product_id']][$values['variation_id']] + $values['quantity']);
            }

            if (empty($in_cart_total[$values['product_id']][$values['variation_id']])) {
                $in_cart_total[$values['product_id']][$values['variation_id']] = $values['line_subtotal'];
            } else {
                $in_cart_total[$values['product_id']][$values['variation_id']] = (string)((float) $in_cart_total[$values['product_id']][$values['variation_id']] + $values['line_subtotal']);
            }

            // other than variation
        } else {
            $in_cart[$values['product_id']] = (string) $values['quantity'];
            $in_cart_total[$values['product_id']] = $values['line_subtotal'];
        }
    }

    $notice = '';
    if (!($success = !wc_notice_count('error'))) {
        ob_start();
        wc_print_notices();
        $notice = ob_get_clean();
    }

    wc_clear_notices();

    if (!empty($_POST['iwptp_payload']['mini_cart'])) {
        $mini_cart = iwptp_sanitize_array(json_decode(sanitize_text_field(wp_unslash($_POST['iwptp_payload']['mini_cart'])), true));

        if (!empty($mini_cart)) {
            extract($mini_cart);
            ob_start();
            include_once(apply_filters('iwptp_template', IWPTPL_PLUGIN_PATH . 'templates/mini_cart_items.php', 'mini_cart_items.php'));
            $cart_widget = ob_get_clean();
        }
    }

    ob_start();
    woocommerce_mini_cart();
    $mini_cart = ob_get_clean();

    $payload = [];
    if (!empty($_REQUEST['_iwptp_payload'])) {
        $payload = iwptp_sanitize_array($_REQUEST['_iwptp_payload']);
    } else if (!empty($_REQUEST['iwptp_payload'])) {
        $payload = iwptp_sanitize_array($_REQUEST['iwptp_payload']);
    }

    $data = array(
        'success' => $success,
        'cart_hash' => apply_filters('woocommerce_add_to_cart_hash', WC()->cart->get_cart_for_session() ? md5(wp_json_encode(WC()->cart->get_cart_for_session())) : '', WC()->cart->get_cart_for_session()),
        'cart_subtotal' => WC()->cart->get_cart_subtotal(),
        'cart_quantity' => WC()->cart->get_cart_contents_count(),
        'in_cart' => $in_cart,
        'in_cart_total' => $in_cart_total,
        'notice' => $notice,
        'payload' => $payload,
        'cart_widget' => (!empty($cart_widget)) ? $cart_widget : '',
        'fragments' => apply_filters(
            'woocommerce_add_to_cart_fragments',
            array(
                'div.widget_shopping_cart_content' => '<div class="widget_shopping_cart_content">' . $mini_cart . '</div>',
            )
        ),
        'cart_hash' => apply_filters('woocommerce_add_to_cart_hash', WC()->cart->get_cart_for_session() ? md5(wp_json_encode(WC()->cart->get_cart_for_session())) : '', WC()->cart->get_cart_for_session()),
        'cart_quantity' => WC()->cart->get_cart_contents_count(),
    );

    wp_send_json($data);
}

add_filter('iwptp_template', 'iwptp_get_template_from_theme', 10, 2);
function iwptp_get_template_from_theme($location, $template)
{
    if (file_exists(get_stylesheet_directory() . '/ithemeland-woocommerce-product-table-pro-lite/' . $template)) { // child theme
        return get_stylesheet_directory() . '/ithemeland-woocommerce-product-table-pro-lite/' . $template;
    } else if (file_exists(get_template_directory() . '/ithemeland-woocommerce-product-table-pro-lite/' . $template)) { // parent theme
        return get_template_directory() . '/ithemeland-woocommerce-product-table-pro-lite/' . $template;
    }

    return $location;
}

function iwptp_get_table_query_string()
{

    $data = iwptp_get_table_data();
    $table_id = $data['id'];

    $query_string_arr = [];

    foreach (iwptp_sanitize_array($_GET) as $key => $val) {

        if (
            !in_array(
                strtolower($key),
                apply_filters('iwptp_permitted_params', array('s', 'post_type'))
            ) &&
            (empty($val) ||
                0 !== strpos($key, (string) $table_id) || // table id should be key prefix
                in_array(
                    strtolower($key),
                    array( // excluded
                        $table_id . '_sc_attrs',
                        // $table_id . '_paged',
                        $table_id . '_url',
                        // $table_id . '_fresh_search',
                    )
                )
            )

        ) {
            continue;
        }

        if (is_array($val)) {
            $imploded_val = implode('', $val);
            if (!$imploded_val) {
                continue;
            }

            $val = array_unique(array_values($val));
        }

        if (
            0 !== strpos($key, 'search') &&
            !is_array($val)
        ) {
            // $val = htmlentities( stripslashes( $val ) );
            $val = htmlentities($val);
        }

        $query_string_arr[$key] = $val;
    }

    return add_query_arg($query_string_arr, '');
}

function iwptp_get_archive_query_string($archive, $term_id = '')
{

    if ($archive == 'category') {
        $term = get_term_by('id', $term_id, 'product_cat');
        extract(iwptp_archive_override__get_table_vars($archive, $term->slug));
    } else if ($archive == 'shop') {
        extract(iwptp_archive_override__get_table_vars($archive));
    }

    if (!$iwptp_table_id) {
        return '';
    }

    if (
        $iwptp_table_id == 'custom' &&
        $iwptp_custom_table
    ) {
        $iwptp_table_id = iwptp_extract_id_from_shortcode($iwptp_custom_table);
    }

    if (is_numeric($iwptp_table_id)) {
        $table_data = iwptp_get_table_data();
        $table_id = $table_data['id'];

        $query_string = iwptp_get_table_query_string();
        parse_str(ltrim($query_string, '?'), $params);

        if (isset($params[$table_id . '_product_cat'])) {
            unset($params[$table_id . '_product_cat']);
        }

        if (isset($params[$table_id . '_paged'])) {
            unset($params[$table_id . '_paged']);
        }

        $query_string = '?' . http_build_query($params);

        // from shop
        if (
            empty($params[$table_id . '_from_shop']) &&
            (!empty($table_data['query']['sc_attrs']['_archive']) &&
                $table_data['query']['sc_attrs']['_archive'] == 'shop'
            )
        ) {
            $query_string .= '&' . $table_data['id'] . '_from_shop=true';
        }

        return str_replace($table_id . '_', $iwptp_table_id . '_', $query_string);
    }

    return '';
}

function iwptp_extract_id_from_shortcode($shortcode)
{
    $id = '';

    // get id from shortcode
    $arr = [];
    preg_match('/id\s*=\s*[\'"](.*)[\'"]/U', $shortcode, $arr);

    if (!empty($arr[1])) {
        $id = $arr[1];
    }

    // get id from name in shortcode
    if (!$id) {
        preg_match('/name\s*=\s*[\'"](.*)[\'"]/U', $shortcode, $arr);

        if (!empty($arr[1])) {
            $name = $arr[1];
            $id = iwptp_get_table_id_from_name($name);
        }
    }

    return $id;
}


function iwptp_get_grouped_product_price($product = false)
{
    $prices = array(
        'min_price' => 0,
        'max_price' => 0
    );

    if (!$product) {
        global $product;
    }

    $tax_display_mode = get_option('woocommerce_tax_display_shop');
    $child_prices     = [];
    $children         = array_filter(array_map('wc_get_product', $product->get_children()), 'wc_products_array_filter_visible_grouped');

    foreach ($children as $child) {
        if ('' !== $child->get_price()) {
            // $child_price = 'incl' === $tax_display_mode ? wc_get_price_including_tax( $child ) : wc_get_price_excluding_tax( $child );

            if (apply_filters('iwptp_product_is_on_sale', $child->is_on_sale(), $child)) {
                $child_prices[] = apply_filters('iwptp_product_get_sale_price', $child->get_sale_price(), $child);
            } else {
                $child_prices[] = apply_filters('iwptp_product_get_regular_price', $child->get_price(), $child);
            }
        }
    }

    if (!empty($child_prices)) {
        $min_price = $prices['min_price'] = min($child_prices);
        $max_price = $prices['max_price'] = max($child_prices);
    } else {
        $min_price = '';
        $max_price = '';
    }

    if ('' !== $min_price) {
        $is_free = 0 === $min_price && 0 === $max_price;

        if ($is_free) {
            $prices = apply_filters('woocommerce_grouped_free_price_html', __('Free!', 'woocommerce'), $product);
        }
    } else {
        $prices = apply_filters('woocommerce_grouped_empty_price_html', '', $product);
    }

    return $prices;
}

// make shortcode column sortable in IWPTPL All Product Tables page
add_filter('manage_edit-iwptp_product_table_sortable_columns', 'iwptp_shortcode_column_sortable');
function iwptp_shortcode_column_sortable($columns)
{
    $columns['shortcode'] = 'id';
    return $columns;
}

// terms for variation
add_action('wp_ajax_iwptp_get_attribute_terms', 'iwptp_get_attribute_terms_ajax');
function iwptp_get_attribute_terms_ajax()
{
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'iwptp_ajax_nonce')) {
        die();
    }

    if (empty($_POST['taxonomy'])) {
        return false;
    }

    $terms = get_terms(array(
        'taxonomy' => sanitize_text_field($_POST['taxonomy']),
        'hide_empty' => false,
        'orderby' => 'menu_order',
    ));

    if (is_wp_error($terms)) {
        return false;
        die();
    }

    foreach ($terms as &$term) {
        $term_obj = get_term($term->term_id, sanitize_text_field($_POST['taxonomy']));
        $term->name = esc_html($term_obj->name);
    }

    wp_send_json($terms);
}

// get matching variation from attribute_terms
function iwptp_find_matching_product_variation($product, $attributes)
{
    foreach ($attributes as $key => $value) {
        if (strpos($key, 'attribute_') === 0) {
            continue;
        }

        unset($attributes[$key]);
        $attributes[sprintf('attribute_%s', $key)] = $value;
    }

    if (class_exists('WC_Data_Store')) {
        $data_store = WC_Data_Store::load('product');
        return $data_store->find_matching_product_variation($product, $attributes);
    } else {
        return $product->get_matching_variation($attributes);
    }
}

function iwptp_find_closests_matching_product_variation($product, $attributes)
{
    // iterate the variations
    $partial_match = false; // variation has some extra attributes
    $matched_variation = false;
    $variation_attributes = []; // attributes of the complete / partial variation (used for pre-set in form)
    $last_attributes_diff = 100000; // extra attributes in the last partial match variation
    $total_attributes = count(array_keys($attributes));

    // wmpl
    global $sitepress;
    if (
        !empty($sitepress) &&
        $sitepress->get_default_language() !==  $sitepress->get_current_language()
    ) {
        $_attributes = [];
        foreach ($attributes as $attr => $term_slug) {
            $term = get_term_by('slug', $term_slug, substr($attr, 10));
            $_attributes[$attr] = $term->slug;
        }

        $attributes = $_attributes;
    }

    $variations = iwptp_get_variations($product);
    foreach ($variations as $variation) {
        // skip if variation has too few attributes
        $total_variation_attributes = count(array_keys($variation['attributes']));
        if ($total_variation_attributes < $total_attributes) {
            continue;
        }

        // all the desired attributes must be in the variation
        $match = true;
        foreach ($attributes as $attribute => $term) {
            // skip variation if it does not have a desired attribute / match
            if (
                empty($variation['attributes'][$attribute]) ||
                $variation['attributes'][$attribute] !== $term
            ) {
                $match = false;
                break;
            }
        }

        if (!$match) {
            continue;
        } else {

            // complete match
            $attributes_diff = $total_variation_attributes - $total_attributes;
            if (!$attributes_diff) {
                return array(
                    'type' => 'complete_match',
                    'variation' => $variation,
                    'variation_id' => $variation['variation_id'],
                    'variation_attributes' => $variation['attributes']
                );

                // partial match
            } else if ($attributes_diff < $last_attributes_diff) {
                $partial_match = $variation['variation_id'];
                $variation_attributes = $variation['attributes'];
                $last_attributes_diff = $attributes_diff;
                $matched_variation = $variation;
            }
        }
    }

    if ($partial_match) {
        return array(
            'type' => 'partial_match',
            'variation' => $matched_variation,
            'variation_id' => $partial_match,
            'variation_attributes' => $variation_attributes,
        );
    } else {
        return false;
    }
}

// get variations array for the product
$iwptp_variations_cache = [];
function iwptp_get_variations($product = '')
{
    global $iwptp_variations_cache;

    if (gettype($product) !== 'object') {
        $product = wc_get_product($product);
    }

    $id = $product->get_id();

    if (!empty($iwptp_variations_cache[$id])) {
        return $iwptp_variations_cache[$id];
    } else {
        $iwptp_variations_cache[$id] = apply_filters('iwptp_get_variations', $product->get_available_variations());

        foreach ($iwptp_variations_cache[$id] as &$variation) {
            if ($variation['display_price']) {
                $variation['display_price'] = iwptp_price_decimal($variation['display_price']);
            }

            if ($variation['display_regular_price']) {
                $variation['display_regular_price'] = iwptp_price_decimal($variation['display_regular_price']);
            }

            if (iwptp_get_the_browser() == "Safari") {
                $currency_symbol = iwptp_icon('dollar-sign');
                $variation['price_html'] = str_replace('&#36;', $currency_symbol, $variation['price_html']);
            }
        }

        return $iwptp_variations_cache[$id];
    }
}

// get default variation for current product
function iwptp_get_default_variation($product)
{
    if (!$default_attributes = $product->get_default_attributes()) {
        return false;
    }

    $_default_attributes = [];
    foreach ($default_attributes as $key => $value) {
        $_default_attributes['attribute_' . $key] = $value;
    }

    return iwptp_find_closests_matching_product_variation($product, $_default_attributes);
}

// check if current variation is incomplete
function iwptp_is_incomplete_variation($product, $variation)
{

    foreach ($product->get_variation_attributes() as $attribute => $terms) {
        if (substr($attribute, 0, 3) !== 'pa_') { // custom attribute
            $attribute = sanitize_title($attribute);
        }

        if (empty($variation['attributes']['attribute_' . $attribute])) {
            return true;
        }
    }

    return false;
}

// check if all variations are out of stock
function iwptp_all_variations_out_of_stock($product_id)
{
    $product = wc_get_product($product_id);
    $children = $product->get_children();
    $out_of_stock = true;

    foreach ($children as $variation_id) {
        $variation = wc_get_product($variation_id);
        if ($variation->is_in_stock()) {
            $out_of_stock = false;
            break;
        }
    }

    return $out_of_stock;
}

/* clear product transients */
add_action('before_delete_post', 'iwptp_clear_product_transients');
add_action('save_post', 'iwptp_clear_product_transients');
function iwptp_clear_product_transients($post_id)
{
    if (get_post_type($post_id) == 'product') {
        delete_transient('iwptp_variations_' . $post_id);
    }
}

// duplicate post
add_filter('post_row_actions', 'iwptp_duplicate_post_link', 10000, 2);
function iwptp_duplicate_post_link($actions, $post)
{
    if (current_user_can('edit_posts') && $post->post_type == 'iwptp_product_table') {
        $actions['duplicate'] = '<a href="' . wp_nonce_url('admin.php?action=iwptp_duplicate_post_as_draft&post=' . $post->ID, 'iwptp_post_nonce', 'duplicate_nonce') . '" title="Duplicate this table" rel="permalink">Duplicate table</a>';
    }
    return $actions;
}

// gets the required filter from nav -- recursive
function iwptp_check_if_nav_has_filter($arr, $type, $second = false)
{
    if (null === $arr) {
        $arr = iwptp_get_table_data();
    }
    foreach ($arr as $key => &$val) {
        if (
            $key === 'type' &&
            $val === $type &&
            (!$second ||
                $type === 'taxonomy_filter' && $second === $arr['taxonomy'] ||
                $type === 'attribute_filter' && $second === $arr['attribute_name']
            )
        ) {
            return true;
        } else if (
            gettype($val) == 'array' &&
            TRUE === iwptp_check_if_nav_has_filter($val, $type, $second)
        ) {
            return true;
        }
    }
}

// ensure search settings are attached
add_filter('iwptp_settings', 'iwptp_settings__search', 2, 10);
function iwptp_settings__search($data, $ctx)
{

    if ($ctx == 'view') {
        return $data;
    }

    // attribute integrity
    $attributes = [];
    foreach (wc_get_attribute_taxonomies() as $attribute) {
        $match = false;
        if (isset($data['search']['attribute'])) {
            foreach ($data['search']['attribute']['items'] as $item) {
                if ($item['item'] === $attribute->attribute_name) {
                    $attributes[] = $item;
                    $match = true;
                    break;
                }
            }
        }

        if (!$match) {
            $attributes[] = array(
                'item' => $attribute->attribute_name,
                'label' => $attribute->attribute_label,
                'enabled' => true,
                'custom_rules_enabled' => false,
                'rules' => array(
                    'phrase_exact_enabled' => true,
                    'phrase_exact_score' => 100,

                    'phrase_like_enabled' => true,
                    'phrase_like_score' => 60,

                    'keyword_exact_enabled' => true,
                    'keyword_exact_score' => 40,

                    'keyword_like_enabled' => true,
                    'keyword_like_score' => 20,
                )
            );
        }
    }

    $data['search']['attribute']['items'] = $attributes;

    // custom field integrity
    if (!isset($data['search']['custom_field'])) {
        $data['search']['custom_field'] = [];
    }
    if (!isset($data['search']['custom_field']['items'])) {
        $data['search']['custom_field']['items'] = [];
    }

    $custom_fields = [];
    foreach (iwptp_get_product_custom_fields() as $meta_name) {
        $match = false;

        // get previous settings
        foreach ($data['search']['custom_field']['items'] as $item) {
            if ($item['item'] == $meta_name) {
                $custom_fields[] = $item;
                $match = true;
                break;
            }
        }

        // generate fresh settings
        if (!$match) {
            $custom_fields[] = array(
                'item' => $meta_name,
                'label' => $meta_name,
                'enabled' => true,
                'custom_rules_enabled' => false,
                'rules' => array(
                    'phrase_exact_enabled' => true,
                    'phrase_exact_score' => 80,

                    'phrase_like_enabled' => true,
                    'phrase_like_score' => 60,

                    'keyword_exact_enabled' => true,
                    'keyword_exact_score' => 40,

                    'keyword_like_enabled' => true,
                    'keyword_like_score' => 20,
                )
            );
        }
    }

    $data['search']['custom_field']['items'] = $custom_fields;

    return $data;
}

function iwptp_get_product_custom_fields()
{
    if (!empty($GLOBALS['IWPTPL_CF'])) {
        return $GLOBALS['IWPTPL_CF'];
    }

    global $wpdb;

    // product ids
    $query = "SELECT ID FROM $wpdb->posts WHERE post_type='product'";
    $product_ids = $wpdb->get_col($query); //phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared  

    // custom fields
    $query = "SELECT DISTINCT meta_key FROM $wpdb->postmeta meta WHERE post_id IN (" . implode(", ", $product_ids) . ")";
    $custom_fields = [];
    foreach ($wpdb->get_col($query) as $meta_name) { //phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared  
        if (
            '_' == substr($meta_name, 0, 1) ||
            'total_sales' == $meta_name
        ) {
            continue;
        } else {
            $custom_fields[] = $meta_name;
        }
    }

    $GLOBALS['IWPTPL_CF'] = $custom_fields;

    return $custom_fields;
}

function iwptp_general_placeholders__parse($str, $source = false)
{
    if (function_exists('iwptp_general_placeholders__parse__pro')) {
        return iwptp_general_placeholders__parse__pro($str, $source);
    } else {
        return $str;
    }
}

// iterate over arr and refresh all ids
function iwptp_new_ids(&$arr, $fresh = true)
{
    if ($fresh) {
        $GLOBALS['iwptp_new_ids_id'] = time() + wp_rand(0, 100000);
    }
    global $iwptp_new_ids_id;
    foreach ($arr as $key => &$val) {
        if ($key === 'id') {
            $val = ++$iwptp_new_ids_id;
        } else if (gettype($val) == 'array') {
            iwptp_new_ids($val, false);
        }
    }
}

// presets
if (file_exists(IWPTPL_PLUGIN_PATH . 'presets/presets.php')) {
    require_once(IWPTPL_PLUGIN_PATH . 'presets/presets.php');
}

// skip default relabels
function iwptp_is_default_relabel($rule)
{
    if (
        !$rule['label'][0]['elements'] ||
        (count($rule['label'][0]['elements']) == 1 &&
            !empty($rule['label'][0]['elements'][0]['text']) &&
            $rule['label'][0]['elements'][0]['text'] == "[term]"
        )
    ) {
        return true;
    }
}

// replace < and > with htmlentities
function iwptp_esc_tag($text)
{
    return str_replace('>', '&gt;', str_replace('<', '&lt;',  $text));
}

function iwptp_option($val, $label)
{
?>
    <option value="<?php echo esc_attr($val); ?>"><?php echo esc_html($label); ?></option>
<?php
}

function iwptp_radio($val, $label, $mkey)
{
?>
    <label>
        <input type="radio" value="<?php echo esc_attr($val); ?>" iwptp-model-key="<?php echo esc_attr($mkey); ?>">
        <?php echo esc_html($label); ?>
    </label>
<?php
}

function iwptp_checkbox($val, $label, $mkey, $disabled = null)
{
?>
    <label>
        <input type="checkbox" value="<?php echo esc_attr($val); ?>" iwptp-model-key="<?php echo esc_attr($mkey); ?>" <?php echo (!empty($disabled)) ? 'disabled' : ''; ?>>
        <?php echo esc_html($label); ?>
    </label>
    <?php
}

// add shortcode column in IWPTPL All Product Tables page
add_action('manage_iwptp_product_table_posts_custom_column', 'iwptp_shortcode_column', 10, 2);
function iwptp_shortcode_column($column, $post_id)
{
    switch ($column) {
        case 'shortcode':
    ?>
            <input style="width: 230px; border: 1px solid #e2e2e2; padding: 10px; background: #f7f7f7;" value="<?php echo esc_html('[it_product_table id="' . $post_id . '"]'); ?>" readonly />
            <textarea id="iwptp-product-table-list-shortcode-<?php echo intval($post_id); ?>" style="opacity: 0; position: absolute; z-index: -100;"><?php echo esc_html('[it_product_table id="' . $post_id . '"]'); ?></textarea>
            <button type="button" class="iwptp-product-table-list-shortcode-copy-button" title="<?php esc_attr_e('Copy to clipboard', 'ithemeland-woocommerce-product-table-pro-lite'); ?>" data-target="iwptp-product-table-list-shortcode-<?php echo intval($post_id); ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                    <path d="M22 6v16h-16v-16h16zm2-2h-20v20h20v-20zm-24 17v-21h21v2h-19v19h-2z" />
                </svg>
                <span class="iwptp-copied" style="display: none;"><?php esc_html_e('Copied to Clipboard:', 'ithemeland-woocommerce-product-table-pro-lite'); ?></span>
            </button>

    <?php
            break;
    }
}

// manage IWPTPL All Product Tables page columns
add_filter('manage_iwptp_product_table_posts_columns', 'iwptp_set_shortcode_column');
function iwptp_set_shortcode_column($columns)
{
    $new_columns = [];
    foreach ($columns as $name => $label) {
        $new_columns[$name] = $label;
        if ($name == 'title') {
            $new_columns['shortcode'] = __('Shortcode', 'ithemeland-woocommerce-product-table-pro-lite');
        }
    }
    return $new_columns;
}

// use default search
add_filter('iwptp_shortcode_attributes', 'iwptp_shortcode_attributes__use_default_search');
function iwptp_shortcode_attributes__use_default_search($atts)
{
    if (!empty($atts['use_default_search'])) {
        add_filter('iwptp_search_args', 'iwptp_search_args__use_default_search');
    } else {
        remove_filter('iwptp_search_args', 'iwptp_search_args__use_default_search');
    }

    return $atts;
}

function iwptp_search_args__use_default_search($args)
{
    $args['use_default_search'] = true;
    return $args;
}

// LiteSpeed Cache compatbility fix
// force js > iwptp_cart to run on every page load
add_action('wp_enqueue_scripts', 'iwptp_lightspeed_cache_compatibility_fix', 1000);
function iwptp_lightspeed_cache_compatibility_fix()
{
    if (class_exists('LiteSpeed\Core')) {
        wp_add_inline_script('iwptp', "iwptp_params.initially_empty_cart = false;", 'after');
    }
}

// Jupiter theme v ~ 6.8 compatibility
// stop regenerating images each time Select Variation is called  
add_action('iwptp_before_loop', 'iwptp_jupiter_remove_image_regen_handler');
function iwptp_jupiter_remove_image_regen_handler()
{
    if (has_filter('image_downsize', 'gambit_otf_regen_thumbs_media_downsize')) {
        remove_filter('image_downsize', 'gambit_otf_regen_thumbs_media_downsize', 10, 3);
        add_filter('iwptp_container_close', 'iwptp_jupiter_reattach_image_regen_handler');
    }
}

function iwptp_jupiter_reattach_image_regen_handler()
{
    add_filter('image_downsize', 'gambit_otf_regen_thumbs_media_downsize', 10, 3);
}

// genral placeholders
// -- print options
function iwptp_general_placeholders__print_placeholders($destination = false)
{
    ob_start();
    ?>
    <small style="cursor: default;">
        <strong>Available placeholders:</strong><br>
        <div class="">
            [product_id]: Product ID<br>
            <!-- [parent_id]: parent ID in case of variation, else product ID<br> -->
            <!-- [variation_id]: variation ID in case of variation, else empty<br> -->
            [product_url]: Product url (no trailing slash "/")<br>
            <!-- [parent_url]: Parent url in case of variation, else product url<br> -->
            [custom_field: <em>name</em>]: Replace <em>name</em> with custom field name<br>
            [attribute: <em>slug</em>]: Replace <em>slug</em> with attribute slug<br>
            [product_slug]: Product slug, eg: red-shoes-02<br>
            <!-- [parent_slug]: parent slug if variation, else product slug<br> -->
            [product_sku]: Product SKU<br>
            <!-- [parent_sku]: parent SKU if variation, else product SKU<br> -->
            [product_name]: Product name<br>
            <!-- [parent_name]: parent name in case of variation, else product name<br> -->
            [product_menu_order]: Product menu order<br>
            <!-- [parent_menu_order]: parent menu order in case of variation, else product menu order<br> -->
            [site_url]: Site URL (no trailing slash "/")<br>
            [page_url]: Current page URL (no trailing slash "/")<br>
        </div>
    </small>
    <?php

    $mkp = ob_get_clean();

    if ($destination == 'shortcode') {
        $mkp = str_replace(array('[', ']'), array('%', '%'), $mkp);
    }

    echo wp_kses($mkp, iwptp_allowed_html_tags());
}


// -- remove [it_product_table] shortcode
add_filter('iwptp_content', 'iwptp_remove_product_table_shortcode');
add_filter('iwptp_excerpt', 'iwptp_remove_product_table_shortcode');

// -- excerpt only - do media & other shortcodes
add_filter('iwptp_excerpt', 'iwptp_do_inner_shortcode', 100, 1);
function iwptp_do_inner_shortcode($excerpt)
{
    global $wp_embed;
    return do_shortcode($wp_embed->autoembed($wp_embed->run_shortcode($excerpt)));
}

function iwptp_truncate_string($text, $limit)
{
    if (str_word_count($text, 0) > $limit) {
        $words = str_word_count($text, 2);
        $pos   = array_keys($words);
        $text  = substr($text, 0, $pos[$limit]);
    }
    return $text;
}

function iwptp_how_to_use_link($link)
{
    ?>
    <a href="<?php echo esc_url($link); ?>" target="_blank" class="iwptp-how-to-use">
        <?php echo wp_kses(iwptp_icon('file-text'), iwptp_allowed_html_tags()); ?>
        <span>How to use</span>
    </a>
<?php
}

// add the import export markup
add_action('admin_footer', 'iwptp_insert_import_export_markup');
function iwptp_insert_import_export_markup()
{
    $arr = explode('/', $_SERVER['PHP_SELF']);
    $page = end($arr);

    if (
        $page !== 'edit.php' ||
        !empty($_GET['page']) ||
        (empty($_GET['post_type']) ||
            $_GET['post_type'] !== 'iwptp_product_table'
        )
    ) {
        return;
    }
    $iwptp_import_export_button_label_append = 'tables';
    $iwptp_import_export_button_context = 'tables';
    require_once('editor/settings-partials/import-export.php');
?>
    <style>
        .iwptp-import-export-wrapper {
            display: none;
        }
    </style>

    <script>
        (function($) {
            $('.iwptp-import-export-wrapper').appendTo('#wpbody-content').show();
        })(jQuery)
    </script>
<?php
}

// checks if template is empty
function iwptp_is_template_empty($tpl)
{
    if (empty($tpl)) {
        return true;
    }

    if (in_array(gettype($tpl), array('string', 'number'))) {
        return false;
    }

    $has_content = false;
    foreach ($tpl as $row) {
        if (
            !empty($row['elements']) &&
            count($row['elements'])
        ) {
            $has_content = true;
        }
    }

    return !$has_content;
}

// list image sizes
function iwptp_get_all_image_sizes()
{
    global $_wp_additional_image_sizes;

    $default_image_sizes = get_intermediate_image_sizes();

    $image_sizes = [];

    foreach ($default_image_sizes as $size) {
        $image_sizes[$size] = array(
            'width' => intval(get_option("{$size}_size_w")),
            'height' => intval(get_option("{$size}_size_h")),
            'crop' => get_option("{$size}_crop") ? get_option("{$size}_crop") : false,
        );
    }

    if (isset($_wp_additional_image_sizes) && count($_wp_additional_image_sizes)) {
        $image_sizes = array_merge($image_sizes, $_wp_additional_image_sizes);
    }

    return $image_sizes;
}

// WooCommerce Product Search compatibility
add_filter('iwptp_query_args', 'iwptp_compatibility__woocommerce_product_search', 10, 1);
function iwptp_compatibility__woocommerce_product_search($args)
{
    if (class_exists('WooCommerce_Product_Search_Service')) {
        remove_filter('pre_get_posts', 'WooCommerce_Product_Search_Service::wps_pre_get_posts', 10);
    }

    return $args;
}

function iwptp_price_decimal($price)
{
    $unformatted_price = $price;
    $negative          = $price < 0;
    $price             = apply_filters('raw_woocommerce_price', floatval($negative ? $price * -1 : $price));
    $price             = apply_filters(
        'formatted_woocommerce_price',
        number_format(
            $price,
            wc_get_price_decimals(),
            wc_get_price_decimal_separator(),
            wc_get_price_thousand_separator()
        ),
        $price,
        wc_get_price_decimals(),
        wc_get_price_decimal_separator(),
        wc_get_price_thousand_separator()
    );

    if (apply_filters('woocommerce_price_trim_zeros', false) && wc_get_price_decimals() > 0) {
        $price = wc_trim_zeros($price);
    }

    return $price;
}

// session
$iwptp_session_instance = false;
$iwptp_session_instance_dummy = false;
function iwptp_session()
{
    if ($GLOBALS['iwptp_session_instance']) {
        return $GLOBALS['iwptp_session_instance'];
    } else {
        // the dummy is useful to keep the code running when sessions is disabled
        if (empty($GLOBALS['iwptp_session_instance_dummy'])) {
            class IWPTPL_Session_Handler_Dummy
            {
                public function init()
                {
                }
                public function get($arg)
                {
                }
                public function set($arg)
                {
                }
            }

            $GLOBALS['iwptp_session_instance_dummy'] = new IWPTPL_Session_Handler_Dummy();
        }

        return $GLOBALS['iwptp_session_instance_dummy'];
    }
}

// to disable sessions (in case of conflict)
// add define( 'IWPTPL_DISABLE_SESSION', TRUE ) to wp-config.php
add_action('plugins_loaded', 'iwptp_load_session', 100);
function iwptp_load_session()
{
    if (defined('IWPTPL_DISABLE_SESSION')) {
        return;
    }

    global $wpdb;

    // create db if required
    if (!get_option('iwptp_sessions_db_version')) {
        $collate = $wpdb->has_cap('collation') ? $wpdb->get_charset_collate() : '';
        $sql = "CREATE TABLE {$wpdb->prefix}iwptp_sessions (
                            session_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                            session_key char(32) NOT NULL,
                            session_value longtext NOT NULL,
                            session_expiry BIGINT UNSIGNED NOT NULL,
                            PRIMARY KEY  (session_id),
                            UNIQUE KEY session_key (session_key)
                            ) $collate";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        update_option('iwptp_sessions_db_version', '1.0', TRUE);

        // schedule cleanup
        wp_clear_scheduled_hook('iwptp_cleanup_sessions');
        wp_schedule_event(time() + (6 * HOUR_IN_SECONDS), 'twicedaily', 'iwptp_cleanup_sessions');
    }

    // init session
    global $iwptp_session_instance;
    if ($wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}iwptp_sessions'")) {
        if (class_exists('WooCommerce')) {
            require_once(IWPTPL_PLUGIN_PATH . 'class-iwptp-session-handler.php');
            $iwptp_session_instance = new IWPTPL_Session_Handler();
            iwptp_session()->init();
        }
    }
}

add_action('iwptp_cleanup_sessions', 'iwptp_cleanup_session_data');
function iwptp_cleanup_session_data()
{
    global $wpdb;
    if (
        class_exists('WooCommerce') &&
        $wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}iwptp_sessions'")
    ) {
        require_once(IWPTPL_PLUGIN_PATH . 'class-iwptp-session-handler.php');
        $session = new IWPTPL_Session_Handler();
        $session->cleanup_sessions();
    }
}

function iwptp_get_post_meta_min_max($custom_field)
{
    $non_numeric_is_zero = false;

    global $wpdb;
    $query = $wpdb->prepare("
                            SELECT $wpdb->postmeta.meta_value
                            FROM $wpdb->postmeta 
                            WHERE $wpdb->postmeta.meta_key = %s
                            ORDER BY ($wpdb->postmeta.meta_value + 0) ASC
                        ", array($custom_field));
    $vals = $wpdb->get_col($query); //phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared  

    foreach ($vals as $key => &$value) {
        $value = (float) $value;
    }

    $vals = array_values($vals);

    if (empty($vals)) {
        $min = 0;
        $max = 0;
    } else {
        $min = $vals[0];
        $max = array_slice($vals, -1)[0];
    }

    return array(
        'min' => $min + 0,
        'max' => $max + 0
    );
}

function iwptp_get_translation($mixed)
{
    if (FALSE === strpos($mixed, ":")) {
        return $mixed;
    }

    $chopped = array_map('trim', explode("|", $mixed));

    foreach ($chopped as $translation_info) {
        $translation_info_chopped = array_map('trim', explode(":", $translation_info));
        if ('default' == strtolower($translation_info_chopped[0])) {
            $default = $translation_info_chopped[1];
        }

        if (strtolower(get_locale()) == strtolower($translation_info_chopped[0])) {
            $translation = $translation_info_chopped[1];
            break;
        }
    }

    if (empty($translation)) {
        if ($default) {
            $translation = $default;
        } else {
            $translation = '';
        }
    }

    return $translation;
}

add_filter('iwptp_element', 'iwptp_content__max_width', 10, 1);
function iwptp_content__max_width($elm)
{
    if (in_array($elm['type'], array('content', 'excerpt'))) {
        if (
            empty($elm['style']) ||
            empty($elm['style']['[id]']) ||
            (empty($elm['style']['[id]']['width']) &&
                empty($elm['style']['[id]']['max-width'])
            )
        ) {
            if (empty($elm['html_class'])) {
                $elm['html_class'] = '';
            }
            $elm['html_class'] .= " iwptp-content--max-width ";
        }
    }

    return $elm;
}

// salient qty width fix
$iwptp_salient_fixed_qty_ids = [];
add_filter('iwptp_element_markup', 'iwptp_salient_qty_width_fix', 100, 2);
function iwptp_salient_qty_width_fix($markup, $element)
{
    if (
        !empty($element) &&
        !empty($element['type']) &&
        $element['type'] === 'quantity' &&
        !in_array($element['id'], $GLOBALS['iwptp_salient_fixed_qty_ids'])
    ) {
        $theme = wp_get_theme();
        if ('Salient' == $theme->name || 'Salient' == $theme->parent_theme) {
            if ( // force width
                !empty($element['style']) &&
                !empty($element['style']['[id].iwptp-display-type-input']) &&
                !empty($element['style']['[id].iwptp-display-type-input']['width'])
            ) {
                if (is_numeric($element['style']['[id].iwptp-display-type-input']['width'])) {
                    $width = $element['style']['[id].iwptp-display-type-input']['width'] . 'px';
                } else {
                    $width = $element['style']['[id].iwptp-display-type-input']['width'];
                }

                $markup .= ' <style> .woocommerce .iwptp-table .iwptp-' . $element['id'] . ' {width: ' . $width . ' !important; } </style>';
            } else { // default width
                $markup .= ' <style> .woocommerce .iwptp-table .iwptp-' . $element['id'] . ' {width: 50px !important; } </style>';
            }
            $GLOBALS['iwptp_salient_fixed_qty_ids'][] = $element['id'];
        }
    }

    return $markup;
}

// IWPTPL PRO buttons, covers and markers
function iwptp_elm_type_list($element_types)
{
    sort($element_types);
?>
    <div class="iwptp-block-editor-element-type-list">
        <div class="iwptp-block-editor-element-type-list__search">
            <input type="text" class="iwptp-block-editor-element-type-list__search__input" placeholder="Search for element">
            <?php echo wp_kses(iwptp_icon('search', 'iwptp-block-editor-element-type-list__search__icon'), iwptp_allowed_html_tags()); ?>
        </div>
        <?php
        foreach ($element_types as $element) {
            if (!isset($element['label']) || !isset($element['type'])) {
                echo '<div class="iwptp-clear"></div>';
                continue;
            }

            if ($element['label'] == 'Availability Filter' && get_option('woocommerce_hide_out_of_stock_items', 'no') == 'yes') {
                continue;
            }
            if ($element['label'] == '_divider') {
                echo '<hr class="iwptp-block-editor-element-type-_divider" />';
            } else {
                $slug = strtolower(str_replace(' ', '_', str_replace(' / ', '_', $element['label'])));
                if (false !== strpos($element['label'], "__")) {
                    $element['label'] = substr($element['label'], 0, strpos($element['label'], "__"));
                }

        ?>
                <span class="iwptp-block-editor-element-type <?php echo ($element['type'] == 'pro') ? "iwptp-disabled" : ""; ?>" data-elm="<?php echo esc_attr($slug); ?>">
                    <?php echo esc_html($element['label']); ?>
                </span>
        <?php
            }
        }
        ?>

        <div class="iwptp-left-sidebar-pro-link">
            <p><?php esc_html_e('All of elements are available on Premium Version', 'ithemeland-woocommerce-product-table-pro-lite'); ?></p>
            <a href="https://ithemelandco.com/woocommerce-product-table-pro?utm_source=free_plugins&utm_medium=plugin_links&utm_campaign=user-lite-buy" target="_blank"><?php esc_html_e('Get Premium', 'ithemeland-woocommerce-product-table-pro-lite'); ?></a>
        </div>
    </div>
<?php
}

// search
require_once(IWPTPL_PLUGIN_PATH . 'search.php');
require_once(IWPTPL_PLUGIN_PATH . 'taxonomy-walker.php');
require_once(IWPTPL_PLUGIN_PATH . 'functions.php');
require_once(IWPTPL_PLUGIN_PATH . 'condition.php');

// decides whether orderby: relevance should be force applied
function iwptp_maybe_apply_sortby_relevance()
{
    $data = iwptp_get_table_data();
    $table_id = $data['id'];
    $sc_attrs = &$data['query']['sc_attrs'];

    if (
        !empty($_GET['s']) &&
        !empty($sc_attrs['_archive'])
    ) {
        return true;
    }

    $local_seach = false;
    foreach (iwptp_sanitize_array($_GET) as $key => $val) {
        if (
            strpos($key, $table_id . '_search') !== false &&
            $val
        ) {
            $local_seach = true;
        }
    }

    if (
        !empty($_GET[$table_id . '_orderby']) &&
        $_GET[$table_id . '_orderby'] == 'relevance' &&
        $local_seach
    ) {
        return true;
    }

    return false;
}

// filter header
add_filter('iwptp_navigation', 'iwptp_navigation_filter');
add_filter('iwptp_footer', 'iwptp_navigation_filter');
function iwptp_navigation_filter($navigation_header)
{

    global $iwptp_products;

    $paged = max(1, $iwptp_products->get('paged'));
    $per_page = $iwptp_products->get('posts_per_page');
    $total = $iwptp_products->found_posts;
    $first = ($per_page * $paged) - $per_page + 1;
    $last = min($total, $iwptp_products->get('posts_per_page') * $paged);

    $result_count_html_class = '';

    if ($total == 1) {
        $result_count_html_class = 'iwptp-single-result';
    } else if ($total == 0) {
        $result_count_html_class = 'iwptp-no-results';
    } else if ($total <= (int) $per_page || -1 === (int) $per_page) {
        $result_count_html_class = 'iwptp-single-page';
    }

    $search = array(
        '[result-count-html-class]',
        '[displayed_results]',
        '[total_results]',
        '[first_result]',
        '[last_result]',
    );
    $replace = array(
        $result_count_html_class,
        $last - $first + 1,
        $total,
        $first,
        $last,
    );

    return str_replace($search, $replace, $navigation_header);
}

function iwptp_corner_options($args = [])
{
    extract(shortcode_atts(array(
        'prepend' => '',
        'append' => '',
    ), $args));
?>
    <div class="iwptp-editor-corner-options">
        <?php echo wp_kses($prepend, iwptp_allowed_html_tags()); ?>
        <i class="iwptp-editor-row-move-up iwptp-sortable-handle" iwptp-move-up title="Move row up">
            <?php echo wp_kses(iwptp_icon('chevron-up'), iwptp_allowed_html_tags()); ?>
        </i>
        <i class="iwptp-editor-row-move-down iwptp-sortable-handle" iwptp-move-down title="Move row down">
            <?php echo wp_kses(iwptp_icon('chevron-down'), iwptp_allowed_html_tags()); ?>
        </i>
        <i class="iwptp-editor-row-duplicate" iwptp-duplicate-row title="Copy row">
            <?php echo wp_kses(iwptp_icon('copy'), iwptp_allowed_html_tags()); ?>
        </i>
        <i class="iwptp-editor-row-remove" iwptp-remove-row title="Delete row">
            <?php echo wp_kses(iwptp_icon('x'), iwptp_allowed_html_tags()); ?>
        </i>
        <?php echo wp_kses($append, iwptp_allowed_html_tags()); ?>
    </div>
<?php
}

function iwptp_get_cart_item_quantity($product_id)
{
    global $woocommerce;
    $in_cart = 0;

    if (is_object($woocommerce->cart)) {
        $contents = $woocommerce->cart->cart_contents;
        if ($contents) {
            foreach ($contents as $key => $details) {
                if ($details['product_id'] == $product_id) {
                    $in_cart += $details['quantity'];
                }
            }
        }
    }

    return $in_cart;
}

add_action('wp_ajax_iwptp_get_terms', 'iwptp_get_terms_ajax');
function iwptp_get_terms_ajax()
{
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'iwptp_ajax_nonce')) {
        die();
    }

    $term_taxonomy_id = !empty($_POST['limit_terms']) ? sanitize_text_field($_POST['limit_terms']) : false;
    $terms = iwptp_get_terms(sanitize_text_field($_POST['taxonomy']), $term_taxonomy_id);

    $relabels = [];
    $timestamp = time();
    foreach ($terms as $term) {
        // code...
        $relabels[] = array(
            'term' => wp_specialchars_decode($term->name),
            'ttid' => $term->term_taxonomy_id,
            'label' => array(
                array(
                    'id' => $timestamp++,
                    'style' => [],
                    'elements' => array(
                        array(
                            'id' => $timestamp++,
                            'style' => [],
                            'type' => 'text',
                            'text' => '[term]',
                        ),
                    ),
                )
            ),
            'tooltip' => '',
            'link' => '',
            'target' => '_self',
            'id' => $timestamp++,
        );
    }

    wp_send_json($relabels);
}

// gets terms include children
function iwptp_get_terms($taxonomy, $term_taxonomy_ids = false, $hide_empty = false)
{
    // user has set terms
    if (!empty($term_taxonomy_ids)) {

        if (gettype($term_taxonomy_ids) == 'string') {
            $term_taxonomy_ids = array_map('trim', explode(',', $term_taxonomy_ids));
        }

        // get term ids
        $term_ids = get_terms(array(
            'taxonomy' => $taxonomy,
            'hide_empty' => $hide_empty,
            'term_taxonomy_id' => $term_taxonomy_ids,
            'fields' => 'ids',
            // 'orderby' => 'parent',
            'orderby' => 'menu_order',
        ));

        // include all child terms
        foreach ($term_ids as $term_id) {
            // get its children
            $child_terms = get_term_children($term_id, $taxonomy);
            // include if not already there
            if ($child_terms && !is_wp_error($child_terms)) {
                $term_ids = array_unique(array_merge($term_ids, $child_terms));
            }
        }

        global $sitepress;
        if (
            !empty($sitepress) &&
            $taxonomy == 'product_cat'
        ) {
            $filter_exists = remove_filter('terms_clauses', array($sitepress, 'terms_clauses'), 10);
        }

        // get correct order
        $terms = get_terms(array(
            'taxonomy' => $taxonomy,
            'hide_empty' => $hide_empty,
            'include' => $term_ids,
            // 'orderby' => 'parent',
            'orderby' => 'menu_order',
        ));

        if (
            !empty($sitepress) &&
            !empty($filter_exists) &&
            $taxonomy == 'product_cat'
        ) {
            add_filter('terms_clauses', array($sitepress, 'terms_clauses'), 10, 3);
        }

        // user didn't set terms, so get all
    } else {
        $terms = get_terms(array(
            'taxonomy' => $taxonomy,
            'hide_empty' => $hide_empty,
            'orderby' => 'menu_order',
        ));
    }

    return $terms;
}

/* debug */
function iwptp_console_log()
{
    $arguments = func_get_args();
    if (!count($arguments)) {
        return;
    }
?>
    <script>
        console.log(
            <?php
            foreach ($arguments as $arg) {
                echo wp_json_encode($arg);
                echo ', ';
            }
            ?>
        );
    </script>
    <?php
}

/* navigation */
// footer
function iwptp_parse_footer($data = false)
{
    if (!$data) {
        $data = iwptp_get_table_data();
    }

    if (empty($data['navigation']['laptop']['footer'])) {
        return;
    }

    ob_start();
    if (!empty($data['navigation']['laptop']['footer']['rows'])) {

    ?>
        <div class="<?php echo esc_attr(apply_filters('iwptp_nav_footer_class', 'iwptp-navigation iwptp-footer iwptp-always-show')); ?>" style="<?php echo esc_attr(apply_filters('iwptp_nav_footer_style', '')); ?>">
            <?php

            foreach ($data['navigation']['laptop']['footer']['rows'] as $row) {
                if (empty($row)) {
                    continue;
                }

                if (empty($row['ratio'])) {
                    $row['ratio'] = '100-0';
                }

                $empty_row = true;
                ob_start(); // will feed $row_markup
            ?>
                <div class="iwptp-filter-row iwptp-ratio-<?php echo esc_attr($row['ratio']); ?> %maybe_hide%">
                    <?php

                    foreach (array('left', 'center', 'right') as $position) {
                        if (false !== strpos($row['columns_enabled'], $position)) {
                            echo '<div class="iwptp-filter-column iwptp-' . esc_attr($position) . '">';
                            if ($column_content = iwptp_parse_2($row['columns'][$position]['template'])) {
                                $empty_row = false;
                            }
                            echo wp_kses($column_content, iwptp_allowed_html_tags());
                            echo '</div>';
                        }
                    }
                    ?>
                </div>
            <?php
                $row_markup = ob_get_clean();
                if ($empty_row) {
                    $row_markup = '';
                } else {
                    $row_markup = str_replace('%maybe_hide%', '', $row_markup);
                }

                echo wp_kses($row_markup, iwptp_allowed_html_tags());
            }
            ?>
        </div>

    <?php
    }
    return ob_get_clean();
}

// header
function iwptp_parse_navigation($data = false)
{
    if (!$data) {
        $data = iwptp_get_table_data();
    }

    if (empty($data['navigation'])) {
        return;
    }

    ob_start();

    $mkp = '';
    if (!empty($data['navigation']['laptop']['header']['rows'])) {
    ?>

        <div class="<?php echo esc_attr(apply_filters('iwptp_nav_header_class', 'iwptp-navigation iwptp-header {{maybe-always}}')); ?>" style="<?php echo esc_attr(apply_filters('iwptp_nav_header_style', '')); ?>">
            <?php
            foreach ($data['navigation']['laptop']['header']['rows'] as $row) {
                if (empty($row)) {
                    continue;
                }

                if (empty($row['ratio'])) {
                    $row['ratio'] = '100-0';
                }

                $empty_row = true;

                ob_start(); // will feed $row_markup
            ?>
                <div class="iwptp-filter-row iwptp-ratio-<?php echo esc_attr($row['ratio']); ?> %maybe_hide%">
                    <?php
                    foreach (array('left', 'center', 'right') as $position) {
                        if (false !== strpos($row['columns_enabled'], $position)) {
                            echo '<div class="iwptp-filter-column iwptp-' . esc_attr($position) . '">';
                            if ($column_content = iwptp_parse_2($row['columns'][$position]['template'])) {
                                $empty_row = false;
                            }
                            echo wp_kses($column_content, iwptp_allowed_html_tags());
                            echo '</div>';
                        }
                    }
                    ?>
                </div>
            <?php
                $row_markup = ob_get_clean();

                if ($empty_row) {
                    $row_markup = '';
                } else {
                    $row_markup = str_replace('%maybe_hide%', '', $row_markup);
                }

                echo wp_kses($row_markup, iwptp_allowed_html_tags());
            }
            ?>
        </div>

        <div class="iwptp-responsive-navigation">
            <?php
            if (empty($data['navigation']['phone'])) {
                $data['navigation']['phone'] = '';
            }
            $res_nav = iwptp_parse_2($data['navigation']['phone']);
            echo wp_kses($res_nav, iwptp_allowed_html_tags());
            ?>
        </div>
<?php
    }

    include(IWPTPL_PLUGIN_PATH . 'templates/modals.php');

    $mkp = ob_get_clean();

    $always_show = 'iwptp-always-show';
    if (isset($res_nav) && $res_nav) {
        $always_show = '';
    }

    $mkp = str_replace('{{maybe-always}}', $always_show, $mkp);

    return $mkp;
}
