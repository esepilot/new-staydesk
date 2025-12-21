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
        
        // AJAX handlers for hotel management
        add_action('wp_ajax_staydesk_admin_get_hotels', array($this, 'get_hotels'));
        add_action('wp_ajax_staydesk_admin_add_hotel', array($this, 'add_hotel'));
        add_action('wp_ajax_staydesk_admin_edit_hotel', array($this, 'edit_hotel'));
        add_action('wp_ajax_staydesk_admin_delete_hotel', array($this, 'delete_hotel'));
        
        // AJAX handlers for booking management
        add_action('wp_ajax_staydesk_admin_get_bookings', array($this, 'get_bookings'));
        add_action('wp_ajax_staydesk_admin_add_booking', array($this, 'add_booking'));
        add_action('wp_ajax_staydesk_admin_edit_booking', array($this, 'edit_booking'));
        add_action('wp_ajax_staydesk_admin_delete_booking', array($this, 'delete_booking'));
        add_action('wp_ajax_staydesk_admin_change_booking_status', array($this, 'change_booking_status'));
        
        // AJAX handlers for stats and roles
        add_action('wp_ajax_staydesk_admin_get_stats', array($this, 'get_stats'));
        add_action('wp_ajax_staydesk_admin_assign_role', array($this, 'assign_role'));
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
    
    // Hotel Management AJAX Handlers
    
    public function get_hotels() {
        check_ajax_referer('staydesk_nonce', 'nonce');
        
        if (!Staydesk_Roles::can('manage_hotels')) {
            wp_send_json_error('Insufficient permissions');
        }
        
        global $wpdb;
        $hotels_table = $wpdb->prefix . 'staydesk_hotels';
        
        $hotels = $wpdb->get_results("SELECT * FROM $hotels_table ORDER BY created_at DESC");
        
        wp_send_json_success($hotels);
    }
    
    public function add_hotel() {
        check_ajax_referer('staydesk_nonce', 'nonce');
        
        if (!Staydesk_Roles::can('manage_hotels')) {
            wp_send_json_error('Insufficient permissions');
        }
        
        global $wpdb;
        $hotels_table = $wpdb->prefix . 'staydesk_hotels';
        
        $password = wp_generate_password(12, true, true);
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        
        $hotel_data = array(
            'hotel_name' => sanitize_text_field($_POST['hotel_name']),
            'email' => sanitize_email($_POST['email']),
            'phone' => sanitize_text_field($_POST['phone']),
            'address' => sanitize_text_field($_POST['address']),
            'city' => sanitize_text_field($_POST['city']),
            'state' => sanitize_text_field($_POST['state']),
            'password_hash' => $password_hash,
            'subscription_plan' => sanitize_text_field($_POST['subscription_plan']),
            'subscription_status' => 'active',
            'subscription_expires_at' => date('Y-m-d H:i:s', strtotime('+1 year')),
            'email_confirmed' => 1,
            'created_at' => current_time('mysql')
        );
        
        $wpdb->insert($hotels_table, $hotel_data);
        
        wp_send_json_success(array(
            'message' => 'Hotel added successfully',
            'hotel_id' => $wpdb->insert_id,
            'temp_password' => $password
        ));
    }
    
    public function edit_hotel() {
        check_ajax_referer('staydesk_nonce', 'nonce');
        
        if (!Staydesk_Roles::can('manage_hotels')) {
            wp_send_json_error('Insufficient permissions');
        }
        
        global $wpdb;
        $hotels_table = $wpdb->prefix . 'staydesk_hotels';
        
        $hotel_id = intval($_POST['hotel_id']);
        
        $hotel_data = array(
            'hotel_name' => sanitize_text_field($_POST['hotel_name']),
            'phone' => sanitize_text_field($_POST['phone']),
            'address' => sanitize_text_field($_POST['address']),
            'city' => sanitize_text_field($_POST['city']),
            'state' => sanitize_text_field($_POST['state']),
            'subscription_plan' => sanitize_text_field($_POST['subscription_plan'])
        );
        
        $wpdb->update($hotels_table, $hotel_data, array('id' => $hotel_id));
        
        wp_send_json_success('Hotel updated successfully');
    }
    
    public function delete_hotel() {
        check_ajax_referer('staydesk_nonce', 'nonce');
        
        if (!Staydesk_Roles::can('manage_hotels')) {
            wp_send_json_error('Insufficient permissions');
        }
        
        global $wpdb;
        $hotels_table = $wpdb->prefix . 'staydesk_hotels';
        $hotel_id = intval($_POST['hotel_id']);
        
        $wpdb->delete($hotels_table, array('id' => $hotel_id));
        
        wp_send_json_success('Hotel deleted successfully');
    }
    
    // Booking Management AJAX Handlers
    
    public function get_bookings() {
        check_ajax_referer('staydesk_nonce', 'nonce');
        
        global $wpdb;
        $bookings_table = $wpdb->prefix . 'staydesk_bookings';
        $hotels_table = $wpdb->prefix . 'staydesk_hotels';
        
        $user_id = get_current_user_id();
        
        if (Staydesk_Roles::is_admin($user_id)) {
            // Admin sees all bookings
            $bookings = $wpdb->get_results("
                SELECT b.*, h.hotel_name 
                FROM $bookings_table b
                LEFT JOIN $hotels_table h ON b.hotel_id = h.id
                ORDER BY b.created_at DESC
            ");
        } else {
            // Hotel users see only their bookings
            $hotel_id = Staydesk_Roles::get_user_hotel_id($user_id);
            $bookings = $wpdb->get_results($wpdb->prepare("
                SELECT b.*, h.hotel_name 
                FROM $bookings_table b
                LEFT JOIN $hotels_table h ON b.hotel_id = h.id
                WHERE b.hotel_id = %d
                ORDER BY b.created_at DESC
            ", $hotel_id));
        }
        
        wp_send_json_success($bookings);
    }
    
    public function add_booking() {
        check_ajax_referer('staydesk_nonce', 'nonce');
        
        if (!Staydesk_Roles::can('manage_bookings')) {
            wp_send_json_error('Insufficient permissions');
        }
        
        global $wpdb;
        $bookings_table = $wpdb->prefix . 'staydesk_bookings';
        
        $booking_data = array(
            'hotel_id' => intval($_POST['hotel_id']),
            'guest_name' => sanitize_text_field($_POST['guest_name']),
            'guest_email' => sanitize_email($_POST['guest_email']),
            'guest_phone' => sanitize_text_field($_POST['guest_phone']),
            'room_number' => sanitize_text_field($_POST['room_number']),
            'check_in' => sanitize_text_field($_POST['check_in']),
            'check_out' => sanitize_text_field($_POST['check_out']),
            'guests' => intval($_POST['guests']),
            'amount' => floatval($_POST['amount']),
            'special_requests' => sanitize_textarea_field($_POST['special_requests']),
            'status' => sanitize_text_field($_POST['status']),
            'booking_reference' => 'BK' . strtoupper(substr(md5(time() . rand()), 0, 10)),
            'created_at' => current_time('mysql')
        );
        
        $wpdb->insert($bookings_table, $booking_data);
        
        wp_send_json_success(array(
            'message' => 'Booking added successfully',
            'booking_id' => $wpdb->insert_id,
            'reference' => $booking_data['booking_reference']
        ));
    }
    
    public function edit_booking() {
        check_ajax_referer('staydesk_nonce', 'nonce');
        
        $booking_id = intval($_POST['booking_id']);
        
        if (!Staydesk_Roles::can_edit_booking(get_current_user_id(), $booking_id)) {
            wp_send_json_error('Insufficient permissions');
        }
        
        global $wpdb;
        $bookings_table = $wpdb->prefix . 'staydesk_bookings';
        
        $booking_data = array(
            'guest_name' => sanitize_text_field($_POST['guest_name']),
            'guest_phone' => sanitize_text_field($_POST['guest_phone']),
            'check_in' => sanitize_text_field($_POST['check_in']),
            'check_out' => sanitize_text_field($_POST['check_out']),
            'guests' => intval($_POST['guests']),
            'special_requests' => sanitize_textarea_field($_POST['special_requests'])
        );
        
        $wpdb->update($bookings_table, $booking_data, array('id' => $booking_id));
        
        wp_send_json_success('Booking updated successfully');
    }
    
    public function delete_booking() {
        check_ajax_referer('staydesk_nonce', 'nonce');
        
        $booking_id = intval($_POST['booking_id']);
        
        if (!Staydesk_Roles::can_delete_booking(get_current_user_id(), $booking_id)) {
            wp_send_json_error('Insufficient permissions');
        }
        
        global $wpdb;
        $bookings_table = $wpdb->prefix . 'staydesk_bookings';
        
        $wpdb->delete($bookings_table, array('id' => $booking_id));
        
        wp_send_json_success('Booking deleted successfully');
    }
    
    public function change_booking_status() {
        check_ajax_referer('staydesk_nonce', 'nonce');
        
        $booking_id = intval($_POST['booking_id']);
        $new_status = sanitize_text_field($_POST['status']);
        $user_id = get_current_user_id();
        
        if (!Staydesk_Roles::can_change_booking_status($user_id, $booking_id, $new_status)) {
            wp_send_json_error('Insufficient permissions to change to this status');
        }
        
        global $wpdb;
        $bookings_table = $wpdb->prefix . 'staydesk_bookings';
        
        $wpdb->update($bookings_table, array('status' => $new_status), array('id' => $booking_id));
        
        wp_send_json_success('Booking status updated successfully');
    }
    
    // Stats and Role Management
    
    public function get_stats() {
        check_ajax_referer('staydesk_nonce', 'nonce');
        
        if (!Staydesk_Roles::can('view_dashboard')) {
            wp_send_json_error('Insufficient permissions');
        }
        
        global $wpdb;
        $hotels_table = $wpdb->prefix . 'staydesk_hotels';
        $bookings_table = $wpdb->prefix . 'staydesk_bookings';
        
        $stats = array(
            'total_hotels' => $wpdb->get_var("SELECT COUNT(*) FROM $hotels_table"),
            'active_subscriptions' => $wpdb->get_var("SELECT COUNT(*) FROM $hotels_table WHERE subscription_status = 'active'"),
            'total_bookings' => $wpdb->get_var("SELECT COUNT(*) FROM $bookings_table"),
            'total_revenue' => $wpdb->get_var("SELECT SUM(amount) FROM $bookings_table WHERE status IN ('confirmed', 'completed')") ?: 0,
            'pending_bookings' => $wpdb->get_var("SELECT COUNT(*) FROM $bookings_table WHERE status = 'pending'")
        );
        
        wp_send_json_success($stats);
    }
    
    public function assign_role() {
        check_ajax_referer('staydesk_nonce', 'nonce');
        
        if (!Staydesk_Roles::can('manage_roles')) {
            wp_send_json_error('Insufficient permissions');
        }
        
        $user_id = intval($_POST['user_id']);
        $role_name = sanitize_text_field($_POST['role_name']);
        $hotel_id = isset($_POST['hotel_id']) ? intval($_POST['hotel_id']) : null;
        
        Staydesk_Roles::assign_role($user_id, $role_name, $hotel_id);
        
        wp_send_json_success('Role assigned successfully');
    }
}
