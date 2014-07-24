<?php

/**
 * Class WCP_Admin_Nag_Window
 *
 * Displays the nagging window when the settings are not filled out.
 *
 * @package WooCommerce_Plivo
 * @class WCP_Admin_Nag_Window
 * @author Pieter Carette <pieter@siteoptimo.com>
 */
class WCP_Admin_Nag_Window
{
    /**
     * Construct the class.
     */
    public function __construct()
    {
        add_action('admin_notices', array($this, 'wcp_nag_ignore'));
        add_action('admin_notices', array($this, 'wcp_admin_notice'));

    }

    /**
     * Print the admin notice.
     */
    public function wcp_admin_notice()
    {
        global $current_user;
        $user_id = $current_user->ID;

        // Show the notice if the settings are blank AND the message has not yet been dismissed.
        if(!is_wcp_ready() && !get_user_meta($user_id, 'wcp_ignored_notice', true))
        {
            // Only show the notice if we can edit the settings and if we're not on the settings page.
            if(current_user_can('install_plugins') && !(isset($_GET['tab']) && $_GET['tab'] == 'woocommerce_sms_settings'))
            {
                $current_page = $_SERVER['REQUEST_URI'];
                $separator = strpos($current_page, '?') !== false ? '&' : '?';
                $nag_url = $current_page . $separator . 'wcp_nag_ignore';
                echo '<div class="updated"><p>' . __('The WooCommerce Plivo integration plugin is not yet configured.', 'woocommerce-plivo') . '<br><a href="admin.php?page=wc-settings&tab=woocommerce_sms_settings">' . __('Take me to the Plivo settings page!', 'woocommerce-plivo') . '</a> | <a href="' . $nag_url . '">' . __('I know, hide this message', 'woocommerce-plivo') . '</a></p></div>';
            }
        }
    }

    /**
     * Handles the notice's ignore button.
     */
    public function wcp_nag_ignore()
    {
        global $current_user;
        $user_id = $current_user->ID;

        if(isset($_GET['wcp_nag_ignore']))
        {
            // Okay, the user doesn't want to hear it anymore, let's save his preferences.
            add_user_meta($user_id, 'wcp_ignored_notice', true, true);
        }
    }
}