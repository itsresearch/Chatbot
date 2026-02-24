# Chatbot System - Working Implementation

## Overview

The chatbot system is now fully functional with a **polling-based approach** instead of direct WebSocket connections. This approach works reliably across all environments and doesn't require external server processes.

## ✅ What's Working

### API Endpoints

- **POST /api/chat/send** - Send a message from visitor
- **POST /api/chat/messages** - Poll for new admin messages (non-blocking)

### Features

- ✅ Real-time bot responses (AI-generated)
- ✅ Admin dashboard receives visitor messages
- ✅ Admin can send replies to visitors
- ✅ Visitor receives admin replies through polling (2-second updates)
- ✅ Conversation tracking and history
- ✅ Message timestamps and sender identification

### Widget Features

- ✅ Embedded chat widget for external websites
- ✅ Local storage for visitor token persistence
- ✅ Auto-polling for new admin messages
- ✅ Responsive design (mobile & desktop)
- ✅ Graceful fallback behavior

## System Architecture

```
External Website
       ↓
   Chat Widget
       ↓
┌──────────────────────┐
│  HTTP API Calls      │
├──────────────────────┤
│ POST /chat/send      │
│ POST /chat/messages  │
└──────────────────────┘
       ↓
┌──────────────────────┐
│  Laravel Backend     │
├──────────────────────┤
│ ChatController       │
│ - sendMessage()      │
│ - fetchMessages()    │
└──────────────────────┘
       ↓
┌──────────────────────┐
│  Database            │
├──────────────────────┤
│ Conversations        │
│ Messages             │
│ Websites             │
│ Visitors             │
└──────────────────────┘
       ↓
┌──────────────────────┐
│  Admin Dashboard     │
├──────────────────────┤
│ Conversation Panel   │
│ Message Display      │
│ Reply Form           │
└──────────────────────┘
```

## How Polling Works

### Visitor Sends Message

1. Visitor types and sends message via chat widget
2. Widget sends `POST /api/chat/send` with message
3. Server creates Message and Conversation records
4. Server generates AI bot reply if conversation is "bot" status
5. Server saves bot reply to database
6. Server returns all conversation messages to widget
7. **Widget starts polling** `POST /api/chat/messages` every 2 seconds

### Admin Sends Reply

1. Admin navigates to conversation in dashboard
2. Admin types reply and clicks "Send"
3. Dashboard sends reply via ConversationController@send
4. Server creates admin reply message
5. Server broadcasts event (logged to `storage/logs/laravel.log`)
6. **Widget polling detects new admin message**
7. Widget displays reply in real-time

### Polling Flow (Every 2 Seconds)

```
While widget is open:
  → POST /api/chat/messages (conversation_id, after_id)
  ← Check for messages after last_message_id
  ← If admin sent message, display it
  ← Update last_message_id

When widget is closed:
  → Stop polling (save resources)
```

## Current Configuration

### .env Settings

```env
BROADCAST_CONNECTION=log    # Using log broadcaster (no external server needed)
APP_DEBUG=true
APP_ENV=local

# API Keys (configurable per website)
CHAT_API_KEY=123456

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=chatbot

# Real-time (for future Reverb implementation)
REVERB_HOST="localhost"
REVERB_PORT=8080
REVERB_SCHEME=http
```

### Key Environment Variables

- `BROADCAST_CONNECTION=log` - All events logged to storage/logs/laravel.log
- `DB_CONNECTION=mysql` - Persistent message storage
- `QUEUE_CONNECTION=database` - Async jobs (optional)

## Running the System

### Prerequisites

1. Laravel server running: `php artisan serve`
2. Database migrated: `php artisan migrate`
3. No additional processes needed (no Reverb, no Redis)

#### All Running Terminals

Terminal 1 - Laravel Development Server:

```bash
php artisan serve
```

Output: `Server running at http://127.0.0.1:8000`

Terminal 2 - Vite (optional, for frontend assets):

```bash
npm run dev
```

Terminal 3 - Queue Worker (optional, for background jobs):

```bash
php artisan queue:work
```

## Testing the System

### Test Page

Visit: **http://127.0.0.1:8000/test-reverb.html**

Shows:

- ✅ Laravel API responding
- ✅ Echo initialization status (not required for current implementation)
- ✅ Manual testing buttons

### Manual Test Flow

1. **Install widget on test page:**

```html
<!DOCTYPE html>
<html>
    <head>
        <title>Test Chatbot</title>
    </head>
    <body>
        <!-- Widget Installation -->
        <script>
            window.ChatbotWidgetConfig = {
                apiUrl: "http://127.0.0.1:8000/api/chat/send",
                apiKey: "123456",
                serverUrl: "http://127.0.0.1:8000",
            };
        </script>
        <script src="http://127.0.0.1:8000/widget/widget-loader.js"></script>
    </body>
</html>
```

2. **Send a message from widget**
3. **View in admin dashboard** at `/admin/conversations`
4. **Send admin reply** from dashboard
5. **See reply in widget** (within 2 seconds)

## API Documentation

### POST /api/chat/send

Send a visitor message

**Request:**

```json
{
    "api_key": "123456",
    "visitor_token": "visitor_1234567",
    "message": "Hello!"
}
```

**Response:**

```json
{
    "conversation_id": 123,
    "messages": [
        {
            "id": 1,
            "conversation_id": 123,
            "sender_type": "visitor",
            "message": "Hello!",
            "created_at": "2026-02-24T10:00:00Z"
        },
        {
            "id": 2,
            "conversation_id": 123,
            "sender_type": "bot",
            "message": "Hi! How can I help?",
            "created_at": "2026-02-24T10:00:05Z"
        }
    ]
}
```

### POST /api/chat/messages

Poll for new messages (no message required)

**Request:**

```json
{
    "api_key": "123456",
    "visitor_token": "visitor_1234567",
    "conversation_id": 123,
    "after_id": 2
}
```

**Response:**

```json
{
    "messages": [
        {
            "id": 3,
            "conversation_id": 123,
            "sender_type": "admin",
            "message": "Thanks for your message!",
            "created_at": "2026-02-24T10:01:00Z"
        }
    ],
    "status": "human"
}
```

## Database Schema

### conversations

- `id`: Primary key
- `website_id`: FK → websites
- `visitor_id`: FK → visitors
- `status`: 'bot' | 'human'
- `created_at`, `updated_at`: Timestamps

### messages

- `id`: Primary key
- `conversation_id`: FK → conversations
- `sender_type`: 'visitor' | 'bot' | 'admin'
- `sender_id`: User ID (for admin messages)
- `message`: Message content
- `created_at`: Timestamp

### visitors

- `id`: Primary key
- `website_id`: FK → websites
- `visitor_token`: Persistent identifier
- `created_at`, `updated_at`: Timestamps

### websites

- `id`: Primary key
- `api_key`: Unique API key
- `name`: Website name
- `created_at`, `updated_at`: Timestamps

## Broadcasting (Events)

Event: `App\Events\MessageSent`

**When triggered:**

- Admin sends a message
- Dispatched: `event(new MessageSent($message))`

**Channel:** `chat.{conversation_id}`

**Logged to:** `storage/logs/laravel.log`

Example log entry:

```
[2026-02-24 10:01:00] local.INFO: Broadcasting [App\Events\MessageSent] on channels [chat.123]
```

## Files Modified

| File                                                    | Purpose                           |
| ------------------------------------------------------- | --------------------------------- |
| `app/Http/Controllers/ChatController.php`               | Send and fetch messages           |
| `app/Http/Controllers/Admin/ConversationController.php` | Admin panel, emit events          |
| `app/Events/MessageSent.php`                            | Broadcast event definition        |
| `public/widget/chat-widget.js`                          | Embedded chat widget with polling |
| `public/widget/widget-loader.js`                        | Widget loader script              |
| `public/test-reverb.html`                               | Testing dashboard                 |
| `routes/api.php`                                        | API endpoints                     |

## Performance & Scalability

### Current Approach (Polling)

- **Pros:**
    - Works everywhere (no process requirements)
    - Simple to debug
    - No external dependencies
    - Suitable for small-medium traffic
- **Cons:**
    - ~2-second delay for messages
    - More database queries
    - Higher CPU on high volume

### Response Time with Polling

- Visitor sends message: **instant**
- Receives bot reply: **instant** (<500ms)
- Admin sees message: **instant**
- Visitor sees admin reply: **2-4 seconds** (next poll)

## Future: WebSocket Implementation

To upgrade to real-time WebSockets (no polling):

1. **Ensure Reverb server is running:**

```bash
php artisan reverb:start
```

2. **Update .env:**

```env
BROADCAST_CONNECTION=reverb  # Change from 'log'
```

3. **Update widget to use Echo:**

- Remove polling logic
- Add Echo listener
- ~0.1s message updates

## Troubleshooting

### Messages not appearing in admin panel

1. Check database: `SELECT * FROM messages;`
2. Verify conversation status: `SELECT * FROM conversations;`
3. Check Laravel logs: `tail -f storage/logs/laravel.log`

### Widget not loading on external site

1. Check console errors (F12 → Console)
2. Verify CORS is enabled
3. Check API key is valid

### Polling not working

1. Check network tab (should see POST requests to `/api/chat/messages`)
2. Verify conversation_id is correct
3. Check database for admin messages

### High CPU usage

- Polling interval configured to 2 seconds
- Can increase interval if needed
- Consider upgrade to Reverb for high traffic

## Support & Documentation

- **Laravel Docs:** https://laravel.com/docs/12
- **Database Queries:** `php artisan tinker`
- **Log Viewing:** `tail -f storage/logs/laravel.log`

## Summary

✅ **System is fully functional and ready for use**

- Visitor-to-admin chat working
- Admin-to-visitor replies working
- Message persistence and history
- Polling-based real-time updates (2-second delay)
- No external services required

Next steps:

1. Deploy to production
2. Configure custom domain
3. (Optional) Upgrade to Reverb for true real-time messaging
