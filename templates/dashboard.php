<?php
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

$dashboard_data = Staydesk_Dashboard::get_dashboard_data($hotel->id);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .staydesk-dashboard {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 100%);
            min-height: 100vh;
            padding: 25px;
        }
        
        .dashboard-header {
            background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
            padding: 25px;
            border-radius: 18px;
            margin-bottom: 30px;
            box-shadow: 0 6px 30px rgba(0, 0, 0, 0.6);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            border: 1px solid rgba(212, 175, 55, 0.3);
        }
        
        .header-left h1 {
            background: linear-gradient(90deg, #FFD700 0%, #4FC3F7 25%, #FFD700 50%, #64B5F6 75%, #FFD700 100%);
            background-size: 200% auto;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: sparkle 3s linear infinite;
            margin-bottom: 8px;
            font-weight: 800;
            letter-spacing: -0.5px;
            font-size: 1.8rem;
            text-shadow: 0 2px 8px rgba(212, 175, 55, 0.2);
        }
        
        @keyframes sparkle {
            0% { background-position: 0% center; }
            100% { background-position: 200% center; }
        }
        
        .header-left p {
            color: #B8B8B8;
            font-size: 14px;
            line-height: 1.6;
        }
        
        .header-right {
            display: flex;
            gap: 25px;
            align-items: center;
        }
        
        .real-time-clock {
            font-size: 1.6rem;
            font-weight: 700;
            background: linear-gradient(135deg, #D4AF37 0%, #FFD700 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .btn-logout {
            padding: 12px 28px;
            background: linear-gradient(135deg, #DC3545 0%, #C82333 100%);
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 700;
            transition: all 0.3s ease;
            box-shadow: 0 4px 20px rgba(220, 53, 69, 0.3);
        }
        
        .btn-logout:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 35px rgba(220, 53, 69, 0.5);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
            padding: 25px;
            border-radius: 18px;
            box-shadow: 0 6px 30px rgba(0, 0, 0, 0.5);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            animation: fadeInUp 0.6s ease-out;
            border: 1px solid rgba(212, 175, 55, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(135deg, #D4AF37, #FFD700);
            border-radius: 18px;
            opacity: 0;
            transition: opacity 0.5s ease;
            z-index: -1;
        }
        
        .stat-card:hover::before {
            opacity: 0.3;
        }
        
        .stat-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 60px rgba(212, 175, 55, 0.4);
            background: linear-gradient(135deg, #2a2a2a 0%, #3a3a3a 100%);
        }
        
        .stat-icon {
            font-size: 2.2rem;
            margin-bottom: 15px;
            filter: drop-shadow(0 0 15px rgba(212, 175, 55, 0.5));
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: 900;
            background: linear-gradient(135deg, #D4AF37 0%, #FFD700 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 8px;
            text-shadow: 0 2px 8px rgba(255, 215, 0, 0.3);
        }
        }
        
        .stat-label {
            color: #A0A0A0;
            font-size: 1.05rem;
            font-weight: 500;
        }
        
        .dashboard-sections {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 28px;
        }
        
        .section-card {
            background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
            padding: 35px;
            border-radius: 18px;
            box-shadow: 0 6px 30px rgba(0, 0, 0, 0.5);
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(212, 175, 55, 0.2);
            position: relative;
        }
        
        .section-card::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(135deg, #D4AF37, #FFD700);
            border-radius: 18px;
            opacity: 0;
            transition: opacity 0.5s ease;
            z-index: -1;
        }
        
        .section-card:hover::before {
            opacity: 0.2;
        }
        
        .section-card:hover {
            box-shadow: 0 15px 50px rgba(212, 175, 55, 0.3);
            transform: translateY(-6px);
        }
        
        .section-card h2 {
            color: #E8E8E8;
            margin-bottom: 22px;
            font-size: 1.6rem;
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        
        .section-card p {
            color: #A0A0A0;
            margin-bottom: 25px;
            line-height: 1.7;
        }
        
        .btn-section {
            display: inline-block;
            padding: 14px 32px;
            background: linear-gradient(135deg, #D4AF37 0%, #FFD700 100%);
            color: #000;
            text-decoration: none;
            border-radius: 10px;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            font-weight: 700;
            letter-spacing: 0.3px;
            box-shadow: 0 4px 20px rgba(212, 175, 55, 0.3);
        }
        
        .btn-section:hover {
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 8px 35px rgba(212, 175, 55, 0.5);
        }
        
        .subscription-alert {
            background: linear-gradient(135deg, rgba(255, 193, 7, 0.15) 0%, rgba(255, 215, 0, 0.15) 100%);
            border: 1px solid rgba(255, 193, 7, 0.4);
            padding: 22px;
            border-radius: 12px;
            margin-bottom: 35px;
            color: #FFD700;
            font-weight: 600;
        }
        
        .subscription-alert.expired {
            background: linear-gradient(135deg, rgba(220, 53, 69, 0.15) 0%, rgba(200, 35, 51, 0.15) 100%);
            border-color: rgba(220, 53, 69, 0.4);
            color: #FF6B7A;
        }
        
        .subscription-alert.active {
            background: linear-gradient(135deg, rgba(40, 167, 69, 0.15) 0%, rgba(35, 150, 60, 0.15) 100%);
            border-color: rgba(40, 167, 69, 0.4);
            color: #4ADE80;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @media (max-width: 768px) {
            .dashboard-header {
                flex-direction: column;
                text-align: center;
            }
            
            .header-right {
                margin-top: 20px;
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="staydesk-dashboard">
        <div class="dashboard-header">
            <div class="header-left">
                <h1>Welcome, <?php echo esc_html($hotel->hotel_name); ?>!</h1>
                <p>Here's what's happening with your hotel today</p>
            </div>
            <div class="header-right">
                <div class="real-time-clock" id="clock"></div>
                <button class="btn-logout" id="logout-btn">Logout</button>
            </div>
        </div>
        
        <?php if ($hotel->subscription_status === 'expired'): ?>
            <div class="subscription-alert expired">
                ⚠️ Your subscription has expired. Please <a href="<?php echo home_url('/staydesk-pricing'); ?>">renew your subscription</a> to continue using StayDesk.
            </div>
        <?php elseif ($hotel->subscription_status === 'active'): ?>
            <div class="subscription-alert active">
                ✅ Your subscription is active until <?php echo date('F j, Y', strtotime($hotel->subscription_expiry)); ?>
            </div>
        <?php endif; ?>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">🏨</div>
                <div class="stat-value"><?php echo number_format($dashboard_data['total_bookings']); ?></div>
                <div class="stat-label">Total Bookings</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">⏳</div>
                <div class="stat-value"><?php echo number_format($dashboard_data['pending_bookings']); ?></div>
                <div class="stat-label">Pending Bookings</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">💰</div>
                <div class="stat-value">₦<?php echo number_format($dashboard_data['total_revenue'], 2); ?></div>
                <div class="stat-label">Total Revenue</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">🛏️</div>
                <div class="stat-value"><?php echo $dashboard_data['available_rooms']; ?>/<?php echo $dashboard_data['total_rooms']; ?></div>
                <div class="stat-label">Available Rooms</div>
            </div>
        </div>
        
        <div class="dashboard-sections">
            <div class="section-card">
                <h2>📋 Bookings Management</h2>
                <p>View and manage all your hotel bookings, check-ins, and check-outs.</p>
                <a href="<?php echo home_url('/staydesk-bookings'); ?>" class="btn-section">Manage Bookings</a>
            </div>
            
            <div class="section-card">
                <h2>🛏️ Rooms Management</h2>
                <p>Add, edit, or remove rooms. Set pricing and availability.</p>
                <a href="<?php echo home_url('/staydesk-rooms'); ?>" class="btn-section">Manage Rooms</a>
            </div>
            
            <div class="section-card">
                <h2>💳 Payment Verification</h2>
                <p>Verify payments and track transactions for all bookings.</p>
                <a href="#" class="btn-section">Verify Payments</a>
            </div>
            
            <div class="section-card">
                <h2>↩️ Refund Management</h2>
                <p>Process refund requests and manage cancellations.</p>
                <a href="#" class="btn-section">Manage Refunds</a>
            </div>
            
            <div class="section-card">
                <h2>💬 Guest Enquiries</h2>
                <p>View and respond to guest messages from the chatbot.</p>
                <a href="#" class="btn-section">View Enquiries (<?php echo $dashboard_data['enquiries_count']; ?>)</a>
            </div>
            
            <div class="section-card">
                <h2>⚙️ Profile & Settings</h2>
                <p>Update your hotel profile, account details, and preferences.</p>
                <a href="<?php echo home_url('/staydesk-profile'); ?>" class="btn-section">Edit Profile</a>
            </div>
            
            <div class="section-card">
                <h2>💎 Subscription</h2>
                <p>Manage your subscription plan and billing.</p>
                <a href="<?php echo home_url('/staydesk-pricing'); ?>" class="btn-section">View Subscription</a>
            </div>
            
            <div class="section-card">
                <h2>ℹ️ Hotel Information</h2>
                <p>Manage hotel FAQs and information for the AI chatbot.</p>
                <a href="<?php echo home_url('/staydesk-hotel-info'); ?>" class="btn-section">Manage Hotel Info</a>
            </div>
        </div>
    </div>
    
    <?php 
    // Render embedded chatbot widget on dashboard
    Staydesk_Chatbot::render_dashboard_widget($hotel->id);
    ?>
    
    <!-- WhatsApp Support Widget -->
    <div id="whatsapp-support-widget" style="position: fixed; bottom: 20px; right: 20px; z-index: 1000;">
        <style>
            #whatsapp-support-widget .whatsapp-toggle {
                width: 50px;
                height: 50px;
                border-radius: 50%;
                background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
                border: none;
                cursor: pointer;
                box-shadow: 0 4px 15px rgba(37, 211, 102, 0.4);
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 26px;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                text-decoration: none;
                color: #FFFFFF;
            }
            
            #whatsapp-support-widget .whatsapp-toggle:hover {
                transform: scale(1.1);
                box-shadow: 0 6px 20px rgba(37, 211, 102, 0.6);
            }
            
            #whatsapp-support-widget .whatsapp-tooltip {
                position: absolute;
                bottom: 10px;
                right: 65px;
                background: #1a1a1a;
                color: #FFFFFF;
                padding: 8px 12px;
                border-radius: 8px;
                font-size: 13px;
                white-space: nowrap;
                opacity: 0;
                pointer-events: none;
                transition: opacity 0.3s;
                border: 1px solid rgba(37, 211, 102, 0.3);
            }
            
            #whatsapp-support-widget:hover .whatsapp-tooltip {
                opacity: 1;
            }
        </style>
        <span class="whatsapp-tooltip">Contact Support</span>
        <a href="https://wa.me/2348012345678?text=Hello,%20I%20need%20help%20with%20StayDesk" 
           target="_blank" 
           class="whatsapp-toggle" 
           title="WhatsApp Support">
            💬
        </a>
    </div>
    
    <script>
        // Real-time clock
        function updateClock() {
            var now = new Date();
            var hours = now.getHours().toString().padStart(2, '0');
            var minutes = now.getMinutes().toString().padStart(2, '0');
            var seconds = now.getSeconds().toString().padStart(2, '0');
            document.getElementById('clock').textContent = hours + ':' + minutes + ':' + seconds;
        }
        
        setInterval(updateClock, 1000);
        updateClock();
        
        // Logout
        jQuery(document).ready(function($) {
            $('#logout-btn').on('click', function() {
                $.ajax({
                    url: staydesk_ajax.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'staydesk_logout',
                        nonce: staydesk_ajax.nonce
                    },
                    success: function(response) {
                        if (response.success) {
                            window.location.href = response.data.redirect;
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>
