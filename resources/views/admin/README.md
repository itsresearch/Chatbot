# Chatbot Admin Dashboard Template

## Overview

This is a complete Admin Dashboard template for a Chatbot Application built using Laravel Blade, Bootstrap 5, and responsive design principles. This is a **frontend-only template** with no backend logic or database integration.

---

## Files Created

### 1. `resources/views/layouts/admin.blade.php`

**Main Layout Template** - The base layout used by all admin pages.

**Features:**

- Fixed top navbar with search bar, notifications, and profile dropdown
- Collapsible sidebar with navigation menu
- Main content area with `@yield('content')`
- Bootstrap 5 CDN integration
- Bootstrap Icons for UI elements
- Responsive design with custom CSS
- Mobile-friendly toggle for sidebar

**Navbar Components:**

- Application name: "Chatbot Admin"
- Search functionality
- Notification bell with badge counter
- Admin profile dropdown (Profile, Settings, Logout)

**Sidebar Menu Items:**

- Dashboard
- Conversations
- Active Chats
- Closed Chats
- Analytics
- Settings
- Logout

**Design Features:**

- Gradient purple sidebar (`#667eea` to `#764ba2`)
- Fixed header with shadow
- Smooth transitions and animations
- Modern color scheme with light backgrounds
- Soft shadows and rounded corners

---

### 2. `resources/views/admin/dashboard.blade.php`

**Dashboard Page** - Main statistics and overview page.

**Sections:**

1. **Page Header**
    - Title with description
    - Export Report button

2. **Statistics Cards** (4 cards in responsive grid)
    - Total Conversations: 1,248
    - Active Chats: 24
    - Closed Chats: 892
    - Visitors Today: 632

    Each card features:
    - Color-coded icon (with unique background colors)
    - Large stat number
    - Descriptive label
    - Hover animation effect

3. **Recent Conversations Table**
    - 6 sample conversations with:
        - Visitor avatar and name
        - Email address
        - Last message preview
        - Date and time
        - Status badge (Active/Closed)
        - View action button
    - Pagination controls at bottom

**Design:**

- Clean card layout
- Responsive table with avatars
- Color-coded status badges
- Interactive hover effects

---

### 3. `resources/views/admin/conversations.blade.php`

**Conversations Management Page** - View and filter all conversations.

**Sections:**

1. **Page Header**
    - Title with description
    - Filter and Export buttons

2. **Advanced Search & Filter Panel**
    - Search by visitor name/email
    - Filter by Status (All/Active/Closed/Pending)
    - Filter by Date Range (7 days/30 days/90 days/All time)
    - Sort options (Newest/Oldest/Most Active/Least Active)

3. **Conversations Table**
    - 10 sample conversations
    - Columns:
        - Checkbox for bulk selection
        - Visitor name with avatar
        - Email address
        - Last message (truncated)
        - Date and time
        - Status badge
        - View Chat action button
    - Pagination showing 1-10 of 1,248 conversations

**Design:**

- Modern filter interface
- Responsive data table
- Selectable rows with checkboxes
- Quick action buttons
- Smooth interactions

---

### 4. `resources/views/admin/chat.blade.php`

**Chat View Page** - Conversation viewer with split layout.

**Layout:**

- **Left Sidebar (35% width on desktop):**
    - "Conversations" header
    - Search input
    - Scrollable chat list with 7 conversations
    - Each item shows:
        - Avatar
        - Visitor name
        - Last message preview
        - Timestamp
    - Active state highlighting

- **Right Chat Window (65% width on desktop):**
    - Chat header with visitor info (name, online status)
    - Info and menu buttons
    - Scrollable message area with sample conversation
    - Message styling:
        - Visitor messages: aligned left, white background
        - Admin messages: aligned right, purple background
        - Timestamps on each message
    - Message input area at bottom with text field and Send button

**JavaScript Features:**

- Auto-scroll to bottom of messages
- Click handlers for chat items to switch conversations
- Send message functionality (demo)
- Enter key to send messages

**Responsive Behavior:**

- Desktop: Side-by-side layout
- Tablet: Split layout with reduced heights
- Mobile: Stacked layout with conversation list above

---

## Blade Template Features Used

### Template Inheritance

```blade
@extends('layouts.admin')
@section('title', 'Page Title')
@section('content')
    <!-- Page content -->
@endsection
```

### Dynamic Sections

- `@yield('content')` - Main content area
- `@section('title')` - Page title
- `@section('extra-styles')` - Additional CSS
- `@section('scripts')` - Additional JavaScript

---

## Design System

### Color Palette

- **Primary Purple:** `#667eea`
- **Secondary Purple:** `#764ba2`
- **Success Green:** `#10b981`
- **Danger Red:** `#ef4444`
- **Warning Orange:** `#f59e0b`
- **Light Gray:** `#f8f9fa`, `#f9fafb`
- **Dark Gray:** `#1f2937`
- **Text Gray:** `#6b7280`
- **Border Gray:** `#e5e7eb`

### Typography

- Font Family: Segoe UI, Tahoma, Geneva, Verdana, sans-serif
- Font Sizes: 12px (small), 14px (default), 16px (larger), 20px+ (headers)
- Font Weights: 500 (medium), 600 (semibold), 700 (bold)

### Components

- **Cards:** 25px padding, rounded 12px, soft shadow
- **Buttons:** Rounded 8px, padding 10px 20px, smooth transitions
- **Badges:** Varied colors with rounded 20px, uppercase text
- **Inputs:** Rounded 8px, light gray background, focus state
- **Tables:** Striped rows, hover effect, border separators

### Spacing

- Standard padding: 15px, 20px, 25px, 30px
- Standard margin: 10px, 15px, 20px, 30px
- Gap between elements: Consistent use of Bootstrap gap utilities

### Shadows

- Light shadow: `0 2px 8px rgba(0, 0, 0, 0.08)`
- Medium shadow: `0 2px 12px rgba(0, 0, 0, 0.08)`
- Heavy shadow: `0 4px 20px rgba(0, 0, 0, 0.12)`

---

## Bootstrap 5 Classes Used

### Layout

- `container`, `row`, `col-*`
- Flexbox utilities: `d-flex`, `align-items-center`, `justify-content-between`
- Display: `d-none`, `d-lg-block`, `d-sm-inline`
- Gap: `gap-2`, `gap-3`

### Components

- Navbar: `.navbar`, `.navbar-brand`
- Buttons: `.btn`, `.btn-primary`, `.btn-outline-secondary`
- Cards: `.card`
- Tables: `.table`, `.table-hover`, `.table-responsive`
- Badges: `.badge`, `.badge-success`, `.badge-secondary`
- Dropdowns: `.dropdown`, `.dropdown-menu`
- Forms: `.form-control`, `.form-label`, `.form-select`
- Pagination: `.pagination`, `.page-item`

### Utilities

- Sizing: `width: 100%`, `max-width`
- Positioning: `position-absolute`, `position-relative`
- Spacing: `m-*`, `p-*`, `mb-*`, `mt-*`
- Text: `text-center`, `text-muted`, `text-dark`, `text-success`
- Borders: `border`, `border-bottom`, `rounded-circle`

---

## Responsive Breakpoints

### Desktop (> 992px)

- Full sidebar (260px) always visible
- Collapse mode toggle available
- Chat layout: 35% left, 65% right

### Tablet (768px - 992px)

- Sidebar toggles with mobile menu
- Chat layout with reduced sidebar height
- Adjusted padding and spacing

### Mobile (< 768px)

- Hidden sidebar (toggle-able)
- Full-width layout
- Chat list hidden, chat window full width
- Optimized touch interactions
- Reduced padding for better mobile experience

---

## Static Data Included

### Conversations (All pages)

- 10+ sample visitor profiles
- Avatar generation using UI Avatars API
- Unique names, emails, and messages
- Realistic chat scenarios

### Statistics (Dashboard)

- Total Conversations: 1,248
- Active Chats: 24
- Closed Chats: 892
- Visitors Today: 632

### Message History (Chat page)

- 10 sample messages
- Mix of visitor and admin messages
- Timestamps and realistic conversation flow

---

## Customization Guide

### Change Colors

Edit the color variables in `layouts/admin.blade.php`:

```css
.sidebar {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
```

### Modify Sidebar Items

Edit the `<ul class="sidebar-menu">` in `layouts/admin.blade.php`:

```blade
<li>
    <a href="#">
        <i class="bi bi-icon-name"></i>
        <span class="sidebar-text">Menu Item</span>
    </a>
</li>
```

### Add New Pages

Create new files in `resources/views/admin/` and extend the admin layout:

```blade
@extends('layouts.admin')
@section('title', 'New Page')
@section('content')
    <!-- Your content here -->
@endsection
```

### Update Statistics

Replace the hardcoded numbers in the stat cards with actual data.

### Connect Message Sending

Replace the demo click handler with actual API calls to your backend.

---

## Icons Used

All icons are from **Bootstrap Icons** (https://icons.getbootstrap.com):

- `bi-house-door-fill` - Dashboard
- `bi-chat-left-text` - Conversations
- `bi-circle-fill` - Active indicator
- `bi-check-circle-fill` - Closed/checked
- `bi-graph-up` - Analytics
- `bi-gear-fill` - Settings
- `bi-box-arrow-right` - Logout
- `bi-search` - Search
- `bi-bell` - Notifications
- `bi-person` - Profile
- `bi-chat-dots` - Chat/messages
- `bi-download` - Download/export
- `bi-eye` - View
- `bi-info-circle` - Information
- `bi-three-dots-vertical` - More options
- `bi-send` - Send message

---

## Browser Support

- Chrome/Edge (latest)
- Firefox (latest)
- Safari (latest)
- Mobile browsers

---

## Notes

1. **No Backend:** All data is static/dummy. Connect to your backend to make it functional.
2. **No Database:** This is purely frontend. Add database integration as needed.
3. **CDN Dependencies:**
    - Bootstrap 5.3.0
    - Bootstrap Icons 1.11.0
4. **Blade Syntax:** Uses standard Laravel Blade template syntax.
5. **Responsive:** Fully responsive from mobile to desktop.
6. **Accessibility:** Uses semantic HTML and ARIA labels where appropriate.

---

## File Structure

```
resources/
├── views/
│   ├── layouts/
│   │   └── admin.blade.php (Main Layout)
│   └── admin/
│       ├── dashboard.blade.php (Dashboard Page)
│       ├── conversations.blade.php (Conversations Page)
│       └── chat.blade.php (Chat View Page)
```

---

## Quick Start

1. **View Dashboard:**
   Access `resources/views/admin/dashboard.blade.php`

2. **View Conversations:**
   Access `resources/views/admin/conversations.blade.php`

3. **View Chat:**
   Access `resources/views/admin/chat.blade.php`

4. **All inherit from:**
   `resources/views/layouts/admin.blade.php`

---

**Template Created:** February 20, 2026
**Technology:** Laravel Blade + Bootstrap 5
**Status:** Frontend Template Only (No Backend)
