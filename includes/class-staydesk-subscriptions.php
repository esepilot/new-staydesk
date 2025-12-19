<?php
/**
 * Subscription management functionality.
 *
 * @since      1.0.0
 * @package    Staydesk
 */
class Staydesk_Subscriptions {

    const MONTHLY_PRICE = 49900; // ₦49,900
    const YEARLY_PRICE = 598800; // ₦598,800
    const DISCOUNT_PERCENTAGE = 10; // 10% for first 10 hotels

    /**
     * Initialize the class.
     */
    public function __construct() {
        add_shortcode('staydesk_pricing', array($this, 'render_pricing'));

        // AJAX handlers
        add_action('wp_ajax_staydesk_subscribe', array($this, 'subscribe'));
        add_action('wp_ajax_staydesk_cancel_subscription', array($this, 'cancel_subscription'));

        // Cron job for checking expired subscriptions
        add_action('staydesk_check_expired_subscriptions', array($this, 'check_expired_subscriptions'));
        
        if (!wp_next_scheduled('staydesk_check_expired_subscriptions')) {
            wp_schedule_event(time(), 'daily', 'staydesk_check_expired_subscriptions');
        }
    }

    /**
     * Render pricing page.
     */
    public function render_pricing() {
        ob_start();
        include STAYDESK_PLUGIN_DIR . 'templates/pricing.php';
        return ob_get_clean();
    }

    /**
     * Subscribe to a plan.
     */
    public function subscribe() {
        check_ajax_referer('staydesk_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => 'Please login to subscribe.'));
        }

        global $wpdb;

        $user_id = get_current_user_id();
        $plan_type = sanitize_text_field($_POST['plan_type']); // 'monthly' or 'yearly'

        // Get hotel
        $table_hotels = $wpdb->prefix . 'staydesk_hotels';
        $hotel = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_hotels WHERE user_id = %d",
            $user_id
        ));

        if (!$hotel) {
            wp_send_json_error(array('message' => 'Hotel not found.'));
        }

        // Calculate price
        $base_price = ($plan_type === 'monthly') ? self::MONTHLY_PRICE : self::YEARLY_PRICE;
        $discount = 0;

        if ($hotel->discount_applied) {
            $discount = $base_price * (self::DISCOUNT_PERCENTAGE / 100);
        }

        $final_price = $base_price - $discount;

        // Calculate expiry date
        $start_date = current_time('mysql');
        $expiry_date = ($plan_type === 'monthly') 
            ? date('Y-m-d H:i:s', strtotime('+1 month')) 
            : date('Y-m-d H:i:s', strtotime('+1 year'));

        // Create subscription record
        $table_subscriptions = $wpdb->prefix . 'staydesk_subscriptions';
        $wpdb->insert($table_subscriptions, array(
            'hotel_id' => $hotel->id,
            'plan_type' => $plan_type,
            'plan_price' => $final_price,
            'discount_percentage' => $hotel->discount_applied ? self::DISCOUNT_PERCENTAGE : 0,
            'start_date' => $start_date,
            'expiry_date' => $expiry_date,
            'status' => 'pending'
        ));

        // Create transaction
        $reference = Staydesk_Payments::create_transaction(null, $hotel->id, $final_price, 'subscription');

        // Initialize payment
        $payments = new Staydesk_Payments();
        $payment_init = $payments->initialize_payment(
            $hotel->hotel_email,
            $final_price,
            $reference,
            array(
                'hotel_id' => $hotel->id,
                'plan_type' => $plan_type,
                'subscription' => true
            )
        );

        if ($payment_init && $payment_init->status) {
            wp_send_json_success(array(
                'message' => 'Subscription initiated!',
                'authorization_url' => $payment_init->data->authorization_url,
                'reference' => $reference
            ));
        } else {
            wp_send_json_error(array('message' => 'Failed to initialize payment.'));
        }
    }

    /**
     * Cancel subscription.
     */
    public function cancel_subscription() {
        check_ajax_referer('staydesk_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => 'Access denied.'));
        }

        global $wpdb;

        $user_id = get_current_user_id();
        
        $table_hotels = $wpdb->prefix . 'staydesk_hotels';
        $hotel = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_hotels WHERE user_id = %d",
            $user_id
        ));

        if (!$hotel) {
            wp_send_json_error(array('message' => 'Hotel not found.'));
        }

        // Update subscription status
        $table_subscriptions = $wpdb->prefix . 'staydesk_subscriptions';
        $wpdb->update(
            $table_subscriptions,
            array('status' => 'cancelled', 'auto_renew' => 0),
            array('hotel_id' => $hotel->id, 'status' => 'active')
        );

        // Update hotel subscription status
        $wpdb->update(
            $table_hotels,
            array('subscription_status' => 'cancelled'),
            array('id' => $hotel->id)
        );

        wp_send_json_success(array('message' => 'Subscription cancelled successfully.'));
    }

    /**
     * Check for expired subscriptions (cron job).
     */
    public function check_expired_subscriptions() {
        global $wpdb;

        $table_hotels = $wpdb->prefix . 'staydesk_hotels';
        $current_time = current_time('mysql');

        // Get expired subscriptions
        $expired_hotels = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_hotels 
             WHERE subscription_status = 'active' 
             AND subscription_expiry < %s",
            $current_time
        ));

        foreach ($expired_hotels as $hotel) {
            // Update hotel status
            $wpdb->update(
                $table_hotels,
                array('subscription_status' => 'expired'),
                array('id' => $hotel->id)
            );

            // Update subscription record
            $table_subscriptions = $wpdb->prefix . 'staydesk_subscriptions';
            $wpdb->update(
                $table_subscriptions,
                array('status' => 'expired'),
                array('hotel_id' => $hotel->id, 'status' => 'active')
            );

            // Send notification
            $notifications = new Staydesk_Notifications();
            $notifications->send_subscription_expiry_notice($hotel->id);
        }
    }

    /**
     * Activate subscription after payment.
     */
    public static function activate_subscription($hotel_id, $plan_type) {
        global $wpdb;

        $table_hotels = $wpdb->prefix . 'staydesk_hotels';
        
        $expiry_date = ($plan_type === 'monthly') 
            ? date('Y-m-d H:i:s', strtotime('+1 month')) 
            : date('Y-m-d H:i:s', strtotime('+1 year'));

        $wpdb->update(
            $table_hotels,
            array(
                'subscription_status' => 'active',
                'subscription_plan' => $plan_type,
                'subscription_expiry' => $expiry_date
            ),
            array('id' => $hotel_id)
        );

        // Update subscription record
        $table_subscriptions = $wpdb->prefix . 'staydesk_subscriptions';
        $wpdb->update(
            $table_subscriptions,
            array('status' => 'active'),
            array('hotel_id' => $hotel_id, 'status' => 'pending')
        );
    }

    /**
     * Check if hotel subscription is active.
     */
    public static function is_subscription_active($hotel_id) {
        global $wpdb;

        $table_hotels = $wpdb->prefix . 'staydesk_hotels';
        $hotel = $wpdb->get_row($wpdb->prepare(
            "SELECT subscription_status, subscription_expiry FROM $table_hotels WHERE id = %d",
            $hotel_id
        ));

        if (!$hotel) {
            return false;
        }

        if ($hotel->subscription_status !== 'active') {
            return false;
        }

        // Check if not expired
        $current_time = current_time('mysql');
        if ($hotel->subscription_expiry && $hotel->subscription_expiry < $current_time) {
            return false;
        }

        return true;
    }
}

new Staydesk_Subscriptions();
