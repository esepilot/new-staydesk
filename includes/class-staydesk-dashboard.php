<?php
/**
 * Dashboard functionality.
 *
 * @since      1.0.0
 * @package    Staydesk
 */
class Staydesk_Dashboard {

    /**
     * Initialize the class.
     */
    public function init() {
        add_shortcode('staydesk_dashboard', array($this, 'render_dashboard'));
        add_shortcode('staydesk_homepage', array($this, 'render_homepage'));
    }

    /**
     * Render hotel dashboard.
     */
    public function render_dashboard() {
        if (!is_user_logged_in()) {
            return '<p>Please <a href="' . home_url('/staydesk-login') . '">login</a> to access the dashboard.</p>';
        }

        ob_start();
        include STAYDESK_PLUGIN_DIR . 'templates/dashboard.php';
        return ob_get_clean();
    }

    /**
     * Render homepage.
     */
    public function render_homepage() {
        ob_start();
        include STAYDESK_PLUGIN_DIR . 'templates/homepage.php';
        return ob_get_clean();
    }

    /**
     * Get dashboard data.
     */
    public static function get_dashboard_data($hotel_id) {
        global $wpdb;

        $data = array();

        // Get total bookings
        $table_bookings = $wpdb->prefix . 'staydesk_bookings';
        $data['total_bookings'] = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_bookings WHERE hotel_id = %d",
            $hotel_id
        ));

        // Get pending bookings
        $data['pending_bookings'] = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_bookings WHERE hotel_id = %d AND booking_status = 'pending'",
            $hotel_id
        ));

        // Get total revenue
        $table_transactions = $wpdb->prefix . 'staydesk_transactions';
        $data['total_revenue'] = $wpdb->get_var($wpdb->prepare(
            "SELECT SUM(amount) FROM $table_transactions WHERE hotel_id = %d AND transaction_status = 'completed'",
            $hotel_id
        )) ?: 0;

        // Get total rooms
        $table_rooms = $wpdb->prefix . 'staydesk_rooms';
        $data['total_rooms'] = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_rooms WHERE hotel_id = %d",
            $hotel_id
        ));

        // Get available rooms
        $data['available_rooms'] = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_rooms WHERE hotel_id = %d AND availability_status = 'available'",
            $hotel_id
        ));

        // Get recent bookings
        $data['recent_bookings'] = $wpdb->get_results($wpdb->prepare(
            "SELECT b.*, g.guest_name, g.guest_email, r.room_name 
             FROM $table_bookings b
             LEFT JOIN {$wpdb->prefix}staydesk_guests g ON b.guest_id = g.id
             LEFT JOIN {$wpdb->prefix}staydesk_rooms r ON b.room_id = r.id
             WHERE b.hotel_id = %d
             ORDER BY b.created_at DESC
             LIMIT 10",
            $hotel_id
        ));

        // Get guest enquiries
        $table_chat_logs = $wpdb->prefix . 'staydesk_chat_logs';
        $data['enquiries_count'] = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(DISTINCT session_id) FROM $table_chat_logs WHERE hotel_id = %d",
            $hotel_id
        ));

        return $data;
    }
}
