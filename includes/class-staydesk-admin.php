<?php
/**
 * Admin functionality for BendlessTech platform management.
 *
 * @since      1.0.0
 * @package    Staydesk
 */
class Staydesk_Admin {

    private $plugin_name;
    private $version;

    /**
     * Initialize the class.
     */
    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;

        add_shortcode('staydesk_admin_dashboard', array($this, 'render_admin_dashboard'));
    }

    /**
     * Enqueue admin styles.
     */
    public function enqueue_styles() {
        wp_enqueue_style($this->plugin_name, STAYDESK_PLUGIN_URL . 'admin/css/staydesk-admin.css', array(), $this->version, 'all');
    }

    /**
     * Enqueue admin scripts.
     */
    public function enqueue_scripts() {
        wp_enqueue_script($this->plugin_name, STAYDESK_PLUGIN_URL . 'admin/js/staydesk-admin.js', array('jquery'), $this->version, false);
    }

    /**
     * Render admin dashboard.
     */
    public function render_admin_dashboard() {
        if (!current_user_can('manage_options')) {
            return '<p>Access denied.</p>';
        }

        ob_start();
        include STAYDESK_PLUGIN_DIR . 'templates/admin-dashboard.php';
        return ob_get_clean();
    }

    /**
     * Get admin dashboard data.
     */
    public static function get_admin_data() {
        global $wpdb;

        $data = array();

        // Get total hotels
        $table_hotels = $wpdb->prefix . 'staydesk_hotels';
        $data['total_hotels'] = $wpdb->get_var("SELECT COUNT(*) FROM $table_hotels");

        // Get active subscriptions
        $data['active_subscriptions'] = $wpdb->get_var(
            "SELECT COUNT(*) FROM $table_hotels WHERE subscription_status = 'active'"
        );

        // Get total bookings
        $table_bookings = $wpdb->prefix . 'staydesk_bookings';
        $data['total_bookings'] = $wpdb->get_var("SELECT COUNT(*) FROM $table_bookings");

        // Get total revenue
        $table_transactions = $wpdb->prefix . 'staydesk_transactions';
        $data['total_revenue'] = $wpdb->get_var(
            "SELECT SUM(amount) FROM $table_transactions WHERE transaction_status = 'completed'"
        ) ?: 0;

        // Get recent hotels
        $data['recent_hotels'] = $wpdb->get_results(
            "SELECT * FROM $table_hotels ORDER BY created_at DESC LIMIT 10"
        );

        // Get support tickets
        $table_support = $wpdb->prefix . 'staydesk_support_tickets';
        $data['open_tickets'] = $wpdb->get_var(
            "SELECT COUNT(*) FROM $table_support WHERE status = 'open'"
        );

        return $data;
    }
}
