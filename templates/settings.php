<?php
/**
 * Settings Page Template
 */

if (!is_user_logged_in() || !Staydesk_Roles::can('manage_settings')) {
    echo '<p>Please <a href="' . home_url('/staydesk-login') . '">login</a> with admin privileges to access this page.</p>';
    return;
}

// Get current settings
global $wpdb;
$settings_table = $wpdb->prefix . 'staydesk_settings';

$test_mode = $wpdb->get_var($wpdb->prepare("SELECT setting_value FROM $settings_table WHERE setting_key = %s", 'paystack_test_mode')) ?: 'yes';
$test_public_key = $wpdb->get_var($wpdb->prepare("SELECT setting_value FROM $settings_table WHERE setting_key = %s", 'paystack_test_public_key')) ?: '';
$test_secret_key = $wpdb->get_var($wpdb->prepare("SELECT setting_value FROM $settings_table WHERE setting_key = %s", 'paystack_test_secret_key')) ?: '';
$live_public_key = $wpdb->get_var($wpdb->prepare("SELECT setting_value FROM $settings_table WHERE setting_key = %s", 'paystack_live_public_key')) ?: '';
$live_secret_key = $wpdb->get_var($wpdb->prepare("SELECT setting_value FROM $settings_table WHERE setting_key = %s", 'paystack_live_secret_key')) ?: '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_settings'])) {
    check_admin_referer('staydesk_settings_nonce');
    
    $settings_to_save = array(
        'paystack_test_mode' => sanitize_text_field($_POST['test_mode']),
        'paystack_test_public_key' => sanitize_text_field($_POST['test_public_key']),
        'paystack_test_secret_key' => sanitize_text_field($_POST['test_secret_key']),
        'paystack_live_public_key' => sanitize_text_field($_POST['live_public_key']),
        'paystack_live_secret_key' => sanitize_text_field($_POST['live_secret_key'])
    );
    
    foreach ($settings_to_save as $key => $value) {
        $existing = $wpdb->get_var($wpdb->prepare("SELECT id FROM $settings_table WHERE setting_key = %s", $key));
        
        if ($existing) {
            $wpdb->update($settings_table, array('setting_value' => $value), array('setting_key' => $key));
        } else {
            $wpdb->insert($settings_table, array('setting_key' => $key, 'setting_value' => $value));
        }
    }
    
    echo '<div class="success-message">Settings saved successfully!</div>';
}
?>

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    
    body {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        background: #0a0a0a;
        color: #F0F0F0;
        font-size: 15px;
        line-height: 1.6;
    }
    
    .settings-container {
        max-width: 800px;
        margin: 30px auto;
        padding: 25px;
        background: #1a1a1a;
        border-radius: 18px;
        border: 1px solid rgba(212, 175, 55, 0.3);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.5);
    }
    
    h1 {
        font-size: 2.2rem;
        background: linear-gradient(135deg, #D4AF37, #FFD700);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 30px;
        text-shadow: 0 2px 12px rgba(212, 175, 55, 0.3);
    }
    
    h2 {
        font-size: 1.8rem;
        color: #D4AF37;
        margin: 30px 0 20px 0;
        padding-bottom: 10px;
        border-bottom: 2px solid rgba(212, 175, 55, 0.3);
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    label {
        display: block;
        margin-bottom: 8px;
        color: #FFFFFF;
        font-weight: 700;
        font-size: 15px;
    }
    
    input[type="text"],
    input[type="password"] {
        width: 100%;
        padding: 15px;
        background: #2a2a2a;
        border: 2px solid rgba(212, 175, 55, 0.3);
        border-radius: 12px;
        color: #FFFFFF;
        font-size: 15px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    input[type="text"]:focus,
    input[type="password"]:focus {
        outline: none;
        border-color: #D4AF37;
        box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.2);
    }
    
    .mode-toggle {
        display: flex;
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .mode-option {
        flex: 1;
        padding: 15px;
        background: #2a2a2a;
        border: 2px solid rgba(212, 175, 55, 0.3);
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.3s;
        text-align: center;
    }
    
    .mode-option.active {
        background: linear-gradient(135deg, rgba(212, 175, 55, 0.2), rgba(255, 215, 0, 0.2));
        border-color: #D4AF37;
    }
    
    .mode-option input[type="radio"] {
        margin-right: 8px;
    }
    
    .btn-primary {
        padding: 15px 30px;
        background: linear-gradient(135deg, #D4AF37, #FFD700);
        color: #0a0a0a;
        border: none;
        border-radius: 12px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 15px rgba(212, 175, 55, 0.4);
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(212, 175, 55, 0.6);
    }
    
    .btn-secondary {
        padding: 15px 30px;
        background: #2a2a2a;
        color: #D4AF37;
        border: 2px solid #D4AF37;
        border-radius: 12px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        margin-left: 10px;
    }
    
    .btn-secondary:hover {
        background: rgba(212, 175, 55, 0.1);
    }
    
    .success-message {
        padding: 15px;
        background: rgba(0, 200, 100, 0.1);
        border: 2px solid #00C864;
        border-radius: 12px;
        color: #00FF80;
        margin-bottom: 20px;
    }
    
    .info-box {
        padding: 15px;
        background: rgba(212, 175, 55, 0.1);
        border: 2px solid rgba(212, 175, 55, 0.3);
        border-radius: 12px;
        margin-bottom: 20px;
    }
    
    .back-link {
        display: inline-block;
        margin-bottom: 20px;
        color: #D4AF37;
        text-decoration: none;
        transition: all 0.3s;
    }
    
    .back-link:hover {
        color: #FFD700;
    }
</style>

<div class="settings-container">
    <a href="<?php echo home_url('/staydesk-admin-dashboard'); ?>" class="back-link">← Back to Dashboard</a>
    
    <h1>⚙️ StayDesk Settings</h1>
    
    <form method="POST" action="">
        <?php wp_nonce_field('staydesk_settings_nonce'); ?>
        
        <h2>🔑 Paystack Configuration</h2>
        
        <div class="info-box">
            <strong>Test Mode:</strong> Use test keys for development and testing<br>
            <strong>Live Mode:</strong> Use live keys for production payments<br>
            <small>Get your API keys from your Paystack Dashboard at https://dashboard.paystack.com</small>
        </div>
        
        <div class="mode-toggle">
            <label class="mode-option <?php echo $test_mode === 'yes' ? 'active' : ''; ?>">
                <input type="radio" name="test_mode" value="yes" <?php checked($test_mode, 'yes'); ?>>
                Test Mode
            </label>
            <label class="mode-option <?php echo $test_mode === 'no' ? 'active' : ''; ?>">
                <input type="radio" name="test_mode" value="no" <?php checked($test_mode, 'no'); ?>>
                Live Mode
            </label>
        </div>
        
        <h2>🧪 Test Keys (for development)</h2>
        
        <div class="form-group">
            <label for="test_public_key">Test Public Key</label>
            <input type="text" id="test_public_key" name="test_public_key" value="<?php echo esc_attr($test_public_key); ?>" placeholder="pk_test_...">
        </div>
        
        <div class="form-group">
            <label for="test_secret_key">Test Secret Key</label>
            <input type="password" id="test_secret_key" name="test_secret_key" value="<?php echo esc_attr($test_secret_key); ?>" placeholder="sk_test_...">
        </div>
        
        <h2>🚀 Live Keys (for production)</h2>
        
        <div class="form-group">
            <label for="live_public_key">Live Public Key</label>
            <input type="text" id="live_public_key" name="live_public_key" value="<?php echo esc_attr($live_public_key); ?>" placeholder="pk_live_...">
        </div>
        
        <div class="form-group">
            <label for="live_secret_key">Live Secret Key</label>
            <input type="password" id="live_secret_key" name="live_secret_key" value="<?php echo esc_attr($live_secret_key); ?>" placeholder="sk_live_...">
        </div>
        
        <div style="margin-top: 30px;">
            <button type="submit" name="save_settings" class="btn-primary">💾 Save Settings</button>
            <button type="button" class="btn-secondary" onclick="testPaystackConnection()">🔌 Test Connection</button>
        </div>
    </form>
</div>

<script>
function testPaystackConnection() {
    alert('Testing Paystack connection... (Feature coming soon)');
}

// Toggle mode styling
document.querySelectorAll('.mode-option input[type="radio"]').forEach(radio => {
    radio.addEventListener('change', function() {
        document.querySelectorAll('.mode-option').forEach(opt => opt.classList.remove('active'));
        this.closest('.mode-option').classList.add('active');
    });
});
</script>
