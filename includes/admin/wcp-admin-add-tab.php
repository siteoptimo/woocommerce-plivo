<?php
if(!defined('ABSPATH')) exit;

/**
 * Class WCP_Admin_Add_Tab
 *
 * Adds the WooCommerce settings SMS tab.
 *
 * @package WooCommerce_Plivo
 * @class WCP_Admin_Add_Tab
 * @author Pieter Carette <pieter@siteoptimo.com>
 */
class WCP_Admin_Add_Tab
{
    /**
     * Constructs the class and adds the filter.
     */
    public function __construct()
    {
        add_filter('woocommerce_settings_tabs_array', array($this, 'add_settings_tab'), 50);
    }

    /**
     * Adds the settings tab.
     *
     * @param $settings_tabs
     * @return mixed
     */
    public function add_settings_tab($settings_tabs)
    {
        $settings_tabs['woocommerce_sms_settings'] = __('SMS', 'woocommerce-sms-settings');

        return $settings_tabs;
    }
} 