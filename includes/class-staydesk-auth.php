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

        // AJAX handlers (for both logged-in and non-logged-in users)
        add_action('wp_ajax_nopriv_staydesk_login', array($this, 'handle_login'));
        add_action('wp_ajax_staydesk_login', array($this, 'handle_login'));
        add_action('wp_ajax_nopriv_staydesk_signup', array($this, 'handle_signup'));
        add_action('wp_ajax_staydesk_signup', array($this, 'handle_signup'));
        add_action('wp_ajax_nopriv_staydesk_confirm_email', array($this, 'confirm_email'));
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

        ob_start();
        include STAYDESK_PLUGIN_DIR . 'templates/signup.php';
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

        // Generate confirmation token
        $token = wp_generate_password(32, false);
        update_user_meta($user_id, 'staydesk_email_token', $token);

        // Send confirmation email
        $this->send_confirmation_email($email, $hotel_name, $token);

        error_log('StayDesk Signup: Success - confirmation email sent');

        wp_send_json_success(array(
            'message' => 'Registration successful! Please check your email to confirm your account.',
            'redirect' => home_url('/staydesk-login')
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
     * Confirm email address.
     */
    public function confirm_email() {
        $token = sanitize_text_field($_GET['token']);
        
        if (empty($token)) {
            wp_die('Invalid confirmation link.');
        }

        // Find user with this token
        $users = get_users(array(
            'meta_key' => 'staydesk_email_token',
            'meta_value' => $token
        ));

        if (empty($users)) {
            wp_die('Invalid or expired confirmation link.');
        }

        $user = $users[0];

        // Update hotel record
        global $wpdb;
        $table_hotels = $wpdb->prefix . 'staydesk_hotels';
        $wpdb->update(
            $table_hotels,
            array('email_confirmed' => 1),
            array('user_id' => $user->ID)
        );

        // Delete token
        delete_user_meta($user->ID, 'staydesk_email_token');

        // Redirect to login
        wp_redirect(add_query_arg('confirmed', '1', home_url('/staydesk-login')));
        exit;
    }

    /**
     * Send confirmation email.
     */
    private function send_confirmation_email($email, $hotel_name, $token) {
        $confirm_url = add_query_arg(array(
            'action' => 'staydesk_confirm_email',
            'token' => $token
        ), admin_url('admin-ajax.php'));

        $subject = 'Confirm Your Email - StayDesk by BendlessTech';
        
        $message = "
        <html>
        <head>
            <style>
                body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #0066CC; color: white; padding: 20px; text-align: center; }
                .content { padding: 30px; background: #f8f9fa; }
                .button { background: #0066CC; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 20px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Welcome to StayDesk!</h1>
                </div>
                <div class='content'>
                    <p>Hello {$hotel_name},</p>
                    <p>Thank you for signing up for StayDesk by BendlessTech!</p>
                    <p>Please confirm your email address by clicking the button below:</p>
                    <a href='{$confirm_url}' class='button'>Confirm Email Address</a>
                    <p>If the button doesn't work, copy and paste this link into your browser:</p>
                    <p>{$confirm_url}</p>
                    <p>Best regards,<br>The BendlessTech Team</p>
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
