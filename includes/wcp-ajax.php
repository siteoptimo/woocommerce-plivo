<?php

/**
 * @author Koen Van den Wijngaert <koen@siteoptimo.com>
 */
class WCP_AJAX
{
    public function send_message() {

        //TODO: validation + nonce

        if($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.0 405 Method Not Allowed');
            die('<h1>Method Not Allowed!</h1>');
        }

        $smsService = WCP_SMS_Service::instance();

        $smsService->sendText($_POST['to'], $_POST['message']);

        die();
    }
}