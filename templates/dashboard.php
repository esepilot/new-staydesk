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
            background: #f5f7fa;
            min-height: 100vh;
            padding: 20px;
        }
        
        .dashboard-header {
            background: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .header-left h1 {
            color: #0066CC;
            margin-bottom: 5px;
        }
        
        .header-left p {
            color: #666;
        }
        
        .header-right {
            display: flex;
            gap: 20px;
            align-items: center;
        }
        
        .real-time-clock {
            font-size: 1.5rem;
            font-weight: 600;
            color: #0066CC;
        }
        
        .btn-logout {
            padding: 10px 25px;
            background: #DC3545;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-logout:hover {
            background: #C82333;
            transform: translateY(-2px);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            animation: fadeInUp 0.5s ease-out;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }
        
        .stat-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }
        
        .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            color: #0066CC;
            margin-bottom: 5px;
        }
        
        .stat-label {
            color: #666;
            font-size: 1rem;
        }
        
        .dashboard-sections {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
        }
        
        .section-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }
        
        .section-card:hover {
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }
        
        .section-card h2 {
            color: #333;
            margin-bottom: 20px;
            font-size: 1.5rem;
        }
        
        .section-card p {
            color: #666;
            margin-bottom: 20px;
            line-height: 1.6;
        }
        
        .btn-section {
            display: inline-block;
            padding: 12px 25px;
            background: #0066CC;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-weight: 600;
        }
        
        .btn-section:hover {
            background: #0052A3;
            transform: translateY(-2px);
        }
        
        .subscription-alert {
            background: #FFF3CD;
            border: 1px solid #FFC107;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            color: #856404;
        }
        
        .subscription-alert.expired {
            background: #F8D7DA;
            border-color: #DC3545;
            color: #721C24;
        }
        
        .subscription-alert.active {
            background: #D4EDDA;
            border-color: #28A745;
            color: #155724;
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
                <a href="#" class="btn-section">Manage Rooms</a>
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
        </div>
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
