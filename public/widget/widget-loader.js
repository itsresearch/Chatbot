/**
 * Chatbot Widget Loader
 * This script loads the chat widget
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
    
    // Load a script dynamically
    function loadScript(src) {
        return new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = src;
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
            // If DOMContentLoaded already fired, manually trigger widget init
            if (document.readyState !== 'loading') {
                // The widget listens for DOMContentLoaded, but it already fired
                // Dispatch a custom event or directly call init if needed
                if (window.ChatbotWidget && typeof window.ChatbotWidget.init === 'function') {
                    window.ChatbotWidget.init();
                }
            }
        }).catch((error) => {
            console.error('Failed to load chat widget:', error);
        });
    }

    // Start loading when DOM is ready or immediately if already ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', loadChatWidget);
    } else {
        loadChatWidget();
    }
})();
