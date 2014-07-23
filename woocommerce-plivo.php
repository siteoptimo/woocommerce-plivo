<?php
/*
Plugin Name: WooCommerce Plivo
Version: 1.0
Plugin URI: http://www.siteoptimo.com/#utm_source=wpadmin&utm_medium=plugin&utm_campaign=wcplivoplugin
Description: Send SMS update notifications to your customers with this Plivo plugin for WooCommerce.
Author: SiteOptimo
Author URI: https://www.siteoptimo.com/
Text Domain: woocommerce-plivo
Domain Path: /i18n/languages/
License: GPL v3

WordPress SEO Plugin
Copyright (C) 2014, SiteOptimo - team@siteoptimo.com

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
gi
You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

/**
 * Check if WooCommerce is active
 */
if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
    exit;
}

define('WCP_PATH', dirname(__FILE__));

if (!class_exists('WooCommerce_Plivo')) {

    final class WooCommerce_Plivo
    {
        protected static $_instance = null;

        function __construct()
        {
            $this->includes();
            add_action('init', function () {
                new WCP_Add_Tab();
                new WCP_Setting_Fields();
            });

        }

        public static function instance()
        {
            if (is_null(self::$_instance)) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        public function includes()
        {
            include(trailingslashit(WCP_PATH) . 'classes/admin/WCP_Add_Tab.php');
            include(trailingslashit(WCP_PATH) . 'classes/admin/WCP_Setting_Fields.php');
        }


    }

    WooCommerce_Plivo::instance();
}