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

        /* Dark theme overrides */
        body.theme-dark {
            --surface-bg: rgb(16, 16, 16);
            --surface: rgb(24, 24, 24);
            --surface-elevated: rgb(38, 38, 38);
            --surface-soft: rgb(24, 24, 24);
            --border-subtle: #27272a;
            --border-light: #3f3f46;
            --text-main: #f9fafb;
            --text-secondary: #e5e7eb;
            --text-muted: #9ca3af;
            --sidebar-bg: linear-gradient(180deg, rgb(16, 16, 16) 0%, rgb(24, 24, 24) 100%);
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.4);
            --shadow-md: 0 4px 16px rgba(0, 0, 0, 0.55);
            --shadow-lg: 0 8px 32px rgba(0, 0, 0, 0.7);
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

        /* Dark theme navbar */
        body.theme-dark .navbar {
            background: rgba(16, 16, 16, 0.95);
        }

        /* Dark theme specific tweaks */
        body.theme-dark .card,
        body.theme-dark .chat-window,
        body.theme-dark .dropdown-menu {
            background: var(--surface-elevated);
        }

        body.theme-dark .chat-messages {
            background: radial-gradient(circle at top left, #27272f 0%, #101010 45%, #000000 100%);
        }

        body.theme-dark .chat-item:hover {
            background-color: #18181b;
        }

        body.theme-dark .chat-item.chat-item-unread {
            background-color: rgba(249, 115, 22, 0.18);
        }

        body.theme-dark .chat-input-area input {
            background-color: #18181b;
            border-color: #27272a;
            color: var(--text-main);
        }

        body.theme-dark .chat-input-area input::placeholder {
            color: var(--text-muted);
        }

        body.theme-dark ::-webkit-scrollbar-thumb {
            background: #3f3f46;
        }

        body.theme-dark ::-webkit-scrollbar-thumb:hover {
            background: #52525b;
        }

        body.theme-dark .search-input:focus {
            background-color: var(--surface-elevated);
        }

        body.theme-dark .alert-success {
            background: rgba(16, 185, 129, 0.12) !important;
            border-color: #27272a !important;
            color: #6ee7b7 !important;
        }

        body.theme-dark .alert-danger {
            background: rgba(239, 68, 68, 0.12) !important;
            border-color: #27272a !important;
            color: #fca5a5 !important;
        }

        body.theme-dark .form-control {
            background-color: var(--surface-elevated);
            border-color: var(--border-subtle);
            color: var(--text-main);
        }

        body.theme-dark .form-control:focus {
            background-color: var(--surface);
            border-color: var(--primary);
            color: var(--text-main);
        }

        body.theme-dark .table {
            background: var(--surface-elevated) !important;
            color: var(--text-main);
            --bs-table-bg: var(--surface-elevated);
            --bs-table-color: var(--text-main);
            --bs-table-striped-bg: var(--surface);
            --bs-table-hover-bg: var(--surface);
            --bs-table-hover-color: var(--text-main);
            --bs-table-border-color: var(--border-subtle);
        }

        body.theme-dark .table thead {
            background-color: var(--surface);
            border-color: var(--border-subtle);
        }

        body.theme-dark .table thead th {
            color: var(--text-secondary);
            border-color: var(--border-subtle);
        }

        body.theme-dark .table td {
            color: var(--text-secondary);
            border-color: var(--border-subtle);
        }

        body.theme-dark .table-hover tbody tr:hover {
            background-color: var(--surface);
        }

        body.theme-dark .card {
            background: var(--surface-elevated);
            border-color: var(--border-subtle);
        }

        body.theme-dark .stat-card {
            background: var(--surface-elevated);
            border-color: var(--border-subtle);
        }

        body.theme-dark .dropdown-menu {
            background: var(--surface-elevated);
            border-color: var(--border-subtle);
        }

        body.theme-dark .dropdown-item {
            color: var(--text-secondary);
        }

        body.theme-dark .dropdown-item:hover {
            background-color: var(--surface);
            color: var(--text-main);
        }

        body.theme-dark .btn-outline-secondary {
            border-color: var(--border-subtle);
            color: var(--text-secondary);
            background: var(--surface);
        }

        body.theme-dark .btn-outline-secondary:hover {
            background: var(--surface-elevated);
            border-color: var(--primary-light);
            color: var(--primary);
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

            <div class="d-flex align-items-center gap-2">

                {{-- ── Theme Toggle ───────────────────────────────── --}}
                <button class="btn btn-outline-secondary btn-sm d-flex align-items-center justify-content-center"
                    id="theme-toggle" type="button" style="width: 38px; height: 32px; padding: 0;"
                    aria-label="Toggle dark mode">
                    <i class="bi bi-moon-stars" id="theme-toggle-icon" style="font-size: 16px;"></i>
                </button>

                {{-- ── Website Switcher (Client only) ─────────────── --}}
                @if (auth()->user()->isClient())
                    @php
                        $__userWebsites = auth()->user()->websites()->where('is_active', true)->get();
                        $__activeWebsite = auth()->user()->activeWebsite();
                        $__activeWebsiteId = session('active_website_id', 0);
                    @endphp
                    @if ($__userWebsites->count() > 0)
                        <div class="dropdown me-1">
                            <button class="btn btn-sm d-flex align-items-center gap-2 text-decoration-none"
                                type="button" id="websiteSwitcher" data-bs-toggle="dropdown" aria-expanded="false"
                                style="background: var(--surface-soft); border: 1px solid var(--border-subtle); border-radius: 10px; padding: 6px 14px; color: var(--text-main); font-weight: 600; font-size: 13px;">
                                <i class="bi bi-globe2" style="font-size: 15px; color: var(--primary);"></i>
                                <span id="active-website-label">
                                    {{ $__activeWebsite ? $__activeWebsite->name : 'All Websites' }}
                                </span>
                                <i class="bi bi-chevron-down" style="font-size: 10px; color: var(--text-muted);"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" style="min-width: 240px;"
                                aria-labelledby="websiteSwitcher">
                                <li>
                                    <div class="px-3 py-2"
                                        style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted);">
                                        Switch Website
                                    </div>
                                </li>
                                <li>
                                    <button
                                        class="dropdown-item d-flex align-items-center gap-2 website-switch-btn {{ !$__activeWebsiteId ? 'active' : '' }}"
                                        data-website-id="" style="font-size: 13px;">
                                        <i class="bi bi-grid-3x3-gap"
                                            style="color: var(--primary); font-size: 15px;"></i>
                                        <div class="flex-grow-1">
                                            <div style="font-weight: 600;">All Websites</div>
                                            <div style="font-size: 11px; color: var(--text-muted);">View data from all
                                                sites</div>
                                        </div>
                                        @if (!$__activeWebsiteId)
                                            <i class="bi bi-check-circle-fill"
                                                style="color: var(--primary); font-size: 14px;"></i>
                                        @endif
                                    </button>
                                </li>
                                <li>
                                    <hr class="dropdown-divider" style="border-color: var(--border-subtle);">
                                </li>
                                @foreach ($__userWebsites as $__ws)
                                    <li>
                                        <button
                                            class="dropdown-item d-flex align-items-center gap-2 website-switch-btn {{ $__activeWebsiteId == $__ws->id ? 'active' : '' }}"
                                            data-website-id="{{ $__ws->id }}" style="font-size: 13px;">
                                            <div
                                                style="width: 28px; height: 28px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; color: #fff; background: var(--primary-gradient); flex-shrink: 0;">
                                                {{ strtoupper(substr($__ws->name, 0, 1)) }}
                                            </div>
                                            <div class="flex-grow-1 overflow-hidden">
                                                <div style="font-weight: 600;">{{ $__ws->name }}</div>
                                                <div
                                                    style="font-size: 11px; color: var(--text-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                    {{ $__ws->domain }}</div>
                                            </div>
                                            @if ($__activeWebsiteId == $__ws->id)
                                                <i class="bi bi-check-circle-fill"
                                                    style="color: var(--primary); font-size: 14px;"></i>
                                            @endif
                                            <span class="badge website-unread-badge"
                                                data-website-unread-id="{{ $__ws->id }}"
                                                style="background: #ef4444; color: #fff; font-size: 10px; display: none;">0</span>
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                @endif

                {{-- ── Notification Bell (Client only) ────────────── --}}
                @if (auth()->user()->isClient())
                    <div class="dropdown me-1">
                        <button class="btn btn-link position-relative" type="button" id="notificationBell"
                            data-bs-toggle="dropdown" aria-expanded="false"
                            style="font-size: 20px; color: var(--text-secondary); padding: 4px 8px; border: none;">
                            <i class="bi bi-bell"></i>
                            <span id="notification-badge"
                                class="position-absolute translate-middle badge rounded-pill"
                                style="top: 4px; right: 0; font-size: 10px; background: #ef4444; color: #fff; display: none; min-width: 18px;">
                                0
                            </span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end p-0"
                            style="min-width: 360px; max-height: 440px; overflow: hidden; border-radius: 14px;"
                            aria-labelledby="notificationBell">
                            <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom"
                                style="border-color: var(--border-subtle) !important;">
                                <div>
                                    <div style="font-weight: 700; font-size: 14px; color: var(--text-main);">
                                        Notifications</div>
                                    <div id="notif-subtitle" style="font-size: 11px; color: var(--text-muted);">No new
                                        messages</div>
                                </div>
                                <button class="btn btn-sm" id="mark-all-read-btn"
                                    style="font-size: 12px; color: var(--primary); font-weight: 600; padding: 2px 8px; display: none;">
                                    Mark all read
                                </button>
                            </div>
                            <div id="notification-list" style="max-height: 340px; overflow-y: auto;">
                                <div class="px-3 py-4 text-center" style="color: var(--text-muted); font-size: 13px;">
                                    <i class="bi bi-bell-slash d-block mb-2" style="font-size: 28px;"></i>
                                    No new notifications
                                </div>
                            </div>
                            <div class="border-top px-3 py-2 text-center"
                                style="border-color: var(--border-subtle) !important;">
                                <a href="{{ route('client.conversations') }}" class="text-decoration-none"
                                    style="font-size: 12px; color: var(--primary); font-weight: 600;">
                                    View all conversations <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- ── Profile Dropdown ───────────────────────────── --}}
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
        const themeToggle = document.getElementById('theme-toggle');
        const themeToggleIcon = document.getElementById('theme-toggle-icon');

        // --- Theme handling ---
        function applyTheme(theme) {
            if (theme === 'dark') {
                document.body.classList.add('theme-dark');
                if (themeToggleIcon) {
                    themeToggleIcon.classList.remove('bi-moon-stars');
                    themeToggleIcon.classList.add('bi-sun');
                }
            } else {
                document.body.classList.remove('theme-dark');
                if (themeToggleIcon) {
                    themeToggleIcon.classList.remove('bi-sun');
                    themeToggleIcon.classList.add('bi-moon-stars');
                }
            }
        }

        (function initTheme() {
            const stored = window.localStorage.getItem('panel-theme');
            const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
            const theme = stored || (prefersDark ? 'dark' : 'light');
            applyTheme(theme);
        })();

        themeToggle?.addEventListener('click', () => {
            const isDark = document.body.classList.contains('theme-dark');
            const next = isDark ? 'light' : 'dark';
            applyTheme(next);
            try {
                window.localStorage.setItem('panel-theme', next);
            } catch (e) {}
        });

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

    {{-- ── Notification Toast Container ──────────────────── --}}
    <div id="notification-toast-container" aria-live="polite" aria-atomic="true"
        style="position: fixed; top: 80px; right: 20px; z-index: 9999; display: flex; flex-direction: column; gap: 10px; pointer-events: none;">
    </div>

    {{-- ── Website Switcher + Notifications + Sound JS ──── --}}
    @if (auth()->user()->isClient())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                const userId = {{ auth()->user()->id }};
                const switchUrl = "{{ route('client.switch-website') }}";
                const unreadCountUrl = "{{ route('client.notifications.unread-count') }}";
                const recentUrl = "{{ route('client.notifications.recent') }}";

                // ── Notification Sound (Web Audio API) ──────────────
                let audioCtx = null;
                let soundEnabled = true;

                function playNotificationSound() {
                    if (!soundEnabled) return;
                    try {
                        if (!audioCtx) audioCtx = new(window.AudioContext || window.webkitAudioContext)();

                        // Pleasant two-tone chime
                        const now = audioCtx.currentTime;
                        const gainNode = audioCtx.createGain();
                        gainNode.connect(audioCtx.destination);
                        gainNode.gain.setValueAtTime(0.3, now);
                        gainNode.gain.exponentialRampToValueAtTime(0.01, now + 0.6);

                        // First tone
                        const osc1 = audioCtx.createOscillator();
                        osc1.type = 'sine';
                        osc1.frequency.setValueAtTime(830, now);
                        osc1.connect(gainNode);
                        osc1.start(now);
                        osc1.stop(now + 0.15);

                        // Second tone (higher)
                        const gainNode2 = audioCtx.createGain();
                        gainNode2.connect(audioCtx.destination);
                        gainNode2.gain.setValueAtTime(0.25, now + 0.15);
                        gainNode2.gain.exponentialRampToValueAtTime(0.01, now + 0.7);

                        const osc2 = audioCtx.createOscillator();
                        osc2.type = 'sine';
                        osc2.frequency.setValueAtTime(1060, now + 0.15);
                        osc2.connect(gainNode2);
                        osc2.start(now + 0.15);
                        osc2.stop(now + 0.4);

                    } catch (e) {
                        console.warn('Sound failed:', e);
                    }
                }

                // Enable AudioContext on first user interaction (browser autoplay policy)
                document.addEventListener('click', function initAudio() {
                    if (!audioCtx) audioCtx = new(window.AudioContext || window.webkitAudioContext)();
                    if (audioCtx.state === 'suspended') audioCtx.resume();
                    document.removeEventListener('click', initAudio);
                }, {
                    once: true
                });

                // ── Website Switcher ────────────────────────────────
                document.querySelectorAll('.website-switch-btn').forEach(btn => {
                    btn.addEventListener('click', async function(e) {
                        e.preventDefault();
                        const websiteId = this.dataset.websiteId;

                        try {
                            const res = await fetch(switchUrl, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken
                                },
                                body: JSON.stringify({
                                    website_id: websiteId || null
                                })
                            });

                            if (res.ok) {
                                // Reload page to reflect filtered data
                                window.location.reload();
                            }
                        } catch (e) {
                            console.error('Switch error:', e);
                        }
                    });
                });

                // ── Notification Badge + Bell ───────────────────────
                const badge = document.getElementById('notification-badge');
                const notifList = document.getElementById('notification-list');
                const notifSubtitle = document.getElementById('notif-subtitle');
                const markAllBtn = document.getElementById('mark-all-read-btn');
                let currentUnreadCount = 0;

                function updateBadge(count) {
                    if (!badge) return;
                    if (count > 0) {
                        badge.textContent = count > 99 ? '99+' : count;
                        badge.style.display = '';
                        if (notifSubtitle) notifSubtitle.textContent = count + ' unread message' + (count > 1 ? 's' :
                            '');
                        if (markAllBtn) markAllBtn.style.display = '';
                    } else {
                        badge.style.display = 'none';
                        if (notifSubtitle) notifSubtitle.textContent = 'No new messages';
                        if (markAllBtn) markAllBtn.style.display = 'none';
                    }
                }

                function updateWebsiteUnreadBadges(perWebsite) {
                    document.querySelectorAll('.website-unread-badge').forEach(el => {
                        const wid = el.dataset.websiteUnreadId;
                        if (perWebsite[wid] && perWebsite[wid].count > 0) {
                            el.textContent = perWebsite[wid].count;
                            el.style.display = '';
                        } else {
                            el.style.display = 'none';
                        }
                    });
                }

                async function fetchUnreadCount() {
                    try {
                        const res = await fetch(unreadCountUrl, {
                            headers: {
                                'Accept': 'application/json'
                            }
                        });
                        const data = await res.json();
                        currentUnreadCount = data.total || 0;
                        updateBadge(currentUnreadCount);
                        updateWebsiteUnreadBadges(data.per_website || {});
                    } catch (e) {
                        console.error('Unread count error:', e);
                    }
                }

                async function fetchRecentNotifications() {
                    if (!notifList) return;
                    try {
                        const res = await fetch(recentUrl, {
                            headers: {
                                'Accept': 'application/json'
                            }
                        });
                        const items = await res.json();

                        if (!Array.isArray(items) || items.length === 0) {
                            notifList.innerHTML = `
                        <div class="px-3 py-4 text-center" style="color: var(--text-muted); font-size: 13px;">
                            <i class="bi bi-bell-slash d-block mb-2" style="font-size: 28px;"></i>
                            No new notifications
                        </div>`;
                            return;
                        }

                        notifList.innerHTML = items.map(item => `
                    <a href="/client/chat/${item.conversation_id}" class="d-flex align-items-start gap-3 px-3 py-2 text-decoration-none border-bottom" style="border-color: var(--border-subtle) !important; transition: background 0.15s;"
                        onmouseover="this.style.background='var(--surface-soft)'" onmouseout="this.style.background='transparent'">
                        <div style="width: 36px; height: 36px; border-radius: 50%; background: var(--primary-soft); display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px;">
                            <i class="bi bi-chat-dots" style="color: var(--primary); font-size: 14px;"></i>
                        </div>
                        <div class="flex-grow-1 overflow-hidden">
                            <div class="d-flex justify-content-between align-items-center">
                                <span style="font-weight: 600; font-size: 13px; color: var(--text-main);">${item.visitor_label}</span>
                                <span style="font-size: 11px; color: var(--text-muted); white-space: nowrap;">${item.time_human}</span>
                            </div>
                            <div style="font-size: 12px; color: var(--text-secondary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${item.message_preview}</div>
                            <div style="font-size: 11px; color: var(--primary); font-weight: 500; margin-top: 1px;">
                                <i class="bi bi-globe2" style="font-size: 10px;"></i> ${item.website_name}
                            </div>
                        </div>
                    </a>
                `).join('');
                    } catch (e) {
                        console.error('Notifications error:', e);
                    }
                }

                // Load on open
                const bellDropdown = document.getElementById('notificationBell');
                if (bellDropdown) {
                    bellDropdown.addEventListener('click', function() {
                        fetchRecentNotifications();
                    });
                }

                // ── Toast Notifications ─────────────────────────────
                const toastContainer = document.getElementById('notification-toast-container');
                let toastCounter = 0;

                function showNotificationToast(data) {
                    if (!toastContainer) return;
                    const id = 'toast-' + (++toastCounter);
                    const toast = document.createElement('div');
                    toast.id = id;
                    toast.style.cssText =
                        'pointer-events: auto; background: #fff; border: 1px solid var(--border-subtle); border-radius: 14px; box-shadow: 0 8px 32px rgba(0,0,0,0.12); padding: 14px 16px; min-width: 320px; max-width: 400px; animation: slideInRight 0.3s ease; cursor: pointer;';
                    toast.innerHTML = `
                <div class="d-flex align-items-start gap-3">
                    <div style="width: 38px; height: 38px; border-radius: 50%; background: linear-gradient(135deg, var(--primary), #ea580c); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="bi bi-chat-dots-fill" style="color: #fff; font-size: 16px;"></i>
                    </div>
                    <div class="flex-grow-1 overflow-hidden">
                        <div class="d-flex justify-content-between align-items-center">
                            <span style="font-weight: 700; font-size: 13px; color: var(--text-main);">New Message</span>
                            <button onclick="document.getElementById('${id}')?.remove()" class="btn-close" style="font-size: 10px; padding: 4px;"></button>
                        </div>
                        <div style="font-weight: 600; font-size: 12px; color: var(--text-main);">${data.visitor_label || 'Visitor'}</div>
                        <div style="font-size: 12px; color: var(--text-secondary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${data.message_preview || ''}</div>
                        <div style="font-size: 11px; color: var(--primary); font-weight: 500; margin-top: 2px;">
                            <i class="bi bi-globe2" style="font-size: 10px;"></i> ${data.website_name || 'Website'}
                        </div>
                    </div>
                </div>
            `;

                    // Click toast to go to conversation
                    toast.addEventListener('click', function(e) {
                        if (e.target.closest('.btn-close')) return;
                        if (data.conversation_id) {
                            window.location.href = '/client/chat/' + data.conversation_id;
                        }
                    });

                    toastContainer.appendChild(toast);

                    // Auto-remove after 8 seconds
                    setTimeout(() => {
                        toast.style.animation = 'slideOutRight 0.3s ease';
                        setTimeout(() => toast.remove(), 300);
                    }, 8000);
                }

                // ── Echo Real-time Listener ─────────────────────────
                if (window.Echo) {
                    try {
                        window.Echo.private('App.Models.User.' + userId)
                            .listen('.NewVisitorMessage', function(data) {
                                // Increment badge
                                currentUnreadCount++;
                                updateBadge(currentUnreadCount);

                                // Play sound
                                playNotificationSound();

                                // Show toast
                                showNotificationToast(data);

                                // Update title
                                document.title = '(' + currentUnreadCount + ') ' + document.title.replace(
                                    /^\(\d+\)\s*/, '');

                                // Update website unread badges (quick increment)
                                const wBadge = document.querySelector('[data-website-unread-id="' + data
                                    .website_id + '"]');
                                if (wBadge) {
                                    const cur = parseInt(wBadge.textContent) || 0;
                                    wBadge.textContent = cur + 1;
                                    wBadge.style.display = '';
                                }
                            });
                    } catch (e) {
                        console.error('Echo private channel error:', e);
                    }
                }

                // ── Polling fallback for notifications ──────────────
                // Even without WebSockets, poll every 15s for unread count
                fetchUnreadCount();
                setInterval(fetchUnreadCount, 15000);

                // ── Mark all read (navigates to conversations) ──────
                if (markAllBtn) {
                    markAllBtn.addEventListener('click', function() {
                        window.location.href = "{{ route('client.conversations') }}";
                    });
                }
            });
        </script>

        <style>
            @keyframes slideInRight {
                from {
                    opacity: 0;
                    transform: translateX(100px);
                }

                to {
                    opacity: 1;
                    transform: translateX(0);
                }
            }

            @keyframes slideOutRight {
                from {
                    opacity: 1;
                    transform: translateX(0);
                }

                to {
                    opacity: 0;
                    transform: translateX(100px);
                }
            }

            .website-switch-btn.active {
                background: var(--primary-soft) !important;
            }

            .website-switch-btn:hover {
                background: var(--surface-soft) !important;
            }

            #notification-badge {
                animation: badgePulse 2s infinite;
            }

            @keyframes badgePulse {

                0%,
                100% {
                    box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4);
                }

                50% {
                    box-shadow: 0 0 0 4px rgba(239, 68, 68, 0);
                }
            }
        </style>
    @endif

    @yield('scripts')
</body>

</html>
