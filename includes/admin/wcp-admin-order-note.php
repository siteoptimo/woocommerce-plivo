<?php
if(!defined('ABSPATH')) exit;

/**
 * Class WCP_Admin_Add_Settings_Link
 *
 * Adds the settings link to the plugin's box.
 *
 * @package WooCommerce_Plivo
 * @class WCP_Admin_Add_Settings_Link
 * @author Koen Van den Wijngaert <koen@siteoptimo.com>
 */
class WCP_Admin_Order_Note
{
    /**
     * Construct the class.
     */
    public function __construct()
    {
        $this->enqueue();
    }

    /**
     * Enqueue the javascript file.
     */
    private function enqueue()
    {
        wp_register_script('wcp-admin-metabox', WooCommerce_Plivo::instance()->plugin_url() . 'assets/js/admin-metabox.js', array('jquery'), WooCommerce_Plivo::$version, true);

        wp_localize_script('wcp-admin-metabox', 'WCP_Metabox', array('send_as_sms' => __('Send as SMS?'), 'woocommerce-plivo'));

        wp_enqueue_script('wcp-admin-metabox');
    }

    /**
     * This function hooks into the "woocommerce_new_order_note_data" hook.
     * It will then decide, based on the contents, whether or not to send
     * a text message to the client.
     *
     * @param $note_data array
     * @return array
     */
    public static function order_note_data($note_data)
    {
        // Only continue if we're handling an SMS comment for WooCommerce.
        if(strpos($note_data['comment_content'], '[SMS]') !== 0 || $note_data['comment_agent'] !== "WooCommerce")
        {
            return $note_data;
        }

        $message = trim(substr($note_data['comment_content'], 5));

        // Get Phone number
        $phone = WCP_Tools::getPhoneNumberByOrder($note_data['comment_post_ID']);

        if($phone && !empty($phone))
        {
            // All set, let's roll.
            try
            {
                $smsService = WCP_SMS_Service::instance();
                $smsService->sendText($phone, $message);

                $note_data['comment_content'] = sprintf(__('Sent "%s" to %s.', 'woocommerce-plivo'), $message, $phone);
            } catch(Exception $e)
            {
                $note_data['comment_content'] = sprintf(__('Could not send text "%s", the SMS service returned an exception.', 'woocommerce-plivo'), $message);
            }

        } else
        {
            $note_data['comment_content'] = sprintf(__('Could not send text "%s", the phone number was missing or invalid.', 'woocommerce-plivo'), $message);
        }

        return $note_data;
    }

}