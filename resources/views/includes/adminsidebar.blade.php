
@if(Auth::user()->role === 'admin')
    <div id="adminSidebar" class="sidebar-expanded">
        <div class="sidebar-header">
            <button id="toggleSidebarBtn">&#9776;</button>
        </div>
        <nav class="sidebar-navigation">
            <a href="{{ route('admin.dashboard') }}" class="sidebar-link">
                <span class="sidebar-icon">🏠</span>
                <span class="sidebar-text">Dashboard</span>
            </a>
            <a href="{{ route('products.create') }}" class="sidebar-link">
                <span class="sidebar-icon">📦</span>
                <span class="sidebar-text">Products</span>
            </a>
            <a href="{{ route('categories.create') }}" class="sidebar-link">
                <span class="sidebar-icon">🗂️</span>
                <span class="sidebar-text">Categories</span>
            </a>
            <a href="{{route('admin.sales')}}" class="sidebar-link">
                <span class="sidebar-icon">💰</span>
                <span class="sidebar-text">Sales</span>
            </a>
            <a href="{{route('shops.create')}}" class="sidebar-link">
                <span class="sidebar-icon">🏬</span>
                <span class="sidebar-text">Shops</span>
            </a>
            <a href="{{route('stock-transfers.create')}}" class="sidebar-link">
                <span class="sidebar-icon">🏢</span>
                <span class="sidebar-text">Stock Transfer</span>
            </a>       
            <a href="{{ route('user.notifications') }}" class="sidebar-link">
                <span class="sidebar-icon notify"><span class="badge bg-danger">{{ $unreadNotificationCount }}</span>🔔</span>
                <span class="sidebar-text">
                    Notification
                    @if(isset($unreadNotificationCount) && $unreadNotificationCount > 0)
                        {{-- <span class="badge bg-danger">{{ $unreadNotificationCount }}</span> --}}
                    @endif
                </span>
            </a>   
            <a href="{{route('admin.manager-permissions')}}" class="sidebar-link">
                <span class="sidebar-icon">🛡️</span>
                <span class="sidebar-text">Permissions</span>
            </a>         
            <button class="sidebar-link collapsible-btn" onclick="toggleSubmenu(this)">
                <span class="sidebar-icon">👥</span>
                <span class="sidebar-text">Users</span>
                <span class="arrow">&#9662;</span> <!-- down arrow -->
            </button>
            <div class="submenu">
                <a href="{{ route('admin.manage_roles') }}" class="submenu-link">
                    <span class="sidebar-icon">🛠️</span>
                    <span class="sidebar-text">Manage Users</span>
                </a>
                <a href="{{ route('admin.register') }}" class="submenu-link">
                    <span class="sidebar-icon">➕</span>
                    <span class="sidebar-text">Add Staff</span>
                </a>
            </div>
            <a href="{{ route('admin.profile') }}" class="sidebar-link">
                <span class="sidebar-icon">🧑‍💼</span>
                <span class="sidebar-text">Profile</span>
            </a>
            <a id="navbarDropdown" class="sidebar-link" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                <span class="sidebar-icon">👤</span> <!-- Profile Icon -->
                <span class="sidebar-text">{{ Auth::user()->name }}</span> <!-- User's name -->
            </a>
            
            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                <a class="dropdown-item logout-item" href="{{ route('logout') }}"
                   onclick="event.preventDefault();
                             document.getElementById('logout-form').submit();">
                    <span class="sidebar-icon">🚪</span> <!-- Logout icon -->
                    <span class="sidebar-text">{{ __('Logout') }}</span>
                </a>
            
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>            
        </nav>
    </div>
@endif

