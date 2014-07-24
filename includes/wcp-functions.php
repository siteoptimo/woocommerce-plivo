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
