<?php

class Post_Handler
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
        add_action('admin_post_iwptp_activation_plugin', [$this, 'activation_plugin']);
    }

    public function activation_plugin()
    {
        if (!isset($_REQUEST['_wpnonce']) || !wp_verify_nonce($_REQUEST['_wpnonce'], 'iwptp_post_nonce')) {
            die('403 Forbidden');
        }

        $message = "Error! Try again";

        if (isset($_POST['activation_type'])) {
            if ($_POST['activation_type'] == 'skip') {
                update_option('iwptpl_is_active', 'skipped');
                return $this->redirect(IWPTPL_MAIN_PAGE);
            } else {
                if (!empty($_POST['email']) && !empty($_POST['industry'])) {
                    $activation_service = IWPTPL_Activation_Service::get_instance();
                    $info = $activation_service->activation([
                        'email' => sanitize_email($_POST['email']),
                        'domain' => sanitize_url($_SERVER['SERVER_NAME']),
                        'product_id' => 'iwptpl',
                        'product_name' => IWPTPL_LABEL,
                        'industry' => sanitize_text_field($_POST['industry']),
                        'multi_site' => is_multisite(),
                        'core_version' => null,
                        'subsystem_version' => IWPTPL_VERSION,
                    ]);

                    if (!empty($info) && is_array($info)) {
                        if (!empty($info['result']) && $info['result'] == true) {
                            update_option('iwptpl_is_active', 'yes');
                            $message = esc_html__('Success !', 'ithemeland-woocommerce-product-table-pro-lite');
                        } else {
                            update_option('iwptpl_is_active', 'no');
                            $message = (!empty($info['message'])) ? esc_html($info['message']) : esc_html__('System Error !', 'ithemeland-woocommerce-product-table-pro-lite');
                        }
                    } else {
                        update_option('iwptpl_is_active', 'no');
                        $message = esc_html__('Connection Timeout! Please Try Again', 'ithemeland-woocommerce-product-table-pro-lite');
                    }
                }
            }
        }

        $this->redirect(IWPTPL_ACTIVATION_PAGE, $message);
    }

    private function redirect($url = null, $message = [])
    {
        $url = (!empty($url)) ? $url : IWPTPL_ACTIVATION_PAGE;

        if (!is_null($message)) {
            $flush_message = IWPTPL_Flush_Message::get_instance();
            $flush_message->set($message);
        }

        wp_redirect($url);
        die();
    }
}
