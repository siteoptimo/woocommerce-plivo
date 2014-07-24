<?php
if(!defined('ABSPATH')) exit;

/**
 * Class WCP_Status_Hook
 *
 * @package WooCommerce_Plivo
 * @class WCP_Status_Hook
 * @author Koen Van den Wijngaert <koen@siteoptimo.com>
 */
class WCP_Status_Hook
{
    /**
     * The selected statuses to send updates from.
     * @var array
     */
    private static $statuses;

    /**
     * A copy of the current order ID.
     * @var int
     */
    private static $orderID;

    /**
     * Constructor. Initialize properties and create hook.
     */
    public function __construct()
    {
        self::$statuses = get_option('wcp_notification');

        $this->createHooks();
    }

    /**
     * Sends a text message based on the new order status.
     *
     * @param $orderID
     * @param $oldStatus
     * @param $newStatus
     *
     * @return bool
     */
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
                    // Load the SMS Service.
                    $smsService = WCP_SMS_Service::instance();

                    $message = apply_filters('wcp_order_status_changed_message', $message);

                    // Send the text message.
                    $sent = $smsService->sendText($phone, $message);

                    if($sent)
                    {
                        $note = sprintf(__('Sent "%s" to %s.', 'woocommerce-plivo'), $message, $phone);
                    } else
                    {
                        $note = sprintf(__('Could not send "%s" to %s.', 'woocommerce-plivo'), $message, $phone);
                    }

                    // Add an order note.
                    $order->add_order_note($note, false);

                    return true;

                } catch(Exception $e)
                {
                    $order->add_order_note(__('Could not text status update to customer. The SMS service returned an exception.'), false);
                }
            } else
            {
                $order->add_order_note(__('Could not text status update to customer. Either the phone number or the status message were invalid.'), false);
            }
        }

        return false;
    }

    /**
     * This will prepare the message string, filtering out the variables.
     *
     * @param $message
     * @return string
     */
    public function replaceMessageVariables($message)
    {
        return preg_replace_callback('~\{([^\}]+)\}~', array($this, 'replaceVariable'), $message);
    }

    /**
     * Callback function. Replaces the variables in the message.
     *
     * @param $var
     * @return string|integer
     */
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

    /**
     * Create the necessary hooks.
     */
    private function createHooks()
    {
        add_action('woocommerce_order_status_changed', array($this, 'orderStatusChanged'), 10, 3);

        add_filter('wcp_order_status_changed_message', array($this, 'replaceMessageVariables'), 1);
    }
}