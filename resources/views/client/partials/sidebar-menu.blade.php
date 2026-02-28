{{-- Client sidebar menu partial (DRY - reused across all client views) --}}
<li class="menu-label">Client Panel</li>
<li>
    <a href="{{ route('client.dashboard') }}" class="{{ request()->routeIs('client.dashboard') ? 'active' : '' }}">
        <i class="bi bi-grid-1x2-fill"></i>
        <span class="sidebar-text">Dashboard</span>
    </a>
</li>
<li>
    <a href="{{ route('client.websites.index') }}" class="{{ request()->routeIs('client.websites.*') ? 'active' : '' }}">
        <i class="bi bi-globe"></i>
        <span class="sidebar-text">Websites</span>
    </a>
</li>
<li>
    <a href="{{ route('client.conversations') }}"
        class="{{ request()->routeIs('client.conversations') || request()->routeIs('client.chat') ? 'active' : '' }}">
        <i class="bi bi-chat-square-text-fill"></i>
        <span class="sidebar-text">Conversations</span>
    </a>
</li>
