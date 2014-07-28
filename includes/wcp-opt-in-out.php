<?php
if(!defined('ABSPATH')) exit;

/**
 * Class WCP_Opt_In_Out
 *
 * Handles opt in/out functionality.
 *
 * @package WooCommerce_Plivo
 * @class WCP_Opt_In_Out
 * @author Pieter Carette <pieter@siteoptimo.com>
 * @author Koen Van den Wijngaert <koen@siteoptimo.com>
 */
class WCP_Opt_In_Out
{

    /**
     * Constructs the class and adds the hooks.
     */
    public function __construct()
    {
        if(!self::WCP_Optout_Enabled())
        {
            // Nothing to do here.
            return;
        }

        add_filter('woocommerce_checkout_fields', array($this, 'wcp_checkout_fields'));
        add_action('woocommerce_checkout_update_order_meta', array($this, 'update_order_meta'));
        add_action('woocommerce_admin_order_data_after_billing_address', array($this, 'show_optout'), 10, 1);

    }

    /**
     * Override the checkout fields and add the optin/optout option.
     *
     * @param $fields
     * @return mixed
     */
    public function wcp_checkout_fields($fields)
    {
        $fields['order']['get_sms_notification'] = array(
            'label' => get_option('wcp_optout_message', __('I want to receive SMS notifications.', 'woocommerce-plivo')),
            'type' => 'checkbox',
            'default' => get_option('wcp_optout_default', 'yes'),
            'required' => false,
            'clear' => true
        );

        return $fields;
    }

    /**
     * Save the optin/optout option in a meta field.
     *
     * @param $order_id
     */
    public function update_order_meta($order_id)
    {
        $value = isset($_POST['get_sms_notification']) && $_POST['get_sms_notification'] == 1 ? 'yes' : 'no';
        update_post_meta($order_id, '_receive_sms_notifications', $value);
    }

    /**
     * Displays opt out meta on order overview (admin).
     *
     * @param $order
     */
    public function show_optout($order)
    {
        $sms_notifications = !self::has_user_opted_out($order->id) ? 'Yes' : 'No';
        echo '<p><strong> ' . __('Wants SMS notifications', 'woocommerce-plivo') . ':</strong><br /> ' . $sms_notifications . '</p>';
    }



    /**
     * Checks if the opt-in/opt-out feature is enabled.
     *
     * @return bool
     */
    public static function WCP_Optout_Enabled()
    {
        $option = get_option('wcp_optout_enabled', 'yes');

        return $option == 'yes';
    }

    /**
     * Checks if the order is eligible for SMS notifications
     *
     * @param $orderID
     * @return bool
     */
    public static function has_user_opted_out($orderID)
    {
        $option = get_post_meta($orderID, '_receive_sms_notifications', true);

        return ($option && $option == 'no');
    }
}