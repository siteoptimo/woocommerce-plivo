<?php
/*
Plugin Name: WooCommerce Plivo
Version: 1.1
Plugin URI: http://www.siteoptimo.com/#utm_source=wpadmin&utm_medium=plugin&utm_campaign=wcplivoplugin
Description: Send SMS update notifications to your customers with this Plivo plugin for WooCommerce.
Author: SiteOptimo
Author URI: http://www.siteoptimo.com/
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

if(!defined('ABSPATH'))
{
    exit;
}

/**
 * Check if WooCommerce is active
 */
if(in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))))
{
    if(!class_exists('WooCommerce_Plivo'))
    {
        /**
         * Main WooCommerce_Plivo Class
         *
         * @class WooCommerce_Plivo
         * @version 1.1
         */
        final class WooCommerce_Plivo
        {
            /**
             * @var WooCommerce_Plivo Singleton implementation
             */
            private static $_instance = null;

            /**
             * Current version number
             *
             * @var string
             */
            public static $version = "1.1";


            /**
             * Constructor method
             *
             * Bootstraps the plugin.
             */
            function __construct()
            {
                // Register the autoloader classes.
                spl_autoload_register(array($this, 'autoload'));
                spl_autoload_register(array($this, 'autoload_plivo'));

                // Use the fallback HTTP_Request2 if the PEAR package is not available.
                if(!$this->HTTP_Request2_Available())
                {
                    ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . $this->plugin_path() . 'library');
                }

                $this->includes();

                $this->register_scripts();

                $this->init();

            }

            /**
             * Returns an instance of the WooCommerce_Plivo class.
             *
             * @return WooCommerce_Plivo
             */
            public static function instance()
            {
                if(is_null(self::$_instance))
                {
                    // Create instance if not set.
                    self::$_instance = new self();
                }

                return self::$_instance;
            }


            /**
             * Handles file includes, like functions.
             */
            public function includes()
            {
                require_once $this->plugin_path() . 'includes/wcp-functions.php';
            }

            /**
             * Loads the Plivo API library as soon as it is needed.
             *
             * @param $class string
             */
            public function autoload_plivo($class)
            {
                if($class == "RestAPI")
                {
                    require_once $this->plugin_path() . 'library/plivo/plivo.php';
                }
            }

            /**
             * Autoloads the WooCommerce Plivo classes whenever they are needed.
             *
             * @param $class
             */
            public function autoload($class)
            {
                if(strpos($class, 'WCP_') !== 0)
                {
                    return;
                }

                $class_exploded = explode('_', $class);

                $filename = strtolower(implode('-', $class_exploded)) . '.php';

                // first try the directory
                $file = 'includes/' . strtolower($class_exploded[1]) . '/' . $filename;

                if(is_readable($this->plugin_path() . $file))
                {
                    require_once $this->plugin_path() . $file;

                    return;
                }

                // try without a subdirectory
                $filename = strtolower(implode('-', $class_exploded)) . '.php';

                $file = 'includes/' . $filename;

                if(is_readable($this->plugin_path() . $file))
                {
                    require_once $this->plugin_path() . $file;

                    return;
                }

                return;
            }

            /**
             * @return string The plugin URL
             */
            public function plugin_url()
            {
                return plugins_url('/', __FILE__);
            }

            /**
             * @return string The plugin path
             */
            public function plugin_path()
            {
                return plugin_dir_path(__FILE__);
            }

            /**
             * @return string The plugin basename
             */
            public function plugin_basename()
            {
                return plugin_basename(__FILE__);
            }

            /**
             * Hooks onto the admin_enqueue_scripts hook.
             */
            private function register_scripts()
            {
                add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
            }

            /**
             * Registers, localizes and enqueues the Javascript files.
             */
            public function admin_enqueue_scripts()
            {
                wp_register_script('wcp-admin', $this->plugin_url() . 'assets/js/admin.js', array('jquery'), self::$version, true);

                wp_localize_script('wcp-admin', 'WCP', array('plugin_url' => $this->plugin_url()));

                wp_enqueue_script('wcp-admin');
            }

            /**
             * Initialize.
             */
            private function init()
            {
                if(is_admin())
                {
                    $this->admin_hooks();
                }

                $this->frontend_hooks();

                $this->hooks();
            }

            /**
             * Enables the needed admin hooks.
             */
            private function admin_hooks()
            {

                add_action('init', array($this, 'admin_init'));

                // First check if the plugin is configured properly.
                if(is_wcp_ready())
                {
                    add_filter('woocommerce_new_order_note_data', array('WCP_Admin_Order_Note', 'order_note_data'));

                    add_action('current_screen', array($this, 'current_screen'));

                    add_action('wp_ajax_wcp_send_message', array(new WCP_AJAX(), 'send_message'));
                }
            }

            /**
             * Initializes all of the admin classes.
             */
            public function admin_init()
            {
                new WCP_Admin_Add_Tab();
                new WCP_Admin_Nag_Window();
                new WCP_Admin_Add_Settings_Link();
                new WCP_Admin_Setting_Fields();
            }

            /**
             * Enables the needed frontend hooks.
             */
            private function frontend_hooks()
            {
                // First check if the plugin is configured properly.
                if(is_wcp_ready())
                {
                    add_action('init', array($this, 'frontend_init'));
                }
            }

            /**
             * Initializes all of the frontend classes.
             */
            public function frontend_init()
            {
                new WCP_Opt_In_Out();
            }

            /**
             * Initializes the WCP_Admin_Order_Note class.
             */
            public function current_screen()
            {
                $current_screen = get_current_screen()->id;

                // Only do this if we are on the edit shop_order screen.
                if($current_screen == 'shop_order')
                {
                    new WCP_Admin_Order_Note();
                }
            }

            /**
             * The site-wide hooks.
             */
            private function hooks()
            {
                if(is_wcp_ready())
                {
                    add_action('init', array($this, 'init_status_hooks'));
                }
            }

            /**
             * Initializes the status hooks.
             */
            public function init_status_hooks()
            {
                new WCP_Status_Hook();
            }

            /**
             * Checks for availability of the HTTP_Request2 PEAR package
             *
             * @return bool
             */
            private function HTTP_Request2_Available()
            {
                if(class_exists('HTTP_Request2', false)) return true;

                @$include = include('HTTP/Request2.php');

                return $include === 1;
            }
        }

        // Our WooCommmerce_Plivo instance.
        $WCP = WooCommerce_Plivo::instance();
    }

}