@extends('layouts.admin')

@section('title', 'Conversations')

@section('content')
    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1" style="color: #1f2937; font-weight: 700;">Conversations</h1>
            <p class="text-muted mb-0">Manage all visitor conversations in one place.</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary">
                <i class="bi bi-funnel me-2"></i>Filter
            </button>
            <button class="btn btn-primary">
                <i class="bi bi-download me-2"></i>Export
            </button>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="card mb-4">
        <div class="row g-3">
            <!-- Search by Name -->
            <div class="col-md-6 col-lg-3">
                <label class="form-label" style="color: #6b7280; font-weight: 500; font-size: 13px;">Search Visitor</label>
                <div class="input-group">
                    <span class="input-group-text search-input" style="border: none;">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" class="form-control search-input" placeholder="Name or email..."
                        style="border: none;">
                </div>
            </div>

            <!-- Filter by Status -->
            <div class="col-md-6 col-lg-3">
                <label class="form-label" style="color: #6b7280; font-weight: 500; font-size: 13px;">Status</label>
                <select class="form-select search-input" style="border: none;">
                    <option selected>All Status</option>
                    <option>Active</option>
                    <option>Closed</option>
                    <option>Pending</option>
                </select>
            </div>

            <!-- Filter by Date -->
            <div class="col-md-6 col-lg-3">
                <label class="form-label" style="color: #6b7280; font-weight: 500; font-size: 13px;">Date Range</label>
                <select class="form-select search-input" style="border: none;">
                    <option selected>Last 7 Days</option>
                    <option>Last 30 Days</option>
                    <option>Last 90 Days</option>
                    <option>All Time</option>
                </select>
            </div>

            <!-- Filter by Chat Type -->
            <div class="col-md-6 col-lg-3">
                <label class="form-label" style="color: #6b7280; font-weight: 500; font-size: 13px;">Sort By</label>
                <select class="form-select search-input" style="border: none;">
                    <option selected>Newest First</option>
                    <option>Oldest First</option>
                    <option>Most Active</option>
                    <option>Least Active</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Conversations Table -->
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="color: #6b7280;">
                            <input class="form-check-input" type="checkbox">
                        </th>
                        <th style="color: #6b7280;">Visitor Name</th>
                        <th style="color: #6b7280;">Email</th>
                        <th style="color: #6b7280;">Last Message</th>
                        <th style="color: #6b7280;">Date</th>
                        <th style="color: #6b7280;">Status</th>
                        <th style="color: #6b7280;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Row 1 -->
                    <tr>
                        <td><input class="form-check-input" type="checkbox"></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="https://ui-avatars.com/api/?name=John+Smith&background=667eea&color=fff"
                                    alt="John" width="40" height="40" class="rounded-circle me-3">
                                <div style="font-weight: 600; color: #1f2937;">John Smith</div>
                            </div>
                        </td>
                        <td class="text-muted">john.smith@email.com</td>
                        <td class="text-muted"
                            style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">Can
                            you help me track my order status?</td>
                        <td class="text-muted">Today, 2:45 PM</td>
                        <td><span class="badge bg-success">Active</span></td>
                        <td>
                            <a href="#" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-chat-dots"></i> View Chat
                            </a>
                        </td>
                    </tr>

                    <!-- Row 2 -->
                    <tr>
                        <td><input class="form-check-input" type="checkbox"></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="https://ui-avatars.com/api/?name=Sarah+Johnson&background=764ba2&color=fff"
                                    alt="Sarah" width="40" height="40" class="rounded-circle me-3">
                                <div style="font-weight: 600; color: #1f2937;">Sarah Johnson</div>
                            </div>
                        </td>
                        <td class="text-muted">sarah.j@email.com</td>
                        <td class="text-muted"
                            style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">Thank
                            you for resolving the issue!</td>
                        <td class="text-muted">Today, 1:30 PM</td>
                        <td><span class="badge bg-secondary">Closed</span></td>
                        <td>
                            <a href="#" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-chat-dots"></i> View Chat
                            </a>
                        </td>
                    </tr>

                    <!-- Row 3 -->
                    <tr>
                        <td><input class="form-check-input" type="checkbox"></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="https://ui-avatars.com/api/?name=Mike+Davis&background=f59e0b&color=fff"
                                    alt="Mike" width="40" height="40" class="rounded-circle me-3">
                                <div style="font-weight: 600; color: #1f2937;">Mike Davis</div>
                            </div>
                        </td>
                        <td class="text-muted">m.davis@email.com</td>
                        <td class="text-muted"
                            style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">I need
                            technical support with login</td>
                        <td class="text-muted">Today, 12:15 PM</td>
                        <td><span class="badge bg-success">Active</span></td>
                        <td>
                            <a href="#" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-chat-dots"></i> View Chat
                            </a>
                        </td>
                    </tr>

                    <!-- Row 4 -->
                    <tr>
                        <td><input class="form-check-input" type="checkbox"></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="https://ui-avatars.com/api/?name=Emma+Wilson&background=10b981&color=fff"
                                    alt="Emma" width="40" height="40" class="rounded-circle me-3">
                                <div style="font-weight: 600; color: #1f2937;">Emma Wilson</div>
                            </div>
                        </td>
                        <td class="text-muted">emma.w@email.com</td>
                        <td class="text-muted"
                            style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">Do
                            you have this product in blue?</td>
                        <td class="text-muted">Yesterday, 4:20 PM</td>
                        <td><span class="badge bg-secondary">Closed</span></td>
                        <td>
                            <a href="#" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-chat-dots"></i> View Chat
                            </a>
                        </td>
                    </tr>

                    <!-- Row 5 -->
                    <tr>
                        <td><input class="form-check-input" type="checkbox"></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="https://ui-avatars.com/api/?name=James+Brown&background=ef4444&color=fff"
                                    alt="James" width="40" height="40" class="rounded-circle me-3">
                                <div style="font-weight: 600; color: #1f2937;">James Brown</div>
                            </div>
                        </td>
                        <td class="text-muted">james.b@email.com</td>
                        <td class="text-muted"
                            style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            Billing inquiry for enterprise plan</td>
                        <td class="text-muted">Yesterday, 10:00 AM</td>
                        <td><span class="badge bg-success">Active</span></td>
                        <td>
                            <a href="#" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-chat-dots"></i> View Chat
                            </a>
                        </td>
                    </tr>

                    <!-- Row 6 -->
                    <tr>
                        <td><input class="form-check-input" type="checkbox"></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="https://ui-avatars.com/api/?name=Lisa+Taylor&background=0ea5e9&color=fff"
                                    alt="Lisa" width="40" height="40" class="rounded-circle me-3">
                                <div style="font-weight: 600; color: #1f2937;">Lisa Taylor</div>
                            </div>
                        </td>
                        <td class="text-muted">lisa.t@email.com</td>
                        <td class="text-muted"
                            style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            Feedback about your service</td>
                        <td class="text-muted">Feb 18, 3:45 PM</td>
                        <td><span class="badge bg-secondary">Closed</span></td>
                        <td>
                            <a href="#" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-chat-dots"></i> View Chat
                            </a>
                        </td>
                    </tr>

                    <!-- Row 7 -->
                    <tr>
                        <td><input class="form-check-input" type="checkbox"></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="https://ui-avatars.com/api/?name=David+Miller&background=8b5cf6&color=fff"
                                    alt="David" width="40" height="40" class="rounded-circle me-3">
                                <div style="font-weight: 600; color: #1f2937;">David Miller</div>
                            </div>
                        </td>
                        <td class="text-muted">david.m@email.com</td>
                        <td class="text-muted"
                            style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            Return and refund process question</td>
                        <td class="text-muted">Feb 17, 11:30 AM</td>
                        <td><span class="badge bg-secondary">Closed</span></td>
                        <td>
                            <a href="#" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-chat-dots"></i> View Chat
                            </a>
                        </td>
                    </tr>

                    <!-- Row 8 -->
                    <tr>
                        <td><input class="form-check-input" type="checkbox"></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="https://ui-avatars.com/api/?name=Rachel+Garcia&background=ec4899&color=fff"
                                    alt="Rachel" width="40" height="40" class="rounded-circle me-3">
                                <div style="font-weight: 600; color: #1f2937;">Rachel Garcia</div>
                            </div>
                        </td>
                        <td class="text-muted">rachel.g@email.com</td>
                        <td class="text-muted"
                            style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            Integration with third-party tools</td>
                        <td class="text-muted">Feb 16, 9:20 AM</td>
                        <td><span class="badge bg-success">Active</span></td>
                        <td>
                            <a href="#" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-chat-dots"></i> View Chat
                            </a>
                        </td>
                    </tr>

                    <!-- Row 9 -->
                    <tr>
                        <td><input class="form-check-input" type="checkbox"></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="https://ui-avatars.com/api/?name=Andrew+Martin&background=14b8a6&color=fff"
                                    alt="Andrew" width="40" height="40" class="rounded-circle me-3">
                                <div style="font-weight: 600; color: #1f2937;">Andrew Martin</div>
                            </div>
                        </td>
                        <td class="text-muted">andrew.m@email.com</td>
                        <td class="text-muted"
                            style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            Pricing and plan comparison</td>
                        <td class="text-muted">Feb 15, 2:10 PM</td>
                        <td><span class="badge bg-secondary">Closed</span></td>
                        <td>
                            <a href="#" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-chat-dots"></i> View Chat
                            </a>
                        </td>
                    </tr>

                    <!-- Row 10 -->
                    <tr>
                        <td><input class="form-check-input" type="checkbox"></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="https://ui-avatars.com/api/?name=Katherine+Lee&background=06b6d4&color=fff"
                                    alt="Katherine" width="40" height="40" class="rounded-circle me-3">
                                <div style="font-weight: 600; color: #1f2937;">Katherine Lee</div>
                            </div>
                        </td>
                        <td class="text-muted">katherine.l@email.com</td>
                        <td class="text-muted"
                            style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">API
                            documentation request</td>
                        <td class="text-muted">Feb 14, 5:30 PM</td>
                        <td><span class="badge bg-success">Active</span></td>
                        <td>
                            <a href="#" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-chat-dots"></i> View Chat
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex align-items-center justify-content-between mt-4"
            style="border-top: 1px solid #e5e7eb; padding-top: 20px;">
            <small class="text-muted">Showing 1-10 of 1,248 conversations</small>
            <nav>
                <ul class="pagination mb-0">
                    <li class="page-item disabled">
                        <a class="page-link" href="#" tabindex="-1"><i class="bi bi-chevron-left"></i></a>
                    </li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item"><a class="page-link" href="#">4</a></li>
                    <li class="page-item"><a class="page-link" href="#">5</a></li>
                    <li class="page-item">
                        <a class="page-link" href="#"><i class="bi bi-chevron-right"></i></a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
@endsection
