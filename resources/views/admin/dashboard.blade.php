@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1" style="color: #1f2937; font-weight: 700;">Dashboard</h1>
            <p class="text-muted mb-0">Welcome back! Here's what's happening with your chatbot today.</p>
        </div>
        <div>
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
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h5 class="mb-0" style="color: #1f2937; font-weight: 600;">Recent Conversations</h5>
            <a href="#" style="color: #667eea; text-decoration: none; font-weight: 500;">
                View All <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        <!-- Responsive Table Wrapper -->
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="color: #6b7280;">Visitor Name</th>
                        <th style="color: #6b7280;">Last Message</th>
                        <th style="color: #6b7280;">Date & Time</th>
                        <th style="color: #6b7280;">Status</th>
                        <th style="color: #6b7280;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Conversation 1 -->
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="https://ui-avatars.com/api/?name=John+Smith&background=667eea&color=fff"
                                    alt="John" width="40" height="40" class="rounded-circle me-3">
                                <div>
                                    <div style="font-weight: 600; color: #1f2937;">John Smith</div>
                                    <small class="text-muted">john.smith@email.com</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="text-muted">Can you help me with my order?</span>
                        </td>
                        <td class="text-muted">Today, 2:45 PM</td>
                        <td>
                            <span class="badge bg-success">Active</span>
                        </td>
                        <td>
                            <a href="#" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> View
                            </a>
                        </td>
                    </tr>

                    <!-- Conversation 2 -->
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="https://ui-avatars.com/api/?name=Sarah+Johnson&background=764ba2&color=fff"
                                    alt="Sarah" width="40" height="40" class="rounded-circle me-3">
                                <div>
                                    <div style="font-weight: 600; color: #1f2937;">Sarah Johnson</div>
                                    <small class="text-muted">sarah.j@email.com</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="text-muted">Thanks for your help!</span>
                        </td>
                        <td class="text-muted">Today, 1:30 PM</td>
                        <td>
                            <span class="badge bg-secondary">Closed</span>
                        </td>
                        <td>
                            <a href="#" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> View
                            </a>
                        </td>
                    </tr>

                    <!-- Conversation 3 -->
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="https://ui-avatars.com/api/?name=Mike+Davis&background=f59e0b&color=fff"
                                    alt="Mike" width="40" height="40" class="rounded-circle me-3">
                                <div>
                                    <div style="font-weight: 600; color: #1f2937;">Mike Davis</div>
                                    <small class="text-muted">m.davis@email.com</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="text-muted">I need technical support</span>
                        </td>
                        <td class="text-muted">Today, 12:15 PM</td>
                        <td>
                            <span class="badge bg-success">Active</span>
                        </td>
                        <td>
                            <a href="#" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> View
                            </a>
                        </td>
                    </tr>

                    <!-- Conversation 4 -->
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="https://ui-avatars.com/api/?name=Emma+Wilson&background=10b981&color=fff"
                                    alt="Emma" width="40" height="40" class="rounded-circle me-3">
                                <div>
                                    <div style="font-weight: 600; color: #1f2937;">Emma Wilson</div>
                                    <small class="text-muted">emma.w@email.com</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="text-muted">Product inquiry</span>
                        </td>
                        <td class="text-muted">Yesterday, 4:20 PM</td>
                        <td>
                            <span class="badge bg-secondary">Closed</span>
                        </td>
                        <td>
                            <a href="#" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> View
                            </a>
                        </td>
                    </tr>

                    <!-- Conversation 5 -->
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="https://ui-avatars.com/api/?name=James+Brown&background=ef4444&color=fff"
                                    alt="James" width="40" height="40" class="rounded-circle me-3">
                                <div>
                                    <div style="font-weight: 600; color: #1f2937;">James Brown</div>
                                    <small class="text-muted">james.b@email.com</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="text-muted">Billing issue</span>
                        </td>
                        <td class="text-muted">Yesterday, 10:00 AM</td>
                        <td>
                            <span class="badge bg-success">Active</span>
                        </td>
                        <td>
                            <a href="#" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> View
                            </a>
                        </td>
                    </tr>

                    <!-- Conversation 6 -->
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="https://ui-avatars.com/api/?name=Lisa+Taylor&background=0ea5e9&color=fff"
                                    alt="Lisa" width="40" height="40" class="rounded-circle me-3">
                                <div>
                                    <div style="font-weight: 600; color: #1f2937;">Lisa Taylor</div>
                                    <small class="text-muted">lisa.t@email.com</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="text-muted">Feedback</span>
                        </td>
                        <td class="text-muted">Feb 18, 3:45 PM</td>
                        <td>
                            <span class="badge bg-secondary">Closed</span>
                        </td>
                        <td>
                            <a href="#" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> View
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex align-items-center justify-content-between mt-4"
            style="border-top: 1px solid #e5e7eb; padding-top: 20px;">
            <small class="text-muted">Showing 1-6 of 1,248 conversations</small>
            <nav>
                <ul class="pagination mb-0">
                    <li class="page-item disabled">
                        <a class="page-link" href="#" tabindex="-1"><i class="bi bi-chevron-left"></i></a>
                    </li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item">
                        <a class="page-link" href="#"><i class="bi bi-chevron-right"></i></a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
@endsection
