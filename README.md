# StayDesk by BendlessTech

A comprehensive WordPress hotel management plugin designed for hotels in Nigeria. Manage bookings, rooms, payments, and guest enquiries with an integrated AI chatbot.

## Features

- **Hotel Dashboard** - Comprehensive dashboard with key metrics and real-time clock
- **Booking Management** - Complete booking workflow with date selection and guest forms
- **Room Management** - Add, edit, and manage room types, pricing, and availability
- **Payment Integration** - Paystack integration for Nigerian Naira (NGN) payments
- **Subscription Plans** - Monthly (₦49,900) and Yearly (₦598,800) plans with 10% discount for first 10 hotels on yearly plan
- **AI Chatbot** - Bilingual chatbot (English & Nigerian Pidgin) for guest enquiries
- **Email Notifications** - Automated notifications for bookings, payments, and subscriptions
- **WhatsApp Integration** - Guest notifications via WhatsApp Business API
- **Admin Dashboard** - Platform management for BendlessTech
- **Apple-inspired Design** - Beautiful, responsive design with smooth animations

## Installation

1. Upload the `staydesk` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Configure Paystack API keys in the plugin settings
4. Set up email configuration for reach@bendlesstech.com
5. Visit the StayDesk homepage at `/staydesk-home`

## Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- MySQL 5.6 or higher
- Paystack account for payment processing
- cURL extension enabled

## Configuration

After activation, configure the following:

- **Paystack API Keys** - Add your Paystack secret and public keys
- **Email Settings** - Configure sending from reach@bendlesstech.com
- **WhatsApp API** - Set up WhatsApp Business API credentials
- **API Key** - Generate API key for external integrations

## Shortcodes

The plugin provides multiple shortcodes for embedding pages anywhere in WordPress:

- `[staydesk_signup]` - Hotel registration form
- `[staydesk_login]` - Login page
- `[staydesk_verify_email]` - Email verification with 6-digit code
- `[staydesk_dashboard]` - Hotel dashboard
- `[staydesk_rooms]` - Room management interface
- `[staydesk_bookings]` - Booking management
- `[staydesk_profile]` - Profile & settings
- `[staydesk_pricing]` - Subscription plans
- `[staydesk_homepage]` - Marketing landing page
- `[staydesk_admin_dashboard]` - Admin dashboard

**For complete shortcode documentation, see [SHORTCODES.md](SHORTCODES.md)**

## Usage

### For Hotels

1. Sign up at `/staydesk-signup`
2. Verify email with 6-digit code sent to your email
3. Login at `/staydesk-login`
4. Complete hotel profile
5. Add rooms and set pricing
6. Choose subscription plan
7. Start accepting bookings!

### For BendlessTech Admin

Access the admin dashboard at `/staydesk-admin` with administrator privileges.

## Pages Created

- `/staydesk-home` - Marketing homepage
- `/staydesk-login` - Hotel login
- `/staydesk-signup` - Hotel registration
- `/staydesk-dashboard` - Hotel dashboard
- `/staydesk-bookings` - Booking management
- `/staydesk-profile` - Profile & settings
- `/staydesk-pricing` - Subscription plans
- `/staydesk-admin` - Admin dashboard

## REST API Endpoints

- `GET /wp-json/staydesk/v1/hotels` - Get all hotels
- `GET /wp-json/staydesk/v1/hotel/{id}` - Get hotel details
- `GET /wp-json/staydesk/v1/rooms` - Get available rooms
- `POST /wp-json/staydesk/v1/bookings` - Create booking
- `POST /wp-json/staydesk/v1/chatbot` - Chatbot message
- `GET /wp-json/staydesk/v1/widget-config/{hotel_id}` - Widget config

## Database Tables

- `wp_staydesk_hotels` - Hotel profiles
- `wp_staydesk_rooms` - Room inventory
- `wp_staydesk_bookings` - Reservations
- `wp_staydesk_guests` - Guest information
- `wp_staydesk_transactions` - Payments & refunds
- `wp_staydesk_chat_logs` - Chatbot conversations
- `wp_staydesk_subscriptions` - Subscription records
- `wp_staydesk_support_tickets` - Support tickets

## Support

For support, contact: reach@bendlesstech.com
WhatsApp: 07120018023

## License

GPL-2.0+

## Author

BendlessTech - https://bendlesstech.com

## Version

1.0.0