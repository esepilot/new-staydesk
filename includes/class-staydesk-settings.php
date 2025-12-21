<?php
/**
 * Plugin settings and configuration.
 *
 * @since      1.0.0
 * @package    Staydesk
 */
class Staydesk_Settings {

    /**
     * Initialize the class.
     */
    public function __construct() {
        add_action('admin_menu', array($this, 'add_settings_page'));
        add_action('admin_init', array($this, 'register_settings'));
    }

    /**
     * Add settings page to WordPress admin.
     */
    public function add_settings_page() {
        add_menu_page(
            'StayDesk Settings',
            'StayDesk',
            'manage_options',
            'staydesk-settings',
            array($this, 'render_settings_page'),
            'dashicons-building',
            30
        );

        add_submenu_page(
            'staydesk-settings',
            'Settings',
            'Settings',
            'manage_options',
            'staydesk-settings',
            array($this, 'render_settings_page')
        );

        add_submenu_page(
            'staydesk-settings',
            'Hotels',
            'Hotels',
            'manage_options',
            'staydesk-hotels',
            array($this, 'render_hotels_page')
        );

        add_submenu_page(
            'staydesk-settings',
            'Bookings',
            'Bookings',
            'manage_options',
            'staydesk-all-bookings',
            array($this, 'render_all_bookings_page')
        );
    }

    /**
     * Register plugin settings.
     */
    public function register_settings() {
        // Paystack settings
        register_setting('staydesk_settings', 'staydesk_paystack_test_mode');
        register_setting('staydesk_settings', 'staydesk_paystack_test_public_key');
        register_setting('staydesk_settings', 'staydesk_paystack_test_secret_key');
        register_setting('staydesk_settings', 'staydesk_paystack_live_public_key');
        register_setting('staydesk_settings', 'staydesk_paystack_live_secret_key');
        
        // Legacy keys (for backwards compatibility)
        register_setting('staydesk_settings', 'staydesk_paystack_secret_key');
        register_setting('staydesk_settings', 'staydesk_paystack_public_key');
        
        // Email settings
        register_setting('staydesk_settings', 'staydesk_email_from');
        register_setting('staydesk_settings', 'staydesk_email_name');
        
        // API settings
        register_setting('staydesk_settings', 'staydesk_api_key');
        
        // WhatsApp settings
        register_setting('staydesk_settings', 'staydesk_whatsapp_api_token');
    }

    /**
     * Render settings page.
     */
    public function render_settings_page() {
        $test_mode = get_option('staydesk_paystack_test_mode', 'yes');
        ?>
        <div class="wrap">
            <h1>StayDesk Settings</h1>
            <form method="post" action="options.php">
                <?php settings_fields('staydesk_settings'); ?>
                <?php do_settings_sections('staydesk_settings'); ?>
                
                <table class="form-table">
                    <tr>
                        <th colspan="2"><h2>Paystack Configuration</h2></th>
                    </tr>
                    
                    <tr>
                        <th scope="row">Payment Mode</th>
                        <td>
                            <fieldset>
                                <label>
                                    <input type="radio" name="staydesk_paystack_test_mode" value="yes" <?php checked($test_mode, 'yes'); ?>>
                                    Test Mode (for development & testing)
                                </label>
                                <br>
                                <label>
                                    <input type="radio" name="staydesk_paystack_test_mode" value="no" <?php checked($test_mode, 'no'); ?>>
                                    Live Mode (for production payments)
                                </label>
                            </fieldset>
                            <p class="description">Get your API keys from <a href="https://dashboard.paystack.com" target="_blank">Paystack Dashboard</a></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th colspan="2"><h3 style="color: #0073aa;">Test Keys (for development)</h3></th>
                    </tr>
                    <tr>
                        <th scope="row">Test Public Key</th>
                        <td>
                            <input type="text" name="staydesk_paystack_test_public_key" 
                                   value="<?php echo esc_attr(get_option('staydesk_paystack_test_public_key')); ?>" 
                                   class="regular-text" placeholder="pk_test_..." />
                            <p class="description">Your Paystack test public key</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Test Secret Key</th>
                        <td>
                            <input type="password" name="staydesk_paystack_test_secret_key" 
                                   value="<?php echo esc_attr(get_option('staydesk_paystack_test_secret_key')); ?>" 
                                   class="regular-text" placeholder="sk_test_..." />
                            <p class="description">Your Paystack test secret key (will be hidden)</p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th colspan="2"><h3 style="color: #d63638;">Live Keys (for production)</h3></th>
                    </tr>
                    <tr>
                        <th scope="row">Live Public Key</th>
                        <td>
                            <input type="text" name="staydesk_paystack_live_public_key" 
                                   value="<?php echo esc_attr(get_option('staydesk_paystack_live_public_key')); ?>" 
                                   class="regular-text" placeholder="pk_live_..." />
                            <p class="description">Your Paystack live public key</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Live Secret Key</th>
                        <td>
                            <input type="password" name="staydesk_paystack_live_secret_key" 
                                   value="<?php echo esc_attr(get_option('staydesk_paystack_live_secret_key')); ?>" 
                                   class="regular-text" placeholder="sk_live_..." />
                            <p class="description">Your Paystack live secret key (will be hidden)</p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th colspan="2"><h2>Email Configuration</h2></th>
                    </tr>
                    <tr>
                        <th scope="row">From Email</th>
                        <td>
                            <input type="email" name="staydesk_email_from" 
                                   value="<?php echo esc_attr(get_option('staydesk_email_from', 'reach@bendlesstech.com')); ?>" 
                                   class="regular-text" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">From Name</th>
                        <td>
                            <input type="text" name="staydesk_email_name" 
                                   value="<?php echo esc_attr(get_option('staydesk_email_name', 'StayDesk by BendlessTech')); ?>" 
                                   class="regular-text" />
                        </td>
                    </tr>
                    
                    <tr>
                        <th colspan="2"><h2>API Configuration</h2></th>
                    </tr>
                    <tr>
                        <th scope="row">API Key</th>
                        <td>
                            <input type="text" name="staydesk_api_key" 
                                   value="<?php echo esc_attr(get_option('staydesk_api_key', wp_generate_password(32, false))); ?>" 
                                   class="regular-text" readonly />
                            <button type="button" class="button" onclick="this.previousElementSibling.value = '<?php echo wp_generate_password(32, false); ?>'">Regenerate</button>
                            <p class="description">API key for external integrations</p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th colspan="2"><h2>WhatsApp Configuration</h2></th>
                    </tr>
                    <tr>
                        <th scope="row">WhatsApp API Token</th>
                        <td>
                            <input type="text" name="staydesk_whatsapp_api_token" 
                                   value="<?php echo esc_attr(get_option('staydesk_whatsapp_api_token')); ?>" 
                                   class="regular-text" />
                            <p class="description">WhatsApp Business API token for notifications</p>
                        </td>
                    </tr>
                </table>
                
                <?php submit_button(); ?>
            </form>
            
            <hr>
            
            <h2>Plugin Information</h2>
            <table class="form-table">
                <tr>
                    <th>Plugin Version:</th>
                    <td><?php echo STAYDESK_VERSION; ?></td>
                </tr>
                <tr>
                    <th>Total Hotels:</th>
                    <td><?php echo intval(get_option('staydesk_hotel_count', 0)); ?></td>
                </tr>
                <tr>
                    <th>Database Tables:</th>
                    <td>
                        <?php
                        global $wpdb;
                        $tables = array(
                            'hotels', 'rooms', 'bookings', 'guests', 
                            'transactions', 'chat_logs', 'subscriptions', 'support_tickets'
                        );
                        foreach ($tables as $table) {
                            $table_name = $wpdb->prefix . 'staydesk_' . $table;
                            echo $table_name . '<br>';
                        }
                        ?>
                    </td>
                </tr>
            </table>
        </div>
        <?php
    }

    /**
     * Render hotels management page.
     */
    public function render_hotels_page() {
        global $wpdb;
        
        $table_hotels = $wpdb->prefix . 'staydesk_hotels';
        $hotels = $wpdb->get_results("SELECT * FROM $table_hotels ORDER BY created_at DESC");
        
        ?>
        <div class="wrap">
            <h1>Hotels Management</h1>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Hotel Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Subscription</th>
                        <th>Status</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($hotels as $hotel): ?>
                        <tr>
                            <td><?php echo $hotel->id; ?></td>
                            <td><?php echo esc_html($hotel->hotel_name); ?></td>
                            <td><?php echo esc_html($hotel->hotel_email); ?></td>
                            <td><?php echo esc_html($hotel->hotel_phone); ?></td>
                            <td><?php echo esc_html($hotel->subscription_plan); ?></td>
                            <td>
                                <span class="status-<?php echo $hotel->subscription_status; ?>">
                                    <?php echo ucfirst($hotel->subscription_status); ?>
                                </span>
                            </td>
                            <td><?php echo date('Y-m-d', strtotime($hotel->created_at)); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
    }

    /**
     * Render all bookings page.
     */
    public function render_all_bookings_page() {
        global $wpdb;
        
        $table_bookings = $wpdb->prefix . 'staydesk_bookings';
        $bookings = $wpdb->get_results(
            "SELECT b.*, h.hotel_name, g.guest_name 
             FROM $table_bookings b
             LEFT JOIN {$wpdb->prefix}staydesk_hotels h ON b.hotel_id = h.id
             LEFT JOIN {$wpdb->prefix}staydesk_guests g ON b.guest_id = g.id
             ORDER BY b.created_at DESC
             LIMIT 100"
        );
        
        ?>
        <div class="wrap">
            <h1>All Bookings</h1>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>Hotel</th>
                        <th>Guest</th>
                        <th>Check-in</th>
                        <th>Check-out</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Payment</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td><?php echo esc_html($booking->booking_reference); ?></td>
                            <td><?php echo esc_html($booking->hotel_name); ?></td>
                            <td><?php echo esc_html($booking->guest_name); ?></td>
                            <td><?php echo $booking->check_in_date; ?></td>
                            <td><?php echo $booking->check_out_date; ?></td>
                            <td>₦<?php echo number_format($booking->total_amount, 2); ?></td>
                            <td><?php echo ucfirst($booking->booking_status); ?></td>
                            <td><?php echo ucfirst($booking->payment_status); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
    }
}

new Staydesk_Settings();
