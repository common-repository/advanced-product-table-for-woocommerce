<?php
// archive override
function iwptp_archive_override()
{
    if (!class_exists('WooCommerce')) {
        return false;
    }

    // shortcode attributes
    $iwptp_table_id = '';
    $iwptp_custom_table = false;
    $category = '';
    $tag = '';
    $attribute = '';
    $taxonomy = '';
    $_taxonomy = '';
    $_term = '';
    $page = '';
    $search = '';
    if (get_query_var('paged') !== 1) {
        $page = " page='" . get_query_var('paged') . "' ";
    }

    // get global settings
    $iwptp_settings = iwptp_get_settings_data();

    if (!empty($iwptp_settings['archive_override'])) {
        // get global default
        if (empty($iwptp_settings['archive_override']['default'])) {
            $iwptp_settings['archive_override']['default'] = '';
        }

        $iwptp_table_id = $iwptp_settings['archive_override']['default'];
        if ($iwptp_table_id == 'custom') {
            $iwptp_custom_table = $iwptp_settings['archive_override']['default_custom'];
        }

        // search
        if (is_search()) {
            // Product search results page
            global $wp_query;
            $search = " _search='" . get_query_var("s") . "' ";
            $archive = ' _archive="search" ';

            // search archive override table
            extract(iwptp_archive_override__get_table_vars('search'));
        }

        // category
        if (is_product_category()) {
            $term = get_query_var('term') ? get_query_var('term') : get_query_var('product_cat');
            $category = " category='" . esc_attr($term) . "' ";

            // category archive override table
            extract(iwptp_archive_override__get_table_vars('category', $term));

            $tag = '';
            $attribute = '';
        }

        // shop
        if (is_shop() && !is_search()) {
            // shop archive override table
            extract(iwptp_archive_override__get_table_vars('shop'));

            $category = '';
            $tag = '';
            $attribute = '';
            $archive = ' _archive="shop" ';

            // tag
        } else if (is_product_tag()) {
            $term = get_query_var('term');
            $tag = " taxonomy='product_tag:" . esc_attr($term) . "' ";

            // tag archive override table
            extract(iwptp_archive_override__get_table_vars('tag', $term));

            $category = '';
            $attribute = '';

            // attribute
        } else if (taxonomy_is_product_attribute(get_query_var('taxonomy', false))) {
            $_taxonomy = get_query_var('taxonomy', false);
            $_attribute = substr($_taxonomy, 3);

            $attr_label = wc_attribute_label($_taxonomy);

            if (function_exists('icl_object_id')) { // WPML
                $slug       = wc_attribute_taxonomy_slug($_taxonomy);
                $all_labels = wc_get_attribute_taxonomy_labels();
                $attr_label = isset($all_labels[$slug]) ? $all_labels[$slug] : $slug;
            }

            $attribute = " attribute='" . $attr_label . ":" . get_query_var('term', '') . "' ";

            // attribute archive override table
            extract(iwptp_archive_override__get_table_vars('attribute', $_attribute));

            $category = '';
            $tag = '';

            // other taxonomy
        } else if (is_product_taxonomy() && !is_product_category()) {
            $vars = array(
                'iwptp_table_id' => false,
                'iwptp_custom_table' => false
            );

            // // iwptp archive override for custom taxonomy
            // add_filter('iwptp_archive_override_taxonomy_table_vars', 'iwptp_archive_override_taxonomy_table_vars__location', 3, 100);
            // function iwptp_archive_override_taxonomy_table_vars__location( $vars, $taxonomy, $term ){
            //   if( in_array( $taxonomy, array( 'location' ) ) ){ // replace location with your custom taxonomy slug
            //     $vars['iwptp_table_id'] = 62; // replace 62 with your table ID
            //   }
            //   return $vars;
            // }

            extract(apply_filters('iwptp_archive_override_taxonomy_table_vars', $vars, get_query_var('taxonomy'), get_query_var('term')));

            $taxonomy = ' taxonomy="' . esc_attr(get_query_var('taxonomy')) . ':' . esc_attr(get_query_var('term')) . '" ';
        }
    }

    if ($iwptp_table_id) {

        // handle subcategories grid on shop and category archive pages
        if (is_shop() || is_product_category() && !is_search()) {
            if (!defined('IWPTPL_FLAG_CAT_ARCHIVE')) {
                $display_mode = woocommerce_get_loop_display_mode();

                // form mode should force products
                $table_id = $iwptp_table_id === 'custom' ? iwptp_extract_id_from_shortcode($iwptp_custom_table) : $iwptp_table_id;
                if (!empty($_GET[$table_id . '_filtered'])) {
                    if ($display_mode == 'subcategories') {
                        $display_mode = 'products';
                    }
                }

                switch ($display_mode) {
                    case 'subcategories':
                        return false;

                    case 'both':
                        add_action('woocommerce_after_shop_loop', 'iwptp_archive_override', 9); // just before woocommerce_pagination()
                        define('IWPTPL_FLAG_CAT_ARCHIVE', TRUE);
                        return false;
                }
            } else {
                wp_reset_query();
                $theme = wp_get_theme();
                if (in_array('Salient', array($theme->name, $theme->parent_theme))) {
                    echo '<style> .products .product:not(.product-category) { display: none !important; } </style>';
                } else if (in_array('Unicon', array($theme->name, $theme->parent_theme))) {
                    echo '<style> #content .products .product:not(.product-category) { display: none !important; } </style>';
                } else {
                    echo '<style> #main .products .product:not(.product-category) { display: none !important; } </style>';
                }
            }
        }

        // hide the table navigation and depend
        // upon external filter to direct query
        // in these cases:
        $only_loop = '';
        if (!function_exists('is_plugin_active')) {
            include_once(ABSPATH . 'wp-admin/includes/plugin.php');
        }
        $theme = wp_get_theme();
        if (
            is_plugin_active('prdctfltr/prdctfltr.php') ||
            is_plugin_active('xforwoocommerce/xforwoocommerce.php') ||
            is_plugin_active('woocommerce-products-filter/index.php') ||
            is_plugin_active('woo-product-filter/woo-product-filter.php') ||
            is_plugin_active('woocommerce-ajax-layered-nav/ajax_layered_nav-widget.php') ||
            is_plugin_active('woocommerce-ajax-filters/woocommerce-filters.php') ||
            ('Woodmart' == $theme->name || 'Woodmart' == $theme->parent_theme)
        ) {

            if (false === strpos($iwptp_custom_table, ' _only_loop')) { // respect user entered _only_loop='false'
                $only_loop = " _only_loop='true'  ";
            }
        } else if (false === strpos($iwptp_custom_table, ' _only_loop')) {
            // hide result count, orderby and pagination
?>
            <style>
                /* general */
                .woocommerce-result-count,
                .woocommerce-ordering,
                .woocommerce-pagination,

                /* martfury */
                #mf-catalog-toolbar,
                #mf-catalog-toolbar+.mf-toolbar-empty-space
                /* uncode */
                .post-body+.row-navigation,

                /* oceanwp */
                .oceanwp-toolbar.clr

                /* unicon */
                #content .iwptp+#pagination {
                    display: none !important;
                }

                /* uncode */
                .post-body .isotope-container {
                    height: auto !important;
                }

                /* xstore */
                .products-per-page,
                .view-switcher {
                    display: none !important;
                }

                /* riode */
                .toolbox-pagination {
                    display: none !important;
                }

                /* dokan */
                .gridlist-toggle {
                    display: none !important;
                }
            </style>
    <?php
        }

        if (empty($archive)) {
            $archive = " _archive='true' ";
        }

        $_taxonomy = get_query_var('taxonomy') ? " _taxonomy='" . get_query_var('taxonomy') . "' " : "";
        $_term = get_query_var('term') ? " _term='" . get_query_var('term') . "' " : "";

        if (
            get_query_var('taxonomy') == 'product_cat' &&
            !get_query_var('term') &&
            get_query_var('product_cat')
        ) {
            $_term = " _term='" . esc_attr(get_query_var('product_cat')) . "' ";
        }

        $common = " {$archive} {$category} {$attribute} {$taxonomy} {$tag} {$page} {$search} {$only_loop} {$_taxonomy} {$_term} ";

        if ($iwptp_custom_table) {
            $shortcode = str_replace("]", " {$common} ]", $iwptp_custom_table);
        } else {
            $shortcode = "[it_product_table id='" . $iwptp_table_id . "' {$common} ]";
        }

        echo do_shortcode(apply_filters('iwptp_archive_override', $shortcode));

        return true;
    }

    return false;
}

function iwptp_archive_override__get_table_vars($target = '', $term = '')
{
    $iwptp_table_id = false;
    $iwptp_custom_table = false;

    $iwptp_settings = iwptp_get_settings_data();

    if (empty($iwptp_settings['archive_override']['default_custom'])) {
        $iwptp_settings['archive_override']['default_custom'] = '';
    }

    switch ($target) {

        case 'shop':
            //-- none
            if (empty($iwptp_settings['archive_override']['shop'])) {
                $iwptp_table_id = '';

                //-- default
            } else if ($iwptp_settings['archive_override']['shop'] == 'default') {
                $iwptp_table_id = $iwptp_settings['archive_override']['default'];
                $iwptp_custom_table = $iwptp_settings['archive_override']['default_custom'];

                //-- custom
            } else if ($iwptp_settings['archive_override']['shop'] == 'custom') {
                $iwptp_table_id = 'custom';
                $iwptp_custom_table = $iwptp_settings['archive_override']['shop_custom'];

                //-- a table is assigned
            } else {
                $iwptp_table_id = $iwptp_settings['archive_override']['shop'];
            }

            break;

        case 'search':
            //-- none
            if (empty($iwptp_settings['archive_override']['search'])) {
                $iwptp_table_id = '';

                //-- default
            } else if ($iwptp_settings['archive_override']['search'] == 'default') {
                $iwptp_table_id = $iwptp_settings['archive_override']['default'];
                $iwptp_custom_table = $iwptp_settings['archive_override']['default_custom'];

                //-- custom
            } else if ($iwptp_settings['archive_override']['search'] == 'custom') {
                $iwptp_table_id = 'custom';
                $iwptp_custom_table = $iwptp_settings['archive_override']['search_custom'];

                //-- a table is assigned
            } else {
                $iwptp_table_id = $iwptp_settings['archive_override']['search'];
            }

            break;

        case 'category':
            //-- none
            if (empty($iwptp_settings['archive_override']['category']['default'])) {
                $iwptp_table_id = '';

                //-- default
            } else if ($iwptp_settings['archive_override']['category']['default'] == 'default') {
                $iwptp_table_id = $iwptp_settings['archive_override']['default'];
                $iwptp_custom_table = $iwptp_settings['archive_override']['default_custom'];

                //-- custom
            } else if ($iwptp_settings['archive_override']['category']['default'] == 'custom') {
                $iwptp_table_id = 'custom';
                $iwptp_custom_table = $iwptp_settings['archive_override']['category']['custom'];

                //-- a category default table is assigned
            } else {
                $iwptp_table_id = $iwptp_settings['archive_override']['category']['default'];
            }

            // look through other category rules
            if (!empty($iwptp_settings['archive_override']['category']['other_rules'])) {
                foreach ($iwptp_settings['archive_override']['category']['other_rules'] as $rule) {

                    // apply rule
                    if (!empty($rule['category']) && in_array($term, $rule['category'])) {
                        $iwptp_table_id = !empty($rule['table_id']) ? $rule['table_id'] : '';
                        // use default
                        if ($iwptp_table_id == 'default') {
                            $iwptp_table_id = $iwptp_settings['archive_override']['category']['default'];
                            $iwptp_custom_table = $iwptp_settings['archive_override']['category']['custom'];
                        }
                        // custom
                        if ($iwptp_table_id == 'custom') {
                            $iwptp_custom_table = $rule['custom'];
                        } else {
                            $iwptp_custom_table = '';
                        }
                    }
                }
            }

            break;

        case 'tag':
            //-- none
            if (empty($iwptp_settings['archive_override']['tag']['default'])) {
                $iwptp_table_id = '';

                //-- default
            } else if ($iwptp_settings['archive_override']['tag']['default'] == 'default') {
                $iwptp_table_id = $iwptp_settings['archive_override']['default'];
                $iwptp_custom_table = $iwptp_settings['archive_override']['default_custom'];

                //-- custom
            } else if ($iwptp_settings['archive_override']['tag']['default'] == 'custom') {
                $iwptp_table_id = 'custom';
                $iwptp_custom_table = $iwptp_settings['archive_override']['tag']['custom'];

                //-- a tag default table is assigned
            } else {
                $iwptp_table_id = $iwptp_settings['archive_override']['tag']['default'];
            }

            // look through other tag rules
            if (!empty($iwptp_settings['archive_override']['tag']['other_rules'])) {
                foreach ($iwptp_settings['archive_override']['tag']['other_rules'] as $rule) {

                    // apply rule
                    if (!empty($rule['tag']) && in_array($term, $rule['tag'])) {
                        $iwptp_table_id = !empty($rule['table_id']) ? $rule['table_id'] : '';
                        // use default
                        if ($iwptp_table_id == 'default') {
                            $iwptp_table_id = $iwptp_settings['archive_override']['tag']['default'];
                            $iwptp_custom_table = $iwptp_settings['archive_override']['tag']['custom'];
                        }
                        // custom
                        if ($iwptp_table_id == 'custom') {
                            $iwptp_custom_table = $rule['custom'];
                        } else {
                            $iwptp_custom_table = '';
                        }
                    }
                }
            }

            break;

        case 'attribute':
            $attribute = $term;

            // default attribute archive override table
            //-- none
            if (empty($iwptp_settings['archive_override']['attribute']['default'])) {
                $iwptp_table_id = '';

                //-- default
            } else if ($iwptp_settings['archive_override']['attribute']['default'] == 'default') {
                $iwptp_table_id = $iwptp_settings['archive_override']['default'];
                $iwptp_custom_table = $iwptp_settings['archive_override']['default_custom'];

                //-- custom
            } else if ($iwptp_settings['archive_override']['attribute']['default'] == 'custom') {
                $iwptp_table_id = 'custom';
                $iwptp_custom_table = $iwptp_settings['archive_override']['attribute']['custom'];

                //-- a attribute default table is assigned
            } else {
                $iwptp_table_id = $iwptp_settings['archive_override']['attribute']['default'];
            }

            // look through other attribute rules
            if (!empty($iwptp_settings['archive_override']['attribute']['other_rules'])) {
                foreach ($iwptp_settings['archive_override']['attribute']['other_rules'] as $rule) {

                    // apply rule
                    if (!empty($rule['attribute']) && in_array($attribute, $rule['attribute'])) {
                        $iwptp_table_id = !empty($rule['table_id']) ? $rule['table_id'] : '';
                        // use default
                        if ($iwptp_table_id == 'default') {
                            $iwptp_table_id = $iwptp_settings['archive_override']['attribute']['default'];
                            $iwptp_custom_table = $iwptp_settings['archive_override']['attribute']['custom'];
                        }
                        // custom
                        if ($iwptp_table_id == 'custom') {
                            $iwptp_custom_table = $rule['custom'];
                        } else {
                            $iwptp_custom_table = '';
                        }
                    }
                }
            }

            break;

        default:
            break;
    }

    if (!empty($iwptp_table_id) && $iwptp_table_id !== 'custom') {
        $iwptp_custom_table = '';
    }

    return array(
        'iwptp_table_id' => $iwptp_table_id,
        'iwptp_custom_table' => $iwptp_custom_table
    );
}

// check if we are on archive and iwptp override is enabled for the archive
function iwptp_archive__get_table_shortcode($q)
{
    // backend
    if (is_admin() && !wp_doing_ajax()) {
        return false;
    }

    // AJAX
    if (wp_doing_ajax() && !empty($_REQUEST['action']) && $_REQUEST['action'] == 'iwptp_ajax') {
        return true;
    }

    // direct

    if (!wp_doing_ajax()) {

        if (!empty($_GET['action']) && $_GET['action'] == 'elementor') {
            die();
            return false;
        }

        if (!get_queried_object()) {
            return false;
        }

        if (defined('SHOP_IS_ON_FRONT') || is_shop()) {
            extract(iwptp_archive_override__get_table_vars('shop'));
        }

        if (is_search() && get_query_var('post_type') == 'product') {
            extract(iwptp_archive_override__get_table_vars('search'));
        }

        if (is_product_category()) {
            extract(iwptp_archive_override__get_table_vars('category', $q->slug));
        }

        if (is_product_tag()) {
            extract(iwptp_archive_override__get_table_vars('attribute', $q->slug));
        }

        if (is_product_taxonomy() && !is_product_category()) {
            extract(iwptp_archive_override__get_table_vars('attribute', $q->taxonomy));
        }

        // $iwptp_table_id = empty( $iwptp_table_id ) ? '' : $iwptp_table_id;
        // $iwptp_custom_table = empty( $iwptp_custom_table ) ? '' : $iwptp_custom_table;

        // if( 
        //   $iwptp_table_id == 'custom' &&
        //   $iwptp_custom_table
        // ){
        //   $iwptp_table_id = iwptp_extract_id_from_shortcode( $iwptp_custom_table );
        // }

        // return !! $iwptp_table_id;

        $iwptp_table_id = empty($iwptp_table_id) ? '' : $iwptp_table_id;
        $iwptp_custom_table = empty($iwptp_custom_table) ? '' : $iwptp_custom_table;

        $iwptp_shortcode = false;

        if ($iwptp_table_id == 'custom' && $iwptp_custom_table) {
            $iwptp_shortcode = $iwptp_custom_table;
        } else if ($iwptp_table_id !== 'custom' && $iwptp_table_id) {
            $iwptp_shortcode = "[it_product_table id='" . $iwptp_table_id . "' ]";
        }

        return $iwptp_shortcode;
    }

    return false;
}

// remove main query hook if a compatible filter plugin is being used
add_action('plugins_loaded', 'iwptp_archive__unhook');

function iwptp_archive__unhook()
{

    // ensure fn exists to check for active plugins 
    if (!function_exists('is_plugin_active')) {
        include_once(ABSPATH . 'wp-admin/includes/plugin.php');
    }

    if (
        // product filter plugins
        is_plugin_active('prdctfltr/prdctfltr.php') || // plugin: wc product filter (code canyon)
        is_plugin_active('xforwoocommerce/xforwoocommerce.php') || // plugin: X for WooCommerce (code canyon)
        is_plugin_active('woocommerce-products-filter/index.php') || // plugin: woof
        is_plugin_active('woo-product-filter/woo-product-filter.php') || // plugin: woobewoo product filter
        is_plugin_active('woocommerce-ajax-layered-nav/ajax_layered_nav-widget.php') || // plugin: official wc nav filter
        is_plugin_active('woocommerce-ajax-filters/woocommerce-filters.php') || // plugin: berocket wc product filter

        // product search plugins
        is_plugin_active('ajax-search-for-woocommerce/ajax-search-for-woocommerce.php') // plugin: AJAX Search for WC
    ) {
        remove_action('pre_get_posts', 'iwptp_archive__get_post_ids', 1000, 1);
        remove_filter('iwptp_query_args', 'iwptp_archive__use_post_ids');
    }
}

// hook into main query for archive purposes
add_action('pre_get_posts', 'iwptp_archive__get_post_ids', 1000, 1);
function iwptp_archive__get_post_ids($query)
{
    if (!class_exists('WooCommerce') || !$query->is_main_query() || !$archive_shortcode = iwptp_archive__get_table_shortcode($query)) {
        return;
    }

    // stop redirection to single product page
    add_filter('woocommerce_redirect_single_search_result', '__return_false');

    // avoid infinite loop
    remove_action('pre_get_posts', 'iwptp_archive__get_post_ids', 1000, 1);

    // cache query vars
    iwptp_session()->set(iwptp_archive__get_key($query) . '_query_vars', $query->query_vars);

    if (get_query_var('s') && empty($_GET['type_aws'])) {

        // unset( $_GET['dgwt_wcas'] ); // plugin: AJAX Search for WooCommerce

        $keyword = get_query_var('s');

        // store query vars
        define('IWPTPL_QV_S', get_query_var('s'));
        define('IWPTPL_QV_POSTS_PER_PAGE', get_query_var('posts_per_page'));
        define('IWPTPL_QV_PAGE', get_query_var('page'));
        define('IWPTPL_QV_FIELDS', get_query_var('fields'));
        define('IWPTPL_QV_ORDERBY', get_query_var('orderby'));
        if (get_query_var('tax_query')) {
            define('IWPTPL_QV_TAX_QUERY', wp_json_encode(get_query_var('tax_query')));
        } else {
            define('IWPTPL_QV_TAX_QUERY', '');
        }

        // modify query vars
        set_query_var('s', false);
        set_query_var('posts_per_page', -1);
        set_query_var('page', 1);
        set_query_var('fields', 'ids');

        // include_hidden="true"
        if (FALSE !== strpos($archive_shortcode, 'include_hidden') && get_query_var('tax_query')) {

            $product_visibility_terms  = wc_get_product_visibility_term_ids();
            $remove_term = $product_visibility_terms['exclude-from-search'];

            $tax_query = get_query_var('tax_query');

            foreach ($tax_query as $key => &$condition) {
                if (
                    is_array($condition) &&
                    !empty($condition['taxonomy']) &&
                    $condition['taxonomy'] == 'product_visibility'
                ) {
                    if ($condition['terms'] == $remove_term) {
                        unset($tax_query[$key]);
                    } else if (
                        is_array($condition['terms']) &&
                        (($index = array_search($remove_term, $condition['terms'])) !== FALSE)
                    ) {
                        array_splice($condition['terms'], $index, 1);
                    }
                }
            }

            set_query_var('tax_query', $tax_query);
        }

        // restore query vars (later)
        add_action('wp', 'iwptp_archive__restore_query_vars');

        // do search, replace post__in with results
        $settings = iwptp_get_settings_data();

        $search_settings = $settings['search'];
        $ov_settings = $search_settings['override_settings'];

        $target = empty($ov_settings['target']) ? [] : $ov_settings['target'];
        $custom_fields = empty($ov_settings['custom_fields']) ? [] : $ov_settings['custom_fields'];
        $attributes = empty($ov_settings['attributes']) ? [] : $ov_settings['attributes'];

        $keyword_separator = ' ';

        $filter_info = array(
            'keyword' => $keyword,
            'target'    => $target,
            'custom_fields'    => $custom_fields,
            'attributes' => $attributes,
            'keyword_separator'    => $keyword_separator,
        );

        if (FALSE !== strpos($archive_shortcode, 'use_default_search')) {
            add_filter('iwptp_search_args', 'iwptp_search_args__use_default_search');
        } else {
            remove_filter('iwptp_search_args', 'iwptp_search_args__use_default_search');
        }

        $post__in = [];
        iwptp_search($filter_info, $post__in);
        set_query_var('post__in', $post__in);
        set_query_var('orderby', 'post__in');

        // cache search result ids when $_SESSION is ready
        add_action('wp', 'iwptp_archive__cache_ids');
    }
}

function iwptp_archive__restore_query_vars()
{
    set_query_var('s', IWPTPL_QV_S);
    set_query_var('post_per_page', IWPTPL_QV_POSTS_PER_PAGE);
    set_query_var('page', IWPTPL_QV_PAGE);
    set_query_var('fields', IWPTPL_QV_FIELDS);
    set_query_var('orderby', IWPTPL_QV_ORDERBY);

    if (IWPTPL_QV_TAX_QUERY) {
        set_query_var('tax_query', iwptp_sanitize_array(json_decode(IWPTPL_QV_TAX_QUERY, true)));
    } else {
        set_query_var('tax_query', '');
    }
}

function iwptp_archive__cache_ids()
{
    global $wp_query;
    iwptp_session()->set(iwptp_archive__get_key(), $wp_query->posts);
}

// use post ids gathered from main query
add_filter('iwptp_query_args', 'iwptp_archive__use_post_ids');
function iwptp_archive__use_post_ids($query_vars)
{
    $table_data = iwptp_get_table_data();
    $sc_attrs = $table_data['query']['sc_attrs'];

    if (empty($sc_attrs['_archive'])) {
        return $query_vars;
    }

    $post_ids = !empty($query_vars['post__in']) ? $query_vars['post__in'] : false;

    // do nothing if no cache
    if (!$cached_post_ids = iwptp_session()->get(iwptp_archive__get_key())) {
        return $query_vars;
    }

    if (empty($post_ids)) {
        $post_ids = $cached_post_ids;
    } else {
        $post_ids = array_intersect($post_ids, $cached_post_ids);

        if (empty($post_ids)) {
            $post_ids = array(0);
        }
    }

    $query_vars['post__in'] = $post_ids;

    return $query_vars;
}

function iwptp_archive__get_key($query = false)
{
    // when caller: iwptp_archive__use_cached_ids  
    if ($table_data = iwptp_get_table_data()) {

        $sc_attrs = $table_data['query']['sc_attrs'];
        $archive = in_array($sc_attrs['_archive'], array('shop', 'search')) ? '_' . $sc_attrs['_archive'] : '';
        $taxonomy = !empty($sc_attrs['_taxonomy']) ? '_' . $sc_attrs['_taxonomy'] : '';
        $term = !empty($sc_attrs['_term']) ? '_' . $sc_attrs['_term'] : '';

        // when caller: iwptp_archive__get_post_ids    
    } else {

        $archive = '';
        if (is_search()) {
            $archive = '_search';
        } else if (
            defined('SHOP_IS_ON_FRONT') ||
            is_shop()
        ) {
            $archive = '_shop';
        }

        if (
            !empty($query) &&
            !empty($query->get('product_cat'))
        ) {
            $taxonomy = '_product_cat';
            $term = '_' . $query->get('product_cat');
        } else {
            $taxonomy = !empty(get_query_var('taxonomy')) ? '_' . get_query_var('taxonomy') : '';
            $term = !empty(get_query_var('term')) ? '_' . get_query_var('term') : '';
        }
    }

    return 'iwptp' . $archive . $taxonomy . $term . '_post_ids';
}

// archive: search

// -- carry search params
add_action('wp_print_scripts', 'iwptp_search_archive__js_params');
function iwptp_search_archive__js_params()
{
    ?>
    <script>
        if (typeof iwptp_persist_params === 'undefined') {
            var iwptp_persist_params = [];
        }
        iwptp_persist_params.push('post_type', 's', 'term', 'taxonomy');
    </script>
<?php
}

// -- ensure main category is applied when no other is applied
add_filter('iwptp_query_args', 'iwptp_category_archive__ensure_term');
function iwptp_category_archive__ensure_term($query_args)
{
    if (
        !empty($sc_attrs['_archive']) &&
        !empty($sc_attrs['_taxonomy']) &&
        $sc_attrs['_taxonomy'] == 'product_cat' &&
        !empty($sc_attrs['_term'])
    ) {
        $data = iwptp_get_table_data();
        $table_id = $data['id'];
        $sc_attrs = $data['query']['sc_attrs'];

        if (empty($_GET[$table_id . '_product_cat'])) {
            $term = get_term_by('slug', $sc_attrs['_term'], 'product_cat');

            foreach ($query_args['tax_query'] as &$tax_query) {
                if ($tax_query['taxonomy'] == 'product_cat') {
                    $tax_query['terms'][] = $term->term_id;
                }
            }
        }
    }

    return $query_args;
}

function iwptp_archive__get_table_query_args($sc)
{
    $sc = rtrim($sc, ']') . ' _return_query_args="true"]';
    $args = iwptp_sanitize_array(json_decode(do_shortcode($sc, true), true));

    return $args;
}

add_filter('iwptp_parse_attributes', 'iwptp_archive__blank_query');
function iwptp_archive__blank_query($attrs)
{
    $data = iwptp_get_table_data();

    if (!empty($data['query']['sc_attrs']['_return_query_args'])) {
        $attrs['terms'] = $attrs['attribute'] = $attrs['nav_category'] = $attrs['nav_category_id'] = $attrs['category'] = $attrs['ids'] = $attrs['skus'] = '';
    }

    return $attrs;
}
