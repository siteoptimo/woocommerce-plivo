<?php
/**
 * @author Koen Van den Wijngaert <koen@siteoptimo.com>
 */
if(!defined('ABSPATH')) exit;

class WCP_Tools {
    public static function cleanPhoneNumber($phoneNumber) {
        if(!self::isValidPhoneNumber($phoneNumber)) return false;

        $search = array('(0)');

        $replace = array('');

        $new_number = str_replace($search, $replace, $phoneNumber);

        $new_number = ltrim($new_number, '0');

        $new_number = preg_replace('/[^0-9.]+/', '', $new_number);

        return empty($new_number) ? false : $new_number;
    }

    public static function isValidPhoneNumber($phoneNumber) {
        $phoneNumber = trim($phoneNumber);
        return (isset($phoneNumber) && !empty($phoneNumber));
    }

    public static function getPhoneNumberByOrder($orderID) {
        $billing_phone = get_post_meta($orderID, '_billing_phone', true);
        $customer = get_post_meta($orderID, '_customer_user', true);
        $customer_phone = get_user_meta($customer, 'billing_phone', true);
        $phone = (empty($billing_phone)) ? $customer_phone : $billing_phone;

        return self::cleanPhoneNumber($phone);

    }

    public static function getTextMessageByOrderStatus($orderStatus)
    {
        return get_option('wcp_notification_message_' . $orderStatus);
    }
}