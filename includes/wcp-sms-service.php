<?php

if(!defined('ABSPATH')) exit;

/**
 * Class WCP_SMS_Service
 *
 * Handles the sending of text messages.
 *
 * @package WooCommerce_Plivo
 * @class WCP_SMS_Service
 * @author Koen Van den Wijngaert <koen@siteoptimo.com>
 */
class WCP_SMS_Service
{
    /**
     * Single instance.
     * @var WCP_SMS_Service
     */
    private static $_instance = null;

    /**
     * @var string Plivo authentication ID.
     */
    private $auth_id;

    /**
     * @var string Plivo authentication token.
     */
    private $auth_token;

    /**
     * @var string "from" phone number.
     */
    private static $from = '123456789';

    /**
     * @var RestAPI The plivo API.
     */
    private $plivo;

    /**
     * Bootstraps the service.
     */
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

        self::$from = get_option('wcp_from_number', '123456789');

    }

    /**
     * Returns the WCP_SMS_Service instance.
     *
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

    /**
     * Populates the authentication data.
     */
    private function populateAuthenticationInformation()
    {
        $this->auth_id = get_option('wcp_auth_id');
        $this->auth_token = get_option('wcp_auth_password');
    }

    /**
     * Sends a text message.
     *
     * @param $to string The "to" number.
     * @param $message string The message.
     * @return bool Returns true on success, false on failure.
     */
    public function sendText($to, $message)
    {

        $params = array('src' => self::$from, 'dst' => $to, 'text' => $message, 'type' => 'sms');

        $response = $this->plivo->send_message($params);

        return in_array($response["status"], array('200', '201', '202', '203', '204'));
    }
}