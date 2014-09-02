<?php

/**
 * WooCommerce WPML Compatibility
 **/

if(!defined('ABSPATH'))
    exit; // Exit if accessed directly

if(!class_exists('WPML_Compatibility')) :

    class WPML_Compatibility
    {

        function __construct()
        {
            add_action('init', array($this, 'init'), 9);

        }

        function init()
        {
            add_filter('wcp_after_order_status_changed_message', array($this, 'translate_message_to_order_lang'), 10, 3);
        }

        function translate_message_to_order_lang($message, $id, $newStatus)
        {
            global $sitepress;
            $currentLang = ICL_LANGUAGE_CODE;
            $orderLang = $this->getLanguageByOrder($id);
            $sitepress->switch_lang($orderLang);
            $msg = get_option('wcp_notification_message_' . $newStatus);
            $sitepress->switch_lang($currentLang);

            return $msg;
        }

        function getLanguageByOrder($orderID)
        {
            return get_post_meta($orderID, 'wpml_language', true);
        }

    }

endif; // Class exists check

if(class_exists('SitePress'))
{
    $wpml_compatibility = new WPML_Compatibility();
}