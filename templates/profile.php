<?php
if (!is_user_logged_in()) {
    wp_redirect(home_url('/staydesk-login'));
    exit;
}

wp_enqueue_script('jquery');

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

$table_subscriptions = $wpdb->prefix . 'staydesk_subscriptions';
$subscription = $wpdb->get_row($wpdb->prepare(
    "SELECT * FROM $table_subscriptions WHERE hotel_id = %d AND status = 'active' ORDER BY expiry_date DESC LIMIT 1",
    $hotel->id
));
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 100%);
            min-height: 100vh;
            padding: 25px;
        }
        
        .profile-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .page-header {
            background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
            padding: 25px;
            border-radius: 18px;
            margin-bottom: 30px;
            box-shadow: 0 6px 30px rgba(0, 0, 0, 0.6);
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid rgba(212, 175, 55, 0.3);
        }
        
        .page-header h1 {
        
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
            font-weight: 800;
            font-size: 1.8rem;
            text-shadow: 0 2px 8px rgba(212, 175, 55, 0.2);
        }
        
        .btn {
            padding: 15px 30px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 700;
            font-size: 14px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .btn-back {
            background: linear-gradient(135deg, #2a2a2a 0%, #1a1a1a 100%);
            color: #F0F0F0;
            border: 1px solid rgba(212, 175, 55, 0.3);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #D4AF37 0%, #FFD700 100%);
            color: #000;
            box-shadow: 0 4px 20px rgba(212, 175, 55, 0.4);
        }
        
        .profile-section {
            background: #1a1a1a;
            padding: 25px;
            border-radius: 18px;
            margin-bottom: 20px;
            box-shadow: 0 6px 30px rgba(0, 0, 0, 0.6);
            border: 1px solid rgba(212, 175, 55, 0.3);
        }
        
        .profile-section h2 {
            color: #D4AF37;
            margin-bottom: 20px;
            font-size: 1.8rem;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            color: #FFFFFF;
            margin-bottom: 8px;
            font-weight: 700;
            font-size: 14px;
        }
        
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 14px;
            background: #2a2a2a;
            border: 1px solid rgba(212, 175, 55, 0.2);
            border-radius: 10px;
            color: #E8E8E8;
            font-size: 1rem;
        }
        
        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #D4AF37;
            box-shadow: 0 0 20px rgba(212, 175, 55, 0.3);
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            border-bottom: 1px solid rgba(212, 175, 55, 0.1);
        }
        
        .info-row label {
            color: #A0A0A0;
            font-weight: 600;
        }
        
        .info-row span {
            color: #E8E8E8;
        }
        
        .chatbot-section {
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.1) 0%, rgba(255, 215, 0, 0.05) 100%);
        }
        
        .embed-code {
            background: #0a0a0a;
            padding: 20px;
            border-radius: 10px;
            margin-top: 15px;
            border: 1px solid rgba(212, 175, 55, 0.3);
        }
        
        .embed-code code {
            color: #D4AF37;
            font-size: 0.9rem;
            word-break: break-all;
            display: block;
        }
        
        .alert {
            padding: 16px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: none;
        }
        
        .alert.show {
            display: block;
        }
        
        .alert-success {
            background: rgba(40, 167, 69, 0.2);
            color: #28A745;
            border: 1px solid rgba(40, 167, 69, 0.3);
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <div class="page-header">
            <h1>Profile & Settings</h1>
        
        @keyframes sparkle {
            0% { background-position: 0% center; }
            100% { background-position: 200% center; }
        }
            <button class="btn btn-back" onclick="window.location.href='<?php echo home_url('/staydesk-dashboard'); ?>'">← Back to Dashboard</button>
        </div>
        
        <div id="alertBox" class="alert"></div>
        
        <?php if (isset($_GET['payment']) && $_GET['payment'] === 'success'): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                showAlert('✅ Payment successful! Your subscription is now active.', 'success');
            });
        </script>
        <?php endif; ?>
        
        <div class="profile-section">
            <h2>Hotel Information</h2>
            <form id="profileForm">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Hotel Name</label>
                        <input type="text" name="hotel_name" value="<?php echo esc_attr($hotel->hotel_name); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Contact Email</label>
                        <input type="email" name="contact_email" value="<?php echo esc_attr($hotel->contact_email); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="tel" name="phone_number" value="<?php echo esc_attr($hotel->phone_number); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Address</label>
                        <input type="text" name="address" value="<?php echo esc_attr($hotel->address); ?>">
                    </div>
                    <div class="form-group">
                        <label>City</label>
                        <input type="text" name="city" value="<?php echo esc_attr($hotel->city); ?>">
                    </div>
                    <div class="form-group">
                        <label>State</label>
                        <input type="text" name="state" value="<?php echo esc_attr($hotel->state); ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Hotel Description</label>
                    <textarea name="description" rows="4"><?php echo esc_textarea($hotel->description ?? ''); ?></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">Update Profile</button>
            </form>
        </div>
        
        <div class="profile-section">
            <h2>Subscription Status</h2>
            <?php if ($subscription): ?>
                <div class="info-row">
                    <label>Plan Type</label>
                    <span><strong><?php echo ucfirst($subscription->plan_type); ?> Plan</strong></span>
                </div>
                <div class="info-row">
                    <label>Status</label>
                    <span><strong style="color: #28A745;">● Active</strong></span>
                </div>
                <div class="info-row">
                    <label>Expiry Date</label>
                    <span><?php echo date('F d, Y', strtotime($subscription->expiry_date)); ?></span>
                </div>
                <div class="info-row">
                    <label>Days Remaining</label>
                    <span><?php 
                        $days = ceil((strtotime($subscription->expiry_date) - time()) / 86400);
                        echo $days . ' days';
                    ?></span>
                </div>
                <div class="info-row">
                    <label>Amount</label>
                    <span>₦<?php echo number_format($subscription->plan_price); ?>/<?php echo $subscription->plan_type === 'monthly' ? 'month' : 'year'; ?></span>
                </div>
                <div class="info-row">
                    <label>Auto-Renew</label>
                    <span><?php echo $subscription->auto_renew ? 'On' : 'Off'; ?></span>
                </div>
                
                <?php if ($subscription->plan_type === 'monthly'): ?>
                <div style="margin-top: 30px; padding: 20px; background: rgba(212, 175, 55, 0.1); border-radius: 10px; border: 1px solid rgba(212, 175, 55, 0.3);">
                    <h3 style="color: #D4AF37; margin-bottom: 15px; font-size: 1.3rem;">Upgrade to Yearly Plan</h3>
                    <p style="color: #E8E8E8; margin-bottom: 15px;">Save ₦59,880 annually with our yearly plan!</p>
                    <p style="color: #B8B8B8; font-size: 0.9rem; margin-bottom: 15px;">
                        Yearly: ₦598,800/year (instead of ₦598,800)<br>
                        10% discount for first 10 hotels!
                    </p>
                    <button class="btn btn-primary" onclick="upgradeToYearly()" style="background: linear-gradient(135deg, #28A745 0%, #20C997 100%);">
                        Upgrade Now - Save ₦59,880!
                    </button>
                </div>
                <?php endif; ?>
                
                <div style="margin-top: 20px;">
                    <button class="btn" onclick="cancelSubscription()" style="background: #dc3545; color: #fff;">
                        Cancel Subscription
                    </button>
                </div>
            <?php else: ?>
                <?php if ($hotel->subscription_status !== 'active'): ?>
                    <p style="color: #A0A0A0;">No active subscription. <a href="<?php echo home_url('/staydesk-pricing'); ?>" style="color: #D4AF37;">Subscribe now</a></p>
                <?php else: ?>
                    <div style="padding: 20px; background: rgba(40, 167, 69, 0.1); border-radius: 10px; border: 1px solid rgba(40, 167, 69, 0.3);">
                        <p style="color: #28A745; font-size: 1.1rem; margin-bottom: 10px;">✓ Subscription Active</p>
                        <p style="color: #B8B8B8; font-size: 0.9rem;">Your subscription is currently active. Detailed information will appear once the system completes synchronization.</p>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        
        <div class="profile-section chatbot-section">
            <h2>Chatbot Widget</h2>
            <p style="color: #E8E8E8; margin-bottom: 20px;">Add this code to your hotel website to enable the AI chatbot:</p>
            
            <div class="embed-code">
                <code>&lt;script src="<?php echo home_url('/wp-content/plugins/staydesk/public/js/chatbot-embed.js'); ?>"&gt;&lt;/script&gt;<br>
&lt;script&gt;StayDeskChatbot.init({hotelId: <?php echo $hotel->id; ?>});&lt;/script&gt;</code>
            </div>
            
            <button class="btn btn-primary" style="margin-top: 20px;" onclick="copyEmbedCode()">Copy Code</button>
        </div>
    </div>
    
    <script>
        function showAlert(message, type) {
            const alert = document.getElementById('alertBox');
            alert.className = 'alert alert-' + type + ' show';
            alert.textContent = message;
            setTimeout(() => {
                alert.classList.remove('show');
            }, 5000);
        }
        
        function copyEmbedCode() {
            const code = document.querySelector('.embed-code code').textContent;
            navigator.clipboard.writeText(code).then(() => {
                showAlert('Embed code copied to clipboard!', 'success');
            });
        }
        
        function upgradeToYearly() {
            if (!confirm('Upgrade to yearly plan and save ₦59,880 per year?')) {
                return;
            }
            
            jQuery.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                data: {
                    action: 'staydesk_upgrade_subscription',
                    nonce: '<?php echo wp_create_nonce('staydesk_nonce'); ?>'
                },
                success: function(response) {
                    if (response.success && response.data.authorization_url) {
                        window.location.href = response.data.authorization_url;
                    } else {
                        showAlert(response.data.message || 'Failed to initialize upgrade', 'error');
                    }
                },
                error: function() {
                    showAlert('An error occurred. Please try again.', 'error');
                }
            });
        }
        
        function cancelSubscription() {
            if (!confirm('Cancel subscription? You will keep access until your current expiry date.')) {
                return;
            }
            
            jQuery.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                data: {
                    action: 'staydesk_cancel_subscription',
                    nonce: '<?php echo wp_create_nonce('staydesk_nonce'); ?>'
                },
                success: function(response) {
                    if (response.success) {
                        showAlert('Subscription cancelled successfully. Access until <?php echo isset($subscription) ? date("F d, Y", strtotime($subscription->expiry_date)) : "expiry"; ?>', 'success');
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    } else {
                        showAlert(response.data.message || 'Failed to cancel subscription', 'error');
                    }
                },
                error: function() {
                    showAlert('An error occurred. Please try again.', 'error');
                }
            });
        }
        
        jQuery(document).ready(function($) {
            $('#profileForm').on('submit', function(e) {
                e.preventDefault();
                
                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    type: 'POST',
                    data: {
                        action: 'staydesk_update_profile',
                        nonce: '<?php echo wp_create_nonce('staydesk_nonce'); ?>',
                        hotel_id: <?php echo $hotel->id; ?>,
                        ...Object.fromEntries(new FormData(this))
                    },
                    success: function(response) {
                        if (response.success) {
                            showAlert('Profile updated successfully!', 'success');
                        } else {
                            showAlert(response.data.message || 'Error updating profile', 'error');
                        }
                    },
                    error: function() {
                        showAlert('An error occurred. Please try again.', 'error');
                    }
                });
            });
        });
    </script>
</body>
</html>
