# StayDesk Screenshots & UI Guide

## Homepage (`/staydesk-home`)

### Hero Section
- **Background**: Blue gradient (135deg, #0066CC to #004C99)
- **Title**: "Welcome to StayDesk" (4rem, white, bold)
- **Subtitle**: "The Ultimate Hotel Management Platform for Nigerian Hotels"
- **CTA Buttons**: 
  - Primary: "Get Started" (Gold #D4AF37)
  - Secondary: "View Pricing" (White border)
- **Animation**: Fade in up effect on load

### Features Grid
- **Layout**: 3-column grid (responsive)
- **Cards**: White background with hover lift effect
- **Icons**: Emoji icons (🏨, 💳, 🤖, 📊, 📧, 🛡️)
- **Hover**: Elevates with enhanced shadow

## Login Page (`/staydesk-login`)

### Layout
- **Background**: Full-screen blue gradient
- **Container**: Centered white card (450px max-width)
- **Border Radius**: 20px with shadow
- **Animation**: Slide in from top

### Form Fields
- Email input with icon
- Password input with show/hide toggle
- Remember me checkbox
- Login button (full-width, blue)
- Sign up link at bottom

### States
- Email confirmation success message (green)
- Error messages (red)
- Loading state with spinner

## Signup Page (`/staydesk-signup`)

### Layout
Similar to login but with additional fields:
- Hotel Name
- Email Address
- Phone Number
- Password
- Confirm Password

### Features
- Real-time password strength indicator
- Email format validation
- Phone number formatting
- Terms & conditions checkbox

## Dashboard (`/staydesk-dashboard`)

### Header Section
- **Left**: 
  - Welcome message with hotel name
  - Subtitle with current date
- **Right**: 
  - Real-time clock (HH:MM:SS)
  - Logout button (red)

### Subscription Alert
- **Active**: Green background with checkmark
- **Expired**: Red background with warning icon
- **Expiring Soon**: Yellow background

### Statistics Cards (4-column grid)
1. Total Bookings (🏨 icon)
2. Pending Bookings (⏳ icon)
3. Total Revenue (💰 icon, formatted as ₦)
4. Available/Total Rooms (🛏️ icon)

**Card Style**:
- White background
- Large number (2.5rem, blue)
- Icon above
- Hover: Lifts up
- Animation: Counter animation on load

### Action Sections (3-column grid)
1. Bookings Management
2. Rooms Management
3. Payment Verification
4. Refund Management
5. Guest Enquiries (with count badge)
6. Profile & Settings
7. Subscription Status

**Section Cards**:
- Icon + Title + Description
- Blue action button
- Hover effect

## Pricing Page (`/staydesk-pricing`)

### Header
- Title: "Choose Your Plan" (3rem)
- Subtitle: "Affordable pricing for hotels of all sizes"

### Pricing Cards (2-column grid)

**Monthly Plan**:
- Price: ₦49,900/month
- Standard features list
- Blue subscribe button

**Yearly Plan** (Popular):
- "BEST VALUE" badge (rotated, gold)
- Price: ₦598,800/year
- All features + premium features
- Gold subscribe button
- 3px gold border

**Discount Banner**:
- Yellow background
- "🎉 First 10 hotels get 10% off!"

### Features List
- Green checkmarks (✓)
- Features aligned left
- Clear typography

## Chatbot Widget

### Button (Fixed Bottom-Right)
- WhatsApp green (#25D366)
- 60px diameter circle
- WhatsApp icon (white)
- Hover: Scales to 1.1
- Box shadow with green glow

### Chat Window
- Opens above button
- 350px width
- Rounded corners (15px)
- **Header**: 
  - Dark green background (#075E54)
  - "StayDesk Support"
  - "Typically replies instantly"
  - Close button (×)

- **Body**:
  - Light beige background (#ECE5DD)
  - Bot messages in white bubbles
  - User messages in green bubbles (right-aligned)
  - Max height: 400px, scrollable

- **Footer**:
  - "Chat on WhatsApp" button
  - WhatsApp icon
  - Green button

## Color Palette

### Primary Colors
- **Blue**: #0066CC (buttons, headings, accents)
- **Gold**: #D4AF37 (premium, highlights)
- **Green**: #28A745 (success states)
- **Red**: #DC3545 (errors, warnings)
- **Orange**: #FFC107 (warnings)

### Neutral Colors
- **Text**: #333333 (dark gray)
- **Background**: #FFFFFF (white)
- **Light BG**: #f8f9fa (off-white)
- **Border**: #e0e0e0 (light gray)

### Gradient
- **Hero**: linear-gradient(135deg, #0066CC 0%, #004C99 100%)

## Typography

### Font Family
```css
font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
```

### Font Sizes
- **Hero H1**: 4rem (64px)
- **Hero P**: 1.5rem (24px)
- **Section Title**: 2.5rem (40px)
- **Card Title**: 1.5rem (24px)
- **Body**: 1rem (16px)
- **Small**: 0.85rem (13.6px)

### Font Weights
- **Bold**: 700 (headings, buttons)
- **Semi-bold**: 600 (labels, important text)
- **Regular**: 400 (body text)

## Animations

### Fade In Up
```css
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
```

### Slide In
```css
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
```

### Hover Effects
- **Cards**: `translateY(-5px)` with enhanced shadow
- **Buttons**: `translateY(-2px)` with shadow
- **Scale**: 1.05 for icons

### Transitions
- **Standard**: `all 0.3s ease`
- **Quick**: `all 0.2s ease`
- **Slow**: `all 0.5s ease-out`

## Responsive Design

### Breakpoints
- **Desktop**: 1200px+
- **Tablet**: 768px - 1199px
- **Mobile**: < 768px

### Mobile Adjustments
- Hero title: 2.5rem (from 4rem)
- Single column grids
- Reduced padding
- Stacked buttons
- Full-width forms
- Hamburger menu (if needed)

## Accessibility

### WCAG Compliance
- Color contrast: Minimum 4.5:1
- Focus indicators: 2px solid outline
- ARIA labels on interactive elements
- Keyboard navigation support
- Screen reader friendly

### Focus States
```css
*:focus {
    outline: 2px solid #0066CC;
    outline-offset: 2px;
}
```

## Loading States

### Spinner
- 40px diameter
- Blue border with spinning top
- Centered in container

### Button Loading
- Text changes to "Processing..."
- Disabled state
- Optional spinner icon

### Page Loading
- Skeleton screens for cards
- Progressive loading
- Fade-in when ready

## Form Validation

### Visual Feedback
- **Valid**: Green border
- **Invalid**: Red border + error message
- **Focus**: Blue border + shadow
- **Success**: Green checkmark icon

### Error Messages
- Red text below field
- Icon (⚠️) before message
- Fade in animation

## Icons

### Source
- Emoji icons (native)
- SVG icons for UI elements
- Font Awesome (optional)

### Usage
- Large icons in feature cards (3rem)
- Small icons in buttons (1rem)
- Status icons (0.85rem)

## Spacing

### Standard Spacing
- **xs**: 5px
- **sm**: 10px
- **md**: 20px
- **lg**: 30px
- **xl**: 40px
- **2xl**: 60px

### Section Padding
- Desktop: 60px - 100px
- Mobile: 30px - 40px

### Card Padding
- Desktop: 40px
- Mobile: 20px

## Shadows

### Card Shadow
```css
box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
```

### Hover Shadow
```css
box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
```

### Button Shadow
```css
box-shadow: 0 10px 25px rgba(0, 102, 204, 0.3);
```

## Border Radius

### Standard
- **Small**: 8px (inputs, badges)
- **Medium**: 10px (cards, modals)
- **Large**: 15px (sections)
- **XL**: 20px (main containers)
- **Round**: 50% (buttons, avatars)

## Implementation Notes

All styles are implemented in:
- `/public/css/staydesk-public.css` - Main public styles
- `/admin/css/staydesk-admin.css` - Admin area styles
- Template files contain inline styles for specific components

JavaScript animations are in:
- `/public/js/staydesk-public.js` - Main interactions
- `/admin/js/staydesk-admin.js` - Admin interactions

---

**Design System**: Apple-inspired
**Framework**: Custom CSS (no dependencies)
**Browser Support**: Modern browsers (Chrome, Firefox, Safari, Edge)
**Mobile-First**: Yes
**RTL Support**: Not yet implemented
