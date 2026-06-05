<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Cổng Quản Trị') - V2T Portal</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    
    <style>
        body {
            background-color: #f4f1ea;
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
        .admin-sidebar {
            width: 260px;
            background-color: #ffffff;
            border-right: 1px solid #e5e7eb;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 24px 16px;
            z-index: 40;
        }
        .admin-main-wrapper {
            margin-left: 260px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .admin-top-navbar {
            height: 64px;
            background-color: #ffffff;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 32px;
            position: sticky;
            top: 0;
            z-index: 30;
        }
        .admin-content-body {
            padding: 32px;
            flex: 1;
        }
        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 600;
            color: #4b5563;
            text-decoration: none;
            transition: all 0.2s;
        }
        .sidebar-link:hover {
            background-color: #f3f4f6;
            color: #111827;
        }
        .sidebar-link-active {
            background-color: #1e3e36 !important;
            color: #ffffff !important;
        }
    </style>
</head>
<body>

    <div class="admin-sidebar">
        <div>
            <div style="padding-bottom: 20px; border-bottom: 1px solid #f3f4f6; margin-bottom: 24px;">
                <h2 style="font-family: Georgia, serif; font-size: 1.3rem; font-weight: 700; color: #1e3e36; margin: 0;">V2T Portal</h2>
                <p style="font-size: 11px; color: #9ca3af; margin: 2px 0 0 0; font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em;">HỆ THỐNG KIỂM SOÁT KHO</p>
            </div>
            
            <nav style="display: flex; flex-direction: column; gap: 6px;">
                <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'sidebar-link-active' : '' }}">
                    Bảng điều khiển
                </a>
                <a href="/admin/books" class="sidebar-link {{ request()->is('admin/books*') ? 'sidebar-link-active' : '' }}">
                    Quản lý sách
                </a>
                <a href="/admin/orders" class="sidebar-link {{ request()->is('admin/orders*') ? 'sidebar-link-active' : '' }}">
                    Quản lý đơn hàng
                </a>
                <a href="/admin/users" class="sidebar-link {{ request()->is('admin/users*') ? 'sidebar-link-active' : '' }}">
                    Người dùng
                </a>
                <a href="{{ route('admin.suppliers.index') }}" class="sidebar-link {{ request()->is('admin/suppliers*') ? 'sidebar-link-active' : '' }}">
                    Quản lý nhà cung cấp
                </a>
                
                <a href="{{ route('admin.reviews.index') }}" class="sidebar-link {{ request()->routeIs('admin.reviews.*') ? 'sidebar-link-active' : '' }}">
                    Quản lý đánh giá
                </a>

                <a href="{{ route('admin.vouchers.index') }}" class="sidebar-link {{ request()->routeIs('admin.vouchers.*') ? 'sidebar-link-active' : '' }}">
                    Quản lý Mã giảm giá
                </a>

            </nav>
        </div>

        <div style="border-top: 1px solid #f3f4f6; padding-top: 16px;">
            <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                @csrf
                <button type="submit" class="sidebar-link" style="width: 100%; text-align: left; border: none; background: transparent; cursor: pointer; color: #dc2626;">
                    Đăng xuất quản trị
                </button>
            </form>
        </div>
    </div>

    <div class="admin-main-wrapper">
        <div class="admin-top-navbar shadow-sm">
            <form action="{{ route('admin.orders.index') }}" method="GET" style="margin: 0; display: flex; align-items: center; gap: 8px;">
                <div style="position: relative; display: flex; align-items: center;">
                    
                    <span style="position: absolute; left: 12px; color: #9ca3af; display: flex; align-items: center; pointer-events: none;">
                        <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    
                    <input type="text" 
                        name="search" 
                        value="{{ request('search') }}" 
                        placeholder="Tìm theo mã đơn hoặc tên khách..." 
                        style="width: 280px; padding: 8px 12px 8px 36px; border: 1px solid #e5e7eb; border-radius: 8px 0 0 8px; font-size: 0.875rem; outline: none; background-color: #f9fafb; border-right: none; transition: all 0.2s;">
                    
                    <button type="submit" 
                            style="padding: 8px 16px; background-color: #1e3e36; color: white; border: 1px solid #1e3e36; border-radius: 0 8px 8px 0; font-size: 0.875rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px; transition: background-color 0.2s;"
                            onmouseover="this.style.backgroundColor='#142b25'" 
                            onmouseout="this.style.backgroundColor='#1e3e36'">
                        <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Tìm
                    </button>
                </div>

                @if(request()->filled('search'))
                    <a href="{{ route('admin.orders.index') }}" style="text-decoration: none; color: #dc2626; font-size: 0.85rem; font-weight: 600; padding: 8px 14px; background: #fee2e2; border-radius: 8px; transition: background 0.2s;" onmouseover="this.style.background='#fca5a5'" onmouseout="this.style.background='#fee2e2'">
                        Hủy lọc
                    </a>
                @endif
            </form>
            
            <div style="display: flex; align-items: center; gap: 10px;">
                <div style="width: 32px; height: 32px; border-radius: 50%; background-color: #e5e7eb; display: flex; justify-content: center; align-items: center; font-weight: 700; color: #1e3e36;">
                    {{ mb_substr(Auth::user()->name, 0, 1) }}
                </div>
                <span style="font-size: 0.875rem; font-weight: 700; color: #374151;">{{ Auth::user()->name }} (Admin)</span>
            </div>
        </div>

        <div class="admin-content-body">
            @yield('content')
        </div>
    </div>

</body>
</html>