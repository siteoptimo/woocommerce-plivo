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

        $terms = get_terms('shop_order_status', array('hide_empty' => 0,'orderby'=>'id'));
        $optionterms= array();
        foreach($terms as $term){
            $optionterms[$term->slug]=$term->name;
        }

        $settings = array(
            'plivo_settings_title' => array(
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
            ),
            'notification_settings_title' => array(
                'name'     => __( 'Notification settings', 'woocommerce-plivo' ),
                'type'     => 'title',
                'id'       => 'wcp_notification_settings_section_title'
            ),
            'notification_on' => array(
                'title' => __( 'Auto send notification on:', 'woocommerce-plivo' ),
                'type' => 'multiselect',
                'class' => 'multiselect chosen_select',
                'id'   => 'wcp_notification',
                'options' => $optionterms
            ),

            'section_end2' => array(
                'type' => 'sectionend',
                'id' => 'wcp_settings_section_end'
            ),
            'demo_title' => array(
                'name'     => __( 'Send a test SMS', 'woocommerce-plivo' ),
                'type'     => 'title',
                'id'       => 'wcp_demo_section_title'
            ),
            'demo_phone_number' => array(
                'name' => __( 'Phone Number', 'woocommerce-plivo' ),
                'type' => 'text',
                'id'   => 'wcp_demo_phone_number'
            ),

            'demo_message' => array(
                'name' => __( 'Message', 'woocommerce-plivo' ),
                'type' => 'textarea',
                'id'   => 'wcp_demo_message',
                'default'=> 'Test message'
            ),
            'demo_send_button' => array(
                'name' => '',
                'type' => 'text',
                'class'=> 'hidden',
                'id'   => 'wcp_send_button',
                'desc'=> __('<a href="" class="button">Send</a>','woocommerce-plivo'),
            ),

            'demo_section_end' => array(
                'type' => 'sectionend',
                'id' => 'wcp_semo_section_end'
            ),

        );
        return apply_filters( 'woocommerce_sms_settings', $settings );
    }

} 