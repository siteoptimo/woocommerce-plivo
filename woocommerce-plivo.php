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

if (!class_exists('WooCommerce_Plivo')) {

    final class WooCommerce_Plivo
    {
        protected static $_instance = null;

        function __construct()
        {
            if ( function_exists( "__autoload" ) ) {
                spl_autoload_register( "__autoload" );
            }

            spl_autoload_register(array($this, 'autoload'));

            $this->includes();
            $this->register_scripts();
            add_action(
                'init',
                function () {
                    new WCP_Admin_Add_Tab();
                    new WCP_Admin_Setting_Fields();
                }
            );

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
            require_once $this->plugin_path() . 'includes/wcp-functions.php';
        }

        public function autoload($class)
        {
            if(strpos($class, 'WCP_') !== 0) return;

            $class_exploded = explode('_', $class);

            $filename = strtolower(implode('-', $class_exploded)) . '.php';

            // first try the directory
            $file = 'includes/' . strtolower($class_exploded[1]) . '/' . $filename;

            if(is_readable($this->plugin_path() . $file)) {
                require_once $this->plugin_path() . $file;
                return;
            }

            // try without a subdirectory
            $filename = strtolower(implode('-', $class_exploded)) . '.php';

            $file = 'includes/' . $filename;

            if(is_readable($this->plugin_path() . $file)) {
                require_once $this->plugin_path() . $file;
                return;
            }
            return;
        }

        public function plugin_url()
        {
            return plugins_url('/', __FILE__);
        }

        public function plugin_path()
        {
            return plugin_dir_path(__FILE__);
        }

        private function register_scripts()
        {
            wp_register_script('wcp-admin', $this->plugin_url() . 'assets/js/admin.js', array('jquery'), '0.1', true);
        }


    }

    WooCommerce_Plivo::instance();
}