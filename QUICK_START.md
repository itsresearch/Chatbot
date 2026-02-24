# ✅ Chatbot System - Quick Start Guide

## System Status

**✅ All Systems Operational**

- Laravel API: http://127.0.0.1:8000 ✅
- Chat Widget: Ready to embed ✅
- Message polling: Working (2-second updates) ✅
- Database: Configured with test data ✅

## 🚀 Getting Started (5 Minutes)

### Step 1: Verify All Servers Running

```bash
# Terminal 1 - Laravel Server
php artisan serve

# Terminal 2 - Vite (frontend compilation)
npm run dev

# Terminal 3 - Queue Worker (optional)
php artisan queue:work
```

### Step 2: Test the System

Visit: **http://127.0.0.1:8000/test-reverb.html**

All tests should pass:

- ✅ Laravel API Responding
- ✅ Chat Widget Ready
- ✅ Polling enabled

### Step 3: Embed Widget on Your Website

Add this code to any HTML page:

```html
<script>
    window.ChatbotWidgetConfig = {
        apiUrl: "http://127.0.0.1:8000/api/chat/send",
        apiKey: "123456",
        serverUrl: "http://127.0.0.1:8000",
        logoUrl: "http://127.0.0.1:8000/images/chatbot-logo.png",
    };
</script>
<script src="http://127.0.0.1:8000/widget/widget-loader.js"></script>
```

The widget launcher appears in bottom-right corner.

### Step 4: Access Admin Dashboard

Visit: **http://127.0.0.1:8000/admin/conversations**

- View all visitor conversations
- Send replies to visitors
- Replies appear instantly in visitor's chat

## 📊 How the Chat Works

```
Visitor                           Admin
   |                               |
   | Sends message                 |
   +---> API /chat/send            |
   |        Stores message         |
   |        AI generates reply     |
   |        Returns messages       |
   |                     <----- Views in dashboard
   |                               |
   |                          Sends reply
   |                          Stores in DB
   |                               |
   | Polls every 2 sec             |
   <------- API /chat/messages    |
   |        Finds new message      |
   | Displays admin reply          |
```

**Response times:**

- Visitor → Bot: **instant** (<500ms)
- Admin → Visitor: **2-4 seconds** (next poll)

## 🔧 Configuration

### Customize Widget

Edit widget config before loading:

```html
<script>
    window.ChatbotWidgetConfig = {
        apiUrl: "https://your-domain.com/api/chat/send",
        apiKey: "your-unique-api-key",
        serverUrl: "https://your-domain.com",
        logoUrl: "https://your-domain.com/logo.png",
    };
</script>
<script src="https://your-domain.com/widget/widget-loader.js"></script>
```

### Create New Website

```bash
php artisan tinker
>>> App\Models\Website::create(['name' => 'My Site', 'api_key' => 'unique-key-here'])
```

## 📁 Key Files

| File                                                    | Purpose            |
| ------------------------------------------------------- | ------------------ |
| `public/widget/chat-widget.js`                          | Embeddable widget  |
| `public/widget/widget-loader.js`                        | Widget initializer |
| `app/Http/Controllers/ChatController.php`               | API endpoints      |
| `app/Http/Controllers/Admin/ConversationController.php` | Admin panel        |
| `app/Events/MessageSent.php`                            | Broadcast event    |
| `routes/api.php`                                        | API routes         |

## 🔌 API Endpoints

### Send Message

```
POST /api/chat/send

{
  "api_key": "123456",
  "visitor_token": "visitor_xxx",
  "message": "Hello!"
}

Response:
{
  "conversation_id": 1,
  "messages": [...]
}
```

### Poll Messages

```
POST /api/chat/messages

{
  "api_key": "123456",
  "visitor_token": "visitor_xxx",
  "conversation_id": 1,
  "after_id": 0
}

Response:
{
  "messages": [...],
  "status": "bot"
}
```

## 🐛 Troubleshooting

### Widget not loading on my website

1. Check browser console (F12)
2. Verify CORS is enabled
3. Check API key is correct

### Messages not sending

1. Verify Website exists: `SELECT * FROM websites;`
2. Check API key matches config: `123456`
3. Look at Laravel logs: `tail storage/logs/laravel.log`

### Admin replies not appearing

1. Check polling is active (network tab shows POST requests)
2. Verify admin sent message correctly
3. Check database: `SELECT * FROM messages;`

### Slow updates

- Currently polling every 2 seconds
- Can increase to instant with Reverb (see IMPLEMENTATION_GUIDE.md)

## 📚 Database Schema

### websites

- `id` - Primary key
- `name` - Website name
- `api_key` - Unique API key
- `created_at`

### visitors

- `id` - Primary key
- `website_id` - Foreign key
- `visitor_token` - Unique token
- `created_at`

### conversations

- `id` - Primary key
- `website_id` - Foreign key
- `visitor_id` - Foreign key
- `status` - 'bot' or 'human'
- `last_message_at` - Timestamp
- `created_at`

### messages

- `id` - Primary key
- `conversation_id` - Foreign key
- `sender_type` - 'visitor', 'bot', or 'admin'
- `message` - Text content
- `created_at`

## 🚀 Deployment

### For Production

1. **Update .env:**

```env
APP_DEBUG=false
APP_ENV=production
APP_URL=https://your-domain.com
```

2. **Update widget config on external sites:**

```html
<script>
    window.ChatbotWidgetConfig = {
        apiUrl: "https://your-domain.com/api/chat/send",
        apiKey: "your-production-key",
        serverUrl: "https://your-domain.com",
        logoUrl: "https://your-domain.com/images/chatbot-logo.png",
    };
</script>
<script src="https://your-domain.com/widget/widget-loader.js"></script>
```

3. **Enable HTTPS:** Use wss:// for WebSocket (when upgraded to Reverb)

4. **Scale to real-time (optional):**
    - Set `BROADCAST_CONNECTION=reverb`
    - Start Reverb service
    - Update widget to use Echo

## 💡 Tips

### Customize Bot Responses

Edit `app/Http/Controllers/ChatController.php` → `generateBotReply()` method

### Add Custom Branding

- Logo: Replace `public/images/chatbot-logo.png`
- Colors: Edit CSS in `public/widget/chat-widget.js` (lines 40-50)

### Monitor System

```bash
# View live logs
tail -f storage/logs/laravel.log

# Check database
php artisan tinker
>>> App\Models\Conversation::with('messages')->latest()->first()

# Queue status
php artisan queue:failed
```

## ✨ Next Steps

1. ✅ Test locally (you are here!)
2. Deploy to staging server
3. Update DNS and SSL certificates
4. Go live!
5. (Optional) Upgrade to Reverb for instant messaging

## 📞 Support

- **Laravel Docs:** https://laravel.com/docs/12
- **API Details:** See `IMPLEMENTATION_GUIDE.md`
- **Broadcasting:** See `REVERB_SETUP.md`

---

**Ready to go live!** 🎉

For production domain: Replace `http://127.0.0.1:8000` with your domain everywhere.
