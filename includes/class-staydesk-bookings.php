<?php
/**
 * Bookings management functionality.
 *
 * @since      1.0.0
 * @package    Staydesk
 */
class Staydesk_Bookings {

    /**
     * Initialize the class.
     */
    public function __construct() {
        add_shortcode('staydesk_bookings', array($this, 'render_bookings'));
        
        // AJAX handlers
        add_action('wp_ajax_staydesk_create_booking', array($this, 'create_booking'));
        add_action('wp_ajax_staydesk_update_booking_status', array($this, 'update_booking_status'));
        add_action('wp_ajax_staydesk_cancel_booking', array($this, 'cancel_booking'));
        add_action('wp_ajax_staydesk_edit_booking', array($this, 'edit_booking'));
        add_action('wp_ajax_staydesk_delete_booking', array($this, 'delete_booking'));
        add_action('wp_ajax_staydesk_get_booking', array($this, 'get_booking'));
    }

    /**
     * Render bookings page.
     */
    public function render_bookings() {
        if (!is_user_logged_in()) {
            return '<p>Please login to view bookings.</p>';
        }

        ob_start();
        include STAYDESK_PLUGIN_DIR . 'templates/bookings.php';
        return ob_get_clean();
    }

    /**
     * Create a new booking.
     */
    public function create_booking() {
        check_ajax_referer('staydesk_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => 'Please login to create a booking.'));
        }

        global $wpdb;

        // Sanitize input
        $room_id = intval($_POST['room_id']);
        $check_in = sanitize_text_field($_POST['check_in_date']);
        $check_out = sanitize_text_field($_POST['check_out_date']);
        $num_guests = intval($_POST['num_guests']);
        $guest_name = sanitize_text_field($_POST['guest_name']);
        $guest_email = sanitize_email($_POST['guest_email']);
        $guest_phone = sanitize_text_field($_POST['guest_phone']);
        $special_requests = sanitize_textarea_field($_POST['special_requests']);

        // Get room details
        $table_rooms = $wpdb->prefix . 'staydesk_rooms';
        $room = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_rooms WHERE id = %d",
            $room_id
        ));

        if (!$room) {
            wp_send_json_error(array('message' => 'Room not found.'));
        }

        // Calculate total amount
        $check_in_date = new DateTime($check_in);
        $check_out_date = new DateTime($check_out);
        $nights = $check_in_date->diff($check_out_date)->days;
        $total_amount = $room->price_per_night * $nights;

        // Create or get guest
        $table_guests = $wpdb->prefix . 'staydesk_guests';
        $guest = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_guests WHERE guest_email = %s",
            $guest_email
        ));

        if (!$guest) {
            $wpdb->insert($table_guests, array(
                'guest_name' => $guest_name,
                'guest_email' => $guest_email,
                'guest_phone' => $guest_phone
            ));
            $guest_id = $wpdb->insert_id;
        } else {
            $guest_id = $guest->id;
        }

        // Generate booking reference
        $booking_reference = 'BK' . strtoupper(substr(uniqid(), -8));

        // Create booking
        $table_bookings = $wpdb->prefix . 'staydesk_bookings';
        $wpdb->insert($table_bookings, array(
            'booking_reference' => $booking_reference,
            'hotel_id' => $room->hotel_id,
            'room_id' => $room_id,
            'guest_id' => $guest_id,
            'check_in_date' => $check_in,
            'check_out_date' => $check_out,
            'num_guests' => $num_guests,
            'total_amount' => $total_amount,
            'booking_status' => 'pending',
            'payment_status' => 'pending',
            'special_requests' => $special_requests
        ));

        $booking_id = $wpdb->insert_id;

        // Send confirmation emails
        $notifications = new Staydesk_Notifications();
        $notifications->send_booking_confirmation($booking_id);

        wp_send_json_success(array(
            'message' => 'Booking created successfully!',
            'booking_reference' => $booking_reference,
            'booking_id' => $booking_id
        ));
    }

    /**
     * Update booking status.
     */
    public function update_booking_status() {
        check_ajax_referer('staydesk_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => 'Access denied.'));
        }

        global $wpdb;

        $booking_id = intval($_POST['booking_id']);
        $status = sanitize_text_field($_POST['status']);

        $table_bookings = $wpdb->prefix . 'staydesk_bookings';
        $wpdb->update(
            $table_bookings,
            array('booking_status' => $status),
            array('id' => $booking_id)
        );

        wp_send_json_success(array('message' => 'Booking status updated.'));
    }

    /**
     * Cancel booking.
     */
    public function cancel_booking() {
        check_ajax_referer('staydesk_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => 'Access denied.'));
        }

        global $wpdb;

        $booking_id = intval($_POST['booking_id']);

        $table_bookings = $wpdb->prefix . 'staydesk_bookings';
        $wpdb->update(
            $table_bookings,
            array('booking_status' => 'cancelled'),
            array('id' => $booking_id)
        );

        wp_send_json_success(array('message' => 'Booking cancelled.'));
    }

    /**
     * Get booking by reference.
     */
    public static function get_booking_by_reference($reference) {
        global $wpdb;

        $table_bookings = $wpdb->prefix . 'staydesk_bookings';
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_bookings WHERE booking_reference = %s",
            $reference
        ));
    }

    /**
     * Get booking details by ID (AJAX).
     */
    public function get_booking() {
        check_ajax_referer('staydesk_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => 'Access denied.'));
        }

        global $wpdb;
        $booking_id = intval($_POST['booking_id']);

        $table_bookings = $wpdb->prefix . 'staydesk_bookings';
        $table_rooms = $wpdb->prefix . 'staydesk_rooms';
        $table_guests = $wpdb->prefix . 'staydesk_guests';

        $booking = $wpdb->get_row($wpdb->prepare("
            SELECT b.*, r.room_name, g.guest_name, g.guest_email, g.guest_phone
            FROM $table_bookings b
            LEFT JOIN $table_rooms r ON b.room_id = r.id
            LEFT JOIN $table_guests g ON b.guest_id = g.id
            WHERE b.id = %d
        ", $booking_id));

        if (!$booking) {
            wp_send_json_error(array('message' => 'Booking not found.'));
        }

        wp_send_json_success($booking);
    }

    /**
     * Edit booking.
     */
    public function edit_booking() {
        check_ajax_referer('staydesk_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => 'Access denied.'));
        }

        global $wpdb;

        $booking_id = intval($_POST['booking_id']);
        $check_in = sanitize_text_field($_POST['check_in_date']);
        $check_out = sanitize_text_field($_POST['check_out_date']);
        $num_guests = intval($_POST['num_guests']);
        $special_requests = sanitize_textarea_field($_POST['special_requests']);

        // Update booking
        $table_bookings = $wpdb->prefix . 'staydesk_bookings';
        $wpdb->update(
            $table_bookings,
            array(
                'check_in_date' => $check_in,
                'check_out_date' => $check_out,
                'num_guests' => $num_guests,
                'special_requests' => $special_requests
            ),
            array('id' => $booking_id)
        );

        wp_send_json_success(array('message' => 'Booking updated successfully.'));
    }

    /**
     * Delete booking.
     */
    public function delete_booking() {
        check_ajax_referer('staydesk_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => 'Access denied.'));
        }

        global $wpdb;
        $booking_id = intval($_POST['booking_id']);

        $table_bookings = $wpdb->prefix . 'staydesk_bookings';
        $wpdb->delete($table_bookings, array('id' => $booking_id));

        wp_send_json_success(array('message' => 'Booking deleted successfully.'));
    }
}

new Staydesk_Bookings();
