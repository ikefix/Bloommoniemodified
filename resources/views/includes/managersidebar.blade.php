@if(Auth::user()->role === 'manager')

    <style>
        .bx{
            font-size: 1.5rem;
        }
    </style>

    <div id="adminSidebar" class="sidebar-expanded">
        <div class="sidebar-header">
            <img id="sidebarLogo" src="{{ asset('logobloomp.png') }}" alt="BloomMonie Dashboard" width="30px">
            <button id="toggleSidebarBtn">&#9776;</button>
        </div>
        <nav class="sidebar-navigation">
            <a href="{{url("/manager-dashboard")}}" class="sidebar-link">
                <span class="sidebar-icon"><i class='bx bxs-dashboard bx-tada' ></i></span>
                <span class="sidebar-text">Dashboard</span>
            </a>

            <a href="{{ route('manager.product') }}" class="sidebar-link">
                <span class="sidebar-icon"><i class='bx bx-package'></i></span>
                <span class="sidebar-text">Products</span>
            </a>

            {{-- Check if manager has product access --}}
            {{-- @php
                $hasProductAccess = \App\Models\ProductPermission::where('manager_id', Auth::user()->id)->exists();
            @endphp

            @if($hasProductAccess)
                <a href="{{ route('manager.product') }}" class="sidebar-link">
                    <span class="sidebar-icon">📦</span>
                    <span class="sidebar-text">Products</span>
                </a>
            @endif --}}

            {{-- <a href="{{ route('categories.create') }}" class="sidebar-link">
                <span class="sidebar-icon">🗂️</span>
                <span class="sidebar-text">Categories</span>
            </a> --}}

            <a href="{{url("manager/sales")}}" class="sidebar-link">
                <span class="sidebar-icon"><i class='bx bx-receipt'></i></span>
                <span class="sidebar-text">Sales</span>
            </a>
            <a href="{{ route('managerexpense.create') }}" class="sidebar-link">
                <span class="sidebar-icon"><i class='bx bx-purchase-tag'></i></span>
                <span class="sidebar-text">Create Expense</span>
            </a>
            <a href="{{ route('managerexpense.index') }}" class="sidebar-link">
                <span class="sidebar-icon"><i class='bx bxs-discount'></i></span>
                <span class="sidebar-text">View Expenses</span>
            </a>
            <a href="{{ route('manager.customers.index') }}" class="sidebar-link">
                <span class="sidebar-icon"><i class='bx bxs-discount'></i></span>
                <span class="sidebar-text">C M</span>
            </a>
            <a href="{{ route('manager.barcode') }}" class="sidebar-link">
                <span class="sidebar-icon"><i class='bx bx-barcode-reader'></i></span>
                <span class="sidebar-text">Barcode Manager</span>
            </a>
            <a href="{{ route('user.notifications') }}" class="sidebar-link">
                <span class="sidebar-icon"><i class='bx bx-bell'></i></span>
                <span class="sidebar-text">
                    Notification
                    @if(isset($unreadNotificationCount) && $unreadNotificationCount > 0)
                        <span class="badge bg-danger">{{ $unreadNotificationCount }}</span>
                    @endif
                </span>
            </a>            
            <button class="sidebar-link collapsible-btn" onclick="toggleSubmenu(this)">
                <span class="sidebar-icon"><i class='bx bx-user'></i></span>
                <span class="sidebar-text">Users</span>
                <span class="arrow">&#9662;</span> <!-- down arrow -->
            </button>
            <div class="submenu">
                <a href="{{ route('manager.manage_role') }}" class="sidebar-link">
                    <span class="sidebar-icon"><i class='bx bx-user-check'></i></span>
                    <span class="sidebar-text">Manage Users</span>
                </a>
                <a href="{{ route('manager.register') }}" class="sidebar-link">
                    <span class="sidebar-icon"><i class='bx bx-user-plus'></i></span>
                    <span class="sidebar-text">Add Staff</span>
                </a>
            </div>
            <a href="{{ route('manager.profile') }}" class="sidebar-link">
                <span class="sidebar-icon"><i class='bx bx-user-circle'></i></span>
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
                    <span class="sidebar-icon"><i class='bx bx-door-open'></i></span> <!-- Logout icon -->
                    <span class="sidebar-text">{{ __('Logout') }}</span>
                </a>
            
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>            
        </nav>
    </div>
@endif
