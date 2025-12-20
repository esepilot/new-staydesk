<?php
/**
 * Authentication functionality.
 *
 * @since      1.0.0
 * @package    Staydesk
 */
class Staydesk_Auth {

    /**
     * Initialize the class.
     */
    public function init() {
        // Register shortcodes
        add_shortcode('staydesk_login', array($this, 'render_login_form'));
        add_shortcode('staydesk_signup', array($this, 'render_signup_form'));
        add_shortcode('staydesk_verify_email', array($this, 'render_verify_form'));

        // AJAX handlers (for both logged-in and non-logged-in users)
        add_action('wp_ajax_nopriv_staydesk_login', array($this, 'handle_login'));
        add_action('wp_ajax_staydesk_login', array($this, 'handle_login'));
        add_action('wp_ajax_nopriv_staydesk_signup', array($this, 'handle_signup'));
        add_action('wp_ajax_staydesk_signup', array($this, 'handle_signup'));
        add_action('wp_ajax_nopriv_staydesk_verify_email', array($this, 'verify_email_code'));
        add_action('wp_ajax_staydesk_verify_email', array($this, 'verify_email_code'));
        add_action('wp_ajax_nopriv_staydesk_resend_code', array($this, 'resend_verification_code'));
        add_action('wp_ajax_staydesk_resend_code', array($this, 'resend_verification_code'));
        add_action('wp_ajax_staydesk_logout', array($this, 'handle_logout'));
        
        // Test endpoint to verify AJAX is working
        add_action('wp_ajax_nopriv_staydesk_test', array($this, 'test_ajax'));
        add_action('wp_ajax_staydesk_test', array($this, 'test_ajax'));

        // Handle login redirects
        add_action('template_redirect', array($this, 'check_authentication'));
        
        // Log initialization
        error_log('StayDesk Auth: Class initialized and AJAX handlers registered');
    }
    
    /**
     * Test AJAX endpoint.
     */
    public function test_ajax() {
        error_log('StayDesk Test: AJAX test endpoint hit');
        wp_send_json_success(array('message' => 'AJAX is working!'));
    }

    /**
     * Render login form.
     */
    public function render_login_form() {
        if ($this->is_user_logged_in()) {
            wp_redirect(home_url('/staydesk-dashboard'));
            exit;
        }

        // Enqueue jQuery for the form
        wp_enqueue_script('jquery');

        ob_start();
        include STAYDESK_PLUGIN_DIR . 'templates/login.php';
        return ob_get_clean();
    }

    /**
     * Render signup form.
     */
    public function render_signup_form() {
        if ($this->is_user_logged_in()) {
            wp_redirect(home_url('/staydesk-dashboard'));
            exit;
        }

        // Enqueue jQuery for the form
        wp_enqueue_script('jquery');

        ob_start();
        include STAYDESK_PLUGIN_DIR . 'templates/signup.php';
        return ob_get_clean();
    }

    /**
     * Render email verification form.
     */
    public function render_verify_form() {
        if ($this->is_user_logged_in()) {
            wp_redirect(home_url('/staydesk-dashboard'));
            exit;
        }

        // Enqueue jQuery for the form
        wp_enqueue_script('jquery');

        ob_start();
        include STAYDESK_PLUGIN_DIR . 'templates/verify-email.php';
        return ob_get_clean();
    }

    /**
     * Handle user signup.
     */
    public function handle_signup() {
        // Log the request for debugging
        error_log('StayDesk Signup: Request received');
        error_log('StayDesk Signup: POST data - ' . print_r($_POST, true));
        
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'staydesk_nonce')) {
            error_log('StayDesk Signup: Nonce verification failed');
            error_log('StayDesk Signup: Nonce value - ' . (isset($_POST['nonce']) ? $_POST['nonce'] : 'not set'));
            wp_send_json_error(array('message' => 'Security verification failed. Please refresh the page and try again.'));
            return;
        }
        
        error_log('StayDesk Signup: Nonce verified successfully');

        global $wpdb;

        // Sanitize input
        $hotel_name = isset($_POST['hotel_name']) ? sanitize_text_field($_POST['hotel_name']) : '';
        $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $phone = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : '';

        error_log("StayDesk Signup: Processing for hotel: $hotel_name, email: $email");

        // Validate input
        if (empty($hotel_name) || empty($email) || empty($password)) {
            error_log('StayDesk Signup: Validation failed - missing required fields');
            wp_send_json_error(array('message' => 'All fields are required.'));
            return;
        }

        if (!is_email($email)) {
            error_log('StayDesk Signup: Validation failed - invalid email');
            wp_send_json_error(array('message' => 'Invalid email address.'));
            return;
        }

        // Check if email already exists
        if (email_exists($email)) {
            error_log('StayDesk Signup: Validation failed - email already exists');
            wp_send_json_error(array('message' => 'Email already registered.'));
            return;
        }

        // Create WordPress user
        $user_id = wp_create_user($email, $password, $email);

        if (is_wp_error($user_id)) {
            error_log('StayDesk Signup: User creation failed - ' . $user_id->get_error_message());
            wp_send_json_error(array('message' => $user_id->get_error_message()));
            return;
        }

        error_log("StayDesk Signup: User created with ID: $user_id");

        // Update user meta
        wp_update_user(array(
            'ID' => $user_id,
            'display_name' => $hotel_name,
            'first_name' => $hotel_name
        ));

        // Add hotel role
        $user = new WP_User($user_id);
        $user->set_role('staydesk_hotel');

        // Check if eligible for discount (first 10 hotels)
        $hotel_count = intval(get_option('staydesk_hotel_count', 0));
        $discount_applied = ($hotel_count < 10) ? 1 : 0;

        if ($discount_applied) {
            update_option('staydesk_hotel_count', $hotel_count + 1);
        }

        error_log("StayDesk Signup: Hotel count: $hotel_count, discount applied: $discount_applied");

        // Create hotel record
        $table_hotels = $wpdb->prefix . 'staydesk_hotels';
        $insert_result = $wpdb->insert($table_hotels, array(
            'user_id' => $user_id,
            'hotel_name' => $hotel_name,
            'hotel_email' => $email,
            'hotel_phone' => $phone,
            'email_confirmed' => 0,
            'discount_applied' => $discount_applied,
            'subscription_status' => 'inactive'
        ));

        if ($insert_result === false) {
            error_log('StayDesk Signup: Hotel record creation failed - ' . $wpdb->last_error);
            // Don't fail the signup, just log the error
        } else {
            error_log('StayDesk Signup: Hotel record created successfully');
        }

        // Generate 6-digit verification code
        $verification_code = sprintf('%06d', mt_rand(0, 999999));
        update_user_meta($user_id, 'staydesk_verification_code', $verification_code);
        update_user_meta($user_id, 'staydesk_verification_code_expiry', time() + 3600); // Expires in 1 hour

        // Send confirmation email with code
        $this->send_verification_code_email($email, $hotel_name, $verification_code);

        error_log('StayDesk Signup: Success - verification code sent');

        wp_send_json_success(array(
            'message' => 'Registration successful! Please check your email for the verification code.',
            'redirect' => home_url('/staydesk-verify-email?email=' . urlencode($email))
        ));
    }

    /**
     * Handle user login.
     */
    public function handle_login() {
        error_log('StayDesk Login: Request received');
        error_log('StayDesk Login: POST data - ' . print_r($_POST, true));
        
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'staydesk_nonce')) {
            error_log('StayDesk Login: Nonce verification failed');
            error_log('StayDesk Login: Nonce value - ' . (isset($_POST['nonce']) ? $_POST['nonce'] : 'not set'));
            wp_send_json_error(array('message' => 'Security verification failed. Please refresh the page and try again.'));
            return;
        }
        
        error_log('StayDesk Login: Nonce verified successfully');

        global $wpdb;

        $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $remember = isset($_POST['remember']) ? true : false;

        error_log("StayDesk Login: Attempting login for email: $email");

        if (empty($email) || empty($password)) {
            error_log('StayDesk Login: Validation failed - missing credentials');
            wp_send_json_error(array('message' => 'Email and password are required.'));
            return;
        }

        // Get user
        $user = get_user_by('email', $email);

        if (!$user) {
            error_log('StayDesk Login: User not found');
            wp_send_json_error(array('message' => 'Invalid email or password.'));
            return;
        }

        error_log("StayDesk Login: User found with ID: {$user->ID}");

        // Check password
        if (!wp_check_password($password, $user->data->user_pass, $user->ID)) {
            error_log('StayDesk Login: Password verification failed');
            wp_send_json_error(array('message' => 'Invalid email or password.'));
            return;
        }

        // Check if email is confirmed
        $table_hotels = $wpdb->prefix . 'staydesk_hotels';
        $hotel = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_hotels WHERE user_id = %d",
            $user->ID
        ));

        if ($hotel && !$hotel->email_confirmed) {
            error_log('StayDesk Login: Email not confirmed');
            wp_send_json_error(array('message' => 'Please confirm your email before logging in.'));
            return;
        }

        // Log the user in
        wp_set_current_user($user->ID);
        wp_set_auth_cookie($user->ID, $remember);

        // Set session
        if (!session_id()) {
            session_start();
        }
        $_SESSION['staydesk_user_id'] = $user->ID;
        $_SESSION['staydesk_hotel_id'] = $hotel ? $hotel->id : 0;

        error_log('StayDesk Login: Success');

        wp_send_json_success(array(
            'message' => 'Login successful!',
            'redirect' => home_url('/staydesk-dashboard')
        ));
    }

    /**
     * Handle user logout.
     */
    public function handle_logout() {
        wp_logout();
        
        if (session_id()) {
            session_destroy();
        }

        wp_send_json_success(array(
            'redirect' => home_url('/staydesk-login')
        ));
    }

    /**
     * Verify email with code.
     */
    public function verify_email_code() {
        error_log('StayDesk Verify: Request received');
        
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'staydesk_nonce')) {
            error_log('StayDesk Verify: Nonce verification failed');
            wp_send_json_error(array('message' => 'Security verification failed. Please refresh the page and try again.'));
            return;
        }

        $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
        $code = isset($_POST['code']) ? sanitize_text_field($_POST['code']) : '';

        error_log("StayDesk Verify: Verifying code for email: $email");

        if (empty($email) || empty($code)) {
            wp_send_json_error(array('message' => 'Email and verification code are required.'));
            return;
        }

        // Get user by email
        $user = get_user_by('email', $email);

        if (!$user) {
            error_log('StayDesk Verify: User not found');
            wp_send_json_error(array('message' => 'Invalid email address.'));
            return;
        }

        // Get stored code and expiry
        $stored_code = get_user_meta($user->ID, 'staydesk_verification_code', true);
        $code_expiry = get_user_meta($user->ID, 'staydesk_verification_code_expiry', true);

        error_log("StayDesk Verify: Stored code: $stored_code, Entered code: $code");

        // Check if code has expired
        if (empty($code_expiry) || time() > intval($code_expiry)) {
            error_log('StayDesk Verify: Code expired');
            wp_send_json_error(array('message' => 'Verification code has expired. Please request a new one.'));
            return;
        }

        // Verify code
        if ($code !== $stored_code) {
            error_log('StayDesk Verify: Code mismatch');
            wp_send_json_error(array('message' => 'Invalid verification code. Please check and try again.'));
            return;
        }

        // Update hotel record
        global $wpdb;
        $table_hotels = $wpdb->prefix . 'staydesk_hotels';
        $wpdb->update(
            $table_hotels,
            array('email_confirmed' => 1),
            array('user_id' => $user->ID)
        );

        // Delete verification code
        delete_user_meta($user->ID, 'staydesk_verification_code');
        delete_user_meta($user->ID, 'staydesk_verification_code_expiry');

        error_log('StayDesk Verify: Success');

        wp_send_json_success(array(
            'message' => 'Email verified successfully! You can now log in.',
            'redirect' => home_url('/staydesk-login?verified=1')
        ));
    }

    /**
     * Resend verification code.
     */
    public function resend_verification_code() {
        error_log('StayDesk Resend: Request received');
        
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'staydesk_nonce')) {
            error_log('StayDesk Resend: Nonce verification failed');
            wp_send_json_error(array('message' => 'Security verification failed.'));
            return;
        }

        $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';

        if (empty($email)) {
            wp_send_json_error(array('message' => 'Email is required.'));
            return;
        }

        // Get user by email
        $user = get_user_by('email', $email);

        if (!$user) {
            wp_send_json_error(array('message' => 'Invalid email address.'));
            return;
        }

        // Check if already verified
        global $wpdb;
        $table_hotels = $wpdb->prefix . 'staydesk_hotels';
        $hotel = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_hotels WHERE user_id = %d",
            $user->ID
        ));

        if ($hotel && $hotel->email_confirmed) {
            wp_send_json_error(array('message' => 'Email is already verified. You can log in now.'));
            return;
        }

        // Generate new verification code
        $verification_code = sprintf('%06d', mt_rand(0, 999999));
        update_user_meta($user->ID, 'staydesk_verification_code', $verification_code);
        update_user_meta($user->ID, 'staydesk_verification_code_expiry', time() + 3600); // Expires in 1 hour

        // Send new code
        $hotel_name = $user->display_name;
        $this->send_verification_code_email($email, $hotel_name, $verification_code);

        error_log('StayDesk Resend: New code sent');

        wp_send_json_success(array(
            'message' => 'A new verification code has been sent to your email.'
        ));
    }

    /**
     * Send verification code email.
     */
    private function send_verification_code_email($email, $hotel_name, $code) {
        $subject = 'Your Email Verification Code - StayDesk by BendlessTech';
        
        $message = "
        <html>
        <head>
            <style>
                body { 
                    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; 
                    background-color: #0a0a0a;
                    margin: 0;
                    padding: 0;
                }
                .container { 
                    max-width: 600px; 
                    margin: 40px auto; 
                    background: #1a1a1a;
                    border-radius: 12px;
                    overflow: hidden;
                    border: 1px solid rgba(212, 175, 55, 0.3);
                }
                .header { 
                    background: linear-gradient(135deg, #D4AF37 0%, #FFD700 100%);
                    color: #0a0a0a; 
                    padding: 30px; 
                    text-align: center; 
                }
                .header h1 {
                    margin: 0;
                    font-size: 28px;
                    font-weight: 700;
                }
                .content { 
                    padding: 40px; 
                    color: #E8E8E8;
                }
                .content p {
                    line-height: 1.6;
                    margin: 15px 0;
                }
                .code-box {
                    background: #2a2a2a;
                    border: 2px solid #D4AF37;
                    border-radius: 12px;
                    padding: 30px;
                    text-align: center;
                    margin: 30px 0;
                }
                .code {
                    font-size: 42px;
                    font-weight: 700;
                    letter-spacing: 8px;
                    color: #FFD700;
                    font-family: 'Courier New', monospace;
                }
                .note {
                    background: rgba(212, 175, 55, 0.1);
                    border-left: 4px solid #D4AF37;
                    padding: 15px;
                    margin: 20px 0;
                    border-radius: 4px;
                }
                .footer {
                    padding: 20px 40px;
                    background: #0a0a0a;
                    color: #A0A0A0;
                    font-size: 12px;
                    text-align: center;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>✨ Welcome to StayDesk!</h1>
                </div>
                <div class='content'>
                    <p>Hello <strong>{$hotel_name}</strong>,</p>
                    <p>Thank you for signing up for StayDesk by BendlessTech!</p>
                    <p>To complete your registration, please enter the verification code below on the verification page:</p>
                    
                    <div class='code-box'>
                        <div style='color: #A0A0A0; font-size: 14px; margin-bottom: 10px;'>YOUR VERIFICATION CODE</div>
                        <div class='code'>{$code}</div>
                    </div>
                    
                    <div class='note'>
                        <strong>⏰ Important:</strong> This code will expire in 1 hour for security reasons. If it expires, you can request a new code on the verification page.
                    </div>
                    
                    <p>If you didn't create an account with StayDesk, please ignore this email.</p>
                    
                    <p style='margin-top: 30px;'>Best regards,<br><strong>The BendlessTech Team</strong></p>
                </div>
                <div class='footer'>
                    <p>© 2024 BendlessTech. All rights reserved.</p>
                    <p>Contact us: reach@bendlesstech.com | WhatsApp: 07120018023</p>
                </div>
            </div>
        </body>
        </html>
        ";

        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: StayDesk <reach@bendlesstech.com>'
        );

        wp_mail($email, $subject, $message, $headers);
    }

    /**
     * Check if user is logged in.
     */
    public function is_user_logged_in() {
        return is_user_logged_in() && current_user_can('staydesk_hotel');
    }

    /**
     * Check authentication on protected pages.
     */
    public function check_authentication() {
        global $post;

        if (!$post) {
            return;
        }

        $protected_pages = array('staydesk-dashboard', 'staydesk-bookings', 'staydesk-profile');

        if (in_array($post->post_name, $protected_pages)) {
            if (!$this->is_user_logged_in()) {
                wp_redirect(home_url('/staydesk-login'));
                exit;
            }

            // Check subscription status
            $this->check_subscription_status();
        }
    }

    /**
     * Check subscription status.
     */
    private function check_subscription_status() {
        global $wpdb;

        $user_id = get_current_user_id();
        $table_hotels = $wpdb->prefix . 'staydesk_hotels';
        
        $hotel = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_hotels WHERE user_id = %d",
            $user_id
        ));

        if ($hotel && $hotel->subscription_status === 'expired') {
            wp_redirect(home_url('/staydesk-pricing'));
            exit;
        }
    }
}
