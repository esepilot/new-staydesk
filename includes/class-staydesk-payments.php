<?php
/**
 * Payment processing functionality with Paystack integration.
 *
 * @since      1.0.0
 * @package    Staydesk
 */
class Staydesk_Payments {

    private $paystack_secret_key;
    private $paystack_public_key;

    /**
     * Initialize the class.
     */
    public function __construct() {
        // Get Paystack keys from WordPress options
        $this->paystack_secret_key = get_option('staydesk_paystack_secret_key', '');
        $this->paystack_public_key = get_option('staydesk_paystack_public_key', '');

        // AJAX handlers
        add_action('wp_ajax_staydesk_verify_payment', array($this, 'verify_payment'));
        add_action('wp_ajax_staydesk_process_refund', array($this, 'process_refund'));
        add_action('wp_ajax_nopriv_staydesk_paystack_callback', array($this, 'paystack_callback'));
    }

    /**
     * Initialize payment with Paystack.
     */
    public function initialize_payment($email, $amount, $reference, $metadata = array()) {
        $url = "https://api.paystack.co/transaction/initialize";

        $fields = array(
            'email' => $email,
            'amount' => $amount * 100, // Convert to kobo
            'reference' => $reference,
            'currency' => 'NGN',
            'metadata' => $metadata
        );

        $fields_string = json_encode($fields);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Authorization: Bearer " . $this->paystack_secret_key,
            "Content-Type: application/json"
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode($result);
    }

    /**
     * Verify payment.
     */
    public function verify_payment() {
        check_ajax_referer('staydesk_nonce', 'nonce');

        $reference = sanitize_text_field($_POST['reference']);

        $url = "https://api.paystack.co/transaction/verify/" . $reference;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Authorization: Bearer " . $this->paystack_secret_key
        ));

        $result = curl_exec($ch);
        curl_close($ch);

        $response = json_decode($result);

        if ($response && $response->status && $response->data->status === 'success') {
            // Update transaction status
            global $wpdb;
            $table_transactions = $wpdb->prefix . 'staydesk_transactions';
            
            $wpdb->update(
                $table_transactions,
                array(
                    'transaction_status' => 'completed',
                    'payment_method' => 'paystack',
                    'transaction_data' => json_encode($response->data)
                ),
                array('transaction_reference' => $reference)
            );

            // Update booking payment status if it's a booking payment
            $transaction = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM $table_transactions WHERE transaction_reference = %s",
                $reference
            ));

            if ($transaction && $transaction->booking_id) {
                $table_bookings = $wpdb->prefix . 'staydesk_bookings';
                $wpdb->update(
                    $table_bookings,
                    array('payment_status' => 'paid', 'booking_status' => 'confirmed'),
                    array('id' => $transaction->booking_id)
                );
            }

            wp_send_json_success(array(
                'message' => 'Payment verified successfully!',
                'data' => $response->data
            ));
        } else {
            wp_send_json_error(array('message' => 'Payment verification failed.'));
        }
    }

    /**
     * Process refund.
     */
    public function process_refund() {
        check_ajax_referer('staydesk_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => 'Access denied.'));
        }

        global $wpdb;

        $transaction_id = intval($_POST['transaction_id']);
        $amount = floatval($_POST['amount']);
        $reason = sanitize_textarea_field($_POST['reason']);

        $table_transactions = $wpdb->prefix . 'staydesk_transactions';
        $transaction = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_transactions WHERE id = %d",
            $transaction_id
        ));

        if (!$transaction) {
            wp_send_json_error(array('message' => 'Transaction not found.'));
        }

        // Create refund transaction
        $refund_reference = 'REF' . strtoupper(substr(uniqid(), -8));
        
        $wpdb->insert($table_transactions, array(
            'booking_id' => $transaction->booking_id,
            'hotel_id' => $transaction->hotel_id,
            'transaction_type' => 'refund',
            'transaction_reference' => $refund_reference,
            'amount' => $amount,
            'currency' => 'NGN',
            'transaction_status' => 'pending',
            'transaction_data' => json_encode(array('reason' => $reason, 'original_transaction' => $transaction->transaction_reference))
        ));

        // Send refund notification
        $notifications = new Staydesk_Notifications();
        $notifications->send_refund_notification($transaction->booking_id, $amount, $reason);

        wp_send_json_success(array('message' => 'Refund request submitted successfully!'));
    }

    /**
     * Paystack webhook callback.
     */
    public function paystack_callback() {
        $input = @file_get_contents("php://input");
        $event = json_decode($input);

        if ($event && $event->event === 'charge.success') {
            $reference = $event->data->reference;
            
            // Verify and process payment
            $this->verify_payment_internal($reference);
        }

        http_response_code(200);
    }

    /**
     * Internal payment verification.
     */
    private function verify_payment_internal($reference) {
        global $wpdb;

        $url = "https://api.paystack.co/transaction/verify/" . $reference;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Authorization: Bearer " . $this->paystack_secret_key
        ));

        $result = curl_exec($ch);
        curl_close($ch);

        $response = json_decode($result);

        if ($response && $response->status && $response->data->status === 'success') {
            $table_transactions = $wpdb->prefix . 'staydesk_transactions';
            
            $wpdb->update(
                $table_transactions,
                array(
                    'transaction_status' => 'completed',
                    'payment_method' => 'paystack',
                    'transaction_data' => json_encode($response->data)
                ),
                array('transaction_reference' => $reference)
            );
        }
    }

    /**
     * Create transaction record.
     */
    public static function create_transaction($booking_id, $hotel_id, $amount, $type = 'payment') {
        global $wpdb;

        $reference = 'TXN' . strtoupper(substr(uniqid(), -10));

        $table_transactions = $wpdb->prefix . 'staydesk_transactions';
        $wpdb->insert($table_transactions, array(
            'booking_id' => $booking_id,
            'hotel_id' => $hotel_id,
            'transaction_type' => $type,
            'transaction_reference' => $reference,
            'amount' => $amount,
            'currency' => 'NGN',
            'transaction_status' => 'pending'
        ));

        return $reference;
    }
}

new Staydesk_Payments();
