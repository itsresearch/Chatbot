(function() {
    function initChatWidget() {
        // Prevent double initialization
        if (window.ChatbotWidgetInitialized) return;
        window.ChatbotWidgetInitialized = true;

        const API_URL = (window.ChatbotWidgetConfig && window.ChatbotWidgetConfig.apiUrl) || "http://127.0.0.1:8000/api/chat/send";
        const API_KEY = (window.ChatbotWidgetConfig && window.ChatbotWidgetConfig.apiKey) || "123456"; // Your website's API key
        const SERVER_URL = (window.ChatbotWidgetConfig && window.ChatbotWidgetConfig.serverUrl) || "http://127.0.0.1:8000";
        const LOGO_URL = (window.ChatbotWidgetConfig && window.ChatbotWidgetConfig.logoUrl) || SERVER_URL + "/images/chatbot-logo.png";

    const BRAND_PRIMARY = "#ff7a18";
    const BRAND_PRIMARY_DARK = "#e8620b";
    const BRAND_BACKGROUND = "#fff3e0";

    // Ensure we keep a stable visitor token
    let visitorToken = localStorage.getItem("visitor_token");
    if (!visitorToken) {
        visitorToken = "visitor_" + Date.now();
        localStorage.setItem("visitor_token", visitorToken);
    }

    // Inject scoped styles for the widget
    const style = document.createElement("style");
    style.textContent = `
      .cb-widget-root {
        position: fixed;
        inset: auto 20px 20px auto;
        z-index: 999999;
        font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
      }

      .cb-launcher {
        background: transparent;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        border: none;
        outline: none;
        transition: transform 0.18s ease;
        padding: 0;
        width: auto;
        height: auto;
      }

      .cb-launcher:hover {
        transform: scale(1.05);
      }

      .cb-launcher img {
        display: block;
        object-fit: contain;
        max-width: 80px;
        max-height: 80px;
      }

      .cb-launcher-img {
        display: block;
        object-fit: contain;
        max-width: 80px;
        max-height: 80px;
        filter: drop-shadow(0 4px 8px rgba(17, 15, 15, 0.5));
      }

      .cb-launcher-img.error-img {
        display: none;
      }

      .cb-header-img {
        width: 36px;
        height: 36px;
        object-fit: contain;
        display: block;
      }

      .cb-header-img.error-img {
        display: none;
      }

      .cb-panel {
        position: absolute;
        right: 0;
        bottom: 70px;
        width: 360px;
        max-height: 520px;
        background: #ffffff;
        border-radius: 20px;
        box-shadow: 0 18px 45px rgba(15,23,42,0.35);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        transform-origin: bottom right;
        transform: scale(0.95);
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.18s ease, transform 0.18s ease;
      }

      .cb-panel.cb-open {
        opacity: 1;
        transform: scale(1);
        pointer-events: auto;
      }

      .cb-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 16px;
        background: radial-gradient(circle at top left, ${BRAND_PRIMARY} 0%, ${BRAND_PRIMARY_DARK} 50%, #ff512f 100%);
        color: #ffffff;
      }

      .cb-header-main {
        display: flex;
        align-items: center;
        gap: 10px;
      }

      .cb-header-logo {
        width: 40px;
        height: 40px;
        border-radius: 0;
        background: transparent;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: visible;
      }

      .cb-header-logo img {
        width: 36px;
        height: 36px;
        object-fit: contain;
      }

      .cb-header-title {
        font-size: 15px;
        font-weight: 600;
      }

      .cb-header-subtitle {
        font-size: 12px;
        opacity: 0.85;
      }

      .cb-header-close {
        background: transparent;
        border: none;
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        cursor: pointer;
        font-size: 20px;
        font-weight: bold;
        line-height: 1;
        padding: 0;
      }

      .cb-messages {
        background: radial-gradient(circle at top, ${BRAND_BACKGROUND}, #ffffff 40%);
        padding: 16px 14px 12px 14px;
        flex: 1;
        overflow-y: auto;
      }

      .cb-message-row {
        display: flex;
        flex-direction: column;
        margin-bottom: 10px;
      }

      .cb-message-row.cb-message-user {
        align-items: flex-end;
      }

      .cb-message-row.cb-message-bot {
        align-items: flex-start;
      }

      .cb-bubble {
        max-width: 78%;
        border-radius: 18px;
        padding: 10px 12px;
        font-size: 14px;
        line-height: 1.4;
        position: relative;
        box-shadow: 0 4px 12px rgba(15,23,42,0.12);
      }

      .cb-bubble-user {
        background: ${BRAND_PRIMARY};
        color: #ffffff;
        border-bottom-right-radius: 4px;
      }

      .cb-bubble-bot {
        background: #ffffff;
        color: #111827;
        border-bottom-left-radius: 4px;
      }

      .cb-meta {
        display: flex;
        justify-content: flex-end;
        margin-top: 4px;
        font-size: 11px;
        color: #9ca3af;
      }

      .cb-message-row.cb-message-bot .cb-meta {
        justify-content: flex-start;
      }

      .cb-input-area {
        border-top: 1px solid #e5e7eb;
        padding: 10px 10px 12px 10px;
        display: flex;
        gap: 8px;
        background: #ffffff;
      }

      .cb-input {
        flex: 1;
        border-radius: 999px;
        border: 1px solid #e5e7eb;
        padding: 9px 12px;
        font-size: 14px;
        outline: none;
        transition: border-color 0.14s ease, box-shadow 0.14s ease, background-color 0.14s ease;
      }

      .cb-input:focus {
        border-color: ${BRAND_PRIMARY};
        box-shadow: 0 0 0 2px rgba(255, 122, 24, 0.25);
        background-color: #ffffff;
      }

      .cb-send {
        border-radius: 999px;
        border: none;
        background: linear-gradient(135deg, ${BRAND_PRIMARY} 0%, ${BRAND_PRIMARY_DARK} 100%);
        color: #ffffff;
        padding: 0 16px;
        font-size: 13px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        transition: transform 0.12s ease, box-shadow 0.12s ease, opacity 0.12s ease;
      }

      .cb-send:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 18px rgba(15,23,42,0.18);
      }

      .cb-send:disabled {
        opacity: 0.6;
        box-shadow: none;
        cursor: not-allowed;
      }

      .cb-send-icon {
        font-size: 14px;
      }

      @media (max-width: 480px) {
        .cb-panel {
          width: 100vw;
          right: 0;
          bottom: 0;
          max-height: 100vh;
          border-radius: 0;
        }

        .cb-widget-root {
          inset: auto 12px 12px auto;
        }
      }
    `;
    document.head.appendChild(style);

    // Build widget DOM
    const root = document.createElement("div");
    root.className = "cb-widget-root";
    root.innerHTML = `
      <button class="cb-launcher" type="button" aria-label="Open chat" id="cb-launcher">
        ${LOGO_URL ? `<img src="${LOGO_URL}" alt="Chatbot" class="cb-launcher-img" />` : "💬"}
      </button>
      <section class="cb-panel" id="cb-panel" aria-label="Chatbot widget" aria-hidden="true">
        <header class="cb-header">
          <div class="cb-header-main">
            <div class="cb-header-logo">
              ${LOGO_URL ? `<img src="${LOGO_URL}" alt="Chatbot logo" class="cb-header-img" />` : "💬"}
            </div>
            <div>
              <div class="cb-header-title">Chat with us</div>
              <div class="cb-header-subtitle">Typically replies in a few minutes</div>
            </div>
          </div>
          <button class="cb-header-close" type="button" id="cb-close" aria-label="Close chat">X</button>
        </header>
        <div class="cb-messages" id="cb-messages" role="log" aria-live="polite"></div>
        <form class="cb-input-area" id="cb-form">
          <input
            class="cb-input"
            id="cb-input"
            type="text"
            autocomplete="off"
            placeholder="Type a message..."
          />
          <button class="cb-send" type="submit" id="cb-send">
            <span>Send</span>
            <span class="cb-send-icon">></span>
          </button>
        </form>
      </section>
    `;

    document.body.appendChild(root);

    const panel = document.getElementById("cb-panel");
    const launcher = document.getElementById("cb-launcher");
    const closeBtn = document.getElementById("cb-close");
    const messagesDiv = document.getElementById("cb-messages");
    const form = document.getElementById("cb-form");
    const input = document.getElementById("cb-input");
    const sendButton = document.getElementById("cb-send");

    // Handle image loading errors
    const launcherImg = launcher.querySelector("img");
    if (launcherImg) {
        launcherImg.addEventListener("error", function () {
            this.classList.add("error-img");
            launcher.textContent = "💬";
        });
    }

    const headerImg = document.querySelector(".cb-header-logo img");
    if (headerImg) {
        headerImg.addEventListener("error", function () {
            this.classList.add("error-img");
            this.parentNode.textContent = "💬";
        });
    }

    let hasInitialGreeting = false;
    let conversationId = null;
    let lastMessageId = 0;
    let pollInterval = null;
    
    // Message persistence with localStorage
    const STORAGE_KEY = `chatbot_messages_${visitorToken}`;
    const CONVERSATION_KEY = `chatbot_conversation_${visitorToken}`;
    
    function loadConversationId() {
        const stored = localStorage.getItem(CONVERSATION_KEY);
        return stored ? parseInt(stored) : null;
    }
    
    function saveConversationId(id) {
        localStorage.setItem(CONVERSATION_KEY, id.toString());
        conversationId = id;
    }
    
    function loadStoredMessages() {
        const stored = localStorage.getItem(STORAGE_KEY);
        return stored ? JSON.parse(stored) : [];
    }
    
    function saveMessages(messages) {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(messages));
    }
    
    function addMessageToStorage(id, senderType, message, timestamp) {
        const msgs = loadStoredMessages();
        // Avoid duplicates
        if (!msgs.find(m => m.id === id)) {
            msgs.push({
                id: id,
                sender_type: senderType,
                message: message,
                timestamp: timestamp,
                created_at: timestamp
            });
            lastMessageId = Math.max(lastMessageId, id);
            saveMessages(msgs);
        }
    }
    
    function restoreMessageHistory() {
      const messages = loadStoredMessages();
      messages.forEach(msg => {
        // visitor → "user" (right side), admin/bot → "bot" (left side)
        const type = msg.sender_type === "visitor" ? "user" : "bot";
        displayMessage(type, msg.message, msg.created_at || msg.timestamp, msg.id || null);
        if (msg.id) {
          lastMessageId = Math.max(lastMessageId, msg.id);
        }
      });
    }

    // Adaptive polling: fast when active, slows when idle to reduce server load
    let pollDelay = 3000;
    let pollIdleSince = Date.now();
    const POLL_FAST = 3000;
    const POLL_SLOW = 8000;
    const POLL_IDLE_AFTER = 30000;
    const MESSAGES_API_URL = SERVER_URL + "/api/chat/messages";

    function pollForMessages() {
        if (!conversationId) return;
        stopPolling();
        pollDelay = POLL_FAST;
        pollIdleSince = Date.now();

        async function doPoll() {
            try {
                const response = await fetch(MESSAGES_API_URL, {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({
                        api_key: API_KEY,
                        visitor_token: visitorToken,
                        conversation_id: conversationId,
                        after_id: lastMessageId,
                    }),
                });
                const data = await response.json();
                let hasNew = false;

                if (data && Array.isArray(data.messages) && data.messages.length > 0) {
                    data.messages.forEach(msg => {
                        if (msg.sender_type === "admin" || msg.sender_type === "bot") {
                            if (!messagesDiv.querySelector('[data-message-id="' + msg.id + '"]')) {
                                displayMessage("bot", msg.message, msg.created_at, msg.id);
                                addMessageToStorage(msg.id, msg.sender_type, msg.message, msg.created_at);
                                hasNew = true;
                            }
                        }
                        lastMessageId = Math.max(lastMessageId, msg.id);
                    });
                }

                if (hasNew) {
                    pollDelay = POLL_FAST;
                    pollIdleSince = Date.now();
                } else if (Date.now() - pollIdleSince > POLL_IDLE_AFTER) {
                    pollDelay = Math.min(pollDelay + 1000, POLL_SLOW);
                }
            } catch (error) {
                console.error("Polling error:", error);
                pollDelay = Math.min(pollDelay + 2000, POLL_SLOW);
            }

            // Only schedule next if polling hasn't been stopped
            if (pollInterval !== null) {
                pollInterval = setTimeout(doPoll, pollDelay);
            }
        }

        pollInterval = setTimeout(doPoll, pollDelay);
    }

    function stopPolling() {
        if (pollInterval) {
            clearTimeout(pollInterval);
            pollInterval = null;
        }
    }

    function resetPollSpeed() {
        pollDelay = POLL_FAST;
        pollIdleSince = Date.now();
    }

    function togglePanel(open) {
        const shouldOpen = typeof open === "boolean" ? open : !panel.classList.contains("cb-open");
        if (shouldOpen) {
            panel.classList.add("cb-open");
            panel.setAttribute("aria-hidden", "false");
            setTimeout(() => input.focus(), 160);
            
            // Restore conversation and message history if exists
            if (!conversationId) {
                conversationId = loadConversationId();
            }
            
            if (conversationId && messagesDiv.children.length === 0) {
                // Restore message history from localStorage
                restoreMessageHistory();
                // Start polling for new admin messages
                pollForMessages();
            } else if (!conversationId && !hasInitialGreeting) {
                // First time - show greeting
                displayMessage("bot", "Hi there! How can I help you today?", new Date().toISOString());
                hasInitialGreeting = true;
            } else if (conversationId && !pollInterval) {
                // Resume polling if it was paused
                pollForMessages();
            }
        } else {
            panel.classList.remove("cb-open");
            panel.setAttribute("aria-hidden", "true");
            stopPolling();
        }
    }

    launcher.addEventListener("click", () => togglePanel(true));
    closeBtn.addEventListener("click", () => togglePanel(false));

    form.addEventListener("submit", function (event) {
        event.preventDefault();
        sendMessage();
    });

    input.addEventListener("keypress", function (e) {
        if (e.key === "Enter" && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    function formatTime(date) {
        return date.toLocaleTimeString(undefined, {
            hour: "2-digit",
            minute: "2-digit",
        });
    }
    
    function displayMessage(type, text, timestamp = null, messageId = null) {
        const row = document.createElement("div");
        row.className = "cb-message-row " + (type === "user" ? "cb-message-user" : "cb-message-bot");
      if (messageId) {
        row.dataset.messageId = messageId;
      }

        const bubble = document.createElement("div");
        bubble.className = "cb-bubble " + (type === "user" ? "cb-bubble-user" : "cb-bubble-bot");
        bubble.textContent = text;

        const meta = document.createElement("div");
        meta.className = "cb-meta";
        const displayDate = timestamp ? new Date(timestamp) : new Date();
        meta.textContent = formatTime(displayDate);

        const wrapper = document.createElement("div");
        wrapper.appendChild(bubble);
        wrapper.appendChild(meta);

        row.appendChild(wrapper);
        messagesDiv.appendChild(row);

        // Auto scroll to latest message
        setTimeout(() => {
            messagesDiv.scrollTop = messagesDiv.scrollHeight;
        }, 0);
    }
    
    function addMessage(type, text) {
        displayMessage(type, text, new Date().toISOString());
    }

    async function sendMessage() {
        const message = input.value.trim();
        if (!message) return;

        // Display user message immediately
        addMessage("user", message);
        input.value = "";
        input.focus();

        sendButton.disabled = true;

        try {
            const response = await fetch(API_URL, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({
                    api_key: API_KEY,
                    visitor_token: visitorToken,
                    message: message,
                }),
            });

            const data = await response.json();

            // Handle conversation creation and ID tracking
            if (data && data.conversation_id) {
                if (!conversationId) {
                    // First message - save conversation ID and start polling
                    saveConversationId(data.conversation_id);
                    pollForMessages();
                }
            }

            // Process bot response messages
            if (data && Array.isArray(data.messages) && data.messages.length > 0) {
              // Save all messages to localStorage with their database IDs
              data.messages.forEach(msg => {
                addMessageToStorage(msg.id, msg.sender_type, msg.message, msg.created_at);
              });

              // Display any admin or bot messages not yet shown
              data.messages.forEach(msg => {
                if (msg.sender_type === "admin" || msg.sender_type === "bot") {
                  const existing = messagesDiv.querySelector('[data-message-id="' + msg.id + '"]');
                  if (!existing) {
                    displayMessage("bot", msg.message, msg.created_at, msg.id);
                  }
                }
              });

              // Update lastMessageId to highest in conversation
              const maxId = Math.max(
                lastMessageId,
                ...data.messages.map(msg => msg.id || 0)
              );
              lastMessageId = maxId;
            } else if (!data || !data.status || data.status !== "human") {
                // Only show fallback if NOT in human mode and no messages returned
                displayMessage("bot", "Thanks! I've received your message.", new Date().toISOString());
            }

            // Speed up polling after sending
            resetPollSpeed();
        } catch (error) {
            console.error("Error:", error);
            displayMessage("bot", "Sorry, something went wrong. Please try again.", new Date().toISOString());
        } finally {
            sendButton.disabled = false;
        }
    }
    }

    // Initialize immediately if DOM already ready, otherwise wait
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initChatWidget);
    } else {
        initChatWidget();
    }
})();

