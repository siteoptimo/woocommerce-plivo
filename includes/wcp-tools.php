<?php
if(!defined('ABSPATH')) exit;

/**
 * The Utilities class
 *
 * This class contains all of the utilities that support the WooCommerce_Plivo plugin.
 *
 * @package WooCommerce_Plivo
 * @class WCP_Tools
 * @author Koen Van den Wijngaert <koen@siteoptimo.com>
 */
class WCP_Tools
{
    /**
     * @param $phoneNumber
     * @return bool|string Returns false on error, the cleaned phone number on success.
     */
    public static function cleanPhoneNumber($phoneNumber)
    {
        if(!self::isValidPhoneNumber($phoneNumber)) return false;

        $search = array('(0)');

        $replace = array('');

        // Replace unneeded characters.
        $new_number = str_replace($search, $replace, $phoneNumber);

        // Trim prefix zeros.
        $new_number = ltrim($new_number, '0');

        // Trim all excessive characters.
        $new_number = preg_replace('/[^0-9.]+/', '', $new_number);

        return empty($new_number) ? false : $new_number;
    }

    /**
     * Checks if the phone number is valid.
     *
     * @param $phoneNumber string The phone number
     * @return bool Returns true if valid.
     */
    public static function isValidPhoneNumber($phoneNumber)
    {
        $phoneNumber = trim($phoneNumber);

        return (isset($phoneNumber) && !empty($phoneNumber));
    }

    /**
     * Gets the phone number, given an order ID.
     *
     * Falls back to the customer's phone number.
     *
     * @param $orderID int The order ID.
     * @return bool|string Returns false on failure, phone number on success.
     */
    public static function getPhoneNumberByOrder($orderID)
    {
        $billing_phone = get_post_meta($orderID, '_billing_phone', true);
        $customer = get_post_meta($orderID, '_customer_user', true);
        $customer_phone = get_user_meta($customer, 'billing_phone', true);
        $phone = (empty($billing_phone)) ? $customer_phone : $billing_phone;

        return self::cleanPhoneNumber($phone);
    }

    /**
     * Gets the right notification message, given an order status.
     *
     * @param $orderStatus
     * @return string
     */
    public static function getTextMessageByOrderStatus($orderStatus)
    {
        return get_option('wcp_notification_message_' . $orderStatus);
    }
}