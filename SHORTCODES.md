# StayDesk WordPress Plugin - Shortcodes Reference

This document lists all available shortcodes that can be used to embed StayDesk pages anywhere in your WordPress site.

## 📋 All Available Shortcodes

### Authentication Pages

#### 1. Signup Page
```
[staydesk_signup]
```
**Description**: Hotel registration form with email verification
**URL**: `/staydesk-signup`
**Features**:
- Hotel name, email, phone, password fields
- Email verification via 6-digit code
- Luxury dark theme design
- AJAX form submission

#### 2. Login Page
```
[staydesk_login]
```
**Description**: Hotel login form
**URL**: `/staydesk-login`
**Features**:
- Email and password authentication
- Remember me option
- Automatic redirect to dashboard
- Success/error messages

#### 3. Email Verification Page
```
[staydesk_verify_email]
```
**Description**: Email verification code input page
**URL**: `/staydesk-verify-email`
**Features**:
- 6-digit code input field
- Resend code functionality
- 1-hour code expiry
- Swift verification process

### Hotel Management Pages

#### 4. Dashboard
```
[staydesk_dashboard]
```
**Description**: Hotel dashboard with metrics and quick actions
**URL**: `/staydesk-dashboard`
**Features**:
- Real-time statistics (bookings, rooms, revenue)
- Real-time clock display
- Quick action buttons
- Subscription status display
- Welcome message with hotel name

#### 5. Room Management
```
[staydesk_rooms]
```
**Description**: Complete room management interface
**URL**: `/staydesk-rooms`
**Features**:
- View all rooms in table format
- Add new rooms with details
- Toggle room availability status
- Delete rooms with confirmation
- Room type, pricing, capacity management

#### 6. Booking Management
```
[staydesk_bookings]
```
**Description**: Comprehensive booking oversight
**URL**: `/staydesk-bookings`
**Features**:
- View all bookings with filters
- Statistics cards (total, pending, confirmed, completed)
- Update booking status
- Guest information display
- Payment tracking

#### 7. Profile & Settings
```
[staydesk_profile]
```
**Description**: Hotel profile and settings management
**URL**: `/staydesk-profile`
**Features**:
- Edit hotel information (name, email, phone, address, description)
- View subscription status
- Get chatbot embed code
- One-click copy functionality
- Update contact details

#### 8. Pricing & Subscription
```
[staydesk_pricing]
```
**Description**: Subscription plans and payment
**URL**: `/staydesk-pricing`
**Features**:
- Monthly plan: ₦49,900/month
- Yearly plan: ₦598,800/year (10% discount for first 10 hotels)
- Paystack payment integration
- Plan comparison cards

### Marketing & Public Pages

#### 9. Homepage
```
[staydesk_homepage]
```
**Description**: Marketing landing page
**URL**: `/staydesk-home`
**Features**:
- Feature showcase
- Pricing preview
- Call-to-action buttons
- Apple-inspired design
- WhatsApp widget (lower right corner)

### Admin Pages

#### 10. Admin Dashboard
```
[staydesk_admin_dashboard]
```
**Description**: Platform administration for BendlessTech
**URL**: `/staydesk-admin`
**Features**:
- All hotels overview
- Platform statistics
- Booking management across all hotels
- Revenue analytics
- Subscription management

## 🎨 Design Features

All shortcodes maintain the **luxury dark theme**:
- Dark black hue backgrounds (#0a0a0a, #1a1a1a, #2a2a2a)
- Gold gradient accents (#D4AF37 to #FFD700)
- Smooth animations and transitions
- Responsive design for all devices
- Premium hover effects

## 💡 How to Use Shortcodes

### Method 1: WordPress Page/Post Editor
1. Create a new page or edit an existing one
2. Add a shortcode block
3. Paste the shortcode (e.g., `[staydesk_dashboard]`)
4. Publish the page

### Method 2: PHP Template
```php
<?php echo do_shortcode('[staydesk_dashboard]'); ?>
```

### Method 3: Widget
1. Go to Appearance > Widgets
2. Add a "Shortcode" or "Text" widget
3. Paste the shortcode
4. Save

## 📱 Pages Auto-Created on Activation

When the plugin is activated, the following pages are automatically created with their respective shortcodes:

| Page URL | Shortcode | Title |
|----------|-----------|-------|
| `/staydesk-home` | `[staydesk_homepage]` | StayDesk - Hotel Assistant Platform |
| `/staydesk-login` | `[staydesk_login]` | Login - StayDesk |
| `/staydesk-signup` | `[staydesk_signup]` | Sign Up - StayDesk |
| `/staydesk-verify-email` | `[staydesk_verify_email]` | Verify Email - StayDesk |
| `/staydesk-dashboard` | `[staydesk_dashboard]` | Dashboard - StayDesk |
| `/staydesk-rooms` | `[staydesk_rooms]` | Room Management - StayDesk |
| `/staydesk-bookings` | `[staydesk_bookings]` | Bookings - StayDesk |
| `/staydesk-profile` | `[staydesk_profile]` | Profile & Settings - StayDesk |
| `/staydesk-pricing` | `[staydesk_pricing]` | Pricing & Subscription - StayDesk |
| `/staydesk-admin` | `[staydesk_admin_dashboard]` | Admin Dashboard - StayDesk |

## 🔒 Access Control

Some shortcodes have built-in access control:

**Requires Authentication** (redirects to login if not logged in):
- `[staydesk_dashboard]`
- `[staydesk_rooms]`
- `[staydesk_bookings]`
- `[staydesk_profile]`

**Redirects to Dashboard if Already Logged In**:
- `[staydesk_login]`
- `[staydesk_signup]`
- `[staydesk_verify_email]`

**Admin Only**:
- `[staydesk_admin_dashboard]`

## 🎯 Quick Reference

### For Hotels
```
Registration Flow:
1. [staydesk_signup] → 2. [staydesk_verify_email] → 3. [staydesk_login] → 4. [staydesk_dashboard]

Management Pages:
- [staydesk_rooms] - Manage rooms
- [staydesk_bookings] - Manage bookings  
- [staydesk_profile] - Edit profile & get chatbot code
- [staydesk_pricing] - Subscribe or upgrade
```

### For Admins
```
- [staydesk_admin_dashboard] - Platform overview
```

### For Public
```
- [staydesk_homepage] - Marketing page
- [staydesk_pricing] - View plans
```

## 📞 Support

For any issues with shortcodes, contact:
- **Email**: reach@bendlesstech.com
- **WhatsApp**: 07120018023
- **Website**: https://bendlesstech.com

---

**Built with ❤️ by BendlessTech**
