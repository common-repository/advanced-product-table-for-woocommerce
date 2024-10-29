<?php

class Ajax_Handler
{
    private static $instance;

    public static function register_callback()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct()
    {
        add_action('wp_ajax_iwptp_get_user_roles', [$this, 'get_user_roles']);
        add_action('wp_ajax_iwptp_get_products_variations', [$this, 'get_products_variations']);
        add_action('wp_ajax_iwptp_get_taxonomies', [$this, 'get_taxonomies']);
        add_action('wp_ajax_iwptp_get_users', [$this, 'get_users']);
        add_action('wp_ajax_iwptp_save_table_settings', [$this, 'save_table_settings']);
        add_action('wp_ajax_iwptp_save_global_settings', [$this, 'save_global_settings']);
        add_action('wc_ajax_iwptp_ajax', [$this, 'iwptp_ajax']);
        add_action('wp_ajax_iwptp_ajax', [$this, 'iwptp_ajax']);
        add_action('wp_ajax_iwptp_delete_cart_item', [$this, 'delete_cart_item']);
        add_action('wp_ajax_nopriv_iwptp_delete_cart_item', [$this, 'delete_cart_item']);
        add_action('wp_ajax_iwptp_cart_clear', [$this, 'cart_clear']);
        add_action('wp_ajax_nopriv_iwptp_cart_clear', [$this, 'cart_clear']);
        add_action('wp_ajax_nopriv_iwptp_ajax', [$this, 'iwptp_ajax']);
        add_action('wc_ajax_iwptp_add_to_cart', [$this, 'add_to_cart']);
        add_action('wp_ajax_iwptp_add_to_cart', [$this, 'add_to_cart']);
        add_action('wp_ajax_nopriv_iwptp_add_to_cart', [$this, 'add_to_cart']);
        add_action('wp_ajax_iwptp_cart_widget', [$this, 'cart_widget']);
        add_action('wp_ajax_nopriv_iwptp_cart_widget', [$this, 'cart_widget']);
        add_action('wp_ajax_nopriv_iwptp_get_cart', [$this, 'get_cart']);
        add_action('wp_ajax_iwptp_get_cart', [$this, 'get_cart']);
        add_action('wc_ajax_iwptp_get_product_form_modal', [$this, 'get_product_form_modal']);
        add_action('wp_ajax_nopriv_iwptp_get_product_form_modal', [$this, 'get_product_form_modal']);
        add_action('wp_ajax_iwptp_get_product_form_modal', [$this, 'get_product_form_modal']);
        add_action('wp_ajax_iwptp_editor_update_theme', [$this, 'editor_update_theme']);
        add_action('wp_ajax_iwptp_create_style_preset', [$this, 'create_style_preset']);
        add_action('wp_ajax_iwptp_delete_style_preset', [$this, 'delete_style_preset']);
        add_action('wp_ajax_iwptp_get_preview', [$this, 'get_preview']);
    }

    public function delete_cart_item()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'iwptp_ajax_nonce')) {
            die();
        }

        if (empty($_POST['item_key']) || empty(WC()->cart)) {
            die('Error');
        }

        $result = WC()->cart->remove_cart_item(sanitize_text_field($_POST['item_key']));
        $iwptp_settings = iwptp_get_settings_data();
        $settings = $iwptp_settings['cart_widget'];

        die(wp_json_encode([
            'success' => $result,
            'cart' => WC()->cart->get_cart(),
            'subtotal' => apply_filters('iwptp_cart_total_price', !empty($settings['cost_source']) && $settings['cost_source'] == 'subtotal' ? WC()->cart->get_cart_subtotal() : WC()->cart->get_total()),
            'total_quantity' => apply_filters('iwptp_cart_total_quantity', WC()->cart->cart_contents_count),
        ]));
    }

    public function cart_clear()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'iwptp_ajax_nonce')) {
            die();
        }

        WC()->cart->empty_cart(true);
        die(wp_json_encode([
            'success' => true,
            'cart' => WC()->cart->get_cart(),
            'subtotal' => WC()->cart->get_cart_subtotal(),
        ]));
    }

    public function get_preview()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'iwptp_ajax_nonce')) {
            die();
        }

        if (empty($_POST['table_id']) || empty($_POST['table_data'])) {
            die(wp_json_encode(['success' => false]));
        }

        $table_data = iwptp_sanitize_array(json_decode(wp_unslash($_POST['table_data']), true));
        $table_data['device'] = (!empty($_POST['device'])) ? sanitize_text_field($_POST['device']) : 'laptop';
        $preview = iwptp_shortcode_product_table(['id' => intval($_POST['table_id'])], $table_data);
        die(wp_json_encode(['success' => true, 'preview' => $preview]));
    }

    public function create_style_preset()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'iwptp_ajax_nonce')) {
            die();
        }

        if (empty($_POST['preset_name'])) {
            die('Error! Name is required');
        }

        if (empty($_POST['preset_image_url'])) {
            die('Error! Image is required');
        }

        if (empty($_POST['preset_data'])) {
            die('Error! Data is required');
        }

        $result = iwptp_create_style_preset([
            'slug' => 'user-' . sanitize_text_field(strtolower(str_replace(' ', '-', $_POST['preset_name']))),
            'name' => sanitize_text_field($_POST['preset_name']),
            'data' => iwptp_sanitize_array($_POST['preset_data']),
            'deletable' => true,
            'image_url' => sanitize_url($_POST['preset_image_url']),
        ]);

        $presets = iwptp_get_style_presets();
        ob_start();
        include IWPTPL_PLUGIN_PATH . "editor/partials/style-presets.php";
        $presets_rendered = ob_get_clean();

        die(wp_json_encode(['success' => ($result), 'presets' => $presets_rendered]));
    }

    public function delete_style_preset()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'iwptp_ajax_nonce')) {
            die();
        }

        if (empty($_POST['preset_slug'])) {
            die('Error!');
        }

        $result = iwptp_delete_style_preset(sanitize_text_field($_POST['preset_slug']));
        die(wp_json_encode(['success' => ($result)]));
    }

    public function editor_update_theme()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'iwptp_ajax_nonce')) {
            die();
        }

        if (!empty($_POST['theme_name'])) {
            update_option('iwptp_editor_theme', sanitize_text_field($_POST['theme_name']));
        }

        die(wp_json_encode(['success' => true]));
    }

    public function get_user_roles()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'iwptp_ajax_nonce')) {
            die();
        }

        $list = [];
        $roles = wp_roles();

        if (!empty($roles)) {
            foreach ($roles->roles as $roleKey => $role) {
                if (isset($role['name']) && strpos($roleKey, strtolower(sanitize_text_field($_POST['search']))) !== false) {
                    $list['results'][] = [
                        'id' => $roleKey,
                        'text' => $role['name'],
                    ];
                }
            }
        }

        die(wp_json_encode($list));
    }

    public function get_products_variations()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'iwptp_ajax_nonce')) {
            die();
        }

        $list = [];
        $product_types = array_keys(wc_get_product_types());
        $product_types[] = 'variation';
        if (!empty($_POST['search'])) {
            $products = wc_get_products([
                'type' => $product_types,
                'like_name' => sanitize_text_field($_POST['search']),
                'limit' => -1,
            ]);
            if (!empty($products)) {
                foreach ($products as $product) {
                    if ($product instanceof \WC_Product) {
                        $list['results'][] = [
                            'id' => $product->get_id(),
                            'text' => $product->get_name(),
                        ];
                    }
                }
            }
        }

        die(wp_json_encode($list));
    }

    public function get_taxonomies()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'iwptp_ajax_nonce')) {
            die();
        }

        $list = [];
        $terms = get_terms([
            'hide_empty' => false,
            'name__like' => strtolower(sanitize_text_field($_POST['search'])),
        ]);

        if (!empty($terms)) {
            foreach ($terms as $term) {
                if ($term instanceof \WP_Term) {
                    $list['results'][] = [
                        'id' => $term->taxonomy . '{iwptp-tax-id}' . ((strpos($term->taxonomy, 'pa_') === 0) ? $term->slug : $term->term_id),
                        'text' => $term->taxonomy . ': ' . $term->name,
                    ];
                }
            }
        }

        die(wp_json_encode($list));
    }

    public function get_users()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'iwptp_ajax_nonce')) {
            die();
        }

        $list = [];
        $query = new \WP_User_Query([
            'search' => '*' . sanitize_text_field($_POST['search']) . '*',
            'search_columns' => array('user_nicename', 'display_name'),
        ]);

        $users = $query->results;
        if (!empty($users)) {
            foreach ($users as $user) {
                if ($user instanceof \WP_User) {
                    $list['results'][] = [
                        'id' => $user->ID,
                        'text' => $user->display_name,
                    ];
                }
            }
        }

        die(wp_json_encode($list));
    }

    public function save_table_settings()
    {
        // check for errors first
        $errors = [];

        // error: no table settings
        if (empty($_POST['iwptp_data'])) {
            $errors[] = 'Table settings were not received.';
        }

        // error: no post ID
        if (empty($_POST['iwptp_post_id'])) {
            $errors[] = 'Post ID was not received.';

            // error: unathorized user
        } else if (!current_user_can('edit_iwptp_product_table', intval($_POST['iwptp_post_id']))) {
            $user = wp_get_current_user();
            $errors[] = 'User (' . implode(", ", $user->roles) . ') is not authorized to edit product tables.';
        }

        // error: no nonce
        if (empty($_POST['iwptp_nonce'])) {
            $errors[] = 'Nonce string was not received.';

            // error: wrong nonce
        } else if (!wp_verify_nonce($_POST['iwptp_nonce'], 'iwptp')) {
            $errors[] = 'Nonce verification failed.';
        }

        if (count($errors)) { // failure
            $error_message = 'IWPTPL error: Table data was not saved because:';
            foreach ($errors as $i => $error) {
                $error_message .= ' (' . ($i + 1) . ') ' . $error;
            }

            $remedy = ' Please contact plugin author at https://ithemelandco.com/support/ for prompt assistance with this issue!';

            echo esc_html($error_message) . esc_html($remedy);
        } else { // success
            $post_id = intval($_POST['iwptp_post_id']);
            $data = iwptp_sanitize_array(json_decode(stripslashes($_POST['iwptp_data']), true));
            $data['timestamp'] = time();
            update_post_meta($post_id, 'iwptp_data', addslashes(wp_json_encode($data)));
            if (!empty($_POST['fonts'])) {
                update_post_meta($post_id, 'iwptp_fonts', wp_json_encode(array_map('sanitize_text_field', $_POST['fonts'])));
            }
            $my_post = array(
                'ID' => $post_id,
                'post_title' => sanitize_text_field($_POST['iwptp_title']),
                'post_status' => 'publish',
            );
            wp_update_post($my_post);

            echo "IWPTPL success: Table data was saved.";
        }

        wp_die();
    }

    public function save_global_settings()
    {
        if (!empty($_POST['iwptp_data']) && wp_verify_nonce($_POST['iwptp_nonce'], 'iwptp')) {
            $settings = iwptp_sanitize_array(json_decode(stripslashes($_POST['iwptp_data']), true));
            $settings['timestamp'] = time();
            $settings = addslashes(wp_json_encode($settings));

            update_option('iwptp_settings', apply_filters('iwptp_global_settings', $settings));
            echo "IWPTPL success: Global settings saved.";
        }
        wp_die();
    }

    public function iwptp_ajax()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'iwptp_ajax_nonce')) {
            die();
        }

        if (!empty($_REQUEST['id'])) {
            $sc_attrs = '';
            if (!empty($_REQUEST[$_REQUEST['id'] . '_sc_attrs'])) {
                $_sc_attrs = iwptp_sanitize_array(json_decode(stripslashes($_REQUEST[$_REQUEST['id'] . '_sc_attrs'])));

                if (!empty($_sc_attrs)) {
                    foreach ($_sc_attrs as $key => $val) {
                        if (in_array($key, $GLOBALS['iwptp_permitted_shortcode_attributes'])) {
                            $sc_attrs .= ' ' . $key . ' ="' . (is_array($val) ? implode('|', $val) : $val) . '" ';
                        }
                    }
                }
            }

            echo do_shortcode('[it_product_table id="' . intval($_REQUEST['id']) . '" ' . wp_kses($sc_attrs, iwptp_allowed_html_tags()) . ' ]');
        }

        die();
    }

    public function add_to_cart()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'iwptp_ajax_nonce')) {
            die();
        }

        if (!empty($_POST['return_notice']) && $_POST['return_notice'] == "false") {
            wp_die();
        }

        // success
        if (wc_notice_count('success')) {
            ob_start();
            woocommerce_mini_cart();
            $mini_cart = ob_get_clean();

            $cart_items = WC()->cart->get_cart();
            $in_cart = [];

            foreach ($cart_items as $item => $values) {
                if (!empty($values['variation_id'])) {
                    if (empty($in_cart[$values['product_id']])) {
                        $in_cart[$values['product_id']] = [];
                    }
                    $in_cart[$values['product_id']][$values['variation_id']] = $values['quantity'];
                } else {
                    $in_cart[$values['product_id']] = $values['quantity'];
                }
            }

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
                'in_cart' => $in_cart,
            );

            // error
        } else {
            $data = array(
                'error' => true,
            );
        }

        // get notice markup
        $data['notice'] = "";
        if (wc_notice_count()) {
            ob_start();
            wc_print_notices();
            $data['notice'] = ob_get_clean();
        }

        wp_send_json($data);
    }

    public function cart_widget()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'iwptp_ajax_nonce')) {
            die();
        }
        ob_start();
        include_once(IWPTPL_PLUGIN_PATH . 'templates/mini_cart.php');
        $mini_cart = ob_get_clean();
        wp_die(wp_kses($mini_cart, iwptp_allowed_html_tags()));
    }

    public function get_cart()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'iwptp_ajax_nonce')) {
            die();
        }

        wp_send_json(WC()->cart->get_cart());
    }

    public function get_product_form_modal()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'iwptp_ajax_nonce')) {
            die();
        }

        $product_id = intval($_REQUEST['product_id']);
        if (get_post_status($product_id) == 'publish') {
            ob_start();
            echo wp_kses($this->get_product_form(array('id' => $product_id)), iwptp_allowed_html_tags());
            echo wp_kses(ob_get_clean(), iwptp_allowed_html_tags());
        }
        wp_die();
    }

    private function get_product_form($atts)
    {
        global $post;

        // store global post
        if (!empty($post)) {
            $_post = $post;
        }

        $product_id = $atts['id'];

        $product = apply_filters('iwptp_product', wc_get_product($product_id));

        $GLOBALS['product'] = $product;

        $product_type = $product->get_type();

        ob_start();
        include IWPTPL_PLUGIN_PATH . 'templates/modal_form.php';

        // restore global post
        if (!empty($_post)) {
            $post = $_post;
        }

        return ob_get_clean();
    }
}
