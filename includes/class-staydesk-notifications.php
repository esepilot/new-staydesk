<?php
/**
 * Notifications functionality for email and WhatsApp.
 *
 * @since      1.0.0
 * @package    Staydesk
 */
class Staydesk_Notifications {

    private $from_email = 'reach@bendlesstech.com';
    private $from_name = 'StayDesk by BendlessTech';
    private $whatsapp_number = '2347120018023'; // 07120018023

    /**
     * Send booking confirmation email.
     */
    public function send_booking_confirmation($booking_id) {
        global $wpdb;

        $table_bookings = $wpdb->prefix . 'staydesk_bookings';
        $booking = $wpdb->get_row($wpdb->prepare(
            "SELECT b.*, g.guest_name, g.guest_email, g.guest_phone, h.hotel_name, r.room_name
             FROM $table_bookings b
             LEFT JOIN {$wpdb->prefix}staydesk_guests g ON b.guest_id = g.id
             LEFT JOIN {$wpdb->prefix}staydesk_hotels h ON b.hotel_id = h.id
             LEFT JOIN {$wpdb->prefix}staydesk_rooms r ON b.room_id = r.id
             WHERE b.id = %d",
            $booking_id
        ));

        if (!$booking) {
            return false;
        }

        // Email to guest
        $subject = 'Booking Confirmation - ' . $booking->booking_reference;
        
        $message = "
        <html>
        <head>
            <style>
                body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #0066CC; color: white; padding: 20px; text-align: center; }
                .content { padding: 30px; background: #f8f9fa; }
                .booking-details { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; }
                .detail-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #eee; }
                .button { background: #0066CC; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 20px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Booking Confirmed!</h1>
                </div>
                <div class='content'>
                    <p>Dear {$booking->guest_name},</p>
                    <p>Your booking has been confirmed. Here are your booking details:</p>
                    
                    <div class='booking-details'>
                        <div class='detail-row'>
                            <strong>Booking Reference:</strong>
                            <span>{$booking->booking_reference}</span>
                        </div>
                        <div class='detail-row'>
                            <strong>Hotel:</strong>
                            <span>{$booking->hotel_name}</span>
                        </div>
                        <div class='detail-row'>
                            <strong>Room:</strong>
                            <span>{$booking->room_name}</span>
                        </div>
                        <div class='detail-row'>
                            <strong>Check-in:</strong>
                            <span>{$booking->check_in_date}</span>
                        </div>
                        <div class='detail-row'>
                            <strong>Check-out:</strong>
                            <span>{$booking->check_out_date}</span>
                        </div>
                        <div class='detail-row'>
                            <strong>Guests:</strong>
                            <span>{$booking->num_guests}</span>
                        </div>
                        <div class='detail-row'>
                            <strong>Total Amount:</strong>
                            <span>₦" . number_format($booking->total_amount, 2) . "</span>
                        </div>
                        <div class='detail-row'>
                            <strong>Payment Status:</strong>
                            <span>" . ucfirst($booking->payment_status) . "</span>
                        </div>
                    </div>
                    
                    <p>If you have any questions, please contact the hotel or reach out to us.</p>
                    <p>Best regards,<br>The StayDesk Team</p>
                </div>
            </div>
        </body>
        </html>
        ";

        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . $this->from_name . ' <' . $this->from_email . '>'
        );

        wp_mail($booking->guest_email, $subject, $message, $headers);

        // Send WhatsApp notification to guest
        $this->send_whatsapp_notification($booking->guest_phone, "Your booking {$booking->booking_reference} at {$booking->hotel_name} is confirmed! Check-in: {$booking->check_in_date}, Total: ₦" . number_format($booking->total_amount, 2));

        return true;
    }

    /**
     * Send payment reminder.
     */
    public function send_payment_reminder($booking_id) {
        global $wpdb;

        $table_bookings = $wpdb->prefix . 'staydesk_bookings';
        $booking = $wpdb->get_row($wpdb->prepare(
            "SELECT b.*, g.guest_name, g.guest_email, h.hotel_name
             FROM $table_bookings b
             LEFT JOIN {$wpdb->prefix}staydesk_guests g ON b.guest_id = g.id
             LEFT JOIN {$wpdb->prefix}staydesk_hotels h ON b.hotel_id = h.id
             WHERE b.id = %d",
            $booking_id
        ));

        if (!$booking) {
            return false;
        }

        $subject = 'Payment Reminder - ' . $booking->booking_reference;
        
        $message = "
        <html>
        <head>
            <style>
                body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #FFC107; color: #333; padding: 20px; text-align: center; }
                .content { padding: 30px; background: #f8f9fa; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Payment Reminder</h1>
                </div>
                <div class='content'>
                    <p>Dear {$booking->guest_name},</p>
                    <p>This is a friendly reminder that your booking payment is pending.</p>
                    <p><strong>Booking Reference:</strong> {$booking->booking_reference}</p>
                    <p><strong>Amount Due:</strong> ₦" . number_format($booking->total_amount, 2) . "</p>
                    <p>Please complete your payment to confirm your booking.</p>
                    <p>Best regards,<br>The StayDesk Team</p>
                </div>
            </div>
        </body>
        </html>
        ";

        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . $this->from_name . ' <' . $this->from_email . '>'
        );

        wp_mail($booking->guest_email, $subject, $message, $headers);

        return true;
    }

    /**
     * Send refund notification.
     */
    public function send_refund_notification($booking_id, $amount, $reason) {
        global $wpdb;

        $table_bookings = $wpdb->prefix . 'staydesk_bookings';
        $booking = $wpdb->get_row($wpdb->prepare(
            "SELECT b.*, g.guest_name, g.guest_email
             FROM $table_bookings b
             LEFT JOIN {$wpdb->prefix}staydesk_guests g ON b.guest_id = g.id
             WHERE b.id = %d",
            $booking_id
        ));

        if (!$booking) {
            return false;
        }

        $subject = 'Refund Notification - ' . $booking->booking_reference;
        
        $message = "
        <html>
        <head>
            <style>
                body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #28A745; color: white; padding: 20px; text-align: center; }
                .content { padding: 30px; background: #f8f9fa; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Refund Processing</h1>
                </div>
                <div class='content'>
                    <p>Dear {$booking->guest_name},</p>
                    <p>A refund has been initiated for your booking.</p>
                    <p><strong>Booking Reference:</strong> {$booking->booking_reference}</p>
                    <p><strong>Refund Amount:</strong> ₦" . number_format($amount, 2) . "</p>
                    <p><strong>Reason:</strong> {$reason}</p>
                    <p>The refund will be processed within 5-7 business days.</p>
                    <p>Best regards,<br>The StayDesk Team</p>
                </div>
            </div>
        </body>
        </html>
        ";

        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . $this->from_name . ' <' . $this->from_email . '>'
        );

        wp_mail($booking->guest_email, $subject, $message, $headers);

        return true;
    }

    /**
     * Send subscription expiry notice.
     */
    public function send_subscription_expiry_notice($hotel_id) {
        global $wpdb;

        $table_hotels = $wpdb->prefix . 'staydesk_hotels';
        $hotel = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_hotels WHERE id = %d",
            $hotel_id
        ));

        if (!$hotel) {
            return false;
        }

        $subject = 'Subscription Expired - StayDesk';
        
        $message = "
        <html>
        <head>
            <style>
                body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #DC3545; color: white; padding: 20px; text-align: center; }
                .content { padding: 30px; background: #f8f9fa; }
                .button { background: #0066CC; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 20px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Subscription Expired</h1>
                </div>
                <div class='content'>
                    <p>Dear {$hotel->hotel_name},</p>
                    <p>Your StayDesk subscription has expired.</p>
                    <p>To continue enjoying our services, please renew your subscription:</p>
                    <a href='" . home_url('/staydesk-pricing') . "' class='button'>Renew Subscription</a>
                    <p>Best regards,<br>The BendlessTech Team</p>
                </div>
            </div>
        </body>
        </html>
        ";

        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . $this->from_name . ' <' . $this->from_email . '>'
        );

        wp_mail($hotel->hotel_email, $subject, $message, $headers);

        return true;
    }

    /**
     * Send WhatsApp notification (using WhatsApp Business API or integration).
     */
    private function send_whatsapp_notification($phone, $message) {
        // This would integrate with WhatsApp Business API
        // For now, we'll log the notification
        // In production, implement actual WhatsApp API integration
        
        error_log("WhatsApp notification to {$phone}: {$message}");
        
        // Example: Using a WhatsApp API service
        // You would need to implement actual API calls here
        return true;
    }
}

new Staydesk_Notifications();
