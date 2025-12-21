<?php
/**
 * Role-Based Access Control (RBAC) system for StayDesk.
 *
 * @since      1.0.0
 * @package    Staydesk
 */
class Staydesk_Roles {

    // Role constants
    const ROLE_SUPER_ADMIN = 'super_admin';
    const ROLE_ADMIN = 'admin';
    const ROLE_HOTEL_MANAGER = 'hotel_manager';
    const ROLE_HOTEL_STAFF = 'hotel_staff';
    const ROLE_GUEST = 'guest';

    /**
     * Initialize the class.
     */
    public function __construct() {
        // AJAX handlers for role management
        add_action('wp_ajax_staydesk_assign_role', array($this, 'assign_role'));
        add_action('wp_ajax_staydesk_get_user_role', array($this, 'get_user_role_ajax'));
    }

    /**
     * Get user's role in StayDesk system.
     */
    public static function get_user_role($user_id = null) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }

        global $wpdb;
        $table_roles = $wpdb->prefix . 'staydesk_roles';
        
        $role = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_roles WHERE user_id = %d ORDER BY id DESC LIMIT 1",
            $user_id
        ));

        return $role ? $role->role_name : self::ROLE_GUEST;
    }

    /**
     * Check if user has capability.
     */
    public static function user_can($capability, $user_id = null) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }

        $role = self::get_user_role($user_id);
        $capabilities = self::get_role_capabilities($role);

        return in_array($capability, $capabilities);
    }

    /**
     * Get capabilities for a role.
     */
    public static function get_role_capabilities($role) {
        $capabilities = array();

        switch ($role) {
            case self::ROLE_SUPER_ADMIN:
                $capabilities = array(
                    'manage_hotels',
                    'add_hotel',
                    'edit_hotel',
                    'delete_hotel',
                    'view_all_hotels',
                    'manage_bookings',
                    'add_booking',
                    'edit_booking',
                    'delete_booking',
                    'view_all_bookings',
                    'change_booking_status',
                    'manage_users',
                    'manage_roles',
                    'configure_settings',
                    'view_analytics',
                    'export_data',
                    'manage_subscriptions',
                    'refund_payments'
                );
                break;

            case self::ROLE_ADMIN:
                $capabilities = array(
                    'view_all_hotels',
                    'add_hotel',
                    'edit_hotel',
                    'manage_bookings',
                    'add_booking',
                    'edit_booking',
                    'delete_booking',
                    'view_all_bookings',
                    'change_booking_status',
                    'view_analytics',
                    'moderate_support'
                );
                break;

            case self::ROLE_HOTEL_MANAGER:
                $capabilities = array(
                    'manage_own_hotel',
                    'edit_own_hotel',
                    'manage_own_rooms',
                    'add_room',
                    'edit_room',
                    'delete_room',
                    'manage_own_bookings',
                    'add_booking',
                    'edit_own_booking',
                    'delete_own_booking',
                    'view_own_bookings',
                    'change_booking_status',
                    'view_own_analytics',
                    'manage_staff'
                );
                break;

            case self::ROLE_HOTEL_STAFF:
                $capabilities = array(
                    'view_hotel_bookings',
                    'add_booking',
                    'change_booking_status',
                    'view_rooms'
                );
                break;

            case self::ROLE_GUEST:
                $capabilities = array(
                    'view_own_bookings',
                    'make_booking'
                );
                break;
        }

        return $capabilities;
    }

    /**
     * Assign role to user.
     */
    public function assign_role() {
        check_ajax_referer('staydesk_nonce', 'nonce');

        if (!self::user_can('manage_roles')) {
            wp_send_json_error(array('message' => 'Access denied'));
            return;
        }

        $user_id = intval($_POST['user_id']);
        $role_name = sanitize_text_field($_POST['role_name']);
        $hotel_id = isset($_POST['hotel_id']) ? intval($_POST['hotel_id']) : null;

        global $wpdb;
        $table_roles = $wpdb->prefix . 'staydesk_roles';

        $result = $wpdb->insert(
            $table_roles,
            array(
                'user_id' => $user_id,
                'role_name' => $role_name,
                'hotel_id' => $hotel_id,
                'assigned_by' => get_current_user_id(),
                'capabilities' => json_encode(self::get_role_capabilities($role_name))
            ),
            array('%d', '%s', '%d', '%d', '%s')
        );

        if ($result) {
            wp_send_json_success(array('message' => 'Role assigned successfully'));
        } else {
            wp_send_json_error(array('message' => 'Failed to assign role'));
        }
    }

    /**
     * Get user role via AJAX.
     */
    public function get_user_role_ajax() {
        check_ajax_referer('staydesk_nonce', 'nonce');

        $user_id = intval($_POST['user_id']);
        $role = self::get_user_role($user_id);
        
        wp_send_json_success(array('role' => $role));
    }

    /**
     * Get hotel ID for user (if they have hotel association).
     */
    public static function get_user_hotel_id($user_id = null) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }

        global $wpdb;
        $table_hotels = $wpdb->prefix . 'staydesk_hotels';
        
        $hotel = $wpdb->get_row($wpdb->prepare(
            "SELECT id FROM $table_hotels WHERE user_id = %d",
            $user_id
        ));

        return $hotel ? $hotel->id : null;
    }

    /**
     * Check if user can access hotel.
     */
    public static function can_access_hotel($hotel_id, $user_id = null) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }

        // Super admin and admin can access all hotels
        if (self::user_can('view_all_hotels', $user_id)) {
            return true;
        }

        // Hotel managers/staff can only access their own hotel
        $user_hotel_id = self::get_user_hotel_id($user_id);
        return $user_hotel_id == $hotel_id;
    }

    /**
     * Check if user can access booking.
     */
    public static function can_access_booking($booking_id, $user_id = null) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }

        // Super admin and admin can access all bookings
        if (self::user_can('view_all_bookings', $user_id)) {
            return true;
        }

        global $wpdb;
        $table_bookings = $wpdb->prefix . 'staydesk_bookings';
        
        $booking = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_bookings WHERE id = %d",
            $booking_id
        ));

        if (!$booking) {
            return false;
        }

        // Hotel users can access bookings for their hotel
        if (self::user_can('view_hotel_bookings', $user_id) || self::user_can('view_own_bookings', $user_id)) {
            $user_hotel_id = self::get_user_hotel_id($user_id);
            if ($user_hotel_id == $booking->hotel_id) {
                return true;
            }

            // Guests can view their own bookings
            if ($booking->guest_id == $user_id) {
                return true;
            }
        }

        return false;
    }
}
