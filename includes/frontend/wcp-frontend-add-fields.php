<?php
if(!defined('ABSPATH')) exit;

/**
 * Class WCP_Frontend_Add_Fields
 *
 * Adds an optin/optout on checkout
 *
 * @package WooCommerce_Plivo
 * @class WCP_Frontend_Add_Fields
 * @author Pieter Carette <pieter@siteoptimo.com>
 */
class WCP_Frontend_Add_Fields {

    /**
     * Constructs the class and adds the filter.
     */
    public function __construct()
    {
        add_filter('woocommerce_checkout_fields', array($this, 'wcp_override_checkout_fields'));
        add_action('woocommerce_checkout_process', array($this, 'wcp_checkout_field_process'));
    }

    /**
     * Override the checkout fields and add the optin/optout option.
     *
     * @param $fields
     * @return mixed
     */
    public function wcp_override_checkout_fields($fields)
    {
        $fields['order']['get_sms_notification'] = array(
            'label'     => __('Receive SMS status notifications?', 'woocommerce'),
            'type'      => 'checkbox',
            'default'   => 'yes',
            'required'  => false,
            'clear'     => true
        );

        return $fields;
    }
    /**
     * Process the optin/optout option.
     *
     * @param $order_id
     */
    public function wcp_checkout_field_process( $order_id ) {
            update_post_meta( $order_id, 'SMS notifications', sanitize_text_field( $_POST['get_sms_notification'] ) );
    }
}