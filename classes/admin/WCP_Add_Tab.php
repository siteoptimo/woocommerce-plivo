<?php
/**
* @author Pieter Carette <pieter@siteoptimo.com>
*/

class WCP_Add_Tab {
    public function __construct() {
        add_filter( 'woocommerce_settings_tabs_array', array($this,'add_settings_tab'), 50 );
    }

    public function add_settings_tab( $settings_tabs ) {
        $settings_tabs['woocommerce_sms_settings'] = __( 'SMS', 'woocommerce-sms-settings' );
        return $settings_tabs;
    }
} 