<?php
/**
 * AI Chatbot functionality with bilingual support.
 *
 * @since      1.0.0
 * @package    Staydesk
 */
class Staydesk_Chatbot {

    /**
     * Initialize the class.
     */
    public function __construct() {
        // AJAX handlers
        add_action('wp_ajax_nopriv_staydesk_chatbot_message', array($this, 'process_message'));
        add_action('wp_ajax_staydesk_chatbot_message', array($this, 'process_message'));
    }

    /**
     * Process chatbot message.
     */
    public function process_message() {
        global $wpdb;

        $hotel_id = isset($_POST['hotel_id']) ? intval($_POST['hotel_id']) : 0;
        $session_id = isset($_POST['session_id']) ? sanitize_text_field($_POST['session_id']) : '';
        $message = isset($_POST['message']) ? sanitize_text_field($_POST['message']) : '';
        $language = isset($_POST['language']) ? sanitize_text_field($_POST['language']) : 'en';

        if (empty($message)) {
            wp_send_json_error(array('message' => 'Message is required.'));
        }

        // Generate session ID if not provided
        if (empty($session_id)) {
            $session_id = 'session_' . uniqid();
        }

        // Check if hotel subscription is active
        if ($hotel_id > 0 && !Staydesk_Subscriptions::is_subscription_active($hotel_id)) {
            wp_send_json_error(array(
                'message' => 'This hotel\'s chatbot is currently unavailable. Please contact support.',
                'fallback_whatsapp' => true
            ));
        }

        // Log user message
        $table_chat_logs = $wpdb->prefix . 'staydesk_chat_logs';
        $wpdb->insert($table_chat_logs, array(
            'hotel_id' => $hotel_id,
            'session_id' => $session_id,
            'message_type' => 'user',
            'message_text' => $message,
            'language' => $language
        ));

        // Process message and generate response
        $response = $this->generate_response($hotel_id, $message, $session_id, $language);

        // Log bot response
        $wpdb->insert($table_chat_logs, array(
            'hotel_id' => $hotel_id,
            'session_id' => $session_id,
            'message_type' => 'bot',
            'message_text' => $response['message'],
            'language' => $language,
            'response_text' => json_encode($response)
        ));

        wp_send_json_success($response);
    }

    /**
     * Generate chatbot response.
     */
    private function generate_response($hotel_id, $message, $session_id, $language = 'en') {
        global $wpdb;

        $message_lower = strtolower($message);

        // Get hotel data
        $hotel = null;
        if ($hotel_id > 0) {
            $table_hotels = $wpdb->prefix . 'staydesk_hotels';
            $hotel = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM $table_hotels WHERE id = %d",
                $hotel_id
            ));
        }

        // Check for greeting
        if (preg_match('/(hello|hi|hey|good morning|good afternoon|good evening|wetin dey|wetin sup)/i', $message_lower)) {
            return array(
                'message' => $this->translate("Hello! Welcome to " . ($hotel ? $hotel->hotel_name : "StayDesk") . ". How can I help you today?", $language),
                'type' => 'greeting',
                'options' => array(
                    'Check available rooms',
                    'Make a booking',
                    'Check booking status',
                    'Request refund',
                    'Contact support'
                )
            );
        }

        // Check for room availability query
        if (preg_match('/(room|available|vacancy|free|book)/i', $message_lower)) {
            if ($hotel_id > 0) {
                $rooms = Staydesk_Rooms::get_hotel_rooms($hotel_id);
                
                if (empty($rooms)) {
                    return array(
                        'message' => $this->translate("Currently, there are no rooms available. Would you like me to notify you when rooms become available?", $language),
                        'type' => 'no_rooms'
                    );
                }

                $room_list = array();
                foreach ($rooms as $room) {
                    if ($room->availability_status === 'available') {
                        $room_list[] = array(
                            'id' => $room->id,
                            'name' => $room->room_name,
                            'type' => $room->room_type,
                            'price' => $room->price_per_night,
                            'description' => $room->room_description
                        );
                    }
                }

                return array(
                    'message' => $this->translate("We have " . count($room_list) . " rooms available. Here are your options:", $language),
                    'type' => 'room_list',
                    'rooms' => $room_list
                );
            } else {
                return array(
                    'message' => $this->translate("Please specify which hotel you're interested in, or visit our website to browse all hotels.", $language),
                    'type' => 'need_hotel'
                );
            }
        }

        // Check for booking status query
        if (preg_match('/(booking|reservation|reference|status|check)/i', $message_lower)) {
            return array(
                'message' => $this->translate("Please provide your booking reference number (e.g., BK12345678) so I can check your booking status.", $language),
                'type' => 'booking_inquiry',
                'input_required' => 'booking_reference'
            );
        }

        // Check if message contains booking reference
        if (preg_match('/BK[A-Z0-9]{8}/i', $message, $matches)) {
            $reference = strtoupper($matches[0]);
            $booking = Staydesk_Bookings::get_booking_by_reference($reference);
            
            if ($booking) {
                return array(
                    'message' => $this->translate("I found your booking! Reference: {$reference}, Status: {$booking->booking_status}, Payment: {$booking->payment_status}, Check-in: {$booking->check_in_date}, Check-out: {$booking->check_out_date}", $language),
                    'type' => 'booking_found',
                    'booking' => $booking
                );
            } else {
                return array(
                    'message' => $this->translate("I couldn't find a booking with reference {$reference}. Please check the reference number and try again.", $language),
                    'type' => 'booking_not_found'
                );
            }
        }

        // Check for refund request
        if (preg_match('/(refund|cancel|money back|return)/i', $message_lower)) {
            return array(
                'message' => $this->translate("I can help you with a refund request. Please provide your booking reference number.", $language),
                'type' => 'refund_request',
                'input_required' => 'booking_reference'
            );
        }

        // Check for pricing query
        if (preg_match('/(price|cost|how much|rate|amount)/i', $message_lower)) {
            if ($hotel_id > 0) {
                $rooms = Staydesk_Rooms::get_hotel_rooms($hotel_id);
                $price_info = array();
                
                foreach ($rooms as $room) {
                    $price_info[] = $room->room_name . ": ₦" . number_format($room->price_per_night, 2) . " per night";
                }
                
                return array(
                    'message' => $this->translate("Here are our room rates:\n" . implode("\n", $price_info), $language),
                    'type' => 'pricing'
                );
            }
        }

        // Check for payment query
        if (preg_match('/(payment|pay|account|transfer)/i', $message_lower)) {
            if ($hotel && $hotel->account_details) {
                $account_info = json_decode($hotel->account_details, true);
                return array(
                    'message' => $this->translate("For payment, please use the following account details:\n" . json_encode($account_info), $language),
                    'type' => 'payment_info',
                    'account_details' => $account_info
                );
            } else {
                return array(
                    'message' => $this->translate("Please contact the hotel directly for payment information.", $language),
                    'type' => 'contact_hotel',
                    'fallback_whatsapp' => true
                );
            }
        }

        // Default fallback - offer to connect to WhatsApp
        return array(
            'message' => $this->translate("I'm not sure how to help with that. Would you like to chat with our support team on WhatsApp?", $language),
            'type' => 'fallback',
            'fallback_whatsapp' => true,
            'whatsapp_link' => 'https://wa.me/2347120018023?text=' . urlencode($message)
        );
    }

    /**
     * Translate message based on language.
     */
    private function translate($message, $language) {
        if ($language === 'pidgin') {
            // Simple Nigerian Pidgin translations
            $translations = array(
                'Hello! Welcome to' => 'Hello! Welcome to',
                'How can I help you today?' => 'Wetin I fit do for you today?',
                'We have' => 'We get',
                'rooms available' => 'room wey dey available',
                'Please provide your booking reference' => 'Abeg give me your booking reference',
                'I found your booking!' => 'I don see your booking!',
                "I couldn't find" => 'I no fit find',
                'I can help you' => 'I fit help you',
                'Would you like to chat with our support team' => 'You wan talk to our support people',
                "I'm not sure how to help" => 'I no too sure how I go help'
            );

            foreach ($translations as $en => $pidgin) {
                $message = str_replace($en, $pidgin, $message);
            }
        }

        return $message;
    }

    /**
     * Get chat history for a session.
     */
    public static function get_chat_history($session_id) {
        global $wpdb;

        $table_chat_logs = $wpdb->prefix . 'staydesk_chat_logs';
        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_chat_logs WHERE session_id = %s ORDER BY created_at ASC",
            $session_id
        ));
    }
}

new Staydesk_Chatbot();
