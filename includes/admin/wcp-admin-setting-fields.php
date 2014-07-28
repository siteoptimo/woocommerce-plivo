<?php
if(!defined('ABSPATH')) exit;

/**
 * Class WCP_Admin_Setting_Fields
 *
 * Handles the WooCommerce Plivo settings. This uses the WooCommerce Settings API.
 *
 * @package WooCommerce_Plivo
 * @class WCP_Admin_Setting_Fields
 * @author Pieter Carette <pieter@siteoptimo.com>
 */
class WCP_Admin_Setting_Fields
{
    /**
     * Construct the class.
     */
    public function __construct()
    {
        add_action('woocommerce_settings_woocommerce_sms_settings', array($this, 'settings_tab'));
        add_action('woocommerce_update_options_woocommerce_sms_settings', array($this, 'update_settings'));
    }

    /**
     * Settings tab.
     */
    function settings_tab()
    {
        woocommerce_admin_fields($this->get_settings());
    }

    /**
     * Update the settings values.
     */
    function update_settings()
    {
        woocommerce_update_options($this->get_settings());
    }

    /**
     * Gets the order statuses.
     *
     * @return array The terms.
     */
    private function get_status_terms()
    {
        $terms = get_terms('shop_order_status', array('hide_empty' => 0, 'orderby' => 'id'));

        return $terms;
    }

    /**
     * Gets the option values for the order statuses.
     *
     * @return array The option terms.
     */
    private function get_option_terms()
    {
        $terms = $this->get_status_terms();
        $optionterms = array();
        foreach($terms as $term)
        {
            $optionterms[$term->slug] = $term->name;
        }

        return $optionterms;
    }

    /**
     * Generates the optional textareas.
     *
     * @return array the textareas.
     */
    private function generate_optional_textareas()
    {
        $terms = $this->get_status_terms();
        $textareas = array();
        foreach($terms as $term)
        {
            $textareas['notification_message_' . $term->slug] = array('title' => __('Auto message for ', 'woocommerce-plivo') . $term->slug, 'type' => 'textarea', 'id' => 'wcp_notification_message_' . $term->slug, 'css' => 'width:100%; height: 65px;', 'class' => 'optional_textarea', 'default' => sprintf(__('{shop_name} status update: Order {order_id} is now %s', 'woocommerce-plivo'), $term->slug),);
        }

        return $textareas;
    }

    /**
     * Gets all of the settings.
     *
     * @return mixed The settings
     */
    private function get_settings()
    {

        $settings['plivo_settings_title'] = array('name' => __('Plivo Settings', 'woocommerce-plivo'), 'type' => 'title', 'desc' => __('Find your Plivo Auth ID and Auth Token on your <a href="https://manage.plivo.com/dashboard/" target="_blank">Plivo Dashboard</a> page. Get your number at <a href="https://manage.plivo.com/number/" target="_blank">the Plivo numbers tab</a>.', 'woocommerce-plivo'), 'id' => 'wcp_plivo_settings_section_title');

        $settings['auth_id'] = array('name' => __('Auth ID', 'woocommerce-plivo'), 'type' => 'text', 'id' => 'wcp_auth_id', 'desc_tip' => __('Required. Needed to make the magic happen.', 'woocommerce-plivo'));

        $settings['auth_token'] = array('name' => __('Auth Token', 'woocommerce-plivo'), 'type' => 'password', 'id' => 'wcp_auth_password', 'desc_tip' => __('Required. Needed to make the magic happen.', 'woocommerce-plivo'));
        $settings['from_number'] = array('name' => __('From number', 'woocommerce-plivo'), 'type' => 'text', 'id' => 'wcp_from_number', 'desc_tip' => __('Required. Needed to make the magic happen.', 'woocommerce-plivo'));
        $settings['section_end'] = array('type' => 'sectionend', 'id' => 'wcp_settings_section_end');

        $settings['optout_settings_title'] = array('name' => __('Opt-in/Opt-out for customers', 'woocommerce-plivo'), 'type' => 'title', 'desc' => __('Should the customer be able to opt-out of SMS status notifications?', 'woocommerce-plivo'), 'id' => 'wcp_optout_settings_title');
        $settings['optout_enabled'] = array('name' => __('Enable opt-out for clients', 'woocommerce-plivo'), 'type' => 'checkbox', 'desc' => '', 'id' => 'wcp_optout_enabled', 'default' => 'yes');
        $settings['optout_default'] = array('name' => __('Opt-in checkbox default', 'woocommerce-plivo'), 'type' => 'select', 'options' => array('yes' => __('Checked', 'woocommerce-plivo'), 'no' => __('Unchecked', 'woocommerce-plivo')), 'desc' => '', 'id' => 'wcp_optout_default', 'default' => 'yes');
        $settings['optout_message'] = array('name' => __('Opt-in checkbox message', 'woocommerce-plivo'), 'type' => 'text', 'id' => 'wcp_optout_message', 'default' => __('I want to receive SMS notifications.', 'woocommerce-plivo'));

        $settings['section_end2'] = array('type' => 'sectionend', 'id' => 'wcp_settings_section_end2');

        $settings['notification_settings_title'] = array('name' => __('Notification settings and messages', 'woocommerce-plivo'), 'type' => 'title', 'desc' => __('Choose when to send a status notification message and modify the content of the messages.', 'woocommerce-plivo'), 'id' => 'wcp_notification_settings_section_title');
        $settings['notification_on'] = array('title' => __('Auto send notification on:', 'woocommerce-plivo'), 'type' => 'multiselect', 'class' => 'multiselect chosen_select', 'id' => 'wcp_notification', 'options' => $this->get_option_terms());

        $settings = array_merge($settings, $this->generate_optional_textareas());

        $settings['section_end3'] = array('type' => 'sectionend', 'id' => 'wcp_settings_section_end3');

        $auth_token = get_option('wcp_auth_password', '');
        $auth_id = get_option('wcp_auth_id', '');
        $from = get_option('wcp_from_number', '');

        if(!empty($auth_token) && !empty($auth_id) && !empty($from))
        {
            $settings['demo_title'] = array('name' => __('Send a test SMS', 'woocommerce-plivo'), 'type' => 'title', 'id' => 'wcp_demo_section_title');
            $settings['demo_phone_number'] = array('name' => __('Phone Number', 'woocommerce-plivo'), 'type' => 'text', 'id' => 'wcp_demo_phone_number', 'desc_tip' => __('Phone number to send the test message to.', 'woocommerce-plivo'));

            $settings['demo_message'] = array('name' => __('Message', 'woocommerce-plivo'), 'type' => 'textarea', 'id' => 'wcp_demo_message', 'default' => 'Test message', 'desc_tip' => __('Test message to send to your mobile.', 'woocommerce-plivo'));
            $settings['demo_send_button'] = array('name' => '', 'type' => 'text', 'class' => 'hidden', 'id' => 'wcp_send_button', 'desc' => '<a href="" class="button">' . __('Send', 'woocommerce-plivo') . '</a>');

            $settings['demo_section_end'] = array('type' => 'sectionend', 'id' => 'wcp_semo_section_end');
        }

        return apply_filters('woocommerce_sms_settings', $settings);
    }
} 