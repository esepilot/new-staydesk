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
        // Flush rewrite rules
        flush_rewrite_rules();
    }
}
