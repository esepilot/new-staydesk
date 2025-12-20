<?php
/**
 * Fired during plugin activation.
 *
 * @since      1.0.0
 * @package    Staydesk
 */
class Staydesk_Activator {

    /**
     * Activate the plugin.
     *
     * Create database tables and set default options.
     */
    public static function activate() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        // Hotels table
        $table_hotels = $wpdb->prefix . 'staydesk_hotels';
        $sql_hotels = "CREATE TABLE $table_hotels (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id bigint(20) UNSIGNED NOT NULL,
            hotel_name varchar(255) NOT NULL,
            hotel_email varchar(255) NOT NULL,
            hotel_phone varchar(50) DEFAULT NULL,
            hotel_address text DEFAULT NULL,
            hotel_description text DEFAULT NULL,
            hotel_logo varchar(255) DEFAULT NULL,
            onboarding_data longtext DEFAULT NULL,
            account_details longtext DEFAULT NULL,
            hotel_info_json longtext DEFAULT NULL,
            subscription_status varchar(50) DEFAULT 'inactive',
            subscription_plan varchar(50) DEFAULT NULL,
            subscription_expiry datetime DEFAULT NULL,
            discount_applied tinyint(1) DEFAULT 0,
            email_confirmed tinyint(1) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY user_id (user_id),
            KEY subscription_status (subscription_status)
        ) $charset_collate;";

        // Rooms table
        $table_rooms = $wpdb->prefix . 'staydesk_rooms';
        $sql_rooms = "CREATE TABLE $table_rooms (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            hotel_id bigint(20) UNSIGNED NOT NULL,
            room_name varchar(255) NOT NULL,
            room_type varchar(100) NOT NULL,
            room_description text DEFAULT NULL,
            room_image varchar(255) DEFAULT NULL,
            price_per_night decimal(10,2) NOT NULL,
            max_guests int(11) DEFAULT 2,
            amenities text DEFAULT NULL,
            availability_status varchar(50) DEFAULT 'available',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY hotel_id (hotel_id),
            KEY availability_status (availability_status)
        ) $charset_collate;";

        // Bookings table
        $table_bookings = $wpdb->prefix . 'staydesk_bookings';
        $sql_bookings = "CREATE TABLE $table_bookings (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            booking_reference varchar(50) NOT NULL,
            hotel_id bigint(20) UNSIGNED NOT NULL,
            room_id bigint(20) UNSIGNED NOT NULL,
            guest_id bigint(20) UNSIGNED NOT NULL,
            check_in_date date NOT NULL,
            check_out_date date NOT NULL,
            num_guests int(11) DEFAULT 1,
            total_amount decimal(10,2) NOT NULL,
            booking_status varchar(50) DEFAULT 'pending',
            payment_status varchar(50) DEFAULT 'pending',
            special_requests text DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY booking_reference (booking_reference),
            KEY hotel_id (hotel_id),
            KEY guest_id (guest_id),
            KEY booking_status (booking_status),
            KEY check_in_date (check_in_date)
        ) $charset_collate;";

        // Guests table
        $table_guests = $wpdb->prefix . 'staydesk_guests';
        $sql_guests = "CREATE TABLE $table_guests (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            guest_name varchar(255) NOT NULL,
            guest_email varchar(255) NOT NULL,
            guest_phone varchar(50) NOT NULL,
            guest_address text DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY guest_email (guest_email)
        ) $charset_collate;";

        // Transactions table
        $table_transactions = $wpdb->prefix . 'staydesk_transactions';
        $sql_transactions = "CREATE TABLE $table_transactions (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            booking_id bigint(20) UNSIGNED DEFAULT NULL,
            hotel_id bigint(20) UNSIGNED NOT NULL,
            transaction_type varchar(50) NOT NULL,
            transaction_reference varchar(100) NOT NULL UNIQUE,
            amount decimal(10,2) NOT NULL,
            currency varchar(10) DEFAULT 'NGN',
            payment_method varchar(50) DEFAULT NULL,
            payment_gateway varchar(50) DEFAULT NULL,
            transaction_status varchar(50) DEFAULT 'pending',
            transaction_data longtext DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY booking_id (booking_id),
            KEY hotel_id (hotel_id),
            KEY transaction_status (transaction_status)
        ) $charset_collate;";

        // Chat logs table
        $table_chat_logs = $wpdb->prefix . 'staydesk_chat_logs';
        $sql_chat_logs = "CREATE TABLE $table_chat_logs (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            hotel_id bigint(20) UNSIGNED NOT NULL,
            session_id varchar(100) NOT NULL,
            message_type varchar(50) NOT NULL,
            message_text text NOT NULL,
            language varchar(10) DEFAULT 'en',
            response_text text DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY hotel_id (hotel_id),
            KEY session_id (session_id)
        ) $charset_collate;";

        // Subscriptions table
        $table_subscriptions = $wpdb->prefix . 'staydesk_subscriptions';
        $sql_subscriptions = "CREATE TABLE $table_subscriptions (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            hotel_id bigint(20) UNSIGNED NOT NULL,
            plan_type varchar(50) NOT NULL,
            plan_price decimal(10,2) NOT NULL,
            discount_percentage decimal(5,2) DEFAULT 0,
            start_date datetime NOT NULL,
            expiry_date datetime NOT NULL,
            auto_renew tinyint(1) DEFAULT 0,
            status varchar(50) DEFAULT 'active',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY hotel_id (hotel_id),
            KEY status (status)
        ) $charset_collate;";

        // Support tickets table
        $table_support = $wpdb->prefix . 'staydesk_support_tickets';
        $sql_support = "CREATE TABLE $table_support (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            hotel_id bigint(20) UNSIGNED NOT NULL,
            ticket_reference varchar(50) NOT NULL,
            subject varchar(255) NOT NULL,
            message text NOT NULL,
            priority varchar(50) DEFAULT 'normal',
            status varchar(50) DEFAULT 'open',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY ticket_reference (ticket_reference),
            KEY hotel_id (hotel_id),
            KEY status (status)
        ) $charset_collate;";
        
        // Room types table
        $table_room_types = $wpdb->prefix . 'staydesk_room_types';
        $sql_room_types = "CREATE TABLE $table_room_types (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            hotel_id bigint(20) UNSIGNED DEFAULT NULL,
            type_name varchar(100) NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY hotel_id (hotel_id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        
        dbDelta($sql_hotels);
        dbDelta($sql_rooms);
        dbDelta($sql_bookings);
        dbDelta($sql_guests);
        dbDelta($sql_transactions);
        dbDelta($sql_chat_logs);
        dbDelta($sql_subscriptions);
        dbDelta($sql_support);
        dbDelta($sql_room_types);
        
        // Insert default room types (hotel_id = NULL means available to all)
        $default_types = array('Single', 'Double', 'Suite', 'Deluxe', 'Presidential');
        foreach ($default_types as $type) {
            $wpdb->insert(
                $table_room_types,
                array('type_name' => $type, 'hotel_id' => NULL),
                array('%s', '%d')
            );
        }

        // Set plugin version
        add_option('staydesk_version', STAYDESK_VERSION);

        // Set initial hotel count for discount tracking
        add_option('staydesk_hotel_count', 0);

        // Create custom pages
        self::create_plugin_pages();

        // Add custom roles
        self::add_custom_roles();

        // Flush rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Add custom user roles.
     */
    private static function add_custom_roles() {
        // Add hotel role
        add_role(
            'staydesk_hotel',
            'StayDesk Hotel',
            array(
                'read' => true,
                'edit_posts' => false,
                'delete_posts' => false,
                'publish_posts' => false,
                'upload_files' => true,
            )
        );

        // Add hotel admin capabilities to administrator role
        $admin = get_role('administrator');
        if ($admin) {
            $admin->add_cap('manage_staydesk');
            $admin->add_cap('manage_hotels');
            $admin->add_cap('view_hotel_reports');
        }
    }

    /**
     * Create custom pages for the plugin.
     */
    private static function create_plugin_pages() {
        $pages = array(
            'staydesk-home' => array(
                'title' => 'StayDesk - Hotel Assistant Platform',
                'content' => '[staydesk_homepage]'
            ),
            'staydesk-login' => array(
                'title' => 'Login - StayDesk',
                'content' => '[staydesk_login]'
            ),
            'staydesk-signup' => array(
                'title' => 'Sign Up - StayDesk',
                'content' => '[staydesk_signup]'
            ),
            'staydesk-verify-email' => array(
                'title' => 'Verify Email - StayDesk',
                'content' => '[staydesk_verify_email]'
            ),
            'staydesk-dashboard' => array(
                'title' => 'Dashboard - StayDesk',
                'content' => '[staydesk_dashboard]'
            ),
            'staydesk-rooms' => array(
                'title' => 'Room Management - StayDesk',
                'content' => '[staydesk_rooms]'
            ),
            'staydesk-bookings' => array(
                'title' => 'Bookings - StayDesk',
                'content' => '[staydesk_bookings]'
            ),
            'staydesk-profile' => array(
                'title' => 'Profile & Settings - StayDesk',
                'content' => '[staydesk_profile]'
            ),
            'staydesk-pricing' => array(
                'title' => 'Pricing & Subscription - StayDesk',
                'content' => '[staydesk_pricing]'
            ),
            'staydesk-forgot-password' => array(
                'title' => 'Forgot Password - StayDesk',
                'content' => '[staydesk_forgot_password]'
            ),
            'staydesk-hotel-info' => array(
                'title' => 'Hotel Information - StayDesk',
                'content' => '[staydesk_hotel_info]'
            ),
            'staydesk-admin' => array(
                'title' => 'Admin Dashboard - StayDesk',
                'content' => '[staydesk_admin_dashboard]'
            )
        );

        foreach ($pages as $slug => $page) {
            $page_check = get_page_by_path($slug);
            if (!$page_check) {
                wp_insert_post(array(
                    'post_title'    => $page['title'],
                    'post_content'  => $page['content'],
                    'post_name'     => $slug,
                    'post_status'   => 'publish',
                    'post_type'     => 'page',
                    'post_author'   => 1
                ));
            }
        }
    }
}
