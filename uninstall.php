<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @since      1.0.0
 * @package    Staydesk
 */

// If uninstall not called from WordPress, exit.
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

global $wpdb;

// Delete all plugin options
delete_option('staydesk_version');
delete_option('staydesk_hotel_count');
delete_option('staydesk_paystack_secret_key');
delete_option('staydesk_paystack_public_key');
delete_option('staydesk_api_key');

// Delete all plugin tables
$tables = array(
    $wpdb->prefix . 'staydesk_hotels',
    $wpdb->prefix . 'staydesk_rooms',
    $wpdb->prefix . 'staydesk_bookings',
    $wpdb->prefix . 'staydesk_guests',
    $wpdb->prefix . 'staydesk_transactions',
    $wpdb->prefix . 'staydesk_chat_logs',
    $wpdb->prefix . 'staydesk_subscriptions',
    $wpdb->prefix . 'staydesk_support_tickets'
);

foreach ($tables as $table) {
    $wpdb->query("DROP TABLE IF EXISTS $table");
}

// Delete all plugin pages
$plugin_pages = array(
    'staydesk-home',
    'staydesk-login',
    'staydesk-signup',
    'staydesk-dashboard',
    'staydesk-bookings',
    'staydesk-profile',
    'staydesk-pricing',
    'staydesk-admin'
);

foreach ($plugin_pages as $slug) {
    $page = get_page_by_path($slug);
    if ($page) {
        wp_delete_post($page->ID, true);
    }
}

// Delete all user meta related to the plugin
$wpdb->query("DELETE FROM {$wpdb->usermeta} WHERE meta_key LIKE 'staydesk_%'");

// Clear any scheduled cron jobs
wp_clear_scheduled_hook('staydesk_check_expired_subscriptions');

// Remove custom roles
remove_role('staydesk_hotel');

// Remove capabilities from administrator
$admin = get_role('administrator');
if ($admin) {
    $admin->remove_cap('manage_staydesk');
    $admin->remove_cap('manage_hotels');
    $admin->remove_cap('view_hotel_reports');
}
