<?php
/*
Plugin Name: iThemeland Woocommerce Product Table Pro Lite
Plugin URI: https://ithemelandco.com/woocommerce-product-table-pro
Description: Allows you to list your WooCommerce products in a searchable and sortable table layout.
Author: iThemelandco
Tested up to: WP 5.8.1
Requires PHP: 5.4
Tags: woocommerce,woocommerce product table,product table
Text Domain: ithemeland-woocommerce-product-table-pro-lite
Domain Path: /languages
Requires Plugins: woocommerce
WC requires at least: 3.9
WC tested up to: 8.9
Requires at least: 4.4
Version: 1.1.2
Author URI: https://www.ithemelandco.com
 */

defined('ABSPATH') || exit();

if (defined('IWPTP_PRO')) {
    return;
}

define('IWPTPL_VERSION', '1.1.2');
define('IWPTPL_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('IWPTPL_PLUGIN_URL', plugin_dir_url(__FILE__));
define('IWPTPL_NAME', 'ithemeland-woocommerce-product-table-pro-lite');
define('IWPTPL_LABEL', 'iThemeland Woocommerce Product Table Pro Lite');
define('IWPTPL_DESCRIPTION', __("Allows you to list your WooCommerce products in a searchable and sortable table layout.", 'ithemeland-woocommerce-product-table-pro-lite'));
define('IWPTPL_MAIN_PAGE', admin_url('edit.php?post_type=iwptp_product_table'));
define('IWPTPL_ACTIVATION_PAGE', admin_url('edit.php?post_type=iwptp_product_table&page=iwptp-activation'));
define('IWPTPL_PLUGIN', IWPTPL_NAME . '/' . IWPTPL_NAME . '.php');
define('IWPTPL_IMAGES_URL', trailingslashit(IWPTPL_PLUGIN_URL . 'assets/images'));
define('IWPTPL_EDITOR_IMAGES_URL', trailingslashit(IWPTPL_PLUGIN_URL . 'editor/assets/images'));
define('IWPTPL_CAP', 'edit_iwptp_product_tables');
define('IWPTPL_STYLE_PRESETS_OPTION', 'iwptp_style_presets');
define('IWPTPL_TABLE_PRESETS_OPTION', 'iwptp_table_presets');

require_once IWPTPL_PLUGIN_PATH . 'helpers.php';
require_once IWPTPL_PLUGIN_PATH . 'presets/table-presets.php';
require_once IWPTPL_PLUGIN_PATH . 'presets/style-presets.php';
require_once IWPTPL_PLUGIN_PATH . 'class-flush-message.php';
require_once IWPTPL_PLUGIN_PATH . 'global-functions.php';
require_once IWPTPL_PLUGIN_PATH . 'requests/ajax-handler.php';
require_once IWPTPL_PLUGIN_PATH . 'requests/post-handler.php';
require_once IWPTPL_PLUGIN_PATH . 'style-functions.php';
require_once IWPTPL_PLUGIN_PATH . 'activation/class-activation-controller.php';

require_once IWPTPL_PLUGIN_PATH . 'class-top-banners.php';


$IWPTPL_CHECKBOX_TRIGGER_DATA = array(
    'toggle' => 'enabled',
    'r_toggle' => 'enabled',
    'link' => '',
    'labels' => array(
        'label' => "en_US: Add selected ([n]) to cart\r\nfr_FR: Ajouter des produits ([n]) au panier",
    ),
    'style' => array(
        'background-color' => '#4CAF50',
        'border-color' => 'rgba(0, 0, 0, .1)',
        'color' => 'rgba(255, 255, 255)',
    ),
);

// compatible with woocommerce custom order tables
add_action('before_woocommerce_init', function () {
    if (class_exists(\Automattic\WooCommerce\Utilities\FeaturesUtil::class)) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', __FILE__, true);
    }
});

/* flush rewrites upon activation */
register_activation_hook(__FILE__, function () {
    flush_rewrite_rules();
});

if (!class_exists('IWPTPL_INIT')) {
    class IWPTPL_INIT
    {
        public function __construct()
        {
            add_shortcode('it_product_table', 'iwptp_shortcode_product_table');
            add_action('plugins_loaded', [$this, 'load_textdomain']);
            add_action('init', [$this, 'register_post_type']);
            add_action('plugins_loaded', [$this, 'redirect_to_table_editor']);
            add_action('admin_menu', [$this, 'hook_menu_pages']);
            add_action('admin_menu', [$this, 'correct_menu_highlight']);
            add_filter('woocommerce_product_data_store_cpt_get_products_query', [$this, 'custom_query_var'], 10, 2);
            add_action('admin_notices', [$this, 'min_spec_warning']);
            add_action('admin_enqueue_scripts', [$this, 'admin_enqueue_scripts']);
            add_action('admin_print_scripts', [$this, 'admin_print_scripts']);
            add_action('wp_enqueue_scripts', [$this, 'front_enqueue_scripts']);
            add_action('init', [$this, 'set_permitted_shortcode_attributes']);
            add_filter('post_row_actions', [$this, 'row_buttons'], 10, 2);

            if ((!empty($_REQUEST['action']) && $_REQUEST['action'] === 'iwptp_add_to_cart') || !empty($_REQUEST['iwptp_payload']) || !empty($_REQUEST['iwptp_request'])) {
                add_filter('woocommerce_add_to_cart_redirect', '__return_false', 10000);
            }

            add_action('wp_loaded', [$this, 'cart_works'], 15);
            add_filter('woocommerce_add_error', [$this, 'woocommerce_add_error'], 10);
            add_filter('woocommerce_add_cart_item_data', [$this, 'woocommerce_add_cart_item_data'], 10, 2);
            add_action('woocommerce_checkout_create_order_line_item', array($this, 'woocommerce_checkout_create_order_line_item'), 10, 4);
            add_filter('woocommerce_order_item_display_meta_key', [$this, 'woocommerce_order_item_display_meta_key'], 10, 1);
            add_filter('iwptp_element_markup', [$this, 'safari_dollar_fix'], 10, 2);
            add_filter('iwptp_data', [$this, 'include_new_child_categories'], 10, 2);
            add_filter('admin_body_class', [$this, 'set_editor_theme']);

            IWPTPL_Top_Banners::register();

            Ajax_Handler::register_callback();
            Post_Handler::register_callback();
        }

        public function woocommerce_order_item_display_meta_key($display_key)
        {
            if ($display_key == 'iwptp_short_message') {
                $display_key = __('Message', 'ithemeland-woocommerce-product-table-pro-lite');
            }

            return $display_key;
        }

        public function woocommerce_checkout_create_order_line_item($item, $cart_item_key, $values, $order)
        {
            if (isset($values['iwptp_short_message'])) {
                $item->add_meta_data('iwptp_short_message', sanitize_text_field($values['iwptp_short_message']));
            }
        }

        public function woocommerce_add_cart_item_data($cart_item_data, $product_id)
        {
            if (!empty($_REQUEST['iwptp_payload']['short_message'])) {
                $cart_item_data['iwptp_short_message'] = sanitize_text_field($_REQUEST['iwptp_payload']['short_message']);
            }

            return $cart_item_data;
        }

        public function set_editor_theme($classes)
        {
            if (!isset($_GET['page']) || $_GET['page'] != 'iwptp-edit') {
                return false;
            }

            $class_name = get_option('iwptp_editor_theme', 'iwptp-dark-theme');
            if (is_array($classes)) {
                $classes[] = $class_name;
            } else {
                $classes .= ' ' . $class_name;
            }

            return $classes;
        }

        public function load_textdomain()
        {
            load_plugin_textdomain('ithemeland-woocommerce-product-table-pro-lite', false, basename(dirname(__FILE__)) . '/languages');
        }

        public function register_post_type()
        {
            register_post_type(
                'iwptp_product_table',
                array(
                    'labels' => array(
                        'name' => __('iT Product Tables', 'ithemeland-woocommerce-product-table-pro-lite'),
                        'singular_name' => __('Product Table', 'ithemeland-woocommerce-product-table-pro-lite'),
                        'menu_name' => __('iT Product Tables', 'ithemeland-woocommerce-product-table-pro-lite'),
                        'add_new' => __('Add New Product Table', 'ithemeland-woocommerce-product-table-pro-lite'),
                    ),
                    'description' => __('Easily display your WooCommerce products in responsive tables.', 'ithemeland-woocommerce-product-table-pro-lite'),
                    'public' => true,
                    'has_archive' => true,
                    'show_in_menu' => false,
                    // 'menu_icon' => 'dashicons-editor-justify',
                    'rewrite' => array('slug' => 'iwptp-product-table'),
                    'capability_type' => 'iwptp_product_table',
                    'map_meta_cap' => true,
                    'supports' => [],
                    'hierarchical' => false,
                    'show_in_nav_menus' => true,
                    'publicly_queryable' => false,
                    'exclude_from_search' => true,
                    'can_export' => true,
                ),
            );

            $admins = get_role('administrator');

            $admins->add_cap('create_iwptp_product_tables');
            $admins->add_cap('edit_iwptp_product_table');
            $admins->add_cap('edit_iwptp_product_tables');
            $admins->add_cap('edit_others_iwptp_product_tables');
            $admins->add_cap('edit_published_iwptp_product_tables');
            $admins->add_cap('publish_iwptp_product_tables');
            $admins->add_cap('read_iwptp_product_table');
            $admins->add_cap('read_private_iwptp_product_tables');
            $admins->add_cap('delete_iwptp_product_table');
            $admins->add_cap('delete_iwptp_product_tables');
            $admins->add_cap('delete_published_iwptp_product_tables');
        }

        public function redirect_to_table_editor()
        {
            global $pagenow;

            // edit
            if ($pagenow == 'post.php' && isset($_GET['post']) && isset($_GET['action']) && $_GET['action'] == 'edit') {
                $post_id = intval($_GET['post']);
                $post = get_post_type($post_id);
                if ($post === 'iwptp_product_table') {
                    wp_redirect(admin_url('/edit.php?post_type=iwptp_product_table&page=iwptp-edit&post_id=' . $post_id));
                    exit;
                }
            }

            // add
            if ($pagenow == 'post-new.php' && isset($_GET['post_type']) && $_GET['post_type'] == 'iwptp_product_table') {
                wp_redirect(admin_url('/edit.php?post_type=iwptp_product_table&page=iwptp-edit'));
                exit;
            }
        }

        public function hook_menu_pages()
        {
            add_menu_page(__('iT Product Table', 'ithemeland-woocommerce-product-table-pro-lite'), wp_kses('<span style="color: #627ddd;font-weight: 900;">iT</span> Product Table', iwptp_allowed_html_tags()), IWPTPL_CAP, 'edit.php?post_type=iwptp_product_table', '', esc_url(IWPTPL_IMAGES_URL) . 'iwptp_icon.png', 2);
            add_submenu_page('edit.php?post_type=iwptp_product_table', __('All Tables', 'ithemeland-woocommerce-product-table-pro-lite'), __('All Tables', 'ithemeland-woocommerce-product-table-pro-lite'), IWPTPL_CAP, 'edit.php?post_type=iwptp_product_table', '', 1);
            add_submenu_page('edit.php?post_type=iwptp_product_table', 'IT Product Table', 'Add New Table', IWPTPL_CAP, 'iwptp-edit', 'iwptp_editor_page');
            add_submenu_page('edit.php?post_type=iwptp_product_table', 'Settings', 'Settings', IWPTPL_CAP, 'iwptp-settings', 'iwptp_settings_page');
            add_submenu_page('edit.php?post_type=iwptp_product_table', 'Activation', 'Activation', IWPTPL_CAP, 'iwptp-activation', [new IWPTPL_Activation_Controller(), 'index'], 15);

            if (
                isset($_GET['post_type'])
                && $_GET['post_type'] == 'iwptp_product_table'
                && !IWPTPL_Verification::is_active()
                && !IWPTPL_Verification::skipped()
                && (!isset($_GET['page']) || $_GET['page'] != 'iwptp-activation')
            ) {
                wp_redirect(IWPTPL_ACTIVATION_PAGE);
                die();
            }
        }

        public function correct_menu_highlight()
        {
            if (isset($_GET['post_type']) && $_GET['post_type'] === 'iwptp_product_table' && isset($_GET['page']) && $_GET['page'] === 'iwptp-edit' && !empty($_GET['post_id'])) {
                global $submenu_file;
                $submenu_file = "edit.php?post_type=iwptp_product_table";
            }
        }

        public function custom_query_var($query, $query_vars)
        {
            if (isset($query_vars['like_name']) && !empty($query_vars['like_name'])) {
                $query['s'] = esc_attr($query_vars['like_name']);
            }

            return $query;
        }

        public function min_spec_warning()
        {
            $errors = false;
            // check if php version is compatible
            if (version_compare(PHP_VERSION, '5.4.0') < 0) {
                $errors = true;
                echo '<div class="notice notice-error iwptp-needs-woocommerce">
                    <p>
                        ' . esc_html__('WooCommerce Product Table requires at least PHP 5.4.0. Please request you webhost to update your PHP version or run the plugin on another server to avoid incompatibility issues and unexpected behaviour.', 'ithemeland-woocommerce-product-table-pro-lite') . '
                    </p>
                </div>';
            }

            // check if wordpress version is compatible
            if (version_compare($GLOBALS['wp_version'], '4.9.0') < 0) {
                $errors = true;
                echo '<div class="notice notice-error iwptp-needs-woocommerce">
                    <p>
                        ' . esc_html__('WooCommerce Product Table requires at least WordPress 4.9.0. Please update your WordPress version to avoid incompatibility issues and unexpected behaviour.', 'ithemeland-woocommerce-product-table-pro-lite') . '
                    </p>
                </div>';
            }

            // check if woocommerce is installed
            if (!class_exists('WooCommerce')) {
                $errors = true;
                echo '<div class="notice notice-error iwptp-needs-woocommerce">
                    <p>
                        ' . esc_html__('WooCommerce Product Table needs the WooCommerce plugin to be installed and activated on your site!', 'ithemeland-woocommerce-product-table-pro-lite') . '
                        <a href="' . esc_url(get_admin_url(false, "/plugin-install.php?s=woocommerce&tab=search&type=term")) . '" target="_blank">' . esc_html__('Install now?', 'ithemeland-woocommerce-product-table-pro-lite') . '</a>
                    </p>
                </div>';
            }

            // check if woocommerce version is compatible
            $wc_version_compat = true;
            if (class_exists('WooCommerce')) {
                $wc_info = get_plugin_data(WP_PLUGIN_DIR . '/woocommerce/woocommerce.php');

                if (version_compare($wc_info['Version'], '3.4.4') < 0) {
                    $errors = true;
                    $wc_version_compat = false;
                    echo '<div class="notice notice-error iwptp-needs-woocommerce">
                        <p>
                            ' . esc_html__('WooCommerce Product Table requires at least WooCommerce 3.4.4. Please update your WooCommerce version to avoid incompatibility issues and unexpected behaviour.', 'ithemeland-woocommerce-product-table-pro-lite') . '
                        </p>
                    </div>';
                }
            }

            // check if woocommerce products exist version is compatible
            if (class_exists('WooCommerce') && $wc_version_compat && !empty($_GET['post_type']) && $_GET['post_type'] === 'iwptp_product_table') {
                $query = new WP_Query(array(
                    'post_type' => 'product',
                    'posts_per_page' => 1,
                    'post_status' => 'publish',
                ));

                if (!$query->found_posts) {
                    echo '<div class="notice notice-error iwptp-needs-woocommerce">
                        <p>
                            ' . esc_html__('WooCommerce Product Table (IWPTPL) could not find a single \'published\' WooCommerce product on your site! IWPTPL cannot display any products in tables if you do not have any published products on your site. See:', 'ithemeland-woocommerce-product-table-pro-lite') . '
                            <a href="https://docs.woocommerce.com/document/managing-products/" target="_blank">' . esc_html__('How to add WooCommerce products', 'ithemeland-woocommerce-product-table-pro-lite') . '</a>
                        </p>
                    </div>';
                }
            }

            if (!$errors) {
                return;
            }

            echo '<style media="screen">
                .wp-admin.post-type-iwptp #posts-filter,
                .wp-admin.post-type-iwptp .subsubsub,
                #menu-posts-iwptp .wp-submenu,
                #menu-posts-iwptp:after {
                    display: none;
                }

                .wp-admin.post-type-iwptp .iwptp-needs-woocommerce {
                    margin-top: 10px;
                }

                .wp-admin.post-type-iwptp .iwptp-needs-woocommerce p {
                    font-size: 18px;
                }

                .plugin-card-woocommerce {
                    border: 4px solid #03A9F4;
                    animation: iwptp-pulse 1s infinite;
                }

                .plugin-card-woocommerce:hover {
                    animation: none;
                }

                @-webkit-keyframes iwptp-pulse {
                    0% {
                        -webkit-box-shadow: 0 0 0 0 rgba(3, 169, 244, 1);
                    }

                    70% {
                        -webkit-box-shadow: 0 0 0 15px rgba(3, 169, 244, 0);
                    }

                    100% {
                        -webkit-box-shadow: 0 0 0 0 rgba(3, 169, 244, 0);
                    }
                }

                @keyframes iwptp-pulse {
                    0% {
                        -moz-box-shadow: 0 0 0 0 rgba(3, 169, 244, 1);
                        box-shadow: 0 0 0 0 rgba(3, 169, 244, 1);
                    }

                    70% {
                        -moz-box-shadow: 0 0 0 15px rgba(3, 169, 244, 0);
                        box-shadow: 0 0 0 15px rgba(3, 169, 244, 0);
                    }

                    100% {
                        -moz-box-shadow: 0 0 0 0 rgba(3, 169, 244, 0);
                        box-shadow: 0 0 0 0 rgba(3, 169, 244, 0);
                    }
                }
            </style>';
        }

        public function admin_enqueue_scripts()
        {
            wp_enqueue_script('iwptp-google-fonts-1', 'https://fonts.googleapis.com/css?family=Roboto%3A100%2C100italic%2C200%2C200italic%2C300%2C300italic%2C400%2C400italic%2C500%2C500italic%2C600%2C600italic%2C700%2C700italic%2C800%2C800italic%2C900%2C900italic%7CRoboto+Slab%3A100%2C100italic%2C200%2C200italic%2C300%2C300italic%2C400%2C400italic%2C500%2C500italic%2C600%2C600italic%2C700%2C700italic%2C800%2C800italic%2C900%2C900italic%7CRock+Salt%3A100%2C100italic%2C200%2C200italic%2C300%2C300italic%2C400%2C400italic%2C500%2C500italic%2C600%2C600italic%2C700%2C700italic%2C800%2C800italic%2C900%2C900italic&amp;display=auto&amp;ver=6.0.2', [], IWPTPL_VERSION);

            // common assets
            if (!empty($_GET['post_type']) && $_GET['post_type'] == 'iwptp_product_table') {
                wp_enqueue_style('iwptp-main-common', plugin_dir_url(__FILE__) . 'assets/iwptp-common.css', [], IWPTPL_VERSION);
                wp_enqueue_script('iwptp-main-common', plugin_dir_url(__FILE__) . 'assets/iwptp-common.js', [], IWPTPL_VERSION);
            }

            $screen = get_current_screen();
            if ($screen->id == 'edit-iwptp_product_table') {
                wp_enqueue_style('iwptp-reset-css', plugin_dir_url(__FILE__) . 'editor/assets/css/reset.css', [], IWPTPL_VERSION);
                wp_enqueue_style('iwptp-list', plugin_dir_url(__FILE__) . 'editor/assets/css/iwptp-list.css', [], IWPTPL_VERSION);

                wp_enqueue_script('iwptp-list', plugin_dir_url(__FILE__) . 'editor/assets/js/iwptp-list.js', [], IWPTPL_VERSION);
                wp_localize_script('iwptp-list', 'IWPTPL', [
                    'title' => IWPTPL_LABEL,
                    'icon' => esc_url(IWPTPL_IMAGES_URL . 'iwptp_icon_lg.png'),
                    'description' => IWPTPL_DESCRIPTION,
                ]);
            }

            if (!isset($_GET['page']) || !in_array($_GET['page'], array('iwptp-edit', 'iwptp-settings'))) {
                return;
            }

            wp_enqueue_style('Ubuntu', 'https://fonts.googleapis.com/css?family=Ubuntu:300,400,500,600');
            wp_enqueue_style('iwptp-reset-css', plugin_dir_url(__FILE__) . 'editor/assets/css/reset.css', [], IWPTPL_VERSION);
            wp_enqueue_style('iwptp-tipsy', plugin_dir_url(__FILE__) . 'assets/jquery.tipsy.css', [], IWPTPL_VERSION);
            wp_enqueue_style('iwptp-modal', plugin_dir_url(__FILE__) . 'assets/iwptp-modal.css', [], IWPTPL_VERSION);
            wp_enqueue_style('iwptp-sweetalert', plugin_dir_url(__FILE__) . 'assets/sweetalert.css', [], IWPTPL_VERSION);
            wp_enqueue_style('iwptp-line-icons', plugin_dir_url(__FILE__) . 'editor/assets/css/LineIcons.min.css', [], IWPTPL_VERSION);
            wp_enqueue_style('iwptp-select2', plugin_dir_url(__FILE__) . 'editor/assets/css/select2.css');

            wp_enqueue_script('iwptp-tipsy', plugin_dir_url(__FILE__) . 'assets/jquery.tipsy.js', array('jquery'), IWPTPL_VERSION);
            wp_enqueue_script('iwptp-sweetalert', plugin_dir_url(__FILE__) . 'assets/sweetalert.min.js', array('jquery'), IWPTPL_VERSION);
            wp_enqueue_script('iwptp-modal', plugin_dir_url(__FILE__) . 'assets/iwptp-modal.js', array('jquery'), IWPTPL_VERSION);
            wp_enqueue_script('iwptp-select2', plugin_dir_url(__FILE__) . 'editor/assets/js/select2.min.js');
            wp_dequeue_script('gmwqp_select2_js');

            wp_enqueue_style('iwptp', plugin_dir_url(__FILE__) . 'assets/css.css', [], IWPTPL_VERSION);

            wp_enqueue_style('iwptp-editor', plugin_dir_url(__FILE__) . 'editor/assets/css/editor.css', [], IWPTPL_VERSION);
            wp_enqueue_style('iwptp-editor-common', plugin_dir_url(__FILE__) . 'editor/assets/css/common.css', [], IWPTPL_VERSION);
            wp_enqueue_style('spectrum', plugin_dir_url(__FILE__) . 'editor/assets/css/spectrum.min.css', [], IWPTPL_VERSION);
            wp_enqueue_style('iwptp-block-editor', plugin_dir_url(__FILE__) . 'editor/partials/element-editor/block-editor/block-editor.css', [], IWPTPL_VERSION);
            wp_enqueue_style('iwptp-tabs', plugin_dir_url(__FILE__) . 'editor/partials/element-editor/tabs/tabs.css', [], IWPTPL_VERSION);
            wp_enqueue_style('iwptp-element-editor', plugin_dir_url(__FILE__) . 'editor/partials/element-editor/element-editor.css', [], IWPTPL_VERSION);

            // JS
            wp_enqueue_script('iwptp-dominator', plugin_dir_url(__FILE__) . 'editor/assets/js/dominator_ui.js', array('jquery'), IWPTPL_VERSION);
            wp_enqueue_script('wp-util');
            wp_enqueue_script('spectrum', plugin_dir_url(__FILE__) . 'editor/assets/js/spectrum.min.js', array('jquery'), IWPTPL_VERSION);
            wp_enqueue_media();
            wp_enqueue_script('iwptp-block-editor', plugin_dir_url(__FILE__) . 'editor/partials/element-editor/block-editor/block-editor.js', array('jquery'), IWPTPL_VERSION, true);
            wp_enqueue_script('iwptp-block-editor-model', plugin_dir_url(__FILE__) . 'editor/partials/element-editor/block-editor/block-editor-model.js', array('jquery', 'iwptp-block-editor'), IWPTPL_VERSION, true);
            wp_enqueue_script('iwptp-block-editor-view', plugin_dir_url(__FILE__) . 'editor/partials/element-editor/block-editor/block-editor-view.js', array('jquery', 'iwptp-block-editor'), IWPTPL_VERSION, true);
            wp_enqueue_script('iwptp-block-editor-controller', plugin_dir_url(__FILE__) . 'editor/partials/element-editor/block-editor/block-editor-controller.js', array('jquery', 'iwptp-block-editor'), IWPTPL_VERSION, true);
            wp_enqueue_script('iwptp-tabs', plugin_dir_url(__FILE__) . 'editor/partials/element-editor/tabs/tabs.js', array('jquery'), IWPTPL_VERSION, true);
            wp_enqueue_script('iwptp-element-editor', plugin_dir_url(__FILE__) . 'editor/partials/element-editor/element-editor.js', array('jquery', 'iwptp-dominator'), IWPTPL_VERSION, true);
            wp_localize_script('iwptp-element-editor', 'IWPTP_DATA', [
                'ajax_url' => admin_url('admin-ajax.php'),
                'ajax_nonce' => wp_create_nonce('iwptp_ajax_nonce')
            ]);

            wp_enqueue_script('iwptp-controller', plugin_dir_url(__FILE__) . 'editor/assets/js/controller.js', array('jquery', 'iwptp-dominator'), IWPTPL_VERSION, true);
            wp_add_inline_script('iwptp-controller', 'var iwptp_version = "' . IWPTPL_VERSION . '";', 'after');
            wp_enqueue_script('iwptp-feedback-anim', plugin_dir_url(__FILE__) . 'editor/assets/js/feedback_anim.js', array('iwptp-controller'), IWPTPL_VERSION, true);
            wp_enqueue_script('jquery-ui-sortable', array('jquery'), false, true);
            wp_localize_script('iwptp-controller', 'IWPTPL_DATA', [
                'ajax_url' => admin_url('admin-ajax.php'),
                'ajax_nonce' => wp_create_nonce('iwptp_ajax_nonce')
            ]);

            if ($_GET['page'] == 'iwptp-settings') {
                wp_enqueue_style('iwptp-settings', plugin_dir_url(__FILE__) . 'editor/assets/css/iwptp-settings.css', [], IWPTPL_VERSION);
                wp_enqueue_script('iwptp-settings', plugin_dir_url(__FILE__) . 'editor/assets/js/iwptp-settings.js', array('jquery'), IWPTPL_VERSION);
            } else {
                wp_enqueue_script('iwptp-editor-events', plugin_dir_url(__FILE__) . 'editor/assets/js/iwptp-events.js', array('jquery', 'iwptp-select2'), IWPTPL_VERSION, true);
            }
        }

        public function admin_print_scripts()
        {
            echo '<script>
                var iwptp_icons = "' . esc_url(IWPTPL_PLUGIN_URL) . 'assets/feather/";
            </script>

            <style media="screen">
                #menu-posts-iwptp_product_table .wp-submenu li:nth-child(3) {
                    display: none;
                }
            </style>';
        }

        public function front_enqueue_scripts()
        {
            require_once IWPTPL_PLUGIN_PATH . 'front-scripts.php';
        }

        public function set_permitted_shortcode_attributes()
        {
            $GLOBALS['iwptp_permitted_shortcode_attributes'] = apply_filters('iwptp_permitted_shortcode_attributes', array(
                'id',
                'name',
                'offset',
                'limit',
                'category',
                'orderby',
                'order',
                'ids',
                'skus',
                'use_default_search',
                'class',
                'laptop_auto_scroll',
                'tablet_auto_scroll',
                'phone_auto_scroll',
                'laptop_scroll_offset',
                'tablet_scroll_offset',
                'phone_scroll_offset',
                'disable_url_update',
                'disable_ajax',
                'html_class',
            ));
        }

        public function row_buttons($actions, $post)
        {
            if ($post->post_type == 'iwptp_product_table') {
                unset($actions['inline hide-if-no-js'], $actions['view']);
            }
            return $actions;
        }

        public function cart_works()
        {
            if (empty($_REQUEST['iwptp_payload']) || empty($_REQUEST['iwptp_payload']['products'])) {
                return;
            }

            $_REQUEST['_iwptp_payload'] = iwptp_sanitize_array($_REQUEST['iwptp_payload']);

            if (!empty($_REQUEST['iwptp_payload']['short_message'])) {
                $_REQUEST['iwptp_short_message'] = sanitize_text_field($_REQUEST['iwptp_payload']['short_message']);
            }

            add_filter('woocommerce_add_error', [$this, 'woocommerce_add_error'], 10);

            // addons - official Woocommerce Product Addons
            if (class_exists('WC_Product_Addons_Helper') || function_exists('get_product_addons')) {
                // don't need to sync with product loop, each product addon name is based on product id
                if (!empty($_REQUEST['iwptp_payload']['addons'])) {
                    foreach ($_REQUEST['iwptp_payload']['addons'] as $product_id => $addons) {
                        foreach ($addons as $key => $val) {
                            $_POST[sanitize_text_field($key)] = (is_array($val)) ? iwptp_sanitize_array($val) : sanitize_text_field($val);
                        }
                    }
                }
            }

            foreach ($_REQUEST['iwptp_payload']['products'] as $product_id => $qty) {
                $product_id = apply_filters('woocommerce_add_to_cart_product_id', absint($product_id));
                $adding_to_cart = wc_get_product(intval($product_id));

                if (!$adding_to_cart) {
                    continue;
                }

                if (!empty($_REQUEST['iwptp_payload']['overwrite_cart_qty'])) {
                    $cart = WC()->instance()->cart;
                    $found = false;
                    $cart_contents = $cart->get_cart_contents();
                    foreach ($cart_contents as $key => $item) {
                        if ($item['product_id'] == $product_id) {
                            if ( // product variation
                                $item['variation_id'] &&
                                isset($_REQUEST['iwptp_payload']['variations']) &&
                                !empty($_REQUEST['iwptp_payload']['variations'][$product_id]) &&
                                !isset($_REQUEST['iwptp_payload']['variations'][$product_id][$item['variation_id']])
                            ) {
                                continue;
                            }

                            if ($item['variation_id']) {
                                if (
                                    isset($_REQUEST['iwptp_payload']['variations']) &&
                                    isset($_REQUEST['iwptp_payload']['variations'][$product_id][$item['variation_id']])
                                ) {
                                    $qty = sanitize_text_field($_REQUEST['iwptp_payload']['variations'][$product_id][$item['variation_id']]);
                                    unset($_REQUEST['iwptp_payload']['variations'][$product_id][$item['variation_id']]);
                                } else {
                                    continue;
                                }
                            }

                            $cart->set_quantity($key, intval($qty));
                            $found = true;
                        }
                    }

                    // variations still left for 'Add'
                    if (
                        $adding_to_cart->get_type() === 'variable' &&
                        isset($_REQUEST['iwptp_payload']['variations']) &&
                        isset($_REQUEST['iwptp_payload']['variations'][$product_id])
                    ) {
                        // these variation removal requests could not be dealt with because they didn't exist in the cart to being with
                        foreach ($_REQUEST['iwptp_payload']['variations'][$product_id] as $variation_id => $variation_qty) {
                            if ($variation_qty == '0') {
                                unset($_REQUEST['iwptp_payload']['variations'][$product_id][$variation_id]);
                            }
                        }

                        if (count($_REQUEST['iwptp_payload']['variations'][$product_id])) {
                            $found = false;
                        }
                    }

                    if ($qty === '0' || $found) {
                        continue;
                    }
                }

                $clear_measurement = [];
                if (!empty($_REQUEST['iwptp_payload']['measurement']) && !empty($_REQUEST['iwptp_payload']['measurement'][$product_id])) {
                    foreach ($_REQUEST['iwptp_payload']['measurement'][$product_id] as $key => $val) {
                        $key = sanitize_text_field($key);
                        $val = (is_array($val)) ? iwptp_sanitize_array($val) : sanitize_text_field($val);
                        $_REQUEST[$key] = $val;
                        $_POST[$key] = $val;
                        $clear_measurement[] = $key;
                    }
                }

                // -- name your price
                $_REQUEST['nyp'] = $_POST['nyp'] = 0; // clear nyp
                if (!empty($_REQUEST['iwptp_payload']['nyp']) && !empty($_REQUEST['iwptp_payload']['nyp'][$product_id])) {
                    $_REQUEST['nyp'] = $_POST['nyp'] = (is_array($_REQUEST['iwptp_payload']['nyp'][$product_id])) ? iwptp_sanitize_array($_REQUEST['iwptp_payload']['nyp'][$product_id]) : sanitize_text_field($_REQUEST['iwptp_payload']['nyp'][$product_id]);
                }

                // -- addons - Woocommerce Custom Product Addons
                $clear_addons = [];
                if (!empty($_REQUEST['iwptp_payload']['addons']) && !empty($_REQUEST['iwptp_payload']['addons'][$product_id]) && function_exists('wcpa_is_wcpa_product')) {
                    foreach ($_REQUEST['iwptp_payload']['addons'][$product_id] as $key => $val) {
                        $key = sanitize_text_field($key);
                        $val = (is_array($val)) ? iwptp_sanitize_array($val) : sanitize_text_field($val);
                        $_REQUEST[$key] = $_POST[$key] = $val;
                        $clear_addons[] = $key;
                    }
                }

                // -- product data
                $_REQUEST['product_id'] = $product_id;
                $add_to_cart_handler = apply_filters('woocommerce_add_to_cart_handler', $adding_to_cart->get_type(), $adding_to_cart);

                // Variable product handling
                if ('variable' === $add_to_cart_handler) {
                    if (empty($_REQUEST['iwptp_payload']['variations']) || empty($_REQUEST['iwptp_payload']['variations'][$product_id])) {
                        wc_add_notice(esc_attr__('Please select some product options before adding this product to your cart.', 'woocommerce'), 'error');
                    } else {
                        foreach ($_REQUEST['iwptp_payload']['variations'][$product_id] as $variation_id => $variation_qty) {
                            $variation_id = intval($variation_id);
                            $variation_qty = intval($variation_qty);

                            $_REQUEST['variation_id'] = $variation_id;
                            $variation = wc_get_product(intval($_REQUEST['variation_id']));
                            $_REQUEST['quantity'] = $variation_qty ? $variation_qty : $variation->get_min_purchase_quantity();

                            foreach ($variation->get_attributes() as $key => $val) {
                                unset($_REQUEST['attribute_' . $key]);
                            }

                            if (!empty($_REQUEST['iwptp_payload']['attributes'][$variation_id])) {
                                $_REQUEST += $_REQUEST['iwptp_payload']['attributes'][$variation_id];
                                iwptp_woo_hack_invoke_private_method('WC_Form_Handler', 'add_to_cart_handler_variable', $product_id);
                            } else {
                                wc_add_notice(esc_attr__('Please select some product options before adding this product to your cart.', 'woocommerce'), 'error');
                            }

                            unset($_REQUEST['variation_id']);
                            unset($_REQUEST['quantity']);
                        }
                    }

                    continue;
                }

                if (!$qty) {
                    $qty = $adding_to_cart->get_min_purchase_quantity();
                }

                $_REQUEST['iwptp_payload']['quantity'] = $_REQUEST['quantity'] = $_POST['post'] = $qty;

                // Grouped Products
                if ('grouped' === $add_to_cart_handler) {
                    iwptp_woo_hack_invoke_private_method('WC_Form_Handler', 'add_to_cart_handler_grouped', $product_id);

                    // Custom Handler
                } elseif (has_action('woocommerce_add_to_cart_handler_' . $add_to_cart_handler)) {
                    $url = (!empty($url)) ? $url : '';
                    do_action('woocommerce_add_to_cart_handler_' . $add_to_cart_handler, $url);

                    // Simple Products
                } else {
                    iwptp_woo_hack_invoke_private_method('WC_Form_Handler', 'add_to_cart_handler_simple', $product_id);
                }

                // clear addons
                foreach ($clear_addons as $key) {
                    unset($_REQUEST[$key]);
                    unset($_POST[$key]);
                }

                // clear measurement
                foreach ($clear_measurement as $key) {
                    unset($_REQUEST[$key]);
                    unset($_POST[$key]);
                }
            }

            remove_filter('woocommerce_add_error', [$this, 'woocommerce_add_error'], 10);
        }

        public function woocommerce_add_error($message)
        {
            if (!empty($_REQUEST['iwptp_payload']) && !empty($_REQUEST['product_id'])) {
                $product = wc_get_product(intval($_REQUEST['product_id']));
                $title = $product->get_title();

                if ($product->get_type() == 'variable' && !empty($_REQUEST['variation_id'])) {
                    $title = get_the_title(intval($_REQUEST['variation_id']));
                }

                $title_mkp = '<span class="iwptp-error-product-name">' . esc_html($title) . '</span>';

                if (false === strpos($message, $title_mkp)) {
                    $message = $title_mkp . $message;
                }
            }

            return $message;
        }

        public function safari_dollar_fix($elm_markup, $elm)
        {
            if (empty($elm['type']) || $elm['type'] !== 'price' || empty($elm['use_default_template'])) {
                return $elm_markup;
            }

            $currency_symbol = esc_attr(get_woocommerce_currency_symbol());

            if (iwptp_get_the_browser() == "Safari" && $currency_symbol == '&#036;') {
                $currency_symbol = iwptp_icon('dollar-sign');
                $currency = '<span class="iwptp-currency iwptp-safari-currency">' . wp_kses($currency_symbol, iwptp_allowed_html_tags()) . '</span>';
                return str_replace('<span class="woocommerce-Price-currencySymbol">&#36;</span>', wp_kses($currency, iwptp_allowed_html_tags()), wp_kses($elm_markup, iwptp_allowed_html_tags()));
            }

            return $elm_markup;
        }

        public function include_new_child_categories($data, $context)
        {
            if (!empty($GLOBALS['sitepress'])) {
                return $data;
            }

            if (!empty($data) && !empty($data['query']['category'])) {
                $terms = iwptp_get_terms('product_cat', $data['query']['category']);
                if ($terms && !is_wp_error($terms)) {
                    $term_taxonomy_id = [];
                    foreach ($terms as $term) {
                        $term_taxonomy_id[] = sanitize_text_field($term->term_taxonomy_id);
                    }

                    $data['query']['category'] = $term_taxonomy_id;
                }
            }

            return $data;
        }
    }

    new IWPTPL_INIT();
}
