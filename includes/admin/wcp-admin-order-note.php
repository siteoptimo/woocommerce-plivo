<?php
/**
 * @author Koen Van den Wijngaert <koen@siteoptimo.com>
 */
if(!defined('ABSPATH')) exit;

class WCP_Admin_Order_Note
{
    public function __construct()
    {
        $this->enqueue();
    }

    private function enqueue()
    {
        wp_enqueue_script(
            'wcp-admin-metabox',
            WooCommerce_Plivo::instance()->plugin_url() . 'assets/js/admin-metabox.js',
            array('jquery'),
            "0.1",
            true
        );
    }

    public static function order_note_data($note_data)
    {
        if(strpos($note_data['comment_content'], '[SMS]') !== 0 || $note_data['comment_agent'] !== "WooCommerce")
        {
            return $note_data;
        }

        $message = trim(substr($note_data['comment_content'], 5));

        $phone = WCP_Tools::getPhoneNumberByOrder($note_data['comment_post_ID']);

        if($phone && !empty($phone))
        {
            $smsService = WCP_SMS_Service::instance();

            $smsService->sendText($phone, $message);

            $note_data['comment_content'] = 'Sent "' . $message . '" to ' . $phone . '.';
        } else
        {
            $note_data['comment_content'] = 'Could not send text "' . $message . '", the phone number was missing or invalid.';
        }

        return $note_data;
    }

}