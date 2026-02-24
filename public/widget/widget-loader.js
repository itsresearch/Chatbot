/**
 * Chatbot Widget Loader
 * This script initializes Laravel Echo and loads the chat widget
 * 
 * Usage:
 * <script>
 *   window.ChatbotWidgetConfig = {
 *     apiUrl: 'https://your-domain.com/api/chat/send',
 *     apiKey: 'your-api-key',
 *     serverUrl: 'https://your-domain.com',
 *     logoUrl: 'https://your-domain.com/images/chatbot-logo.png'
 *   };
 * </script>
 * <script src="https://your-domain.com/widget/widget-loader.js"></script>
 */

(function() {
    // Configuration
    const SERVER_URL = (window.ChatbotWidgetConfig && window.ChatbotWidgetConfig.serverUrl) || 'http://127.0.0.1:8000';
    
    // Load Laravel Echo and Pusher
    function loadEcho() {
        // Check if Echo is already loaded
        if (window.Echo) {
            console.log('Laravel Echo already loaded');
            loadChatWidget();
            return;
        }

        // Create a promise-based loading system
        Promise.all([
            loadScript(SERVER_URL + '/js/app.js', 'module'),
        ]).then(() => {
            // Wait for Echo to be initialized
            const checkEcho = setInterval(() => {
                if (window.Echo) {
                    clearInterval(checkEcho);
                    console.log('Laravel Echo initialized successfully');
                    loadChatWidget();
                }
            }, 100);
            
            // Timeout after 5 seconds
            setTimeout(() => {
                clearInterval(checkEcho);
                if (!window.Echo) {
                    console.warn('Laravel Echo failed to initialize, loading chat widget anyway');
                    loadChatWidget();
                }
            }, 5000);
        }).catch((error) => {
            console.error('Failed to load Laravel Echo:', error);
            console.log('Loading chat widget without real-time features');
            loadChatWidget();
        });
    }

    // Load a script dynamically
    function loadScript(src, type = 'text/javascript') {
        return new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = src;
            script.type = type;
            script.async = true;
            
            script.onload = () => resolve(script);
            script.onerror = () => reject(new Error(`Failed to load script: ${src}`));
            
            document.head.appendChild(script);
        });
    }

    // Load the chat widget
    function loadChatWidget() {
        loadScript(SERVER_URL + '/widget/chat-widget.js').then(() => {
            console.log('Chat widget loaded successfully');
        }).catch((error) => {
            console.error('Failed to load chat widget:', error);
        });
    }

    // Start loading when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', loadEcho);
    } else {
        loadEcho();
    }
})();
