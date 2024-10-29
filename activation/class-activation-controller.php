<?php

require_once IWPTPL_PLUGIN_PATH . 'activation/classes/class-activation-service.php';
require_once IWPTPL_PLUGIN_PATH . 'activation/classes/class-iwptpl-verification.php';

class IWPTPL_Activation_Controller
{
    public function __construct()
    {
        if (isset($_GET['page']) && $_GET['page'] == 'iwptp-activation') {
            wp_enqueue_style('iwptp-reset-css', IWPTPL_PLUGIN_URL . 'editor/assets/css/reset.css', null, IWPTPL_VERSION);
            wp_enqueue_style('iwptp-line-icons', IWPTPL_PLUGIN_URL . 'editor/assets/css/LineIcons.min.css', null, IWPTPL_VERSION);
            wp_enqueue_style('iwptp-common', IWPTPL_PLUGIN_URL . 'editor/assets/css/common.css', null, IWPTPL_VERSION);
            wp_enqueue_style('iwptp-activation', IWPTPL_PLUGIN_URL . 'assets/iwptp-activation.css', null, IWPTPL_VERSION);
            wp_enqueue_style('iwptp-sweetalert', IWPTPL_PLUGIN_URL . 'assets/sweetalert.css', null, IWPTPL_VERSION);

            wp_enqueue_script('iwptp-sweetalert', IWPTPL_PLUGIN_URL . 'assets/sweetalert.min.js', null, IWPTPL_VERSION);
            wp_enqueue_script('iwptp-activation', IWPTPL_PLUGIN_URL . 'assets/iwptp-activation.js', null, IWPTPL_VERSION);
            wp_localize_script('iwptp-activation', 'IWPTPL_DATA', [
                'ajax_url' => admin_url('admin-ajax.php'),
                'ajax_nonce' => wp_create_nonce('iwptp_ajax_nonce')
            ]);
        }
    }

    public function index()
    {
        $is_active = IWPTPL_Verification::is_active();
        $activation_skipped = IWPTPL_Verification::skipped();
        $industries = $this->get_industries();
        $flush_message_instance = IWPTPL_Flush_Message::get_instance();
        $flush_message = $flush_message_instance->get();

        require_once "views/activation.php";
    }

    private function get_industries()
    {
        return [
            'Automotive and Transportation' => __('Automotive', 'ithemeland-woocommerce-product-table-pro-lite'),
            'AdTech and AdNetwork' => __('AdTech and AdNetwork', 'ithemeland-woocommerce-product-table-pro-lite'),
            'Agency' => __('Agency', 'ithemeland-woocommerce-product-table-pro-lite'),
            'B2B Software' => __('B2B Software', 'ithemeland-woocommerce-product-table-pro-lite'),
            'B2C Internet Services' => __('B2C Internet Services', 'ithemeland-woocommerce-product-table-pro-lite'),
            'Classifieds' => __('Classifieds', 'ithemeland-woocommerce-product-table-pro-lite'),
            'Consulting and Market Research' => __('Consulting and Market Research', 'ithemeland-woocommerce-product-table-pro-lite'),
            'CPG, Food and Beverages' => __('CPG', 'ithemeland-woocommerce-product-table-pro-lite'),
            'Education' => __('Education', 'ithemeland-woocommerce-product-table-pro-lite'),
            'Education (student)' => __('Education (Student)', 'ithemeland-woocommerce-product-table-pro-lite'),
            'Equity Research' => __('Equity Research', 'ithemeland-woocommerce-product-table-pro-lite'),
            'Financial services' => __('Financial Services', 'ithemeland-woocommerce-product-table-pro-lite'),
            'Gambling / Gaming' => __('Gambling and Gaming', 'ithemeland-woocommerce-product-table-pro-lite'),
            'Hedge Funds and Asset Management' => __('Hedge Funds and Asset Management', 'ithemeland-woocommerce-product-table-pro-lite'),
            'Investment Banking' => __('Investment Banking', 'ithemeland-woocommerce-product-table-pro-lite'),
            'Logistics and Shipping' => __('Logistics and Shipping', 'ithemeland-woocommerce-product-table-pro-lite'),
            'Payments' => __('Payments', 'ithemeland-woocommerce-product-table-pro-lite'),
            'Pharma and Healthcare' => __('Pharma and Healthcare', 'ithemeland-woocommerce-product-table-pro-lite'),
            'Private Equity and Venture Capital' => __('Private Equity and Venture Capital', 'ithemeland-woocommerce-product-table-pro-lite'),
            'Media and Entertainment' => __('Publishers and Media', 'ithemeland-woocommerce-product-table-pro-lite'),
            'Government Public Sector & Non Profit' => __('Public Sector, Non Profit, Fraud and Compliance', 'ithemeland-woocommerce-product-table-pro-lite'),
            'Retail / eCommerce' => __('Retail and eCommerce', 'ithemeland-woocommerce-product-table-pro-lite'),
            'Telecom and Hardware' => __('Telecom', 'ithemeland-woocommerce-product-table-pro-lite'),
            'Travel and Hospitality' => __('Travel', 'ithemeland-woocommerce-product-table-pro-lite'),
            'Other' => __('Other', 'ithemeland-woocommerce-product-table-pro-lite'),
        ];
    }
}
