<?php

/**
 * @author Koen Van den Wijngaert <koen@siteoptimo.com>
 */
class WCP_AJAX
{
    public function send_message() {

        if($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.0 405 Method Not Allowed');
            die('<h1>Method Not Allowed!</h1>');
        }

        $smsService = WCP_SMS_Service::instance();

        $number = WCP_Tools::cleanPhoneNumber($_POST['to']);

        if($number && !empty($number))
        {
            return $smsService->sendText($number, $_POST['message']);
        }

        return false;
    }
}