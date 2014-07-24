<?php
/*
Plugin Name: WooCommerce Plivo
Version: 1.0
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

/**
 * Check if WooCommerce is active
 */
if(!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))))
{
    exit;
}

if ( ! defined( 'WCP_FILE' ) ) {
    define( 'WCP_FILE', __FILE__ );
}

if ( ! defined( 'WCP_PATH' ) ) {
    define( 'WCP_PATH', plugin_dir_path( WCP_FILE ) );
}

if ( ! defined( 'WCP_BASENAME' ) ) {
    define( 'WCP_BASENAME', plugin_basename( WCP_FILE ) );
}


if(!class_exists('WooCommerce_Plivo'))
{

    final class WooCommerce_Plivo
    {
        protected static $_instance = null;

        function __construct()
        {
            if(function_exists("__autoload"))
            {
                spl_autoload_register("__autoload");
            }

            spl_autoload_register(array($this, 'autoload'));
            spl_autoload_register(array($this, 'autoload_plivo'));

            $this->includes();
            $this->register_scripts();


            $this->init();

        }

        public static function instance()
        {
            if(is_null(self::$_instance))
            {
                self::$_instance = new self();
            }

            return self::$_instance;
        }

        public function includes()
        {
            require_once $this->plugin_path() . 'includes/wcp-functions.php';
        }

        public function autoload_plivo($class)
        {
            if($class == "RestAPI")
            {
                require_once $this->plugin_path() . 'library/plivo/plivo.php';
            }
        }

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
            add_action(
                'admin_enqueue_scripts',
                function ()
                {
                    wp_enqueue_script(
                        'wcp-admin',
                        $this->plugin_url() . 'assets/js/admin.js',
                        array('jquery'),
                        '0.1',
                        true
                    );
                }
            );
        }

        private function init()
        {

            if(is_admin()) {
                $this->admin_init();
            }



        }

        private function admin_init()
        {
            add_action('wp_ajax_wcp_send_message', array(new WCP_AJAX(), 'send_message'));

            add_action(
                'init',
                function ()
                {
                    new WCP_Admin_Add_Tab();
                    new WCP_Admin_Add_Settings_Link();
                    new WCP_Admin_Nag_Window();
                    new WCP_Admin_Setting_Fields();
                }
            );

            add_filter('woocommerce_new_order_note_data', array('WCP_Admin_Order_Note', 'order_note_data'));


            add_action('current_screen', function() {
                    $current_screen = get_current_screen()->id;

                    if($current_screen == 'shop_order') {
                        new WCP_Admin_Order_Note();
                    }
            });
        }


    }

    $WCP = WooCommerce_Plivo::instance();
}