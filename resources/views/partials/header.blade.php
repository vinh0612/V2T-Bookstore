<header class="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm">
    <div class="container mx-auto px-4 flex items-center justify-between gap-4 md:gap-8 h-16">
        
        <a href="/" class="text-3xl font-serif font-bold text-v2t-green shrink-0" style="text-decoration: none;">
            V2T Bookstore
        </a>

        <div class="hidden lg:flex items-center h-full group cursor-pointer">
            <div class="flex items-center gap-2 text-gray-600 group-hover:text-v2t-green transition">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                <svg class="w-4 h-4 text-gray-400 group-hover:text-v2t-green" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </div>

            <div class="absolute left-0 top-full w-full bg-white border-t border-gray-200 shadow-2xl opacity-0 invisible translate-y-2 group-hover:opacity-100 group-hover:visible group-hover:translate-y-0 transition-all duration-300 ease-in-out z-50">
                <div class="container mx-auto flex relative" style="height: 460px;">
                    
                    <div class="w-1/4 border-r border-gray-100 py-6 pr-4 bg-gray-50/50 h-full">
                        <h3 class="font-bold text-lg mb-4 px-4 text-gray-800">Danh mục sản phẩm</h3>
                        <ul class="text-sm font-medium text-gray-600 space-y-0.5" style="list-style: none; padding: 0; margin: 0;">
                            @foreach($megamenuCategories as $index => $parentCat)
                            <li class="category-parent-item px-4 py-2.5 rounded-lg cursor-pointer flex justify-between items-center transition duration-150 {{ $index === 0 ? 'active' : '' }}">
                                <span class="truncate font-semibold">{{ $parentCat->name }}</span>
                                <span class="text-lg leading-none opacity-60">›</span>
                                
                                <div class="subcategory-right-panel absolute right-0 top-0 w-3/4 h-full py-6 bg-white pl-8 pr-4 overflow-y-auto hidden z-20 border-l border-gray-150">
                                    <div class="flex justify-between items-center mb-6 border-b border-gray-100 pb-3">
                                        <div class="flex items-center gap-2">
                                            <span class="bg-v2t-green text-white w-6 h-6 rounded flex items-center justify-center text-xs font-mono font-bold">
                                                {{ sprintf("%02d", $index + 1) }}
                                            </span>
                                            <h3 class="font-bold text-xl text-gray-800">{{ $parentCat->name }}</h3>
                                        </div>
                                        <a href="{{ url('/shop?category=' . $parentCat->id) }}" class="text-xs font-bold text-v2t-green hover:underline" style="text-decoration: none;">Xem toàn bộ kho sách &rarr;</a>
                                    </div>
                                    
                                    <div class="grid grid-cols-4 gap-x-6 gap-y-8">
                                        @foreach($parentCat->children as $subCat)
                                        <div>
                                            <h4 class="font-bold text-sm text-gray-900 mb-3 uppercase tracking-wider hover:text-v2t-green transition">
                                                <a href="{{ url('/shop?category=' . $subCat->id) }}" style="text-decoration: none; color: inherit;">{{ $subCat->name }}</a>
                                            </h4>
                                            
                                            <ul class="space-y-2 text-[13px] text-gray-500" style="list-style: none; padding: 0; margin: 0;">
                                                @foreach($subCat->children as $childCat)
                                                <li>
                                                    <a href="{{ url('/shop?category=' . $childCat->id) }}" class="hover:text-v2t-green transition line-clamp-1" style="text-decoration: none; color: inherit;">
                                                        {{ $childCat->name }}
                                                    </a>
                                                </li>
                                                @endforeach
                                            </ul>
                                            <a href="{{ url('/shop?category=' . $subCat->id) }}" class="text-blue-500 hover:text-blue-700 text-xs mt-3 inline-block font-medium" style="text-decoration: none;">
                                                Xem tất cả &rarr;
                                            </a>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>

                </div>
            </div>
        </div>

        <form action="{{ url('/shop') }}" method="GET" class="flex flex-1 max-w-xl mx-8 border border-gray-200 rounded-lg overflow-hidden bg-white shadow-sm focus-within:border-v2t-green">
            @if(request('category'))
                <input type="hidden" name="category" value="{{ request('category') }}">
            @endif
            
            <input type="text" 
                name="search" 
                value="{{ request('search') }}" 
                placeholder="Tìm kiếm tựa sách, tác giả, nhà xuất bản..." 
                class="w-full px-4 py-2 text-sm text-gray-700 focus:outline-none bg-transparent">
            
            <button type="submit" class="hover:opacity-95 px-5 text-white transition flex items-center justify-center focus:outline-none" style="background-color: #1e3e36;">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </button>
        </form>

        <div class="flex items-center gap-4 shrink-0">
            
            {{-- Phân hệ Thông Báo Dropdown (ĐÃ CLEAN SVG CHUỔN CHỈ ĐẦY ĐỦ PHẦN THÂN) --}}
            <div style="position: relative; display: inline-block;" id="notif-wrapper">
                <div onclick="toggleNotifDropdown(event)" class="flex flex-col items-center cursor-pointer text-gray-600 hover:text-v2t-green transition" style="position: relative;">
                    <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    @auth
                        @php
                            $recentOrders = \App\Models\Order::where('user_id', Auth::id())->latest()->take(5)->get();
                            $notifCount = $recentOrders->where('created_at', '>=', now()->subDays(7))->count();
                        @endphp
                        @if($notifCount > 0)
                            <span style="position: absolute; top: -2px; right: -6px; background: #ef4444; color: white; font-size: 10px; font-weight: 700; padding: 1px 5px; border-radius: 9999px; line-height: 1.2;">{{ $notifCount }}</span>
                        @endif
                    @endauth
                    <span class="text-[11px] font-medium">Thông Báo</span>
                </div>

                {{-- Dropdown Panel Danh sách thông báo --}}
                <div id="notif-dropdown" style="display: none; position: absolute; right: -20px; top: calc(100% + 8px); width: 320px; background: white; border: 1px solid #e5e7eb; border-radius: 12px; box-shadow: 0 10px 40px rgba(0,0,0,0.12); z-index: 999; overflow: hidden;">
                    <div style="padding: 14px 16px; border-bottom: 1px solid #f3f4f6; display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 0.875rem; font-weight: 700; color: #111827;">🔔 Thông báo</span>
                        @auth
                            <span style="font-size: 0.6875rem; color: #9ca3af;">{{ $recentOrders->count() }} gần đây</span>
                        @endauth
                    </div>
                    <div style="max-height: 300px; overflow-y: auto;">
                        @auth
                            @if($recentOrders->count() > 0)
                                @foreach($recentOrders as $order)
                                    <a href="{{ route('orders.show', $order->id) }}" style="padding: 12px 16px; border-bottom: 1px solid #f9fafb; display: flex; gap: 10px; align-items: flex-start; transition: background 0.15s; cursor: pointer; text-decoration: none;" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">
                                        <span style="font-size: 1.25rem; flex-shrink: 0;">
                                            @if($order->status == 'completed') ✅
                                            @elseif($order->status == 'processing') 🔄
                                            @elseif($order->status == 'cancelled') ❌
                                            @else 📦
                                            @endif
                                        </span>
                                        <div style="flex: 1; min-width: 0;">
                                            <p style="font-size: 0.8125rem; font-weight: 600; color: #374151; margin: 0 0 2px 0;">
                                                Đơn hàng #{{ $order->id }}
                                                @if($order->status == 'completed')
                                                    <span style="color: #059669;">- Hoàn thành</span>
                                                @elseif($order->status == 'processing')
                                                    <span style="color: #d97706;">- Đang xử lý</span>
                                                @elseif($order->status == 'cancelled')
                                                    <span style="color: #dc2626;">- Đã hủy</span>
                                                @else
                                                    <span style="color: #6b7280;">- {{ ucfirst($order->status) }}</span>
                                                @endif
                                            </p>
                                            <p style="font-size: 0.6875rem; color: #9ca3af; margin: 0;">{{ number_format($order->total_price, 0, ',', '.') }}đ · {{ $order->created_at->diffForHumans() }}</p>
                                        </div>
                                    </a>
                                @endforeach
                            @else
                                <div style="padding: 32px 16px; text-align: center;">
                                    <p style="font-size: 1.5rem; margin-bottom: 6px;">📭</p>
                                    <p style="font-size: 0.8125rem; color: #9ca3af;">Chưa có thông báo nào</p>
                                </div>
                            @endif
                        @else
                            <div style="padding: 24px 16px; text-align: center;">
                                <p style="font-size: 0.8125rem; color: #6b7280;">Vui lòng <a href="/login" style="color: #0a3622; font-weight: 700; text-decoration: underline;">đăng nhập</a> để xem thông báo.</p>
                            </div>
                        @endauth
                    </div>
                </div>
            </div>
            
            <a href="{{ route('cart.index') }}" class="flex flex-col items-center cursor-pointer text-gray-600 hover:text-v2t-green transition relative" style="text-decoration: none;">
                <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                <span id="cart-count" class="absolute top-0 right-0 -mt-1 -mr-3 bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full leading-none">
                    {{ count(session('cart', [])) }}
                </span>
                <span class="text-[11px] font-medium">Giỏ Hàng</span>
            </a>

            <a href="{{ route('wishlist.index') }}" class="flex flex-col items-center cursor-pointer text-gray-600 hover:text-v2t-green transition relative" style="text-decoration: none;">
                <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                <span class="absolute top-0 right-0 -mt-1 -mr-2 bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full leading-none">
                    {{ count(session('wishlist', [])) }}
                </span>
                <span class="text-[11px] font-medium">Yêu Thích</span>
            </a>

            {{-- Phân hệ Tài Khoản Thành Viên --}}
            @guest
            <a href="{{ route('login') }}" class="flex flex-col items-center cursor-pointer text-gray-600 hover:text-v2t-green transition" style="text-decoration: none;">
                <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                <span class="text-[11px] font-medium">Tài Khoản</span>
            </a>
            @endguest
            @auth
                <div class="relative" style="display: inline-block;">
                    <button type="button" onclick="toggleUserDropdown(event)" class="flex items-center gap-1.5 py-2 text-sm font-medium text-gray-700 hover:text-[var(--color-v2t-green)] transition focus:outline-none cursor-pointer border-none bg-transparent">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span>{{ Auth::user()->name }}</span>
                        <svg id="dropdown-arrow" class="w-3.5 h-3.5 opacity-60 transition-transform duration-300" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <div id="user-dropdown-menu" class="absolute right-0 w-44 bg-white border border-gray-200 rounded-lg shadow-lg py-1 mt-1 z-50" style="display: none; position: absolute; right: 0; top: 100%; width: 11rem; background-color: white; border: 1px solid #e5e7eb; border-radius: 8px; padding-top: 4px; padding-bottom: 4px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);">
                        <a href="{{ route('profile') }}" class="block px-4 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 hover:text-[var(--color-v2t-green)] transition" style="text-decoration: none; display: block; padding: 8px 16px; font-size: 0.75rem; color: #374151; font-weight: 700;">
                            👤 Hồ sơ cá nhân
                        </a>
                        <div style="border-top: 1px solid #f3f4f6; margin-top: 4px; margin-bottom: 4px;"></div>
                        <form action="{{ route('logout') }}" method="POST" class="m-0" style="margin: 0;">
                            @csrf
                            <button type="submit" class="w-full text-left block px-4 py-2 text-xs font-bold text-red-600 hover:bg-red-50/60 transition focus:outline-none cursor-pointer border-none bg-transparent" style="width: 100%; text-align: left; display: block; padding: 8px 16px; font-size: 0.75rem; color: #dc2626; font-weight: 700; border: none; background: transparent; cursor: pointer;">
                                🚪 Đăng xuất
                            </button>
                        </form>
                    </div>
                </div>
            @endauth
        </div>
    </div>

    <style>
        .category-parent-item.active {
            background-color: #f3f4f6 !important;
        }
        .category-parent-item.active > span {
            color: #1e3e36 !important;
        }
        .category-parent-item.active .subcategory-right-panel {
            display: block !important;
        }
    </style>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const parentItems = document.querySelectorAll('.category-parent-item');

            parentItems.forEach(item => {
                item.addEventListener('click', function (e) {
                    if (e.target.closest('.subcategory-right-panel')) {
                        return;
                    }
                    parentItems.forEach(el => el.classList.remove('active'));
                    this.classList.add('active');
                });
            });
        });

        function toggleUserDropdown(event) {
            event.stopPropagation(); 
            const menu = document.getElementById('user-dropdown-menu');
            const arrow = document.getElementById('dropdown-arrow');
            const notif = document.getElementById('notif-dropdown');
            if (notif) notif.style.display = 'none';
            
            if (menu.style.display === 'none' || menu.style.display === '') {
                menu.style.display = 'block';
                arrow.style.transform = 'rotate(180deg)';
            } else {
                menu.style.display = 'none';
                arrow.style.transform = 'rotate(0deg)';
            }
        }

        function toggleNotifDropdown(event) {
            event.stopPropagation();
            const dd = document.getElementById('notif-dropdown');
            const userMenu = document.getElementById('user-dropdown-menu');
            const arrow = document.getElementById('dropdown-arrow');
            if (userMenu && userMenu.style.display === 'block') {
                userMenu.style.display = 'none';
                if (arrow) arrow.style.transform = 'rotate(0deg)';
            }
            if (dd.style.display === 'none' || dd.style.display === '') {
                dd.style.display = 'block';
            } else {
                dd.style.display = 'none';
            }
        }

        window.addEventListener('click', function(e) {
            const menu = document.getElementById('user-dropdown-menu');
            const arrow = document.getElementById('dropdown-arrow');
            if (menu && menu.style.display === 'block') {
                menu.style.display = 'none';
                arrow.style.transform = 'rotate(0deg)';
            }
            const notif = document.getElementById('notif-dropdown');
            if (notif && notif.style.display === 'block') {
                notif.style.display = 'none';
            }
        });
    </script>
</header>