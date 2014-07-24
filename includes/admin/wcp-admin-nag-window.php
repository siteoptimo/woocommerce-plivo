<?php

/**
 * @author Pieter Carette <pieter@siteoptimo.com>
 */
class WCP_Admin_Nag_Window
{
    public function __construct()
    {
        add_action('admin_notices', array($this, 'wcp_admin_notice'));
        add_action('admin_notices', array($this, 'wcp_nag_ignore'));

    }

    public function wcp_admin_notice()
    {
        global $current_user;
        $user_id = $current_user->ID;
        var_dump(get_user_meta($user_id, 'wcp_ignored_notice'));

        if (!get_user_meta($user_id, 'wcp_ignored_notice') && !get_option('wcp_auth_id') && !get_option('wcp_auth_password')) {

            if (current_user_can('install_plugins') && !(isset($_GET['tab']) && $_GET['tab'] == 'woocommerce_sms_settings')) {
                echo '<div class="updated"><p>' . __('The WooCommerce Plivo integration plugin is not yet configured.', 'woocommerce-plivo') . '<br><a href="admin.php?page=wc-settings&tab=woocommerce_sms_settings">' . __('Take me to the Plivo settings page!', 'woocommerce-plivo') . '</a> | <a href="?wcp_nag_ignore=0">'.__('I know, hide this message','woocommerce-plivo').'</a></p></div>';
            }

        }
    }

    public function wcp_nag_ignore()
    {
        global $current_user;
        $user_id = $current_user->ID;
        /* If user clicks to ignore the notice, add that to their user meta */
        if (isset($_GET['wcp_nag_ignore']) && '0' == $_GET['wcp_nag_ignore']) {
            add_user_meta($user_id, 'wcp_ignored_notice', 'true', true);
        }
    }
}