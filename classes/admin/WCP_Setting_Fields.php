<?php
/**
* @author Pieter Carette <pieter@siteoptimo.com>
*/

class WCP_Setting_Fields {


    public function __construct(){
        add_action( 'woocommerce_settings_woocommerce_sms_settings', array($this, 'settings_tab') );
        add_action( 'woocommerce_update_options_woocommerce_sms_settings', array($this, 'update_settings') );

    }

    function settings_tab() {
        woocommerce_admin_fields( $this->get_settings() );
    }

    function update_settings() {
        woocommerce_update_options( $this->get_settings() );
    }

    function get_settings() {
        $settings = array(
            'section_title' => array(
                'name'     => __( 'Plivo Settings', 'woocommerce-plivo' ),
                'type'     => 'title',
                'desc' => __( 'Find your Plivo Auth ID and Auth Token on your <a href="https://manage.plivo.com/dashboard/" target="_blank">Plivo Dashboard</a> page.', 'woocommerce-plivo' ),
                'id'       => 'wcp_plivo_settings_section_title'
            ),
            'auth_id' => array(
                'name' => __( 'Auth ID', 'woocommerce-plivo' ),
                'type' => 'text',
                'id'   => 'wcp_auth_id'
            ),

            'auth_token' => array(
                'name' => __( 'Auth Token', 'woocommerce-plivo' ),
                'type' => 'password',
                'id'   => 'wcp_auth_password'
            ),
            'section_end' => array(
                'type' => 'sectionend',
                'id' => 'wcp_settings_section_end'
            )
        );
        return apply_filters( 'woocommerce_sms_settings', $settings );
    }

} 