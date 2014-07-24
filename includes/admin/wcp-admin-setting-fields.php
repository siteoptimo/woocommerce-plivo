<?php
/**
* @author Pieter Carette <pieter@siteoptimo.com>
*/

class WCP_Admin_Setting_Fields {


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

    private function get_status_terms()
    {
        $terms = get_terms('shop_order_status', array('hide_empty' => 0, 'orderby' => 'id'));
        return $terms;
    }

    private function get_option_terms()
    {
        $terms = $this->get_status_terms();
        $optionterms = array();
        foreach ($terms as $term) {
            $optionterms[$term->slug] = $term->name;
        }
        return $optionterms;
    }

    private function generate_optional_textareas()
    {
        $terms = $this->get_status_terms();
        $textareas = array();
        foreach ($terms as $term) {
            $textareas['notification_message_' . $term->slug] = array(
                'title' => __('Auto message for ', 'woocommerce-plivo') . $term->slug,
                'type' => 'textarea',
                'id' => 'wcp_notification_message_' . $term->slug,
                'css' => 'width:100%; height: 65px;',
                'class' => 'optional_textarea',
                'default' => '{shop name} status update: Order {order number} is now ' . $term->slug . '.',
            );
        }
        return $textareas;
    }

    private function get_settings()
    {

        $settings['plivo_settings_title'] = array(
            'name' => __('Plivo Settings', 'woocommerce-plivo'),
            'type' => 'title',
            'desc' => __('Find your Plivo Auth ID and Auth Token on your <a href="https://manage.plivo.com/dashboard/" target="_blank">Plivo Dashboard</a> page.', 'woocommerce-plivo'),
            'id' => 'wcp_plivo_settings_section_title'
        );

        $settings['auth_id'] = array(
            'name' => __('Auth ID', 'woocommerce-plivo'),
            'type' => 'text',
            'id' => 'wcp_auth_id'
        );

        $settings['auth_token'] = array(
            'name' => __('Auth Token', 'woocommerce-plivo'),
            'type' => 'password',
            'id' => 'wcp_auth_password'
        );
        $settings['section_end'] = array(
            'type' => 'sectionend',
            'id' => 'wcp_settings_section_end'
        );
        $settings['notification_settings_title'] = array(
            'name' => __('Notification settings and messages', 'woocommerce-plivo'),
            'type' => 'title',
            'desc' => __( 'Choose when to send a status notification message and modify the content of the messages.', 'woocommerce-plivo' ),
            'id' => 'wcp_notification_settings_section_title'
        );
        $settings['notification_on'] = array(
            'title' => __('Auto send notification on:', 'woocommerce-plivo'),
            'type' => 'multiselect',
            'class' => 'multiselect chosen_select',
            'id' => 'wcp_notification',
            'options' => $this->get_option_terms()
        );

        $settings=array_merge($settings,$this->generate_optional_textareas());

        $settings['section_end2'] = array(
            'type' => 'sectionend',
            'id' => 'wcp_settings_section_end'
        );
        $settings['demo_title'] = array(
            'name' => __('Send a test SMS', 'woocommerce-plivo'),
            'type' => 'title',
            'id' => 'wcp_demo_section_title'
        );
        $settings['demo_phone_number'] = array(
            'name' => __('Phone Number', 'woocommerce-plivo'),
            'type' => 'text',
            'id' => 'wcp_demo_phone_number',
            'desc_tip'	=> __( 'Phone number to send the test message to.', 'woocommerce-plivo' )
        );

        $settings['demo_message'] = array(
            'name' => __('Message', 'woocommerce-plivo'),
            'type' => 'textarea',
            'id' => 'wcp_demo_message',
            'default' => 'Test message',
            'desc_tip'	=> __( 'Test message to send to your mobile.', 'woocommerce-plivo' ),

        );
        $settings['demo_send_button'] = array(
            'name' => '',
            'type' => 'text',
            'class' => 'hidden',
            'id' => 'wcp_send_button',
            'desc' => __('<a href="" class="button">Send</a>', 'woocommerce-plivo'),
        );

        $settings['demo_section_end'] = array(
            'type' => 'sectionend',
            'id' => 'wcp_semo_section_end'
        );

        return apply_filters('woocommerce_sms_settings', $settings);
    }
} 