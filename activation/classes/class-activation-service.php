<?php

class IWPTPL_Activation_Service
{
    private static $instance;
    private $service_url;

    public static function get_instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct()
    {
        $this->service_url = "https://license.ithemelandco.com/index.php";
    }

    public function activation($data)
    {
        $data['service'] = 'free_activation';
        $response = wp_remote_post($this->service_url, [
            'sslverify' => false,
            'method' => 'POST',
            'timeout' => 45,
            'httpversion' => '1.0',
            'body' => $data,
        ]);
        if (!is_object($response) && !empty($response['body'])) {
            if (!empty($response['response']['code']) && $response['response']['code'] != 500) {
                $data = iwptp_sanitize_array(json_decode($response['body'], true));
                return (!is_null($data)) ? $data : $response['body'];
            } else {
                return "System Error!";
            }
        }
        return null;
    }
}
