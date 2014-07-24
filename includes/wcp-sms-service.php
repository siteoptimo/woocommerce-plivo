<?php

/**
 * @author Koen Van den Wijngaert <koen@siteoptimo.com>
 */
if(!defined('ABSPATH')) exit;

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

        if(empty($this->auth_token) || empty($this->auth_id))
        {
            throw new Exception('Can\'t start SMS Service. No plivo credentials detected.');
        }

        try
        {
            $this->plivo = new RestAPI($this->auth_id, $this->auth_token);
        } catch(Exception $e)
        {
            throw new Exception('Can\'t start SMS Service. The plivo credentials were faulty.');
        }

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
    }

    public function sendText($to, $message)
    {

        $params = array('src' => self::$from, 'dst' => $to, 'text' => $message, 'type' => 'sms');

        $response = $this->plivo->send_message($params);

        return in_array($response["status"], array('200', '201', '202', '203', '204'));
    }
}