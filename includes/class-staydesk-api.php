<?php
/**
 * REST API endpoints for chatbot and external integrations.
 *
 * @since      1.0.0
 * @package    Staydesk
 */
class Staydesk_API {

    /**
     * Register REST API routes.
     */
    public function register_routes() {
        register_rest_route('staydesk/v1', '/hotels', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_hotels'),
            'permission_callback' => '__return_true'
        ));

        register_rest_route('staydesk/v1', '/hotel/(?P<id>\d+)', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_hotel'),
            'permission_callback' => '__return_true'
        ));

        register_rest_route('staydesk/v1', '/rooms', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_rooms'),
            'permission_callback' => '__return_true'
        ));

        register_rest_route('staydesk/v1', '/bookings', array(
            'methods' => 'POST',
            'callback' => array($this, 'create_booking'),
            'permission_callback' => array($this, 'verify_api_key')
        ));

        register_rest_route('staydesk/v1', '/chatbot', array(
            'methods' => 'POST',
            'callback' => array($this, 'chatbot_endpoint'),
            'permission_callback' => '__return_true'
        ));

        register_rest_route('staydesk/v1', '/widget-config/(?P<hotel_id>\d+)', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_widget_config'),
            'permission_callback' => '__return_true'
        ));
    }

    /**
     * Get all hotels.
     */
    public function get_hotels($request) {
        global $wpdb;

        $table_hotels = $wpdb->prefix . 'staydesk_hotels';
        $hotels = $wpdb->get_results(
            "SELECT id, hotel_name, hotel_email, hotel_phone, hotel_address, hotel_description 
             FROM $table_hotels 
             WHERE subscription_status = 'active' 
             AND email_confirmed = 1"
        );

        return rest_ensure_response(array(
            'success' => true,
            'data' => $hotels
        ));
    }

    /**
     * Get single hotel.
     */
    public function get_hotel($request) {
        global $wpdb;

        $hotel_id = $request['id'];

        $table_hotels = $wpdb->prefix . 'staydesk_hotels';
        $hotel = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_hotels WHERE id = %d",
            $hotel_id
        ));

        if (!$hotel) {
            return new WP_Error('not_found', 'Hotel not found', array('status' => 404));
        }

        // Get hotel rooms
        $rooms = Staydesk_Rooms::get_hotel_rooms($hotel_id);

        return rest_ensure_response(array(
            'success' => true,
            'data' => array(
                'hotel' => $hotel,
                'rooms' => $rooms
            )
        ));
    }

    /**
     * Get rooms.
     */
    public function get_rooms($request) {
        global $wpdb;

        $hotel_id = isset($request['hotel_id']) ? intval($request['hotel_id']) : 0;

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

        return rest_ensure_response(array(
            'success' => true,
            'data' => $rooms
        ));
    }

    /**
     * Create booking via API.
     */
    public function create_booking($request) {
        global $wpdb;

        $params = $request->get_json_params();

        // Validate required fields
        $required = array('room_id', 'check_in_date', 'check_out_date', 'guest_name', 'guest_email', 'guest_phone');
        foreach ($required as $field) {
            if (empty($params[$field])) {
                return new WP_Error('missing_field', "Field {$field} is required", array('status' => 400));
            }
        }

        // Create guest
        $table_guests = $wpdb->prefix . 'staydesk_guests';
        $wpdb->insert($table_guests, array(
            'guest_name' => sanitize_text_field($params['guest_name']),
            'guest_email' => sanitize_email($params['guest_email']),
            'guest_phone' => sanitize_text_field($params['guest_phone'])
        ));
        $guest_id = $wpdb->insert_id;

        // Get room details
        $table_rooms = $wpdb->prefix . 'staydesk_rooms';
        $room = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_rooms WHERE id = %d",
            intval($params['room_id'])
        ));

        if (!$room) {
            return new WP_Error('not_found', 'Room not found', array('status' => 404));
        }

        // Calculate total
        $check_in = new DateTime($params['check_in_date']);
        $check_out = new DateTime($params['check_out_date']);
        $nights = $check_in->diff($check_out)->days;
        $total = $room->price_per_night * $nights;

        // Create booking
        $reference = 'BK' . strtoupper(substr(uniqid(), -8));
        
        $table_bookings = $wpdb->prefix . 'staydesk_bookings';
        $wpdb->insert($table_bookings, array(
            'booking_reference' => $reference,
            'hotel_id' => $room->hotel_id,
            'room_id' => $room->id,
            'guest_id' => $guest_id,
            'check_in_date' => $params['check_in_date'],
            'check_out_date' => $params['check_out_date'],
            'num_guests' => isset($params['num_guests']) ? intval($params['num_guests']) : 1,
            'total_amount' => $total,
            'booking_status' => 'pending',
            'payment_status' => 'pending'
        ));

        $booking_id = $wpdb->insert_id;

        return rest_ensure_response(array(
            'success' => true,
            'message' => 'Booking created successfully',
            'data' => array(
                'booking_id' => $booking_id,
                'booking_reference' => $reference,
                'total_amount' => $total
            )
        ));
    }

    /**
     * Chatbot API endpoint.
     */
    public function chatbot_endpoint($request) {
        $params = $request->get_json_params();

        $hotel_id = isset($params['hotel_id']) ? intval($params['hotel_id']) : 0;
        $session_id = isset($params['session_id']) ? sanitize_text_field($params['session_id']) : 'session_' . uniqid();
        $message = isset($params['message']) ? sanitize_text_field($params['message']) : '';
        $language = isset($params['language']) ? sanitize_text_field($params['language']) : 'en';

        if (empty($message)) {
            return new WP_Error('missing_message', 'Message is required', array('status' => 400));
        }

        // Use chatbot class to process
        $chatbot = new Staydesk_Chatbot();
        
        // Simulate AJAX post data
        $_POST['hotel_id'] = $hotel_id;
        $_POST['session_id'] = $session_id;
        $_POST['message'] = $message;
        $_POST['language'] = $language;

        // Process through chatbot (but we need to return directly)
        // For API, we'll create a direct method
        
        return rest_ensure_response(array(
            'success' => true,
            'session_id' => $session_id,
            'response' => 'This endpoint processes chatbot messages'
        ));
    }

    /**
     * Get widget configuration for a hotel.
     */
    public function get_widget_config($request) {
        global $wpdb;

        $hotel_id = $request['hotel_id'];

        $table_hotels = $wpdb->prefix . 'staydesk_hotels';
        $hotel = $wpdb->get_row($wpdb->prepare(
            "SELECT id, hotel_name, subscription_status FROM $table_hotels WHERE id = %d",
            $hotel_id
        ));

        if (!$hotel) {
            return new WP_Error('not_found', 'Hotel not found', array('status' => 404));
        }

        $config = array(
            'hotel_id' => $hotel->id,
            'hotel_name' => $hotel->hotel_name,
            'enabled' => $hotel->subscription_status === 'active',
            'api_url' => rest_url('staydesk/v1/chatbot'),
            'languages' => array('en', 'pidgin'),
            'whatsapp_fallback' => '2347120018023'
        );

        return rest_ensure_response(array(
            'success' => true,
            'data' => $config
        ));
    }

    /**
     * Verify API key.
     */
    public function verify_api_key($request) {
        $api_key = $request->get_header('X-API-Key');
        
        if (empty($api_key)) {
            return false;
        }

        // Verify API key against stored keys
        $valid_key = get_option('staydesk_api_key', '');
        
        return $api_key === $valid_key;
    }
}
