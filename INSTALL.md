# StayDesk Installation & Setup Guide

## Quick Start

### Step 1: Download & Install

1. Download the StayDesk plugin folder
2. Upload to `/wp-content/plugins/` directory on your WordPress site
3. Go to WordPress Admin → Plugins
4. Find "StayDesk by BendlessTech" and click "Activate"

### Step 2: Initial Configuration

After activation, the plugin will automatically:
- Create 8 database tables
- Create 8 custom pages
- Add custom user role "StayDesk Hotel"
- Set up default options

### Step 3: Configure Settings

1. Go to WordPress Admin → StayDesk → Settings
2. Configure the following:

#### Paystack Configuration
- **Secret Key**: Your Paystack secret key (starts with `sk_`)
- **Public Key**: Your Paystack public key (starts with `pk_`)

Get your keys from: https://dashboard.paystack.com/#/settings/developer

#### Email Configuration
- **From Email**: reach@bendlesstech.com (or your preferred email)
- **From Name**: StayDesk by BendlessTech

#### API Configuration
- **API Key**: Generated automatically (for external integrations)

#### WhatsApp Configuration
- **API Token**: Your WhatsApp Business API token (optional)

### Step 4: Test the Installation

1. Visit your site at `/staydesk-home` to see the homepage
2. Try signing up at `/staydesk-signup`
3. Check email confirmation
4. Login at `/staydesk-login`
5. Access dashboard at `/staydesk-dashboard`

## Pages Created

The plugin creates these pages automatically:

- `/staydesk-home` - Marketing homepage
- `/staydesk-login` - Hotel login
- `/staydesk-signup` - Hotel registration
- `/staydesk-dashboard` - Hotel dashboard
- `/staydesk-bookings` - Booking management
- `/staydesk-profile` - Profile & settings
- `/staydesk-pricing` - Subscription plans
- `/staydesk-admin` - Admin dashboard

## Database Tables

The plugin creates these tables:

- `wp_staydesk_hotels` - Hotel profiles and settings
- `wp_staydesk_rooms` - Room inventory
- `wp_staydesk_bookings` - Booking records
- `wp_staydesk_guests` - Guest information
- `wp_staydesk_transactions` - Payment transactions
- `wp_staydesk_chat_logs` - Chatbot conversation logs
- `wp_staydesk_subscriptions` - Subscription records
- `wp_staydesk_support_tickets` - Support tickets

## User Roles

### StayDesk Hotel Role
Created automatically for hotel users with these capabilities:
- Read site content
- Upload files
- Access hotel dashboard
- Manage bookings
- Manage rooms
- View reports

### Administrator Enhancements
Administrators get additional capabilities:
- `manage_staydesk` - Full plugin management
- `manage_hotels` - Manage all hotels
- `view_hotel_reports` - View analytics

## Email Setup

### Configure WordPress Email
For production use, configure proper email sending:

1. Install an SMTP plugin (recommended):
   - WP Mail SMTP
   - Post SMTP
   - Easy WP SMTP

2. Configure to send from `reach@bendlesstech.com`

3. Test email sending from StayDesk Settings

### Email Templates
The plugin sends these emails:
- Account confirmation (on signup)
- Booking confirmation (after booking)
- Payment reminders
- Refund notifications
- Subscription expiry notices

## Paystack Integration

### Setup Paystack

1. Create account at https://paystack.com
2. Complete business verification
3. Get API keys from Settings → API Keys & Webhooks
4. Use TEST keys for testing
5. Switch to LIVE keys for production

### Test Mode
- Use test cards: 4084084084084081 (successful)
- Amount: Any amount in kobo (multiply by 100)

### Live Mode
- Complete KYC verification
- Switch to live keys
- Test with small amounts first

## WhatsApp Integration

### Basic Setup (Current)
- Widget links to: 07120018023
- Fallback for chatbot

### Advanced Setup (Optional)
1. Get WhatsApp Business API access
2. Set up webhook endpoints
3. Configure API token in settings
4. Enable automated notifications

## Subscription Plans

### Monthly Plan
- **Price**: ₦49,900 per month
- **Discount**: 10% off for first 10 hotels
- **Final Price**: ₦44,910 (with discount)

### Yearly Plan
- **Price**: ₦598,800 per year
- **Discount**: 10% off for first 10 hotels
- **Final Price**: ₦538,920 (with discount)

### Subscription Features
- Automatic expiry checking (daily cron)
- Email notifications before expiry
- Access restrictions on expired subscriptions
- Chatbot disabled on expired subscriptions

## Chatbot Configuration

### Basic Setup
The chatbot works automatically for:
- Room availability queries
- Booking status checks
- Pricing information
- General enquiries

### Languages Supported
- English (default)
- Nigerian Pidgin English

### Chatbot Features
- Query hotel database
- Check room availability
- Retrieve booking information
- Process guest enquiries
- Fallback to WhatsApp when needed

## API Endpoints

### Public Endpoints

```
GET /wp-json/staydesk/v1/hotels
- Get all active hotels

GET /wp-json/staydesk/v1/hotel/{id}
- Get single hotel details

GET /wp-json/staydesk/v1/rooms?hotel_id={id}
- Get available rooms

POST /wp-json/staydesk/v1/chatbot
- Send message to chatbot
Body: { hotel_id, session_id, message, language }
```

### Protected Endpoints

```
POST /wp-json/staydesk/v1/bookings
- Create new booking
Headers: X-API-Key: {your_api_key}
Body: { room_id, check_in_date, check_out_date, guest_name, guest_email, guest_phone }
```

## Troubleshooting

### Email Not Sending
1. Check SMTP configuration
2. Verify "From Email" in settings
3. Test with WP Mail SMTP test email
4. Check server email logs

### Paystack Errors
1. Verify API keys are correct
2. Check if using test/live keys appropriately
3. Ensure cURL is enabled
4. Check Paystack dashboard for errors

### Database Errors
1. Check table creation in phpMyAdmin
2. Verify database permissions
3. Check WordPress debug log
4. Re-activate plugin to recreate tables

### Login Issues
1. Verify email confirmation
2. Check password requirements (min 8 characters)
3. Clear browser cache
4. Check user role assignment

### Dashboard Access
1. Verify subscription status
2. Check user role (should be staydesk_hotel)
3. Clear cookies and try again
4. Check for redirect loops

## Security Checklist

- [ ] Update WordPress to latest version
- [ ] Use strong passwords
- [ ] Enable HTTPS/SSL
- [ ] Keep plugin updated
- [ ] Regular database backups
- [ ] Monitor failed login attempts
- [ ] Use security plugins (Wordfence, Sucuri)
- [ ] Restrict file permissions
- [ ] Disable directory listing
- [ ] Use secure API keys

## Performance Optimization

### Recommended
- Use caching plugin (WP Rocket, W3 Total Cache)
- Enable object caching
- Optimize images
- Use CDN for assets
- Enable GZIP compression
- Minify CSS/JS

### Database
- Regular optimization
- Clean up old bookings/logs
- Index optimization
- Use persistent connections

## Backup Strategy

### What to Backup
- Database (all wp_staydesk_* tables)
- Plugin files
- wp-config.php (for settings)
- Uploaded files (if any)

### Recommended Tools
- UpdraftPlus
- BackWPup
- VaultPress
- Manual mysqldump

## Uninstallation

### Clean Uninstall
When you delete the plugin:
- All database tables are dropped
- All plugin pages are deleted
- All options are removed
- Custom roles are removed
- User meta is cleaned up
- Scheduled cron jobs are cleared

### Before Uninstalling
- Export hotel data
- Backup database
- Notify hotels
- Export booking records
- Save transaction history

## Support

### Documentation
- README.md - Quick overview
- INSTALL.md - This file
- CHANGELOG.md - Version history

### Contact
- Email: reach@bendlesstech.com
- WhatsApp: 07120018023
- Website: https://bendlesstech.com

## Updates

### Checking for Updates
Updates will be available through WordPress plugin repository (when published).

### Updating
1. Backup your database
2. Update via WordPress admin
3. Test all features
4. Check for any breaking changes

## Advanced Configuration

### Custom Email Templates
Modify email templates in:
`includes/class-staydesk-notifications.php`

### Custom Chatbot Responses
Modify chatbot logic in:
`includes/class-staydesk-chatbot.php`

### Custom Styling
Override styles in your theme:
```css
.staydesk-dashboard { /* your styles */ }
```

### Hooks & Filters
Available hooks:
- `staydesk_after_booking_created`
- `staydesk_after_payment_verified`
- `staydesk_subscription_activated`
- `staydesk_subscription_expired`

## Production Deployment

### Pre-Launch Checklist
- [ ] Configure live Paystack keys
- [ ] Test payment flow
- [ ] Test email delivery
- [ ] Configure SMTP
- [ ] Enable SSL/HTTPS
- [ ] Set up backups
- [ ] Configure caching
- [ ] Test on mobile devices
- [ ] Review security settings
- [ ] Set up monitoring

### Launch Checklist
- [ ] Announce to potential hotels
- [ ] Monitor first signups
- [ ] Verify email confirmations
- [ ] Test first bookings
- [ ] Monitor payment processing
- [ ] Check chatbot functionality
- [ ] Monitor error logs
- [ ] Collect user feedback

## Maintenance

### Daily
- Check for booking errors
- Monitor payment transactions
- Review chatbot logs

### Weekly
- Check subscription expirations
- Review support tickets
- Database optimization
- Backup verification

### Monthly
- Security audit
- Performance review
- User feedback analysis
- Feature planning

---

**Version**: 1.0.0
**Last Updated**: 2024-01-01
**Author**: BendlessTech
