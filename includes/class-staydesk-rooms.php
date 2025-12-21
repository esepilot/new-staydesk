<?php
/**
 * Rooms management functionality.
 *
 * @since      1.0.0
 * @package    Staydesk
 */
class Staydesk_Rooms {

    /**
     * Initialize the class.
     */
    public function __construct() {
        add_shortcode('staydesk_rooms', array($this, 'render_rooms'));
        
        // AJAX handlers
        add_action('wp_ajax_staydesk_add_room', array($this, 'add_room'));
        add_action('wp_ajax_staydesk_update_room', array($this, 'update_room'));
        add_action('wp_ajax_staydesk_update_room_status', array($this, 'update_room_status'));
        add_action('wp_ajax_staydesk_delete_room', array($this, 'delete_room'));
        add_action('wp_ajax_staydesk_get_available_rooms', array($this, 'get_available_rooms'));
    }
    
    /**
     * Render rooms page.
     */
    public function render_rooms() {
        ob_start();
        include STAYDESK_PLUGIN_DIR . 'templates/rooms.php';
        return ob_get_clean();
    }

    /**
     * Add a new room.
     */
    public function add_room() {
        check_ajax_referer('staydesk_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => 'Access denied.'));
        }

        global $wpdb;

        $user_id = get_current_user_id();
        $table_hotels = $wpdb->prefix . 'staydesk_hotels';
        $hotel = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_hotels WHERE user_id = %d",
            $user_id
        ));

        if (!$hotel) {
            wp_send_json_error(array('message' => 'Hotel not found.'));
        }

        // Sanitize input
        $room_name = sanitize_text_field($_POST['room_name']);
        $room_type = sanitize_text_field($_POST['room_type']);
        $room_description = sanitize_textarea_field($_POST['room_description']);
        $price_per_night = floatval($_POST['price_per_night']);
        $max_guests = intval($_POST['max_guests']);
        $amenities = sanitize_textarea_field($_POST['amenities']);

        // Create room
        $table_rooms = $wpdb->prefix . 'staydesk_rooms';
        $wpdb->insert($table_rooms, array(
            'hotel_id' => $hotel->id,
            'room_name' => $room_name,
            'room_type' => $room_type,
            'room_description' => $room_description,
            'price_per_night' => $price_per_night,
            'max_guests' => $max_guests,
            'amenities' => $amenities,
            'availability_status' => 'available'
        ));

        wp_send_json_success(array('message' => 'Room added successfully!'));
    }

    /**
     * Update room details.
     */
    public function update_room() {
        check_ajax_referer('staydesk_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => 'Access denied.'));
        }

        global $wpdb;

        $room_id = intval($_POST['room_id']);
        $room_name = sanitize_text_field($_POST['room_name']);
        $room_type = sanitize_text_field($_POST['room_type']);
        $room_description = sanitize_textarea_field($_POST['room_description']);
        $price_per_night = floatval($_POST['price_per_night']);
        $max_guests = intval($_POST['max_guests']);
        $amenities = sanitize_textarea_field($_POST['amenities']);
        $availability_status = sanitize_text_field($_POST['availability_status']);

        $table_rooms = $wpdb->prefix . 'staydesk_rooms';
        $wpdb->update(
            $table_rooms,
            array(
                'room_name' => $room_name,
                'room_type' => $room_type,
                'room_description' => $room_description,
                'price_per_night' => $price_per_night,
                'max_guests' => $max_guests,
                'amenities' => $amenities,
                'availability_status' => $availability_status
            ),
            array('id' => $room_id)
        );

        wp_send_json_success(array('message' => 'Room updated successfully!'));
    }
    
    /**
     * Update room status.
     */
    public function update_room_status() {
        check_ajax_referer('staydesk_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => 'Access denied.'));
        }

        global $wpdb;

        $room_id = intval($_POST['room_id']);
        $status = sanitize_text_field($_POST['status']);

        $table_rooms = $wpdb->prefix . 'staydesk_rooms';
        $wpdb->update(
            $table_rooms,
            array('availability_status' => $status),
            array('id' => $room_id)
        );

        wp_send_json_success(array('message' => 'Room status updated successfully!'));
    }

    /**
     * Delete a room.
     */
    public function delete_room() {
        check_ajax_referer('staydesk_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => 'Access denied.'));
        }

        global $wpdb;

        $room_id = intval($_POST['room_id']);

        $table_rooms = $wpdb->prefix . 'staydesk_rooms';
        $wpdb->delete($table_rooms, array('id' => $room_id));

        wp_send_json_success(array('message' => 'Room deleted successfully!'));
    }

    /**
     * Get available rooms.
     */
    public function get_available_rooms() {
        global $wpdb;

        $hotel_id = isset($_POST['hotel_id']) ? intval($_POST['hotel_id']) : 0;
        $check_in = isset($_POST['check_in']) ? sanitize_text_field($_POST['check_in']) : '';
        $check_out = isset($_POST['check_out']) ? sanitize_text_field($_POST['check_out']) : '';

        $table_rooms = $wpdb->prefix . 'staydesk_rooms';
        
        if ($hotel_id > 0) {
            $rooms = $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM $table_rooms WHERE hotel_id = %d AND availability_status = 'available'",
                $hotel_id
            ));
        } else {
            $rooms = $wpdb->get_results(
                "SELECT * FROM $table_rooms WHERE availability_status = 'available'"
            );
        }

        wp_send_json_success(array('rooms' => $rooms));
    }

    /**
     * Get hotel rooms.
     */
    public static function get_hotel_rooms($hotel_id) {
        global $wpdb;

        $table_rooms = $wpdb->prefix . 'staydesk_rooms';
        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_rooms WHERE hotel_id = %d ORDER BY created_at DESC",
            $hotel_id
        ));
    }
}

new Staydesk_Rooms();
