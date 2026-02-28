{{-- Shared admin layout: used by both SuperAdmin & Client dashboards (DRY) --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Miraai</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

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

        .sidebar {
            height: 100vh;
            background: var(--sidebar-bg);
            position: fixed;
            top: 0;
            left: 0;
            width: 260px;
            overflow-y: auto;
            z-index: 99;
            border-right: 1px solid var(--border-subtle);
            display: flex;
            flex-direction: column;
        }

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

        .navbar {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(16px);
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

        .sidebar-menu {
            list-style: none;
            padding: 12px;
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
            transition: all 0.2s;
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
            color: #fff;
            box-shadow: 0 4px 14px rgba(249, 115, 22, 0.30);
            border-color: transparent;
        }

        .sidebar-menu a.active i {
            color: #fff;
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

        .main-content {
            padding: 90px 30px 30px;
        }

        .card {
            border: 1px solid var(--border-subtle);
            background: var(--surface);
            box-shadow: var(--shadow-sm);
            border-radius: 16px;
            padding: 25px;
            transition: all 0.25s;
        }

        .card:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-1px);
        }

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
            transition: all 0.25s;
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

        .stat-icon.bg-info-light {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.12) 0%, rgba(59, 130, 246, 0.06) 100%);
            color: #3b82f6;
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

        .badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 11px;
        }

        .btn {
            border-radius: 10px;
            padding: 10px 20px;
            font-weight: 600;
            transition: all 0.2s;
            border: none;
            font-size: 14px;
        }

        .btn-primary {
            background: var(--primary-gradient);
            color: #fff;
            box-shadow: 0 3px 12px rgba(249, 115, 22, 0.25);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #ea580c 0%, #dc2626 100%);
            color: #fff;
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

        .search-input {
            background-color: var(--surface-soft);
            border: 1px solid var(--border-subtle);
            border-radius: 10px;
            padding: 10px 15px;
            transition: all 0.2s;
            color: var(--text-main);
        }

        .search-input:focus {
            background-color: #fff;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.10);
            outline: none;
        }

        .chat-list {
            height: calc(100vh - 200px);
            overflow-y: auto;
            border-right: 1px solid var(--border-subtle);
        }

        .chat-item {
            padding: 14px 18px;
            border-bottom: 1px solid var(--border-subtle);
            cursor: pointer;
            transition: all 0.15s;
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

        .chat-item.chat-item-unread {
            background-color: rgba(249, 115, 22, 0.07);
            border-left: 3px solid var(--primary);
            padding-left: 15px;
            position: relative;
        }

        .chat-item.chat-item-unread .chat-item-time {
            color: var(--primary);
            font-weight: 700;
        }

        .chat-item.chat-item-unread .chat-item-message {
            color: var(--text-secondary);
            font-weight: 600;
        }

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
            background-color: #fff;
            color: var(--text-main);
            border: 1px solid var(--border-subtle);
            border-bottom-left-radius: 6px;
            box-shadow: var(--shadow-sm);
        }

        .admin-message .message-content {
            background: var(--primary-gradient);
            color: #fff;
            border-bottom-right-radius: 6px;
            box-shadow: 0 3px 12px rgba(249, 115, 22, 0.20);
        }

        .message-time {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 4px;
            font-weight: 500;
        }

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
            background-color: #fff;
        }

        .chat-input-area .btn-primary {
            border-radius: 999px;
            padding: 10px 22px;
        }

        .profile-avatar {
            border: 2px solid var(--border-subtle);
            transition: border-color 0.2s;
        }

        .profile-avatar:hover {
            border-color: var(--primary-light);
        }

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

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s;
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
                padding: 80px 15px 15px;
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
    </style>

    @yield('extra-styles')
</head>

<body>
    <div class="sidebar-overlay" id="sidebar-overlay"></div>

    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <img src="{{ asset('images/chatbot-logo.png') }}" alt="Miraai Logo">
            <span class="sidebar-brand-text">Miraai</span>
        </div>

        <ul class="sidebar-menu">
            @yield('sidebar-menu')
        </ul>

        <!-- Sidebar Footer -->
        <div class="sidebar-footer">
            @php $avatarName = urlencode(auth()->user()->name ?? 'U'); @endphp
            <img src="https://ui-avatars.com/api/?name={{ $avatarName }}&background=f97316&color=fff&bold=true"
                alt="{{ auth()->user()->name }}" class="sidebar-footer-avatar">
            <div style="flex: 1; min-width: 0;">
                <div class="sidebar-footer-name">{{ auth()->user()->name }}</div>
                <div class="sidebar-footer-role">
                    {{ auth()->user()->isSuperAdmin() ? 'Super Admin' : auth()->user()->company_name ?? 'Client' }}
                </div>
            </div>
        </div>
    </aside>

    <!-- TOP NAVBAR -->
    <nav class="navbar">
        <div class="d-flex align-items-center" style="width: 100%; justify-content: space-between;">
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
                <span class="mb-0"
                    style="font-weight: 700; font-size: 18px; color: var(--text-main);">@yield('title', 'Dashboard')</span>
            </div>

            <div class="d-flex align-items-center">
                <div class="dropdown">
                    <button class="btn btn-link d-flex align-items-center gap-2 text-decoration-none" type="button"
                        id="profileDropdown" data-bs-toggle="dropdown" style="padding: 4px;">
                        <img src="https://ui-avatars.com/api/?name={{ $avatarName }}&background=f97316&color=fff&bold=true"
                            alt="{{ auth()->user()->name }}" width="36" height="36"
                            class="rounded-circle profile-avatar">
                        <div class="d-none d-sm-block text-start">
                            <div style="font-weight: 600; font-size: 13px; color: var(--text-main); line-height: 1.2;">
                                {{ auth()->user()->name }}</div>
                            <div style="font-size: 11px; color: var(--text-muted); line-height: 1.2;">
                                {{ auth()->user()->isSuperAdmin() ? 'Super Admin' : 'Client' }}
                            </div>
                        </div>
                        <i class="bi bi-chevron-down d-none d-sm-inline"
                            style="font-size: 12px; color: var(--text-muted);"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('profile.show') }}"><i class="bi bi-person me-2"
                                    style="color: var(--text-muted);"></i>Profile</a></li>
                        <li>
                            <hr class="dropdown-divider" style="border-color: var(--border-subtle);">
                        </li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item" style="color: #ef4444;">
                                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- MAIN CONTENT -->
    <div class="main-wrapper">
        <main class="main-content">
            {{-- Flash messages --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-3" role="alert"
                    style="border-radius: 12px; border: 1px solid #d1fae5; background: #ecfdf5; color: #065f46;">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert"
                    style="border-radius: 12px; border: 1px solid #fecaca; background: #fef2f2; color: #991b1b;">
                    <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const mobileSidebarToggle = document.getElementById('mobile-sidebar-toggle');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebar-overlay');

        if (window.innerWidth > 992) {
            sidebarToggle?.addEventListener('click', function() {
                sidebar.classList.toggle('collapse-mode');
                const navbar = document.querySelector('.navbar');
                navbar.style.left = sidebar.classList.contains('collapse-mode') ? '72px' : '260px';
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
