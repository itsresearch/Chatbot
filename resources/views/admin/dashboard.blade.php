@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1" style="color: #111827; font-weight: 700;">Chatbot Overview</h1>
            <p class="text-muted mb-0">Clean, messaging‑style view of what your assistant is doing.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-sliders2 me-1"></i>Filters
            </button>
            <button class="btn btn-primary">
                <i class="bi bi-download me-2"></i>Export Report
            </button>
        </div>
    </div>

    <!-- Statistics Cards Row -->
    <div class="row mb-4">
        <!-- Total Conversations Card -->
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div style="flex: 1;">
                    <div class="stat-number">1,248</div>
                    <div class="stat-label">Total Conversations</div>
                </div>
                <div class="stat-icon bg-primary-light">
                    <i class="bi bi-chat-dots"></i>
                </div>
            </div>
        </div>

        <!-- Active Chats Card -->
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div style="flex: 1;">
                    <div class="stat-number">24</div>
                    <div class="stat-label">Active Chats</div>
                </div>
                <div class="stat-icon bg-success-light">
                    <i class="bi bi-circle-fill"></i>
                </div>
            </div>
        </div>

        <!-- Closed Chats Card -->
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div style="flex: 1;">
                    <div class="stat-number">892</div>
                    <div class="stat-label">Closed Chats</div>
                </div>
                <div class="stat-icon bg-danger-light">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
            </div>
        </div>

        <!-- Visitors Today Card -->
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div style="flex: 1;">
                    <div class="stat-number">632</div>
                    <div class="stat-label">Visitors Today</div>
                </div>
                <div class="stat-icon bg-warning-light">
                    <i class="bi bi-people"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Conversations Section -->
    <div class="card">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h5 class="mb-0" style="color: #111827; font-weight: 600;">Recent conversations</h5>
            <a href="{{ route('admin.conversations') }}"
                style="color: var(--primary); text-decoration: none; font-weight: 500; font-size: 0.9rem;">
                View all <i class="bi bi-arrow-right-short"></i>
            </a>
        </div>

        <div class="chat-list">
            <!-- Example conversation rows (replace with dynamic data later if needed) -->
            <div class="chat-item active">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div
                            style="width: 40px; height: 40px; border-radius: 999px; background: var(--primary-soft); display:flex; align-items:center; justify-content:center; margin-right:12px;">
                            <i class="bi bi-person-fill" style="color: var(--primary);"></i>
                        </div>
                        <div>
                            <div class="chat-item-name">John Smith</div>
                            <div class="chat-item-message">Can you help me with my order?</div>
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="chat-item-time">Today · 2:45 PM</div>
                        <span class="badge bg-success">Active</span>
                    </div>
                </div>
            </div>

            <div class="chat-item">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div
                            style="width: 40px; height: 40px; border-radius: 999px; background: #fef2f2; display:flex; align-items:center; justify-content:center; margin-right:12px;">
                            <i class="bi bi-person-fill" style="color: #ef4444;"></i>
                        </div>
                        <div>
                            <div class="chat-item-name">Sarah Johnson</div>
                            <div class="chat-item-message">Thanks for your help!</div>
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="chat-item-time">Today · 1:30 PM</div>
                        <span class="badge bg-secondary">Closed</span>
                    </div>
                </div>
            </div>

            <div class="chat-item">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div
                            style="width: 40px; height: 40px; border-radius: 999px; background: #ecfeff; display:flex; align-items:center; justify-content:center; margin-right:12px;">
                            <i class="bi bi-person-fill" style="color: #0ea5e9;"></i>
                        </div>
                        <div>
                            <div class="chat-item-name">Mike Davis</div>
                            <div class="chat-item-message">I need technical support.</div>
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="chat-item-time">Today · 12:15 PM</div>
                        <span class="badge bg-success">Active</span>
                    </div>
                </div>
            </div>

            <div class="chat-item">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div
                            style="width: 40px; height: 40px; border-radius: 999px; background: #ecfdf5; display:flex; align-items:center; justify-content:center; margin-right:12px;">
                            <i class="bi bi-person-fill" style="color: #10b981;"></i>
                        </div>
                        <div>
                            <div class="chat-item-name">Emma Wilson</div>
                            <div class="chat-item-message">Product inquiry about pricing.</div>
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="chat-item-time">Yesterday · 4:20 PM</div>
                        <span class="badge bg-secondary">Closed</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
