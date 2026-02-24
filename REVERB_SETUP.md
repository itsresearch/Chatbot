# Chatbot Reverb WebSocket Integration Guide

## Overview
This chatbot system uses Laravel Reverb for real-time WebSocket communication. Admins can see visitor messages instantly and send real-time responses.

## System Components

### Backend
- **Laravel Reverb**: WebSocket server for real-time communication
- **Event Broadcasting**: Uses `MessageSent` event to broadcast messages
- **ConversationController**: Handles message sending and event dispatching

### Frontend
- **Laravel Echo**: JavaScript library for listening to broadcasted events
- **Chat Widget**: Embedded widget for external websites
- **Widget Loader**: Script that initializes Echo and loads the chat widget

## Setup Instructions

### 1. Verify Environment Configuration
Ensure your `.env` file has the correct Reverb configuration:

```env
BROADCAST_CONNECTION=reverb

REVERB_APP_ID=947361
REVERB_APP_KEY=v9tsewjswwxoflomix2d
REVERB_APP_SECRET=ns8j7i9qly9bdauaddbv
REVERB_HOST="localhost"
REVERB_PORT=8080
REVERB_SCHEME=http

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

### 2. Start the Reverb Server
Run the Reverb server in a separate terminal:

```bash
php artisan reverb:start
```

Expected output:
```
INFO  Server running at http://127.0.0.1:8080
```

### 3. Run the Main Application
In another terminal, start the Laravel development server:

```bash
php artisan serve
```

### 4. Compile Frontend Assets
In a third terminal, start Vite development server:

```bash
npm run dev
```

## Using the Chat Widget on External Websites

### Option 1: Using the Widget Loader (Recommended)

Add this snippet to any external website:

```html
<script>
  window.ChatbotWidgetConfig = {
    apiUrl: 'https://your-domain.com/api/chat/send',
    apiKey: 'your-website-api-key',
    serverUrl: 'https://your-domain.com',
    logoUrl: 'https://your-domain.com/images/chatbot-logo.png'
  };
</script>
<script src="https://your-domain.com/widget/widget-loader.js"></script>
```

The widget loader will:
1. Load Laravel Echo (for real-time updates)
2. Initialize the Reverb WebSocket connection
3. Load the chat widget

### Option 2: Direct Widget Loading

If you want to load just the chat widget:

```html
<script>
  window.ChatbotWidgetConfig = {
    apiUrl: 'https://your-domain.com/api/chat/send',
    apiKey: 'your-website-api-key',
    serverUrl: 'https://your-domain.com'
  };
</script>
<script src="https://your-domain.com/widget/chat-widget.js"></script>
```

Note: This won't have real-time updates unless Laravel Echo is already loaded.

## Features

### Real-Time Message Updates
- Admin sends a message from the conversation panel
- The `MessageSent` event is broadcasted via Reverb
- Visitor receives the message instantly via WebSocket
- No polling/refresh needed

### Fallback Messaging
- Server returns bot reply if conversation is in "bot" status
- Visitor can receive messages through HTTP polling if Reverb is unavailable

### API Response Format

The `/api/chat/send` endpoint returns:

```json
{
  "conversation_id": 123,
  "messages": [
    {
      "id": 1,
      "conversation_id": 123,
      "sender_type": "visitor",
      "message": "Hello",
      "created_at": "2026-02-24T10:00:00Z"
    },
    {
      "id": 2,
      "conversation_id": 123,
      "sender_type": "bot",
      "message": "Hi there! How can I help?",
      "created_at": "2026-02-24T10:00:05Z"
    }
  ]
}
```

## Broadcasting Events

### MessageSent Event

Located at: `app/Events/MessageSent.php`

Triggered when:
- Admin sends a message (via `ConversationController@send`)
- Broadcasts on channel: `chat.{conversation_id}`
- Event name: `MessageSent`

Usage:
```php
event(new MessageSent($message));
```

## Troubleshooting

### Issue: WebSocket connection refused
**Solution**: Ensure Reverb server is running on port 8080:
```bash
php artisan reverb:start
```

### Issue: Messages not appearing in real-time
**Solution**: 
1. Check browser console for errors (F12 → Console)
2. Verify `BROADCAST_CONNECTION=reverb` in `.env`
3. Verify Reverb is running
4. Clear browser cache and reload

### Issue: CORS errors (for external websites)
**Solution**: Ensure CORS is configured in your Laravel app for the Reverb WebSocket connections. Reverb handles this automatically, but you may need to configure CORS for your API endpoints separately.

### Issue: Conversation not showing for admin
**Solution**:
1. Verify visitor sent at least one message
2. Refresh the conversation list
3. Check database: `SELECT * FROM conversations;`

## Architecture Diagram

```
External Website
       ↓
   Chat Widget (on external site)
       ↓
   Widget Loader (loads Echo)
       ↓
   [HTTP API]          [WebSocket]
       ↓                    ↓
   Chatbot Server ← Reverb Server
        ↓
   Database
        ↓
   Admin Panel Dashboard
```

## Database Schema

### Conversations Table
- `id`: Primary key
- `website_id`: FK to websites table
- `visitor_id`: FK to visitors table
- `status`: 'bot' or 'human'
- `created_at`, `updated_at`: Timestamps

### Messages Table
- `id`: Primary key
- `conversation_id`: FK to conversations table
- `sender_type`: 'visitor', 'bot', or 'admin'
- `sender_id`: User ID (for admin messages)
- `message`: Message content
- `created_at`: Timestamp

## Production Deployment Considerations

1. **Domain Configuration**: Update `.env` with your production domain
2. **SSL/TLS**: Use `wss://` for secure WebSocket connections
3. **Environment**: Set `APP_ENV=production` and `APP_DEBUG=false`
4. **Monitoring**: Monitor Reverb process to ensure it stays running
5. **Load Balancing**: If using multiple servers, ensure Reverb is accessible from all of them

## Support

For more information on Laravel Reverb, visit: https://laravel.com/docs/reverb
