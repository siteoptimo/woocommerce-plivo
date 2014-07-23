<?php
/**
 * @author Koen Van den Wijngaert <koen@siteoptimo.com>
 */

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
}