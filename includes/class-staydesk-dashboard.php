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
        add_shortcode('staydesk_profile', array($this, 'render_profile'));
        add_shortcode('staydesk_bookings', array($this, 'render_bookings'));
        add_shortcode('staydesk_hotel_info', array($this, 'render_hotel_info'));
        
        // AJAX handlers
        add_action('wp_ajax_staydesk_update_profile', array($this, 'update_profile'));
        add_action('wp_ajax_staydesk_save_hotel_info', array($this, 'save_hotel_info'));
        add_action('wp_ajax_nopriv_staydesk_save_hotel_info', array($this, 'save_hotel_info'));
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
     * Render profile page.
     */
    public function render_profile() {
        ob_start();
        include STAYDESK_PLUGIN_DIR . 'templates/profile.php';
        return ob_get_clean();
    }
    
    /**
     * Render bookings page.
     */
    public function render_bookings() {
        ob_start();
        include STAYDESK_PLUGIN_DIR . 'templates/bookings.php';
        return ob_get_clean();
    }
    
    /**
     * Render hotel info page.
     */
    public function render_hotel_info() {
        if (!is_user_logged_in()) {
            return '<p>Please <a href="' . home_url('/staydesk-login') . '">login</a> to access this page.</p>';
        }
        
        ob_start();
        include STAYDESK_PLUGIN_DIR . 'templates/hotel-info.php';
        return ob_get_clean();
    }
    
    /**
     * Update hotel profile.
     */
    public function update_profile() {
        check_ajax_referer('staydesk_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => 'Access denied.'));
        }

        global $wpdb;

        $hotel_id = intval($_POST['hotel_id']);
        $hotel_name = sanitize_text_field($_POST['hotel_name']);
        $contact_email = sanitize_email($_POST['contact_email']);
        $phone_number = sanitize_text_field($_POST['phone_number']);
        $address = sanitize_text_field($_POST['address'] ?? '');
        $city = sanitize_text_field($_POST['city'] ?? '');
        $state = sanitize_text_field($_POST['state'] ?? '');
        $description = sanitize_textarea_field($_POST['description'] ?? '');

        $table_hotels = $wpdb->prefix . 'staydesk_hotels';
        $wpdb->update(
            $table_hotels,
            array(
                'hotel_name' => $hotel_name,
                'contact_email' => $contact_email,
                'phone_number' => $phone_number,
                'address' => $address,
                'city' => $city,
                'state' => $state,
                'description' => $description
            ),
            array('id' => $hotel_id)
        );

        wp_send_json_success(array('message' => 'Profile updated successfully!'));
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

    /**
     * Save hotel FAQ information.
     */
    public function save_hotel_info() {
        global $wpdb;

        // Verify nonce
        check_ajax_referer('staydesk_nonce', 'nonce');

        // Check if user is logged in
        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => 'Please login to save hotel information.'));
        }

        $current_user = wp_get_current_user();
        $table_hotels = $wpdb->prefix . 'staydesk_hotels';

        // Get hotel ID for current user
        $hotel = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_hotels WHERE user_id = %d",
            $current_user->ID
        ));

        if (!$hotel) {
            wp_send_json_error(array('message' => 'Hotel profile not found.'));
        }

        // Sanitize and prepare FAQ data
        $payment_pricing = array(
            'checkin_time' => sanitize_text_field($_POST['checkin_time'] ?? ''),
            'checkout_time' => sanitize_text_field($_POST['checkout_time'] ?? ''),
            'payment_methods' => sanitize_textarea_field($_POST['payment_methods'] ?? ''),
            'deposit_policy' => sanitize_textarea_field($_POST['deposit_policy'] ?? ''),
            'cancellation_policy' => sanitize_textarea_field($_POST['cancellation_policy'] ?? '')
        );

        $facilities = array(
            'basic_amenities' => sanitize_textarea_field($_POST['basic_amenities'] ?? ''),
            'recreation' => sanitize_textarea_field($_POST['recreation'] ?? ''),
            'business' => sanitize_textarea_field($_POST['business'] ?? ''),
            'accessibility' => sanitize_textarea_field($_POST['accessibility'] ?? '')
        );

        $location_transport = array(
            'address_details' => sanitize_textarea_field($_POST['address_details'] ?? ''),
            'nearby_attractions' => sanitize_textarea_field($_POST['nearby_attractions'] ?? ''),
            'transport_options' => sanitize_textarea_field($_POST['transport_options'] ?? ''),
            'airport_distance' => sanitize_text_field($_POST['airport_distance'] ?? '')
        );

        $food_dining = array(
            'restaurant_details' => sanitize_textarea_field($_POST['restaurant_details'] ?? ''),
            'cuisine_types' => sanitize_textarea_field($_POST['cuisine_types'] ?? ''),
            'dining_hours' => sanitize_text_field($_POST['dining_hours'] ?? ''),
            'room_service' => sanitize_textarea_field($_POST['room_service'] ?? ''),
            'special_diets' => sanitize_textarea_field($_POST['special_diets'] ?? '')
        );

        $policies = array(
            'pet_policy' => sanitize_textarea_field($_POST['pet_policy'] ?? ''),
            'smoking_policy' => sanitize_textarea_field($_POST['smoking_policy'] ?? ''),
            'children_policy' => sanitize_textarea_field($_POST['children_policy'] ?? ''),
            'payment_policy' => sanitize_textarea_field($_POST['payment_policy'] ?? '')
        );

        $services = array(
            'event_hosting' => sanitize_textarea_field($_POST['event_hosting'] ?? ''),
            'business_services' => sanitize_textarea_field($_POST['business_services'] ?? ''),
            'laundry' => sanitize_textarea_field($_POST['laundry'] ?? ''),
            'concierge' => sanitize_textarea_field($_POST['concierge'] ?? '')
        );

        $safety_security = array(
            'security_measures' => sanitize_textarea_field($_POST['security_measures'] ?? ''),
            'emergency_procedures' => sanitize_textarea_field($_POST['emergency_procedures'] ?? ''),
            'safe_deposit' => sanitize_textarea_field($_POST['safe_deposit'] ?? ''),
            'cctv' => sanitize_text_field($_POST['cctv'] ?? '')
        );

        $additional_faqs = sanitize_textarea_field($_POST['additional_faqs'] ?? '');

        // Update hotel record
        $updated = $wpdb->update(
            $table_hotels,
            array(
                'payment_pricing' => json_encode($payment_pricing),
                'facilities' => json_encode($facilities),
                'location_transport' => json_encode($location_transport),
                'food_dining' => json_encode($food_dining),
                'policies' => json_encode($policies),
                'services' => json_encode($services),
                'safety_security' => json_encode($safety_security),
                'additional_faqs' => $additional_faqs
            ),
            array('id' => $hotel->id),
            array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s'),
            array('%d')
        );

        if ($updated !== false) {
            wp_send_json_success(array('message' => 'Hotel information saved successfully!'));
        } else {
            wp_send_json_error(array('message' => 'Failed to save hotel information. Please try again.'));
        }
    }
}
