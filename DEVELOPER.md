# StayDesk Developer Documentation

## Architecture Overview

StayDesk follows WordPress plugin best practices with a modular architecture:

```
staydesk/
├── staydesk.php              # Main plugin file
├── uninstall.php             # Cleanup on uninstall
├── includes/                 # Core classes
│   ├── class-staydesk.php                  # Main plugin class
│   ├── class-staydesk-loader.php           # Hook loader
│   ├── class-staydesk-activator.php        # Activation hooks
│   ├── class-staydesk-deactivator.php      # Deactivation hooks
│   ├── class-staydesk-settings.php         # Admin settings
│   ├── class-staydesk-auth.php             # Authentication
│   ├── class-staydesk-dashboard.php        # Dashboard logic
│   ├── class-staydesk-admin.php            # Admin functions
│   ├── class-staydesk-bookings.php         # Booking management
│   ├── class-staydesk-rooms.php            # Room management
│   ├── class-staydesk-payments.php         # Payment processing
│   ├── class-staydesk-subscriptions.php    # Subscription logic
│   ├── class-staydesk-chatbot.php          # AI chatbot
│   ├── class-staydesk-notifications.php    # Email/SMS notifications
│   └── class-staydesk-api.php              # REST API endpoints
├── admin/                    # Admin area assets
│   ├── css/
│   └── js/
├── public/                   # Public-facing assets
│   ├── css/
│   └── js/
├── templates/                # Template files
│   ├── homepage.php
│   ├── login.php
│   ├── signup.php
│   ├── dashboard.php
│   ├── bookings.php
│   ├── profile.php
│   ├── pricing.php
│   ├── admin-dashboard.php
│   └── chatbot-widget.php
└── assets/                   # Images, fonts, etc.
    ├── images/
    └── fonts/
```

## Class Structure

### Main Plugin Class (Staydesk)
The central orchestrator that loads dependencies and registers hooks.

```php
class Staydesk {
    protected $loader;
    protected $plugin_name;
    protected $version;
    
    public function __construct()
    public function run()
}
```

### Loader Class (Staydesk_Loader)
Manages WordPress hooks registration.

```php
class Staydesk_Loader {
    public function add_action($hook, $component, $callback, $priority, $accepted_args)
    public function add_filter($hook, $component, $callback, $priority, $accepted_args)
    public function run()
}
```

### Activator Class (Staydesk_Activator)
Handles plugin activation tasks.

```php
class Staydesk_Activator {
    public static function activate()
    private static function create_plugin_pages()
    private static function add_custom_roles()
}
```

## Database Schema

### Hotels Table
```sql
CREATE TABLE wp_staydesk_hotels (
    id bigint(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id bigint(20) UNSIGNED NOT NULL,
    hotel_name varchar(255) NOT NULL,
    hotel_email varchar(255) NOT NULL,
    hotel_phone varchar(50),
    hotel_address text,
    hotel_description text,
    hotel_logo varchar(255),
    onboarding_data longtext,
    account_details longtext,
    subscription_status varchar(50) DEFAULT 'inactive',
    subscription_plan varchar(50),
    subscription_expiry datetime,
    discount_applied tinyint(1) DEFAULT 0,
    email_confirmed tinyint(1) DEFAULT 0,
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY user_id (user_id),
    KEY subscription_status (subscription_status)
);
```

### Rooms Table
```sql
CREATE TABLE wp_staydesk_rooms (
    id bigint(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    hotel_id bigint(20) UNSIGNED NOT NULL,
    room_name varchar(255) NOT NULL,
    room_type varchar(100) NOT NULL,
    room_description text,
    room_image varchar(255),
    price_per_night decimal(10,2) NOT NULL,
    max_guests int(11) DEFAULT 2,
    amenities text,
    availability_status varchar(50) DEFAULT 'available',
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY hotel_id (hotel_id),
    KEY availability_status (availability_status)
);
```

### Bookings Table
```sql
CREATE TABLE wp_staydesk_bookings (
    id bigint(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    booking_reference varchar(50) NOT NULL UNIQUE,
    hotel_id bigint(20) UNSIGNED NOT NULL,
    room_id bigint(20) UNSIGNED NOT NULL,
    guest_id bigint(20) UNSIGNED NOT NULL,
    check_in_date date NOT NULL,
    check_out_date date NOT NULL,
    num_guests int(11) DEFAULT 1,
    total_amount decimal(10,2) NOT NULL,
    booking_status varchar(50) DEFAULT 'pending',
    payment_status varchar(50) DEFAULT 'pending',
    special_requests text,
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY hotel_id (hotel_id),
    KEY guest_id (guest_id),
    KEY booking_status (booking_status),
    KEY check_in_date (check_in_date)
);
```

## API Endpoints

### REST API Routes
All endpoints are prefixed with `/wp-json/staydesk/v1/`

#### Public Endpoints

**GET /hotels**
```php
// Returns list of active hotels
Response: {
    "success": true,
    "data": [
        {
            "id": 1,
            "hotel_name": "Example Hotel",
            "hotel_email": "hotel@example.com",
            ...
        }
    ]
}
```

**GET /hotel/{id}**
```php
// Returns single hotel with rooms
Response: {
    "success": true,
    "data": {
        "hotel": { ... },
        "rooms": [ ... ]
    }
}
```

**POST /chatbot**
```php
// Send message to chatbot
Request: {
    "hotel_id": 1,
    "session_id": "session_abc123",
    "message": "Show available rooms",
    "language": "en"
}

Response: {
    "success": true,
    "session_id": "session_abc123",
    "response": {
        "message": "We have 5 rooms available...",
        "type": "room_list",
        "rooms": [ ... ]
    }
}
```

#### Protected Endpoints (Require API Key)

**POST /bookings**
```php
// Create new booking
Headers: {
    "X-API-Key": "your_api_key_here"
}

Request: {
    "room_id": 1,
    "check_in_date": "2024-01-15",
    "check_out_date": "2024-01-20",
    "guest_name": "John Doe",
    "guest_email": "john@example.com",
    "guest_phone": "08012345678",
    "num_guests": 2
}

Response: {
    "success": true,
    "data": {
        "booking_id": 123,
        "booking_reference": "BK12345678",
        "total_amount": 75000
    }
}
```

## AJAX Actions

### Authentication
- `staydesk_login` - User login
- `staydesk_signup` - User registration
- `staydesk_logout` - User logout
- `staydesk_confirm_email` - Email confirmation

### Bookings
- `staydesk_create_booking` - Create new booking
- `staydesk_update_booking_status` - Update booking status
- `staydesk_cancel_booking` - Cancel booking

### Rooms
- `staydesk_add_room` - Add new room
- `staydesk_update_room` - Update room details
- `staydesk_delete_room` - Delete room
- `staydesk_get_available_rooms` - Get available rooms

### Payments
- `staydesk_verify_payment` - Verify Paystack payment
- `staydesk_process_refund` - Process refund

### Subscriptions
- `staydesk_subscribe` - Subscribe to plan
- `staydesk_cancel_subscription` - Cancel subscription

### Chatbot
- `staydesk_chatbot_message` - Process chatbot message

## Hooks & Filters

### Actions

```php
// After booking is created
do_action('staydesk_after_booking_created', $booking_id, $booking_data);

// After payment is verified
do_action('staydesk_after_payment_verified', $transaction_id, $transaction_data);

// When subscription is activated
do_action('staydesk_subscription_activated', $hotel_id, $plan_type);

// When subscription expires
do_action('staydesk_subscription_expired', $hotel_id);

// Before sending notification
do_action('staydesk_before_send_notification', $type, $recipient, $data);
```

### Filters

```php
// Modify email content
apply_filters('staydesk_email_content', $message, $type, $data);

// Modify chatbot response
apply_filters('staydesk_chatbot_response', $response, $message, $hotel_id);

// Modify subscription price
apply_filters('staydesk_subscription_price', $price, $plan_type, $hotel_id);

// Modify booking data before save
apply_filters('staydesk_booking_data', $booking_data);
```

## Custom Development

### Adding New Features

1. Create new class in `/includes/`
2. Load in main plugin class:
```php
require_once STAYDESK_PLUGIN_DIR . 'includes/class-staydesk-your-feature.php';
```

3. Register hooks in constructor:
```php
class Staydesk_Your_Feature {
    public function __construct() {
        add_action('init', array($this, 'init'));
    }
    
    public function init() {
        // Your code here
    }
}
```

### Adding Custom Templates

1. Create template file in `/templates/`
2. Register shortcode:
```php
add_shortcode('staydesk_your_template', array($this, 'render_template'));

public function render_template() {
    ob_start();
    include STAYDESK_PLUGIN_DIR . 'templates/your-template.php';
    return ob_get_clean();
}
```

### Extending the Chatbot

Add custom responses in `class-staydesk-chatbot.php`:

```php
private function generate_response($hotel_id, $message, $session_id, $language) {
    // Add your custom logic
    if (preg_match('/your pattern/i', $message)) {
        return array(
            'message' => 'Your response',
            'type' => 'custom_type',
            'data' => [ ... ]
        );
    }
    
    // Continue with existing logic
    // ...
}
```

### Adding Payment Gateways

Extend the payment class:

```php
class Staydesk_Payments_Custom extends Staydesk_Payments {
    public function initialize_payment_custom($data) {
        // Your payment gateway logic
    }
}
```

## Testing

### Manual Testing Checklist

- [ ] Plugin activation
- [ ] User registration
- [ ] Email confirmation
- [ ] User login
- [ ] Dashboard access
- [ ] Booking creation
- [ ] Room management
- [ ] Payment processing
- [ ] Subscription activation
- [ ] Chatbot responses
- [ ] Email notifications
- [ ] API endpoints

### Database Testing

```sql
-- Check tables exist
SHOW TABLES LIKE 'wp_staydesk_%';

-- Verify hotel data
SELECT * FROM wp_staydesk_hotels LIMIT 5;

-- Check bookings
SELECT * FROM wp_staydesk_bookings LIMIT 5;

-- Verify subscriptions
SELECT * FROM wp_staydesk_subscriptions WHERE status = 'active';
```

### API Testing

```bash
# Test public endpoint
curl -X GET https://yoursite.com/wp-json/staydesk/v1/hotels

# Test chatbot
curl -X POST https://yoursite.com/wp-json/staydesk/v1/chatbot \
  -H "Content-Type: application/json" \
  -d '{"hotel_id":1,"message":"Hello","language":"en"}'

# Test protected endpoint
curl -X POST https://yoursite.com/wp-json/staydesk/v1/bookings \
  -H "X-API-Key: your_api_key" \
  -H "Content-Type: application/json" \
  -d '{"room_id":1,"check_in_date":"2024-01-15",...}'
```

## Performance Optimization

### Database Queries
- Use prepared statements
- Add indexes on frequently queried columns
- Limit query results
- Use caching for repeated queries

### Caching Strategy
```php
// Cache hotel data
$hotel = wp_cache_get('hotel_' . $hotel_id, 'staydesk');
if (false === $hotel) {
    $hotel = $wpdb->get_row(...);
    wp_cache_set('hotel_' . $hotel_id, $hotel, 'staydesk', 3600);
}
```

### Asset Loading
- Conditionally load scripts
- Minify CSS/JS in production
- Use CDN for libraries
- Lazy load images

## Security Best Practices

### Input Sanitization
```php
// Text fields
$value = sanitize_text_field($_POST['field']);

// Email
$email = sanitize_email($_POST['email']);

// Textarea
$text = sanitize_textarea_field($_POST['textarea']);

// SQL
$wpdb->prepare("SELECT * FROM table WHERE id = %d", $id);
```

### Output Escaping
```php
// HTML
echo esc_html($value);

// Attributes
echo '<div data-value="' . esc_attr($value) . '">';

// URLs
echo '<a href="' . esc_url($url) . '">';

// JavaScript
echo '<script>var data = ' . wp_json_encode($data) . ';</script>';
```

### Nonce Verification
```php
// Create nonce
wp_nonce_field('staydesk_action', 'staydesk_nonce');

// Verify nonce
if (!wp_verify_nonce($_POST['staydesk_nonce'], 'staydesk_action')) {
    wp_die('Security check failed');
}

// AJAX nonce
check_ajax_referer('staydesk_nonce', 'nonce');
```

## Debugging

### Enable WordPress Debug
```php
// wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

### Log Custom Messages
```php
error_log('StayDesk: ' . print_r($data, true));
```

### Database Queries
```php
// Show all queries
define('SAVEQUERIES', true);

// View queries
global $wpdb;
print_r($wpdb->queries);
```

## Contributing

See CONTRIBUTING.md for guidelines on:
- Code style
- Pull request process
- Bug reporting
- Feature requests

## License

GPL-2.0+ - See LICENSE file

## Support

- Documentation: /docs/
- Issues: GitHub Issues
- Email: reach@bendlesstech.com

---

**Version**: 1.0.0
**Author**: BendlessTech
**Website**: https://bendlesstech.com
