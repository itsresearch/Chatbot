(function () {
    function initChatWidget() {
        if (window.ChatbotWidgetInitialized) return;
        window.ChatbotWidgetInitialized = true;

        const API_URL = (window.ChatbotWidgetConfig && window.ChatbotWidgetConfig.apiUrl) || ((window.ChatbotWidgetConfig && window.ChatbotWidgetConfig.serverUrl) || "http://127.0.0.1:8000") + "/api/chat/send";
        const API_KEY = (window.ChatbotWidgetConfig && window.ChatbotWidgetConfig.apiKey) || "123456";
        const SERVER_URL = (window.ChatbotWidgetConfig && window.ChatbotWidgetConfig.serverUrl) || "http://127.0.0.1:8000";
        const LOGO_URL = (window.ChatbotWidgetConfig && window.ChatbotWidgetConfig.logoUrl) || SERVER_URL + "/images/chatbot-logo.png";

        let BRAND_PRIMARY = "#ff7a18";
        let BRAND_PRIMARY_DARK = "#e8620b"; 
        let BRAND_BACKGROUND = "#fff3e0";
        let WELCOME_MESSAGE = "Hi there! How can I help you today?";
        let HAS_KNOWLEDGE_BASE = false;
        let WIDGET_COLOR_TYPE = "gradient"; // "gradient" or "plain"
        let WIDGET_POSITION = "bottom-right"; // "bottom-right" or "bottom-left"

        let visitorToken = localStorage.getItem("visitor_token");
        if (!visitorToken) {
            visitorToken = "visitor_" + Date.now();
            localStorage.setItem("visitor_token", visitorToken);
        }

        const WIDGET_SCOPE = API_KEY.substring(0, 16);
        const STORAGE_KEY = "chatbot_messages_" + visitorToken + "_" + WIDGET_SCOPE;
        const CONVERSATION_KEY = "chatbot_conversation_" + visitorToken + "_" + WIDGET_SCOPE;

        /* ================================================================
         *  Color helpers
         * ================================================================ */
        function darkenColor(hex, pct) {
            var num = parseInt(hex.replace("#", ""), 16);
            var r = Math.max(0, (num >> 16) - Math.round(2.55 * pct));
            var g = Math.max(0, ((num >> 8) & 0x00ff) - Math.round(2.55 * pct));
            var b = Math.max(0, (num & 0x0000ff) - Math.round(2.55 * pct));
            return "#" + ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1);
        }
        function lightenColor(hex, pct) {
            var num = parseInt(hex.replace("#", ""), 16);
            var r = Math.min(255, (num >> 16) + Math.round(2.55 * pct));
            var g = Math.min(255, ((num >> 8) & 0x00ff) + Math.round(2.55 * pct));
            var b = Math.min(255, (num & 0x0000ff) + Math.round(2.55 * pct));
            return "#" + ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1);
        }

        /* ================================================================
         *  Config fetch
         * ================================================================ */
        async function loadWidgetConfig() {
            try {
                var res = await fetch(SERVER_URL + "/api/chat/widget-config", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ api_key: API_KEY }),
                });
                if (res.ok) {
                    var cfg = await res.json();
                    if (cfg.widget_color) {
                        BRAND_PRIMARY = cfg.widget_color;
                        BRAND_PRIMARY_DARK = darkenColor(BRAND_PRIMARY, 20);
                        BRAND_BACKGROUND = lightenColor(BRAND_PRIMARY, 90);
                    }
                    if (cfg.welcome_message) WELCOME_MESSAGE = cfg.welcome_message;
                    if (cfg.has_knowledge_base) HAS_KNOWLEDGE_BASE = true;
                    if (cfg.widget_color_type) WIDGET_COLOR_TYPE = cfg.widget_color_type;
                    if (cfg.widget_position) WIDGET_POSITION = cfg.widget_position;
                }
            } catch (e) {
                console.warn("Widget config fetch failed, using defaults.", e);
            }
        }

        /* ================================================================
         *  API helpers
         * ================================================================ */
        async function apiPost(endpoint, body) {
            var res = await fetch(SERVER_URL + "/api/chat/" + endpoint, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(Object.assign({ api_key: API_KEY }, body)),
            });
            return res.json();
        }

        /* ================================================================
         *  Styles injection
         * ================================================================ */
        function injectStyles() {
            // Resolve position insets
            var posInset = WIDGET_POSITION === "bottom-left" ? "auto auto 20px 20px" : "auto 20px 20px auto";
            var panelAlign = WIDGET_POSITION === "bottom-left" ? "left:0" : "right:0";
            var panelOrigin = WIDGET_POSITION === "bottom-left" ? "bottom left" : "bottom right";
            var mobileInset = WIDGET_POSITION === "bottom-left" ? "auto auto 12px 12px" : "auto 12px 12px auto";

            // Resolve color backgrounds
            var headerBg = WIDGET_COLOR_TYPE === "plain"
                ? BRAND_PRIMARY
                : "radial-gradient(circle at top left," + BRAND_PRIMARY + " 0%," + BRAND_PRIMARY_DARK + " 50%,#ff512f 100%)";
            var sendBg = WIDGET_COLOR_TYPE === "plain"
                ? BRAND_PRIMARY
                : "linear-gradient(135deg," + BRAND_PRIMARY + "," + BRAND_PRIMARY_DARK + ")";
            var userBubbleBg = WIDGET_COLOR_TYPE === "plain"
                ? BRAND_PRIMARY
                : BRAND_PRIMARY;

            var style = document.createElement("style");
            style.textContent =
                ".cb-widget-root{position:fixed;inset:" + posInset + ";z-index:999999;font-family:system-ui,-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif}" +
                ".cb-launcher{background:transparent;display:flex;align-items:center;justify-content:center;cursor:pointer;border:none;outline:none;transition:transform .18s;padding:0}" +
                ".cb-launcher:hover{transform:scale(1.05)}" +
                ".cb-launcher-img{display:block;object-fit:contain;max-width:80px;max-height:80px;filter:drop-shadow(0 4px 8px rgba(17,15,15,.5))}" +
                ".cb-launcher-img.error-img{display:none}" +
                ".cb-header-img{width:36px;height:36px;object-fit:contain;display:block}" +
                ".cb-header-img.error-img{display:none}" +

                ".cb-panel{position:absolute;" + panelAlign + ";bottom:70px;width:380px;height:560px;background:#fff;border-radius:20px;box-shadow:0 18px 45px rgba(15,23,42,.35);display:flex;flex-direction:column;overflow:hidden;transform-origin:" + panelOrigin + ";transform:scale(.95);opacity:0;pointer-events:none;transition:opacity .18s,transform .18s}" +
                ".cb-panel.cb-open{opacity:1;transform:scale(1);pointer-events:auto}" +

                ".cb-header{display:flex;align-items:center;justify-content:space-between;padding:14px 16px;background:" + headerBg + ";color:#fff;flex-shrink:0}" +
                ".cb-header-main{display:flex;align-items:center;gap:10px}" +
                ".cb-header-logo{width:40px;height:40px;display:flex;align-items:center;justify-content:center}" +
                ".cb-header-title{font-size:15px;font-weight:600}" +
                ".cb-header-subtitle{font-size:12px;opacity:.85}" +
                ".cb-header-back{background:transparent;border:none;width:28px;height:28px;display:flex;align-items:center;justify-content:center;color:#fff;cursor:pointer;font-size:18px;padding:0;margin-right:4px;opacity:.9;transition:opacity .15s}" +
                ".cb-header-back:hover{opacity:1}" +
                ".cb-header-close{background:transparent;border:none;width:28px;height:28px;display:flex;align-items:center;justify-content:center;color:#fff;cursor:pointer;font-size:20px;font-weight:bold;padding:0}" +

                ".cb-breadcrumb{display:flex;align-items:center;gap:4px;padding:8px 14px;background:" + BRAND_BACKGROUND + ";border-bottom:1px solid #f3e8de;font-size:12px;color:#6b7280;flex-shrink:0;flex-wrap:wrap}" +
                ".cb-breadcrumb-item{cursor:pointer;color:" + BRAND_PRIMARY + ";font-weight:500}" +
                ".cb-breadcrumb-item:hover{text-decoration:underline}" +
                ".cb-breadcrumb-sep{color:#9ca3af;margin:0 2px}" +
                ".cb-breadcrumb-current{color:#374151;font-weight:600}" +

                ".cb-body{flex:1;overflow-y:auto;padding:14px;background:radial-gradient(circle at top," + BRAND_BACKGROUND + ",#fff 40%)}" +

                ".cb-list{list-style:none;padding:0;margin:0}" +
                ".cb-list-item{display:flex;align-items:center;gap:12px;padding:12px 14px;border-radius:12px;cursor:pointer;transition:all .15s;border:1px solid #f3e8de;margin-bottom:8px;background:#fff}" +
                ".cb-list-item:hover{background:" + BRAND_BACKGROUND + ";border-color:" + BRAND_PRIMARY + ";transform:translateX(2px);box-shadow:0 2px 8px rgba(249,115,22,.10)}" +
                ".cb-list-icon{width:40px;height:40px;border-radius:10px;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:18px;background:linear-gradient(135deg,rgba(249,115,22,.12),rgba(249,115,22,.04));color:" + BRAND_PRIMARY + "}" +
                ".cb-list-text{flex:1;min-width:0}" +
                ".cb-list-name{font-weight:600;font-size:14px;color:#1e293b}" +
                ".cb-list-desc{font-size:12px;color:#6b7280;margin-top:2px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}" +
                ".cb-list-arrow{color:#9ca3af;font-size:16px;flex-shrink:0}" +

                ".cb-detail-title{font-size:16px;font-weight:700;color:#1e293b;margin-bottom:4px}" +
                ".cb-detail-short{font-size:13px;color:#6b7280;margin-bottom:12px}" +
                ".cb-detail-content{font-size:13.5px;line-height:1.65;color:#374151}" +
                ".cb-detail-content h2{font-size:15px;font-weight:700;margin:14px 0 6px}" +
                ".cb-detail-content h3{font-size:14px;font-weight:600;margin:10px 0 4px}" +
                ".cb-detail-content p{margin-bottom:8px}" +
                ".cb-detail-content ul,.cb-detail-content ol{padding-left:18px;margin-bottom:8px}" +
                ".cb-detail-content table{width:100%;border-collapse:collapse;margin-bottom:10px;font-size:12.5px}" +
                ".cb-detail-content table th,.cb-detail-content table td{border:1px solid #e5e7eb;padding:6px 8px}" +
                ".cb-detail-content table th{background:#f9fafb;font-weight:600}" +
                ".cb-detail-content blockquote{border-left:3px solid " + BRAND_PRIMARY + ";padding-left:10px;color:#6b7280;margin:8px 0}" +
                ".cb-detail-content a{color:" + BRAND_PRIMARY + "}" +

                ".cb-messages{padding:0}" +
                ".cb-message-row{display:flex;flex-direction:column;margin-bottom:10px}" +
                ".cb-message-row.cb-message-user{align-items:flex-end}" +
                ".cb-message-row.cb-message-bot{align-items:flex-start}" +
                ".cb-bubble{max-width:78%;border-radius:18px;padding:10px 12px;font-size:14px;line-height:1.4;box-shadow:0 4px 12px rgba(15,23,42,.12)}" +
                ".cb-bubble-user{background:" + BRAND_PRIMARY + ";color:#fff;border-bottom-right-radius:4px}" +
                ".cb-bubble-bot{background:#fff;color:#111827;border-bottom-left-radius:4px}" +
                ".cb-meta{display:flex;justify-content:flex-end;margin-top:4px;font-size:11px;color:#9ca3af}" +
                ".cb-message-row.cb-message-bot .cb-meta{justify-content:flex-start}" +

                ".cb-input-area{border-top:1px solid #e5e7eb;padding:10px;display:flex;gap:8px;background:#fff;flex-shrink:0;flex-wrap:wrap}" +
                ".cb-input{flex:1;border-radius:999px;border:1px solid #e5e7eb;padding:9px 12px;font-size:14px;outline:none;transition:border-color .14s,box-shadow .14s;min-width:0}" +
                ".cb-input:focus{border-color:" + BRAND_PRIMARY + ";box-shadow:0 0 0 2px rgba(255,122,24,.25)}" +
                ".cb-send{border-radius:999px;border:none;background:" + sendBg + ";color:#fff;padding:0 16px;font-size:13px;font-weight:600;display:flex;align-items:center;gap:6px;cursor:pointer;transition:transform .12s,box-shadow .12s}" +
                ".cb-send:hover{transform:translateY(-1px);box-shadow:0 8px 18px rgba(15,23,42,.18)}" +
                ".cb-send:disabled{opacity:.6;cursor:not-allowed}" +
                ".cb-attach-btn{border-radius:999px;border:1px solid #e5e7eb;background:#fff;color:#6b7280;width:36px;height:36px;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:16px;transition:all .14s;padding:0;flex-shrink:0}" +
                ".cb-attach-btn:hover{border-color:" + BRAND_PRIMARY + ";color:" + BRAND_PRIMARY + "}" +
                ".cb-attach-btn.active{border-color:" + BRAND_PRIMARY + ";color:" + BRAND_PRIMARY + "}" +
                ".cb-file-preview{display:none;width:100%;padding:6px 10px;background:" + BRAND_BACKGROUND + ";border-radius:8px;font-size:12px;align-items:center;gap:6px;margin-bottom:4px}" +
                ".cb-file-preview.active{display:flex}" +
                ".cb-file-preview-name{flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:#374151}" +
                ".cb-file-preview-remove{background:none;border:none;color:#9ca3af;cursor:pointer;font-size:16px;padding:0 2px}" +
                ".cb-file-attachment{margin-top:6px}" +
                ".cb-file-attachment img{max-width:200px;max-height:160px;border-radius:10px;display:block;cursor:pointer}" +
                ".cb-file-doc{display:inline-flex;align-items:center;gap:6px;padding:6px 10px;border-radius:8px;text-decoration:none;font-size:12px}" +
                ".cb-file-doc-user{background:rgba(255,255,255,.18);color:#fff}" +
                ".cb-file-doc-bot{background:" + BRAND_BACKGROUND + ";color:#374151}" +

                ".cb-bottom-nav{display:flex;border-top:1px solid #e5e7eb;background:#fff;flex-shrink:0}" +
                ".cb-bottom-nav-btn{flex:1;display:flex;flex-direction:column;align-items:center;gap:2px;padding:8px 0;border:none;background:transparent;cursor:pointer;font-size:11px;color:#6b7280;transition:all .15s}" +
                ".cb-bottom-nav-btn:hover{color:" + BRAND_PRIMARY + "}" +
                ".cb-bottom-nav-btn.active{color:" + BRAND_PRIMARY + ";font-weight:600}" +
                ".cb-bottom-nav-btn i{font-size:16px}" +

                ".cb-loader{text-align:center;padding:30px 0;color:#9ca3af;font-size:13px}" +
                ".cb-loader-spinner{width:24px;height:24px;border:3px solid #f3e8de;border-top-color:" + BRAND_PRIMARY + ";border-radius:50%;animation:cb-spin .7s linear infinite;margin:0 auto 8px}" +
                "@keyframes cb-spin{to{transform:rotate(360deg)}}" +

                ".cb-powered{display:flex;align-items:center;justify-content:center;padding:8px 0;border-top:1px solid #e5e7eb;background:#fff;flex-shrink:0;font-size:12px;color:#9ca3af}" +
                ".cb-powered a{color:#1e293b;font-weight:600;text-decoration:none;margin-left:4px}" +
                ".cb-powered a:hover{text-decoration:underline}" +

                ".cb-empty{text-align:center;padding:30px 14px;color:#9ca3af}" +
                ".cb-empty-icon{font-size:36px;margin-bottom:8px}" +
                ".cb-empty-text{font-size:13px}" +

                "@media(max-width:480px){.cb-panel{width:100vw;" + panelAlign + ";bottom:0;height:100vh;border-radius:0}.cb-widget-root{inset:" + mobileInset + "}}";

            document.head.appendChild(style);
        }

        function loadBootstrapIcons() {
            if (!document.querySelector('link[href*="bootstrap-icons"]')) {
                var link = document.createElement("link");
                link.rel = "stylesheet";
                link.href = "https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css";
                document.head.appendChild(link);
            }
        }

        /* ================================================================
         *  Bootstrap
         * ================================================================ */
        async function bootstrap() {
            await loadWidgetConfig();
            loadBootstrapIcons();
            injectStyles();

            // ── State ───────────────────────────────
            var currentView = HAS_KNOWLEDGE_BASE ? "kb" : "chat";
            var kbStack = [];
            var conversationId = null;
            var lastMessageId = 0;
            var pollInterval = null;
            var hasInitialGreeting = false;

            // ── Build DOM ───────────────────────────
            var root = document.createElement("div");
            root.className = "cb-widget-root";
            root.innerHTML =
                '<button class="cb-launcher" type="button" aria-label="Open chat" id="cb-launcher">' +
                    (LOGO_URL ? '<img src="' + LOGO_URL + '" alt="Chatbot" class="cb-launcher-img" />' : "\uD83D\uDCAC") +
                '</button>' +
                '<section class="cb-panel" id="cb-panel" aria-label="Chatbot widget" aria-hidden="true">' +
                    '<header class="cb-header">' +
                        '<button class="cb-header-back" type="button" id="cb-back" aria-label="Go back" style="display:none;">' +
                            '<i class="bi bi-chevron-left"></i>' +
                        '</button>' +
                        '<div class="cb-header-main">' +
                            '<div class="cb-header-logo">' +
                                (LOGO_URL ? '<img src="' + LOGO_URL + '" alt="" class="cb-header-img" />' : "\uD83D\uDCAC") +
                            '</div>' +
                            '<div>' +
                                '<div class="cb-header-title">Chat with us</div>' +
                                '<div class="cb-header-subtitle">We\'re here to help</div>' +
                            '</div>' +
                        '</div>' +
                        '<button class="cb-header-close" type="button" id="cb-close" aria-label="Close">\u2715</button>' +
                    '</header>' +
                    '<div class="cb-breadcrumb" id="cb-breadcrumb" style="display:none;"></div>' +
                    '<div class="cb-body" id="cb-body"></div>' +
                    (HAS_KNOWLEDGE_BASE ?
                        '<div class="cb-bottom-nav" id="cb-bottom-nav">' +
                            '<button class="cb-bottom-nav-btn active" data-view="kb" type="button">' +
                                '<i class="bi bi-grid-fill"></i><span>Services</span>' +
                            '</button>' +
                            '<button class="cb-bottom-nav-btn" data-view="chat" type="button">' +
                                '<i class="bi bi-chat-dots-fill"></i><span>Chat</span>' +
                            '</button>' +
                        '</div>'
                    : "") +
                    '<div class="cb-powered">Powered by <a href="https://www.devkotaresearch.com.np/" target="_blank" rel="noopener noreferrer">Miraai</a></div>' +
                '</section>';

            document.body.appendChild(root);

            var panel = document.getElementById("cb-panel");
            var launcher = document.getElementById("cb-launcher");
            var closeBtn = document.getElementById("cb-close");
            var backBtn = document.getElementById("cb-back");
            var body = document.getElementById("cb-body");
            var breadcrumb = document.getElementById("cb-breadcrumb");
            var bottomNav = document.getElementById("cb-bottom-nav");

            // Handle image load errors
            var launcherImg = launcher.querySelector("img");
            if (launcherImg) {
                launcherImg.addEventListener("error", function () {
                    this.classList.add("error-img");
                    launcher.textContent = "\uD83D\uDCAC";
                });
            }
            var headerImg = root.querySelector(".cb-header-logo img");
            if (headerImg) {
                headerImg.addEventListener("error", function () {
                    this.classList.add("error-img");
                    this.parentNode.textContent = "\uD83D\uDCAC";
                });
            }

            /* ════════════════════════════════════════
             *  Toggle panel
             * ════════════════════════════════════════ */
            function togglePanel(open) {
                var shouldOpen = typeof open === "boolean" ? open : !panel.classList.contains("cb-open");
                if (shouldOpen) {
                    panel.classList.add("cb-open");
                    panel.setAttribute("aria-hidden", "false");
                    if (currentView === "kb") {
                        navigateKB("categories");
                    } else {
                        showChatView();
                    }
                } else {
                    panel.classList.remove("cb-open");
                    panel.setAttribute("aria-hidden", "true");
                    stopPolling();
                }
            }

            launcher.addEventListener("click", function () { togglePanel(true); });
            closeBtn.addEventListener("click", function () { togglePanel(false); });

            /* ════════════════════════════════════════
             *  Back button — go one step back in KB
             * ════════════════════════════════════════ */
            function updateBackButton() {
                if (currentView === "kb" && kbStack.length > 1) {
                    backBtn.style.display = "flex";
                } else {
                    backBtn.style.display = "none";
                }
            }

            backBtn.addEventListener("click", function () {
                if (currentView === "kb" && kbStack.length > 1) {
                    kbStack.pop();
                    var prev = kbStack.pop();
                    navigateKB(prev.type, prev.data);
                }
            });

            /* ════════════════════════════════════════
             *  Bottom nav switching
             * ════════════════════════════════════════ */
            if (bottomNav) {
                bottomNav.addEventListener("click", function (e) {
                    var btn = e.target.closest("[data-view]");
                    if (!btn) return;
                    var view = btn.dataset.view;
                    if (view === currentView) return;
                    currentView = view;
                    bottomNav.querySelectorAll(".cb-bottom-nav-btn").forEach(function (b) { b.classList.remove("active"); });
                    btn.classList.add("active");
                    // Remove chat input area if switching away from chat
                    var oldInput = panel.querySelector("#cb-chat-input-area");
                    if (oldInput) oldInput.remove();
                    if (view === "kb") {
                        navigateKB("categories");
                    } else {
                        showChatView();
                    }
                });
            }

            /* ════════════════════════════════════════
             *  KNOWLEDGE BASE navigation
             * ════════════════════════════════════════ */
            function showLoader() {
                body.innerHTML = '<div class="cb-loader"><div class="cb-loader-spinner"></div>Loading...</div>';
            }

            function showEmpty(msg) {
                body.innerHTML = '<div class="cb-empty"><div class="cb-empty-icon">\uD83D\uDCED</div><div class="cb-empty-text">' + msg + '</div></div>';
            }

            function updateBreadcrumb() {
                if (!HAS_KNOWLEDGE_BASE || currentView !== "kb") {
                    breadcrumb.style.display = "none";
                    updateBackButton();
                    return;
                }
                breadcrumb.style.display = "flex";
                updateBackButton();
                var html = "";
                kbStack.forEach(function (item, idx) {
                    if (idx > 0) html += '<span class="cb-breadcrumb-sep">\u203A</span>';
                    if (idx < kbStack.length - 1) {
                        html += '<span class="cb-breadcrumb-item" data-bc-idx="' + idx + '">' + item.label + '</span>';
                    } else {
                        html += '<span class="cb-breadcrumb-current">' + item.label + '</span>';
                    }
                });
                breadcrumb.innerHTML = html;

                breadcrumb.querySelectorAll(".cb-breadcrumb-item").forEach(function (el) {
                    el.addEventListener("click", function () {
                        var idx = parseInt(el.dataset.bcIdx);
                        var target = kbStack[idx];
                        kbStack = kbStack.slice(0, idx);
                        navigateKB(target.type, target.data);
                    });
                });
            }

            async function navigateKB(type, data) {
                // Remove chat input if exists
                var oldInput = panel.querySelector("#cb-chat-input-area");
                if (oldInput) oldInput.remove();

                showLoader();

                if (type === "categories") {
                    kbStack = [{ type: "categories", label: "Services", data: {} }];
                    updateBreadcrumb();
                    try {
                        var res = await apiPost("categories", {});
                        if (!res.categories || res.categories.length === 0) {
                            showEmpty("No services available at the moment.");
                            return;
                        }
                        renderList(res.categories, "category");
                    } catch (e) {
                        showEmpty("Failed to load categories.");
                    }
                } else if (type === "services") {
                    kbStack.push({ type: "services", label: data.name, data: data });
                    updateBreadcrumb();
                    try {
                        var res = await apiPost("services", { category_id: data.id });
                        if (!res.services || res.services.length === 0) {
                            showEmpty("No services in this category yet.");
                            return;
                        }
                        renderList(res.services, "service");
                    } catch (e) {
                        showEmpty("Failed to load services.");
                    }
                } else if (type === "sub-services") {
                    kbStack.push({ type: "sub-services", label: data.name, data: data });
                    updateBreadcrumb();
                    try {
                        var res = await apiPost("sub-services", { service_id: data.id });
                        if (!res.sub_services || res.sub_services.length === 0) {
                            showEmpty("No details available for this service.");
                            return;
                        }
                        renderList(res.sub_services, "sub-service");
                    } catch (e) {
                        showEmpty("Failed to load sub-services.");
                    }
                } else if (type === "detail") {
                    kbStack.push({ type: "detail", label: data.name, data: data });
                    updateBreadcrumb();
                    try {
                        var res = await apiPost("sub-service-detail", { sub_service_id: data.id });
                        if (!res.sub_service) {
                            showEmpty("Detail not available.");
                            return;
                        }
                        renderDetail(res.sub_service);
                    } catch (e) {
                        showEmpty("Failed to load detail.");
                    }
                }
            }

            function renderList(items, itemType) {
                var html = '<ul class="cb-list">';
                items.forEach(function (item) {
                    var icon = getDefaultIcon(itemType);
                    var desc = item.description || item.short_description || "";
                    html +=
                        '<li class="cb-list-item" data-type="' + itemType + '" data-id="' + item.id + '" data-name="' + escapeAttr(item.name) + '">' +
                            '<div class="cb-list-icon"><i class="' + icon + '"></i></div>' +
                            '<div class="cb-list-text">' +
                                '<div class="cb-list-name">' + escapeHtml(item.name) + '</div>' +
                                (desc ? '<div class="cb-list-desc">' + escapeHtml(desc) + '</div>' : '') +
                            '</div>' +
                            '<div class="cb-list-arrow">\u203A</div>' +
                        '</li>';
                });
                html += '</ul>';

                body.innerHTML = html;

                body.querySelectorAll(".cb-list-item").forEach(function (el) {
                    el.addEventListener("click", function () {
                        var t = el.dataset.type;
                        var id = parseInt(el.dataset.id);
                        var name = el.dataset.name;
                        if (t === "category") navigateKB("services", { id: id, name: name });
                        else if (t === "service") navigateKB("sub-services", { id: id, name: name });
                        else if (t === "sub-service") navigateKB("detail", { id: id, name: name });
                    });
                });
            }

            function renderDetail(sub) {
                var html =
                    '<div class="cb-detail-title">' + escapeHtml(sub.name) + '</div>';
                if (sub.short_description) {
                    html += '<div class="cb-detail-short">' + escapeHtml(sub.short_description) + '</div>';
                }
                if (sub.detail_content) {
                    html += '<div class="cb-detail-content">' + sub.detail_content + '</div>';
                } else {
                    html += '<div class="cb-detail-short" style="margin-top:12px;font-style:italic;">No additional details available.</div>';
                }

                body.innerHTML = html;
            }

            function getDefaultIcon(type) {
                if (type === "category") return "bi bi-grid-fill";
                if (type === "service") return "bi bi-layers-fill";
                if (type === "sub-service") return "bi bi-file-text-fill";
                return "bi bi-circle";
            }

            function escapeHtml(str) {
                var div = document.createElement("div");
                div.textContent = str;
                return div.innerHTML;
            }

            function escapeAttr(str) {
                return str.replace(/&/g, "&amp;").replace(/"/g, "&quot;").replace(/'/g, "&#39;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
            }

            /* ════════════════════════════════════════
             *  CHAT VIEW
             * ════════════════════════════════════════ */
            function showChatView() {
                breadcrumb.style.display = "none";
                backBtn.style.display = "none";

                body.innerHTML = '<div class="cb-messages" id="cb-messages" role="log" aria-live="polite"></div>';

                // Remove old input area if exists
                var oldInput = panel.querySelector("#cb-chat-input-area");
                if (oldInput) oldInput.remove();

                var inputHtml =
                    '<form class="cb-input-area" id="cb-chat-input-area">' +
                        '<div class="cb-file-preview" id="cb-file-preview">' +
                            '<i class="bi bi-paperclip"></i>' +
                            '<span class="cb-file-preview-name" id="cb-file-preview-name"></span>' +
                            '<button type="button" class="cb-file-preview-remove" id="cb-file-remove">&times;</button>' +
                        '</div>' +
                        '<input type="file" id="cb-file-input" style="display:none" accept=".jpg,.jpeg,.png,.gif,.webp,.svg,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.csv,.rtf,.zip,.rar,.7z" />' +
                        '<button type="button" class="cb-attach-btn" id="cb-attach-btn" title="Attach file"><i class="bi bi-paperclip"></i></button>' +
                        '<input class="cb-input" id="cb-input" type="text" autocomplete="off" placeholder="Type a message..." />' +
                        '<button class="cb-send" type="submit" id="cb-send">' +
                            '<span>Send</span><span style="font-size:14px;">\u203A</span>' +
                        '</button>' +
                    '</form>';

                var poweredDiv = panel.querySelector(".cb-powered");
                if (bottomNav) {
                    bottomNav.insertAdjacentHTML("beforebegin", inputHtml);
                } else if (poweredDiv) {
                    poweredDiv.insertAdjacentHTML("beforebegin", inputHtml);
                } else {
                    panel.insertAdjacentHTML("beforeend", inputHtml);
                }

                var messagesDiv = document.getElementById("cb-messages");
                var form = document.getElementById("cb-chat-input-area");
                var input = document.getElementById("cb-input");
                var sendButton = document.getElementById("cb-send");
                var fileInputEl = document.getElementById("cb-file-input");
                var attachBtn = document.getElementById("cb-attach-btn");
                var filePreview = document.getElementById("cb-file-preview");
                var filePreviewName = document.getElementById("cb-file-preview-name");
                var fileRemove = document.getElementById("cb-file-remove");

                // File attach handlers
                attachBtn.addEventListener("click", function() { fileInputEl.click(); });
                fileInputEl.addEventListener("change", function() {
                    if (this.files.length > 0) {
                        var file = this.files[0];
                        if (file.size > 10 * 1024 * 1024) {
                            alert("File size must be under 10 MB.");
                            this.value = "";
                            return;
                        }
                        filePreviewName.textContent = file.name + " (" + formatFileSize(file.size) + ")";
                        filePreview.classList.add("active");
                        attachBtn.classList.add("active");
                    } else {
                        clearFileSelection();
                    }
                });
                fileRemove.addEventListener("click", function() {
                    fileInputEl.value = "";
                    clearFileSelection();
                });

                function clearFileSelection() {
                    filePreview.classList.remove("active");
                    attachBtn.classList.remove("active");
                    filePreviewName.textContent = "";
                }

                function formatFileSize(bytes) {
                    if (bytes < 1024) return bytes + " B";
                    if (bytes < 1048576) return (bytes / 1024).toFixed(1) + " KB";
                    return (bytes / 1048576).toFixed(1) + " MB";
                }

                if (!conversationId) conversationId = loadConversationId();

                if (conversationId && messagesDiv.children.length === 0) {
                    restoreMessageHistory(messagesDiv);
                    pollForMessages(messagesDiv);
                } else if (!conversationId && !hasInitialGreeting) {
                    displayMessage(messagesDiv, "bot", WELCOME_MESSAGE, new Date().toISOString());
                    hasInitialGreeting = true;
                } else if (conversationId && !pollInterval) {
                    pollForMessages(messagesDiv);
                }

                setTimeout(function () { input.focus(); }, 160);

                form.addEventListener("submit", function (e) {
                    e.preventDefault();
                    sendMessage(messagesDiv, input, sendButton);
                });
                input.addEventListener("keypress", function (e) {
                    if (e.key === "Enter" && !e.shiftKey) {
                        e.preventDefault();
                        sendMessage(messagesDiv, input, sendButton);
                    }
                });
            }

            /* ── Chat helpers ────────────────────────── */
            function loadConversationId() {
                var s = localStorage.getItem(CONVERSATION_KEY);
                return s ? parseInt(s) : null;
            }
            function saveConversationId(id) {
                localStorage.setItem(CONVERSATION_KEY, id.toString());
                conversationId = id;
            }
            function loadStoredMessages() {
                var s = localStorage.getItem(STORAGE_KEY);
                return s ? JSON.parse(s) : [];
            }
            function saveMessages(msgs) {
                localStorage.setItem(STORAGE_KEY, JSON.stringify(msgs));
            }
            function addMessageToStorage(id, senderType, message, timestamp, fileData) {
                var msgs = loadStoredMessages();
                if (!msgs.find(function (m) { return m.id === id; })) {
                    var entry = { id: id, sender_type: senderType, message: message, timestamp: timestamp, created_at: timestamp };
                    if (fileData) {
                        entry.file_url = fileData.file_url || null;
                        entry.file_name = fileData.file_name || null;
                        entry.file_type = fileData.file_type || null;
                        entry.file_size = fileData.file_size || null;
                        entry.is_image = fileData.is_image || false;
                    }
                    msgs.push(entry);
                    lastMessageId = Math.max(lastMessageId, id);
                    saveMessages(msgs);
                }
            }
            function restoreMessageHistory(messagesDiv) {
                loadStoredMessages().forEach(function (msg) {
                    var type = msg.sender_type === "visitor" ? "user" : "bot";
                    var fileData = (msg.file_url || msg.file_name) ? { file_url: msg.file_url, file_name: msg.file_name, file_type: msg.file_type, file_size: msg.file_size, is_image: msg.is_image } : null;
                    displayMessage(messagesDiv, type, msg.message, msg.created_at || msg.timestamp, msg.id || null, fileData);
                    if (msg.id) lastMessageId = Math.max(lastMessageId, msg.id);
                });
            }

            function formatTime(date) {
                return date.toLocaleTimeString(undefined, { hour: "2-digit", minute: "2-digit" });
            }

            function formatFileSize(bytes) {
                if (!bytes) return "";
                if (bytes < 1024) return bytes + " B";
                if (bytes < 1048576) return (bytes / 1024).toFixed(1) + " KB";
                return (bytes / 1048576).toFixed(1) + " MB";
            }

            function displayMessage(container, type, text, timestamp, messageId, fileData) {
                var row = document.createElement("div");
                row.className = "cb-message-row " + (type === "user" ? "cb-message-user" : "cb-message-bot");
                if (messageId) row.dataset.messageId = messageId;
                var bubble = document.createElement("div");
                bubble.className = "cb-bubble " + (type === "user" ? "cb-bubble-user" : "cb-bubble-bot");
                if (text) bubble.textContent = text;

                // File attachment
                if (fileData && fileData.file_url) {
                    var fileDiv = document.createElement("div");
                    fileDiv.className = "cb-file-attachment";
                    var isImage = fileData.is_image || (fileData.file_type && fileData.file_type.indexOf("image/") === 0);
                    if (isImage) {
                        var link = document.createElement("a");
                        link.href = fileData.file_url;
                        link.target = "_blank";
                        var img = document.createElement("img");
                        img.src = fileData.file_url;
                        img.alt = fileData.file_name || "Image";
                        img.loading = "lazy";
                        link.appendChild(img);
                        fileDiv.appendChild(link);
                    } else {
                        var docLink = document.createElement("a");
                        docLink.href = fileData.file_url;
                        docLink.target = "_blank";
                        docLink.className = "cb-file-doc " + (type === "user" ? "cb-file-doc-user" : "cb-file-doc-bot");
                        docLink.innerHTML = '<i class="bi bi-file-earmark-arrow-down" style="font-size:18px;"></i>' +
                            '<span><strong>' + escapeHtml(fileData.file_name || "File") + '</strong>' +
                            (fileData.file_size ? '<br><small>' + formatFileSize(fileData.file_size) + '</small>' : '') +
                            '</span>';
                        fileDiv.appendChild(docLink);
                    }
                    bubble.appendChild(fileDiv);
                }

                var meta = document.createElement("div");
                meta.className = "cb-meta";
                meta.textContent = formatTime(timestamp ? new Date(timestamp) : new Date());
                var wrapper = document.createElement("div");
                wrapper.appendChild(bubble);
                wrapper.appendChild(meta);
                row.appendChild(wrapper);
                container.appendChild(row);
                setTimeout(function () { container.scrollTop = container.scrollHeight; }, 0);
            }

            /* ── Polling ─────────────────────────────── */
            var pollDelay = 3000;
            var pollIdleSince = Date.now();
            var POLL_FAST = 3000, POLL_SLOW = 8000, POLL_IDLE_AFTER = 30000;

            function pollForMessages(messagesDiv) {
                if (!conversationId) return;
                stopPolling();
                pollDelay = POLL_FAST;
                pollIdleSince = Date.now();

                async function doPoll() {
                    try {
                        var data = await apiPost("messages", {
                            visitor_token: visitorToken,
                            conversation_id: conversationId,
                            after_id: lastMessageId,
                        });
                        var hasNew = false;
                        if (data && Array.isArray(data.messages) && data.messages.length > 0) {
                            var currentMsgsDiv = document.getElementById("cb-messages");
                            data.messages.forEach(function (msg) {
                                var msgFileData = (msg.file_url || msg.file_name) ? { file_url: msg.file_url, file_name: msg.file_name, file_type: msg.file_type, file_size: msg.file_size, is_image: msg.is_image } : null;
                                if (msg.sender_type === "admin" || msg.sender_type === "bot") {
                                    if (currentMsgsDiv && !currentMsgsDiv.querySelector('[data-message-id="' + msg.id + '"]')) {
                                        displayMessage(currentMsgsDiv, "bot", msg.message, msg.created_at, msg.id, msgFileData);
                                        hasNew = true;
                                    }
                                    addMessageToStorage(msg.id, msg.sender_type, msg.message, msg.created_at, msgFileData);
                                }
                                lastMessageId = Math.max(lastMessageId, msg.id);
                            });
                        }
                        if (hasNew) { pollDelay = POLL_FAST; pollIdleSince = Date.now(); }
                        else if (Date.now() - pollIdleSince > POLL_IDLE_AFTER) { pollDelay = Math.min(pollDelay + 1000, POLL_SLOW); }
                    } catch (e) {
                        pollDelay = Math.min(pollDelay + 2000, POLL_SLOW);
                    }
                    if (pollInterval !== null) pollInterval = setTimeout(doPoll, pollDelay);
                }
                pollInterval = setTimeout(doPoll, pollDelay);
            }

            function stopPolling() {
                if (pollInterval) { clearTimeout(pollInterval); pollInterval = null; }
            }

            /* ── Send message ────────────────────────── */
            async function sendMessage(messagesDiv, input, sendButton) {
                var message = input.value.trim();
                var fileInputEl = document.getElementById("cb-file-input");
                var hasFile = fileInputEl && fileInputEl.files.length > 0;
                if (!message && !hasFile) return;

                // Show user message immediately
                if (message) {
                    displayMessage(messagesDiv, "user", message, new Date().toISOString());
                }
                if (hasFile) {
                    var fileName = fileInputEl.files[0].name;
                    if (!message) {
                        displayMessage(messagesDiv, "user", "\uD83D\uDCCE " + fileName, new Date().toISOString());
                    }
                }

                input.value = "";
                sendButton.disabled = true;

                try {
                    var data;
                    if (hasFile) {
                        // Use FormData for file upload
                        var formData = new FormData();
                        formData.append("api_key", API_KEY);
                        formData.append("visitor_token", visitorToken);
                        if (message) formData.append("message", message);
                        formData.append("file", fileInputEl.files[0]);

                        var res = await fetch(SERVER_URL + "/api/chat/send-file", {
                            method: "POST",
                            body: formData,
                        });
                        data = await res.json();

                        // Clear file selection
                        fileInputEl.value = "";
                        var clearFn = function() {
                            var preview = document.getElementById("cb-file-preview");
                            var btn = document.getElementById("cb-attach-btn");
                            if (preview) preview.classList.remove("active");
                            if (btn) btn.classList.remove("active");
                        };
                        clearFn();
                    } else {
                        data = await apiPost("send", { visitor_token: visitorToken, message: message });
                    }

                    if (data && data.conversation_id) {
                        if (!conversationId || conversationId !== data.conversation_id) {
                            saveConversationId(data.conversation_id);
                            pollForMessages(messagesDiv);
                        }
                    }
                    if (data && Array.isArray(data.messages) && data.messages.length > 0) {
                        data.messages.forEach(function (msg) {
                            var msgFileData = (msg.file_url || msg.file_name) ? { file_url: msg.file_url, file_name: msg.file_name, file_type: msg.file_type, file_size: msg.file_size, is_image: msg.is_image } : null;
                            addMessageToStorage(msg.id, msg.sender_type, msg.message, msg.created_at, msgFileData);
                        });
                        data.messages.forEach(function (msg) {
                            if (msg.sender_type === "admin" || msg.sender_type === "bot") {
                                if (!messagesDiv.querySelector('[data-message-id="' + msg.id + '"]')) {
                                    var msgFileData = (msg.file_url || msg.file_name) ? { file_url: msg.file_url, file_name: msg.file_name, file_type: msg.file_type, file_size: msg.file_size, is_image: msg.is_image } : null;
                                    displayMessage(messagesDiv, "bot", msg.message, msg.created_at, msg.id, msgFileData);
                                }
                            }
                        });
                        var maxId = Math.max.apply(null, [lastMessageId].concat(data.messages.map(function (m) { return m.id || 0; })));
                        lastMessageId = maxId;
                    }
                    pollDelay = POLL_FAST;
                    pollIdleSince = Date.now();
                } catch (e) {
                    displayMessage(messagesDiv, "bot", "Sorry, something went wrong. Please try again.", new Date().toISOString());
                } finally {
                    sendButton.disabled = false;
                    input.focus();
                }
            }
        } // end bootstrap

        bootstrap();
    } // end initChatWidget

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", initChatWidget);
    } else {
        initChatWidget();
    }
})();
