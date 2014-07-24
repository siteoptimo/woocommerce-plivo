<?php
if(!defined('ABSPATH')) exit;
/**
 * Class WCP_AJAX
 *
 * Handles the AJAX calls from the admin.
 *
 * @package WooCommerce_Plivo
 * @class WCP_AJAX
 * @author Koen Van den Wijngaert <koen@siteoptimo.com>
 */
class WCP_AJAX
{
    /**
     * Handles the sending of a test message.
     *
     * @return bool|int
     */
    public function send_message()
    {

        if($_SERVER['REQUEST_METHOD'] !== 'POST')
        {
            header('HTTP/1.0 405 Method Not Allowed');
            die('<h1>Method Not Allowed!</h1>');
        }

        try
        {
            $smsService = WCP_SMS_Service::instance();

            $number = WCP_Tools::cleanPhoneNumber($_POST['to']);

            if($number && !empty($number))
            {
                echo (int) $smsService->sendText($number, $_POST['message']);
                exit();
            }

            exit(0);

        } catch(Exception $e)
        {
            exit(0);
        }
    }
}