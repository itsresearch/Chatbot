<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Miraai Admin</title>

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind / Vite bundle (includes Echo) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #f97316;
            --primary-dark: #ea580c;
            --primary-light: #fb923c;
            --primary-soft: rgba(249, 115, 22, 0.10);
            --primary-gradient: linear-gradient(135deg, #ff8c42 0%, #f97316 50%, #ea580c 100%);
            --surface: #ffffff;
            --surface-elevated: #ffffff;
            --surface-soft: #fef7f0;
            --surface-bg: #faf5f0;
            --border-subtle: #f3e8de;
            --border-light: #fde8d0;
            --text-main: #1e293b;
            --text-secondary: #475569;
            --text-muted: #94a3b8;
            --sidebar-bg: linear-gradient(180deg, #ffffff 0%, #fef7f0 100%);
            --shadow-sm: 0 1px 3px rgba(249, 115, 22, 0.06);
            --shadow-md: 0 4px 16px rgba(249, 115, 22, 0.08);
            --shadow-lg: 0 8px 32px rgba(249, 115, 22, 0.10);
        }

        body {
            background-color: var(--surface-bg);
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
            color: var(--text-main);
        }

        /* Sidebar Styles */
        .sidebar {
            height: 100vh;
            background: var(--sidebar-bg);
            position: fixed;
            top: 0;
            left: 0;
            width: 260px;
            overflow-y: auto;
            padding-top: 0;
            z-index: 99;
            border-right: 1px solid var(--border-subtle);
            display: flex;
            flex-direction: column;
        }

        /* Logo area in sidebar */
        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 20px 22px;
            border-bottom: 1px solid var(--border-subtle);
        }

        .sidebar-brand img {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            object-fit: cover;
        }

        .sidebar-brand-text {
            font-size: 22px;
            font-weight: 800;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -0.5px;
        }

        .main-wrapper {
            margin-left: 260px;
            min-height: 100vh;
        }

        .sidebar.collapse-mode {
            width: 72px;
        }

        .sidebar.collapse-mode .sidebar-text,
        .sidebar.collapse-mode .sidebar-brand-text {
            display: none;
        }

        .sidebar.collapse-mode .sidebar-brand {
            justify-content: center;
            padding: 20px 10px;
        }

        .sidebar.collapse-mode~.main-wrapper {
            margin-left: 72px;
        }

        /* Navbar Styles */
        .navbar {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            box-shadow: 0 1px 0 var(--border-subtle);
            padding: 0 30px;
            height: 66px;
            position: fixed;
            top: 0;
            left: 260px;
            right: 0;
            z-index: 100;
            display: flex;
            align-items: center;
        }

        .navbar-brand {
            font-weight: 700;
            color: var(--text-main);
            font-size: 18px;
        }

        /* Sidebar Menu */
        .sidebar-menu {
            list-style: none;
            padding: 12px 12px;
            margin: 0;
            flex: 1;
        }

        .sidebar-menu .menu-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: var(--text-muted);
            padding: 18px 14px 8px;
        }

        .sidebar-menu li {
            margin: 2px 0;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 11px 14px;
            color: var(--text-secondary);
            text-decoration: none;
            transition: all 0.2s ease;
            border-radius: 10px;
            font-weight: 500;
            font-size: 14px;
            border: 1px solid transparent;
        }

        .sidebar-menu a:hover {
            background-color: var(--primary-soft);
            color: var(--primary-dark);
        }

        .sidebar-menu a.active {
            background: var(--primary-gradient);
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(249, 115, 22, 0.30);
            border-color: transparent;
        }

        .sidebar-menu a.active i {
            color: #ffffff;
        }

        .sidebar-menu i {
            font-size: 18px;
            min-width: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
            transition: color 0.2s;
        }

        .sidebar-menu a:hover i {
            color: var(--primary);
        }

        .sidebar-text {
            margin-left: 4px;
        }

        /* Main Content */
        .main-content {
            padding: 90px 30px 30px 30px;
        }

        /* Cards */
        .card {
            border: 1px solid var(--border-subtle);
            background: var(--surface);
            box-shadow: var(--shadow-sm);
            border-radius: 16px;
            padding: 25px;
            transition: all 0.25s ease;
        }

        .card:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-1px);
        }

        /* Statistics Cards */
        .stat-card {
            background: var(--surface);
            border: 1px solid var(--border-subtle);
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 20px;
            box-shadow: var(--shadow-sm);
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.25s ease;
        }

        .stat-card:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
        }

        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-right: 20px;
        }

        .stat-icon.bg-primary-light {
            background: linear-gradient(135deg, rgba(249, 115, 22, 0.12) 0%, rgba(249, 115, 22, 0.06) 100%);
            color: var(--primary);
        }

        .stat-icon.bg-success-light {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.12) 0%, rgba(16, 185, 129, 0.06) 100%);
            color: #10b981;
        }

        .stat-icon.bg-danger-light {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.12) 0%, rgba(239, 68, 68, 0.06) 100%);
            color: #ef4444;
        }

        .stat-icon.bg-warning-light {
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.12) 0%, rgba(245, 158, 11, 0.06) 100%);
            color: #f59e0b;
        }

        .stat-number {
            font-size: 28px;
            font-weight: 800;
            color: var(--text-main);
            line-height: 1;
        }

        .stat-label {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 6px;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            font-weight: 600;
        }

        /* Table Styles */
        .table {
            background: var(--surface);
            border-radius: 12px;
            overflow: hidden;
        }

        .table thead {
            background-color: var(--surface-soft);
            border-bottom: 1px solid var(--border-subtle);
        }

        .table thead th {
            color: var(--text-secondary);
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table-hover tbody tr:hover {
            background-color: var(--surface-soft);
        }

        .table td {
            vertical-align: middle;
            border-color: var(--border-subtle);
            padding: 14px 16px;
            color: var(--text-secondary);
        }

        /* Badges */
        .badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 11px;
        }

        /* Buttons */
        .btn {
            border-radius: 10px;
            padding: 10px 20px;
            font-weight: 600;
            transition: all 0.2s ease;
            border: none;
            font-size: 14px;
        }

        .btn-primary {
            background: var(--primary-gradient);
            color: #ffffff;
            box-shadow: 0 3px 12px rgba(249, 115, 22, 0.25);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #ea580c 0%, #dc2626 100%);
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(249, 115, 22, 0.35);
        }

        .btn-outline-secondary {
            border: 1px solid var(--border-subtle);
            color: var(--text-secondary);
            background: var(--surface);
        }

        .btn-outline-secondary:hover {
            background: var(--surface-soft);
            border-color: var(--primary-light);
            color: var(--primary-dark);
        }

        .btn-sm {
            padding: 7px 14px;
            font-size: 13px;
        }

        /* Search Input */
        .search-input {
            background-color: var(--surface-soft);
            border: 1px solid var(--border-subtle);
            border-radius: 10px;
            padding: 10px 15px;
            transition: all 0.2s ease;
            color: var(--text-main);
        }

        .search-input:focus {
            background-color: #ffffff;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.10);
            outline: none;
        }

        /* Chat Styles */
        .chat-list {
            height: calc(100vh - 200px);
            overflow-y: auto;
            border-right: 1px solid var(--border-subtle);
        }

        .chat-item {
            padding: 14px 18px;
            border-bottom: 1px solid var(--border-subtle);
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .chat-item:hover {
            background-color: var(--surface-soft);
        }

        .chat-item.active {
            background: linear-gradient(135deg, rgba(249, 115, 22, 0.08) 0%, rgba(249, 115, 22, 0.03) 100%);
            border-left: 3px solid var(--primary);
            padding-left: 15px;
        }

        .chat-item-name {
            font-weight: 600;
            color: var(--text-main);
            margin-bottom: 4px;
            font-size: 14px;
        }

        .chat-item-message {
            font-size: 13px;
            color: var(--text-muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .chat-item-time {
            font-size: 11px;
            color: var(--text-muted);
            font-weight: 500;
        }

        /* Unread conversation highlight */
        .chat-item.chat-item-unread {
            background-color: rgba(249, 115, 22, 0.07);
            border-left: 3px solid var(--primary);
            padding-left: 15px;
            position: relative;
        }

        .chat-item.chat-item-unread::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(90deg, rgba(249, 115, 22, 0.06) 0%, transparent 100%);
            pointer-events: none;
        }

        .chat-item.chat-item-unread:hover {
            background-color: rgba(249, 115, 22, 0.12);
        }

        .chat-item.chat-item-unread .chat-item-time {
            color: var(--primary);
            font-weight: 700;
        }

        .chat-item.chat-item-unread .chat-item-name {
            color: var(--text-main);
        }

        .chat-item.chat-item-unread .chat-item-message {
            color: var(--text-secondary);
            font-weight: 600;
        }

        /* Chat Window */
        .chat-window {
            height: calc(100vh - 100px);
            display: flex;
            flex-direction: column;
            border-radius: 16px;
            overflow: hidden;
        }

        .chat-header {
            padding: 18px 24px;
            border-bottom: 1px solid var(--border-subtle);
            background: var(--surface);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 24px;
            background: linear-gradient(180deg, #fef7f0 0%, #ffffff 40%, #fef7f0 100%);
        }

        .message {
            margin-bottom: 16px;
            display: flex;
            width: 100%;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .message.visitor-message {
            justify-content: flex-start;
        }

        .message.admin-message {
            justify-content: flex-end;
        }

        .message-content {
            display: inline-block;
            max-width: 60%;
            min-width: 40px;
            padding: 12px 18px;
            border-radius: 20px;
            word-wrap: break-word;
            white-space: normal;
            font-size: 14px;
            line-height: 1.5;
        }

        .visitor-message .message-content {
            background-color: #ffffff;
            color: var(--text-main);
            border: 1px solid var(--border-subtle);
            border-bottom-left-radius: 6px;
            box-shadow: var(--shadow-sm);
        }

        .admin-message .message-content {
            background: var(--primary-gradient);
            color: #ffffff;
            border-bottom-right-radius: 6px;
            box-shadow: 0 3px 12px rgba(249, 115, 22, 0.20);
        }

        .message-time {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 4px;
            font-weight: 500;
        }

        /* Chat Input */
        .chat-input-area {
            padding: 18px 24px;
            background: var(--surface);
            border-top: 1px solid var(--border-subtle);
            display: flex;
            gap: 12px;
        }

        .chat-input-area input {
            flex: 1;
            border: 1.5px solid var(--border-subtle);
            border-radius: 999px;
            padding: 12px 20px;
            font-size: 14px;
            background-color: var(--surface-soft);
            color: var(--text-main);
            transition: all 0.2s;
        }

        .chat-input-area input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.10);
            background-color: #ffffff;
        }

        .chat-input-area .btn-primary {
            border-radius: 999px;
            padding: 10px 22px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
                width: 260px;
                z-index: 1000;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-wrapper {
                margin-left: 0;
            }

            .navbar {
                left: 0;
            }

            .main-content {
                padding: 80px 15px 15px 15px;
            }

            .stat-card {
                flex-direction: column;
                text-align: center;
            }

            .stat-icon {
                margin-right: 0;
                margin-bottom: 15px;
            }

            .chat-list {
                border-right: none;
                border-bottom: 1px solid var(--border-subtle);
                height: auto;
                max-height: 300px;
            }

            .message-content {
                max-width: 85%;
            }
        }

        /* Scrollbar Styling */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #e2d6cc;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #d4c4b6;
        }

        /* Profile avatar ring */
        .profile-avatar {
            border: 2px solid var(--border-subtle);
            transition: border-color 0.2s;
        }

        .profile-avatar:hover {
            border-color: var(--primary-light);
        }

        /* Dropdown styling */
        .dropdown-menu {
            border: 1px solid var(--border-subtle);
            border-radius: 12px;
            box-shadow: var(--shadow-lg);
            padding: 6px;
            background: var(--surface);
        }

        .dropdown-item {
            border-radius: 8px;
            padding: 8px 14px;
            font-size: 14px;
            font-weight: 500;
            color: var(--text-secondary);
            transition: all 0.15s;
        }

        .dropdown-item:hover {
            background-color: var(--surface-soft);
            color: var(--primary-dark);
        }

        .dropdown-divider {
            border-color: var(--border-subtle);
            margin: 4px 0;
        }

        /* Sidebar footer */
        .sidebar-footer {
            padding: 16px 22px;
            border-top: 1px solid var(--border-subtle);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar-footer-avatar {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            border: 2px solid var(--border-subtle);
        }

        .sidebar-footer-name {
            font-weight: 600;
            font-size: 13px;
            color: var(--text-main);
        }

        .sidebar-footer-role {
            font-size: 11px;
            color: var(--text-muted);
            font-weight: 500;
        }

        /* Mobile overlay */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.3);
            z-index: 998;
        }

        .sidebar-overlay.show {
            display: block;
        }
    </style>

    @yield('extra-styles')
</head>

<body>
    <!-- Mobile Overlay -->
    <div class="sidebar-overlay" id="sidebar-overlay"></div>

    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">
        <!-- Brand / Logo -->
        <div class="sidebar-brand">
            <img src="{{ asset('images/chatbot-logo.png') }}" alt="Miraai Logo">
            <span class="sidebar-brand-text">Miraai</span>
        </div>

        <ul class="sidebar-menu">
            <li class="menu-label">Main Menu</li>
            <li>
                <a href="{{ route('admin.dashboard') }}"
                    class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2-fill"></i>
                    <span class="sidebar-text">Dashboard</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.conversations') }}"
                    class="{{ request()->routeIs('admin.conversations') || request()->routeIs('admin.chat') ? 'active' : '' }}">
                    <i class="bi bi-chat-square-text-fill"></i>
                    <span class="sidebar-text">Conversations</span>
                </a>
            </li>
        </ul>

        <!-- Sidebar Footer -->
        <div class="sidebar-footer">
            <img src="https://ui-avatars.com/api/?name=Admin&background=f97316&color=fff&bold=true" alt="Admin"
                class="sidebar-footer-avatar">
            <div style="flex: 1; min-width: 0;">
                <div class="sidebar-footer-name">Admin User</div>
                <div class="sidebar-footer-role">Administrator</div>
            </div>
        </div>
    </aside>

    <!-- TOP NAVBAR -->
    <nav class="navbar">
        <div class="d-flex align-items-center" style="width: 100%; justify-content: space-between;">
            <!-- Left Side: Toggle & Page title -->
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-link d-none d-lg-block" id="sidebar-toggle"
                    style="border: none; font-size: 20px; color: var(--text-secondary); padding: 4px 8px;">
                    <i class="bi bi-layout-sidebar-inset"></i>
                </button>
                <button class="btn btn-link d-lg-none" id="mobile-sidebar-toggle"
                    style="border: none; font-size: 20px; color: var(--text-secondary); padding: 4px 8px;">
                    <i class="bi bi-list"></i>
                </button>
                <div style="height: 24px; width: 1px; background: var(--border-subtle); margin: 0 4px;"
                    class="d-none d-md-block"></div>
                <span class="navbar-brand mb-0" style="margin: 0;">@yield('title', 'Dashboard')</span>
            </div>

            <!-- Right Side: Profile Only -->
            <div class="d-flex align-items-center">
                <div class="dropdown">
                    <button class="btn btn-link d-flex align-items-center gap-2 text-decoration-none" type="button"
                        id="profileDropdown" data-bs-toggle="dropdown" style="padding: 4px;">
                        <img src="https://ui-avatars.com/api/?name=Admin&background=f97316&color=fff&bold=true"
                            alt="Admin" width="36" height="36" class="rounded-circle profile-avatar">
                        <div class="d-none d-sm-block text-start">
                            <div style="font-weight: 600; font-size: 13px; color: var(--text-main); line-height: 1.2;">
                                Admin</div>
                            <div style="font-size: 11px; color: var(--text-muted); line-height: 1.2;">Administrator
                            </div>
                        </div>
                        <i class="bi bi-chevron-down d-none d-sm-inline"
                            style="font-size: 12px; color: var(--text-muted);"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                        <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"
                                    style="color: var(--text-muted);"></i>Profile</a></li>
                        <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"
                                    style="color: var(--text-muted);"></i>Settings</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="#" style="color: #ef4444;"><i
                                    class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- MAIN CONTENT -->
    <div class="main-wrapper">
        <main class="main-content">
            @yield('content')
        </main>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Sidebar Toggle Script -->
    <script>
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const mobileSidebarToggle = document.getElementById('mobile-sidebar-toggle');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebar-overlay');

        if (window.innerWidth > 992) {
            sidebarToggle?.addEventListener('click', function() {
                sidebar.classList.toggle('collapse-mode');
                // Adjust navbar left
                const navbar = document.querySelector('.navbar');
                if (sidebar.classList.contains('collapse-mode')) {
                    navbar.style.left = '72px';
                } else {
                    navbar.style.left = '260px';
                }
            });
        }

        mobileSidebarToggle?.addEventListener('click', function() {
            sidebar.classList.toggle('show');
            sidebarOverlay.classList.toggle('show');
        });

        sidebarOverlay?.addEventListener('click', function() {
            sidebar.classList.remove('show');
            sidebarOverlay.classList.remove('show');
        });

        // Close sidebar on item click (mobile)
        document.querySelectorAll('.sidebar-menu a').forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth < 992) {
                    sidebar.classList.remove('show');
                    sidebarOverlay.classList.remove('show');
                }
            });
        });
    </script>

    @yield('scripts')
</body>

</html>
