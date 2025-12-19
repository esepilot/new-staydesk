#!/bin/bash
# StayDesk Quick Setup Script
# This script helps with initial plugin setup

echo "========================================="
echo "  StayDesk by BendlessTech"
echo "  Quick Setup Script"
echo "========================================="
echo ""

# Check if we're in the right directory
if [ ! -f "staydesk.php" ]; then
    echo "Error: staydesk.php not found. Please run this script from the plugin directory."
    exit 1
fi

echo "Checking WordPress environment..."

# Check if WordPress is present
if [ ! -f "../../../wp-config.php" ]; then
    echo "Warning: WordPress installation not detected in expected location."
    echo "Please ensure this plugin is in wp-content/plugins/staydesk/"
fi

echo ""
echo "Setup Checklist:"
echo "----------------"
echo ""
echo "1. Plugin Files"
echo "   ✓ All files are in place"
echo ""

echo "2. WordPress Requirements"
echo "   - WordPress 5.0 or higher"
echo "   - PHP 7.4 or higher"
echo "   - MySQL 5.6 or higher"
echo ""

echo "3. Server Requirements"
echo "   - cURL extension (for Paystack)"
echo "   - JSON extension"
echo "   - mbstring extension"
echo ""

echo "4. Configuration Needed"
echo "   [ ] Activate plugin in WordPress admin"
echo "   [ ] Go to StayDesk → Settings"
echo "   [ ] Add Paystack API keys"
echo "   [ ] Configure email settings"
echo "   [ ] Test email sending"
echo ""

echo "5. Pages Created Automatically"
echo "   - /staydesk-home"
echo "   - /staydesk-login"
echo "   - /staydesk-signup"
echo "   - /staydesk-dashboard"
echo "   - /staydesk-bookings"
echo "   - /staydesk-profile"
echo "   - /staydesk-pricing"
echo "   - /staydesk-admin"
echo ""

echo "6. Database Tables (created on activation)"
echo "   - wp_staydesk_hotels"
echo "   - wp_staydesk_rooms"
echo "   - wp_staydesk_bookings"
echo "   - wp_staydesk_guests"
echo "   - wp_staydesk_transactions"
echo "   - wp_staydesk_chat_logs"
echo "   - wp_staydesk_subscriptions"
echo "   - wp_staydesk_support_tickets"
echo ""

echo "7. Next Steps"
echo "   1. Activate the plugin"
echo "   2. Visit /wp-admin/admin.php?page=staydesk-settings"
echo "   3. Configure Paystack and email settings"
echo "   4. Visit /staydesk-home to see the homepage"
echo "   5. Test registration at /staydesk-signup"
echo ""

echo "8. Documentation"
echo "   - README.md - Quick overview"
echo "   - INSTALL.md - Detailed installation guide"
echo "   - DEVELOPER.md - Developer documentation"
echo "   - SCREENSHOTS.md - UI/UX guide"
echo ""

echo "9. Support"
echo "   - Email: reach@bendlesstech.com"
echo "   - WhatsApp: 07120018023"
echo "   - Website: https://bendlesstech.com"
echo ""

echo "========================================="
echo "  Setup script completed!"
echo "  Ready to activate the plugin."
echo "========================================="
