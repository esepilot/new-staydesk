<?php
/**
 * Hotel Information & FAQ Management Template
 * This template provides a comprehensive form for hotels to fill in their information
 * Pre-filled with Nigerian hotel-specific FAQs
 */

if (!is_user_logged_in()) {
    wp_redirect(home_url('/staydesk-login'));
    exit;
}

global $wpdb;
$user_id = get_current_user_id();
$table_hotels = $wpdb->prefix . 'staydesk_hotels';
$hotel = $wpdb->get_row($wpdb->prepare(
    "SELECT * FROM $table_hotels WHERE user_id = %d",
    $user_id
));

if (!$hotel) {
    echo '<p>Hotel profile not found.</p>';
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
        padding: 30px;
        color: #E8E8E8;
    }
    
    .info-header {
        background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
        padding: 35px;
        border-radius: 18px;
        margin-bottom: 30px;
        box-shadow: 0 6px 30px rgba(0, 0, 0, 0.6);
        border: 1px solid rgba(212, 175, 55, 0.2);
    }
    
    .info-header h1 {
        background: linear-gradient(135deg, #D4AF37 0%, #FFD700 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin: 0 0 10px 0;
        font-size: 2.2rem;
        font-weight: 800;
    }
    
    .info-header p {
        color: #A0A0A0;
        margin: 0;
        font-size: 1.1rem;
    }
    
    .accordion-section {
        background: rgba(26, 26, 26, 0.9);
        border-radius: 12px;
        margin-bottom: 20px;
        border: 1px solid rgba(212, 175, 55, 0.15);
        overflow: hidden;
    }
    
    .accordion-header {
        padding: 20px 25px;
        cursor: pointer;
        background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
        border-bottom: 1px solid rgba(212, 175, 55, 0.2);
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.3s ease;
    }
    
    .accordion-header:hover {
        background: linear-gradient(135deg, #2a2a2a 0%, #3a3a3a 100%);
    }
    
    .accordion-header h3 {
        margin: 0;
        color: #FFD700;
        font-size: 1.3rem;
        font-weight: 700;
    }
    
    .accordion-icon {
        font-size: 1.5rem;
        transition: transform 0.3s ease;
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
        <p>Fill in your hotel details so guests get accurate information from the chatbot</p>
    </div>
    
    <div id="success-message" class="success-message"></div>
    <div id="error-message" class="error-message"></div>
    
    <form id="hotel-info-form">
