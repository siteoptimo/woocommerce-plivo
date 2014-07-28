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
            'required'  => false,
            'clear'     => true
        );

        return $fields;
    }

} 