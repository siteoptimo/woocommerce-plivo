<?php
/**
 * Class WCP_Admin_Add_Settings_Link
 *
 * Adds the settings link to the plugin's box.
 *
 * @package WooCommerce_Plivo
 * @class WCP_Admin_Add_Settings_Link
 * @author Pieter Carette <pieter@siteoptimo.com>
 */
if(!defined('ABSPATH')) exit;

class WCP_Admin_Add_Settings_Link
{
    /**
     * Add filter
     */
    public function __construct()
    {
        add_filter('plugin_action_links_' . WooCommerce_Plivo::instance()->plugin_basename(), array($this, 'wcp_settings_link'));
    }

    /**
     * Add the settings link.
     *
     * @param $links
     * @return mixed
     */
    public function wcp_settings_link($links)
    {
        $settings_link = '<a href="admin.php?page=wc-settings&tab=woocommerce_sms_settings">Settings</a>';
        array_unshift($links, $settings_link);

        return $links;
    }

}