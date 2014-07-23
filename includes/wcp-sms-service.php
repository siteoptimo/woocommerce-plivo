<?php

/**
 * @author Koen Van den Wijngaert <koen@siteoptimo.com>
 */

class WCP_SMS_Service
{
    private static $_instance = null;

    private $auth_id;

    private $auth_token;

    private static $from = '17192992039';

    /**
     * @var RestAPI
     */
    private $plivo;

    public function __construct()
    {
        $this->populateAuthenticationInformation();

        if(empty($this->auth_token))
        {
            throw new Exception('No plivo credentials detected.');
        }

        $this->plivo = new RestAPI($this->auth_id, $this->auth_token);

    }

    /**
     * @return WCP_SMS_Service
     */
    public static function instance()
    {
        if(self::$_instance == null)
        {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    private function populateAuthenticationInformation()
    {
        $this->auth_id = get_option('wcp_auth_id');
        $this->auth_token = get_option('wcp_auth_password');

        var_dump($this->auth_token, $this->auth_id);
    }

    public function sendText($to, $message) {

        // TODO phone number validation
        $params = array(
            'src' => self::$from,
            'dst' => $to,
            'text' => $message,
            'type' => 'sms'
        );

        var_dump($params);

        $response = $this->plivo->send_message($params);

        var_dump($response);
    }
}