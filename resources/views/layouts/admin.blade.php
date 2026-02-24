<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Chatbot Admin') - Chatbot Admin Dashboard</title>

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <!-- Custom Styles -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #ff7a18;
            --primary-dark: #e8620b;
            --primary-soft: #fff3e0;
            --surface: #ffffff;
            --border-subtle: #e5e7eb;
            --text-main: #111827;
            --text-muted: #6b7280;
        }

        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
            color: var(--text-main);
        }

        /* Sidebar Styles */
        .sidebar {
            height: 100vh;
            background: linear-gradient(135deg, #ff7a18 0%, #ff512f 100%);
            position: fixed;
            top: 0;
            left: 0;
            width: 260px;
            overflow-y: auto;
            padding-top: 80px;
            z-index: 99;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .main-wrapper {
            margin-left: 260px;
            min-height: 100vh;
        }

        .sidebar.collapse-mode {
            width: 70px;
        }

        .sidebar.collapse-mode .sidebar-text {
            display: none;
        }

        .sidebar.collapse-mode .main-wrapper {
            margin-left: 70px;
        }

        /* Navbar Styles */
        .navbar {
            background: white;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            padding: 15px 30px;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
            border-bottom: 1px solid #e9ecef;
        }

        .navbar-brand {
            font-weight: 700;
            color: var(--primary);
            font-size: 24px;
            margin-left: 280px;
        }

        /* Sidebar Menu */
        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar-menu li {
            margin: 0;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background-color: rgba(255, 255, 255, 0.15);
            color: white;
            border-left-color: white;
        }

        .sidebar-menu i {
            font-size: 20px;
            min-width: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .sidebar-text {
            margin-left: 10px;
        }

        /* Main Content */
        .main-content {
            padding: 100px 30px 30px 30px;
        }

        /* Cards */
        .card {
            border: none;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            border-radius: 12px;
            padding: 25px;
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
            transform: translateY(-2px);
        }

        /* Statistics Cards */
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin-right: 20px;
        }

        .stat-icon.bg-primary-light {
            background-color: var(--primary-soft);
            color: var(--primary);
        }

        .stat-icon.bg-success-light {
            background-color: #e8f5e9;
            color: #10b981;
        }

        .stat-icon.bg-danger-light {
            background-color: #ffebee;
            color: #ef4444;
        }

        .stat-icon.bg-warning-light {
            background-color: #fff3e0;
            color: #f59e0b;
        }

        .stat-number {
            font-size: 32px;
            font-weight: 700;
            color: #1f2937;
            line-height: 1;
        }

        .stat-label {
            font-size: 14px;
            color: #6b7280;
            margin-top: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Table Styles */
        .table {
            background: white;
            border-radius: 12px;
            overflow: hidden;
        }

        .table thead {
            background-color: #f3f4f6;
            border-bottom: 2px solid #e5e7eb;
        }

        .table-hover tbody tr:hover {
            background-color: #f9fafb;
        }

        .table td {
            vertical-align: middle;
            border-color: #e5e7eb;
            padding: 15px;
        }

        /* Badges */
        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 500;
            font-size: 12px;
        }

        /* Buttons */
        .btn {
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 500;
            transition: all 0.3s ease;
            border: none;
        }

        .btn-primary {
            background-color: var(--primary);
            color: #ffffff;
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
            color: #ffffff;
            transform: translateY(-2px);
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
        }

        /* Search Input */
        .search-input {
            background-color: #f3f4f6;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 10px 15px;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            background-color: #ffffff;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(255, 122, 24, 0.12);
            outline: none;
        }

        /* Chat Styles */
        .chat-list {
            height: calc(100vh - 200px);
            overflow-y: auto;
            border-right: 1px solid #e5e7eb;
        }

        .chat-item {
            padding: 15px;
            border-bottom: 1px solid #e5e7eb;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .chat-item:hover {
            background-color: #f9fafb;
        }

        .chat-item.active {
            background-color: var(--primary-soft);
            border-left: 4px solid var(--primary);
            padding-left: 11px;
        }

        .chat-item-name {
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 5px;
        }

        .chat-item-message {
            font-size: 13px;
            color: #6b7280;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .chat-item-time {
            font-size: 12px;
            color: #9ca3af;
        }

        /* Chat Window */
        .chat-window {
            height: calc(100vh - 100px);
            display: flex;
            flex-direction: column;
        }

        .chat-header {
            padding: 20px;
            border-bottom: 1px solid #e5e7eb;
            background: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
            background: #f9fafb;
        }

        .message {
            margin-bottom: 15px;
            display: flex;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(10px);
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
            max-width: 60%;
            padding: 12px 16px;
            border-radius: 12px;
            word-wrap: break-word;
        }

        .visitor-message .message-content {
            background-color: white;
            color: #1f2937;
            border: 1px solid #e5e7eb;
        }

        .admin-message .message-content {
            background-color: var(--primary);
            color: #ffffff;
        }

        .message-time {
            font-size: 11px;
            color: #9ca3af;
            margin-top: 5px;
        }

        /* Chat Input */
        .chat-input-area {
            padding: 20px;
            background: white;
            border-top: 1px solid #e5e7eb;
            display: flex;
            gap: 10px;
        }

        .chat-input-area input {
            flex: 1;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 12px 15px;
            font-size: 14px;
        }

        .chat-input-area input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(255, 122, 24, 0.12);
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

            .navbar-brand {
                margin-left: 15px;
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
                border-bottom: 1px solid #e5e7eb;
                height: auto;
                max-height: 300px;
            }

            .message-content {
                max-width: 85%;
            }
        }

        /* Scrollbar Styling */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
        }
    </style>

    @yield('extra-styles')
</head>

<body>
    <!-- TOP NAVBAR -->
    <nav class="navbar">
        <div class="d-flex align-items-center" style="width: 100%; justify-content: space-between;">
            <!-- Left Side: Brand & Toggle -->
            <div class="d-flex align-items-center">
                <button class="btn btn-link text-dark d-none d-lg-block" id="sidebar-toggle"
                    style="border: none; font-size: 24px;">
                    <i class="bi bi-list"></i>
                </button>
                <button class="btn btn-link text-dark d-lg-none" id="mobile-sidebar-toggle"
                    style="border: none; font-size: 24px;">
                    <i class="bi bi-list"></i>
                </button>
                <a class="navbar-brand" style="margin-left: 20px; margin: 0;">
                    <i class="bi bi-chat-dots"></i> Chatbot Admin
                </a>
            </div>

            <!-- Middle: Search Bar -->
            <div class="flex-grow-1" style="margin: 0 30px; max-width: 400px;">
                <div class="input-group">
                    <span class="input-group-text search-input" style="border: none;">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" class="form-control search-input" placeholder="Search conversations..."
                        style="border: none;">
                </div>
            </div>

            <!-- Right Side: Icons & Profile -->
            <div class="d-flex align-items-center gap-3">
                <!-- Notification Icon -->
                <button class="btn btn-link text-dark position-relative" style="font-size: 20px; border: none;">
                    <i class="bi bi-bell"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                        style="font-size: 10px;">
                        3
                    </span>
                </button>

                <!-- Profile Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-link text-dark d-flex align-items-center gap-2" type="button"
                        id="profileDropdown" data-bs-toggle="dropdown">
                        <img src="https://ui-avatars.com/api/?name=Admin+User&background=667eea&color=fff"
                            alt="Admin" width="36" height="36" class="rounded-circle">
                        <span class="d-none d-sm-inline text-dark" style="font-weight: 500;">Admin</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                        <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Profile</a></li>
                        <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Settings</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item text-danger" href="#"><i
                                    class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">
        <ul class="sidebar-menu">
            <li>
                <a href="#" class="active">
                    <i class="bi bi-house-door-fill"></i>
                    <span class="sidebar-text">Dashboard</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.conversations') }}">
                    <i class="bi bi-chat-left-text"></i>
                    <span class="sidebar-text">Conversations</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="bi bi-circle-fill" style="font-size: 10px;"></i>
                    <span class="sidebar-text">Active Chats</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="bi bi-check-circle-fill"></i>
                    <span class="sidebar-text">Closed Chats</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="bi bi-graph-up"></i>
                    <span class="sidebar-text">Analytics</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="bi bi-gear-fill"></i>
                    <span class="sidebar-text">Settings</span>
                </a>
            </li>
            <li style="margin-top: 30px; padding-top: 30px; border-top: 1px solid rgba(255, 255, 255, 0.2);">
                <a href="#" style="color: rgba(255, 255, 255, 0.8);">
                    <i class="bi bi-box-arrow-right"></i>
                    <span class="sidebar-text">Logout</span>
                </a>
            </li>
        </ul>
    </aside>

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

        if (window.innerWidth > 992) {
            sidebarToggle?.addEventListener('click', function() {
                sidebar.classList.toggle('collapse-mode');
            });
        }

        mobileSidebarToggle?.addEventListener('click', function() {
            sidebar.classList.toggle('show');
        });

        // Close sidebar on item click (mobile)
        document.querySelectorAll('.sidebar-menu a').forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth < 992) {
                    sidebar.classList.remove('show');
                }
            });
        });
    </script>

    @yield('scripts')
</body>

</html>
