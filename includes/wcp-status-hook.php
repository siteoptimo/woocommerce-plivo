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

        // Bail if the user has opted out of SMS notifications.
        if(WCP_Opt_In_Out::WCP_Optout_Enabled() && WCP_Opt_In_Out::has_user_opted_out($orderID)) return false;

	    $newStatus = 'wc-' . $newStatus;

        if(in_array($newStatus, self::$statuses))
        {
            $phone = WCP_Tools::getPhoneNumberByOrder($orderID);

            $phone = apply_filters('wcp_phone_number', $phone, $orderID);

            $message = apply_filters('wcp_extra_order_status_changed_message', WCP_Tools::getTextMessageByOrderStatus($newStatus), $orderID, $newStatus);

            $order = new WC_Order($orderID);

            if($phone && $message)
            {
                try
                {
                    // Load the SMS Service.
                    $smsService = WCP_SMS_Service::instance();

                    // Apply filters (includes the variables)
                    $message = apply_filters('wcp_order_status_changed_message', $message, $orderID, $newStatus);

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
        $variables = WCP_Status_Hook::getVariables();
        $values = WCP_Status_Hook::getVariableValues( self::$orderID );
        $search = $replace = array();

        foreach($values as $var => $value) {
            if(array_key_exists($var, $variables)) {
                array_push($search, '{' . $var . '}');
                array_push($replace, $value);
            }
        }

        return str_replace($search, $replace, $message);
    }

    public static function getVariables() {
        return apply_filters( 'wcp_variables', array(
            'order_id'  => __( 'The ID of the order.', 'woocommerce-plivo' ),
            'home_url'  => __( 'The home URL.', 'woocommerce-plivo' ),
            'shop_name' => __( 'The name of the shop.', 'woocommerce-plivo' ),
        ) );
    }

    public static function getVariableValues( $order_id ) {
        return apply_filters( 'wcp_variable_values', array(
            'order_id'  => $order_id,
            'home_url'  => get_option( 'blogname' ),
            'shop_name' => home_url(),
        ), $order_id );
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
