<?php
/**
 * @author Koen Van den Wijngaert <koen@siteoptimo.com>
 */

class WCP_Admin_Add_Settings_Link {

    public function __construct() {
        add_filter('plugin_action_links_'.WCP_BASENAME, array($this,'wcp_settings_link') );
    }
    public function wcp_settings_link($links) {
        $settings_link = '<a href="admin.php?page=wc-settings&tab=woocommerce_sms_settings">Settings</a>';
        array_unshift($links, $settings_link);
        return $links;
    }

}




