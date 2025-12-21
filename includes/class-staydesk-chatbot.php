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

        // Get hotel data including FAQs
        $hotel = null;
        $faq_data = null;
        if ($hotel_id > 0) {
            $table_hotels = $wpdb->prefix . 'staydesk_hotels';
            $hotel = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM $table_hotels WHERE id = %d",
                $hotel_id
            ));
            
            // Get FAQ data for context
            if ($hotel) {
                $faq_data = $this->get_hotel_faq_data($hotel);
            }
        }

        // Check FAQ data for specific questions
        if ($faq_data && $hotel_id > 0) {
            // Check-in/check-out time queries
            if (preg_match('/(check.?in|check.?out|arrival|departure|time)/i', $message_lower)) {
                $payment_pricing = $faq_data['payment_pricing'] ?? array();
                if (!empty($payment_pricing['checkin_time']) || !empty($payment_pricing['checkout_time'])) {
                    $response = "Our check-in time is " . ($payment_pricing['checkin_time'] ?: '2:00 PM') . 
                               " and check-out time is " . ($payment_pricing['checkout_time'] ?: '12:00 PM') . ".";
                    return array(
                        'message' => $this->translate($response, $language),
                        'type' => 'faq_response'
                    );
                }
            }

            // Payment method queries
            if (preg_match('/(payment|pay|paystack|card|cash|transfer)/i', $message_lower)) {
                $payment_pricing = $faq_data['payment_pricing'] ?? array();
                if (!empty($payment_pricing['payment_methods'])) {
                    return array(
                        'message' => $this->translate("We accept the following payment methods: " . $payment_pricing['payment_methods'], $language),
                        'type' => 'faq_response'
                    );
                }
            }

            // Facilities queries (generator, pool, WiFi, etc.)
            if (preg_match('/(generator|power|light|nepa|pool|gym|wifi|internet|facility|amenity)/i', $message_lower)) {
                $facilities = $faq_data['facilities'] ?? array();
                $response = "";
                
                if (preg_match('/(generator|power|light|nepa)/i', $message_lower) && !empty($facilities['basic_amenities'])) {
                    $response = "Power/Generator information: " . $facilities['basic_amenities'];
                } elseif (!empty($facilities['basic_amenities'])) {
                    $response = "Our facilities include: " . $facilities['basic_amenities'];
                }
                
                if (!empty($facilities['recreation'])) {
                    $response .= " Recreation: " . $facilities['recreation'];
                }
                
                if ($response) {
                    return array(
                        'message' => $this->translate($response, $language),
                        'type' => 'faq_response'
                    );
                }
            }

            // Food/dining queries (Jollof rice, Nigerian food, etc.)
            if (preg_match('/(food|eat|restaurant|dining|jollof|rice|meal|breakfast|dinner|lunch)/i', $message_lower)) {
                $food_dining = $faq_data['food_dining'] ?? array();
                $response = "";
                
                if (!empty($food_dining['restaurant_details'])) {
                    $response = $food_dining['restaurant_details'];
                }
                if (!empty($food_dining['cuisine_types'])) {
                    $response .= " We serve: " . $food_dining['cuisine_types'];
                }
                if (!empty($food_dining['room_service'])) {
                    $response .= " Room service: " . $food_dining['room_service'];
                }
                
                if ($response) {
                    return array(
                        'message' => $this->translate($response, $language),
                        'type' => 'faq_response'
                    );
                }
            }

            // Location queries (airport, VI, Lekki, distance)
            if (preg_match('/(location|where|address|airport|victoria island|lekki|ikoyi|distance|far)/i', $message_lower)) {
                $location = $faq_data['location_transport'] ?? array();
                $response = "";
                
                if (!empty($location['address_details'])) {
                    $response = "Location: " . $location['address_details'];
                }
                if (!empty($location['airport_distance'])) {
                    $response .= " Distance from airport: " . $location['airport_distance'];
                }
                if (!empty($location['nearby_attractions'])) {
                    $response .= " Nearby: " . $location['nearby_attractions'];
                }
                
                if ($response) {
                    return array(
                        'message' => $this->translate($response, $language),
                        'type' => 'faq_response'
                    );
                }
            }

            // Policy queries (pets, smoking, children, cancellation)
            if (preg_match('/(pet|dog|cat|animal|smoking|smoke|cigarette|children|child|kid|cancel|refund)/i', $message_lower)) {
                $policies = $faq_data['policies'] ?? array();
                $response = "";
                
                if (preg_match('/(pet|dog|cat|animal)/i', $message_lower) && !empty($policies['pet_policy'])) {
                    $response = "Pet policy: " . $policies['pet_policy'];
                } elseif (preg_match('/(smoking|smoke|cigarette)/i', $message_lower) && !empty($policies['smoking_policy'])) {
                    $response = "Smoking policy: " . $policies['smoking_policy'];
                } elseif (preg_match('/(children|child|kid)/i', $message_lower) && !empty($policies['children_policy'])) {
                    $response = "Children policy: " . $policies['children_policy'];
                } elseif (preg_match('/(cancel|refund)/i', $message_lower) && !empty($policies['payment_policy'])) {
                    $response = "Cancellation/Refund policy: " . $policies['payment_policy'];
                }
                
                if ($response) {
                    return array(
                        'message' => $this->translate($response, $language),
                        'type' => 'faq_response'
                    );
                }
            }

            // Services queries (wedding, conference, laundry, event)
            if (preg_match('/(wedding|event|conference|meeting|laundry|dry.?clean|concierge)/i', $message_lower)) {
                $services = $faq_data['services'] ?? array();
                $response = "";
                
                if (preg_match('/(wedding|event)/i', $message_lower) && !empty($services['event_hosting'])) {
                    $response = "Event hosting: " . $services['event_hosting'];
                } elseif (preg_match('/(conference|meeting)/i', $message_lower) && !empty($services['business_services'])) {
                    $response = "Business services: " . $services['business_services'];
                } elseif (preg_match('/(laundry|dry.?clean)/i', $message_lower) && !empty($services['laundry'])) {
                    $response = "Laundry services: " . $services['laundry'];
                }
                
                if ($response) {
                    return array(
                        'message' => $this->translate($response, $language),
                        'type' => 'faq_response'
                    );
                }
            }

            // Security/safety queries
            if (preg_match('/(safe|security|guard|cctv|camera|emergency)/i', $message_lower)) {
                $safety = $faq_data['safety_security'] ?? array();
                $response = "";
                
                if (!empty($safety['security_measures'])) {
                    $response = "Security: " . $safety['security_measures'];
                }
                if (!empty($safety['cctv'])) {
                    $response .= " CCTV: " . $safety['cctv'];
                }
                
                if ($response) {
                    return array(
                        'message' => $this->translate($response, $language),
                        'type' => 'faq_response'
                    );
                }
            }
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
     * Get hotel FAQ data.
     */
    private function get_hotel_faq_data($hotel) {
        if (!$hotel) {
            return null;
        }

        $faq_data = array();

        // Decode JSON fields
        if (!empty($hotel->payment_pricing)) {
            $faq_data['payment_pricing'] = json_decode($hotel->payment_pricing, true) ?: array();
        }
        if (!empty($hotel->facilities)) {
            $faq_data['facilities'] = json_decode($hotel->facilities, true) ?: array();
        }
        if (!empty($hotel->location_transport)) {
            $faq_data['location_transport'] = json_decode($hotel->location_transport, true) ?: array();
        }
        if (!empty($hotel->food_dining)) {
            $faq_data['food_dining'] = json_decode($hotel->food_dining, true) ?: array();
        }
        if (!empty($hotel->policies)) {
            $faq_data['policies'] = json_decode($hotel->policies, true) ?: array();
        }
        if (!empty($hotel->services)) {
            $faq_data['services'] = json_decode($hotel->services, true) ?: array();
        }
        if (!empty($hotel->safety_security)) {
            $faq_data['safety_security'] = json_decode($hotel->safety_security, true) ?: array();
        }
        if (!empty($hotel->additional_faqs)) {
            $faq_data['additional_faqs'] = $hotel->additional_faqs;
        }

        return $faq_data;
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

    /**
     * Render dashboard chatbot widget.
     */
    public static function render_dashboard_widget($hotel_id) {
        ?>
        <div id="dashboard-chatbot-widget" style="position: fixed; bottom: 20px; left: 20px; z-index: 1000;">
            <style>
                #dashboard-chatbot-widget .chat-toggle {
                    width: 50px;
                    height: 50px;
                    border-radius: 50%;
                    background: linear-gradient(135deg, #D4AF37 0%, #FFD700 100%);
                    border: none;
                    cursor: pointer;
                    box-shadow: 0 4px 15px rgba(212, 175, 55, 0.4);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 24px;
                    transition: all 0.3s ease;
                }
                
                #dashboard-chatbot-widget .chat-toggle:hover {
                    transform: scale(1.1);
                    box-shadow: 0 6px 20px rgba(212, 175, 55, 0.6);
                }
                
                #dashboard-chatbot-widget .chat-window {
                    display: none;
                    position: absolute;
                    bottom: 75px;
                    left: 0;
                    width: 350px;
                    height: 500px;
                    background: #1a1a1a;
                    border-radius: 15px;
                    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.7);
                    border: 1px solid rgba(212, 175, 55, 0.3);
                    display: flex;
                    flex-direction: column;
                }
                
                #dashboard-chatbot-widget .chat-window.open {
                    display: flex;
                }
                
                #dashboard-chatbot-widget .chat-header {
                    background: linear-gradient(135deg, #D4AF37 0%, #FFD700 100%);
                    padding: 15px;
                    border-radius: 15px 15px 0 0;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                }
                
                #dashboard-chatbot-widget .chat-header h3 {
                    margin: 0;
                    color: #0a0a0a;
                    font-size: 16px;
                    font-weight: 700;
                }
                
                #dashboard-chatbot-widget .chat-close {
                    background: none;
                    border: none;
                    color: #0a0a0a;
                    font-size: 20px;
                    cursor: pointer;
                    padding: 0;
                    width: 25px;
                    height: 25px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
                
                #dashboard-chatbot-widget .chat-messages {
                    flex: 1;
                    overflow-y: auto;
                    padding: 15px;
                    background: #0a0a0a;
                }
                
                #dashboard-chatbot-widget .chat-message {
                    margin-bottom: 12px;
                    padding: 10px 12px;
                    border-radius: 10px;
                    max-width: 80%;
                    word-wrap: break-word;
                }
                
                #dashboard-chatbot-widget .chat-message.user {
                    background: linear-gradient(135deg, #2a2a2a 0%, #3a3a3a 100%);
                    color: #E8E8E8;
                    margin-left: auto;
                    text-align: right;
                }
                
                #dashboard-chatbot-widget .chat-message.bot {
                    background: linear-gradient(135deg, #D4AF37 0%, #FFD700 100%);
                    color: #0a0a0a;
                    margin-right: auto;
                }
                
                #dashboard-chatbot-widget .chat-input-area {
                    padding: 15px;
                    background: #1a1a1a;
                    border-top: 1px solid rgba(212, 175, 55, 0.2);
                    border-radius: 0 0 15px 15px;
                    display: flex;
                    gap: 10px;
                }
                
                #dashboard-chatbot-widget .chat-input {
                    flex: 1;
                    background: rgba(42, 42, 42, 0.8);
                    border: 1px solid rgba(212, 175, 55, 0.3);
                    border-radius: 20px;
                    padding: 10px 15px;
                    color: #FFFFFF;
                    font-size: 14px;
                    outline: none;
                }
                
                #dashboard-chatbot-widget .chat-input:focus {
                    border-color: #D4AF37;
                    box-shadow: 0 0 10px rgba(212, 175, 55, 0.3);
                }
                
                #dashboard-chatbot-widget .chat-send {
                    background: linear-gradient(135deg, #D4AF37 0%, #FFD700 100%);
                    border: none;
                    border-radius: 50%;
                    width: 40px;
                    height: 40px;
                    cursor: pointer;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    transition: all 0.3s ease;
                }
                
                #dashboard-chatbot-widget .chat-send:hover {
                    transform: scale(1.1);
                }
            </style>
            
            <button class="chat-toggle" onclick="toggleDashboardChat()">
                <span style="font-size: 22px; color: #0a0a0a;">👤</span>
            </button>
            
            <div class="chat-window" id="dashboardChatWindow">
                <div class="chat-header">
                    <h3>👤 Hotel Assistant</h3>
                    <button class="chat-close" onclick="toggleDashboardChat()">×</button>
                </div>
                
                <div class="chat-messages" id="dashboardChatMessages">
                    <div class="chat-message bot">
                        Hello! I'm your hotel assistant. Ask me anything about check-in times, amenities, policies, or room availability!
                    </div>
                </div>
                
                <div class="chat-input-area">
                    <input type="text" class="chat-input" id="dashboardChatInput" placeholder="Type your message..." onkeypress="if(event.key==='Enter') sendDashboardChat()">
                    <button class="chat-send" onclick="sendDashboardChat()">➤</button>
                </div>
            </div>
        </div>
        
        <script>
        function toggleDashboardChat() {
            var chatWindow = document.getElementById('dashboardChatWindow');
            chatWindow.classList.toggle('open');
        }
        
        function sendDashboardChat() {
            var input = document.getElementById('dashboardChatInput');
            var message = input.value.trim();
            
            if (!message) return;
            
            // Add user message to chat
            var messagesDiv = document.getElementById('dashboardChatMessages');
            var userMsg = document.createElement('div');
            userMsg.className = 'chat-message user';
            userMsg.textContent = message;
            messagesDiv.appendChild(userMsg);
            
            input.value = '';
            messagesDiv.scrollTop = messagesDiv.scrollHeight;
            
            // Send to server
            jQuery.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                data: {
                    action: 'staydesk_chatbot_message',
                    hotel_id: <?php echo intval($hotel_id); ?>,
                    message: message,
                    session_id: 'dashboard_<?php echo get_current_user_id(); ?>_' + Date.now(),
                    language: 'en'
                },
                success: function(response) {
                    var botMsg = document.createElement('div');
                    botMsg.className = 'chat-message bot';
                    botMsg.textContent = response.data.message || 'Sorry, I encountered an error.';
                    messagesDiv.appendChild(botMsg);
                    messagesDiv.scrollTop = messagesDiv.scrollHeight;
                },
                error: function() {
                    var botMsg = document.createElement('div');
                    botMsg.className = 'chat-message bot';
                    botMsg.textContent = 'Sorry, I\'m having trouble connecting. Please try again.';
                    messagesDiv.appendChild(botMsg);
                    messagesDiv.scrollTop = messagesDiv.scrollHeight;
                }
            });
        }
        </script>
        <?php
    }
}

new Staydesk_Chatbot();
