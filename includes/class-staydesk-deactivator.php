<?php
/**
 * Fired during plugin deactivation.
 *
 * @since      1.0.0
 * @package    Staydesk
 */
class Staydesk_Deactivator {

    /**
     * Deactivate the plugin.
     */
    public static function deactivate() {
        // Remove custom roles
        remove_role('staydesk_hotel');

        // Remove custom capabilities from administrator
        $admin = get_role('administrator');
        if ($admin) {
            $admin->remove_cap('manage_staydesk');
            $admin->remove_cap('manage_hotels');
            $admin->remove_cap('view_hotel_reports');
        }

        // Flush rewrite rules
        flush_rewrite_rules();
    }
}
