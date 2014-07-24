<?php

/**
 * @author Koen Van den Wijngaert <koen@siteoptimo.com>
 */
if(!defined('ABSPATH')) exit;

class WCP_Status_Hooks
{
    private static $statuses;

    private static $orderID;

    public function __construct()
    {
        self::$statuses = get_option('wcp_notification');

        $this->createHooks();
    }

    public function orderStatusChanged($orderID, $oldStatus, $newStatus)
    {
        self::$orderID = $orderID;

        if(in_array($newStatus, self::$statuses))
        {
            $phone = WCP_Tools::getPhoneNumberByOrder($orderID);

            $message = WCP_Tools::getTextMessageByOrderStatus($newStatus);

            $order = new WC_Order($orderID);

            if($phone && $message)
            {
                try
                {
                    $smsService = WCP_SMS_Service::instance();

                    $message = apply_filters('wcp_order_status_changed_message', $message);

                    $sent = $smsService->sendText($phone, $message);

                    if($sent)
                    {
                        $note = sprintf(__('Sent "%s" to %s.', 'woocommerce-plivo'), $message, $phone);
                    } else
                    {
                        $note = sprintf(__('Could not send "%s" to %s.', 'woocommerce-plivo'), $message, $phone);
                    }

                    $order->add_order_note($note, false);

                } catch(Exception $e)
                {
                    $order->add_order_note(__('Could not text status update to customer. The SMS service returned an exception.'), false);
                }
            } else
            {
                $order->add_order_note(__('Could not text status update to customer. Either the phone number or the status message were invalid.'), false);
            }
        }
    }

    public function replaceMessageVariables($message)
    {
        return preg_replace_callback('~\{([^\}]+)\}~', array($this, 'replaceVariable'), $message);
    }

    public function replaceVariable($var)
    {
        $variable = $var[0];
        $search = array('{shop_name}', '{home_url}');
        $replace = array(get_option('blogname'), home_url());

        switch($variable)
        {
            case '{order_id}':
                return self::$orderID;
                break;
            default:
                return str_replace($search, $replace, $variable);
        }

    }

    private function createHooks()
    {
        add_action('woocommerce_order_status_changed', array($this, 'orderStatusChanged'), 10, 3);

        add_filter('wcp_order_status_changed_message', array($this, 'replaceMessageVariables'), 1);
    }
}