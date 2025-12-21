<?php
/**
 * Hotel Information & FAQ Management Template
 * This template provides a comprehensive form for hotels to fill in their information
 * Pre-filled with Nigerian hotel-specific FAQs
 */

// Note: Login check is handled by the shortcode handler
global $wpdb;
$user_id = get_current_user_id();
$table_hotels = $wpdb->prefix . 'staydesk_hotels';
$hotel = $wpdb->get_row($wpdb->prepare(
    "SELECT * FROM $table_hotels WHERE user_id = %d",
    $user_id
));

if (!$hotel) {
    echo '<div style="padding: 40px; text-align: center; color: #FFD700; background: #1a1a1a; border-radius: 12px; border: 1px solid rgba(212, 175, 55, 0.3);">';
    echo '<h2 style="color: #FFD700;">Hotel Profile Not Found</h2>';
    echo '<p style="color: #B8B8B8;">Please complete your profile setup on the <a href="' . home_url('/staydesk-profile') . '" style="color: #D4AF37;">Profile page</a> first.</p>';
    echo '</div>';
    return;
}

// Get existing hotel info
$hotel_info = json_decode($hotel->hotel_info_json, true) ?: array();
?>
<style>
    .hotel-info-container {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 100%);
        min-height: 100vh;
        padding: 25px;
        color: #F0F0F0;
    }
    
    .info-header {
        background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
        padding: 25px;
        border-radius: 18px;
        margin-bottom: 30px;
        box-shadow: 0 6px 30px rgba(0, 0, 0, 0.6);
        border: 1px solid rgba(212, 175, 55, 0.3);
    }
    
    .info-header h1 {
        
        @keyframes sparkle {
            0% { background-position: 0% center; }
            100% { background-position: 200% center; }
        }
        background: linear-gradient(90deg, #FFD700 0%, #4FC3F7 25%, #FFD700 50%, #64B5F6 75%, #FFD700 100%);
        background-size: 200% auto;
            -webkit-background-clip: text;
            animation: sparkle 3s linear infinite;
        -webkit-text-fill-color: transparent;
        background-clip: text;
            animation: sparkle 3s linear infinite;
        margin: 0 0 10px 0;
        font-size: 1.8rem;
        font-weight: 800;
        text-shadow: 0 2px 8px rgba(212, 175, 55, 0.2);
    }
    
    .info-header p {
        color: #B8B8B8;
        margin: 0;
        font-size: 14px;
    }
    
    .accordion-section {
        background: rgba(26, 26, 26, 0.9);
        border-radius: 12px;
        margin-bottom: 20px;
        border: 1px solid rgba(212, 175, 55, 0.25);
        overflow: hidden;
    }
    
    .accordion-header {
        padding: 18px 25px;
        cursor: pointer;
        background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
        border-bottom: 1px solid rgba(212, 175, 55, 0.3);
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .accordion-header:hover {
        background: linear-gradient(135deg, #2a2a2a 0%, #3a3a3a 100%);
    }
    
    .accordion-header h3 {
        margin: 0;
        color: #FFD700;
        font-size: 1.2rem;
        font-weight: 700;
    }
    
    .accordion-icon {
        font-size: 1.4rem;
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .accordion-header.active .accordion-icon {
        transform: rotate(180deg);
    }
    
    .accordion-content {
        padding: 25px;
        display: none;
    }
    
    .accordion-content.active {
        display: block;
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-group label {
        display: block;
        color: #FFFFFF;
        font-weight: 700;
        margin-bottom: 8px;
        font-size: 0.95rem;
    }
    
    .form-group input[type="text"],
    .form-group input[type="email"],
    .form-group input[type="time"],
    .form-group textarea,
    .form-group select {
        width: 100%;
        padding: 14px 18px;
        background: rgba(42, 42, 42, 0.8);
        border: 1px solid rgba(212, 175, 55, 0.2);
        border-radius: 10px;
        color: #FFFFFF;
        font-size: 1rem;
        transition: all 0.3s ease;
    }
    
    .form-group input:focus,
    .form-group textarea:focus,
    .form-group select:focus {
        outline: none;
        border-color: #D4AF37;
        background: rgba(42, 42, 42, 0.9);
        box-shadow: 0 0 20px rgba(212, 175, 55, 0.2);
    }
    
    .form-group textarea {
        min-height: 100px;
        resize: vertical;
    }
    
    .checkbox-group {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 12px;
        margin-top: 10px;
    }
    
    .checkbox-group label {
        display: flex;
        align-items: center;
        color: #E8E8E8;
        font-weight: 600;
        cursor: pointer;
    }
    
    .checkbox-group input[type="checkbox"] {
        margin-right: 8px;
        width: 18px;
        height: 18px;
    }
    
    .faq-list {
        margin-top: 20px;
    }
    
    .faq-item {
        background: rgba(42, 42, 42, 0.6);
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 15px;
        border: 1px solid rgba(212, 175, 55, 0.1);
    }
    
    .faq-item input,
    .faq-item textarea {
        margin-bottom: 10px;
    }
    
    .btn-remove-faq {
        background: #DC3545;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-remove-faq:hover {
        background: #C82333;
        transform: scale(1.05);
    }
    
    .btn-add-faq {
        background: linear-gradient(135deg, #28A745 0%, #20C997 100%);
        color: white;
        border: none;
        padding: 12px 24px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 700;
        margin-top: 15px;
        transition: all 0.3s ease;
    }
    
    .btn-add-faq:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
    }
    
    .btn-save {
        background: linear-gradient(135deg, #D4AF37 0%, #FFD700 100%);
        color: #000;
        border: none;
        padding: 16px 40px;
        border-radius: 12px;
        cursor: pointer;
        font-weight: 700;
        font-size: 1.1rem;
        margin-top: 30px;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 20px rgba(212, 175, 55, 0.3);
    }
    
    .btn-save:hover {
        transform: translateY(-3px) scale(1.05);
        box-shadow: 0 8px 35px rgba(212, 175, 55, 0.5);
    }
    
    .btn-back {
        background: rgba(212, 175, 55, 0.1);
        color: #D4AF37;
        border: 1px solid #D4AF37;
        padding: 12px 28px;
        border-radius: 10px;
        text-decoration: none;
        display: inline-block;
        margin-bottom: 20px;
        transition: all 0.3s ease;
        font-weight: 600;
    }
    
    .btn-back:hover {
        background: #D4AF37;
        color: #000;
        transform: translateX(-5px);
    }
    
    .success-message {
        background: linear-gradient(135deg, rgba(40, 167, 69, 0.15) 0%, rgba(32, 201, 151, 0.15) 100%);
        border: 1px solid rgba(40, 167, 69, 0.4);
        color: #4ADE80;
        padding: 18px;
        border-radius: 10px;
        margin-bottom: 25px;
        font-weight: 600;
        display: none;
    }
    
    .error-message {
        background: linear-gradient(135deg, rgba(220, 53, 69, 0.15) 0%, rgba(200, 35, 51, 0.15) 100%);
        border: 1px solid rgba(220, 53, 69, 0.4);
        color: #FF6B7A;
        padding: 18px;
        border-radius: 10px;
        margin-bottom: 25px;
        font-weight: 600;
        display: none;
    }
    
    .helper-text {
        font-size: 0.85rem;
        color: #888;
        margin-top: 5px;
        font-style: italic;
    }
</style>

<div class="hotel-info-container">
    <a href="<?php echo home_url('/staydesk-dashboard'); ?>" class="btn-back">← Back to Dashboard</a>
    
    <div class="info-header">
        <h1>🏨 Hotel Information & FAQs</h1>
        
        @keyframes sparkle {
            0% { background-position: 0% center; }
            100% { background-position: 200% center; }
        }
        <p>Fill in your hotel details so guests get accurate information from the chatbot</p>
    </div>
    
    <div id="success-message" class="success-message"></div>
    <div id="error-message" class="error-message"></div>
    
    <form id="hotel-info-form">
        <!-- Section 1: Payment & Pricing -->
        <div class="accordion-section">
            <div class="accordion-header" onclick="toggleAccordion(this)">
                <h3>💳 Payment & Pricing Information</h3>
                <span class="accordion-icon">▼</span>
            </div>
            <div class="accordion-content">
                <div class="form-group">
                    <label>What payment methods do you accept?</label>
                    <div class="checkbox-group">
                        <label><input type="checkbox" name="payment_methods[]" value="Paystack" checked> Paystack</label>
                        <label><input type="checkbox" name="payment_methods[]" value="Credit/Debit Card" checked> Credit/Debit Card</label>
                        <label><input type="checkbox" name="payment_methods[]" value="Cash" checked> Cash</label>
                        <label><input type="checkbox" name="payment_methods[]" value="Bank Transfer" checked> Bank Transfer</label>
                        <label><input type="checkbox" name="payment_methods[]" value="Cryptocurrency"> Cryptocurrency</label>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Do you accept Naira payments?</label>
                    <input type="text" name="naira_accepted" value="Yes, we accept Nigerian Naira (₦)" placeholder="Yes/No + details">
                </div>
                
                <div class="form-group">
                    <label>What is your check-in time?</label>
                    <input type="time" name="checkin_time" value="14:00">
                </div>
                
                <div class="form-group">
                    <label>What is your check-out time?</label>
                    <input type="time" name="checkout_time" value="12:00">
                </div>
                
                <div class="form-group">
                    <label>Do you offer early check-in?</label>
                    <input type="text" name="early_checkin" value="Yes, subject to availability at ₦5,000 extra">
                </div>
                
                <div class="form-group">
                    <label>Do you offer late check-out?</label>
                    <input type="text" name="late_checkout" value="Yes, subject to availability at ₦5,000 extra">
                </div>
                
                <div class="form-group">
                    <label>What is your cancellation policy?</label>
                    <textarea name="cancellation_policy">Free cancellation up to 24 hours before check-in. Cancellations made within 24 hours are subject to a one-night charge.</textarea>
                </div>
                
                <div class="form-group">
                    <label>Do you charge a deposit?</label>
                    <input type="text" name="deposit_required" value="Yes, a refundable security deposit of ₦10,000 is required">
                </div>
            </div>
        </div>

        <!-- Section 2: Facilities & Amenities -->
        <div class="accordion-section">
            <div class="accordion-header" onclick="toggleAccordion(this)">
                <h3>🏊 Facilities & Amenities</h3>
                <span class="accordion-icon">▼</span>
            </div>
            <div class="accordion-content">
                <div class="form-group">
                    <label>What facilities do you have?</label>
                    <div class="checkbox-group">
                        <label><input type="checkbox" name="facilities[]" value="Swimming Pool" checked> Swimming Pool</label>
                        <label><input type="checkbox" name="facilities[]" value="Gym/Fitness Center" checked> Gym/Fitness Center</label>
                        <label><input type="checkbox" name="facilities[]" value="Restaurant" checked> Restaurant</label>
                        <label><input type="checkbox" name="facilities[]" value="Bar/Lounge" checked> Bar/Lounge</label>
                        <label><input type="checkbox" name="facilities[]" value="Conference Rooms" checked> Conference Rooms</label>
                        <label><input type="checkbox" name="facilities[]" value="Free WiFi" checked> Free WiFi</label>
                        <label><input type="checkbox" name="facilities[]" value="Air Conditioning" checked> Air Conditioning</label>
                        <label><input type="checkbox" name="facilities[]" value="Generator/Backup Power" checked> Generator/Backup Power</label>
                        <label><input type="checkbox" name="facilities[]" value="Parking" checked> Free Parking</label>
                        <label><input type="checkbox" name="facilities[]" value="24/7 Reception" checked> 24/7 Reception</label>
                        <label><input type="checkbox" name="facilities[]" value="Room Service" checked> Room Service</label>
                        <label><input type="checkbox" name="facilities[]" value="Spa" checked> Spa</label>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Do you have a generator?</label>
                    <input type="text" name="has_generator" value="Yes, 24/7 generator backup power" placeholder="Important for Nigerian guests!">
                </div>
                
                <div class="form-group">
                    <label>Is WiFi free and available everywhere?</label>
                    <input type="text" name="wifi_details" value="Yes, free high-speed WiFi in all rooms and public areas">
                </div>
                
                <div class="form-group">
                    <label>Do you have a swimming pool?</label>
                    <input type="text" name="pool_details" value="Yes, outdoor pool open 6am-10pm daily">
                </div>
                
                <div class="form-group">
                    <label>Do you have a gym?</label>
                    <input type="text" name="gym_details" value="Yes, fully equipped fitness center 24/7">
                </div>
                
                <div class="form-group">
                    <label>What room amenities do you provide?</label>
                    <textarea name="room_amenities">All rooms include: Air conditioning, flat-screen TV with DSTV, mini-fridge, work desk, safe, complimentary toiletries, hair dryer, and bottled water.</textarea>
                </div>
            </div>
        </div>

        <!-- Section 3: Location & Transport -->
        <div class="accordion-section">
            <div class="accordion-header" onclick="toggleAccordion(this)">
                <h3>📍 Location & Transport</h3>
                <span class="accordion-icon">▼</span>
            </div>
            <div class="accordion-content">
                <div class="form-group">
                    <label>How far are you from the airport?</label>
                    <input type="text" name="airport_distance" value="15 minutes from Murtala Muhammed International Airport">
                </div>
                
                <div class="form-group">
                    <label>Are you located in Victoria Island (VI)?</label>
                    <input type="text" name="in_vi" value="Yes, in the heart of Victoria Island">
                </div>
                
                <div class="form-group">
                    <label>Are you located in Lekki?</label>
                    <input type="text" name="in_lekki" value="No, but only 20 minutes drive to Lekki">
                </div>
                
                <div class="form-group">
                    <label>How far from Lagos Island?</label>
                    <input type="text" name="lagos_island_distance" value="10 minutes drive from Lagos Island">
                </div>
                
                <div class="form-group">
                    <label>Do you offer airport pickup?</label>
                    <input type="text" name="airport_pickup" value="Yes, at ₦15,000 per trip">
                </div>
                
                <div class="form-group">
                    <label>Do you offer shuttle services?</label>
                    <input type="text" name="shuttle_service" value="Yes, complimentary shuttle to VI and Ikoyi business districts">
                </div>
                
                <div class="form-group">
                    <label>Is taxi/Uber available nearby?</label>
                    <input type="text" name="taxi_availability" value="Yes, Uber, Bolt, and taxis available 24/7">
                </div>
                
                <div class="form-group">
                    <label>What landmarks are you near?</label>
                    <textarea name="nearby_landmarks">Within walking distance: Eko Hotel, Silverbird Cinemas, Terra Kulture. 5 mins to Lekki Conservation Centre, Nike Art Gallery.</textarea>
                </div>
            </div>
        </div>

        <!-- Section 4: Food & Dining -->
        <div class="accordion-section">
            <div class="accordion-header" onclick="toggleAccordion(this)">
                <h3>🍽️ Food & Dining</h3>
                <span class="accordion-icon">▼</span>
            </div>
            <div class="accordion-content">
                <div class="form-group">
                    <label>Do you serve Jollof rice? (Most important question! 🍚)</label>
                    <input type="text" name="has_jollof" value="Yes! The best Jollof rice in Lagos!" placeholder="This is critical!">
                </div>
                
                <div class="form-group">
                    <label>Do you serve Nigerian dishes?</label>
                    <textarea name="nigerian_dishes">Yes! We serve: Jollof rice, Fried rice, Pounded yam, Egusi soup, Pepper soup, Suya, Asun, Moi-moi, Akara, and more.</textarea>
                </div>
                
                <div class="form-group">
                    <label>Do you serve continental/international food?</label>
                    <input type="text" name="continental_food" value="Yes, full continental breakfast and international menu">
                </div>
                
                <div class="form-group">
                    <label>Is breakfast included?</label>
                    <input type="text" name="breakfast_included" value="Yes, complimentary buffet breakfast 6:30am-10:30am">
                </div>
                
                <div class="form-group">
                    <label>Restaurant operating hours?</label>
                    <input type="text" name="restaurant_hours" value="6:30am - 11:00pm daily">
                </div>
                
                <div class="form-group">
                    <label>Do you offer room service?</label>
                    <input type="text" name="room_service" value="Yes, 24/7 room service available">
                </div>
                
                <div class="form-group">
                    <label>Do you cater for vegetarians?</label>
                    <input type="text" name="vegetarian_options" value="Yes, vegetarian options available">
                </div>
                
                <div class="form-group">
                    <label>Can you accommodate special dietary requirements?</label>
                    <input type="text" name="special_diet" value="Yes, please inform us 24hrs in advance">
                </div>
            </div>
        </div>

        <!-- Section 5: Policies -->
        <div class="accordion-section">
            <div class="accordion-header" onclick="toggleAccordion(this)">
                <h3>📋 Hotel Policies</h3>
                <span class="accordion-icon">▼</span>
            </div>
            <div class="accordion-content">
                <div class="form-group">
                    <label>Do you allow pets?</label>
                    <input type="text" name="pets_allowed" value="No, pets are not allowed">
                </div>
                
                <div class="form-group">
                    <label>Is smoking allowed?</label>
                    <input type="text" name="smoking_policy" value="No smoking in rooms. Designated smoking areas available">
                </div>
                
                <div class="form-group">
                    <label>Are children welcome?</label>
                    <input type="text" name="children_policy" value="Yes, children of all ages welcome. Kids under 12 stay free">
                </div>
                
                <div class="form-group">
                    <label>Do you have extra beds/cribs?</label>
                    <input type="text" name="extra_beds" value="Yes, extra beds available at ₦8,000 per night">
                </div>
                
                <div class="form-group">
                    <label>Maximum guests per room?</label>
                    <input type="text" name="max_guests" value="Standard: 2 adults, Deluxe: 3 adults, Suite: 4 adults">
                </div>
                
                <div class="form-group">
                    <label>What is your age restriction?</label>
                    <input type="text" name="age_restriction" value="Guests must be 18+ to check in">
                </div>
                
                <div class="form-group">
                    <label>Do you require ID at check-in?</label>
                    <input type="text" name="id_required" value="Yes, valid government-issued ID required">
                </div>
            </div>
        </div>

        <!-- Section 6: Services & Events -->
        <div class="accordion-section">
            <div class="accordion-header" onclick="toggleAccordion(this)">
                <h3>Services & Events</h3>
                <span class="accordion-icon">▼</span>
            </div>
            <div class="accordion-content">
                <div class="form-group">
                    <label>Do you host weddings?</label>
                    <input type="text" name="wedding_venue" value="Yes, we have a 500-capacity banquet hall for weddings">
                </div>
                
                <div class="form-group">
                    <label>Do you have conference facilities?</label>
                    <input type="text" name="conference_facilities" value="Yes, 3 conference rooms (50, 100, 250 capacity)">
                </div>
                
                <div class="form-group">
                    <label>Do you offer event planning services?</label>
                    <input type="text" name="event_planning" value="Yes, dedicated events team available">
                </div>
                
                <div class="form-group">
                    <label>Do you have laundry service?</label>
                    <input type="text" name="laundry_service" value="Yes, same-day laundry service available">
                </div>
                
                <div class="form-group">
                    <label>Do you offer business center services?</label>
                    <input type="text" name="business_center" value="Yes, business center with printing, fax, copying">
                </div>
                
                <div class="form-group">
                    <label>Currency exchange available?</label>
                    <input type="text" name="currency_exchange" value="Yes, at front desk during business hours">
                </div>
            </div>
        </div>

        <!-- Section 7: Safety & Security -->
        <div class="accordion-section">
            <div class="accordion-header" onclick="toggleAccordion(this)">
                <h3>🔒 Safety & Security</h3>
                <span class="accordion-icon">▼</span>
            </div>
            <div class="accordion-content">
                <div class="form-group">
                    <label>Do you have 24/7 security?</label>
                    <input type="text" name="security_24_7" value="Yes, trained security guards 24/7">
                </div>
                
                <div class="form-group">
                    <label>Do you have CCTV cameras?</label>
                    <input type="text" name="cctv" value="Yes, CCTV monitoring throughout property">
                </div>
                
                <div class="form-group">
                    <label>Are rooms equipped with safes?</label>
                    <input type="text" name="room_safes" value="Yes, electronic safe in every room">
                </div>
                
                <div class="form-group">
                    <label>Fire safety measures?</label>
                    <input type="text" name="fire_safety" value="Yes, smoke detectors, fire extinguishers, emergency exits">
                </div>
                
                <div class="form-group">
                    <label>Medical services available?</label>
                    <input type="text" name="medical_services" value="Yes, doctor on call 24/7">
                </div>
            </div>
        </div>

        <!-- Section 8: Additional FAQs -->
        <div class="accordion-section">
            <div class="accordion-header" onclick="toggleAccordion(this)">
                <h3>❓ Additional FAQs</h3>
                <span class="accordion-icon">▼</span>
            </div>
            <div class="accordion-content">
                <div class="faq-list" id="custom-faqs">
                    <?php
                    $custom_faqs = isset($hotel_info['custom_faqs']) ? $hotel_info['custom_faqs'] : array();
                    if (empty($custom_faqs)) {
                        $custom_faqs = array(
                            array('question' => 'Do you offer long-term stay discounts?', 'answer' => 'Yes, 10% off for stays over 7 days, 20% off for monthly bookings'),
                            array('question' => 'How many rooms do you have?', 'answer' => 'We have 50 rooms total: 30 Standard, 15 Deluxe, 5 Suites'),
                            array('question' => 'What languages does your staff speak?', 'answer' => 'English, Yoruba, Igbo, and Pidgin English'),
                        );
                    }
                    foreach ($custom_faqs as $index => $faq) {
                        echo '<div class="faq-item">';
                        echo '<input type="text" name="custom_faqs[' . $index . '][question]" placeholder="Question" value="' . esc_attr($faq['question']) . '">';
                        echo '<textarea name="custom_faqs[' . $index . '][answer]" placeholder="Answer">' . esc_textarea($faq['answer']) . '</textarea>';
                        echo '<button type="button" class="btn-remove-faq" onclick="removeFAQ(this)">Remove</button>';
                        echo '</div>';
                    }
                    ?>
                </div>
                <button type="button" class="btn-add-faq" onclick="addFAQ()">+ Add Custom FAQ</button>
            </div>
        </div>

        <button type="submit" class="btn-save">💾 Save Hotel Information</button>
    </form>
</div>

<script>
function toggleAccordion(header) {
    const content = header.nextElementSibling;
    header.classList.toggle('active');
    content.classList.toggle('active');
}

function addFAQ() {
    const faqList = document.getElementById('custom-faqs');
    const index = faqList.children.length;
    const faqItem = document.createElement('div');
    faqItem.className = 'faq-item';
    faqItem.innerHTML = `
        <input type="text" name="custom_faqs[${index}][question]" placeholder="Question">
        <textarea name="custom_faqs[${index}][answer]" placeholder="Answer"></textarea>
        <button type="button" class="btn-remove-faq" onclick="removeFAQ(this)">Remove</button>
    `;
    faqList.appendChild(faqItem);
}

function removeFAQ(button) {
    button.parentElement.remove();
}

jQuery(document).ready(function($) {
    $('#hotel-info-form').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        formData.append('action', 'staydesk_save_hotel_info');
        formData.append('nonce', '<?php echo wp_create_nonce('staydesk_hotel_info'); ?>');
        
        $.ajax({
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#success-message').text(response.data).show();
                    $('#error-message').hide();
                    setTimeout(function() {
                        $('#success-message').fadeOut();
                    }, 5000);
                } else {
                    $('#error-message').text(response.data || 'An error occurred').show();
                    $('#success-message').hide();
                }
            },
            error: function() {
                $('#error-message').text('Network error. Please try again.').show();
                $('#success-message').hide();
            }
        });
    });
    
    // Open first section by default
    document.querySelector('.accordion-header').click();
});
</script>
