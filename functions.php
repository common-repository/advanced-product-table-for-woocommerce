<?php

// -- JS
add_action('admin_print_scripts', 'iwptp_pro_flag_js');
function iwptp_pro_flag_js()
{
    ob_start();
?>
    var iwptp_pro = true;
    jQuery(function($){
    $('body').addClass('iwptp-pro');
    })
<?php
    wp_add_inline_script('jquery', ob_get_clean());
}

// -- addons 
// -- -- register addons via this filter
$iwptp_addons = [];
add_action('plugins_loaded', 'iwptp_register_addons');
function iwptp_register_addons()
{
    global $iwptp_addons;
    $iwptp_addons = apply_filters('iwptp_addons', []);
}

//-- generate
add_filter('iwptp_product', 'iwptp_generate_json_ld', 10);
function iwptp_generate_json_ld($product)
{
    if (wp_doing_ajax()) {
        return $product;
    }

    $table_data = iwptp_get_table_data();
    $sc_attrs = $table_data['query']['sc_attrs'];

    if (
        !empty($sc_attrs['_archive']) ||
        !empty($sc_attrs['json_ld'])
    ) {
        $device = 'laptop';
        if (!empty($_GET[$table_data['id'] . '_device'])) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
            $device = sanitize_text_field($_GET[$table_data['id'] . '_device']); //phpcs:ignore WordPress.Security.NonceVerification.Recommended
        }

        if ($device == 'laptop') { // don't want duplicates
            remove_shortcode('it_product_table');
            WC()->structured_data->generate_product_data($product);
            $res = add_shortcode('it_product_table', 'iwptp_shortcode_product_table');
        }
    }

    return $product;
}

//-- decision: print
add_action('iwptp_before_loop', 'iwptp_maybe_print_json_ld', 10);
function iwptp_maybe_print_json_ld()
{
    $data = iwptp_get_table_data();
    if (
        // json-ld requested
        !empty($data['query']['sc_attrs']) &&
        !empty($data['query']['sc_attrs']['json_ld']) &&
        // not a page where WC prints it
        !is_woocommerce()
    ) {
        add_action('wp_footer', 'iwptp_print_json_ld', 10);
    }
}

//-- print
function iwptp_print_json_ld()
{
    if ($data  = WC()->structured_data->get_structured_data(array('product'))) {
        echo '<script type="application/ld+json">' . wp_json_encode($data) . '</script>';
    }
}

// duplicate post
add_action('admin_action_iwptp_duplicate_post_as_draft', 'iwptp_duplicate_post_as_draft');
function iwptp_duplicate_post_as_draft()
{
    global $wpdb;
    if (!(isset($_GET['post']) || isset($_POST['post'])  || (isset($_REQUEST['action']) && 'iwptp_duplicate_post_as_draft' == $_REQUEST['action']))) {
        wp_die('No post to duplicate has been supplied!');
    }

    /*
	 * Nonce verification
	 */
    if (
        !isset($_GET['duplicate_nonce']) ||
        !wp_verify_nonce($_GET['duplicate_nonce'], 'iwptp_post_nonce')
    ) {
        wp_die('Busted!');
    }

    /*
	 * get the original post id
	 */
    $post_id = (isset($_GET['post']) ? absint($_GET['post']) : absint($_POST['post']));

    /*
	 * and all the original post data then
	 */
    $post = get_post($post_id);

    /*
	 * if you don't want current user to be the new post author,
	 * then change next couple of lines to this: $new_post_author = $post->post_author;
	 */
    $current_user = wp_get_current_user();
    $new_post_author = $current_user->ID;

    /*
	 * if post data exists, create the post duplicate
	 */
    if (isset($post) && $post != null) {

        /*
		 * new post data array
		 */
        $args = array(
            'comment_status' => $post->comment_status,
            'ping_status'    => $post->ping_status,
            'post_author'    => $new_post_author,
            'post_content'   => $post->post_content,
            'post_excerpt'   => $post->post_excerpt,
            'post_name'      => $post->post_name,
            'post_parent'    => $post->post_parent,
            'post_password'  => $post->post_password,
            'post_status'    => 'publish',
            'post_title'     => $post->post_title  . ' - Copy',
            'post_type'      => $post->post_type,
            'to_ping'        => $post->to_ping,
            'menu_order'     => $post->menu_order
        );

        /*
		 * insert the post by wp_insert_post() function
		 */
        $new_post_id = wp_insert_post($args);

        /*
		 * get all current post terms ad set them to the new post draft
		 */
        $taxonomies = get_object_taxonomies($post->post_type); // returns array of taxonomy names for post type, ex array("category", "post_tag");
        foreach ($taxonomies as $taxonomy) {
            $post_terms = wp_get_object_terms($post_id, $taxonomy, array('fields' => 'slugs'));
            wp_set_object_terms($new_post_id, $post_terms, $taxonomy, false);
        }

        /*
		 * duplicate all post meta just in two SQL queries
		 */
        $post_meta_infos = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$post_id"); //phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
        if (count($post_meta_infos) != 0) {
            $sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
            foreach ($post_meta_infos as $meta_info) {
                $meta_key = $meta_info->meta_key;
                if ($meta_key == '_wp_old_slug') continue;
                $meta_value = addslashes($meta_info->meta_value);
                $sql_query_sel[] = "SELECT $new_post_id, '$meta_key', '$meta_value'";
            }
            $sql_query .= implode(" UNION ALL ", $sql_query_sel);
            $wpdb->query($sql_query); //phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
        }

        // provide new ids to avoid conflicts
        if ($table_data = get_post_meta($post_id, 'iwptp_data', true)) {
            $table_data = iwptp_sanitize_array(json_decode($table_data, true));
            iwptp_new_ids($table_data); // recursively iterates and switches ids to new randoms
            $table_data['id'] = $new_post_id; // this is the table id and should not be random
            update_post_meta($new_post_id, 'iwptp_data', addslashes(wp_json_encode($table_data)));
        }

        /*
		 * finally, redirect to the edit post screen for the new draft
		 */
        wp_redirect(admin_url('post.php?action=edit&post=' . intval($new_post_id)));
        exit;
    } else {
        wp_die('Post creation failed, could not find original post: ' . intval($post_id));
    }
}

add_action('iwptp_before_apply_user_filters', 'iwptp_pro_shortcode_attributes', 100, 1);
function iwptp_pro_shortcode_attributes($data)
{
    $sc_attrs = $data['query']['sc_attrs'];
    $table_id = $data['id'];

    // attributes
    $attribute_sc_atts = array(
        'attribute' => empty($sc_attrs['attribute']) ? '' : $sc_attrs['attribute'],
        'exclude_attribute' => empty($sc_attrs['exclude_attribute']) ? '' : $sc_attrs['exclude_attribute'],
    );

    foreach ($attribute_sc_atts as $purpose => $string) {
        if (!$string) {
            continue;
        }

        $attrs = explode('|', $string);

        foreach ($attrs as $attribute_string) {
            $split = explode(':', trim($attribute_string));
            $taxonomy = $split[0];

            if (!$split[0]) {
                continue;
            }

            $slug = sanitize_title($taxonomy);

            // already a slug
            if ($taxonomy == $slug) {
                // ensure pa_
                if ('pa_' !== substr($slug, 0, 3)) {
                    $taxonomy = 'pa_' . $slug;
                }
            } else {
                // get slug from name
                $id = wc_attribute_taxonomy_id_by_name($taxonomy);
                $attribute = wc_get_attribute($id);
                $taxonomy = $attribute->slug;
            }

            if (!empty($split[1])) {
                foreach (explode(',', $split[1]) as $term) {
                    $term = get_term_by('slug', trim($term), $taxonomy);
                    $filter_info = array(
                        'filter' => 'attribute',
                        'values' => $term ? array($term->term_taxonomy_id) : array(0),
                        'taxonomy' => $taxonomy,
                        'operator' => $purpose === 'attribute' ? 'IN' : 'NOT IN',
                        'clear_label' => '',
                        'clear_labels_2' => '',
                    );
                    iwptp_update_user_filters($filter_info, false);
                }

                if ($purpose === 'exclude_attribute') {
                    $_GET[$table_id . '_exclude_attribute_' . $taxonomy] = array_map('trim', explode(',', $split[1]));
                }
            }
        }
    }


    // taxonomy
    if (!empty($sc_attrs['taxonomy'])) {
        $taxs = explode('|', $sc_attrs['taxonomy']);
        foreach ($taxs as $taxonomy_string) {
            $split = explode(':', trim($taxonomy_string));
            $taxonomy = $split[0];
            if (!$split[0]) {
                continue;
            }
            $taxonomy = sanitize_title($taxonomy);
            $tax_obj = get_taxonomy($taxonomy);
            if (empty($tax_obj->public) || empty($tax_obj->publicly_queryable)) {
                continue;
            }
            if (!empty($split[1])) {
                foreach (explode(',', $split[1]) as $term) {
                    $term = get_term_by('slug', trim($term), $taxonomy);
                    if ($term && !is_wp_error($term)) {
                        $filter_info = array(
                            'filter'      => 'taxonomy',
                            'values'      => array($term->term_taxonomy_id),
                            'taxonomy'    => $taxonomy,
                            'operator'    => 'IN',
                            'clear_label' => '',
                            'clear_labels_2' => '',
                        );
                        iwptp_update_user_filters($filter_info, false);
                    }
                }
            }
        }
    }

    // tags
    if (!empty($sc_attrs['tags'])) {
        $tag_slugs = explode(',', $sc_attrs['tags']);
        $operator = 'IN';
        if (
            !empty($sc_attrs['tags_operator']) &&
            in_array(strtoupper(trim($sc_attrs['tags_operator'])), array('IN', 'AND', 'NOT IN'))
        ) {
            $operator = strtoupper(trim($sc_attrs['tags_operator']));
        }

        $tt_ids = get_terms(array(
            'taxonomy' => 'product_tag',
            'fields' => 'tt_ids',
            'slug' => $tag_slugs,
        ));

        if ($tt_ids) {
            $filter_info = array(
                'filter'          => 'taxonomy',
                'values'          => $tt_ids,
                'taxonomy'        => 'product_tag',
                'operator'        => $operator,
                'clear_label'     => '',
                'clear_labels_2'  => '',
            );
            iwptp_update_user_filters($filter_info, false);
        }
    }


    // custom fields
    if (!empty($sc_attrs['custom_field'])) {
        $cfs = explode('|', $sc_attrs['custom_field']);
        foreach ($cfs as $cf_string) {
            $split = array_map('trim', explode(':', trim($cf_string)));
            $meta_key = $split[0];
            $meta_val = $split[1];
            $min = 0;
            $max = 0;
            $type = '';

            if (!empty($meta_val)) {

                $filter_info = array(
                    'filter'      => 'custom_field',
                    'meta_key'    => $meta_key,
                    'clear_label' => '',
                    'clear_labels_2' => '',
                );

                if (strtoupper($meta_val) == '*EXISTS*') {
                    $meta_val = '';
                    $compare = 'EXISTS';
                } else if (strtoupper($meta_val) == '*NOT EXISTS*') {
                    $meta_val = '';
                    $compare = 'NOT EXISTS';
                } else if (strtoupper(substr($meta_val, 0, 8)) == '*NOT IN*') {
                    $meta_val = array_map('trim', explode(',', substr($meta_val, 8)));
                    $compare = 'NOT IN';
                } else if (strtoupper(substr($meta_val, 0, 9)) == '*BETWEEN*') {
                    $meta_val = array_map('trim', explode(',', substr($meta_val, 9)));
                    $compare = 'BETWEEN';

                    $min = $meta_val[0];
                    $max = $meta_val[1];
                    $type = 'DECIMAL(10, 3)';
                } else if (strtoupper(substr($meta_val, 0, 6)) == '*LIKE*') {
                    $meta_val = trim(substr($meta_val, 6));
                    $compare = 'LIKE';
                } else { // IN
                    $meta_val = array_map('trim', explode(',', $meta_val));
                    $compare = 'IN';
                }

                $filter_info['values'] = $meta_val;
                $filter_info['compare'] = $compare;
                $filter_info['min'] = $min;
                $filter_info['max'] = $max;
                $filter_info['type'] = $type;

                iwptp_update_user_filters($filter_info, false);
            }
        }
    }

    // min-max price
    if (!empty($sc_attrs['min_price']) || !empty($sc_attrs['max_price'])) {
        $min_price = !empty($sc_attrs['min_price']) ? $sc_attrs['min_price'] : '';
        $max_price = !empty($sc_attrs['max_price']) ? $sc_attrs['max_price'] : '';

        $value = ($min_price ? iwptp_price($min_price) : 0) . ($max_price ? ' - ' . iwptp_price($max_price) : '+');

        // use filter in query
        $filter_info = array(
            'filter'        => 'price_range',
            'values'        => array($value),
            'min_price'     => $min_price,
            'max_price'     => $max_price,
            'clear_label'   => 'Price range',
        );

        iwptp_update_user_filters($filter_info, false);
    }

    // on sale
    if (!empty($sc_attrs['on_sale'])) {
        $filter_info = array(
            'filter' => 'on_sale',
            'value' => true,
        );

        iwptp_update_user_filters($filter_info, true);
    }

    // availability

    // -- exclude
    if (!empty($sc_attrs['exclude_out_of_stock'])) {
        $filter_info = array(
            'filter' => 'availability',
            'operator' => 'NOT IN',
        );

        iwptp_update_user_filters($filter_info, true);

        // -- only out of stock 
    } else if (!empty($sc_attrs['out_of_stock'])) {
        $filter_info = array(
            'filter' => 'availability',
            'operator' => 'IN',
        );

        iwptp_update_user_filters($filter_info, true);

        // -- include out of stock
    } else if (!empty($sc_attrs['include_out_of_stock'])) {
        $filter_info = array(
            'filter' => 'availability',
            'operator' => 'ALSO',
        );

        iwptp_update_user_filters($filter_info, true);
    }
}

// return cart form quantity to min
add_action('iwptp_before_loop', 'iwptp_wcp__woocommerce_quantity_input_args_1');
function iwptp_wcp__woocommerce_quantity_input_args_1()
{
    add_filter('woocommerce_quantity_input_args', 'iwptp_wcp__woocommerce_quantity_input_args_2', 1, 100);
}

function iwptp_wcp__woocommerce_quantity_input_args_2($args)
{
    $args['input_value'] = $args['min_value'];
    return $args;
}

// [... ids="current"]
add_filter('iwptp_shortcode_attributes', 'iwptp__sc_attrs__current_id', 100, 1);
function iwptp__sc_attrs__current_id($sc_attrs)
{
    if (
        !empty($sc_attrs['ids']) &&
        strtolower($sc_attrs['ids']) == 'current' &&
        !empty($GLOBALS['product']) &&
        !is_archive()
    ) {
        $sc_attrs['ids'] = $GLOBALS['product']->get_id();
    }

    return $sc_attrs;
}

// author_username / vendor
add_filter('iwptp_query_args', 'iwptp__query_args__author');
function iwptp__query_args__author($query_args)
{
    $table_data = iwptp_get_table_data();

    if (!empty($table_data['query']['sc_attrs']['vendor'])) {
        $username = $table_data['query']['sc_attrs']['vendor'];
    } else if (!empty($table_data['query']['sc_attrs']['author_username'])) {
        $username = $table_data['query']['sc_attrs']['author_username'];
    }

    if (!empty($username)) {
        $query_args['author_name'] = strtolower(trim($username));
    }

    return $query_args;
}

// product variation

// -- modify sc_attrs to include id 
add_filter('iwptp_shortcode_attributes', 'iwptp_product_variations_auto_add_id', 100, 1);
function iwptp_product_variations_auto_add_id($sc_attrs)
{
    if (
        !empty($sc_attrs['product_variations']) &&
        empty($sc_attrs['ids']) &&
        empty($sc_attrs['variation_skus']) &&
        empty($sc_attrs['variation_ids']) &&
        !empty($GLOBALS['product']) &&
        !is_archive()
    ) {
        global $product;

        $sc_attrs['ids'] = $product->get_id();

        // hide table is not required
        if (
            $product->get_type() !== 'variable' ||
            !$product->get_children()
        ) {
            add_filter('iwptp_container_html_class', 'iwptp_container_html_class__hide_table__empty_variation_table');
        }
    }

    return $sc_attrs;
}

function iwptp_container_html_class__hide_table__empty_variation_table($html_class)
{
    remove_filter('iwptp_container_html_class', 'iwptp_container_html_class__hide_table__empty_variation_table');
    $html_class .= ' iwptp-hide iwptp-hide--empty-variation-table';
    return $html_class;
}

// -- special query for variations 
function iwptp_product_variations_query($query_args)
{
    if (isset($query_args['variation_meta_query'])) {
        $variation_meta_query = $query_args['variation_meta_query'];
        unset($query_args['variation_meta_query']);
    }

    // run a query to get products
    $variable_products = new WP_Query(
        array_merge(
            $query_args,
            array(
                'product_type'    => 'variable',
                'fields'          => 'ids',
                'posts_per_page'  => -1,
                'meta_key'        => '',
                'order_by'        => '',
                'order'           => '',
            )
        )
    );

    $variable_product_ids = $variable_products->posts;
    if (empty($variable_product_ids)) {
        $variable_product_ids = [-1];
    }

    $variation_meta_query['relation'] = 'AND';

    // run another query to get the variations
    $vp_query_args = array(
        'post_parent__in' => $variable_product_ids,
        'post_type' => 'product_variation',
        'post_status' => 'publish',
        'posts_per_page' => !empty($query_args['posts_per_page']) ? $query_args['posts_per_page'] : 10,
        'paged' => !empty($query_args['paged']) ? $query_args['paged'] : 1,
        'tax_query' => array(
            'relation' => (!empty($query_args['tax_query']) && !empty($query_args['tax_query']['relation'])) ? $query_args['tax_query']['relation'] : 'AND',
        ),
        'meta_query' => $variation_meta_query,
        'fields' => 'ids',
    );

    $table_data = iwptp_get_table_data();
    $sc_attrs = $table_data['query']['sc_attrs'];

    // variation skus
    if (!empty($sc_attrs['variation_skus'])) {
        $variation_skus = array_map('trim', explode(',', $sc_attrs['variation_skus']));

        if (!empty($variation_skus)) {
            $vp_query_args['meta_query'][] = array(
                'key' => '_sku',
                'value' => $variation_skus,
                'compare' => 'IN'
            );
        }
    }

    // absorb query args
    //-- orderby
    foreach (array('meta_key', 'order', 'orderby') as $key) {
        if (!empty($query_args[$key])) {
            $vp_query_args[$key] = $query_args[$key];
        }
    }

    if (!empty($query_args['tax_query'])) {
        // ensure meta query
        foreach ($query_args['tax_query'] as $key => $tax_query) {
            if (!is_array($tax_query)) {
                continue; // AND / OR
            }

            //-- attributes
            if ('pa_' == strtolower(substr($tax_query['taxonomy'], 0, 3))) {
                $arr = array(
                    'key'     => 'attribute_' . $tax_query['taxonomy'],
                    'value'   => [],
                    'compare' => 'IN'
                );

                // convert term_taxonomy_id to slugs
                foreach ($tax_query['terms'] as $tt_id) {
                    $term = get_term_by('term_taxonomy_id', $tt_id, $tax_query['taxonomy']);
                    $arr['value'][] = $term->slug;
                }

                $vp_query_args['meta_query'][] = $arr;
            };

            //-- availability
            if (
                'product_visibility' == $tax_query['taxonomy'] &&
                'NOT IN' == $tax_query['operator']
            ) {
                $term_id = gettype($tax_query['terms']) === 'integer' ? $tax_query['terms'] : $tax_query['terms'][0];
                $term_obj = get_term_by('id', $term_id, 'product_visibility');

                if (
                    $term_obj &&
                    !is_wp_error($term_obj) &&
                    $term_obj->name == 'outofstock'
                ) {
                    $arr = array(
                        'key'     => '_stock_status',
                        'value'   => 'instock',
                        'compare' => '=',
                    );
                    $vp_query_args['meta_query'][] = $arr;
                }
            };
        }
    }

    //-- on sale
    $on_sale_filter = iwptp_get_nav_filter('on_sale');
    if ($on_sale_filter && $on_sale_filter['value']) {
        $arr = array(
            'key'     => '_sale_price',
            'value'        => 0,
            'compare' => '>',
        );
        $vp_query_args['meta_query'][] = $arr;
    };

    // price range
    if (!empty($query_args['meta_query']) && !empty($query_args['meta_query']['price_filter'])) {
        $vp_query_args['meta_query']['price_filter'] = $query_args['meta_query']['price_filter'];
    }

    // custom attribute
    $table_data = iwptp_get_table_data();
    if (!empty($table_data['query']['sc_attrs']['custom_attribute'])) {
        $custom_attribute_strs = array_map('trim', explode('|', $table_data['query']['sc_attrs']['custom_attribute']));
        foreach ($custom_attribute_strs as $str) {
            $split = array_map('trim', explode(':', $str));
            if (count($split) !== 2) {
                continue;
            }

            $attribute_name = $split[0];
            $attribute_terms = array_map('trim', explode(',', $split[1]));

            $vp_query_args['meta_query'][] = array(
                'meta_key'    => $attribute_name,
                'value'      => $attribute_terms,
                'operator'    => 'IN',
            );
        }
    }

    $vp_query = new WP_Query(apply_filters('iwptp_variation_query_args', $vp_query_args));

    return apply_filters('iwptp_variation_products', $vp_query);
}

add_filter('iwptp_permitted_shortcode_attributes', 'iwptp_permit_pro_shortcode_attributes', 100, 1);
function iwptp_permit_pro_shortcode_attributes($permitted)
{
    return array_merge($permitted, array(
        'cache',

        'category_relation',
        'category_operator',
        'cat_operator',

        'nav_category',
        'nav_category_id',

        'paginate',

        'product_variations',
        'variation_skus',
        'grouped_product_ids',

        'include_hidden',
        'include_private',
        'attribute',
        'exclude_attribute',
        'custom_field',
        'taxonomy',

        'tags',
        'tags_operator',

        'tag',
        'tag_operator',

        'custom_attribute',
        'attribute_relation',

        'min_price',
        'max_price',

        'dynamic_filters_lazy_load',
        'dynamic_hide_filters',
        'dynamic_recount',

        'main_query_subset',
        'enable_visibility_rules',
        'lazy_load',
        'json_ld',

        'search_orderby',
        'search_order',
        'search_meta_key',

        'featured',
        'on_sale',
        'out_of_stock',
        'include_out_of_stock',
        'exclude_out_of_stock',

        'ti_wishlist',
        'user_favorites',
        'vendor',
        'author_username',

        'product_type',
        'exclude_product_type',

        'one_page_instant_search',
        'one_page_instant_sort_columns',
        'instant_sort',

        'no_results_message',
        'no_results_message_' . strtolower(get_locale()),

        'quick_view_trigger',
        'quick_view_category',
        'quick_view_exclude_category',
        'quick_view_product_type',
        'quick_view_exclude_product_type',

        'exclude_category',
        'exclude_ids',

        'form_mode',
        'hide_form_on_submit',
        'hide_form_on_archive',

        'hide_empty_columns',

        'laptop_attribute_columns',
        'tablet_attribute_columns',
        'phone_attribute_columns',

        'laptop_hide_columns',
        'tablet_hide_columns',
        'phone_hide_columns',

        'laptop_child_row_columns',
        'tablet_child_row_columns',
        'phone_child_row_columns',

        'child_row_toggle_icon_color',
        'child_row_toggle_icon_background_color',

        'laptop_child_row_column_width',
        'tablet_child_row_column_width',
        'phone_child_row_column_width',

        'refresh_table',
        'block_table',

        'store_id',
        'store',

        'meta_key',

        'secondary_orderby',
        'secondary_order',
        'secondary_meta_key',

        'show_related_products',
        'show_upsells',
        'show_cross_sells',

        'show_previous_orders',
        'hide_previous_orders',

        'show_recently_viewed',

        'hide_empty_table',

        'open_dropdown_on_click',
        'enable_dropdown_hover_intent',

        'upcoming',

        'checked_row_background_color',

        'category_required',
        'category_required_message',

        'attribute_required',
        'attribute_required_message',

        'filter_required',
        'filter_required_message',

        'laptop_freeze_heading',
        'laptop_freeze_left',
        'laptop_freeze_right',

        'grab_and_scroll',

        'laptop_freeze_nav_header',
        'laptop_freeze_nav_sidebar',
        'laptop_freeze_nav_footer',

        'tablet_freeze_heading',
        'tablet_freeze_left',
        'tablet_freeze_right',
        'tablet_freeze_nav_header',
        'tablet_freeze_nav_footer',

        'phone_freeze_heading',
        'phone_freeze_left',
        'phone_freeze_right',
        'phone_freeze_nav_header',
        'phone_freeze_nav_footer',

        'laptop_variation_attribute_columns',
        'tablet_variation_attribute_columns',
        'phone_variation_attribute_columns',

        'image_map_labels',

        '_archive',
        '_term',
        '_taxonomy',
        '_only_loop',
        '_search',
        '_return_query_args',
        '_disable_nav',
    ));
}

// hide empty table
add_filter('iwptp_products', 'iwptp__query_args__hide_empty_table');
function iwptp__query_args__hide_empty_table($products)
{
    $table_data = iwptp_get_table_data();
    $sc_attrs = $table_data['query']['sc_attrs'];

    if (empty($sc_attrs['hide_empty_table']) || !empty($_GET[$table_data['id'] . '_filtered'])) {
        return $products;
    }

    if (!$products->found_posts) {
        add_filter('iwptp_container_html_class', 'iwptp_container_html_class__hide_table__hide_empty_table');
        return $products;
    }

    return $products;
}

// -- hide table
function iwptp_container_html_class__hide_table__hide_empty_table($html_class)
{
    remove_filter('iwptp_container_html_class', 'iwptp_container_html_class__hide_table__hide_empty_table');
    return $html_class . ' iwptp-hide ';
}

// open dropdown on click
add_filter('iwptp_element', 'iwptp_element__append_html_class__open_dropdown_on_click');

function iwptp_element__append_html_class__open_dropdown_on_click($elm)
{
    if (in_array($elm['type'], array(
        'sort_by',
        'results_per_page',
        'category_filter',
        'price_filter',
        'tags_filter',
        'attribute_filter',
        'custom_field_filter',
        'taxonomy_filter',
        'availability_filter',
        'on_sale_filter',
        'rating_filter',
    ))) {

        $table_data = iwptp_get_table_data();
        $sc_attrs = $table_data['query']['sc_attrs'];

        if (!empty($sc_attrs['open_dropdown_on_click'])) {
            if (empty($elm['html_class'])) {
                $elm['html_class'] = '';
            }
            $elm['html_class'] .= ' iwptp-tooltip--open-on-click ';
        }
    }

    return $elm;
}

// dropdown hover intent
add_filter('iwptp_element', 'iwptp_element__append_html_class__dropdown_hover_intent');

function iwptp_element__append_html_class__dropdown_hover_intent($elm)
{
    if (in_array($elm['type'], array(
        'sort_by',
        'results_per_page',
        'category_filter',
        'price_filter',
        'tags_filter',
        'attribute_filter',
        'custom_field_filter',
        'taxonomy_filter',
        'availability_filter',
        'on_sale_filter',
        'rating_filter',
    ))) {

        $table_data = iwptp_get_table_data();
        $sc_attrs = $table_data['query']['sc_attrs'];

        if (!empty($sc_attrs['enable_dropdown_hover_intent'])) {
            if (empty($elm['html_class'])) {
                $elm['html_class'] = '';
            }
            $elm['html_class'] .= ' iwptp-tooltip--hover-intent-enabled ';
        }
    }

    return $elm;
}

// recently viewed
add_filter('iwptp_query_args', 'iwptp__query_args__show_recently_viewed');
function iwptp__query_args__show_recently_viewed($query_args)
{
    $table_data = iwptp_get_table_data();
    $sc_attrs = $table_data['query']['sc_attrs'];

    if (empty($sc_attrs['show_recently_viewed'])) {
        return $query_args;
    }

    $ids = !empty($_COOKIE['woocommerce_recently_viewed']) ? (array) explode('|', wp_unslash($_COOKIE['woocommerce_recently_viewed'])) : [];
    $ids = array_reverse(array_filter(array_map('absint', $ids)));

    // hide table if empty
    if (empty($ids)) {
        add_filter('iwptp_container_html_class', 'iwptp_container_html_class__hide_table__show_recently_viewed');
        $query_args['post__in'] = array(0);
        return $query_args;
    }

    $query_args['post__in'] = $ids;

    return $query_args;
}

// -- hide table
function iwptp_container_html_class__hide_table__show_recently_viewed($html_class)
{
    remove_filter('iwptp_container_html_class', 'iwptp_container_html_class__hide_table__show_recently_viewed');
    return $html_class . ' iwptp-hide ';
}

// hide / show table based on user role
add_filter('iwptp_query_args', 'iwptp__query_args__toggle_based_on_user_role');
function iwptp__query_args__toggle_based_on_user_role($query_args)
{
    $table_data = iwptp_get_table_data();
    $sc_attrs = $table_data['query']['sc_attrs'];

    $user = wp_get_current_user();
    $user_roles = (array) $user->roles;

    if (!$user_roles) {
        $user_roles = array('guest');
    }

    $hide = false;

    if ($hide) {
        add_filter('iwptp_container_html_class', 'iwptp_container_html_class__hide_table__based_on_user_role');
        $query_args['post__in'] = array(0);
        return $query_args;
    }

    return $query_args;
}

function iwptp_container_html_class__hide_table__based_on_user_role($html_class)
{
    remove_filter('iwptp_container_html_class', 'iwptp_container_html_class__hide_table__based_on_user_role');
    return $html_class . ' iwptp-hide ';
}

// hide form
add_filter('iwptp_container_html_class', 'iwptp_container_html_class__hide_table__form');
function iwptp_container_html_class__hide_table__form($html_class)
{
    $table_data = iwptp_get_table_data();
    $table_id = $table_data['id'];
    if (
        (!empty($table_data['query']['sc_attrs']['hide_form_on_submit']) &&
            !empty($_GET['hide_form']) &&
            $_GET['hide_form'] == $table_id
        ) ||
        (!empty($table_data['query']['sc_attrs']['hide_form_on_archive']) &&
            (
                (function_exists('is_shop') &&
                    is_shop()
                ) ||
                (function_exists('is_product_tag') &&
                    is_product_tag()
                ) ||
                (function_exists('is_product_category') &&
                    is_product_category()
                ) ||
                (get_query_var('taxonomy') &&
                    taxonomy_is_product_attribute(get_query_var('taxonomy'))
                )
            )
        )
    ) {
        $html_class .= ' iwptp-hide ';
    }

    return $html_class;
}

// -- permit param in JS
add_action('wp_print_scripts', 'iwptp_permit_hide_form_param_js');
function iwptp_permit_hide_form_param_js()
{
?>
    <script>
        if (typeof iwptp_persist_params === 'undefined') {
            var iwptp_persist_params = [];
        }
        iwptp_persist_params.push('hide_form');
    </script>
    <?php
}
// -- permit param in PHP
add_filter('iwptp_permitted_params', 'iwptp_permit_hide_form_param_php', 100, 1);
function iwptp_permit_hide_form_param_php($params)
{
    if (!empty($_GET['hide_form'])) {
        $params[] = 'hide_form';
    }
    return $params;
}

// hide empty columns
add_filter('iwptp_device_columns', 'iwptp_device_columns__empty_cells', 100, 2);

// -- create global array with count equal to number of device cols
function iwptp_device_columns__empty_cells($device_columns, $device)
{
    $table_data = iwptp_get_table_data();

    if (
        !empty($table_data['query']['sc_attrs']['hide_empty_columns']) &&
        $device_columns
    ) {
        $GLOBALS['iwptp_' . $table_data['id'] . '_' . $device . '_empty_columns'] = array_fill(0, count($device_columns), true);
    }

    return $device_columns;
}

// -- while printing cells, mark non-empty cells in that global array
add_filter('iwptp_cell_value', 'iwptp_cell_value__empty_cell', 100, 4);
function iwptp_cell_value__empty_cell($cell_value, $column_index, $column, $device)
{
    $table_data = iwptp_get_table_data();

    if (!empty($table_data['query']['sc_attrs']['hide_empty_columns'])) {
        $empty_columns = &$GLOBALS['iwptp_' . $table_data['id'] . '_' . $device . '_empty_columns'];

        if ($cell_value) {
            $empty_columns[$column_index] = false;
        }
    }

    return $cell_value;
}

// -- add inline css to hide empty columns based on the global array
add_action('iwptp_container_close', 'iwptp_container_close__empty_cells');
function iwptp_container_close__empty_cells()
{
    $table_data = iwptp_get_table_data();

    if (!empty($table_data['query']['sc_attrs']['hide_empty_columns'])) {
        $style = '';

        foreach (array('laptop', 'tablet', 'phone') as $device) {
            if ($empty_columns = &$GLOBALS['iwptp_' . $table_data['id'] . '_' . $device . '_empty_columns']) {
                foreach ($empty_columns as $col_index => $hide) {
                    if ($hide) {
                        $style .= '.iwptp-device-' . $device . ' .iwptp-table-' . $table_data['id'] . ' .iwptp-cell[data-iwptp-column-index="' . $col_index . '"], .iwptp-device-' . $device . ' .iwptp-table-' . $table_data['id'] . ' .iwptp-heading[data-iwptp-column-index="' . $col_index . '"] {display: none !important;}';
                    }
                }
            }
        }

        if ($style) {
            echo '<style>' . wp_kses($style, iwptp_allowed_html_tags()) . '</style>';
        }
    }
}

// attribute columns: laptop_attribute_columns, tablet_attribute_columns, phone_attribute_columns
add_filter('iwptp_device_columns', 'iwptp__toggle_attribute_columns', 100, 2);
function iwptp__toggle_attribute_columns($device_columns, $device)
{
    $table_data = iwptp_get_table_data();
    if (
        empty($table_data['query']['sc_attrs'][$device . '_attribute_columns']) ||
        !empty($table_data['query']['sc_attrs']['form_mode'])
    ) {
        return $device_columns;
    }

    global $product;

    if (
        empty($product) ||
        !$product->get_attributes()
    ) {
        return $device_columns;
    }

    $attributes = $product->get_attributes();

    $map = $table_data['query']['sc_attrs'][$device . '_attribute_columns']; // 2: pa_color | 3: pa_size

    foreach (explode('|', $map) as $line) {
        $arr = explode(':', $line);
        if (count($arr) == 2) {
            $index = $arr[0] - 1;
            $attr = trim(strtolower($arr[1]));

            if (empty($attributes[$attr])) {
                array_splice($device_columns, $index, 1);
            }
        }
    };

    return $device_columns;
}

// hide columns: laptop_hide_columns, tablet_hide_columns, phone_hide_columns
add_filter('iwptp_device_columns', 'iwptp__sc_attr__hide_columns', 100, 2);
function iwptp__sc_attr__hide_columns($device_columns, $device)
{
    $table_data = iwptp_get_table_data();
    if (
        empty($table_data['query']['sc_attrs'][$device . '_hide_columns']) ||
        !empty($table_data['query']['sc_attrs']['form_mode'])
    ) {
        return $device_columns;
    }

    $hide_cols = array_map('trim', explode(",", $table_data['query']['sc_attrs'][$device . '_hide_columns']));

    if (!$hide_cols) {
        return $device_columns;
    }

    foreach ($hide_cols as $key) {
        unset($device_columns[(int) $key - 1]);
    }

    return $device_columns;
}

// skus / ids
add_filter('iwptp_query_args', 'iwptp_query_args__sc_attr__orderby__ids_skus');
function iwptp_query_args__sc_attr__orderby__ids_skus($query_args)
{
    $table_data = iwptp_get_table_data();
    $query = $table_data['query'];
    $sc_attrs = $query['sc_attrs'];

    if (empty($sc_attrs['orderby'])) {
        return $query_args;
    }

    // skus
    if (
        $sc_attrs['orderby'] == 'skus' &&
        !empty($sc_attrs['skus'])
    ) {
        $skus = array_map('trim', explode(',', $sc_attrs['skus']));
        $ids = [];

        foreach ($skus as $sku) {
            $ids[] = wc_get_product_id_by_sku($sku);
        }

        $query_args['orderby'] = 'post__in';
        $query_args['post__in'] = $ids;
    }

    // ids
    if (
        $sc_attrs['orderby'] == 'ids' &&
        !empty($sc_attrs['ids'])
    ) {
        $ids = array_map('trim', explode(',', $sc_attrs['ids']));

        $query_args['orderby'] = 'post__in';
        $query_args['post__in'] = $ids;
    }

    return $query_args;
}

// secondary orderby
// also see: taxonomy sort + secondary orderby
add_filter('iwptp_query_args', 'iwptp_hook_in_secondary_sort_post_clause');
function iwptp_hook_in_secondary_sort_post_clause($query_args)
{

    // skip if sort by taxonomy. See: taxonomy sort + secondary orderby
    if (iwptp_valid_taxonomy_sort_criteria()) {
        return $query_args;
    }

    $table_data = iwptp_get_table_data();
    $query = $table_data['query'];
    $sc_attrs = $query['sc_attrs'];

    $only_loop = !empty($sc_attrs['_only_loop']);

    $primary_orderby = !empty($query_args['orderby']) ? strtolower($query_args['orderby']) : 'date';
    if ($primary_orderby === 'date id') {
        $primary_orderby = 'date';
    } else if (
        $primary_orderby === 'menu_order title'
    ) {
        $primary_orderby = 'menu_order';
    }
    $primary_order = !empty($query_args['order']) ? strtoupper($query_args['order']) : 'DESC';
    $primary_meta = !empty($query_args['meta_key']) ? $query_args['meta_key'] : false;

    $secondary_orderby = !empty($sc_attrs['secondary_orderby']) ? strtolower($sc_attrs['secondary_orderby']) : false;
    $secondary_order = !empty($sc_attrs['secondary_order']) && in_array(strtoupper($sc_attrs['secondary_order']), array('ASC', 'DESC'))  ? strtoupper($sc_attrs['secondary_order']) : 'ASC';
    $secondary_meta = !empty($sc_attrs['secondary_custom_field']) ? $sc_attrs['secondary_custom_field'] : false;

    if ($secondary_orderby === 'sku') {
        $secondary_orderby = 'meta_value';
        $primary_meta = '_sku';
    } else if ($secondary_orderby === 'sku_num') {
        $secondary_orderby = 'meta_value_num';
        $secondary_meta = '_sku';
    }

    if (
        $secondary_orderby &&
        $secondary_orderby !== $primary_orderby &&
        !$only_loop
    ) {

        if (!in_array($secondary_orderby, ['meta_value', 'meta_value_num'])) {
            $GLOBALS['iwptp_primary_orderby_args'] = array(
                'primary_orderby' => $primary_orderby,
                'primary_order' => $primary_order,
                'primary_meta' => $primary_meta,
            );

            $GLOBALS['iwptp_secondary_orderby_args'] = array(
                'secondary_orderby' => $secondary_orderby,
                'secondary_order' => $secondary_order,
                'secondary_meta' => $secondary_meta,
            );

            add_filter('posts_clauses', 'iwptp_secondary_sort_post_clauses', 1000, 1);
        } else { // secondary_orderby: meta

            if (in_array($primary_orderby, ['meta_value', 'meta_value_num'])) { // primary_orderby: meta

                unset($query_args['meta_key']);

                if (empty($query_args['meta_query'])) {
                    $query_args['meta_query'] = [];
                }

                $query_args['meta_query']['relation'] = 'AND';
                $query_args['meta_query']['iwptp_primary_clause'] = array(
                    'key' => $primary_meta,
                    'compare' => 'EXISTS',
                );
                $query_args['meta_query']['iwptp_secondary_clause'] = array(
                    'key' => $secondary_meta,
                    'compare' => 'EXISTS',
                );

                $query_args['orderby'] = array(
                    'iwptp_primary_clause' => $primary_order,
                    'iwptp_secondary_clause' => $secondary_order,
                );
            } else { // primary_orderby: NOT meta

                $query_args['orderby'] = array(
                    $primary_orderby => $primary_order,
                    $secondary_orderby => $secondary_order,
                );

                $query_args['meta_key'] = $secondary_meta;
            }
        }
    }

    return $query_args;
}

add_filter('iwptp_products', 'iwptp_unhook_secondary_sort_post_clause');
function iwptp_unhook_secondary_sort_post_clause($products)
{
    remove_filter('posts_clauses', 'iwptp_secondary_sort_post_clauses', 1000, 1);
    return $products;
}

function iwptp_secondary_sort_post_clauses($args)
{
    extract($GLOBALS['iwptp_primary_orderby_args']);
    extract($GLOBALS['iwptp_secondary_orderby_args']);

    unset($GLOBALS['iwptp_primary_orderby_args']);
    unset($GLOBALS['iwptp_secondary_orderby_args']);

    if ($primary_orderby === 'post__in') {
        return $args;
    }

    $primary_clause = iwptp_create_orderby_clause(array(
        'orderby'   => $primary_orderby,
        'order'     => $primary_order,
        'meta_key'  => $primary_meta,
    ));

    $secondary_clause = iwptp_create_orderby_clause(array(
        'orderby'   => $secondary_orderby,
        'order'     => $secondary_order,
        'meta_key'  => $secondary_meta,
    ));

    $args['join']    = iwptp_append_product_sorting_table_join($args['join']);
    $args['orderby'] = " $primary_clause, $secondary_clause ";

    return $args;
}

function iwptp_create_orderby_clause($args)
{
    global $wpdb;

    $clauses = array(
        'title'             => " $wpdb->posts.post_title %order% ",
        'date'              => " $wpdb->posts.post_date %order% ",
        'menu_order'        => " $wpdb->posts.menu_order %order% ",
        'rating'            => " wc_product_meta_lookup.average_rating %order% ",
        'price'             => " wc_product_meta_lookup.min_price %order% ",
        'popularity'        => " wc_product_meta_lookup.popularity %order% ",
        // rand
        'meta_value_num'    => " $wpdb->postmeta.meta_value+0 %order% ",
        'meta_value'        => " $wpdb->postmeta.meta_value %order% ",
        'id'                => " wc_product_meta_lookup.product_id %order% ",
        'sku_num'           => " $wpdb->postmeta.meta_value+0 %order% ",
        'sku'               => " $wpdb->postmeta.meta_value %order% ",
    );

    return str_replace('%order%', $args['order'], $clauses[$args['orderby']]);
}

function iwptp_append_product_sorting_table_join($sql)
{
    global $wpdb;

    if (!strstr($sql, 'wc_product_meta_lookup')) {
        $sql .= " LEFT JOIN {$wpdb->wc_product_meta_lookup} wc_product_meta_lookup ON $wpdb->posts.ID = wc_product_meta_lookup.product_id ";
    }
    return $sql;
}

// order by taxonomy (category, attribute, tags, other product taxonomy)

// -- force table query to return all results so we can sort 
add_filter('iwptp_query_args', 'iwptp_query_args__sort_by_taxonomy__force_results_no_limit');
function iwptp_query_args__sort_by_taxonomy__force_results_no_limit($query_args)
{
    if (!iwptp_valid_taxonomy_sort_criteria()) {
        return $query_args;
    }

    $table_data = iwptp_get_table_data();

    $GLOBALS['iwptp_sort_by_taxonomy__original_posts_per_page'] = $query_args['posts_per_page'];
    $query_args['posts_per_page'] = -1;

    $GLOBALS['iwptp_sort_by_taxonomy__original_paged'] = !empty($query_args['paged']) ? $query_args['paged'] : 1;
    $query_args['paged'] = 1;

    return $query_args;
}

// -- taxonomy sort + secondary orderby (if it exists)
// Also see: secondary orderby
add_filter('iwptp_products', 'iwptp_products__sort_by_taxonomy__insert_modified_results');
function iwptp_products__sort_by_taxonomy__insert_modified_results($result)
{
    if (
        !iwptp_valid_taxonomy_sort_criteria() ||
        !$result->posts
    ) {
        return $result;
    }

    $table_data = iwptp_get_table_data();
    global $wpdb;

    $meta_key = '_sku';

    $taxonomy = iwptp_taxonomy_sort__get_taxonomy();
    $compare_as_number = iwptp_taxonomy_sort__compare_as_number();

    $focus_terms_sql = '';
    if ($focus_terms_arr = iwptp_taxonomy_sort__get_focus_terms()) {
        $placeholders = implode(', ', array_fill(0, count($focus_terms_arr), '%s'));
        $focus_terms_sql = $wpdb->prepare("AND {$wpdb->prefix}terms.slug IN ($placeholders)", $focus_terms_arr);
    }

    $ignore_terms_sql = '';
    if ($ignore_terms_arr = iwptp_taxonomy_sort__get_ignore_terms()) {
        $placeholders = implode(', ', array_fill(0, count($ignore_terms_arr), '%s'));
        $ignore_terms_sql = $wpdb->prepare("AND {$wpdb->prefix}terms.slug NOT IN ($placeholders)", $ignore_terms_arr);
    }

    $orderby_params = iwptp_get_nav_filter('orderby');

    $primary_order = !empty($orderby_params['order']) ? $orderby_params['order'] : 'ASC';

    // secondary
    $secondary_orderby = false;
    $secondary_order = false;
    $secondary_order_sql = '';
    $secondary_join_sql = '';

    $secondary_custom_field = '';
    $secondary_meta_key_sql = '';

    if (
        !empty($table_data['query']['sc_attrs']['secondary_orderby']) &&
        in_array(
            trim(strtolower($table_data['query']['sc_attrs']['secondary_orderby'])),
            array(
                'id',

                // posts table
                'title',
                'menu_order',

                // product meta lookup table
                'sales',
                'stock',
                'price',

                // postmeta table        
                'custom_field_number',
                'custom_field_text',
                'sku_number',
                'sku_text'
            )
        )
    ) {
        $secondary_orderby = trim(strtolower($table_data['query']['sc_attrs']['secondary_orderby']));
        $secondary_order = 'ASC';
        if (
            !empty($table_data['query']['sc_attrs']['secondary_order']) &&
            in_array(trim(strtoupper($table_data['query']['sc_attrs']['secondary_order'])), array('ASC', 'DESC'))
        ) {
            $secondary_order = trim(strtoupper($table_data['query']['sc_attrs']['secondary_order']));
        }

        global $wpdb;

        switch ($secondary_orderby) {

                // posts table
            case 'title':
            case 'menu_order':
                if ($secondary_orderby == 'title') {
                    $secondary_orderby_column = "{$wpdb->prefix}posts.post_title";
                } else {
                    $secondary_orderby_column = "{$wpdb->prefix}posts.menu_order + 0 ";
                }

                $secondary_join_sql = " LEFT JOIN {$wpdb->prefix}posts
        ON {$wpdb->prefix}term_relationships.object_id = {$wpdb->prefix}posts.ID ";

                break;

                // postmeta table
            case 'custom_field_number':
            case 'custom_field_text':

                if ($secondary_orderby == 'custom_field_text') {
                    $secondary_orderby_column = "{$wpdb->prefix}postmeta.meta_value";
                } else {
                    $secondary_orderby_column = "{$wpdb->prefix}postmeta.meta_value + 0";
                }

                $secondary_join_sql = " LEFT JOIN {$wpdb->prefix}postmeta
        ON {$wpdb->prefix}term_relationships.object_id = {$wpdb->prefix}postmeta.post_id ";

                $meta_key = $table_data['query']['sc_attrs']['secondary_custom_field'];
                $secondary_meta_key_sql = $wpdb->prepare(" AND {$wpdb->prefix}postmeta.meta_key = %s ", $meta_key);

                break;

                // product meta lookup table
            case 'sales':
            case 'stock':
            case 'price':
            case 'sku_number':
            case 'sku_text':
                if ($secondary_orderby == 'sales') {
                    $secondary_orderby_column = "{$wpdb->prefix}wc_product_meta_lookup.total_sales";
                } else if ($secondary_orderby == 'stock') {

                    $secondary_orderby_column = "{$wpdb->prefix}wc_product_meta_lookup.stock_quantity + 0";
                } else if ($secondary_orderby ==  'sku_text') {

                    $secondary_orderby_column = "{$wpdb->prefix}wc_product_meta_lookup.sku";
                } else if ($secondary_orderby ==  'sku_number') {

                    $secondary_orderby_column = "{$wpdb->prefix}wc_product_meta_lookup.sku + 0";
                } else if ($secondary_orderby == 'price') {
                    if ($secondary_order === 'ASC') {
                        $secondary_orderby_column = "{$wpdb->prefix}wc_product_meta_lookup.min_price + 0";
                    } else {
                        $secondary_orderby_column = "{$wpdb->prefix}wc_product_meta_lookup.max_price + 0";
                    }
                }

                $secondary_join_sql = " LEFT JOIN {$wpdb->prefix}wc_product_meta_lookup
        ON {$wpdb->prefix}term_relationships.object_id = {$wpdb->prefix}wc_product_meta_lookup.product_id ";

                break;

            default: // id
                $secondary_orderby_column = "{$wpdb->prefix}terms.object_id";
                break;
        }

        $secondary_order_sql = ", $secondary_orderby_column $secondary_order ";
    }

    $sql_req = $wpdb->prepare(
        "SELECT DISTINCT object_id 
    FROM {$wpdb->prefix}terms 
      LEFT JOIN {$wpdb->prefix}term_taxonomy 
        ON {$wpdb->prefix}terms.term_id = {$wpdb->prefix}term_taxonomy.term_id 
      LEFT JOIN {$wpdb->prefix}term_relationships 
        ON {$wpdb->prefix}term_relationships.term_taxonomy_id = {$wpdb->prefix}term_taxonomy.term_taxonomy_id " .
            $secondary_join_sql . ////phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
            "WHERE {$wpdb->prefix}term_taxonomy.taxonomy = %s " .
            $secondary_meta_key_sql . " " . //phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
            "AND object_id IN (" . implode(',', $result->posts) . ") " . //phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
            $focus_terms_sql . " " . //phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
            $ignore_terms_sql . " " . //phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
            "ORDER BY {$wpdb->prefix}terms.name " . ($compare_as_number ? "+ 0" : "") . " $primary_order " . //phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
            $secondary_order_sql, //phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
        $taxonomy
    );

    $sorted_ids = $wpdb->get_col($sql_req); //phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

    $posts_per_page = $GLOBALS['iwptp_sort_by_taxonomy__original_posts_per_page'];
    if (
        !$posts_per_page ||
        $posts_per_page < 0
    ) {
        $posts_per_page = 9999999;
    }

    if (iwptp_taxonomy_sort__include_all()) {
        $sorted_ids = array_map('intval', $sorted_ids);
        $remaining_ids = array_diff($result->posts, $sorted_ids);
        $sorted_ids = array_merge($sorted_ids, $remaining_ids);

        // if( $primary_order == 'ASC' ){
        //   $sorted_ids = array_merge( $remaining_ids, $sorted_ids );
        // }else{
        //   $sorted_ids = array_merge( $sorted_ids, $remaining_ids );
        // }    
    }

    $offset = $posts_per_page * ($GLOBALS['iwptp_sort_by_taxonomy__original_paged'] - 1);

    $result->posts = array_slice($sorted_ids, $offset, $posts_per_page); // get pagination offset
    $result->found_posts = count($sorted_ids);
    $result->post_count = count($result->posts);
    $result->max_num_pages = ceil($result->found_posts / $posts_per_page);
    $result->query_vars['paged'] = $GLOBALS['iwptp_sort_by_taxonomy__original_paged'];

    return $result;
}

function iwptp_valid_taxonomy_sort_criteria()
{
    $orderby_params = iwptp_get_nav_filter('orderby');

    if (!in_array($orderby_params['orderby'], array(
        'category',
        'attribute',
        'attribute_num',
        'taxonomy'
    ))) {
        return false;
    }

    if (
        (in_array($orderby_params['orderby'], array('attribute', 'attribute_num')) &&
            empty($orderby_params['orderby_attribute'])
        ) ||
        ($orderby_params['orderby'] == 'taxonomy' &&
            empty($orderby_params['orderby_taxonomy'])
        )
    ) {
        return false;
    }

    return true;
}

function iwptp_taxonomy_sort__get_taxonomy()
{
    $orderby_params = iwptp_get_nav_filter('orderby');

    switch ($orderby_params['orderby']) {
        case 'attribute':
        case 'attribute_num':
            return $orderby_params['orderby_attribute'];

        case 'taxonomy':
            return $orderby_params['orderby_taxonomy'];

        case 'category':
            return 'product_cat';

        default:
            return false;
    }
}

function iwptp_taxonomy_sort__compare_as_number()
{
    $orderby_params = iwptp_get_nav_filter('orderby');
    return $orderby_params['orderby'] == 'attribute_num';
}

function iwptp_taxonomy_sort__include_all()
{
    $orderby_params = iwptp_get_nav_filter('orderby');
    return !(empty($orderby_params['orderby_attribute_include_all']) &&
        empty($orderby_params['orderby_taxonomy_include_all'])
    );
}

function iwptp_taxonomy_sort__get_focus_terms()
{
    $orderby_params = iwptp_get_nav_filter('orderby');
    $focus_terms_string = '';

    switch ($orderby_params['orderby']) {
        case 'attribute':
        case 'attribute_num':
            if (empty($orderby_params['orderby_focus_attribute_term'])) {
                return false;
            } else {
                $focus_terms_string = $orderby_params['orderby_focus_attribute_term'];
            }

            break;

        case 'taxonomy':
            if (empty($orderby_params['orderby_focus_taxonomy_term'])) {
                return false;
            } else {
                $focus_terms_string = $orderby_params['orderby_focus_taxonomy_term'];
            }

            break;

        case 'category':
            if (empty($orderby_params['orderby_focus_category'])) {
                return false;
            } else {
                $focus_terms_string = $orderby_params['orderby_focus_category'];
            }

            break;
    }

    if (
        $focus_terms_string &&
        trim($focus_terms_string)
    ) {
        return preg_split("/\r\n|\n|\r/", $focus_terms_string);
    } else {
        return false;
    }
}

function iwptp_taxonomy_sort__get_ignore_terms()
{
    $orderby_params = iwptp_get_nav_filter('orderby');
    $ignore_terms_string = '';

    switch ($orderby_params['orderby']) {
        case 'attribute':
        case 'attribute_num':
            if (empty($orderby_params['orderby_ignore_attribute_term'])) {
                return false;
            } else {
                $ignore_terms_string = $orderby_params['orderby_ignore_attribute_term'];
            }

            break;

        case 'taxonomy':
            if (empty($orderby_params['orderby_ignore_taxonomy_term'])) {
                return false;
            } else {
                $ignore_terms_string = $orderby_params['orderby_ignore_taxonomy_term'];
            }

            break;

        case 'category':
            if (empty($orderby_params['orderby_ignore_category'])) {
                return false;
            } else {
                $ignore_terms_string = $orderby_params['orderby_ignore_category'];
            }

            break;
    }

    if (
        $ignore_terms_string &&
        trim($ignore_terms_string)
    ) {
        return preg_split("/\r\n|\n|\r/", $ignore_terms_string);
    } else {
        return false;
    }
}

// variation sort by attribute (convert to custom field)
add_filter('iwptp_variation_query_args', 'iwptp_taxonomy_sort__variation_by_attribute_query');
function iwptp_taxonomy_sort__variation_by_attribute_query($args)
{
    $taxonomy = iwptp_taxonomy_sort__get_taxonomy();
    $compare_as_number = iwptp_taxonomy_sort__compare_as_number();

    if ($taxonomy) {
        $args['orderby'] = $compare_as_number ? 'meta_value_num' : 'meta_value';
        $args['meta_key'] = 'attribute_' . $taxonomy;
    }

    return $args;
}


// sticky nav sidebar

// -- style attribute
add_filter('iwptp_nav_sidebar_style', 'iwptp_laptop_freeze_nav_sidebar_style_attribute', 10, 1);
function iwptp_laptop_freeze_nav_sidebar_style_attribute($style)
{
    $data = iwptp_get_table_data();
    if (
        !empty($data['query']['sc_attrs']) &&
        !empty($data['query']['sc_attrs']['laptop_freeze_nav_sidebar'])
    ) {
        $top_offset = empty($data['query']['sc_attrs']['laptop_scroll_offset']) ? '0' : ((int) $data['query']['sc_attrs']['laptop_scroll_offset']) . 'px';
        $style .= 'top: ' . $top_offset;
    }

    return $style;
}

// -- class attribute
add_filter('iwptp_nav_sidebar_class', 'iwptp_laptop_freeze_nav_sidebar_class_attribute', 10, 1);
function iwptp_laptop_freeze_nav_sidebar_class_attribute($classes)
{
    $data = iwptp_get_table_data();
    if (
        !empty($data['query']['sc_attrs']) &&
        !empty($data['query']['sc_attrs']['laptop_freeze_nav_sidebar'])
    ) {
        $classes .= ' iwptp-sticky ';
    }

    return $classes;
}

// sticky nav header
// -- style attribute
add_filter('iwptp_nav_header_style', 'iwptp_laptop_freeze_nav_header_style_attribute', 10, 1);
function iwptp_laptop_freeze_nav_header_style_attribute($style)
{
    $data = iwptp_get_table_data();
    if (
        !empty($data['query']['sc_attrs']) &&
        !empty($data['query']['sc_attrs']['laptop_freeze_nav_header'])
    ) {
        $top_offset = empty($data['query']['sc_attrs']['laptop_scroll_offset']) ? '0' : ((int) $data['query']['sc_attrs']['laptop_scroll_offset']) . 'px';
        $style .= 'top: ' . $top_offset;
    }

    return $style;
}

add_filter('iwptp_nav_footer_style', 'iwptp_laptop_freeze_nav_footer_style_attribute', 10, 1);
function iwptp_laptop_freeze_nav_footer_style_attribute($style)
{
    $data = iwptp_get_table_data();
    if (!empty($data['query']['sc_attrs']) && !empty($data['query']['sc_attrs']['laptop_freeze_nav_footer'])) {
        $top_offset = empty($data['query']['sc_attrs']['laptop_scroll_offset']) ? '0' : ((int) $data['query']['sc_attrs']['laptop_scroll_offset']) . 'px';
        $style .= 'top: ' . $top_offset;
    }

    return $style;
}

// -- class attribute
add_filter('iwptp_nav_header_class', 'iwptp_freeze_nav_header_class_attribute', 10, 1);
function iwptp_freeze_nav_header_class_attribute($classes)
{
    $data = iwptp_get_table_data();

    $device = 'laptop';
    if (!empty($_GET[$data['id'] . '_device'])) {
        $device = sanitize_text_field($_GET[intval($data['id']) . '_device']);
    }

    if (
        !empty($data['query']['sc_attrs']) &&
        !empty($data['query']['sc_attrs'][$device . '_freeze_nav_header'])
    ) {
        $classes .= ' iwptp-sticky ';
    }

    return $classes;
}

add_filter('iwptp_nav_footer_class', 'iwptp_freeze_nav_footer_class_attribute', 10, 1);
function iwptp_freeze_nav_footer_class_attribute($classes)
{
    $data = iwptp_get_table_data();

    $device = 'laptop';
    if (!empty($_GET[$data['id'] . '_device'])) {
        $device = sanitize_text_field($_GET[intval($data['id']) . '_device']);
    }

    if (!empty($data['query']['sc_attrs']) && !empty($data['query']['sc_attrs'][$device . '_freeze_nav_footer'])) {
        $classes .= ' iwptp-sticky ';
    }

    return $classes;
}

// up sell products
add_filter('iwptp_query_args', 'iwptp_upsell_products');
function iwptp_upsell_products($query_args)
{
    unset($query_args['p']);

    $data = iwptp_get_table_data();
    if (
        !empty($data['query']['sc_attrs']) &&
        !empty($data['query']['sc_attrs']['show_upsells']) &&
        !empty($GLOBALS['post']) &&
        $product = wc_get_product(is_numeric($data['query']['sc_attrs']['show_upsells']) ? $data['query']['sc_attrs']['show_upsells'] : get_the_id())
    ) {
        $upsell_ids = $product->get_upsell_ids();

        if (!$upsell_ids) {
            $upsell_ids = array(0);
            add_filter('iwptp_container_html_class', 'iwptp_container_html_class__hide_table__no_related_products');
        }

        if (!isset($query_args['post__in'])) {
            $query_args['post__in'] = $upsell_ids;
        } else {
            $query_args['post__in'] = array_intersect($query_args['post__in'], $upsell_ids);
            if (!$query_args['post__in']) {
                $query_args['post__in'] = array(0);
            }
        }
    }

    return $query_args;
}

// cross sell products
add_filter('iwptp_query_args', 'iwptp_cross_sell_products');
function iwptp_cross_sell_products($query_args)
{
    unset($query_args['p']);

    $data = iwptp_get_table_data();
    if (
        !empty($data['query']['sc_attrs']) &&
        !empty($data['query']['sc_attrs']['show_cross_sells']) &&
        !empty($GLOBALS['post']) &&
        $product = wc_get_product(is_numeric($data['query']['sc_attrs']['show_cross_sells']) ? $data['query']['sc_attrs']['show_cross_sells'] : get_the_id())
    ) {
        $cross_sell_ids = $product->get_cross_sell_ids();

        if (!$cross_sell_ids) {
            $cross_sell_ids = array(0);
            add_filter('iwptp_container_html_class', 'iwptp_container_html_class__hide_table__no_related_products');
        }

        if (!isset($query_args['post__in'])) {
            $query_args['post__in'] = $cross_sell_ids;
        } else {
            $query_args['post__in'] = array_intersect($query_args['post__in'], $cross_sell_ids);
            if (!$query_args['post__in']) {
                $query_args['post__in'] = array(0);
            }
        }
    }

    return $query_args;
}

// related products
add_filter('iwptp_query_args', 'iwptp_related_products');
function iwptp_related_products($query_args)
{
    unset($query_args['p']);

    $data = iwptp_get_table_data();
    if (
        !empty($data['query']['sc_attrs']) &&
        !empty($data['query']['sc_attrs']['show_related_products']) &&
        !empty($GLOBALS['post']) &&
        $product = wc_get_product(is_numeric($data['query']['sc_attrs']['show_related_products']) ? $data['query']['sc_attrs']['show_related_products'] : get_the_id())
    ) {
        $related_product_ids = wc_get_related_products($product->get_id(), 9999999); // required

        if (!$related_product_ids) {
            $related_product_ids = array(0);
            add_filter('iwptp_container_html_class', 'iwptp_container_html_class__hide_table__no_related_products');
        }

        if (!isset($query_args['post__in'])) {
            $query_args['post__in'] = $related_product_ids;
        } else {
            $query_args['post__in'] = array_intersect($query_args['post__in'], $related_product_ids);
            if (!$query_args['post__in']) {
                $query_args['post__in'] = array(0);
            }
        }
    }

    return $query_args;
}

// hide related products filter hook handler
function iwptp_container_html_class__hide_table__no_related_products($html_class)
{
    remove_filter('iwptp_container_html_class', 'iwptp_container_html_class__hide_table__no_previous_orders');
    return $html_class . ' iwptp-hide ';
}

// fill out ids="" with current product when displaying cross sell, upsell and related products
add_filter('iwptp_shortcode_attributes', 'iwptp_assign_id_for_related_products');
function iwptp_assign_id_for_related_products($atts)
{
    global $product;
    if ($product) {
        foreach ($atts as $key => &$val) {
            if (in_array($key, array('show_related_products', 'show_upsells', 'show_cross_sells'))) {
                $val = $product->get_id();
            }
        }
    }

    return $atts;
}


// exclude category
add_filter('iwptp_query_args', 'iwptp_query_args__sc_attrs__exclude_category', 20, 1);
function iwptp_query_args__sc_attrs__exclude_category($query_args = [])
{
    $table_data = iwptp_get_table_data();
    $sc_attrs = $table_data['query']['sc_attrs'];

    if (
        !empty($sc_attrs['exclude_category']) &&
        trim($sc_attrs['exclude_category'])
    ) {
        $term_slugs = array_map('trim', explode(',', $sc_attrs['exclude_category']));

        if (empty($query_args['tax_query'])) {
            $query_args['tax_query'] = [];
        }
        $query_args['tax_query'][] = array(
            'taxonomy' => 'product_cat',
            'field' => 'slug',
            'terms' => $term_slugs,
            'operator' => 'NOT IN'
        );
    }

    return $query_args;
}

// exclude ids
add_filter('iwptp_query_args', 'iwptp_query_args__sc_attrs__exclude_ids', 20, 1);
function iwptp_query_args__sc_attrs__exclude_ids($query_args = [])
{
    $table_data = iwptp_get_table_data();
    $sc_attrs = $table_data['query']['sc_attrs'];

    if (
        !empty($sc_attrs['exclude_ids']) &&
        trim($sc_attrs['exclude_ids'])
    ) {
        $ids = array_map('trim', explode(',', $sc_attrs['exclude_ids']));

        if (empty($query_args['post__not_in'])) {
            $query_args['post__not_in'] = [];
        }

        $query_args['post__not_in'] = array_merge($query_args['post__not_in'], $ids);
    }

    return $query_args;
}


// category relation
add_filter('iwptp_query_args', 'iwptp_pro_category_relation', 20, 1);
function iwptp_pro_category_relation($query_args = array())
{
    $data = iwptp_get_table_data();
    $original_categories = (isset($data['query']['category'])) ? $data['query']['category'] : [];
    $sc_attrs = $data['query']['sc_attrs'];

    if (
        !empty($sc_attrs) &&
        (
            (!empty($sc_attrs['cat_operator']) &&
                strtoupper($sc_attrs['cat_operator']) === 'AND'
            ) ||
            (!empty($sc_attrs['category_operator']) &&
                strtoupper($sc_attrs['category_operator']) === 'AND'
            ) ||
            (!empty($sc_attrs['category_relation']) &&
                strtoupper($sc_attrs['category_relation']) === 'AND'
            )
        )
    ) {
        $category_array = (isset($data['query']['category'])) ? $data['query']['category'] : [];
        if (gettype($category_array) !== 'array') {
            $category_array = explode(',', $data['query']['category']);
        }

        foreach ($query_args['tax_query'] as &$arr) {
            if (
                $arr['taxonomy'] === 'product_cat' &&
                ($category_array != $arr['terms'] ||
                    !empty($_GET[$data['id'] . '_product_cat'])
                )
            ) {
                $arr['operator'] = 'AND';
                $arr['include_children'] = false;
            }
        }
    }

    return $query_args;
}

// main query subset
add_filter('iwptp_query_args', 'iwptp_main_query_subset');
function iwptp_main_query_subset($query_args)
{
    $table_data = iwptp_get_table_data();
    if (
        !empty($table_data['query']['sc_attrs']['_archive']) &&
        !empty($table_data['query']['sc_attrs']['main_query_subset'])
    ) {

        $post_ids = iwptp_session()->get(iwptp_archive__get_key() . '_post_ids');

        if (NULL === $post_ids) {
            $query_vars = iwptp_session()->get(iwptp_archive__get_key() . '_query_vars');

            $query_vars['s'] = '';
            $query_vars['posts_per_page'] = -1;
            $query_vars['fields'] = 'ids';
            $query_vars['page'] = $query_vars['paged'] = 1;

            $query = new WP_Query($query_vars);
            $post_ids = $query->posts;
            iwptp_session()->set(iwptp_archive__get_key() . '_post_ids', $post_ids);
        }

        if (!empty($query_args['post__in'])) {
            $query_args['post__in'] = array_intersect($query_args['post__in'], $post_ids);
        } else {
            $query_args['post__in'] = $post_ids;
        }
    }

    return $query_args;
}

// set current product as global post
add_filter('iwptp_product', 'iwptp_pro_global_post', 100);
function iwptp_pro_global_post($product)
{
    $GLOBALS['post'] = get_post($product->get_id()); // used by product addons
    return $product;
}

// archive overrider facility
function iwptp_woocommerce_content()
{
    if (is_singular('product')) {
        while (have_posts()) :
            the_post();
            wc_get_template_part('content', 'single-product');
        endwhile;
    } else {
    ?>

        <?php if (apply_filters('woocommerce_show_page_title', true)) : ?>
            <h1 class="page-title"><?php woocommerce_page_title(); ?></h1>
        <?php endif; ?>

        <?php do_action('woocommerce_archive_description'); ?>

        <?php if (have_posts()) : ?>

            <?php do_action('woocommerce_before_shop_loop'); ?>

            <?php
            if (!function_exists('iwptp_archive_override') || !iwptp_archive_override()) {
                woocommerce_product_loop_start();
                if (wc_get_loop_prop('total')) {
                    while (have_posts()) {
                        the_post();
                        do_action('woocommerce_shop_loop');
                        wc_get_template_part('content', 'product');
                    }
                }
                woocommerce_product_loop_end();
            }
            ?>

            <?php do_action('woocommerce_after_shop_loop'); ?>

        <?php else : ?>

            <?php do_action('woocommerce_no_products_found'); ?>

        <?php
        endif;
    }
}

// form mode

// -- inhibit results
add_filter('iwptp_query_args', 'iwptp_form_mode__inhibit_results', 20, 1);
function iwptp_form_mode__inhibit_results($query_args = [])
{
    $table_data = iwptp_get_table_data();
    if (
        !empty($table_data['query']['sc_attrs']) &&
        !empty($table_data['query']['sc_attrs']['form_mode'])
    ) {
        $query_args['post__in'] = array(0);
    }

    return $query_args;
}

// -- container html class
add_filter('iwptp_container_html_class', 'iwptp_form_mode__container_html_class');
function iwptp_form_mode__container_html_class($html_class)
{
    $table_data = iwptp_get_table_data();
    if (
        !empty($table_data['query']['sc_attrs']) &&
        !empty($table_data['query']['sc_attrs']['form_mode'])
    ) {
        $html_class .= ' iwptp-form-mode ';
    }

    return $html_class;
}

// required category / attribute
add_filter('iwptp_query_args', 'iwptp_required_filter_nav_missing', 20, 1);
function iwptp_required_filter_nav_missing($query_args = [])
{
    $table_data = iwptp_get_table_data();
    $sc_attrs = $table_data['query']['sc_attrs'];

    // category
    if (!empty($sc_attrs['category_required']) && (empty($_GET[$table_data['id'] . '_product_cat']) || !count($_GET[$table_data['id'] . '_product_cat']))) {
        $GLOBALS['iwptp_table_instance']->category_required_but_missing = true;
    }

    // attribute  
    if (!empty($table_data['query']['sc_attrs']) && !empty($table_data['query']['sc_attrs']['attribute_required'])) {
        $required_attrs = [];
        $missing_attrs = [];

        $attrs = array_map('trim', explode(',', $table_data['query']['sc_attrs']['attribute_required']));
        foreach ($attrs as $taxonomy) {
            $slug = sanitize_title($taxonomy);
            // already a slug
            if ($taxonomy == $slug) {
                // ensure pa_
                if ('pa_' !== substr($slug, 0, 3)) {
                    $taxonomy = 'pa_' . $slug;
                }
            } else {
                // get slug from name
                $id = wc_attribute_taxonomy_id_by_name($taxonomy);
                $attribute = wc_get_attribute($id);
                $taxonomy = $attribute->slug;
            }

            if (
                empty($_GET[$table_data['id'] . '_attr_' . $taxonomy]) ||
                !count($_GET[$table_data['id'] . '_attr_' . $taxonomy])
            ) {
                $missing_attrs[] = $taxonomy;
            }

            $required_attrs[] = $taxonomy;
        }

        $GLOBALS['iwptp_table_instance']->attribute_required_but_missing = $missing_attrs ? $missing_attrs : false;
        $GLOBALS['iwptp_table_instance']->attribute_required = $required_attrs;
    }

    // filter
    if (!empty($sc_attrs['filter_required'])) {
        $permit = false;

        foreach (iwptp_sanitize_array($_GET) as $key => $val) {
            if (($key ===  $table_data['id'] . '_product_cat' || false !== strpos($key, $table_data['id'] . '_attr_') || false !== strpos($key, $table_data['id'] . '_search_')) && !empty($val)) {
                $permit = true;
                break;
            }
        }

        if (!$permit) {
            $GLOBALS['iwptp_table_instance']->filter_required_but_missing = true;
        }
    }

    return $query_args;
}

// -- empty out results
add_filter('iwptp_products', 'iwptp_filter_required__maybe_empty_results', 10, 1);
function iwptp_filter_required__maybe_empty_results($products)
{
    if (
        !empty($GLOBALS['iwptp_table_instance']->category_required_but_missing) ||
        !empty($GLOBALS['iwptp_table_instance']->attribute_required_but_missing) ||
        !empty($GLOBALS['iwptp_table_instance']->filter_required_but_missing)
    ) {
        $products->posts = null;
        $products->post_count = 0;
    }

    return $products;
}

// -- print no results message
add_filter('iwptp_no_results', 'iwptp_category_or_attribute_missing_message', 10, 1);
function iwptp_category_or_attribute_missing_message($markup)
{
    $table_data = iwptp_get_table_data();
    $sc_attrs = $table_data['query']['sc_attrs'];
    $table_id = $table_data['id'];

    if (
        !empty($GLOBALS['iwptp_table_instance']->category_required_but_missing) ||
        !empty($GLOBALS['iwptp_table_instance']->attribute_required_but_missing) ||
        !empty($GLOBALS['iwptp_table_instance']->filter_required_but_missing)
    ) {

        // category required message
        $category_required_message = "Select a category!";

        if (!empty($sc_attrs['category_required_message'])) {
            $category_required_message = iwptp_get_translation($sc_attrs['category_required_message']);
        }

        // attribute required message
        $attribute_required_message = "Select {attribute}!";

        if (!empty($sc_attrs['attribute_required_message'])) {
            $attribute_required_message = iwptp_get_translation($sc_attrs['attribute_required_message']);
        }

        // filter required message
        $filter_required_message = "Please use a table filter to show results!";

        if (!empty($sc_attrs['filter_required_message'])) {
            $filter_required_message = $sc_attrs['filter_required_message'];
        }

        $device = !empty($_GET[$table_id . '_device']) ? sanitize_text_field($_GET[intval($table_id) . '_device']) : 'laptop';
        $device_laptop_html_class = '';
        if ($device === 'laptop') {
            $device_laptop_html_class = 'iwptp-device-laptop';
        }

        ob_start();
        ?>
        <div class="iwptp-no-results <?php echo esc_attr($device_laptop_html_class); ?>">
            <div class="iwptp-required-but-missing-nav-filter-message" data-iwptp-device="<?php echo esc_attr($device); ?>">

                <?php if (!empty($table_data['query']['sc_attrs']['category_required'])) : ?>
                    <div class="<?php echo empty($GLOBALS['iwptp_table_instance']->category_required_but_missing) ? "" : "iwptp-missing"; ?>">
                        <?php echo wp_kses(iwptp_icon('check'), iwptp_allowed_html_tags()); ?>
                        <?php echo wp_kses(iwptp_icon('alert-circle'), iwptp_allowed_html_tags()); ?>
                        <span>
                            <?php echo wp_kses($category_required_message, iwptp_allowed_html_tags()); ?>
                        </span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($table_data['query']['sc_attrs']['attribute_required'])) : ?>
                    <?php foreach ($GLOBALS['iwptp_table_instance']->attribute_required as $taxonomy) : ?>
                        <div class="<?php echo !in_array($taxonomy, $GLOBALS['iwptp_table_instance']->attribute_required_but_missing) ? "" : "iwptp-missing"; ?>">
                            <?php echo wp_kses(iwptp_icon('check'), iwptp_allowed_html_tags()); ?>
                            <?php echo wp_kses(iwptp_icon('alert-circle'), iwptp_allowed_html_tags()); ?>
                            <?php
                            $id = wc_attribute_taxonomy_id_by_name($taxonomy);
                            $attribute = wc_get_attribute($id);
                            ?>
                            <span>
                                <?php echo wp_kses(str_replace('{attribute}', $attribute->name, $attribute_required_message), iwptp_allowed_html_tags()); ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <?php if (!empty($table_data['query']['sc_attrs']['filter_required'])) : ?>
                    <div class="<?php echo empty($GLOBALS['iwptp_table_instance']->filter_required_but_missing) ? "" : "iwptp-missing"; ?>">
                        <?php echo wp_kses(iwptp_icon('check'), iwptp_allowed_html_tags()); ?>
                        <?php echo wp_kses(iwptp_icon('alert-circle'), iwptp_allowed_html_tags()); ?>
                        <span>
                            <?php echo wp_kses($filter_required_message, iwptp_allowed_html_tags()); ?>
                        </span>
                    </div>
                <?php endif; ?>

            </div>
        </div>
        <?php
        echo "<style>#iwptp-".esc_attr($table_data['id'])." .iwptp-result-count{ display: none !important; }</style>";

        return ob_get_clean();
    } else {
        return $markup;
    }
}

// parse general placeholders (in Text and HTML elements)
function iwptp_general_placeholders__parse__pro($str, $source = false)
{
    global $product;

    if (!$product) {
        return $str;
    }

    $parent = false;
    if ($product->get_type() == 'variation') {
        $parent = wc_get_product($product->get_parent_id());
    }

    $arr = [
        '[id]' => $product->get_id(),
        '[product_id]' => $product->get_id(),
        '[parent_id]' => $parent ? $parent->get_id() :  $product->get_id(),
        '[variation_id]' => $parent ? $product->get_id() :  '',
        '[site_url]' => rtrim((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]", '/'),
        '[page_url]' => rtrim((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]", '/'),
        '[url]' => rtrim($product->get_permalink(), '/'),
        '[product_url]' => rtrim($product->get_permalink(), '/'),
        '[parent_url]'  => rtrim(($parent ? $parent->get_permalink() : $product->get_permalink()), '/'),
        '[slug]' => $product->get_slug(),
        '[product_slug]' => $product->get_slug(),
        '[parent_slug]' => $parent ? $parent->get_slug() : $product->get_slug(),
        '[sku]' => $product->get_sku(),
        '[product_sku]' => $product->get_sku(),
        '[parent_sku]' => $parent ? $parent->get_sku() : $product->get_sku(),
        '[name]' => $product->get_title(),
        '[product_name]' => $product->get_title(),
        '[parent_name]' => $parent ? $parent->get_title() : $product->get_title(),
        '[menu_order]' => $product->get_menu_order(),
        '[product_menu_order]' => $product->get_menu_order(),
        '[parent_menu_order]' => $parent ? $parent->get_menu_order() : $product->get_menu_order(),
        '[title]' => $product->get_title(),
    ];

    if ($source == 'shortcode') {
        $_arr = $arr;
        foreach ($arr as $key => $val) {
            $_key = str_replace(array('[', ']'), array('%', '%'), $key);
            $_arr[$_key] = $val;
        }

        $arr = $_arr;
    }

    $search = array_keys($arr);
    $replace = array_values($arr);

    // custom field and attribute
    $pattern = '/[\[](.*?)[\]]/';
    if ($source == 'shortcode') {
        $pattern = '/[%](.*?)[%]/';
    }
    $matches = [];
    preg_match_all($pattern, $str, $matches);

    if (!empty($matches[1])) {
        foreach ($matches[1] as $key => $val) {
            $split = array_map('trim', explode(':', strtolower($val)));
            if (count($split) == 2) {
                $type = $split[0];
                $name = $split[1];

                $id = $product->get_id();

                if ($type == 'custom_field') {
                    $field_val = get_post_meta($id, $name, true);

                    if (!$field_val && $parent) {
                        $field_val = get_post_meta($parent->get_id(), $name, true);
                    }

                    $search[] = $matches[0][$key];
                    $replace[] = $field_val;
                } else if ($type == 'attribute') {

                    if ($parent) {
                        $name = get_post_meta($id, 'attribute_pa_' . strtolower($name), true);

                        $search[] = $matches[0][$key];
                        $replace[] = $name;
                    } else {
                        $terms = get_terms('pa_' . $name, array(
                            'hide_empty' => false,
                            'object_ids' => $id,
                        ));

                        $names = '';

                        if (!is_wp_error($terms)) {

                            foreach ($terms as $term) {
                                $names .= str_replace('&amp;', '&', $term->name) . ', ';
                            }

                            $names = rtrim($names, ', ');
                        }

                        $search[] = $matches[0][$key];
                        $replace[] = $names;
                    }
                }
            }
        }
    }

    $str = str_replace($search, $replace, $str);

    return $str;
}

// attribute relation
add_filter('iwptp_query_args', 'iwptp_pro_attribute_relation', 20, 1);
function iwptp_pro_attribute_relation($query_args = [])
{
    $data = iwptp_get_table_data();
    $sc_attrs = $data['query']['sc_attrs'];

    if (!empty($sc_attrs) && !empty($sc_attrs['attribute_relation']) && in_array(strtoupper($sc_attrs['attribute_relation']), array('OR', 'AND'))) {
        $tax_query = [];
        $tax_query_attributes = array(
            'relation' => strtoupper($sc_attrs['attribute_relation']),
        );

        foreach ($query_args['tax_query'] as $arr) {
            $is_attribute = !empty($arr['taxonomy']) && 'pa_' == substr($arr['taxonomy'], 0, 3);

            if ($is_attribute) {
                $tax_query_attributes[] = $arr;
            } else {
                $tax_query[] = $arr;
            }
        }

        $query_args['tax_query'] = $tax_query;
        $query_args['tax_query']['iwptp_attributes__or'] = $tax_query_attributes;
    }

    return $query_args;
}

// featured
add_filter('iwptp_query_args', 'iwptp_pro_featured', 20, 1);
function iwptp_pro_featured($query_args = [])
{
    $data = iwptp_get_table_data();
    $sc_attrs = $data['query']['sc_attrs'];

    if (!empty($sc_attrs) && !empty($sc_attrs['featured'])) {
        $query_args['tax_query'][] = array(
            'taxonomy' => 'product_visibility',
            'field'    => 'name',
            'terms'    => 'featured',
            'operator' => 'IN',
        );
    }

    return $query_args;
}

/* Include modules */
$iwptp_modules = array(
    IWPTPL_PLUGIN_PATH . 'archive_override.php',
    IWPTPL_PLUGIN_PATH . 'dynamic_recount.php',
    IWPTPL_PLUGIN_PATH . 'import_export.php',
);

foreach ($iwptp_modules as $module) {
    if (file_exists($module)) {
        require_once($module);
    }
}

add_action('iwptp_before_apply_user_filters', 'iwptp_store_query_args_before_user_filters', 1000);
function iwptp_store_query_args_before_user_filters()
{
    $original = $GLOBALS['iwptp_table_instance']->query_args;
    $GLOBALS['iwptp_query_args_before_user_filters'] = $GLOBALS['iwptp_table_instance']->parse_query_args();
    $GLOBALS['iwptp_table_instance']->query_args = $original;

    WC()->query->remove_ordering_args();
}

// convert ACF choices into options for the custom field filter
function iwptp_generate_custom_field_options_from_acf_choices($acf_choices)
{
    $options = [];

    foreach (preg_split('/\r\n|[\r\n]/', $acf_choices) as $choice) {
        list($value, $label) = array_map('trim', explode(' : ', $choice));

        if (empty($label)) {
            $label = $value;
        }

        $options[] = array(
            'label' => $label,
            'value' => $value
        );
    }

    return $options;
}

// auto generate values for the custom field filter
function iwptp_auto_generate_custom_field_options($field_name, $type, $order)
{
    $data = iwptp_get_table_data();

    if (!in_array($order, array('ASC', 'DESC'))) {
        $order = 'ASC';
    }

    if (!in_array($type, array('CHAR', 'NUMERIC'))) {
        $type = 'CHAR';
    }

    $data = iwptp_get_table_data();
    $args = array_merge(
        $GLOBALS['iwptp_query_args_before_user_filters'],
        array(
            'posts_per_page' => -1,
            'paged' => 0
        )
    );
    $results = new WP_Query($args);
    $posts = $results->posts;

    if (!count($posts)) {
        return [];
    }

    $orderby_clause = "ORDER BY meta_value $order";
    if ($type == 'NUMERIC') {
        $orderby_clause = "ORDER BY (meta_value+0) $order";
    }

    global $wpdb;
    $meta_values = $wpdb->get_col($wpdb->prepare(
        "
      SELECT DISTINCT meta_value
      FROM {$wpdb->prefix}postmeta 
      WHERE meta_key= %s
      AND post_id IN (" . implode(',', $posts) . ")" . //phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared  
      $orderby_clause, //phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared  
        $field_name
    ));
    $arr = [];
    foreach (array_map('trim', $meta_values) as $meta_value) {
        if (!trim($meta_value)) {
            continue;
        }

        $arr[] = array(
            'label' => $meta_value,
            'value' => $meta_value
        );
    }

    return $arr;
}

// variable price template
add_action('iwptp_container_close', 'iwptp_print_price_template');
function iwptp_print_price_template()
{
    $data = iwptp_get_table_data();
    $table_id = $data['id'];

    if (empty($GLOBALS['iwptp_' . $table_id . '_price_templates'])) {
        return;
    }

    foreach ($GLOBALS['iwptp_' . $table_id . '_price_templates'] as $id => $templates) {
        foreach ($templates as $type => $template) {
        ?>
            <script type="text/template" data-iwptp-element-id="<?php echo esc_attr($id); ?>" data-iwptp-price-type="<?php echo esc_attr($type); ?>">
                <?php echo wp_kses($template, iwptp_allowed_html_tags()); ?>
      </script>
        <?php
        }
    }

    unset($GLOBALS['iwptp_' . $table_id . '_price_templates']);
}

// variable availability template
add_action('iwptp_container_close', 'iwptp_print_availability_template');
function iwptp_print_availability_template()
{
    $data = iwptp_get_table_data();
    $table_id = $data['id'];

    if (empty($GLOBALS['iwptp_' . $table_id . '_availability_templates'])) {
        return;
    }

    foreach ($GLOBALS['iwptp_' . $table_id . '_availability_templates'] as $id => $templates) {
        foreach ($templates as $type => $template) {
        ?>
            <script type="text/template" data-iwptp-element-id="<?php echo esc_attr($id); ?>" data-iwptp-availability-message="<?php echo esc_attr($type); ?>">
                <?php echo wp_kses($template, iwptp_allowed_html_tags()); ?>
      </script>
    <?php
        }
    }

    unset($GLOBALS['iwptp_' . $table_id . '_availability_templates']);
}

// supplement variation information
add_filter('woocommerce_available_variation', 'iwptp__woocommerce_available_variation', 100, 3);
function iwptp__woocommerce_available_variation($props, $_class, $variation)
{
    $props['stock'] = $variation->get_stock_quantity();
    $props['managing_stock'] = $variation->managing_stock();
    $props['is_on_backorder'] = $variation->is_on_backorder(1);
    $props['backorders_require_notification'] = $variation->backorders_require_notification();
    return $props;
}

// custom field variable switch
add_action('iwptp_container_close', 'iwptp_print_variable_switch_cf');
function iwptp_print_variable_switch_cf()
{
    $table_data = iwptp_get_table_data();
    $table_id = $table_data['id'];

    $arr_key = 'iwptp_' . $table_id . '_variable_switch_cf';

    if (empty($GLOBALS[$arr_key])) {
        return;
    }

    ?>
    <script>
        var <?php echo esc_attr($arr_key); ?> = <?php echo wp_kses(wp_json_encode($GLOBALS[$arr_key]), iwptp_allowed_html_tags()); ?>
    </script>
<?php

    unset($GLOBALS[$arr_key]);
}

// search enabled
add_filter('iwptp_element', 'iwptp_search_enabled__add_html_class');
function iwptp_search_enabled__add_html_class($elm)
{
    if (
        in_array(
            $elm['type'],
            array(
                'category_filter',
                'attribute_filter',
                'taxonomy_filter',
                'tags_filter'
            )
        ) &&
        !empty($elm['search_enabled'])
    ) {
        if (empty($elm['html_class'])) {
            $elm['html_class'] = '';
        }
        $elm['html_class'] .= ' iwptp-filter--search-filter-options-enabled ';
    }

    return $elm;
}

// reset table cart form after adding to cart
// else the same $_POST attribute value is used by every cart form in table 
add_action('iwptp_before_loop', 'iwptp_start_resetting_cart_forms');
add_action('iwptp_container_close', 'iwptp_stop_resetting_cart_forms');

function iwptp_start_resetting_cart_forms()
{
    add_filter('woocommerce_dropdown_variation_attribute_options_args', 'iwptp_reset_cart_form', 100, 1);
}

function iwptp_stop_resetting_cart_forms()
{
    remove_filter('woocommerce_dropdown_variation_attribute_options_args', 'iwptp_reset_cart_form', 100, 1);
}

function iwptp_reset_cart_form($args)
{
    global $product;
    if (method_exists($product, 'get_variation_default_attribute')) {
        $args['selected'] = $product->get_variation_default_attribute($args['attribute']);
    }
    return $args;
}

// include private
add_filter('iwptp_query_args', 'iwptp__query_args__include_private');
function iwptp__query_args__include_private($query_args)
{
    $table_data = iwptp_get_table_data();

    if (!empty($table_data['query']['sc_attrs']['include_private'])) {
        if (empty($query_args['post_status'])) {
            $query_args['post_status'] = array('publish');
        }

        if (!is_array($query_args['post_status'])) {
            $query_args['post_status'] = array_map('trim', explode(',', $query_args['post_status']));
        }

        if (!in_array('private', $query_args['post_status'])) {
            $query_args['post_status'][] = 'private';
        }

        add_filter('the_title', 'iwptp_remove_private_prefix');
        add_action('iwptp_container_close', 'iwptp__cancel_filter__remove_private_prefix');
    }

    return $query_args;
}

function iwptp_remove_private_prefix($title)
{
    return str_replace('Private: ', '', $title);
}

function iwptp__cancel_filter__remove_private_prefix()
{
    remove_filter('the_title', 'iwptp_remove_private_prefix');
}

// variation description
add_shortcode('iwptp_variation_description', 'iwptp_variation_description');
function iwptp_variation_description($atts)
{
    global $product;
    if ($product->get_type() !== 'variable') {
        return;
    }

    $atts = shortcode_atts(array(
        'width' => '',
        'max-width' => '',
        'color' => '',
        'font-size' => '',
        'html_class' => '',
    ), $atts);

    $inner = '';
    foreach (iwptp_get_variations($product) as $variation) {
        $inner .= '<div class="iwptp-variation-description__item" data-iwptp-variation-id="' . $variation['variation_id'] . '">' . $variation['variation_description'] . '</div>';
    }

    $style = '';
    foreach ($atts as $key => $val) {
        if (
            $val &&
            $key !== 'html_class'
        ) {
            $style .= $key . ':' . $val . ';';
        }
    }

    if (!$atts['width']) {
        $atts['html_class'] .= ' iwptp-variation-content--max-width ';
    }

    echo '<div class="iwptp-variation-description ' . esc_attr($atts['html_class']) . '" style="' . wp_kses($style, iwptp_allowed_html_tags()) . '">' . wp_kses($inner, iwptp_allowed_html_tags()) . '</div>';
}

// audio player
add_shortcode('iwptp_player', 'iwptp_player');
function iwptp_player($atts)
{
    $atts = shortcode_atts(array(
        'src' => '',
        'disable_loop' => false,
    ), $atts, 'iwptp_player');

    global $post;
    $src = get_post_meta($post->ID, $atts['src'], true);

    if (!$src) {
        return;
    }

    if (is_numeric($src)) {
        $src = wp_get_attachment_url($src);
    }

    ob_start();
?>
    <div class="iwptp-player" data-iwptp-src="<?php echo esc_attr($src); ?>" data-iwptp-loop="<?php if (!$atts['disable_loop']) echo "true"; ?>">
        <div class="iwptp-player__play-button iwptp-player__button">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-play">
                <polygon points="5 3 19 12 5 21 5 3" />
            </svg>
        </div>
        <div class="iwptp-player__pause-button iwptp-player__button">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-pause">
                <rect x="6" y="4" width="4" height="16" />
                <rect x="14" y="4" width="4" height="16" />
            </svg>
        </div>
    </div>
<?php
    return ob_get_clean();
}

// remove from cart
add_shortcode('iwptp_remove', 'iwptp_remove');
function iwptp_remove($atts)
{
    $atts = shortcode_atts(array(
        'style' => ''
    ), $atts, 'iwptp_remove');
    $html_class = 'iwptp-remove';
    global $product;
    $in_cart = false;
    if (
        WC() &&
        WC()->cart &&
        WC()->cart->cart_contents
    ) {
        foreach (WC()->cart->cart_contents as $key => $item) {
            if (
                $item['product_id'] == $product->get_id() ||
                ($product->get_type() == 'variation' &&
                    $item['variation_id'] == $product->get_id()
                )
            ) {
                $in_cart = true;
            }
        }
    }
    if (!$in_cart) {
        $html_class .= ' iwptp-disabled';
    }
   
   return iwptp_icon('x', $html_class, $atts['style'], null, __('Remove'));
}

add_action('wp_ajax_iwptp_remove_product', 'iwptp_remove_product');
add_action('wp_ajax_nopriv_iwptp_remove_product', 'iwptp_remove_product');
function iwptp_remove_product()
{
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'iwptp_ajax_nonce')) {
        die();
    }

    $cart = WC()->instance()->cart;

    $remove = [];
    foreach ($cart->get_cart_contents() as $key => $item) {
        if ($item['product_id'] == $_POST['product_id']) {
            if (
                !empty($_POST['variation_id']) &&
                !empty($item['variation_id']) &&
                (int) $_POST['variation_id'] !== (int) $item['variation_id']
            ) {
                continue;
            }
            $remove[] = $key;
        }
    }

    foreach ($remove as $key) {
        $cart->set_quantity($key, 0);
    }

    ob_start();
    woocommerce_mini_cart();
    $mini_cart = ob_get_clean();

    $data = array(
        'success' => true,
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

// in cart
add_shortcode('iwptp_in_cart', 'iwptp_in_cart');
function iwptp_in_cart($atts)
{

    if (!$atts) {
        $atts = [];
    }

    if (empty($atts['template'])) {
        $atts['template'] = '{n}';
    }

    global $product;
    $qty = 0;

    if (
        !WC()->cart ||
        !$product
    ) {
        return;
    }

    foreach (WC()->cart->get_cart() as $item => $values) {
        if (
            ($product->get_type() === 'simple' &&
                $product->get_id() === $values['product_id']
            ) ||
            ($product->get_type() === 'variation' &&
                $product->get_id() === $values['variation_id']
            )
        ) {
            $qty = $values['quantity'];
            break;
        }

        if (
            $product->get_type() === 'variable' &&
            $product->get_id() === $values['product_id']
        ) {
            $qty += $values['quantity'];
        }
    }

    $hide = '';
    if (!$qty) {
        $hide = 'iwptp-disabled';
    }

    return '<span class="iwptp-in-cart ' . $hide . '" data-iwptp-template="' . esc_attr($atts['template']) . '">' . str_replace('{n}', $qty, $atts['template']) . '</span>';
}

// override search 'no results'
add_action('woocommerce_before_main_content', 'iwptp_search_override');
function iwptp_search_override()
{
    global $wp_query;
    if (iwptp_maybe_override_search()) {
        if (!$wp_query->post_count) {
            $wp_query->post_count = 1;
        }
    }
}

function iwptp_maybe_override_search()
{
    if (!is_search()) {
        return false;
    }

    return iwptp_search_override_enabled();
}

function iwptp_search_override_enabled()
{
    $iwptp_settings = iwptp_get_settings_data();

    if (!$ao_settings = $iwptp_settings['archive_override']) {
        return false;
    }

    // no value
    if (!$search = $ao_settings['search']) {
        return false;
    }

    // custom but no value
    if (
        $search == 'custom' &&
        empty($ao_settings['search_custom'])
    ) {
        return false;
    }

    // default but no value
    if (
        $search == 'default' &&
        empty($ao_settings['default'])
    ) {
        return false;
    }

    return true;
}

// sequence
add_shortcode('iwptp_sequence', 'iwptp_sequence__shortcode');
function iwptp_sequence__shortcode($atts)
{
    global $product;
    $table_data = iwptp_get_table_data();
    $id = $table_data['id'];

    $seq_count_arr = &$GLOBALS['iwptp_' . $id . '_sequence_count_arr'];

    if (!in_array($product->get_id(), $seq_count_arr)) {
        $seq_count_arr[] = $product->get_id();
    }

    $limit = $table_data['query']['limit'] ? $table_data['query']['limit'] : 10;
    $page = empty($_GET[$id . '_paged']) ? 1 : intval($_GET[$id . '_paged']);

    $seq_count = count($seq_count_arr) + ($limit * ($page - 1));

    if (!empty($atts['min_digits'])) {
        $seq_count = str_pad($seq_count, $atts['min_digits'], '0', STR_PAD_LEFT);
    }

    if (!empty($atts['template'])) {
        $seq_count = str_replace('{n}', $seq_count, $atts['template']);
    }

    return '<span class="iwptp-sequence-number">' . $seq_count . '</span>';
}

add_action('iwptp_before_loop', 'iwptp_sequence_count_clear');
function iwptp_sequence_count_clear()
{
    $table_data = iwptp_get_table_data();
    $id = $table_data['id'];
    $GLOBALS['iwptp_' . $id . '_sequence_count_arr'] = [];
}

// search shortcode
add_shortcode('iwptp_search', 'iwptp_search_shortcode');
function iwptp_search_shortcode($atts = [])
{

    if (!class_exists('WooCommerce')) {
        return;
    }

    $value = !empty($_REQUEST['s']) ? sanitize_text_field($_REQUEST['s']) : '';

    $clear_redirect = 'shop';
    if (
        !empty($atts['clear_redirect']) &&
        in_array($atts['clear_redirect'], array('shop', 'home', 'category'))
    ) {
        $clear_redirect = $atts['clear_redirect'];
    }

    $clear_redirect_url = '';
    if ($clear_redirect == 'shop') {
        $clear_redirect_url = get_permalink(wc_get_page_id('shop'));
    } else if ($clear_redirect == 'home') {
        $clear_redirect_url = get_home_url();
    } else if ($clear_redirect == 'category') {
        global $wp;
        $get = iwptp_sanitize_array($_GET);
        if (isset($get['s'])) {
            unset($get['s']);
        }
        $clear_redirect_url = home_url(add_query_arg($get, $wp->request));
    }

    $redirect = 'search';
    if (
        !empty($atts['redirect']) &&
        in_array(strtolower($atts['redirect']), array('category', 'search'))
    ) {
        $redirect = strtolower($atts['redirect']);

        if (
            $redirect == 'category' &&
            !empty($atts['disable_category'])
        ) {
            $redirect = 'search';
        }
    }

    $action = get_home_url();

    $placeholder = __('Search', 'woocommerce');
    if (!empty($atts['placeholder'])) {
        $placeholder = $atts['placeholder'];
    }

    $locale = strtolower(get_locale());
    if (!empty($atts['placeholder_' . $locale])) {
        $placeholder = $atts['placeholder_' . $locale];
    }

    $exclude = '';
    if (!empty($atts['exclude'])) {
        $exclude_slugs = array_map('trim', explode(',', $atts['exclude']));

        $tt_ids = get_terms(array(
            'taxonomy' => 'product_cat',
            'slug' => $exclude_slugs,
            'fields' => 'tt_ids',
        ));

        if ($tt_ids) {
            $exclude = implode(',', $tt_ids);
        }
    }

    $include = '';
    if (!empty($atts['include'])) {
        $include_slugs = array_map('trim', explode(',', $atts['include']));

        $tt_ids = get_terms(array(
            'taxonomy' => 'product_cat',
            'slug' => $include_slugs,
            'fields' => 'tt_ids',
        ));

        if ($tt_ids) {
            $include = implode(',', $tt_ids);
        }
    }

    if (empty($atts['max_width'])) {
        $max_width = "100%";
    } else {
        $max_width = (float) $atts['max_width'] . "px";
    }

    if (empty($atts['width'])) {
        $width = "auto";
    } else {
        if (strpos($atts['width'], '%') !== false) {
            $width = $atts['width'];
        } else {
            $width = (float) $atts['width'] . "px";
        }
    }

    $all_label = __('All', 'woocommerce');
    if (!empty($atts['all_label'])) {
        $all_label = $atts['all_label'];
    }

    if (!empty($atts['all_label_' . $locale])) {
        $all_label = $atts['all_label_' . $locale];
    }

    $submit_label = !empty($atts['submit_label']) ? $atts['submit_label'] : false;
    if (!empty($atts['submit_label_' . $locale])) {
        $submit_label = $atts['submit_label_' . $locale];
    }

    if (
        !empty($atts['hide_search_icon']) &&
        in_array(strtolower(trim($atts['hide_search_icon'])), array('false', '0', 'no'))
    ) {
        $atts['hide_search_icon'] = false;
    }
    $hide_search_icon = !empty($atts['hide_search_icon']);

    $required = false;
    if (
        !empty($atts['category_required']) &&
        in_array(trim(strtolower($atts['category_required'])), array(
            'yes',
            'true',
            '1',
            'required'
        ))
    ) {
        $required = true;
    }

    ob_start();
?>
    <div class="iwptp-global-search" style="max-width: <?php echo esc_attr($max_width) ?>; width: <?php echo esc_attr($width); ?>;">
        <form class="iwptp-global-search__form" action="<?php echo esc_attr($action); ?>" data-iwptp-clear-redirect-url="<?php echo esc_attr($clear_redirect_url); ?>" data-iwptp-clear-redirect="<?php echo esc_attr($clear_redirect); ?>" data-iwptp-redirect="<?php echo esc_attr($redirect); ?>">
            <?php
            foreach (array('lang') as $param) {
                if (!empty($_GET[$param])) {
            ?>
                    <input type="hidden" name="lang" value="<?php echo esc_attr($_GET[$param]); ?>" />
            <?php
                }
            }
            ?>
            <?php if (empty($atts['disable_category'])) : ?>
                <!-- <input type="hidden" name="taxonomy" value="product_cat" /> -->
                <div class="iwptp-global-search__category-selector-wrapper">

                    <?php
                    $categories = get_terms(array(
                        'taxonomy'  => 'product_cat',
                        // 'name'      => 'term',
                        'hide_empty' => true,
                        'orderby'   => 'menu_order',
                        'exclude'   => $exclude,
                        'include'   => $include,
                    ));

                    $links = [];

                    foreach ($categories as $term) {
                        $links[$term->slug] = get_term_link($term->term_id);
                    }
                    ?>
                    <script>
                        var iwptp_product_category_links = <?php echo wp_json_encode($links); ?>
                    </script>
                    <?php
                    $selected = '';
                    if (!empty(get_query_var('term'))) {
                        $selected = get_query_var('term');
                    } else if (!empty(get_query_var('product_cat'))) {
                        $selected = get_query_var('product_cat');
                    } else if (!empty($_GET['term'])) {
                        $selected = sanitize_text_field($_GET['term']);
                    }

                    wc_product_dropdown_categories(array(
                        'show_count'         => 0,
                        'hide_empty'         => 1,
                        'show_uncategorized' => 0,
                        'orderby'            => 'menu_order',
                        'selected'           => $selected,
                        'show_option_none'   => __('All', 'woocommerce'),
                        'option_none_value'  => '',
                        // 'value_field'        => 'slug',
                        'taxonomy'           => 'product_cat',
                        'name'               => 'term',
                        'class'              => 'iwptp-global-search__category-selector',
                        'exclude'            => $exclude,
                        'include'            => $include,
                        'required'           => $required,
                    ));
                    ?>
                    <div class="iwptp-global-search__category-selector-facade">
                        <span class="iwptp-global-search__category-selector-facade__text">
                            <?php
                            if (
                                $selected &&
                                $selected_term = get_term_by('slug', $selected, 'product_cat')
                            ) {
                                echo esc_html($selected_term->name);
                            } else {
                                echo wp_kses($all_label, iwptp_allowed_html_tags());
                            }
                            ?>
                        </span>
                        <?php echo wp_kses(iwptp_icon('chevron-down'), iwptp_allowed_html_tags()); ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="iwptp-global-search__keyword-input-wrapper">
                <input class="iwptp-global-search__keyword-input" type="search" placeholder="<?php echo esc_attr($placeholder); ?>" name="s" autocomplete="off" spellcheck="false" value="<?php echo wp_kses(htmlentities(stripslashes($value)), iwptp_allowed_html_tags()); ?>" />
                <?php echo wp_kses(iwptp_icon('x', 'iwptp-global-search__clear'), iwptp_allowed_html_tags()); ?>
            </div>
            <div class="iwptp-global-search__submit-wrapper <?php echo $hide_search_icon && !$submit_label ? "iwptp-hide" : ""; ?>">
                <!-- hidden submit input -->
                <input type="submit" class="iwptp-global-search__submit" value="" />

                <!-- search submit icon -->
                <?php if (!$hide_search_icon) : ?>
                    <?php echo wp_kses(iwptp_icon('search', 'iwptp-global-search__submit-icon'), iwptp_allowed_html_tags()); ?>
                <?php endif; ?>

                <!-- search submit text -->
                <?php if ($submit_label) : ?>
                    <span class="iwptp-global-search__submit-text"><?php echo wp_kses($submit_label, iwptp_allowed_html_tags()); ?></span>
                <?php endif; ?>

            </div>
            <input type="hidden" name="post_type" value="product" />
            <?php
            if (class_exists('DGWT_WC_Ajax_Search')) { // plugin: AJAX Search for WC
                echo '<input type="hidden" name="dgwt_wcas" value="1" />';
            }
            ?>
        </form>
    </div>
<?php
    return ob_get_clean();
}

add_filter('iwptp_shortcode_attributes', 'iwptp_search__category_shortcode_attribute', 100, 1);
function iwptp_search__category_shortcode_attribute($atts)
{
    if (
        !empty($_GET['iwptp_search_category']) &&
        !empty($atts['_archive'])
    ) {
        $atts['category'] = sanitize_text_field($_GET['iwptp_search_category']);
    }

    return $atts;
}

// total
add_shortcode('iwptp_total', 'iwptp_shortcode__total');
function iwptp_shortcode__total($atts = [])
{
    global $product;

    if (!empty($atts['include_total_in_cart'])) {
        $include_total_in_cart = $atts['include_total_in_cart'];
    }

    if (!empty($atts['include_amount_in_cart'])) {
        $include_total_in_cart = $atts['include_amount_in_cart'];
    }

    $html_class = "";

    ob_start();
    include('templates/total.php');

    return ob_get_clean();
}

// gallery
add_shortcode('iwptp_gallery_strip', 'iwptp_gallery');
add_shortcode('iwptp_gallery', 'iwptp_gallery');
function iwptp_gallery($atts)
{
    extract(shortcode_atts(array(
        'max_images'        => 3,
        'image_width'       => 40,
        'see_more_label'    => '+{n} more',
        'include_featured'  => false,
    ), $atts));

    global $product;
    $html_class = "";

    ob_start();
    include('templates/gallery.php');
    return ob_get_clean();
}

// print product table via [products] shortcode
// helps with XForWooCommerce and other plugin integration that use [products]
// you can make IWPTPL show results in table
add_filter('shortcode_atts_products', 'iwptp_products_sc__atts', 100, 4);
function iwptp_products_sc__atts($out, $pairs, $atts, $shortcode)
{
    if (
        !empty($atts['product_table_id'])
    ) {
        $out['product_table_id'] = (int) $atts['product_table_id'];
        $rand_class = "iwptp-hide-products-sc-" . wp_rand();
        $out['class'] .= " $rand_class ";
        $out['iwptp_hide_class'] = $rand_class;

        add_filter('woocommerce_shortcode_products_query_results', 'iwptp_products_sc__get_query_results');
        add_filter('iwptp_products', 'iwptp_products_sc__copy_results');
        add_action('woocommerce_shortcode_before_products_loop', 'iwptp_products_sc__before');
        add_action('woocommerce_shortcode_after_products_loop', 'iwptp_products_sc__after');
    }
    return $out;
}

function iwptp_products_sc__before($atts)
{
    remove_action('woocommerce_shortcode_before_products_loop', 'iwptp_products_sc__before');
?>
    <style>
        .<?php echo esc_attr($atts["iwptp_hide_class"]); ?>.products,
        .<?php echo esc_attr($atts["iwptp_hide_class"]); ?>.products+.woocommerce-pagination {
            display: none !important;
        }
    </style>
<?php
}

function iwptp_products_sc__get_query_results($results)
{
    remove_filter('woocommerce_shortcode_products_query_results', 'iwptp_products_sc__get_query_results');
    $GLOBALS['iwptp_products_shortcode_results'] = $results;
    return $results;
}

function iwptp_products_sc__copy_results($products)
{
    remove_filter('iwptp_products', 'iwptp_products_sc__copy_results');
    $results = &$GLOBALS['iwptp_products_shortcode_results'];
    $products->posts = $results->ids;
    return $products;
}

function iwptp_products_sc__after($atts)
{
    remove_action('woocommerce_shortcode_after_products_loop', 'iwptp_products_sc__after');

    $results = &$GLOBALS['iwptp_products_shortcode_results'];
    $ids = implode(',', $results->ids);
    echo do_shortcode("[it_product_table id='{$atts['product_table_id']}' ids='$ids' limit='-1' _disable_nav='true']");

    if (!empty($atts['pagination'])) { // xforwoocommerce compatibility
        wc_get_template('loop/pagination.php', array(
            'total'   => $results->total_pages,
            'current' => $results->current_page,
            'base'    => esc_url_raw(add_query_arg('product-page', '%#%', false)),
            'base'    => esc_url_raw(str_replace(999999999, '%#%', remove_query_arg('add-to-cart', get_pagenum_link(999999999, false)))),
            'format'  => '',
        ));
    } else {
        wc_get_template('loop/pagination.php', array(
            'total'   => $results->total_pages,
            'current' => $results->current_page,
            'base'    => esc_url_raw(add_query_arg('product-page', '%#%', false)),
            'format'  => '?product-page=%#%',
        ));
    }

    unset($results);
}

// variation count
add_shortcode('iwptp_variation_count', 'iwptp_variation_count');
function iwptp_variation_count($atts)
{
    global $product;
    if ($product->get_type() !== 'variable') {
        return;
    }

    $template = isset($atts['template']) ? $atts['template'] : '({n})';

    if ($count = count($product->get_children())) {
        echo wp_kses(str_replace('{n}', $count, $template), iwptp_allowed_html_tags());
    }
}

// previously ordered
add_filter('iwptp_query_args', 'iwptp__query_args__grouped_product');
function iwptp__query_args__grouped_product($query_args)
{
    $table_data = iwptp_get_table_data();
    $sc_attrs = $table_data['query']['sc_attrs'];

    if (empty($sc_attrs['grouped_product_ids'])) {
        return $query_args;
    }

    $grouped_product_ids = array_map('trim', explode(',', $sc_attrs['grouped_product_ids']));

    $children_ids = [];
    if ($grouped_product_ids) {
        foreach ($grouped_product_ids as $id) {
            $product = wc_get_product($id);
            $children_ids = array_merge($children_ids, $product->get_children());
        }
    }

    if ($children_ids) {
        if (empty($query_args['post__in'])) {
            $query_args['post__in'] = [];
        }

        if (count($query_args['post__in'])) {
            $query_args['post__in'] = array_intersect($query_args['post__in'], $children_ids);
        } else {
            $query_args['post__in'] = $children_ids;
        }
    }

    return $query_args;
}

// previous orders

// -- show previous orders - modify query 
add_filter('iwptp_query_args', 'iwptp_custom__query_args__show_previous_orders');
function iwptp_custom__query_args__show_previous_orders($query_args)
{
    $table_data = iwptp_get_table_data();
    $sc_attrs = $table_data['query']['sc_attrs'];

    if (empty($sc_attrs['show_previous_orders'])) {
        return $query_args;
    }

    $current_user = wp_get_current_user();

    if (!$current_user->ID) { // show table is no previous orders found
        add_filter('iwptp_container_html_class', 'iwptp_container_html_class__show_table__no_previous_orders');
        $query_args['post__in'] = array(0);
        return $query_args;
    }

    $product_ids = iwptp_get_previously_purchased_products($current_user->ID, $sc_attrs['show_previous_orders']);

    if (empty($query_args['post__in'])) {
        $query_args['post__in'] = [];
    }

    if (count($query_args['post__in'])) {
        $query_args['post__in'] = array_intersect($query_args['post__in'], $product_ids);
    } else {
        $query_args['post__in'] = $product_ids;
    }

    if (!$query_args['post__in']) {
        $query_args['post__in'] = array(0);
    }

    return $query_args;
}

// -- hide previous orders - modify query 
add_filter('iwptp_query_args', 'iwptp_custom__query_args__hide_previous_orders');
function iwptp_custom__query_args__hide_previous_orders($query_args)
{
    $table_data = iwptp_get_table_data();
    $sc_attrs = $table_data['query']['sc_attrs'];

    if (empty($sc_attrs['hide_previous_orders'])) {
        return $query_args;
    }

    $current_user = wp_get_current_user();

    if (!$current_user->ID) { // hide table is no previous orders found
        add_filter('iwptp_container_html_class', 'iwptp_container_html_class__hide_table__no_previous_orders');
        $query_args['post__in'] = array(0);
        return $query_args;
    }

    $product_ids = iwptp_get_previously_purchased_products($current_user->ID, $sc_attrs['hide_previous_orders']);

    if (empty($query_args['post__not_in'])) {
        $query_args['post__not_in'] = [];
    }

    $query_args['post__not_in'] = array_merge($query_args['post__not_in'], $product_ids);

    return $query_args;
}

function iwptp_get_previously_purchased_products($user_id, $order_status)
{
    if ('true' == trim(strtolower($order_status))) {
        $order_status = [];
    } else {
        $order_status = array_map('trim', explode(',', $order_status));
        $_order_status = [];

        foreach ($order_status as $current_order_status) {
            if (substr($current_order_status, 0, 3) !== 'wc-') {
                $current_order_status = 'wc-' . $current_order_status;
            }

            array_push($_order_status, $current_order_status);
        }

        $order_status = array_intersect($_order_status, array_keys(wc_get_order_statuses())); // validate
    }

    if (!$order_status) { // default
        $order_status = array('wc-processing', 'wc-completed');
    }

    $args = array(
        'customer_id' => $user_id,
        'limit' => -1,
        'status' => $order_status
    );

    foreach (wc_get_orders($args) as $order) {
        $items = $order->get_items();
        foreach ($items as $item) {
            $product_id = $item->get_product_id();
            $product_ids[] = $product_id;
        }
    }

    return array_unique($product_ids);
}

function iwptp_container_html_class__hide_table__no_previous_orders($html_class)
{
    remove_filter('iwptp_container_html_class', 'iwptp_container_html_class__hide_table__no_previous_orders');
    return $html_class . ' iwptp-hide ';
}

// rating color
function iwptp_get_rating_stars_highlight_color($highlight_color_range = '', $rating_number = '', $default_color = '#FFC107')
{
    if (
        !trim($highlight_color_range) ||
        !$rating_number
    ) {
        return $default_color;
    }

    $highlight_color = $default_color;

    $array = preg_split("/\r\n|\n|\r/", $highlight_color_range);
    foreach ($array as $line) {
        $line = trim($line);
        if (!$line) {
            continue;
        }

        $line_explode = array_map("trim", explode(":", $line));

        if (!(count($line_explode) === 2)) {
            continue;
        }

        $range = $line_explode[0];
        $color = $line_explode[1];

        $range_explode = array_map("trim", explode("-", $range));

        if (count($range_explode) == 1) {
            $min = $max = $range_explode[0];
        } else if (count($range_explode) == 2) {
            $min = (float) $range_explode[0];
            $max = (float) $range_explode[1];
        } else {
            continue;
        }

        if (
            $rating_number <= $max &&
            $rating_number >= $min
        ) {
            $highlight_color = $color;
        }
    }

    return $highlight_color;
}

add_shortcode('iwptp_video', 'iwptp_video');
function iwptp_video($atts)
{
    if (
        empty($atts['width']) &&
        empty($atts['height'])
    ) {
        $atts['width'] = 300;
        $atts['height'] = 170;
    }

    if (!empty($atts['src'])) {
        global $product;

        $custom_field_src = get_post_meta($product->get_id(), $atts['src'], true);

        if ($custom_field_src) {
            $atts['src'] = $custom_field_src;
        } else {
            $attributes = $product->get_attributes();
            $custom_attr_key = sanitize_title($atts['src']);

            if (!empty($attributes[$custom_attr_key])) {
                $custom_attr = $attributes[$custom_attr_key];
                $options = $custom_attr->get_options();
                $atts['src'] = $attribute_src = $options[0];
            }
        }

        if (
            empty($custom_field_src) &&
            empty($attribute_src)
        ) {
            return;
        }
    }

    return wp_video_shortcode($atts);
}

// woocommerce native filter compatibility
add_action('iwptp_before_apply_user_filters', 'iwptp_apply_native_filters', 100, 1);
function iwptp_apply_native_filters($data)
{
    $table_data = iwptp_get_table_data();
    $sc_attrs = $table_data['query']['sc_attrs'];

    if (empty($sc_attrs['_archive'])) {
        return $data;
    }

    // price filter
    if (
        !empty($_GET['min_price']) ||
        !empty($_GET['max_price'])
    ) {
        $selected_min = !empty($_GET['min_price']) ? sanitize_text_field($_GET['min_price']) : '';
        $selected_max = !empty($_GET['max_price']) ? sanitize_text_field($_GET['max_price']) : '';

        $value = ($selected_min ? iwptp_price($selected_min) : 0) . ($selected_max ? ' - ' . iwptp_price($selected_max) : '+');

        // use filter in query
        $filter_info = array(
            'filter'        => 'price_range',
            'values'        => array($value),
            'min_price'     => $selected_min,
            'max_price'     => $selected_max,
            'clear_label'   => 'Price range',
        );

        iwptp_update_user_filters($filter_info, false);
    }

    // attribute filter
    if (!empty($_GET)) {
        foreach (iwptp_sanitize_array($_GET) as $key => $val) {
            if (substr($key, 0, 7) == 'filter_') {
                $taxonomy = 'pa_' . substr(sanitize_text_field($key), 7);
                foreach (explode(',', $val) as $term) {
                    $term = get_term_by('slug', trim(sanitize_text_field($term)), $taxonomy);
                    $filter_info = array(
                        'filter'      => 'attribute',
                        'values'      => $term ? array($term->term_taxonomy_id) : array(0),
                        'taxonomy'    => $taxonomy,
                        'operator'    => 'IN',
                        'clear_label' => '',
                        'clear_labels_2' => '',
                    );
                    iwptp_update_user_filters($filter_info, false);
                }
            }
        }
    }

    // rating filter
    if (!empty($_GET['rating_filter'])) {
        add_filter('iwptp_query_args', 'iwptp_query_args__apply_wc_native_rating_filter', 100, 1);
    }

    return $data;
}

function iwptp_query_args__apply_wc_native_rating_filter($query_args)
{
    $ratings = explode(',', sanitize_text_field($_GET['rating_filter']));

    if (!empty($query_args['meta_query'])) {
        foreach ($query_args['meta_query'] as $key => &$val) {
            if (
                gettype($val) === 'array' &&
                !empty($val['key']) &&
                $val['key'] === '_wc_average_rating'
            ) {
                $val['compare'] = 'IN';
                $val['value'] = $ratings;
            }
        }
    } else {
        $query_args['meta_query'] = array(
            array(
                'key'         => '_wc_average_rating',
                'value'        => $ratings,
                'compare'    => 'IN',
                'type'    => 'NUMERIC',
            ),
        );
    }

    remove_filter('iwptp_query_args', 'iwptp_query_args__apply_wc_native_rating_filter');

    return $query_args;
}

// -- pass params through JS
add_action('wp_print_scripts', 'iwptp_permit__native_wc_filters_js');
function iwptp_permit__native_wc_filters_js()
{
?>
    <script>
        if (typeof iwptp_persist_params === 'undefined') {
            var iwptp_persist_params = [];
        }
        iwptp_persist_params = iwptp_persist_params.concat(<?php echo wp_json_encode(iwptp_get_possible_native_wc_filter_params()); ?>);
    </script>
<?php
}
// -- pass params through PHP
add_filter('iwptp_permitted_params', 'iwptp_permit__native_wc_filters_php', 100, 1);
function iwptp_permit__native_wc_filters_php($params)
{
    $params = array_merge(
        $params,
        iwptp_get_possible_native_wc_filter_params()
    );
    return $params;
}

function iwptp_get_possible_native_wc_filter_params()
{
    $arr = array(
        'min_price',
        'max_price',
        'rating_filter'
    );

    if (function_exists('wc_get_attribute_taxonomies')) {
        foreach (wc_get_attribute_taxonomies() as $taxonomy) {
            $arr[] = 'filter_' . $taxonomy->attribute_name;
        }
    }

    return $arr;
}

add_shortcode('iwptp_stock_valuation', 'iwptp_stock_valuation');
function iwptp_stock_valuation()
{
    global $product;

    if ($stock = $product->get_stock_quantity()) {
        return wc_price($stock * wc_get_price_to_display($product));
    }
}

// product type
add_filter('iwptp_query_args', 'iwptp_query_args__product_type');
function iwptp_query_args__product_type($query_args)
{
    $table_data = iwptp_get_table_data();

    if (empty($query_args['tax_query'])) {
        $query_args['tax_query'] = [];
    }

    if (!empty($table_data['query']['sc_attrs']['product_type'])) {
        $product_types = array_map('trim', explode(',', $table_data['query']['sc_attrs']['product_type']));

        $query_args['tax_query'][] = array(
            'taxonomy' => 'product_type',
            'field'    => 'slug',
            'terms'    => $product_types,
        );
    }

    if (!empty($table_data['query']['sc_attrs']['exclude_product_type'])) {
        $exclude_product_type = array_map('trim', explode(',', $table_data['query']['sc_attrs']['exclude_product_type']));

        $query_args['tax_query'][] = array(
            'taxonomy' => 'product_type',
            'field'    => 'slug',
            'terms'    => $exclude_product_type,
            'operator' => 'NOT IN',
        );
    }



    return $query_args;
}


// image map label

// @TODO 
// -- orderby: image_map_labels no need to order by separately entered SKU unnecesarily
// -- default text and background color. image_map_labels="1: woo-cap | 2: woo-belt" image_map_label_text_color="white" image_map_label_background_color="black"
// -- different labels for same product SKU
// -- multiple labels for same product SKU

// -- labels 
add_action('iwptp_before_loop', 'iwptp_record_image_map_labels');
function iwptp_record_image_map_labels()
{
    global $iwptp_image_map_labels;
    $iwptp_image_map_labels = [];

    $table_data = iwptp_get_table_data();

    if (!empty($table_data['query']['sc_attrs']['image_map_labels'])) {
        $rules = explode('|', $table_data['query']['sc_attrs']['image_map_labels']);

        foreach ($rules as $rule) {
            list($sku, $text, $background_color, $color) = array_map('trim', explode(':', $rule));
            $iwptp_image_map_labels[$sku] = array(
                'text' => $text,
                'background_color' => $background_color,
                'color' => $color,
            );
        }
    }
}

// -- shortcode
add_shortcode('iwptp_image_map_label', 'iwptp_image_map_label');
function iwptp_image_map_label($atts = false)
{
    global $product, $iwptp_image_map_labels;

    $sku = $product->get_sku();
    if (!empty($iwptp_image_map_labels[$sku])) {
        $label = $iwptp_image_map_labels[$sku];
        return '<span class="iwptp-image-map-label" style="color: ' . $label['color'] . '; background-color: ' . $label['background_color'] . ';" >' . $label['text'] . '</span>';
    }
}

// -- permit repeats
add_filter('iwptp_products', 'iwptp_image_map_label__permit_repeat_sku');
function iwptp_image_map_label__permit_repeat_sku($products)
{
    $table_data = iwptp_get_table_data();

    if (!empty($table_data['query']['sc_attrs']['image_map_labels'])) {
        /* 
    - get the SKUs from the image_map_labels shortcode attr
    - if orderby = image map labels the place ids in same order as skus
    - if order by is anything else then place skus one after the other
    - consider it more. maybe disable sorting if image map relabels
    */

        // $products->posts = ...;
        // $products->found_posts = $products->post_count = ...;
    }

    return $products;
}

// modify number
add_shortcode('iwptp_modify_number', 'iwptp_modify_number');
function iwptp_modify_number($atts = false)
{
    extract(shortcode_atts(array(
        'attribute' => '',
        'custom_field' => '',
        'decimal_places' => 2,
        'minimum_characters' => 0,
        'empty' => '',
    ), $atts));

    global $product;

    if ($attribute) {
        $val = $product->get_attribute($attribute);
    } else if ($custom_field) {
        $val = get_post_meta($product->get_id(), $custom_field, true);
    } else {
        return;
    }

    if ($val) { // not 0
        if ($decimal_places) {
            $val = number_format((float) $val, $decimal_places, '.', '');
        }

        if ($minimum_characters) {
            $val = str_pad($val, $minimum_characters, '0', STR_PAD_LEFT);
        }
    } else {
        $val = $empty;
    }

    return "<span class=\"iwptp-modify-number\">$val</span>";
}

// Download CSV

// -- print json
add_action('iwptp_container_close', 'iwptp_print_csv_json');
function iwptp_print_csv_json()
{
    global $iwptp_csv_json_js_var_name;
    global $iwptp_csv_headings_js_var_name;
    global $iwptp_csv_columns;

    if (
        empty($iwptp_csv_json_js_var_name) ||
        empty($iwptp_csv_headings_js_var_name) ||
        empty($iwptp_csv_columns)
    ) {
        return;
    }

    global $iwptp_products;
    $product_ids = $iwptp_products->posts;

    $download_data = iwptp_csv_get_product_data($product_ids, $iwptp_csv_columns);

?>
    <script>
        var <?php echo wp_kses($iwptp_csv_headings_js_var_name, iwptp_allowed_html_tags()); ?> = <?php echo wp_kses(wp_json_encode(array_column($iwptp_csv_columns, 'column_heading')), iwptp_allowed_html_tags()); ?>,
            <?php echo wp_kses($iwptp_csv_json_js_var_name, iwptp_allowed_html_tags()); ?> = <?php echo wp_kses(wp_json_encode($download_data), iwptp_allowed_html_tags()); ?>;
    </script>
<?php

    $iwptp_csv_json_js_var_name = false;
    $iwptp_csv_headings_js_var_name = false;
    $iwptp_csv_columns = false;
}

// -- store session data
add_filter('iwptp_products', 'iwptp_csv_store_session_data');
add_filter('iwptp_variation_products', 'iwptp_csv_store_session_data');
function iwptp_csv_store_session_data($products)
{
    if (!empty($GLOBALS['iwptp_csv_columns'])) {
        iwptp_session()->set($GLOBALS['iwptp_csv_session_key'], array(
            'query' => $products->query,
            'columns' => $GLOBALS['iwptp_csv_columns'],
            'file_name' => $GLOBALS['iwptp_csv_file_name']
        ));
    }
    return $products;
}

// -- get product data
function iwptp_csv_get_product_data($product_ids = [], $csv_columns = [])
{
    // collect product data
    $download_data = [];

    foreach ($product_ids as $id) {
        $product = wc_get_product($id);
        $props = [];

        foreach ($csv_columns as $col) {
            $val = '';

            if (
                in_array(
                    $col['property'],
                    array(
                        'title',
                        'regular_price',
                        'sale_price',
                        'sku',
                        'id',
                        'rating_count',
                        'average_rating',
                        'weight',
                        'length',
                        'width',
                        'permalink',
                        'type',
                        'product_url',
                        'stock_quantity',
                    )
                ) &&
                method_exists($product, 'get_' . $col['property'])
            ) {
                $val = call_user_func(array($product, 'get_' . $col['property']));
            } else if (
                $col['property'] == 'highest_price' &&
                method_exists($product, 'get_variation_price')
            ) {
                $val = $product->get_variation_price('max');
            } else if (
                $col['property'] == 'lowest_price' &&
                method_exists($product, 'get_variation_price')
            ) {
                $val = $product->get_variation_price('min');
            } else if ($col['property'] == 'category') {
                $_id = $product->get_type() == 'variation' ? $product->get_parent_id() : $product->get_id();

                $terms = get_terms(array(
                    'taxonomy' => 'product_cat',
                    'object_ids' => $_id,
                    'fields' => 'names'
                ));
                $val = implode(', ', $terms);
            } else if ($col['property'] == 'date_created') {
                $val = $product->get_date_created()->format('Y-m-d H:i:s');;
            } else if ($col['property'] == 'availability') {
                $val = $product->get_availability()['availability'];
            } else if ($col['property'] == 'dimensions') {
                $val = str_replace('&times;', 'x', wc_format_dimensions($product->get_dimensions(false)));
            } else if ($col['property'] == 'product_image_url') {
                $val = wp_get_attachment_image_src($product->get_image_id(), 'full')[0];
            } else if ($col['property'] == 'short_description') {
                $val = $product->get_short_description();
            } else if ($col['property'] == 'content') {
                $val = get_the_content(false, false, $product->get_id());
            } else if ($col['property'] == 'is_on_sale') {
                $val = $product->is_on_sale() ? 'Yes' : 'No';
            } else if ($col['property'] == 'tags') {
                $terms = get_terms(array(
                    'taxonomy' => 'product_tag',
                    'object_ids' => $product->get_id(),
                    'fields' => 'names'
                ));
                $val = implode(', ', $terms);
            } else if (
                $col['property'] == 'meta' &&
                !empty($col['meta_key'])
            ) {
                $val = get_post_meta($product->get_id(), $col['meta_key'], true);
            } else if (
                $col['property'] == 'attribute' &&
                !empty($col['attribute_name'])
            ) {
                if ($col['attribute_name'] == '_custom') {
                    $val = $product->get_attribute($col['custom_attribute_name']);
                } else {
                    $val = $product->get_attribute($col['attribute_name']);
                }
            } else if (
                $col['property'] == 'taxonomy' &&
                !empty($col['taxonomy'])
            ) {
                $terms = get_terms(array(
                    'taxonomy' => $col['taxonomy'],
                    'object_ids' => $product->get_id(),
                    'fields' => 'names'
                ));
                $val = implode(', ', $terms);
            }

            // escape comma and double quote
            $val = '"' . str_replace('"', '""', $val) . '"';

            $props[] = $val;
        }

        $download_data[] = $props;
    }

    return $download_data;
}

add_action('wc_ajax_iwptp_get_csv', 'iwptp_get_csv_over_ajax');
function iwptp_get_csv_over_ajax()
{
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'iwptp_ajax_nonce')) {
        die();
    }

    extract(iwptp_session()->get(sanitize_text_field($_REQUEST['iwptp_csv_session_key'])));
    if (!empty($_REQUEST['iwptp_csv_include_all_products'])) {
        $query['posts_per_page'] = -1;
    }
    $products = new WP_Query($query);
    wp_send_json(iwptp_csv_get_product_data($products->posts, $columns));
}

// module: child row

// -- init if sc_attr is available and collect params
add_filter('iwptp_device_columns', 'iwptp_device_columns__detach_child_row_columns', 100, 2);
function iwptp_device_columns__detach_child_row_columns($columns, $device)
{
    global $iwptp_device_child_row_columns;
    $iwptp_device_child_row_columns = [];

    global $iwptp_toggle_params;
    $iwptp_toggle_params = [];

    $table_data = iwptp_get_table_data();

    // toggle rule inheritance
    $detected_device = iwptp_get_device();
    if (!empty($table_data['query']['sc_attrs'][$detected_device . '_child_row_columns'])) {
        $device = $detected_device;
    }

    if (
        empty($columns) ||
        empty($table_data['query']['sc_attrs'][$device . '_child_row_columns'])
    ) {
        return $columns;
    }

    $child_row_column_names = array_map('trim', explode('|', $table_data['query']['sc_attrs'][$device . '_child_row_columns']));

    foreach ($child_row_column_names as $item) { // info: 100%
        $explode = array_map('trim', explode(':', $item));

        $name = strtolower($explode[0]);
        $width = empty($explode[1]) ? '100%' : $explode[1];

        $column_index = null;
        foreach ($columns as $column_index => $column) {
            if (trim(strtolower($column['name'])) == $name) {
                $iwptp_device_child_row_columns[$column_index] = $column;
                $iwptp_device_child_row_columns[$column_index]['child_row_column_width'] = $width;
                unset($columns[$column_index]);
                break;
            }
        }
    }

    global $iwptp_working_on_child_row;
    $iwptp_working_on_child_row = false;
    add_filter('iwptp_product_row_html_class', 'iwptp_product_row_html_class__child_row', 100, 1);

    return $columns;
}

// -- icon style helper fn
function iwptp_get_child_row_toggle_button_style()
{
    $style = '';

    $table_data = iwptp_get_table_data();
    $sc_attrs = $table_data['query']['sc_attrs'];
    if (!empty($sc_attrs['child_row_toggle_icon_color'])) {
        $style .= ' color:' . $sc_attrs['child_row_toggle_icon_color'] . '; ';
    }
    if (!empty($sc_attrs['child_row_toggle_icon_background_color'])) {
        $style .= ' background-color:' . $sc_attrs['child_row_toggle_icon_background_color'] . '; ';
    }

    return $style;
}

// -- insert toggle button
add_action('iwptp_after_row_open', 'iwptp_after_row_open__insert_child_row_toggle_button');
function iwptp_after_row_open__insert_child_row_toggle_button()
{
    global $iwptp_device_child_row_columns;

    if (empty($iwptp_device_child_row_columns)) {
        return;
    }

?>
    <td class="iwptp-child-row-toggle iwptp-child-row-toggle--closed iwptp-noselect">
        <?php echo wp_kses(iwptp_icon('chevron-down', 'iwptp-child-row-toggle__control iwptp-child-row-toggle__control--open', iwptp_get_child_row_toggle_button_style()), iwptp_allowed_html_tags()); ?>
        <?php echo wp_kses(iwptp_icon('chevron-up', 'iwptp-child-row-toggle__control iwptp-child-row-toggle__control--close', iwptp_get_child_row_toggle_button_style()), iwptp_allowed_html_tags()); ?>
    </td>
<?php
}

// -- insert toggle button in column heading
add_action('iwptp_after_heading_row_open', 'iwptp_after_row_open__insert_child_row_toggle_heading');
function iwptp_after_row_open__insert_child_row_toggle_heading()
{
    global $iwptp_device_child_row_columns;

    if (empty($iwptp_device_child_row_columns)) {
        return;
    }

?>
    <th class="iwptp-heading iwptp-child-row-toggle iwptp-child-row-toggle--closed iwptp-noselect">
        <?php echo wp_kses(iwptp_icon('chevron-down', 'iwptp-child-row-toggle__control iwptp-child-row-toggle__control--open', iwptp_get_child_row_toggle_button_style()), iwptp_allowed_html_tags()); ?>
        <?php echo wp_kses(iwptp_icon('chevron-up', 'iwptp-child-row-toggle__control iwptp-child-row-toggle__control--close', iwptp_get_child_row_toggle_button_style()), iwptp_allowed_html_tags()); ?>
    </th>
<?php
}

// -- insert toggle row
add_filter('iwptp_row', 'iwptp_row__insert_toggle_row', 100, 1);
function iwptp_row__insert_toggle_row($row_markup)
{
    global $product;
    global $iwptp_device_child_row_columns;
    global $iwptp_toggle_params;

    if (empty($iwptp_device_child_row_columns)) {
        return $row_markup;
    }

    $iwptp_device_child_row_columns__inner_markup = [];

    // -- parse columns
    $cumulative_width = 0;
    foreach ($iwptp_device_child_row_columns as $column_index => $column) {
        $clear = 'none';
        $cumulative_width += (float) $column['child_row_column_width'];

        if ($cumulative_width > 100) {
            $cumulative_width = (float) $column['child_row_column_width'];
            $clear = 'both';
        }

        $iwptp_device_child_row_columns__inner_markup[] = array(
            'heading' => iwptp_parse_2($column['heading']['content'], $product),
            'content' => iwptp_parse_2($column['cell']['template'], $product),
            'width' => $column['child_row_column_width'],
            'index' => $column_index,
            'clear' => $clear,
        );
    }

    // -- arrange markup
    global $iwptp_working_on_child_row;
    $iwptp_working_on_child_row = true;

    ob_start();
    include(IWPTPL_PLUGIN_PATH . 'templates/row-open.php');
?>
    <td colspan="100">
        <?php foreach ($iwptp_device_child_row_columns__inner_markup as $column_index => $arr) : ?>
            <div class="iwptp-child-row__element iwptp-child-row__element--column-<?php echo esc_attr($arr['index']); ?>" style="width: <?php echo esc_attr($arr['width']); ?>; clear: <?php echo esc_attr($arr['clear']); ?>;">
                <div class="iwptp-child-row__element__heading"><?php echo wp_kses($arr['heading'], iwptp_allowed_html_tags()) ?></div>
                <div class="iwptp-child-row__element__content"><?php echo wp_kses($arr['content'], iwptp_allowed_html_tags()) ?></div>
            </div>
        <?php endforeach; ?>
    </td>
    </tr>
    <?php
    include(IWPTPL_PLUGIN_PATH . 'templates/row-close.php');

    $iwptp_working_on_child_row = false;

    return $row_markup . ob_get_clean();
}

function iwptp_product_row_html_class__child_row($html_class)
{
    global $iwptp_working_on_child_row;

    return $html_class . ($iwptp_working_on_child_row ? ' iwptp-child-row ' : ' iwptp-has-child-row ');
}

// module: instant sort

// -- gather table's sort options
add_filter('iwptp_container_html_attributes', 'iwptp_instant_sort__params');
function iwptp_instant_sort__params($attrs)
{
    $table_data = iwptp_get_table_data();
    global $iwptp_instant_sort_params;
    $iwptp_instant_sort_params = [];

    if (empty($table_data['query']['sc_attrs']['instant_sort'])) {
        return $attrs;
    }

    $iwptp_instant_sort_params = array(
        'column_heading' => [],
        'dropdown' => [],
    );

    foreach (array(
        'laptop'     => iwptp_get_device_columns_2('laptop'),
        'tablet'     => iwptp_get_device_columns_2('tablet'),
        'phone'     => iwptp_get_device_columns_2('phone'),
    ) as $device => $columns) {
        if (empty($columns)) {
            continue;
        }

        // get sort params from column heading sort elements
        foreach ($columns as $column_index => $column) {
            if (!empty($column['heading']['content'][0]['elements'])) {
                foreach ($column['heading']['content'][0]['elements'] as $heading_element) {
                    if ($heading_element['type'] == 'sorting') {
                        unset($heading_element['type']);
                        $_id = $heading_element['id'];
                        unset($heading_element['id']);
                        $iwptp_instant_sort_params['column_heading'][$_id] = $heading_element;
                        break;
                    }
                }
            }
        }
    }

    // get sort params from the nav sort by element
    if ($nav_sort_by__ref = iwptp_get_nav_elms_ref('sort_by', $table_data)) {
        foreach ($nav_sort_by__ref as $sort_by) {
            foreach ($sort_by['dropdown_options'] as $sort_by_option) {
                unset($sort_by_option['label']);
                $iwptp_instant_sort_params['dropdown'][] = $sort_by_option;
            }
        }
    }

    $attrs .= ' data-iwptp-instant-sort-params="' . esc_attr(wp_json_encode($iwptp_instant_sort_params)) . '" ';

    return $attrs;
}

// -- JSON attr on row markup
add_filter('iwptp_product_row_attributes', 'iwptp_instant_sort__print_product_sort_values');
function iwptp_instant_sort__print_product_sort_values($attrs)
{
    global $iwptp_instant_sort_params;

    if (empty($iwptp_instant_sort_params)) {
        return $attrs;
    }

    global $product;

    $sort_props = [];

    foreach ($iwptp_instant_sort_params['column_heading'] + $iwptp_instant_sort_params['dropdown'] as $sort_option) {
        switch ($sort_option['orderby']) {
            case 'title':
                $sort_props['title'] = $product->get_title();
                break;

            case 'price':
            case 'price-desc':
                if ($product->get_type() == 'variable') {
                    $sort_props['min_price'] = $product->get_variation_price('min');
                    $sort_props['max_price'] = $product->get_variation_price('max');
                } else {
                    $sort_props['price'] = wc_get_price_to_display($product);
                }
                break;

            case 'menu_order':
                $sort_props['menu_order'] = $product->get_menu_order();
                break;

            case 'popularity':
                $sort_props['popularity'] = get_post_meta($product->get_id(), 'total_sales', true);
                break;

            case 'rating':
                $sort_props['rating'] = $product->get_average_rating();
                break;

            case 'date':
                $sort_props['date'] = strtotime($product->get_date_created());
                break;

            case 'category':
                $exclude_term_ids = [];
                if (!empty($sort_option['orderby_ignore_category'])) {
                    $exclude_term_ids = get_terms(array(
                        'taxonomy' => 'product_cat',
                        'slug' => preg_split("/\r\n|\n|\r/", $sort_option['orderby_ignore_category']),
                        'fields' => 'ids',
                    ));
                }

                $include_term_ids = [];
                if (!empty($sort_option['orderby_focus_category'])) {
                    $include_term_ids = get_terms(array(
                        'taxonomy' => 'product_cat',
                        'slug' => preg_split("/\r\n|\n|\r/", $sort_option['orderby_focus_category']),
                        'fields' => 'ids',
                    ));
                }

                $terms = get_terms(array(
                    'taxonomy' => 'product_cat',
                    'object_ids' => $product->get_id(),
                    'include' => $include_term_ids,
                    'exclude' => $exclude_term_ids,
                    'fields' => 'names'
                ));

                $sort_props['category'] = implode(', ', $terms);
                break;

            case 'attribute':
            case 'attribute_num':

                if ($product->get_type() == 'variation') {
                    $terms = [];
                    $variation_attributes = $product->get_variation_attributes();
                    if (!empty($variation_attributes['attribute_' . $sort_option['orderby_attribute']])) {
                        $terms[] = $variation_attributes['attribute_' . $sort_option['orderby_attribute']];
                    } else {
                        $parent = wc_get_product($product->get_parent_id());
                        $terms = explode(",", $parent->get_attribute($sort_option['orderby_attribute']));
                    }
                } else {
                    $exclude_term_ids = [];
                    if (!empty($sort_option['orderby_ignore_attribute_term'])) {
                        $exclude_term_ids = get_terms(array(
                            'taxonomy' => $sort_option['orderby_attribute'],
                            'slug' => preg_split("/\r\n|\n|\r/", $sort_option['orderby_ignore_attribute_term']),
                            'fields' => 'ids',
                        ));
                    }

                    $include_term_ids = [];
                    if (!empty($sort_option['orderby_focus_attribute_term'])) {
                        $include_term_ids = get_terms(array(
                            'taxonomy' => $sort_option['orderby_attribute'],
                            'slug' => preg_split("/\r\n|\n|\r/", $sort_option['orderby_focus_attribute_term']),
                            'fields' => 'ids',
                        ));
                    }

                    $terms = get_terms(array(
                        'taxonomy' => $sort_option['orderby_attribute'],
                        'object_ids' => $product->get_id(),
                        'include' => $include_term_ids,
                        'exclude' => $exclude_term_ids,
                        'fields' => 'names'
                    ));
                }

                $sort_props['attribute__' . sanitize_title($sort_option['orderby_attribute'])] = implode(', ', $terms);
                break;

            case 'taxonomy':
                $exclude_term_ids = [];
                if (!empty($sort_option['orderby_ignore_taxonomy_term'])) {
                    $exclude_term_ids = get_terms(array(
                        'taxonomy' => $sort_option['orderby_taxonomy'],
                        'slug' => preg_split("/\r\n|\n|\r/", $sort_option['orderby_ignore_taxonomy_term']),
                        'fields' => 'ids',
                    ));
                }

                $include_term_ids = [];
                if (!empty($sort_option['orderby_focus_taxonomy_term'])) {
                    $include_term_ids = get_terms(array(
                        'taxonomy' => $sort_option['orderby_taxonomy'],
                        'slug' => preg_split("/\r\n|\n|\r/", $sort_option['orderby_focus_taxonomy_term']),
                        'fields' => 'ids',
                    ));
                }

                $terms = get_terms(array(
                    'taxonomy' => $sort_option['orderby_taxonomy'],
                    'object_ids' => $product->get_id(),
                    'include' => $include_term_ids,
                    'exclude' => $exclude_term_ids,
                    'fields' => 'names'
                ));

                $sort_props['taxonomy__' . sanitize_title($sort_option['orderby_taxonomy'])] = implode(', ', $terms);
                break;

            case 'meta_value':
            case 'meta_value_num':
                $sort_props['meta_value__' . sanitize_title($sort_option['meta_key'])] = get_post_meta($product->get_id(), $sort_option['meta_key'], true);
                break;

            case 'id':
                $sort_props['id'] = $product->get_id();
                break;

            case 'sku':
            case 'sku_num':
                $sort_props['sku'] = $product->get_sku();
                break;
        }
    }

    return $attrs . ' data-iwptp-instant-sort-props="' . esc_attr(wp_json_encode($sort_props)) . '" ';
}

// variation attribute columns

$iwptp_variation_attributes = null;
add_filter('iwptp_variation_products', 'iwptp_variation_products__get_variation_attributes');
function iwptp_variation_products__get_variation_attributes($result)
{
    global $iwptp_variation_attributes;
    $iwptp_variation_attributes = null;

    $table_data = iwptp_get_table_data();

    if (
        empty($table_data['query']['sc_attrs']['laptop_variation_attribute_columns']) &&
        empty($table_data['query']['sc_attrs']['tablet_variation_attribute_columns']) &&
        empty($table_data['query']['sc_attrs']['phone_variation_attribute_columns'])
    ) {
        return $result;
    }

    $variation_attributes = [];

    foreach ($result->posts as $variation_id) {
        $variation = wc_get_product($variation_id);
        $variation_attributes = array_merge($variation_attributes, array_keys($variation->get_variation_attributes()));
    }

    $iwptp_variation_attributes = array_unique($variation_attributes);

    return $result;
}

add_filter('iwptp_device_columns', 'iwptp_device_columns__add_variation_attribute_columns', 100, 2);
function iwptp_device_columns__add_variation_attribute_columns($device_columns, $device)
{
    $table_data = iwptp_get_table_data();
    global $iwptp_variation_attributes;

    if (
        empty($table_data['query']['sc_attrs'][$device . '_variation_attribute_columns']) ||
        empty($iwptp_variation_attributes)
    ) {
        return $device_columns;
    }

    foreach ($iwptp_variation_attributes as $variation_attribute) {
        // global attribute
        if ('attribute_pa_' == substr($variation_attribute, 0, 13)) {
            $attribute_name =  substr($variation_attribute, 13);
            $attribute_label =  wc_attribute_label(substr($variation_attribute, 10));

            $column = iwptp_sanitize_array(json_decode('{"name":"' . $attribute_name . '","heading":{"content":[{"id":' . wp_rand(1000, 100000000) . ',"style":[],"condition":[],"elements":[{"style":[],"text":"' . $attribute_label . '","id":' . wp_rand(1000, 100000000) . ',"type":"text"}],"type":"row"}],"style":[],"id":' . wp_rand(1000, 100000000) . '},"cell":{"template":[{"id":' . wp_rand(1000, 100000000) . ',"style":[],"condition":[],"elements":[{"separator":[{"id":' . wp_rand(1000, 100000000) . ',"style":[],"elements":[{"type":"text","text":" / ","id":' . wp_rand(1000, 100000000) . '}],"type":"row"}],"empty_relabel":[{"id":' . wp_rand(1000, 100000000) . ',"style":[],"condition":[],"elements":[],"type":"row"}],"relabels":[],"click_action":"","condition":{"product_type":[],"user_role":[]},"id":' . wp_rand(1000, 100000000) . ',"type":"attribute","attribute_name":"' . $attribute_name . '"}],"type":"row"}],"style":[],"id":' . wp_rand(1000, 100000000) . '}}', true));
        } else { // custom attribute
            $attribute_name =  substr($variation_attribute, 10);
            $attribute_label =  ucfirst($attribute_name);

            $column = iwptp_sanitize_array(json_decode('{"name":"' . $attribute_name . '","heading":{"content":[{"id":' . wp_rand(1000, 100000000) . ',"style":[],"condition":[],"elements":[{"style":[],"text":"' . $attribute_label . '","id":' . wp_rand(1000, 100000000) . ',"type":"text"}],"type":"row"}],"style":[],"id":' . wp_rand(1000, 100000000) . '},"cell":{"template":[{"id":' . wp_rand(1000, 100000000) . ',"style":[],"condition":[],"elements":[{"separator":[{"id":' . wp_rand(1000, 100000000) . ',"style":[],"elements":[{"type":"text","text":" ⋅ ","id":' . wp_rand(1000, 100000000) . '}],"type":"row"}],"empty_relabel":[{"id":' . wp_rand(1000, 100000000) . ',"style":[],"condition":[],"elements":[],"type":"row"}],"relabels":[],"click_action":"","condition":{"product_type":[],"user_role":[]},"id":' . wp_rand(1000, 100000000) . ',"type":"attribute","attribute_name":"_custom","custom_attribute_name":"' . $attribute_name . '"}],"type":"row"}],"style":[],"id":' . wp_rand(1000, 100000000) . '}}', true));
        }

        array_unshift($device_columns, $column);
    }

    return $device_columns;
}

add_shortcode('variation_attribute_list', 'iwptp_variation_attribute_list');
function iwptp_variation_attribute_list($atts)
{
    global $product;

    if ($product->get_type() !== 'variation') {
        return;
    }

    $arr = [];

    foreach ($product->get_variation_attributes() as $variation_attribute => $value) {

        if ('attribute_pa_' == substr($variation_attribute, 0, 13)) {
            $taxonomy = substr($variation_attribute, 10);
            $term = get_term_by('slug', $value, $taxonomy);
            $arr[wc_attribute_label($taxonomy)] = $term->name;
        } else {
        }
    }

    ob_start();
    if ($arr) {
    ?>
        <div class="iwptp-variation-attribute-list">
            <?php foreach ($arr as $key => $val) : ?>
                <div class="iwptp-variation-attribute-list__row">
                    <div class="iwptp-variation-attribute-list__row__column iwptp-variation-attribute-list__row__column--key">
                        <?php echo esc_html($key); ?>:
                    </div>
                    <div class="iwptp-variation-attribute-list__row__column iwptp-variation-attribute-list__row__column--val">
                        <?php echo esc_html($val); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php
    }

    return ob_get_clean();
}

/* 3rd party compatibility */

// ElasticPress compatibility fix
add_filter('iwptp_query_args', 'iwptp_elasticpress_compatibility_fix');
function iwptp_elasticpress_compatibility_fix($query_args)
{
    if (!function_exists('is_plugin_active')) {
        include_once(ABSPATH . 'wp-admin/includes/plugin.php');
    }

    if (is_plugin_active('elasticpress/elasticpress.php')) {
        $query_args['ep_integrate'] = false;
    }

    return $query_args;
}

// WPC Composite Products for WooCommerce

// -- css
add_action('wp_enqueue_scripts', 'iwptp_wpc_composite_products__style');
function iwptp_wpc_composite_products__style()
{
    if (!function_exists('wooco_init')) {
        return;
    }

    ob_start();
    ?>
    /* IWPTPL PRO - WPC Composite Products for WooCommerce integration */
    .iwptp-modal-content .wooco-wrap {
    padding: 10px 25px 0;
    }
    .iwptp-modal-content .wooco-wrap + .stock {
    padding: 0 25px 20px !important;
    margin: 0;
    }
<?php
    wp_add_inline_style('iwptp', ob_get_clean(), 'after');
}

// -- js
add_action('wp_enqueue_scripts', 'iwptp_wpc_composite_products__script');
function iwptp_wpc_composite_products__script()
{
    if (!function_exists('wooco_init')) {
        return;
    }

    ob_start();
?>
    // IWPTPL PRO - WPC Composite Products for WooCommerce integration
    (function($){
    $('body').on('iwptp_product_modal_ready', function(){
    if( typeof wooco_init !== 'undefined' ){
    wooco_vars.container_selector = '.iwptp-modal-content';
    wooco_init_selector();
    wooco_init($('.iwptp-modal-content .wooco-wrap'));
    }
    })
    })(jQuery);
<?php
    wp_add_inline_script('iwptp', ob_get_clean(), 'after');
}

// -- skip sub products from cart count
add_filter('iwptp_permit_item_in_cart_count', 'iwptp_skip_composite_sub_products_from_cart_count', 100, 2);
function iwptp_skip_composite_sub_products_from_cart_count($permit, $item)
{
    if (!empty($item['wooco_parent_id'])) {
        $permit = false;
    }

    return $permit;
}

// WPC Product Bundles for WooCommerce
// -- css
add_action('wp_enqueue_scripts', 'iwptp_wpc_bundled_products__style');
function iwptp_wpc_bundled_products__style()
{
    if (!function_exists('woosb_init')) {
        return;
    }

    ob_start();
?>
    /* IWPTPL PRO - WPC Product Bundles for WooCommerce integration */
    .iwptp-modal-content .woosb-wrap {
    padding: 10px 25px 0;
    }
    .iwptp-modal-content .woosb-wrap + .stock {
    padding: 0 25px 20px !important;
    margin: 0;
    }
<?php
    wp_add_inline_style('iwptp', ob_get_clean(), 'after');
}

// -- js
add_action('wp_enqueue_scripts', 'iwptp_wpc_bundled_products__script');
function iwptp_wpc_bundled_products__script()
{
    if (!function_exists('wooco_init')) {
        return;
    }

    ob_start();
?>
    // IWPTPL PRO - WPC Product Bundles for woosbmmerce integration
    (function($){
    $('body').on('iwptp_product_modal_ready', function(){
    if( typeof woosb_init !== 'undefined' ){
    woosb_vars.container_selector = '.iwptp-modal-content';
    woosb_init($('.iwptp-modal-content .woosb-wrap'));
    }
    })
    })(jQuery);
<?php
    wp_add_inline_script('iwptp', ob_get_clean(), 'after');
}

// -- skip sub products from cart count
add_filter('iwptp_permit_item_in_cart_count', 'iwptp_skip_bundled_sub_products_from_cart_count', 100, 2);
function iwptp_skip_bundled_sub_products_from_cart_count($permit, $item)
{
    if (!empty($item['woosb_parent_id'])) {
        $permit = false;
    }

    return $permit;
}

// Woocommerce Custom Product Addons

// radio and checkbox groups need unique names
// the suffix is removed during iwptp_cart_works
add_action('iwptp_modal_form_content_start', 'iwptp_iwptp__add_filter', 100);
add_action('iwptp_before_loop', 'iwptp_iwptp__add_filter', 100);
function iwptp_iwptp__add_filter()
{
    if (function_exists('wcpa_is_wcpa_product')) {
        add_filter('wcpa_product_form_fields', 'iwptp_iwptp__append_field_names', 100);
    }
}

add_action('iwptp_container_close', 'iwptp_iwptp__remove_filter', 100);
function iwptp_iwptp__remove_filter()
{
    if (function_exists('wcpa_is_wcpa_product')) {
        remove_filter('wcpa_product_form_fields', 'iwptp_iwptp__append_field_names');
    }
}

function iwptp_iwptp__append_field_names($data)
{
    foreach ($data as &$field) {
        if (
            !empty($field->type) &&
            !empty($field->name) &&
            in_array($field->type, array('radio-group', 'checkbox-group'))
        ) {
            $field->name = $field->name . '--iwptp-' . wp_rand(0, 1000000);
        }
    }
    return $data;
}

// -- remove the suffix from modal and cart form submissions
add_action('wp_loaded', 'iwptp_iwptp__remove_suffix', 10);
function iwptp_iwptp__remove_suffix()
{
    if (function_exists('wcpa_is_wcpa_product')) {
        foreach ($_REQUEST as $key => $val) {
            $key = sanitize_text_field($key);
            $val = (is_array($val)) ? iwptp_sanitize_array($val) : sanitize_text_field($key);
            $suffix_index = strpos($key, '--iwptp');
            if (FALSE !== $suffix_index) {
                $_REQUEST[substr($key, 0, $suffix_index)] = $val;
                unset($_REQUEST[$key]);

                $_POST[substr($key, 0, $suffix_index)] = $val;
                unset($_POST[$key]);
            }
        }
    }
}

// WavePlayer
add_shortcode('iwptp_waveplayer', 'iwptp_waveplayer');
function iwptp_waveplayer($args)
{
    if (empty($GLOBALS['waveplayer'])) {
        return;
    }

    if (!$args) {
        $args = [];
    }

    // skin
    if (empty($args['skin'])) {
        $args['skin'] = get_option('waveplayer_skin', 'play_n_wave');
    }

    if ($args['skin'] === 'thumb_n_wave') {
        $args['skin'] = 'play_n_wave';
    }

    if (
        empty($args['width']) &&
        !empty($args['skin'])
    ) {
        // default width based on skin
        $skin = strtolower(trim($args['skin']));

        if (in_array($skin, array('play_n_wave', 'thumb_n_wave'))) {
            $args['width'] = '0 - 349: 200 | 350 - 1199: 250 | 1200+: 300';
        }

        if (in_array($skin, array('w3-standard', 'w3-exhibition'))) {
            $args['width'] = 'auto';
        }

        if (in_array($skin, array('w2-legacy', 'w2-evolution'))) {
            $args['width'] = '0 - 350: 200 | 351 - 500: 250 | 501+: 400';
        }
    }

    // width
    $width = '';
    if (
        !empty($args['width'])
    ) {

        if (
            FALSE !== strpos($args['width'], ":") ||
            FALSE !== strpos($args['width'], "|") ||
            FALSE !== strpos($args['width'], "+") ||
            FALSE !== strpos($args['width'], "-")
        ) {

            // parse width into CSS media queries
            $css = "";
            foreach (explode("|", $args['width']) as $rule) {
                $rule_arr = array_map('trim', explode(":", $rule));

                if (
                    !count($rule_arr) === 2 ||
                    !is_numeric($rule_arr[1])
                ) {
                    continue;
                }

                $params = $rule_arr[0];
                $width = trim((int) $rule_arr[1]) . 'px';

                $params_arr = array_map('trim', explode("-", $params));

                $min = (int) $params_arr[0] . 'px';
                if (!empty($params_arr[1])) {
                    $max = (int) $params_arr[1] . 'px';
                } else {
                    $max = false;
                }

                $css .= " @media " . ($min ? " (min-width: " . $min . ") " : "") . ($min && $max ? "and" : "") . ($max ? " (max-width: " . $max . ") " : "") . " { .iwptp-%id% .iwptp-waveplayer-container { width: " . $width . "; } } ";
            }

            if ($css) {
                $GLOBALS['iwptp_waveplayer_width_css'] = "<style>" . $css . "</style>";
            }
        } else if (is_numeric((int) $args['width'])) {
            $width = (int) $args['width'];
        }
    }

    // active row highlight
    $active_row_styling_info = '';
    if (!empty($args['active_row_background_color'])) {
        $active_row_styling_info .= ' data-iwptp-waveplayer-active-row-background-color="' . $args['active_row_background_color'] . '" ';
    }
    if (!empty($args['active_row_outline_color'])) {
        $active_row_styling_info .= ' data-iwptp-waveplayer-active-row-outline-color="' . $args['active_row_outline_color'] . '" ';
    }
    if (!empty($args['active_row_outline_width'])) {
        $active_row_styling_info .= ' data-iwptp-waveplayer-active-row-outline-width="' . $args['active_row_outline_width'] . '" ';
    }

    // filename
    if (!empty($args['file_name'])) {
        $file_name = $args['file_name'];
        unset($args['file_name']);
    }

    $args_str = '';
    foreach ($args as $key => $val) {
        $args_str .= $key . '="' . $val . '" ';
    }

    // fill out urls / ids
    global $product;
    $product_id = $product->get_id();

    if ($preview_files = PerfectPeach\WavePlayer\WooCommerce::get_preview_files($product_id)) {
        if (!empty($file_name)) {
            $file_name = array_map('trim', explode('|', strtolower($file_name)));
            if ($_preview_files = get_post_meta($product_id, '_preview_files', true)) {
                $urls = [];
                foreach ($file_name as $_file_name) {
                    foreach ($_preview_files as $hash => $data) {
                        if (trim(strtolower($data['name'])) == $_file_name) {
                            $urls[] = $data['file'];
                        }
                    }
                }
                $urls = implode(',', $urls);
                $args_str .= " url='$urls' ";
            }
        } else {
            if (isset($preview_files['ids'])) {
                $type = "ids";
            } else if (isset($preview_files['url'])) {
                $type = "url";
            }

            $list = implode(',', $preview_files[$type]);
            $args_str .= " $type='$list' ";
        }
    }

    ob_start();

    $html_class = 'iwptp-waveplayer-container';
    $style = '';
    if (is_numeric($width)) {
        $style = ' style="width:' . (int) $args['width'] . 'px;" ';
        $html_class .= ' iwptp-waveplayer-container--has-width';
    }

?>
    <div class="<?php echo esc_attr($html_class); ?>" <?php echo esc_attr($active_row_styling_info); ?> <?php echo esc_attr($style); ?>><?php echo do_shortcode("[waveplayer $args_str ]"); ?></div>
    <?php

    return ob_get_clean();
}

// -- print width CSS for shortcode (only once)
add_filter('iwptp_element_markup', 'iwptp_waveplayer_print_width_css', 10, 2);
function iwptp_waveplayer_print_width_css($markup, $element)
{
    if (
        $element['type'] === 'shortcode' &&
        FALSE !== strpos($markup, 'iwptp-waveplayer-container')
    ) {
        if (
            !empty($GLOBALS['iwptp_waveplayer_width_css']) &&
            empty($GLOBALS['iwptp_waveplayer_width_css_' . $element['id'] . '_done'])
        ) {
            $markup .= str_replace('%id%', $element['id'], $GLOBALS['iwptp_waveplayer_width_css']);
            unset($GLOBALS['iwptp_waveplayer_width_css']);
            $GLOBALS['iwptp_waveplayer_width_css_' . $element['id'] . '_done'] = true;
        }
    }

    return $markup;
}

// Product Quantity for WooCommerce Pro
add_filter('woocommerce_available_variation', 'iwptp__woocommerce_available_variation__alg', 100, 3);
function iwptp__woocommerce_available_variation__alg($props, $_class, $variation)
{
    if (class_exists('Alg_WC_PQ_Core')) {
        $props['step'] = iwptp__alg__get_product_qty_step($variation->get_id());
    }

    return $props;
}

function iwptp__alg__get_product_qty_step($product_id, $default_step = 0)
{
    if ('yes' === get_option('alg_wc_pq_step_section_enabled', 'no')) {
        if ('yes' === apply_filters('alg_wc_pq', 'no', 'quantity_step_per_product') && 0 != ($step_per_product = apply_filters('alg_wc_pq', 0, 'quantity_step_per_product_value', array('product_id' => $product_id)))) {
            return $step_per_product;
        } else {
            return (0 != ($step_all_products = get_option('alg_wc_pq_step', 0)) ? $step_all_products : $default_step);
        }
    } else {
        return $default_step;
    }
}

// WCFM
// -- shortcode attr store & store_id
add_filter('iwptp_query_args', 'iwptp__query_args__store');
function iwptp__query_args__store($query_args)
{
    $table_data = iwptp_get_table_data();
    global $WCFM;

    if (empty($WCFM)) {
        return $query_args;
    }

    $store_id = [];

    if (!empty($table_data['query']['sc_attrs']['store_id'])) {
        $store_id = explode(',', $table_data['query']['sc_attrs']['store_id']);
    } else if (!empty($table_data['query']['sc_attrs']['store'])) {
        $store = explode(',', $table_data['query']['sc_attrs']['store']);
        foreach ($store as $_store) {
            $user = get_user_by('login', $_store);
            array_push($store_id, $user->ID);
        }
    }

    if ($store_id) {
        $product_id = [];

        foreach ($store_id as $_store_id) {
            $_products = $WCFM->wcfm_vendor_support->wcfm_get_products_by_vendor(
                $_store_id,
                'publish',
                array(
                    'posts_per_page' => -1
                )
            );

            $product_id = array_merge($product_id, array_keys($_products));
        }

        if ($product_id) {
            if (!empty($query_args['post__in'])) {
                $query_args['post__in'] = array_intersect($product_id, $query_args['post__in']);
            } else {
                $query_args['post__in'] = $product_id;
            }
        }

        // no results 
        if (!$query_args['post__in']) {
            $query_args['post__in'] = array(0);
        }
    }

    return $query_args;
}

// -- WCFM query conflict fix
add_filter('woocommerce_shortcode_products_query', 'iwptp_wcfm_confclit_fix', 1, 3);
function iwptp_wcfm_confclit_fix($query_args, $attributes, $type)
{
    if (
        $type == 'product_table' &&
        !empty($GLOBALS['WCFMmp'])
    ) {
        remove_filter('woocommerce_shortcode_products_query', array($GLOBALS['WCFMmp']->wcfmmp_product_multivendor, 'wcfmmp_product_widget_duplicate_hide'));
    }
    return $query_args;
}

// Upcoming
add_filter('iwptp_query_args', 'iwptp__query_args__upcoming');
function iwptp__query_args__upcoming($query_args)
{
    $table_data = iwptp_get_table_data();

    if (!empty($table_data['query']['sc_attrs']['upcoming'])) {
        if (empty($query_args['meta_query'])) {
            $query_args['meta_query'] = [];
        }

        if ($table_data['query']['sc_attrs']['upcoming'] == 'show') {
            $query_args['meta_query'][] = array(
                'key' => '_upcoming',
                'value' => 'yes',
                'compare' => '=',
            );
        } else {
            $query_args['meta_query'][] = array(
                'relation' => 'OR',
                array(
                    'key'     => '_upcoming',
                    'value'   => array('no', ''),
                    'compare' => 'IN'
                ),
                array(
                    'key'     => '_upcoming',
                    'compare' => 'NOT EXISTS'
                )
            );
        }
    }

    return $query_args;
}

add_shortcode('iwptp_upcoming_message', 'iwptp_upcoming_message');
function iwptp_upcoming_message()
{
    ob_start();
    if (class_exists('Woocommerce_Upcoming_Product')) {
        global $post;
        if (iwptp__is_upcoming()) {
            if (WC_Admin_Settings::get_option('wup_show_available_date', 'yes') == 'yes') {
                $_available_on = get_post_meta($post->ID, '_available_on', true); ?>
                <div class="iwptp-wup-availablity-message">
                    <?php
                    $availabel_date_lebel = WC_Admin_Settings::get_option('wup_availabel_date_lebel', 'Available from');
                    $not_availabel_date_text = WC_Admin_Settings::get_option('wup_not_availabel_date_text', 'Date not set yet');
                    if (!empty($availabel_date_lebel)) {
                        echo esc_html($availabel_date_lebel) . ': ';
                    }
                    if (empty($_available_on)) {
                        echo esc_html($not_availabel_date_text);
                    } else {
                        if ('date' == WC_Admin_Settings::get_option('wup_available_date_format', 'date')) {
                            echo esc_html(date_i18n(get_option('date_format'), strtotime($_available_on)));
                        } else if ('duration' == WC_Admin_Settings::get_option('wup_available_date_format', 'date')) {
                            echo esc_html(iwptp__wup_get_date_diff(current_time('timestamp'), $_available_on));
                        }
                    }
                    ?>
                </div>
            <?php
            }
        }
    }

    return ob_get_clean();
}

add_shortcode('iwptp_upcoming_date', 'iwptp_upcoming_date');
function iwptp_upcoming_date()
{
    ob_start();
    if (class_exists('Woocommerce_Upcoming_Product')) {
        global $post;
        if (iwptp__is_upcoming()) {
            if (
                WC_Admin_Settings::get_option('wup_show_available_date', 'yes') == 'yes' &&
                $_available_on = get_post_meta($post->ID, '_available_on', true)
            ) {
            ?>
                <div class="iwptp-wup-availablity-date">
                    <?php
                    if ('date' == WC_Admin_Settings::get_option('wup_available_date_format', 'date')) {
                        echo esc_html(date_i18n(get_option('date_format'), strtotime($_available_on)));
                    } else if ('duration' == WC_Admin_Settings::get_option('wup_available_date_format', 'date')) {
                        echo esc_html(iwptp__wup_get_date_diff(current_time('timestamp'), $_available_on));
                    }
                    ?>
                </div>
    <?php
            }
        }
    }

    return ob_get_clean();
}

// -- hide Button element if upcoming

add_filter('iwptp_element_markup', 'iwptp_element_markup__upcoming_hide_button', 100, 2);
function iwptp_element_markup__upcoming_hide_button($markup, $element)
{
    if (
        class_exists('Woocommerce_Upcoming_Product') &&
        iwptp__is_upcoming() &&
        !empty($element['type']) &&
        $element['type'] === 'button' &&
        !empty($element['link']) &&
        in_array($element['link'], array('cart_ajax', 'cart_refresh', 'cart_redirect', 'cart_custom'))
    ) {
        $markup = '';
    }

    return $markup;
}

function iwptp__is_upcoming()
{
    global $post;
    if (is_null($post)) {
        return;
    }
    if (get_post_meta($post->ID, '_upcoming', true) == 'yes') {
        return true;
    } else {
        return false;
    }
}

function iwptp__wup_get_date_diff($time1, $time2, $precision = 2)
{
    // If not numeric then convert timestamps
    if (!is_int($time1)) {
        $time1 = strtotime($time1);
    }
    if (!is_int($time2)) {
        $time2 = strtotime($time2);
    }
    // If time1 > time2 then swap the 2 values
    if ($time1 > $time2) {
        list($time1, $time2) = array($time2, $time1);
    }
    // Set up intervals and diffs arrays
    $intervals = array('year', 'month', 'day', 'hour', 'minute', 'second');
    $diffs = [];
    foreach ($intervals as $interval) {
        // Create temp time from time1 and interval
        $ttime = strtotime('+1 ' . $interval, $time1);
        // Set initial values
        $add = 1;
        $looped = 0;
        // Loop until temp time is smaller than time2
        while ($time2 >= $ttime) {
            // Create new temp time from time1 and interval
            $add++;
            $ttime = strtotime("+" . $add . " " . $interval, $time1);
            $looped++;
        }
        $time1 = strtotime("+" . $looped . " " . $interval, $time1);
        $diffs[$interval] = $looped;
    }
    $count = 0;
    $times = [];
    foreach ($diffs as $interval => $value) {
        // Break if we have needed precission
        if ($count >= $precision) {
            break;
        }
        // Add value and interval if value is bigger than 0
        if ($value > 0) {
            if ($value != 1) {
                $interval .= "s";
            }
            // Add value and interval to times array
            $times[] = $value . " " . $interval;
            $count++;
        }
    }
    // Return string with times
    return implode(", ", $times);
}

// User favorites
add_filter('iwptp_shortcode_attributes', 'iwptp_user_favorite_apply_post_ids');
function iwptp_user_favorite_apply_post_ids($atts = [])
{
    if (
        !empty($atts['user_favorites']) &&
        function_exists('get_user_favorites')
    ) {
        $favorites = get_user_favorites();

        if (!count($favorites)) {
            $ids = array(0);
        } else {
            $ids = array_values($favorites);
        }

        $atts['ids'] = implode(', ', $ids);
    }

    return $atts;
}


// TI WooCommerce Wishlist
add_filter('iwptp_shortcode_attributes', 'iwptp_ti_wishlist_apply_post_ids');
function iwptp_ti_wishlist_apply_post_ids($atts = [])
{
    if (
        class_exists('TInvWL_Public_Wishlist_View') &&
        !empty($atts['ti_wishlist'])
    ) {
        $atts['ids'] = iwptp_ti_get_wishlist_product_ids();

        if ($atts['ids'] == -1) {
            add_filter('iwptp_container_html_class', 'iwptp_container_html_class__hide_table__empty_wishlist');
        }
    }

    return $atts;
}

// -- include variations in table
add_filter('iwptp_query_args', 'iwptp_query_args__ti_wishlist__include_variations');
function iwptp_query_args__ti_wishlist__include_variations($args)
{
    $table_data = iwptp_get_table_data();
    $sc_attrs = $table_data['query']['sc_attrs'];

    if (!empty($sc_attrs['ti_wishlist'])) {
        $args['post_type'] = array('product', 'product_variation');
    }

    return $args;
}

// -- get wishlist product ids
function iwptp_ti_get_wishlist_product_ids()
{
    if (!class_exists('TInvWL_Public_Wishlist_View')) {
        return '-1';
    }

    $instance = TInvWL_Public_Wishlist_View::instance();
    $products = $instance->get_current_products(null, false, 999999);

    if ($products) {
        $ids = [];
        foreach ($products as $product) {
            $id = $product['product_id'];

            // note: could be child of grouped product as well (TI Wishlist quirk)
            if ($product['variation_id']) {
                $id = $product['variation_id'];
            }

            $ids[] = $id;
        }
        return implode(",", array_unique($ids));
    } else {
        return '-1';
    }
}

function iwptp_container_html_class__hide_table__empty_wishlist($html_class)
{
    remove_filter('iwptp_container_html_class', 'iwptp_container_html_class__hide_table__empty_wishlist');
    $html_class .= ' iwptp-hide iwptp-hide--empty-wishlist';
    return $html_class;
}

// -- note wishlist items
add_action('wp_enqueue_scripts', 'iwptp_wishlist__wishlist_data');
function iwptp_wishlist__wishlist_data()
{
    if (!class_exists('TInvWL_Public_Wishlist_View')) {
        return;
    }

    ob_start();

    $instance = TInvWL_Public_Wishlist_View::instance();
    $wishlist = $instance->get_current_wishlist();

    ?>
    var iwptp_ti_wishlist_url = "<?php echo esc_url(tinv_url_wishlist_default()); ?>";
    <?php

    if (
        $wishlist &&
        get_current_user_id() === $wishlist['author'] &&
        iwptp_ti_get_wishlist_product_ids() !== '-1'
    ) {
    ?>
        var iwptp_ti_wishlist_ids = "<?php echo esc_html(iwptp_ti_get_wishlist_product_ids()); ?>".split(",");
    <?php
    } else {
    ?>
        var iwptp_ti_wishlist_ids = [];
    <?php
    }

    wp_add_inline_script('iwptp', ob_get_clean(), 'before');
}

// -- integration Shortcode
add_shortcode('iwptp_wishlist', 'iwptp_wishlist');
function iwptp_wishlist($atts)
{
    if (!class_exists('TInvWL_Public_Wishlist_View')) {
        return;
    }

    extract(shortcode_atts(array(
        'icon' => 'heart',
        'add_title' => 'Add to wishlist',
        'remove_title' => 'Remove from wishlist',

        'icon_color' => false,
        'active_icon_color' => false,

        'icon_fill' => false,
        'active_icon_fill' => false,

        'icon_font_size' => false,

        'item_added_label' => false,
        'view_wishlist_label' => false,

        'variable_permitted' => false,

        'custom_url' => false,

        'duration' => '4',

    ), $atts, 'iwptp_wishlist'));

    // style

    // -- active
    $active_style = "";

    if (!empty($icon_font_size)) { // font size
        $active_style .= " font-size: " . (float) $icon_font_size . "px; ";
    }

    if (!empty($active_icon_color)) { // color
        $active_style .= " color: " . $active_icon_color . "; ";
    }

    if (!empty($active_icon_fill)) { // fill
        $active_style .= " fill: " . $active_icon_fill . "; ";
    }

    // -- default
    $default_style = "";

    if (!empty($icon_font_size)) { // font size
        $default_style .= " font-size: " . (float) $icon_font_size . "px; ";
    }

    if (!empty($icon_color)) { // color
        $default_style .= " color: " . $icon_color . "; ";
    }

    if (!empty($icon_fill)) { // fill
        $default_style .= " fill: " . $icon_fill . "; ";
    }

    // icon
    if (!in_array($icon, array('heart', 'playlist'))) {
        $icon = 'heart';
    }

    $icon = trim(strtolower($icon));

    // title
    if (!empty($atts['add_title_' . strtolower(get_locale())])) {
        $add_title = $atts['add_title_' . strtolower(get_locale())];
    }

    if (!empty($atts['remove_title_' . strtolower(get_locale())])) {
        $remove_title = $atts['remove_title_' . strtolower(get_locale())];
    }

    // label
    if (!empty($atts['item_added_label_' . strtolower(get_locale())])) {
        $item_added_label = $atts['item_added_label_' . strtolower(get_locale())];
    }

    if (!empty($atts['view_wishlist_label_' . strtolower(get_locale())])) {
        $view_wishlist_label = $atts['view_wishlist_label_' . strtolower(get_locale())];
    }

    ob_start();
    ?>
    <span class="iwptp-wishlist" <?php if ($variable_permitted) : ?> data-iwptp-variable-permitted="true" <?php endif; ?> data-iwptp-icon="<?php echo esc_attr($icon) ?>" data-iwptp-product-name="<?php echo esc_attr(get_the_title()); ?>" data-iwptp-item-added-label="<?php echo $item_added_label ? esc_attr($item_added_label) : ""; ?>" data-iwptp-view-wishlist-label="<?php echo $view_wishlist_label ? esc_attr($view_wishlist_label) : ""; ?>" data-iwptp-custom-url="<?php echo $custom_url ? esc_url($custom_url) : ""; ?>" data-iwptp-duration-seconds="<?php echo (float) esc_attr($atts['duration']); ?>">
        <span class="iwptp-wishlist__view iwptp-wishlist__view--not-added" title="<?php echo esc_attr($add_title); ?>">
            <?php echo wp_kses(iwptp_icon($icon, null, $default_style), iwptp_allowed_html_tags()); ?>
        </span>
        <span class="iwptp-wishlist__view iwptp-wishlist__view--added" title="<?php echo esc_attr($remove_title); ?>">
            <?php echo wp_kses(iwptp_icon($icon, null, $active_style), iwptp_allowed_html_tags()); ?>
        </span>
    </span>
    </span>
<?php

    return ob_get_clean();
}

// -- HTML classes
add_action('wp_enqueue_scripts', 'iwptp_wishlist__script');
function iwptp_wishlist__script()
{
    if (!class_exists('TInvWL_Public_Wishlist_View')) {
        return;
    }

    ob_start();
?>
    <?php
    wp_add_inline_script('iwptp', ob_get_clean(), 'before');

    ob_start();
    ?>

    .iwptp-wishlist {
    cursor: pointer;
    }

    .iwptp-wishlist.iwptp-disabled {
    opacity: .33;
    cursor: not-allowed;
    }

    .iwptp-wishlist__view {
    display: none;
    }

    .iwptp-wishlist:not(.iwptp-active) .iwptp-wishlist__view--not-added,
    .iwptp-wishlist.iwptp-active .iwptp-wishlist__view--added {
    display: inline-block;
    }

    .iwptp-wishlist.iwptp-loading {
    cursor: default;
    pointer-events: none;
    animation: iwptpPulse4 .6s linear infinite;
    }

    .iwptp-wishlist:not(.iwptp-disabled):not(.iwptp-loading) .iwptp-wishlist__view:hover {
    opacity: .8;
    }

    .iwptp-wishlist__view .iwptp-icon {
    fill: #eee;
    color: #999;
    display: inline-block;
    }

    .iwptp-icon-playlist {
    font-size: 36px;
    }

    .iwptp-table .iwptp-icon-heart {
    font-size: 20px;
    }

    .iwptp-wishlist__view--added .iwptp-icon {
    fill: #c1e3ff;
    color: #0D47A1;
    }

    .iwptp-wishlist-removing-row {
    animation: iwptpPulse2 .3s linear infinite;
    }

    a.iwptp-ti-wishlist-growler {
    position: fixed;
    right: 60px;
    bottom: 60px;
    display: inline-block;
    background: #ffffff;
    color: black;
    font-size: 16px;
    outline: none;
    border: 1px solid rgba(0, 0, 0, .8);
    padding: 15px 20px 15px 65px;
    box-shadow: 0 6px 7px rgb(0 0 0 / 15%);
    border-radius: 6px;
    transform: translateY(40px);
    opacity: 0;
    transition: .4s opacity, .4s transform;
    z-index: 2;
    line-height: 1.6em;
    min-width: 250px;
    }

    @media( max-width: 500px ){
    a.iwptp-ti-wishlist-growler {
    right: 10%;
    bottom: 50px;
    width: 80%;
    min-width: 0;
    }
    }

    .iwptp-ti-wishlist-growler--revealed {
    transform: translateY(0) !important;
    opacity: 1 !important;
    }

    .iwptp-ti-wishlist-growler__icon {
    margin-right: 4px;
    font-size: 28px;
    color: #666;
    vertical-align: middle;
    position: absolute;
    left: 20px;
    top: 50%;
    transform: translateY(-50%);
    }

    .iwptp-ti-wishlist-growler__label {
    display: block;
    }

    .iwptp-ti-wishlist-growler__label--item-added {
    vertical-align: middle;
    }

    .iwptp-ti-wishlist-growler__label--view-wishlist {
    font-size: 12px;
    margin-left: 5px;
    }

    .iwptp-ti-wishlist-growler[data-iwptp-icon="playlist"] .iwptp-ti-wishlist-growler__icon--heart,
    .iwptp-ti-wishlist-growler[data-iwptp-icon="heart"] .iwptp-ti-wishlist-growler__icon--playlist {
    display: none !important;
    }

    <?php
    wp_add_inline_style('iwptp', ob_get_clean(), 'after');

    // template
    ?>
    <script id="iwptp-ti-wishlist-growler-template" type="text/template">
        <a class="iwptp-ti-wishlist-growler" href="#">
      <span class="iwptp-ti-wishlist-growler__icon iwptp-ti-wishlist-growler__icon--heart">
        <?php echo wp_kses(iwptp_icon('heart'), iwptp_allowed_html_tags()); ?>
      </span>
      <span class="iwptp-ti-wishlist-growler__icon iwptp-ti-wishlist-growler__icon--playlist">
        <?php echo wp_kses(iwptp_icon('playlist'), iwptp_allowed_html_tags()); ?>
      </span>
      <span class="iwptp-ti-wishlist-growler__label iwptp-ti-wishlist-growler__label--item-added">
        {n} added
      </span>
      <span class="iwptp-ti-wishlist-growler__label iwptp-ti-wishlist-growler__label--view-wishlist">
        View wishlist →
      </span>
    </a>
  </script>
<?php
}

// AJAX search for woocommerce
if (
    !empty($_GET['dgwt_wcas']) &&
    !empty($_GET['s'])
) {
    add_action('wp_print_scripts', 'iwptp_permit_asw_param_js');
    add_filter('iwptp_permitted_params', 'iwptp_permit_asw_param_php', 100, 1);
    add_filter('posts_request', 'iwptp_asw_increase_post_limit', PHP_INT_MAX - 1, 2);  // hook in just before asw
    add_filter('iwptp_query_args', 'iwptp_asw_inherit_results', 5, 1);
}

// -- persist params js
function iwptp_permit_asw_param_js()
{
?>
    <script>
        if (typeof iwptp_persist_params === 'undefined') {
            var iwptp_persist_params = [];
        }
        iwptp_persist_params.push('dgwt_wcas', 'post_type', 's');
    </script>
<?php
}

// -- permit param in data-iwptp-query-string
function iwptp_permit_asw_param_php($params)
{
    if (!empty($_GET['dgwt_wcas'])) {
        $params[] = 'dgwt_wcas';
    }
    return $params;
}

// -- hook into main query and increase posts_per_page to -1
function iwptp_asw_increase_post_limit($request, $query)
{
    if (
        // empty( $query->query_vars['s'] ) ||
        !$query->is_search() ||
        (!$query->get('post_type') ||
            $query->get('post_type') !== 'product'
        ) ||
        !iwptp_archive__get_table_shortcode($query)
    ) {
        return $request;
    }

    $query->set('posts_per_page', PHP_INT_MAX); // do not use -1

    return $request;
}

function iwptp_asw_inherit_results($query_args)
{
    $table_data = iwptp_get_table_data();
    if (empty($table_data['query']['sc_attrs']['_archive'])) {
        return $query_args;
    }

    // first try to get from session
    $cached_post_ids = iwptp_session()->get('asw' . sanitize_text_field($_GET['s']));

    // if not possible, get from main query  
    if (!$cached_post_ids) {
        global $wp_query;
        // $cached_post_ids = array_column( $wp_query->posts, 'ID' ); 
        $cached_post_ids = $wp_query->query_vars['post__in'];
        iwptp_session()->set('asw' . sanitize_text_field($_GET['s']), $cached_post_ids);
    }

    // use the post ids
    if (empty($cached_post_ids)) {
        $query_args['post__in'] = [0];
    } else if (empty($query_args['post__in'])) {
        $query_args['post__in'] = $cached_post_ids;
    } else {
        $query_args['post__in'] = array_intersect($query_args['post__in'], $cached_post_ids);
    }

    return $query_args;
}

// Print archive table via shortcode
add_shortcode('iwptp_elementor', 'iwptp_print_archive_table');
add_shortcode('iwptp_beaver_builder', 'iwptp_print_archive_table');
add_shortcode('iwptp_archive_table', 'iwptp_print_archive_table');
function iwptp_print_archive_table()
{
    ob_start();
?>
    <?php if (have_posts()) : ?>

        <?php do_action('woocommerce_before_shop_loop'); ?>

        <?php
        if (!function_exists('iwptp_archive_override') || !iwptp_archive_override()) {
            woocommerce_product_loop_start();
            if (wc_get_loop_prop('total')) {
                while (have_posts()) {
                    the_post();
                    do_action('woocommerce_shop_loop');
                    wc_get_template_part('content', 'product');
                }
            }
            woocommerce_product_loop_end();
        }
        ?>

        <?php do_action('woocommerce_after_shop_loop'); ?>

    <?php else : ?>

        <?php do_action('woocommerce_no_products_found'); ?>

    <?php endif; ?>

<?php
    return ob_get_clean();
}

// WPML
//-- pass params through JS
add_action('wp_print_scripts', 'iwptp_permit_wmpl_param_js');
function iwptp_permit_wmpl_param_js()
{
?>
    <script>
        if (typeof iwptp_persist_params === 'undefined') {
            var iwptp_persist_params = [];
        }
        iwptp_persist_params.push('lang');
    </script>
    <?php
}
//-- pass params through PHP
add_filter('iwptp_permitted_params', 'iwptp_permit_wmpl_param_php', 100, 1);
function iwptp_permit_wmpl_param_php($params)
{
    if (!empty($_GET['lang'])) {
        $params[] = 'lang';
    }
    return $params;
}
//-- remove wpml interference in product cats
add_filter('woocommerce_shortcode_products_query', 'iwptp_remove_wpml_sc_hook', 9, 1);
function iwptp_remove_wpml_sc_hook($query_args)
{
    if (!empty($GLOBALS['sitepress'])) {
        iwptp_remove_filters_for_anonymous_class('woocommerce_shortcode_products_query', 'WCML_WC_Shortcode_Product_Category', 'translate_category', 10);
    }
    return $query_args;
}
//-- [iwptp_translate]
add_shortcode('iwptp_translate', 'iwptp_translate');
function iwptp_translate($atts = [])
{
    $locale = strtolower(get_locale());
    $string = '';
    if (!empty($atts['default'])) {
        $string = $atts['default'];
    }
    if (!empty($atts[$locale])) {
        $string = $atts[$locale];
    }
    return $string;
}

// remove action and filter hook handlers where class instance is not captured in a var
function iwptp_remove_filters_for_anonymous_class($hook_name = '', $class_name = '', $method_name = '', $priority = 0)
{
    global $wp_filter;
    // Take only filters on right hook name and priority
    if (!isset($wp_filter[$hook_name][$priority]) || !is_array($wp_filter[$hook_name][$priority])) {
        return false;
    }
    // Loop on filters registered
    foreach ((array) $wp_filter[$hook_name][$priority] as $unique_id => $filter_array) {
        // Test if filter is an array ! (always for class/method)
        if (isset($filter_array['function']) && is_array($filter_array['function'])) {
            // Test if object is a class, class and method is equal to param !
            if (is_object($filter_array['function'][0]) && get_class($filter_array['function'][0]) && get_class($filter_array['function'][0]) == $class_name && $filter_array['function'][1] == $method_name) {
                // Test for WordPress >= 4.7 WP_Hook class (https://make.wordpress.org/core/2016/09/08/wp_hook-next-generation-actions-and-filters/)
                if (is_a($wp_filter[$hook_name], 'WP_Hook')) {
                    unset($wp_filter[$hook_name]->callbacks[$priority][$unique_id]);
                } else {
                    unset($wp_filter[$hook_name][$priority][$unique_id]);
                }
            }
        }
    }
    return false;
}

// WooCommerce Quick View
// -- shortcode
add_shortcode('iwptp_quick_view', 'iwptp_quick_view');
add_shortcode('iwptp_wc_quick_view', 'iwptp_quick_view'); // legacy
function iwptp_quick_view($atts = [])
{
    ob_start();
    if (class_exists('WC_Quick_View')) {
        $instance = new WC_Quick_View();
        echo wp_kses($instance->quick_view_button(), iwptp_allowed_html_tags());
    } else if (class_exists('wcqv_frontend')) {
        global $post;
        $options = get_option('wcqv_options');
    ?>
        <span class="woocommerce">
            <a data-product-id="<?php echo intval($post->ID); ?>" class="quick_view button">
                <span><?php echo esc_html($options['button_lable']); ?></span>
            </a>
        </span>
    <?php

    } else if (class_exists('Barn2\Plugin\WC_Quick_View_Pro\Quick_View_Content')) {
        global $product;
        echo do_shortcode('<div class="woocommerce">[quick_view id="' . $product->get_id() . '"]</div>');
    }
    return ob_get_clean();
}
// -- style
add_action('wp_enqueue_scripts', 'iwptp_quick_view__style');
function iwptp_quick_view__style()
{
    if (class_exists('WC_Quick_View')) {
        ob_start();
    ?>
        .iwptp .quick-view-button {
        background: rgba(0, 0, 0, 0.04);
        padding: 8px 10px 8px 14px;
        }

        .iwptp .quick-view-button > * {
        vertical-align: middle;
        }
    <?php
        wp_add_inline_style('iwptp', ob_get_clean(), 'after');
    }

    if (class_exists('wcqv_frontend')) {
        ob_start();
    ?>
        .iwptp-wc-quick-view .wcqv_prev,
        .iwptp-wc-quick-view .wcqv_next {
        display: none !important;
        }

        .iwptp .woocommerce .quick_view {
        background: #FFF176;
        color: #444;
        }
    <?php
        wp_add_inline_style('iwptp', ob_get_clean(), 'after');
    }
}

// -- script
add_action('wp_enqueue_scripts', 'iwptp_quick_view__script');
function iwptp_quick_view__script()
{
    if (class_exists('WC_Quick_View') && isset($GLOBALS['WC_Quick_View']) && method_exists($GLOBALS['WC_Quick_View'], 'scripts')) {
        $GLOBALS['WC_Quick_View']->scripts();
        wp_enqueue_script('woocommerce-quick-view');
    }

    if (class_exists('WC_Quick_View') || class_exists('wcqv_frontend') || class_exists('Barn2\Plugin\WC_Quick_View_Pro\Quick_View_Content')) {

        ob_start();
    ?>

        jQuery(function($){
        // body needs classes: 'woocommerce' & 'iwptp-wc-quick-view' during modal
        $('body').on('click', '.iwptp-table .quick_view.button', function(){
        var $body = $('body'),
        has_wc = $('body').hasClass('woocommerce'),
        classes = 'iwptp-wc-quick-view ' + ( has_wc ? '' : ' woocommerce ' );

        $body.addClass( classes );

        $(document).one('closed', '.remodal', function(){
        $body.removeClass(classes);
        });
        })

        // refresh after submit
        $('body').on('submit', '.cart', function(){
        var $form = $(this),
        $body = $form.closest('body');

        if( ! $body.hasClass('iwptp-wc-quick-view') ){
        return;
        }

        $form.attr('action', '');
        })

        // trigger from title / image
        $('body').on('click', '.iwptp-quick-view-trigger--title .iwptp-title, .iwptp-quick-view-trigger--product-image .iwptp-product-image-wrapper', function(e){
        var $this = $(this),
        product_id = $this.closest('.iwptp-row').attr('data-iwptp-product-id'),
        $row = $this.closest('.iwptp-row'),
        $body = $('body'),
        $table = $this.closest('.iwptp-table');


        if( $row.hasClass('iwptp-quick-view-trigger__disabled-for-product') ){
        return;
        }

        e.preventDefault();

        <?php if (class_exists('WC_Quick_View')) : ?>
            var $qv = $('<a class="quick-view-button button" data-product_id="'+ product_id +'" href="?wc-api=WC_Quick_View&product='+ product_id +'&width=90%25&height=90%25&ajax=true">');
            <?php elseif (class_exists('wcqv_frontend')) : ?>
                var $qv = $('<a data-product-id="'+ product_id +'" class="quick_view button">');
                <?php elseif (class_exists('Barn2\Plugin\WC_Quick_View_Pro\Quick_View_Content')) : ?>
                    var $qv = $('<a href="#" data-product_id="'+ product_id +'" data-action="quick-view" class="wc-quick-view-button with-icon button btn alt shortcode">');
                    <?php endif; ?>
                    if( typeof $qv !== 'undefined' ){
                    $qv
                    .insertAfter($this)
                    .click()
                    .remove();
                    }
                    })

                    })
                    <?php
                    wp_add_inline_script('jquery', ob_get_clean(), 'after');
                }
            }

            // -- trigger from image / title
            add_filter('iwptp_container_html_class', 'iwptp_quick_view__trigger_container_html_class');
            function iwptp_quick_view__trigger_container_html_class($html_class)
            {
                $table_data = iwptp_get_table_data();
                if (!empty($table_data['query']['sc_attrs']['quick_view_trigger'])) {
                    $trigger = array_map('trim', explode(',', strtolower(trim($table_data['query']['sc_attrs']['quick_view_trigger']))));
                    foreach ($trigger as $t) {
                        if (in_array($t, array('title', 'product image'))) {
                            $html_class .= ' iwptp-quick-view-trigger--' . str_replace(' ', '-', $t) . ' ';
                        }
                    }
                }

                return $html_class;
            }

            // -- conditional trigger from image / title
            add_filter('iwptp_product_row_html_class', 'iwptp_quick_view__trigger_container__conditional');
            function iwptp_quick_view__trigger_container__conditional($html_class)
            {
                $table_data = iwptp_get_table_data();
                $sc_attrs = $table_data['query']['sc_attrs'];

                if (!empty($sc_attrs['quick_view_trigger'])) {
                    global $product;
                    $permit = true;
                    $product_slugs = wp_get_post_terms($product->get_id(), 'product_cat', array('fields' => 'slugs'));

                    // permitted categories
                    if (!empty($sc_attrs['quick_view_category'])) {
                        $permitted_slugs = iwptp_include_descendant_slugs(
                            array_map(
                                'trim',
                                explode(',', $sc_attrs['quick_view_category'])
                            ),
                            'product_cat'
                        );

                        $common_slugs = array_intersect($product_slugs, $permitted_slugs);
                        if (!count($common_slugs)) {
                            $permit = false;
                        }
                    }

                    // exclude categories
                    if (!empty($sc_attrs['quick_view_exclude_category'])) {
                        $exclude_slugs = iwptp_include_descendant_slugs(
                            array_map(
                                'trim',
                                explode(',', $sc_attrs['quick_view_exclude_category'])
                            ),
                            'product_cat'
                        );

                        $common_slugs = array_intersect($product_slugs, $exclude_slugs);
                        if (count($common_slugs)) {
                            $permit = false;
                        }
                    }

                    // permitted product type
                    if (!empty($sc_attrs['quick_view_product_type'])) {
                        $permitted_product_type = array_map('trim', explode(',', $sc_attrs['quick_view_product_type']));

                        if (!in_array($product->get_type(), $permitted_product_type)) {
                            $permit = false;
                        }
                    }

                    // exclude product type
                    if (!empty($sc_attrs['quick_view_exclude_product_type'])) {
                        $exclude_product_type = array_map('trim', explode(',', $sc_attrs['quick_view_exclude_product_type']));

                        if (in_array($product->get_type(), $exclude_product_type)) {
                            $permit = false;
                        }
                    }

                    if (!$permit) {
                        $html_class .= ' iwptp-quick-view-trigger__disabled-for-product ';
                    }
                }

                return $html_class;
            }

            // YITH Request a Quote
            // -- disable ajax
            // add_filter( 'iwptp_shortcode_attributes', 'iwptp_yith_ajax_disable', 100, 1 );
            function iwptp_yith_ajax_disable($atts)
            {
                // -- yith request a quote
                if (function_exists('YITH_YWRAQ_Frontend')) {
                    $atts['disable_ajax'] = true;
                }

                return $atts;
            }
            // -- shortcode
            add_shortcode('iwptp_yith_quote', 'iwptp_yith_quote');
            function iwptp_yith_quote($atts = [])
            {
                if (function_exists('YITH_YWRAQ_Frontend') && defined('YITH_YWRAQ_PREMIUM')) {

                    global $product;

                    if (!apply_filters('yith_ywraq_before_print_button', true, $product)) {
                        return;
                    }

                    if ($product->get_type() == 'grouped') {
                        return;
                    }

                    if (gettype($atts) == 'string') {
                        $atts = [];
                    }

                    if (
                        empty($atts['style']) ||
                        !in_array($atts['style'], array('icon', 'link', 'button', 'yith'))
                    ) {
                        $atts['style'] = 'link';
                    }

                    ob_start();

                    if ($atts['style'] == 'yith') {
                        YITH_YWRAQ_Frontend()->print_button();
                    } else {

                        $out_of_stock = '';
                        if (
                            $product->get_type() !== 'variable' &&
                            $product->get_stock_status() !== 'instock'
                        ) {
                            $out_of_stock = 'iwptp-out-of-stock';
                        }

                        $variations = YITH_Request_Quote()->raq_variations;

                        if ($variations) {
                            $variations = implode(', ', $variations);
                        } else {
                            $variations = '';
                        }

                        $state = '';
                        if (
                            YITH_Request_Quote()->exists($product->get_id()) &&
                            $product->get_type() !== 'variable'
                        ) {
                            $state = 'iwptp-yith-ywraq--added';
                        }

                        if ($product->get_type() == 'variable') {
                            $default_variation = iwptp_get_default_variation($product);
                            if (
                                $default_variation &&
                                $default_variation['type'] == 'complete_match' &&
                                in_array($default_variation['variation_id'], YITH_Request_Quote()->raq_variations)
                            ) {
                                $state = 'iwptp-yith-ywraq--added';
                            }
                        }

                        if ($product->get_type() == 'variation') {
                            if (in_array($product->get_id(), YITH_Request_Quote()->raq_variations)) {
                                $state = 'iwptp-yith-ywraq--added';
                            }
                        }

                        if (!$state) {
                            $state = "iwptp-yith-ywraq--default";
                        }

                    ?>
                        <div class="iwptp-yith-ywraq iwptp-yith-ywraq--<?php echo esc_attr($atts['style']); ?> <?php echo esc_attr($out_of_stock); ?> <?php echo esc_attr($state); ?>" data-variation="<?php echo esc_attr($variations); ?>" data-wp_nonce="<?php echo esc_attr(wp_create_nonce('add-request-quote-' . $product->get_id())); ?>">
                            <?php
                            $add_to_quote_label = "Add to quote";
                            $product_added_label = "Product added";

                            $locale = strtolower(get_locale());

                            if (!empty($atts['add_to_quote_' . $locale])) {
                                $add_to_quote_label = $atts['add_to_quote_' . $locale];
                            } else if (!empty($atts['add_to_quote'])) {
                                $add_to_quote_label = $atts['add_to_quote'];
                            }

                            if (!empty($atts['product_added_' . $locale])) {
                                $product_added_label = $atts['product_added_' . $locale];
                            } else if (!empty($atts['product_added'])) {
                                $product_added_label = $atts['product_added'];
                            }

                            $default_image_width = $adding_image_width = $added_image_width = 0;
                            if (!empty($atts['default_image_width'])) {
                                $default_image_width = ' style="width: ' . intval($atts['default_image_width']) . 'px;" ';
                            }
                            if (!empty($atts['adding_image_width'])) {
                                $adding_image_width = ' style="width:' . intval($atts['adding_image_width']) . 'px;" ';
                            }
                            if (!empty($atts['added_image_width'])) {
                                $added_image_width = ' style="width:' . intval($atts['added_image_width']) . 'px;" ';
                            }

                            ?>
                            <!-- default -->
                            <a class="iwptp-yith-ywraq__content iwptp-yith-ywraq__content--default" href="/">
                                <?php if ($atts['style'] == 'link') : ?>
                                    <?php
                                    if (empty($atts['default_image'])) {
                                        echo wp_kses(iwptp_icon('plus-circle'), iwptp_allowed_html_tags());
                                    } else {
                                        echo '<img class="iwptp-yith-ywraq-image--default" ' . esc_attr($default_image_width) . ' src="' . esc_url($atts['default_image']) . '"/>';
                                    }
                                    ?><span><?php echo esc_html($add_to_quote_label); ?></span>
                                <?php elseif ($atts['style'] == 'button') : ?>
                                    <?php
                                    if (empty($atts['default_image'])) {
                                        echo wp_kses(iwptp_icon('plus-circle'), iwptp_allowed_html_tags());
                                    } else {
                                        echo '<img class="iwptp-yith-ywraq-image--default" ' . esc_attr($default_image_width) . ' src="' . esc_url($atts['default_image']) . '"/>';
                                    }
                                    ?><span><?php echo esc_html($add_to_quote_label); ?></span>
                                <?php elseif ($atts['style'] == 'icon') : ?>
                                    <?php
                                    if (empty($atts['default_image'])) {
                                        echo wp_kses(iwptp_icon('file'), iwptp_allowed_html_tags());
                                        echo wp_kses(iwptp_icon('plus-circle'), iwptp_allowed_html_tags());
                                    } else {
                                        echo '<img class="iwptp-yith-ywraq-image--default" ' . esc_attr($default_image_width) . ' src="' . esc_url($atts['default_image']) . '"/>';
                                    }
                                    ?>
                                <?php endif; ?>
                            </a>
                            <!-- adding -->
                            <span class="iwptp-yith-ywraq__content iwptp-yith-ywraq__content--adding">
                                <?php if ($atts['style'] == 'link') : ?>
                                    <?php
                                    if (empty($atts['adding_image'])) {
                                        echo wp_kses(iwptp_icon('loader'), iwptp_allowed_html_tags());
                                    } else {
                                        echo '<img class="iwptp-yith-ywraq-image--adding" ' . esc_attr($adding_image_width) . ' src="' . esc_url($atts['adding_image']) . '"/>';
                                    }
                                    ?><span><?php echo esc_html($add_to_quote_label); ?></span>
                                <?php elseif ($atts['style'] == 'button') : ?>
                                    <?php
                                    if (empty($atts['adding_image'])) {
                                        echo wp_kses(iwptp_icon('loader'), iwptp_allowed_html_tags());
                                    } else {
                                        echo '<img class="iwptp-yith-ywraq-image--adding" ' . esc_attr($adding_image_width) . ' src="' . esc_url($atts['adding_image']) . '"/>';
                                    }
                                    ?><span><?php echo esc_html($add_to_quote_label); ?></span>
                                <?php elseif ($atts['style'] == 'icon') : ?>
                                    <?php
                                    if (empty($atts['adding_image'])) {
                                        echo wp_kses(iwptp_icon('file'), iwptp_allowed_html_tags());
                                        echo wp_kses(iwptp_icon('loader'), iwptp_allowed_html_tags());
                                    } else {
                                        echo '<img class="iwptp-yith-ywraq-image--adding" ' . esc_attr($adding_image_width) . ' src="' . esc_url($atts['adding_image']) . '"/>';
                                    }
                                    ?>
                                <?php endif; ?>
                            </span>
                            <!-- added -->
                            <a href="<?php echo esc_url(YITH_Request_Quote()->get_raq_page_url()); ?>" class="iwptp-yith-ywraq__content iwptp-yith-ywraq__content--added">
                                <?php if ($atts['style'] == 'link') : ?>
                                    <?php
                                    if (empty($atts['added_image'])) {
                                        echo wp_kses(iwptp_icon('check'), iwptp_allowed_html_tags());
                                    } else {
                                        echo '<img class="iwptp-yith-ywraq-image--added" ' . esc_attr($added_image_width). ' src="' . esc_url($atts['added_image']) . '"/>';
                                    }
                                    ?><span><?php echo esc_html($product_added_label); ?></span><?php echo wp_kses(iwptp_icon('chevron-right'), iwptp_allowed_html_tags()); ?>
                                <?php elseif ($atts['style'] == 'button') : ?>
                                    <?php
                                    if (empty($atts['added_image'])) {
                                        echo wp_kses(iwptp_icon('check'), iwptp_allowed_html_tags());
                                    } else {
                                        echo '<img class="iwptp-yith-ywraq-image--added" ' . esc_attr($added_image_width). ' src="' . esc_url($atts['added_image']) . '"/>';
                                    }
                                    ?><span><?php echo esc_html($product_added_label); ?></span><?php echo wp_kses(iwptp_icon('chevron-right'), iwptp_allowed_html_tags()); ?>
                                <?php elseif ($atts['style'] == 'icon') : ?>
                                    <?php
                                    if (empty($atts['added_image'])) {
                                        echo wp_kses(iwptp_icon('file'), iwptp_allowed_html_tags());
                                        echo wp_kses(iwptp_icon('check'), iwptp_allowed_html_tags());
                                        echo wp_kses(iwptp_icon('chevron-right'), iwptp_allowed_html_tags());
                                    } else {
                                        echo '<img class="iwptp-yith-ywraq-image--added" ' . esc_attr($added_image_width). ' src="' . esc_url($atts['added_image']) . '"/>';
                                    }
                                    ?>
                                <?php endif; ?>
                            </a>
                        </div>
                <?php
                    }

                    return ob_get_clean();
                }
            }

            // Product Add-ons
            add_action('iwptp_product_row_html_class', 'iwptp_product_addon_html_class', 100, 2);
            function iwptp_product_addon_html_class($html_class, $product)
            {
                $has_addons = false;

                // official woocommerce product addons
                if (
                    class_exists('WC_Product_Addons_Helper') &&
                    method_exists('WC_Product_Addons_Helper', 'get_product_addons')
                ) {
                    $product_addons = WC_Product_Addons_Helper::get_product_addons($product->get_id());
                    if (is_array($product_addons) && count($product_addons) > 0) {
                        $has_addons = true;
                    }
                } else if (function_exists('get_product_addons')) {
                    $product_addons = get_product_addons($product->get_id());
                    if (is_array($product_addons) && count($product_addons) > 0) {
                        $has_addons = true;
                    }
                }

                // woocommerce custom product addons  
                if (
                    function_exists('wcpa_is_wcpa_product') &&
                    wcpa_is_wcpa_product($product->get_id())
                ) {
                    $has_addons = true;
                }

                if ($has_addons) {
                    $html_class .= ' iwptp-product-has-addons ';
                }

                return $html_class;
            }

            // -- for variable products
            add_action('iwptp_before_loop', 'iwptp_product_addon_compatibility');
            function iwptp_product_addon_compatibility()
            {
                if (!empty($GLOBALS['Product_Addon_Display'])) {
                    remove_action('woocommerce_before_variations_form', array($GLOBALS['Product_Addon_Display'], 'reposition_display_for_variable_product'));
                }
            }

            // Hide Price Until Login

            add_filter('iwptp_element', 'iwptp_hide_price_until_login');
            function iwptp_hide_price_until_login($el)
            {
                if (!iwptp_hide_price_until_login_permission()) {
                    if (
                        in_array($el['type'], array('price', 'quantity')) ||
                        ($el['type'] == 'button' &&
                            in_array($el['link'], array('cart_ajax', 'cart_refresh', 'cart_redirect', 'cart_checkout'))
                        ) ||
                        ($el['type'] == 'sorting' &&
                            $el['orderby'] == 'price'
                        )
                    ) {
                        $el = [];
                    } else if ($el['type'] == 'select_variation') {
                        $el['hide_price'] = true;
                        $el['hide_stock'] = true;
                    }
                }

                return $el;
            }

            add_filter('iwptp_get_variations', 'iwptp_hide_variation_price_until_login', 1000, 1);
            function iwptp_hide_variation_price_until_login($arr = [])
            {
                if (!iwptp_hide_price_until_login_permission()) {
                    foreach ($arr as &$variation) {
                        $variation['display_price'] = $variation['display_regular_price'] = 0;
                    }
                }

                return $arr;
            }

            add_filter('iwptp_cart_total_quantity', 'iwptp_hide_cart_widget_until_login');
            add_filter('iwptp_cart_total_price', 'iwptp_hide_cart_widget_until_login');
            function iwptp_hide_cart_widget_until_login($figure)
            {
                if (!iwptp_hide_price_until_login_permission()) {
                    $figure = false;
                }

                return $figure;
            }

            function iwptp_hide_price_until_login_permission()
            {
                if (!function_exists('is_plugin_active')) {
                    include_once(ABSPATH . 'wp-admin/includes/plugin.php');
                }

                if (is_plugin_active('hide-price-until-login/hide-price.php')) {
                    $mode = get_option('ced_hpul_enable_hide_price');

                    if (
                        $mode == 'Hide_Price_Until_Login_Features' &&
                        !is_user_logged_in()
                    ) {
                        return false;
                    } else if (
                        $mode == 'Hide_Price_using_Password_Features' &&
                        (empty($_SESSION['ced_hp_password_matched']) ||
                            $_SESSION['ced_hp_password_matched'] != get_option('ced_hp_password_for_price')
                        )
                    ) {
                        return false;
                    } else if (
                        $mode == 'Hide_Price_for_roles' &&
                        $roles = get_option('ced_hpr_role', [])
                    ) {
                        foreach (wp_get_current_user()->roles as $role) {
                            if (in_array($role, $roles)) {
                                return true;
                            }
                        }

                        return false;
                    }
                }

                return true;
            }

            // Advanced Woo Search
            $iwptp_permit_aws_influence = true;

            if ($iwptp_permit_aws_influence) {
                add_action('init', 'iwptp_aws_do_influence');
            } else {
                add_action('init', 'iwptp_aws_remove_influence');
            }

            function iwptp_aws_remove_influence()
            {
                iwptp_remove_filters_for_anonymous_class('posts_request', 'AWS_Search_Page', 'filter_posts_request', 10);
                iwptp_remove_filters_for_anonymous_class('the_posts', 'AWS_Search_Page', 'filter_the_posts', 10);
                iwptp_remove_filters_for_anonymous_class('pre_get_posts', 'AWS_Search_Page', 'action_pre_get_posts', 5);
                iwptp_remove_filters_for_anonymous_class('pre_get_posts', 'AWS_Search_Page', 'pre_get_posts_overwrite', 999);
                iwptp_remove_filters_for_anonymous_class('found_posts_query', 'AWS_Search_Page', 'filter_found_posts_query', 5);
                iwptp_remove_filters_for_anonymous_class('woocommerce_layered_nav_link', 'AWS_Search_Page', 'woocommerce_layered_nav_link');
                iwptp_remove_filters_for_anonymous_class('posts_pre_query', 'AWS_Search_Page', 'posts_pre_query', 10);
                iwptp_remove_filters_for_anonymous_class('body_class', 'AWS_Search_Page', 'body_class', 999);
            }

            function iwptp_aws_do_influence()
            {
                if (!class_exists('AWS_Search')) {
                    return;
                }

                // suppress IWPTPL search
                add_action('iwptp_before_apply_user_filters', 'iwptp_aws_suppress_default_search', 100, 1);
                // capture search results
                add_filter('aws_search_results_products_ids', 'iwptp_aws_search_results_products_ids', 100, 2);
                // use search results
                add_filter('iwptp_query_args', 'iwptp_query_args_aws', 200, 1);
                // persist params js
                add_action('wp_print_scripts', 'iwptp_permit_aws_param_js');
                // permit param in data-iwptp-query-string
                add_filter('iwptp_permitted_params', 'iwptp_permit_aws_param_php', 100, 1);
                // permit param across AJAX
                add_filter('iwptp_permitted_shortcode_attributes', 'iwptp_permitted_shortcode_attributes_aws', 100, 1);
            }

            // -- suppress IWPTPL native search filter
            function iwptp_aws_suppress_default_search($data)
            {
                $remove = [];
                foreach ($GLOBALS['iwptp_user_filters'] as $index => &$filter) {
                    if ($filter['filter'] === 'search') {
                        $remove[] = $index;
                    }
                }

                foreach ($remove as $index) {
                    unset($GLOBALS['iwptp_user_filters'][$index]);
                }
            }

            // -- capture search results
            function iwptp_aws_search_results_products_ids($post_ids, $s)
            {
                iwptp_session()->set('iwptp_aws', $post_ids);
                return $post_ids;
            }

            // -- use search results
            function iwptp_query_args_aws($args)
            {
                if (
                    !empty(iwptp_session()->get('iwptp_aws')) &&
                    !empty($_GET['type_aws'])
                ) {

                    if (empty($args['post__in'])) {
                        $args['post__in'] = iwptp_session()->get('iwptp_aws');
                    } else {
                        $args['post__in'] = array_intersect(iwptp_session()->get('iwptp_aws'), $args['post__in']);
                    }
                }

                return $args;
            }

            // -- persist params js
            function iwptp_permit_aws_param_js()
            {
                ?>
                <script>
                    if (typeof iwptp_persist_params === 'undefined') {
                        var iwptp_persist_params = [];
                    }
                    iwptp_persist_params.push('type_aws', 'post_type', 's');
                </script>
            <?php
            }

            // -- permit param in data-iwptp-query-string
            function iwptp_permit_aws_param_php($params)
            {
                if (!empty($_GET['type_aws'])) {
                    $params[] = 'type_aws';
                }
                return $params;
            }

            // -- permit param across AJAX
            function iwptp_permitted_shortcode_attributes_aws($permitted)
            {
                $permitted[] = 'type_aws';
                return $permitted;
            }

            // print upsell skus
            add_shortcode('iwptp_upsell_skus', 'iwptp_upsell_skus');
            function iwptp_upsell_skus()
            {
                global $product;

                if ($upsells = $product->get_upsell_ids()) {
                    $mkp = '';
                    $query = new WP_Query(array(
                        'post__in' => $upsells,
                        'post_type' => 'product',
                    ));

                    if ($query->have_posts()) {
                        while ($query->have_posts()) {
                            $query->the_post();
                            global $post;
                            $product = wc_get_product($post->ID);
                            $mkp .= ', ' . $product->get_sku();
                        }
                    } else {
                        $mkp .= 'none';
                    }

                    if ($mkp) {
                        return '<span class="iwptp-upsell-skus">' . trim($mkp, ' ,') . '</span>';
                    }
                }
            }

            // WCFM
            add_shortcode('iwptp_wcfm_store', 'iwptp_wcfm_store');
            function iwptp_wcfm_store($atts)
            {
                if (!function_exists('wcfm_get_vendor_id_by_post')) {
                    return;
                }

                global $product;

                if ($vendor_id = (int) wcfm_get_vendor_id_by_post($product->get_id())) {
                    $output = '<span class="iwptp-wcfm-store-name">' . wcfm_get_vendor_store_name($vendor_id) . '</span>';

                    if (!empty($atts['link'])) {
                        $target = '_self';
                        if (
                            !empty($atts['open_new_page']) &&
                            strtolower(trim($atts['open_new_page'])) === 'true'
                        ) {
                            $target = '_blank';
                        }

                        $output = '<a class="iwptp-wcfm-store-url" href="' . wcfmmp_get_store_url($vendor_id)  . '" target="' . $target . '">' . $output . '</a>';
                    }

                    return $output;
                }
            }

            // Advanced dynamic pricing for WC

            // -- on sale
            add_filter('iwptp_product_is_on_sale', 'iwptp_adp__filter_hook__product_is_on_sale', 100, 2);
            function iwptp_adp__filter_hook__product_is_on_sale($on_sale, $product)
            {
                $adp_product = iwptp_adp__get_processed_product($product);

                if (
                    $adp_product &&
                    method_exists($adp_product, 'getCalculatedPrice')
                ) {
                    return $adp_product->isDiscounted();
                }

                return $on_sale;
            }

            // -- sale price
            add_filter('iwptp_product_get_sale_price', 'iwptp_adp__filter_hook__product_sale_price', 100, 2);
            function iwptp_adp__filter_hook__product_sale_price($sale_price, $product)
            {
                $adp_product = iwptp_adp__get_processed_product($product);

                if (
                    $adp_product &&
                    method_exists($adp_product, 'getCalculatedPrice')
                ) {
                    return $adp_product->getCalculatedPrice();
                }

                return $sale_price;
            }

            // -- regular price
            add_filter('iwptp_product_get_regular_price', 'iwptp_adp__filter_hook__product_regular_price', 100, 2);
            function iwptp_adp__filter_hook__product_regular_price($regular_price, $product)
            {
                $adp_product = iwptp_adp__get_processed_product($product);

                if (
                    $adp_product &&
                    method_exists($adp_product, 'getOriginalPrice')
                ) {
                    return $adp_product->getOriginalPrice();
                }

                return $regular_price;
            }

            // -- lowest price
            add_filter('iwptp_product_get_lowest_price', 'iwptp_adp__filter_hook__variable_product_lowest_price', 100, 2);
            function iwptp_adp__filter_hook__variable_product_lowest_price($lowest_price, $product)
            {
                if ($adp_product = iwptp_adp__get_processed_product($product)) {
                    return $adp_product->getLowestPrice();
                }

                return $lowest_price;
            }

            // -- highest price
            add_filter('iwptp_product_get_highest_price', 'iwptp_adp__filter_hook__variable_product_highest_price', 100, 2);
            function iwptp_adp__filter_hook__variable_product_highest_price($highest_price, $product)
            {
                if ($adp_product = iwptp_adp__get_processed_product($product)) {
                    return $adp_product->getHighestPrice();
                }

                return $highest_price;
            }

            // -- variations
            add_filter('iwptp_get_variations', 'iwptp_adp__filter_hook__set_variation_prices', 100, 2);
            function iwptp_adp__filter_hook__set_variation_prices($variations = [])
            {
                foreach ($variations as &$variation) {
                    if (empty($variation['display_price'])) {
                        $is_on_sale = false;
                    } else {
                        $is_on_sale = apply_filters('iwptp_product_is_on_sale', !!($variation['display_price'] - $variation['display_regular_price']), wc_get_product($variation['variation_id']));
                    }

                    if ($is_on_sale) {
                        $variation['display_price'] = iwptp_adp__filter_hook__product_sale_price($variation['display_price'], $variation['variation_id']);
                        $variation['display_regular_price'] = iwptp_adp__filter_hook__product_regular_price($variation['display_regular_price'], $variation['variation_id']);
                    }
                }

                return $variations;
            }

            // -- helper
            function iwptp_adp__get_processed_product($product)
            {
                if (gettype($product) === 'integer') {
                    $product = wc_get_product($product);
                }

                if (function_exists('adp_functions')) {
                    $use_empty_cart = true;
                    $min_price = $product->get_min_purchase_quantity();

                    $adp_fn = adp_functions();
                    $processed_product = false;

                    if (
                        gettype($adp_fn) === 'object' &&
                        method_exists($adp_fn, 'calculateProduct')
                    ) {
                        $processed_product = adp_functions()->calculateProduct($product, $min_price, $use_empty_cart);
                    }

                    if ($processed_product) {
                        return $processed_product;
                    }
                }

                return false;
            }

            // -- bulk discount table
            add_action('wp_enqueue_scripts', 'iwptp_adp__style');
            function iwptp_adp__style()
            {
                if (!function_exists('adp_functions')) {
                    return;
                }
                ob_start();
            ?>
                .iwptp .wdp_bulk_table_content .wdp_pricing_table_caption,
                .iwptp .wdp_bulk_table_content .wdp_pricing_table_footer {
                display: none;
                }

                .iwptp .wdp_pricing_table {
                margin: 0 !important;
                width: auto !important;
                }
                <?php

                wp_add_inline_style('iwptp', ob_get_clean(), 'after');
            }

            // WooCommerce Name Your Price

            // -- row html class
            add_action('iwptp_product_row_html_class', 'iwptp_product_name_your_price_html_class', 100, 2);
            function iwptp_product_name_your_price_html_class($html_class, $product)
            {
                if (iwptp_is_name_your_price()) {
                    $html_class .= ' iwptp-product-has-name-your-price ';
                }

                return $html_class;
            }

            // -- shortcode
            add_shortcode('iwptp_name_your_price', 'iwptp_shortcode__name_your_price');
            function iwptp_shortcode__name_your_price($atts)
            {
                if (!iwptp_is_name_your_price()) {
                    return;
                }

                global $product;

                $atts = shortcode_atts(array(
                    'field' => 'input',
                    'initial_value' => '',
                    'template' => '',
                ), $atts, 'iwptp_name_your_price');

                $markup = '';
                $min_price = WC_Name_Your_Price_Helpers::get_minimum_price($product);
                $max_price = WC_Name_Your_Price_Helpers::get_maximum_price($product);
                $suggested_price = WC_Name_Your_Price_Helpers::get_suggested_price($product);

                if ($atts['field'] == 'input') {

                    $GLOBALS['iwptp_nyp_input_field_flag'] = true;

                    $min = $min_price ? 'min="' . $min_price . '"' : 'min="0"';
                    $max = $max_price ? 'max="' . $max_price . '"' : '';

                    $initial_value = '';
                    $initial_value_field = '';

                    if (!empty($atts['initial_value'])) {
                        $_initial_value_field = strtolower(trim($atts['initial_value']));

                        if (in_array($_initial_value_field, array('minimum', 'maximum', 'suggested'))) {
                            $initial_value_field = $_initial_value_field;

                            switch ($initial_value_field) {
                                case 'minimum':
                                    $initial_value = $min_price;
                                    break;

                                case 'maximum':
                                    $initial_value = $max_price;
                                    break;

                                case 'suggested':
                                    $initial_value = $suggested_price;
                                    break;
                            }
                        }
                    }

                    $markup = '<span class="iwptp-name-your-price-input-wrapper">
    <input 
      type="number" 
      autocomplete="off"      
      class="iwptp-name-your-price iwptp-name-your-price--input"
      value="' . $initial_value . '"
      min="' . ($min_price ? $min_price : 0) . '"
      max="' . ($max_price ? $max_price : '') . '"
      data-iwptp-product-name="' . esc_attr($product->get_name()) . '"
      data-iwptp-nyp-minimum-price="' . $min_price . '"
      data-iwptp-nyp-maximum-price="' . $max_price . '"
      data-iwptp-nyp-suggested-price="' . $suggested_price . '"
      data-iwptp-nyp-initial-value-field="' . $initial_value_field . '"
    />
    <span 
      class="iwptp-name-your-price-input-error-message iwptp-name-your-price-input-error-message--min-price" 
      data-iwptp-error-message-template="Min: [min]"
    >
      Min: [min]
    </span>
    <span 
      class="iwptp-name-your-price-input-error-message iwptp-name-your-price-input-error-message--max-price"
      data-iwptp-error-message-template="Max: [max]"      
    >
      Max: [max]
    </span>
    </span>';
                } else if ($atts['field'] == 'minimum') {
                    if (!$atts['template']) {
                        $atts['template'] = '{n}';
                    }

                    if (!$min_price) {
                        if ($product->get_type() === 'variable') {
                            $hide = ' iwptp-hide ';
                        } else {
                            return;
                        }
                    } else {
                        $hide = '';
                    }

                    $min = '<span class="iwptp-name-your-price--minimum__amount">' . iwptp_price($min_price) . '</span>';
                    $inner = str_replace('{n}', $min, $atts['template']);
                    $markup = '<span class="iwptp-name-your-price iwptp-name-your-price--minimum ' . $hide . '">' . $inner . '</span>';
                } else if ($atts['field'] == 'maximum') {
                    if (!$atts['template']) {
                        $atts['template'] = '{n}';
                    }

                    if (!$max_price) {
                        if ($product->get_type() === 'variable') {
                            $hide = ' iwptp-hide ';
                        } else {
                            return;
                        }
                    } else {
                        $hide = '';
                    }

                    $max = '<span class="iwptp-name-your-price--maximum__amount">' . iwptp_price($max_price) . '</span>';
                    $inner = str_replace('{n}', $max, $atts['template']);
                    $markup = '<span class="iwptp-name-your-price iwptp-name-your-price--maximum ' . $hide . '">' . $inner . '</span>';
                } else if ($atts['field'] == 'suggested') {
                    if (!$atts['template']) {
                        $atts['template'] = '{n}';
                    }

                    if (!$suggested_price) {
                        if ($product->get_type() === 'variable') {
                            $hide = ' iwptp-hide ';
                        } else {
                            return;
                        }
                    } else {
                        $hide = '';
                    }

                    $suggested = '<span class="iwptp-name-your-price--suggested__amount">' . iwptp_price($suggested_price) . '</span>';
                    $inner = str_replace('{n}', $suggested, $atts['template']);
                    $markup = '<span class="iwptp-name-your-price iwptp-name-your-price--suggested ' . $hide . '">' . $inner . '</span>';
                }

                return $markup;
            }

            // -- price variation min-max template
            add_filter('iwptp_element_markup', 'iwptp_name_your_price__add_variation_price', 100, 2);
            function iwptp_name_your_price__add_variation_price($markup, $elm)
            {
                if (
                    empty($elm['type']) ||
                    $elm['type'] !== 'price' ||
                    !iwptp_is_name_your_price()
                ) {
                    return $markup;
                }

                return $markup;
            }

            // -- helper
            function iwptp_is_name_your_price($product = false)
            {
                if (!defined('WC_NYP_PLUGIN_FILE')) {
                    return false;
                }

                if (!$product) {
                    global $product;
                }

                return WC_Name_Your_Price_Helpers::has_nyp($product) ||  WC_Name_Your_Price_Helpers::is_nyp($product);
            }

            // -- provide variation suggested price
            add_filter('iwptp_get_variations', 'iwptp_get_variations__name_your_price__suggested_price', 100, 1);
            function iwptp_get_variations__name_your_price__suggested_price($variations)
            {
                if (!defined('WC_NYP_PLUGIN_FILE')) {
                    return $variations;
                }

                foreach ($variations as &$variation) {
                    if (!empty($variation['is_nyp'])) {
                        $suggested_price = WC_Name_Your_Price_Helpers::get_suggested_price($variation['variation_id']);
                        if ($suggested_price) {
                            $variation['suggested_price'] = $suggested_price;
                        }
                    }
                }

                return $variations;
            }

            // -- hide from cart form (if input is in table)
            add_action('iwptp_container_close', 'iwptp_hide_name_your_price');

            function iwptp_hide_name_your_price()
            {
                if (!empty($GLOBALS['iwptp_nyp_input_field_flag'])) {
                ?>
                    <style>
                        .iwptp-table .cart .nyp {
                            display: none !important;
                        }
                    </style>
                <?php
                }

                $GLOBALS['iwptp_nyp_input_field_flag'] = false;
            }

            // WooCommerce Measurement Price Calculator

            // -- row html class
            add_action('iwptp_product_row_html_class', 'iwptp_product_mc_html_class', 100, 2);
            function iwptp_product_mc_html_class($html_class, $product)
            {
                if (class_exists('WC_Measurement_Price_Calculator')) {
                    global $product;
                    $settings = new \WC_Price_Calculator_Settings($product);
                    $product_measurement = \WC_Price_Calculator_Product::get_product_measurement($product, $settings);

                    if ($product_measurement) {
                        $html_class .= ' iwptp-product-has-measurement ';
                    }
                }

                return $html_class;
            }

            // -- insert mc info above form
            add_action('woocommerce_before_add_to_cart_form', 'iwptp_insert_mc_info'); // inline
            add_action('iwptp_modal_form_content_start', 'iwptp_insert_mc_info'); // modal
            function iwptp_insert_mc_info()
            {
                global $product;
                if (!$info = iwptp_product_mc_info()) {
                    return;
                }
                ?>
                <script>
                    if (!window.iwptp_wc_price_calculator_params) {
                        window.iwptp_wc_price_calculator_params = {};
                    }
                    window.iwptp_wc_price_calculator_params[<?php echo intval($product->get_id()); ?>] = <?php echo wp_kses(iwptp_product_mc_info(), iwptp_allowed_html_tags()); ?>;
                </script>
            <?php
            }

            // -- get product data
            function iwptp_product_mc_info()
            {
                if (!class_exists('WC_Measurement_Price_Calculator')) {
                    return '';
                }

                global $product;
                $settings = new \WC_Price_Calculator_Settings($product);

                /**
                 * Filters the measurement precision.
                 *
                 * @since 3.0
                 *
                 * @param int $measurement_precision the measurement precision
                 */
                $measurement_precision = apply_filters('wc_measurement_price_calculator_measurement_precision', 3);

                // Variables for JS scripts
                $wc_price_calculator_params = array(
                    'woocommerce_currency_symbol'     => get_woocommerce_currency_symbol(),
                    'woocommerce_price_num_decimals'  => wc_get_price_decimals(),
                    'woocommerce_currency_pos'        => get_option('woocommerce_currency_pos', 'left'),
                    'woocommerce_price_decimal_sep'   => stripslashes(wc_get_price_decimal_separator()),
                    'woocommerce_price_thousand_sep'  => stripslashes(wc_get_price_thousand_separator()),
                    'woocommerce_price_trim_zeros'    => get_option('woocommerce_price_trim_zeros'),
                    'unit_normalize_table'            => \WC_Price_Calculator_Measurement::get_normalize_table(),
                    'unit_conversion_table'           => \WC_Price_Calculator_Measurement::get_conversion_table(),
                    'measurement_precision'           => $measurement_precision,
                    'measurement_type'                => $settings->get_calculator_type(),
                    'cookie_name'                     => $settings->get_product_inputs_cookie_name(),
                    'ajax_url'                        => admin_url('admin-ajax.php'),
                    'filter_calculated_price_nonce'   => wp_create_nonce('filter-calculated-price'),
                    'product_id'                      => $product->get_id(),
                    'stock_warning'                   => __("Unfortunately we don't have enough", 'woocommerce-measurement-price-calculator'),
                );

                $min_price = $product->get_meta('_wc_measurement_price_calculator_min_price');

                $wc_price_calculator_params['minimum_price'] = is_numeric($min_price) ? wc_get_price_to_display($product, ['price' => $min_price]) : '';

                // information required for either pricing or quantity calculator to function
                $wc_price_calculator_params['product_price'] = $product->is_type('variable') ? '' : wc_get_price_to_display($product);

                // get the product total measurement (ie Area), get a measurement (ie length), and determine the product total measurement common unit based on the measurements common unit
                $product_measurement = \WC_Price_Calculator_Product::get_product_measurement($product, $settings);

                if (!$product_measurement) {
                    return;
                }

                list($measurement) = $settings->get_calculator_measurements();

                $product_measurement->set_common_unit($measurement->get_unit_common());

                // this is the unit that the product total measurement will be in, ie it's how we know what unit we get for the Volume (AxH) calculator after multiplying A * H
                $wc_price_calculator_params['product_total_measurement_common_unit'] = $product_measurement->get_unit_common();

                if (\WC_Price_Calculator_Product::pricing_calculator_enabled($product)) {

                    // product information required for the pricing calculator javascript to function
                    $wc_price_calculator_params['calculator_type'] = 'pricing';
                    $wc_price_calculator_params['product_price_unit'] = $settings->get_pricing_unit();
                    $wc_price_calculator_params['pricing_overage'] = $settings->get_pricing_overage();

                    // if there are pricing rules, include them on the page source
                    if ($settings->pricing_rules_enabled()) {

                        $wc_price_calculator_params['pricing_rules'] = $settings->get_pricing_rules();

                        // generate the pricing html
                        foreach ($wc_price_calculator_params['pricing_rules'] as $index => $rule) {
                            $wc_price_calculator_params['pricing_rules'][$index]['price_html'] = $settings->get_pricing_rule_price_html($rule);
                        }
                    }
                } else {

                    // product information required for the quantity calculator javascript to function
                    $wc_price_calculator_params['calculator_type'] = 'quantity';

                    $quantity_range = \WC_Price_Calculator_Product::get_quantity_range($product);

                    $wc_price_calculator_params['quantity_range_min_value'] = $quantity_range['min_value'];
                    $wc_price_calculator_params['quantity_range_max_value'] = $quantity_range['max_value'];

                    if ($product->is_type('simple')) {

                        // product_measurement represents one quantity of the product, bail if missing required product physical attributes
                        if (!$product_measurement->get_value()) {
                            return;
                        }

                        $wc_price_calculator_params['product_measurement_value'] = $product_measurement->get_value();
                        $wc_price_calculator_params['product_measurement_unit']  = $product_measurement->get_unit();
                    } else {
                        // provided by the available_variation() method
                        $wc_price_calculator_params['product_measurement_value'] = '';
                        $wc_price_calculator_params['product_measurement_unit']  = '';
                    }
                }

                return wp_json_encode($wc_price_calculator_params);
            }

            // -- clear global $_REQ vals
            add_action('iwptp_before_loop', 'iwptp_mpc_clear_get_vars');
            function iwptp_mpc_clear_get_vars()
            {
                if (!class_exists('WC_Measurement_Price_Calculator')) {
                    return;
                }

                foreach ($_REQUEST as $key => $val) {
                    if (substr($key, -7) === '_needed') {
                        unset($_GET[$key]);
                        unset($_POST[$key]);
                        unset($_REQUEST[$key]);
                    }
                }
            }

            /* Sonaar */

            add_shortcode('iwptp_sonaar', 'iwptp_sonaar');
            function iwptp_sonaar($atts = [])
            {
                global $product;

                if (!empty($atts['custom_field'])) {
                    $playlist_id = get_post_meta($product->get_id(), $atts['custom_field'], true);
                } else if (function_exists('get_field')) {
                    $footer_playlist = get_field('footer_playlist', $product->get_id());

                    if ($footer_playlist) {
                        $playlist_id = $footer_playlist->ID;
                    }
                }

                if (empty($playlist_id)) {
                    return;
                }

                ob_start();
            ?>
                <div class="iwptp-player iwptp-player--sonaar" data-iwptp-sonaar-playlist-id="<?php echo esc_attr($playlist_id); ?>">
                    <div class="iwptp-player__play-button iwptp-player__button">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-play">
                            <polygon points="5 3 19 12 5 21 5 3" />
                        </svg>
                    </div>
                    <div class="iwptp-player__pause-button iwptp-player__button">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-pause">
                            <rect x="6" y="4" width="4" height="16" />
                            <rect x="14" y="4" width="4" height="16" />
                        </svg>
                    </div>
                </div>
            <?php
                return ob_get_clean();
            }

            /* WooCommerce B2B */

            add_filter('iwptp_query_args', 'iwptp_query_args__woocommerce_b2b');
            function iwptp_query_args__woocommerce_b2b($query_args)
            {
                if (!function_exists('wcb2b_create_guest_group')) {
                    return $query_args;
                }

                $terms = get_terms(array(
                    'taxonomy'   => 'product_cat',
                    'hide_empty' => true,
                ));

                if (!$terms) {
                    return $query_args;
                }

                $query_args['tax_query'][] = array(
                    'taxonomy' => 'product_cat',
                    'operator' => 'IN',
                    'field' => 'term_taxonomy_id',
                    'terms' => array_column($terms, 'term_taxonomy_id'),
                );

                return $query_args;
            }

            add_filter('iwptp_element_markup', 'iwptp_element__b2b_hide_price', 100, 2);
            function iwptp_element__b2b_hide_price($markup, $element)
            {
                if (
                    function_exists('wcb2b_create_guest_group') &&
                    !empty($element) &&
                    !empty($element['type']) &&
                    $element['type'] === 'price' &&
                    get_option('wcb2b_hide_prices') === 'yes' &&
                    !is_user_logged_in()
                ) {
                    return '';
                }

                return $markup;
            }

            /* Smart WooCommerce Search */

            // -- force to search out all results in order to collect them later
            add_action('woocommerce_product_query', 'iwptp__smart_wc_search__force_all_results', 10000);
            function iwptp__smart_wc_search__force_all_results($query)
            {
                if (
                    !empty($_GET['search_id']) &&
                    $query->is_main_query() &&
                    !wp_doing_ajax()
                ) {
                    $query->set('posts_per_page', -1);
                }
            }

            // -- collect results from smart wc search, store in session
            add_action('woocommerce_before_main_content', 'iwptp__smart_wc_search__collect_results');
            function iwptp__smart_wc_search__collect_results()
            {
                if (
                    empty($_GET['search_id']) ||
                    empty($_GET['s'])
                ) {
                    return;
                }

                global $wp_query;
                if ($wp_query->posts) {
                    $result_ids = array_column($wp_query->posts, 'ID');
                    iwptp_session()->set('smart_wc_search__result_ids__' . sanitize_text_field($_GET['search_id']) . '__' . sanitize_text_field($_GET['s']), $result_ids);
                }
            }

            // -- always use those collected results in iwptp for that search
            add_filter('iwptp_query_args', 'iwptp_query_args__smart_wc_search', 100, 1);
            function iwptp_query_args__smart_wc_search($query_args)
            {
                if (class_exists('Ysm_Search')) {
                    $table_data = iwptp_get_table_data();
                    $sc_attrs = $table_data['query']['sc_attrs'];

                    if (
                        !empty($sc_attrs['_archive']) &&
                        $sc_attrs['_archive'] === 'search' &&
                        !empty($_GET['search_id']) // smart wc search flag
                    ) {
                        $result_ids = iwptp_session()->get('smart_wc_search__result_ids__' . sanitize_text_field($_GET['search_id']) . '__' . sanitize_text_field($_GET['s']));

                        if ($result_ids) {
                            $query_args['post__in'] = $result_ids;
                        }
                    }
                }

                return $query_args;
            }

            // -- permit 'search_id' param in JS
            add_action('wp_print_scripts', 'iwptp_permit_param__js__smart_wc_search');
            function iwptp_permit_param__js__smart_wc_search()
            {
            ?>
                <script>
                    if (typeof iwptp_persist_params === 'undefined') {
                        var iwptp_persist_params = [];
                    }
                    iwptp_persist_params.push('search_id');
                </script>
                <?php
            }

            // -- permit 'search_id' param in PHP
            add_filter('iwptp_permitted_params', 'iwptp_permit_param__php__smart_wc_search', 100, 1);
            function iwptp_permit_param__php__smart_wc_search($params)
            {
                if (!empty($_GET['search_id'])) {
                    $params[] = 'search_id';
                }
                return $params;
            }

            /* Products Visibility by User Roles (Addify) */

            add_filter('iwptp_query_args', 'iwptp_query_args__product_visibility_by_user_roles', 100, 1);
            function iwptp_query_args__product_visibility_by_user_roles($query_args)
            {
                if (class_exists('Addify_Products_Visibility_Front')) {

                    if (!class_exists('IWPTPL_Dummy_Query_Object')) {
                        class IWPTPL_Dummy_Query_Object
                        {
                            public $query_vars = [];
                            public function __construct($query_args)
                            {
                                $this->query_vars = $query_args;
                            }
                            public function get($query_var)
                            {
                                if (isset($this->query_vars[$query_var])) {
                                    return $this->query_vars[$query_var];
                                }
                                return '';
                            }
                            public function set($query_var, $value)
                            {
                                $this->query_vars[$query_var] = $value;
                            }
                        }
                    }

                    $_query_args = new IWPTPL_Dummy_Query_Object($query_args);
                    do_action('woocommerce_product_query', (object) $_query_args);
                    $query_args = $_query_args->query_vars;

                    if (
                        !empty($query_args['post__not_in']) &&
                        !empty($query_args['post__in']) &&
                        $query_args['post__in'] !== [0] // empty search
                    ) {
                        $query_args['post__in'] = array_diff($query_args['post__in'], $query_args['post__not_in']);

                        if (!count($query_args['post__in'])) {
                            $query_args['post__in'] = array(0);
                        }
                    }

                    return $query_args;
                }

                return $query_args;
            }

            // WooCommerce Request a Quote (Addify)

            // reset actions modified by addify on single product page to avoid breaking integration
            add_action('iwptp_before_loop', 'iwptp_afrfq__reset_hooks');
            function iwptp_afrfq__reset_hooks()
            {
                if (!class_exists('AF_R_F_Q_Front')) {
                    return;
                }

                add_action('woocommerce_single_variation', 'woocommerce_single_variation_add_to_cart_button', 20);
                iwptp_remove_filters_for_anonymous_class('woocommerce_single_variation', 'AF_R_F_Q_Front', 'afrfq_custom_button_replacement', 30);

                add_action('woocommerce_simple_add_to_cart', 'woocommerce_simple_add_to_cart', 30);
                iwptp_remove_filters_for_anonymous_class('woocommerce_simple_add_to_cart', 'AF_R_F_Q_Front', 'afrfq_custom_button_replacement', 30);
            }

            // -- replace button element
            add_filter('iwptp_element_markup', 'iwptp_afrfq__replace_button', 100, 2);
            function iwptp_afrfq__replace_button($markup, $element)
            {
                if (
                    $element['type'] !== 'button' ||
                    empty($element['use_default_template']) ||
                    !class_exists('AF_R_F_Q_Front')
                ) {
                    return $markup;
                }

                global $product;
                $html_class = '';

                ob_start();
                echo '<div class="iwptp-afrfq-button-wrapper">';
                include IWPTPL_PLUGIN_PATH . 'templates/cart_form.php';
                echo '</div>';
                return ob_get_clean();
            }

            // -- replace cart form's inner button 
            add_action('woocommerce_after_add_to_cart_quantity', 'iwptp_afrfq__replace_cart_form_button');
            function iwptp_afrfq__replace_cart_form_button()
            {
                if (
                    iwptp_get_table_data() &&
                    $markup = iwptp_afrfq__get_button_markup()
                ) {
                    global $product;
                    echo wp_kses($markup, iwptp_allowed_html_tags());
                ?>
                    <style>
                        .iwptp-row[data-iwptp-product-id="<?php echo esc_attr($product->get_id()); ?>"] .single_add_to_cart_button {
                            display: none !important;
                        }
                    </style>
                    <?php

                    if (iwptp_afrfq__maybe_hide_price()) {
                    ?>
                        <style>
                            .iwptp-row[data-iwptp-product-id="<?php echo esc_attr($product->get_id()); ?>"] .woocommerce-variation-price {
                                display: none !important;
                            }
                        </style>
                    <?php
                    } else {
                    ?>
                        <style>
                            .iwptp-row[data-iwptp-product-id="<?php echo esc_attr($product->get_id()); ?>"] .woocommerce-variation-price {
                                display: inline-block !important;
                            }
                        </style>
                <?php
                    }
                }
            }

            // -- get button markup
            function iwptp_afrfq__get_button_markup($default_markup = null, $wrap = false)
            {
                if (!class_exists('AF_R_F_Q_Front')) {
                    return $default_markup;
                }

                $markup = iwptp__afrfq_replace_loop_add_to_cart_link($default_markup, $GLOBALS['product']);

                if (
                    $markup &&
                    $wrap &&
                    FALSE === strpos($markup, 'class="woocommerce"')
                ) {
                    $markup = '<div class="woocommerce">' . $markup . '</div>';
                }

                return $markup;
            }

            // -- get price toggle
            function iwptp_afrfq__maybe_hide_price()
            {
                $result = false;

                if (!class_exists('AF_R_F_Q_Front')) {
                    return $result;
                }

                global $product;
                if (FALSE !== strpos($product->get_price_html(), '.woocommerce-variation-price{ display: none !important;}')) {
                    $result = true;
                }

                return $result;
            }

            // -- add js params
            add_action('wp_enqueue_scripts', 'iwptp_afrfq__params');
            function iwptp_afrfq__params()
            {
                if (!class_exists('AF_R_F_Q_Front')) {
                    return;
                }

                $params = array(
                    'product_ids' => [],
                    'view_quote_url' => esc_url(get_page_link(get_option('addify_atq_page_id', true))),
                    'view_quote_label' => esc_html('View Quote', 'addify_rfq'),
                );

                if ($view_quote_label = get_option('afrfq_view_button_message')) {
                    $params['view_quote_label'] = $view_quote_label;
                }

                $quote_product_ids = []; // include variations

                if (!empty(WC()->session->get('quotes'))) {
                    foreach (WC()->session->get('quotes') as $quote) {
                        if (!empty($quote['variation_id'])) {
                            $params['product_ids'][] = (int) $quote['variation_id']; // don't take variable product id
                        } else {
                            $params['product_ids'][] = (int) $quote['product_id'];
                        }
                    }
                }

                wp_add_inline_script('iwptp', 'var iwptp_afrfq_params = ' . wp_json_encode($params) . ';', 'after');
            }

            // -- style
            add_action('wp_enqueue_scripts', 'iwptp_afrfq__style');
            function iwptp_afrfq__style()
            {
                if (!class_exists('AF_R_F_Q_Front')) {
                    return;
                }
                ob_start();
                ?>
                .iwptp-afrfqbt-view-quote-wrapper {
                display: block;
                margin-top: .5em;
                width: 100%;
                clear: both;
                }

                a.iwptp-afrfqbt-view-quote {
                display: inline-block;
                }
                <?php

                wp_add_inline_style('iwptp', ob_get_clean(), 'after');
            }

            // -- helpers taken from the Addify plugin
            function iwptp__afrfq_replace_loop_add_to_cart_link($html, $product)
            {

                $pageurl = get_page_link(get_option('addify_atq_page_id', true));
                global $user;
                $cart_txt = $html;

                $args = array(
                    'post_type' => 'addify_rfq',
                    'post_status' => 'publish',
                    'numberposts' => -1,
                    'orderby' => 'menu_order',
                    'order' => 'ASC'

                );

                $quote_rules = get_posts($args);

                $pageurl = get_page_link(get_option('addify_atq_page_id', true));

                $cart_txt = $html;

                if (!$product->is_in_stock() && 'yes' !== get_option('enable_o_o_s_products')) {

                    return $html;
                }

                $quote_button = false;

                foreach ($quote_rules as $rule) {

                    $afrfq_is_hide_price      = get_post_meta(intval($rule->ID), 'afrfq_is_hide_price', true);
                    $afrfq_hide_price_text    = get_post_meta(intval($rule->ID), 'afrfq_hide_price_text', true);
                    $afrfq_is_hide_addtocart  = get_post_meta(intval($rule->ID), 'afrfq_is_hide_addtocart', true);
                    $afrfq_custom_button_text = get_post_meta(intval($rule->ID), 'afrfq_custom_button_text', true);
                    $afrfq_custom_button_link = get_post_meta(intval($rule->ID), 'afrfq_custom_button_link', true);

                    $istrue = false;

                    if ($quote_button && in_array($afrfq_is_hide_addtocart, array('replace', 'addnewbutton'), true)) {
                        continue;
                    }

                    if (!iwptp__afrfq_check_rule_for_product($product->get_id(), $rule->ID)) {
                        continue;
                    }

                    if (iwptp__afrfq_check_required_addons($product->get_id())) {
                        //WooCommerce Product Add-ons compatibility
                        return $html;
                    } else {

                        if ('replace' === $afrfq_is_hide_addtocart) {
                            $quote_button = true;
                            $cart_txt     = '<div class="added_quote" id="added_quote' . $product->get_id() . '">' . esc_html(get_option('afrfq_pro_success_message')) . '<br /><a href="' . esc_url($pageurl) . '">' . esc_html(get_option('afrfq_view_button_message')) . '</a></div><a href="javascript:void(0)" rel="nofollow" data-product_id="' . $product->get_ID() . '" data-product_sku="' . $product->get_sku() . '" class="afrfqbt button add_to_cart_button product_type_' . $product->get_type() . '">' . esc_attr($afrfq_custom_button_text) . '</a>';
                        } elseif ('replace_custom' === $afrfq_is_hide_addtocart) {

                            if (!empty($afrfq_custom_button_text)) {
                                $cart_txt = '<a href="' . esc_url($afrfq_custom_button_link) . '" rel="nofollow"  class=" button add_to_cart_button product_type_' . $product->get_type() . '">' . esc_attr($afrfq_custom_button_text) . '</a>';
                            } else {

                                $cart_txt = '';
                            }
                        }
                    }
                }

                if ($quote_button) {
                    do_action('addify_rfq_after_add_to_quote_button_loop');
                }
                return $cart_txt;
            }

            function iwptp__afrfq_check_rule_for_product($product_id, $rule_id)
            {

                $afrfq_rule_type         = get_post_meta(intval($rule_id), 'afrfq_rule_type', true);
                $afrfq_hide_products     = (array) unserialize(get_post_meta(intval($rule_id), 'afrfq_hide_products', true));
                $afrfq_hide_categories   = (array) unserialize(get_post_meta(intval($rule_id), 'afrfq_hide_categories', true));
                $afrfq_hide_user_role    = (array) unserialize(get_post_meta(intval($rule_id), 'afrfq_hide_user_role', true));
                $applied_on_all_products = get_post_meta($rule_id, 'afrfq_apply_on_all_products', true);

                if (!is_user_logged_in()) {

                    if (!in_array('guest', (array) $afrfq_hide_user_role, true) && 'afrfq_for_guest_users' !== $afrfq_rule_type) {

                        return false;
                    }
                } else {

                    $curr_user      = wp_get_current_user();
                    $curr_user_role = current($curr_user->roles);

                    if (!in_array($curr_user_role, (array) $afrfq_hide_user_role, true)) {
                        return false;
                    }
                }


                if ('yes' === $applied_on_all_products) {
                    return true;
                }

                if (in_array($product_id, $afrfq_hide_products)) {
                    return true;
                }

                foreach ($afrfq_hide_categories as $cat) {

                    if (!empty($cat) && has_term($cat, 'product_cat', $product_id)) {

                        return true;
                    }
                }

                return false;
            }

            function iwptp__afrfq_check_required_addons($product_id)
            {
                // No parent add-ons, but yes to global.
                if (in_array('woocommerce-product-addons/woocommerce-product-addons.php', apply_filters('active_plugins', get_option('active_plugins')))) {
                    $addons = WC_Product_Addons_Helper::get_product_addons($product_id, false, false, true);

                    if ($addons && !empty($addons)) {
                        foreach ($addons as $addon) {
                            if (isset($addon['required']) && '1' == $addon['required']) {
                                return true;
                            }
                        }
                    }
                }

                return false;
            }

            // Yith min-max 
            add_filter('woocommerce_available_variation', 'iwptp_yith__woocommerce_available_variation', 100, 2);
            function iwptp_yith__woocommerce_available_variation($variation, $product)
            {
                if (!function_exists('YITH_WMMQ')) {
                    return $variation;
                }

                if (
                    'yes' === $product->get_meta('_ywmmq_product_quantity_limit_variations_override') &&
                    apply_filters('ywmmq_set_variation_quantity_locked', true)
                ) {
                    $limits = YITH_WMMQ()->product_limits($product->get_id(), $variation['variation_id']);
                } else {
                    $limits = YITH_WMMQ()->product_limits($product->get_id(), 0);
                }

                if (!empty($limits['min'])) {
                    $variation['min_qty'] = $limits['min'];
                }

                if (!empty($limits['max'])) {
                    $variation['max_qty'] = $limits['max'];
                }

                if (!empty($limits['step'])) {
                    $variation['step'] = $limits['step'];
                }

                return $variation;
            }

            // Wholesale Prices for WooCommerce by Wholesale Suite

            // -- helper: get the wholesale price of a product for current user
            function iwptp_get_wholesale_price($product, $min_max = false)
            {
                if (!class_exists('WWPP_Query')) {
                    return false;
                }

                switch (gettype($product)) {
                    case 'integer':
                    case 'string':
                        $product_id = $product;
                        $product = wc_get_product($product_id);
                        break;

                    case 'array':
                        $product_id = $product['variation_id'];
                        $product = wc_get_product($product_id);

                        break;

                    default: // object
                        $product_id = $product->get_id();
                        break;
                }

                if (gettype($product) !== 'integer') {
                    $product_id = $product->get_id();
                } else {
                    $product_id = $product;
                    $product = wc_get_product($product);
                }

                $user_wholesale_role = WWP_Wholesale_Roles::getInstance()->getUserWholesaleRole();

                if (
                    $product->get_type() == 'variable' &&
                    !empty($min_max)
                ) { // variable
                    $variations = iwptp_get_variations($product);
                    $min_price = '';
                    $max_price = '';
                    $has_wholesale = false;

                    foreach ($variations as $variation) {
                        if (!$variation['is_purchasable']) {
                            continue;
                        }

                        if (!empty($variation['wholesale_price'])) {
                            $has_wholesale = true;
                        }

                        $curr_var_price = $variation['display_price'];
                        $price_arr = WWP_Wholesale_Prices::get_product_wholesale_price_on_shop_v3($variation['variation_id'], $user_wholesale_role);

                        if (strcasecmp($price_arr['wholesale_price'], '') != 0) {
                            $curr_var_price = $price_arr['wholesale_price'];
                        }

                        if (strcasecmp($min_price, '') == 0 || $curr_var_price < $min_price) {
                            $min_price = $curr_var_price;
                        }

                        if (strcasecmp($max_price, '') == 0 || $curr_var_price > $max_price) {
                            $max_price = $curr_var_price;
                        }
                    }

                    if (!$has_wholesale) {
                        return false;
                    }

                    if ($min_max == 'min') {
                        return $min_price;
                    } else {
                        return $max_price;
                    }
                } else { // other
                    $wholesale_price_arr = WWP_Wholesale_Prices::get_product_wholesale_price_on_shop_v3($product->get_id(), $user_wholesale_role);
                    return $wholesale_price_arr['wholesale_price'];
                }
            }

            // -- manage product visibility by user role on archive pages
            add_filter('iwptp_query_args', 'iwptp_query_args__woocommerce_wholesale_prices_compat');
            function iwptp_query_args__woocommerce_wholesale_prices_compat($query_args)
            {
                global $wp_query;

                if (
                    !class_exists('WWPP_Query') ||
                    empty($wp_query->get('post__in'))
                ) {
                    return $query_args;
                }

                if (empty($query_args['post__in'])) {
                    $query_args['post__in'] = $wp_query->get('post__in');
                } else {
                    $query_args['post__in'] = array_intersect($query_args['post__in'], $wp_query->get('post__in'));

                    if (!count($query_args['post__in'])) {
                        $query_args['post__in'] = array(0);
                    }
                }

                return $query_args;
            }

            // -- modify variation price in select variation element
            add_filter('iwptp_select_variation_price', 'iwptp_select_variation_price__wholesale_price_override', 100, 2);
            function iwptp_select_variation_price__wholesale_price_override($variation_price, $variation)
            {
                if (!class_exists('WWPP_Query')) {
                    return $variation_price;
                }

                if (!empty($variation['wholesale_price'])) {
                    $variation_price = $variation['wholesale_price'];
                }

                return $variation_price;
            }

            // -- lowest price
            add_filter('iwptp_product_get_lowest_price', 'iwptp_product_get_lowest_price__wholesale', 100, 2);
            function iwptp_product_get_lowest_price__wholesale($lowest_price, $product)
            {
                if (
                    !class_exists('WWPP_Query') ||
                    !$wholesale_min_price = iwptp_get_wholesale_price($product, 'min')
                ) {
                    return $lowest_price;
                }

                return $wholesale_min_price;
            }

            // -- highest price
            add_filter('iwptp_product_get_highest_price', 'iwptp_product_get_highest_price__wholesale', 100, 2);
            function iwptp_product_get_highest_price__wholesale($highest_price, $product)
            {
                if (
                    !class_exists('WWPP_Query') ||
                    !$wholesale_max_price = iwptp_get_wholesale_price($product, 'max')
                ) {
                    return $highest_price;
                }

                return $wholesale_max_price;
            }

            // -- modify sale price
            add_filter('iwptp_product_get_sale_price', 'iwptp_product_get_sale_price__wholesale', 100, 2);
            function iwptp_product_get_sale_price__wholesale($sale_price, $product)
            {
                if (!class_exists('WWPP_Query')) {
                    return $sale_price;
                }

                if ($wholesale_price = iwptp_get_wholesale_price($product)) {
                    return $wholesale_price;
                }

                return $sale_price;
            }

            // -- modify on sale status
            add_filter('iwptp_product_is_on_sale', 'iwptp_product_is_on_sale__wholesale', 100, 2);
            function iwptp_product_is_on_sale__wholesale($on_sale, $product)
            {
                if (!class_exists('WWPP_Query')) {
                    return $on_sale;
                }

                if (iwptp_get_wholesale_price($product)) {
                    $on_sale = true;
                }

                return $on_sale;
            }

            // -- make the bulk discount table show on non-product pages 
            add_action('plugins_loaded', 'iwptp_wholesale_enable_discount_table');
            function iwptp_wholesale_enable_discount_table()
            {
                if (
                    class_exists('WWPP_Query') &&
                    get_option('wwpp_settings_hide_quantity_discount_table', false) !== 'yes'
                ) {
                    add_filter('render_order_quantity_based_wholesale_pricing', '__return_true');
                }
            }

            // -- modify variation data - set wholesale price as the sale price
            // helps maintain compat. with elements like totals, on sale, add selected to cart {total_cost} 
            add_filter('iwptp_get_variations', 'iwptp_wholesale__set_variation_sale_price');
            function iwptp_wholesale__set_variation_sale_price($variations)
            {
                foreach ($variations as &$variation) {
                    if (!empty($variation['wholesale_price'])) {
                        $variation['original_display_regular_price'] = $variation['display_regular_price'];
                        $variation['original_display_price'] = $variation['display_price'];

                        $variation['display_regular_price'] = $variation['display_price'];
                        $variation['display_price'] = $variation['wholesale_price'];
                    }
                }

                return $variations;
            }

            // -- auto disable AJAX
            add_filter('iwptp_shortcode_attributes', 'iwptp_wholesale__disable_ajax', 100, 1);
            function iwptp_wholesale__disable_ajax($atts)
            {
                if (class_exists('WWPP_Query')) {
                    $atts['disable_ajax'] = true;
                }

                return $atts;
            }

            // -- wholesale shortcode
            add_shortcode('iwptp_wholesale', 'iwptp_wholesale__shortcode');
            function iwptp_wholesale__shortcode($atts)
            {
                global $product;

                if (
                    !class_exists('WWPP_Query') ||
                    ('yes' == get_option('wwp_hide_price_add_to_cart') &&
                        !is_user_logged_in()
                    )
                ) {
                    return '';
                }

                $atts = shortcode_atts(array(
                    'output' => 'wholesale_price',
                    'on_wholesale_label' => 'On wholesale!',
                    'not_on_wholesale_label' => '-',
                ), $atts);

                $markup = '';

                $atts['output'] = trim(strtolower($atts['output']));

                if (!in_array(
                    $atts['output'],
                    array(
                        'wholesale_price',
                        'original_price',
                        'wholesale_table',
                        'wholesale_label'
                    )
                )) {
                    return '';
                }

                $html_class = "iwptp-wholesale iwptp-wholesale--" . str_replace('_', '-', $atts['output']);

                switch ($atts['output']) {
                    case 'wholesale_price':
                        if ($product->get_type() == 'variable') {
                            if (iwptp_get_wholesale_price($product, 'max')) {
                                $default_view = '<div class="iwptp-wholesale__default-view">' . iwptp_price(iwptp_get_wholesale_price($product, 'min')) . ' - ' . iwptp_price(iwptp_get_wholesale_price($product, 'max')) . '</div>';
                                $variation_view = '<div class="iwptp-wholesale__variation-view"></div>';

                                $markup = $default_view . $variation_view;
                            } else { // variable product not on wholesale

                            }
                        } else { // non-variable product types
                            if ($wholesale_price = iwptp_get_wholesale_price($product)) {
                                $markup = iwptp_price($wholesale_price);
                            }
                        }

                        break;

                    case 'original_price':
                        if ($product->get_type() == 'variable') {
                            $default_view = '<div class="iwptp-wholesale__default-view">' . iwptp_price($product->get_variation_price('min')) . ' - ' . iwptp_price($product->get_variation_price('max')) . '</div>';
                            $variation_view = '<div class="iwptp-wholesale__variation-view"></div>';

                            $markup = $default_view . $variation_view;
                        } else { // non-variable product types
                            $markup = iwptp_price(wc_get_price_to_display($product));
                        }

                        break;

                    case 'wholesale_table':
                        $matches = [];
                        if (preg_match('/(<table.+table)>/s', $product->get_price_html(), $match)) {
                            $markup = $match[0];
                        }

                        break;

                    case 'wholesale_label':
                        $has_wholesale = false;
                        if ($product->get_type() == 'variable') {

                            $default_label = '';
                            if (iwptp_get_wholesale_price($product, 'max')) {
                                $default_label = $atts['on_wholesale_label'];
                            } else {
                                $default_label = $atts['not_on_wholesale_label'];
                            }

                            $default_view = '<div class="iwptp-wholesale__default-view">' . $default_label . '</div>';

                            ob_start();
                ?>
                            <div class="iwptp-wholesale__variation-view">
                                <div class="iwptp-wholesale__variation-view__variation-is-on-wholesale-view">
                                    <?php echo esc_html($atts['on_wholesale_label']); ?>
                                </div>
                                <div class="iwptp-wholesale__variation-view__variation-is-not-on-wholesale-view">
                                    <?php echo esc_html($atts['not_on_wholesale_label']); ?>
                                </div>
                            </div>
                <?php
                            $variation_view = ob_get_clean();

                            $markup = $default_view . $variation_view;
                        } else { // non-variable product types
                            if (iwptp_get_wholesale_price($product)) {
                                $markup = $atts['on_wholesale_label'];
                            } else {
                                $markup = $atts['not_on_wholesale_label'];
                            }
                        }

                        break;
                }

                if ($product->get_type() == 'variable') {
                    $html_class .= ' iwptp-variable-switch ';
                }

                return "<div class='$html_class'>$markup</div>";
            }

            // -- shortcode style
            add_action('wp_enqueue_scripts', 'iwptp_wholesale__shortcode__style');
            function iwptp_wholesale__shortcode__style()
            {
                if (!class_exists('WWPP_Query')) {
                    return;
                }

                ob_start();
                ?>
                .iwptp table.order-quantity-based-wholesale-pricing-view { /* freeze table compatibility */
                max-width: 200px;
                margin: 0 !important;
                }

                .iwptp-wholesale--variation-view-enabled .iwptp-wholesale__default-view,
                .iwptp-wholesale__variation-view {
                display: none;
                }

                .iwptp-wholesale--default-view-enabled .iwptp-wholesale__default-view,
                .iwptp-wholesale--variation-view-enabled .iwptp-wholesale__variation-view {
                display: inline-block;
                }

                .iwptp-wholesale__variation-view__variation-is-on-wholesale-view,
                .iwptp-wholesale__variation-view__variation-is-not-on-wholesale-view {
                display: none;
                }

                .iwptp-wholesale--variation-is-on-wholesale-view-enabled .iwptp-wholesale__variation-view__variation-is-on-wholesale-view,
                .iwptp-wholesale--variation-is-not-on-wholesale-view-enabled .iwptp-wholesale__variation-view__variation-is-not-on-wholesale-view {
                display: inline-block;
                }

            <?php
                wp_add_inline_style('iwptp', ob_get_clean(), 'after');
            }

            // -- hide price and add to cart button (as well as on sale, total)
            add_filter('iwptp_element_markup', 'iwptp_wholesale__hide_elements', 100, 2);
            function iwptp_wholesale__hide_elements($markup, $element)
            {
                if (
                    class_exists('WWPP_Query') &&
                    ('yes' === get_option('wwp_hide_price_add_to_cart') &&
                        !is_user_logged_in()
                    ) &&
                    (in_array($element['type'], array('price', 'total', 'on_sale', 'quantity', 'select_variation', 'checkbox', 'add_selected_to_cart')) ||
                        ($element['type'] == 'button' &&
                            strpos($element['link'], 'cart') !== false
                        )
                    )
                ) {
                    if ($element['type'] == 'price') {
                        $message = get_option('wwp_price_and_add_to_cart_replacement_message');

                        if (!$message) {
                            $message = '<a href="' . get_permalink(wc_get_page_id('myaccount')) . '">' . __('Login to see prices', 'woocommerce-wholesale-prices') . '</a>';
                        } else {
                            $message = html_entity_decode($message);
                        }

                        return apply_filters('wwp_display_replacement_message', $message);
                    }

                    return '';
                }

                return $markup;
            }

            // -- variation table product visibility
            add_filter('iwptp_variation_query_args', 'iwptp_wholesale__variation_table_product_visibility');
            function iwptp_wholesale__variation_table_product_visibility($args)
            {
                if (!class_exists('WWPP_Query')) {
                    return $args;
                }

                $user_wholesale_role = WWP_Wholesale_Roles::getInstance()->getUserWholesaleRole();

                if (
                    is_array($user_wholesale_role) &&
                    count($user_wholesale_role)
                ) {
                    $user_wholesale_role = $user_wholesale_role[0];

                    $args['meta_query'][] = array(
                        'key' => WWPP_PRODUCT_WHOLESALE_VISIBILITY_FILTER,
                        'value' => array($user_wholesale_role, 'all'),
                        'compare' => 'IN',
                    );
                }

                return $args;
            }

            // WooCommerce Subscriptions
            // -- css
            add_action('wp_enqueue_scripts', 'iwptp_wc_subscription__style');
            function iwptp_wc_subscription__style()
            {
                if (!class_exists('WC_Subscriptions')) {
                    return;
                }

                ob_start();
            ?>
                /* IWPTPL PRO - WooCommerce subscription integration */
                .iwptp-product-type-variable-subscription .cart {
                max-width: 600px;
                margin-bottom: 10px;
                }

                .iwptp-product-type-variable-subscription .cart td.label {
                display: none;
                }

                .iwptp-product-type-variable-subscription .cart .variations,
                .iwptp-product-type-variable-subscription .cart td {
                border: none !important;
                }

                .iwptp-product-type-variable-subscription .variations {
                margin-bottom: 10px;
                }

                .iwptp-product-type-variable-subscription .variations tr {
                display: inline-block !important;
                }

                .iwptp-product-type-variable-subscription .value {
                padding: 0 !important;
                }

                .woocommerce-variation.single_variation {
                display: block !important;
                overflow: visible !important;
                }
            <?php
                wp_add_inline_style('iwptp', ob_get_clean(), 'after');
            }

            // -- add attribute name in dropdown
            add_filter('woocommerce_dropdown_variation_attribute_options_args', 'iwptp_wc_subscription__modify_dropdown', 10);
            function iwptp_wc_subscription__modify_dropdown($args)
            {
                global $product;

                if (
                    iwptp_get_table_data() &&
                    $product->get_type() == 'variable-subscription'
                ) {
                    $args['show_option_none'] = apply_filters('the_title', get_taxonomy($args['attribute'])->labels->singular_name);
                }

                return $args;
            }

            // -- add variation class to row
            add_filter('iwptp_product_row_html_class', 'iwptp_wc_subscription__modify_variation_html_class_on_row', 100, 1);
            function iwptp_wc_subscription__modify_variation_html_class_on_row($html_class)
            {
                global $product;

                if ($product->get_type() == 'subscription_variation') {
                    $html_class .= ' iwptp-product-type-variation ';
                }

                return $html_class;
            }

            // WooCommerce all products for subscriptions 
            add_action('wp_enqueue_scripts', 'iwptp_woocommerce_all_products_for_subscription__button_label_param');
            function iwptp_woocommerce_all_products_for_subscription__button_label_param()
            {
                if (!class_exists('WCS_ATT')) {
                    return;
                }

                $button_label = get_option(WC_Subscriptions_Admin::$option_prefix . '_add_to_cart_button_text', __('Sign up now', 'woocommerce-subscriptions'));

                ob_start();
            ?>
                // @TODO - default option should be pre-selected
                // @TODO - trigger layout when select is displayed / hidden

                var iwptp_subscription_button_label = "<?php echo esc_html($button_label); ?>",
                iwptp_subscription_default_button_label = '';
                jQuery(function($){
                // switch button label

                // -- first page load / ajax results loaded
                $('body').on('iwptp_after_every_load', function(){
                $('.iwptp-row', this).each(function(){
                var $row = $(this);
                select_default_option($row);
                assign_correct_button_label_for_row( $row );
                });
                })

                // -- select variation
                $('body').on('select_variation', '.iwptp-row', function(){
                var $row = $(this);
                select_default_option($row);
                assign_correct_button_label_for_row( $row );
                });

                // -- option selected
                $('body').on('change', '.iwptp-row .wcsatt-options-wrapper input', function(){
                var $row = $(this).closest('.iwptp-row');
                setTimeout(function(){
                assign_correct_button_label_for_row($row);
                }, 1)
                });

                function assign_correct_button_label_for_row( $row ){
                var $button = $('.single_add_to_cart_button', $row),
                default_label = $row.attr('data-iwptp-subscription-default-add-to-cart-text'),
                single_op_selected = $('.wcsatt-options-prompt-label-one-time input:checked, .one-time-option input:checked', $row).length;

                if( single_op_selected ){
                $button.text( default_label );
                }else{
                $button.text( iwptp_subscription_button_label );
                }
                }

                // toggle select dropdown
                $('body').on('change', '.iwptp-row .wcsatt-options-prompt-radios input', function(){
                var $this = $(this),
                $row = $this.closest('.iwptp-row'),
                $select_wrapper = $('.wcsatt-options-product-wrapper', $row),
                $hidden_ops_wrapper = $('.wcsatt-options-product--hidden', $row);

                if( $this.closest('.wcsatt-options-prompt-label-subscription').length ){
                $select_wrapper.show();
                }else{
                $select_wrapper.hide();
                }

                manage_hidden_input_check( $row );

                // @TODO assign correct label
                });

                function manage_hidden_input_check( $row ){
                var $select = $('.wcsatt-options-product-dropdown', $row);
                selected_val = $select.val(),
                $hidden_ops_wrapper = $('.wcsatt-options-product--hidden', $row),
                $one_time = $('.wcsatt-options-prompt-label-one-time input', $row);

                if( $one_time.is(':checked') ){
                var $current_input = $('input:checked', $hidden_ops_wrapper);
                original_val = $current_input.attr('data-iwptp-original-val') ? $current_input.attr('data-iwptp-original-val') : $current_input.val();

                $current_input.attr('data-iwptp-original-val', original_val);
                $current_input.val('0');

                }else{
                var $current_input = $('input[value="'+ selected_val +'"]', $hidden_ops_wrapper);
                original_val = $current_input.attr('data-iwptp-original-val');

                if( original_val ){
                $current_input.val( original_val );
                }

                $current_input.click();
                }
                }

                function select_default_option( $row ) {
                var default_subscription_scheme = $row.attr('data-iwptp-default-subscription-scheme');

                // select subscription option
                if( default_subscription_scheme ){
                // general subscriptions option
                $('.wcsatt-options-prompt-action-input', $row).click();

                // specific subscription option
                // -- select
                $('.wcsatt-options-product-dropdown', $row).val(default_subscription_scheme);

                // -- radio option
                $('.wcsatt-options-wrapper input[value="'+ default_subscription_scheme +'"]', $row).prop('checked', true);

                // select one time option
                }else{
                $('.wcsatt-options-prompt-label-one-time input, .one-time-option input', $row).click();
                }
                }

                $('body').on('change', '.iwptp-row .wcsatt-options-product-dropdown', function(){
                var $this = $(this),
                $row = $this.closest('.iwptp-row');

                manage_hidden_input_check( $row );
                });

                })
            <?php
                wp_add_inline_script('iwptp', ob_get_clean(), 'before');
            }

            // -- record the default add to cart button for each product and subscription option
            add_filter('iwptp_product_row_attributes', 'iwptp_custom__product_row_attributes');
            function iwptp_custom__product_row_attributes($attrs)
            {
                if (class_exists('WCS_ATT')) {
                    global $product;

                    // add to cart text
                    $attrs .= ' data-iwptp-subscription-default-add-to-cart-text="' . $product->single_add_to_cart_text() . '" ';

                    // default subscription status
                    $attrs .= ' data-iwptp-default-subscription-scheme="' . WCS_ATT_Product_Schemes::get_default_subscription_scheme($product) . '" ';
                }

                return $attrs;
            }

            // Estimated delivery date per product for WooCommerce
            add_action('wp_enqueue_scripts', 'iwptp_pi_estimate_delivery');
            function iwptp_pi_estimate_delivery()
            {
                if (!function_exists('run_pi_edd')) {
                    return;
                }

                ob_start();
            ?>
                jQuery(function($){
                function update_delivery_estimate_widget_inside_row (){
                var $this = $(this),
                $widget = $('.pi-variable-estimate', $this),
                estimates = $widget.data('estimates'),
                notselected = $widget.data('notselected'),
                variation = $this.data('iwptp_variation');

                if( ! $widget.length ){
                return;
                }

                if(
                variation &&
                estimates[variation.variation_id]
                ){
                $widget.html( estimates[variation.variation_id] );
                }else{
                $widget.html( notselected );
                }
                }

                $('.iwptp-row.iwptp-product-type-variable').each(update_delivery_estimate_widget_inside_row);
                $('body').on('select_variation', '.iwptp-row.iwptp-product-type-variable', update_delivery_estimate_widget_inside_row)
                })
            <?php
                wp_add_inline_script('iwptp', ob_get_clean(), 'after');
            }

            // Music player for WooCommerce

            add_action('wp_enqueue_scripts', 'iwptp_music_player_for_wc__js');
            function iwptp_music_player_for_wc__js()
            {
                if (!class_exists('WooCommerceMusicPlayer')) {
                    return;
                }

                ob_start();
            ?>
                jQuery(function($){
                $('body').on('iwptp_after_every_load', function(){
                delete generated_the_wcmp;
                generate_the_wcmp();
                })
                })
            <?php
                wp_add_inline_script('iwptp', ob_get_clean(), 'before');
            }
