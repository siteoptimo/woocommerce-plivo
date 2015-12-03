<?php
/**
 * WooCommerce Plivo's Functions.
 *
 * @author Koen Van den Wijngaert <koen@siteoptimo.com>
 */
if(!defined('ABSPATH')) exit;

/**
 * Checks whether WooCommerce Plivo is ready to use.
 *
 * @return bool
 */
function is_wcp_ready() {
    $wcp_auth_id = get_option('wcp_auth_id');
    $wp_auth_password = get_option('wcp_auth_password');
    return !empty($wcp_auth_id) && !empty($wp_auth_password);
}


function wcp_get_status_hook() {
    global $WCP;

    return $WCP->getStatusHook();
}


add_filter('wcp_variables', function($variables) {
    $variables['test'] = __('Test Description');

    return $variables;
});

add_filter('wcp_variable_values', function($values, $order_id) {
    $values['test'] = 'Your order ID is ' . $order_id;

    return $values;
}, 10, 2);

add_filter('wcp_order_status_changed_message', function($message, $orderID, $newStatus) {

    // do magic

    return $message;
}, 10, 3);