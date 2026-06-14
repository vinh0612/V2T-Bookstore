@extends('layouts.admin')

@section('title', 'Quản lý người dùng')

@section('content')
<style>
    .user-stat-card { background: white; padding: 20px 24px; border-radius: 16px; border: 1px solid #e5e7eb; display: flex; align-items: center; gap: 16px; min-width: 200px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); }
    .user-table { width: 100%; border-collapse: collapse; text-align: left; }
    .user-table th { padding: 16px; color: #6b7280; font-size: 0.8rem; font-weight: 700; border-bottom: 1px solid #f3f4f6; text-transform: uppercase; letter-spacing: 0.05em; }
    .user-table td { padding: 16px; border-bottom: 1px solid #f3f4f6; vertical-align: middle; }
    
    /* Rank Badges */
    .rank-badge { padding: 6px 12px; border-radius: 20px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; display: inline-block; letter-spacing: 0.05em; }
    .rank-gold { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
    .rank-standard { background: #f3f4f6; color: #4b5563; border: 1px solid #e5e7eb; }

    /* Status Indicators */
    .status-indicator { display: inline-flex; align-items: center; gap: 6px; font-size: 0.85rem; font-weight: 600; }
    .status-indicator::before { content: ""; width: 6px; height: 6px; border-radius: 50%; display: inline-block; }
    .status-active { color: #111827; }
    .status-active::before { background: #10b981; }
    .status-blocked { color: #dc2626; }
    .status-blocked::before { background: #dc2626; }

    /* Action Circle Buttons */
    .action-btn { border: none; background: none; color: #4b5563; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 50%; transition: all 0.2s; }
    .action-btn:hover { background: #f3f4f6; color: #111827; }
    
    /* Định màu sắc hành động cho các nút khóa/mở khóa */
    .btn-lock-active { color: #6b7280; }
    .btn-lock-active:hover { background: #fee2e2; color: #dc2626; }
    .btn-lock-blocked { color: #dc2626; background: #fee2e2; }
    .btn-lock-blocked:hover { background: #fca5a5; color: #991b1b; }

    /* Nút xóa tài khoản */
    .btn-delete { color: #9ca3af; }
    .btn-delete:hover { background: #fee2e2; color: #dc2626; }
</style>

<div style="width: 100%;">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 32px;">
        <div>
            <h1 style="font-family: Georgia, serif; font-size: 1.75rem; font-weight: 700; color: #1e3e36; margin: 0 0 6px 0;">Quản lý Người dùng</h1>
            <p style="color: #6b7280; font-size: 0.9rem; margin: 0;">Xem xét trạng thái tài khoản khách hàng và truy cập hồ sơ chi tiết.</p>
        </div>
        
        <div style="display: flex; gap: 16px;">
            <div class="user-stat-card">
                <div style="width: 40px; height: 40px; border-radius: 50%; background: #f3f4f6; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">👥</div>
                <div>
                    <p style="font-size: 0.65rem; color: #6b7280; font-weight: 700; margin: 0; text-transform: uppercase;">Tổng người dùng</p>
                    <h3 style="font-size: 1.4rem; font-weight: 700; margin: 2px 0 0 0;">{{ number_format($totalUsers) }}</h3>
                </div>
            </div>
            
            <div class="user-stat-card">
                <div style="width: 40px; height: 40px; border-radius: 50%; background: #d1fae5; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">🛡️</div>
                <div>
                    <p style="font-size: 0.65rem; color: #6b7280; font-weight: 700; margin: 0; text-transform: uppercase;">Đang hoạt động</p>
                    <h3 style="font-size: 1.4rem; font-weight: 700; margin: 2px 0 0 0;">{{ number_format($activeUsers) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div style="background: white; padding: 24px; border-radius: 16px; border: 1px solid #e5e7eb; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
        <table class="user-table">
            <thead>
                <tr>
                    <th>Chi tiết người dùng</th>
                    <th>Role</th>
                    <th>Trạng thái</th>
                    <th>Lần đăng nhập cuối</th>
                    <th style="text-align: center;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td>
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 40px; height: 40px; border-radius: 50%; background: #e5e7eb; display: flex; align-items: center; justify-content: center; font-weight: 700; color: #1e3e36;">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                            <div>
                                <div style="font-weight: 600; color: #111827; font-size: 0.95rem;">{{ $user->name }}</div>
                                <div style="font-size: 0.8rem; color: #6b7280; margin-top: 1px;">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>
                    
                    <td>
                        <span class="rank-badge {{ $user->role === 'admin' ? 'rank-gold' : 'rank-standard' }}">
                            {{ $user->role === 'admin' ? 'Admin' : 'Customer' }}
                        </span>
                    </td>
                    
                    <td>
                        <span class="status-indicator {{ $user->status === 'active' ? 'status-active' : 'status-blocked' }}">
                            {{ $user->status === 'active' ? 'Hoạt động' : 'Đã khóa' }}
                        </span>
                    </td>
                    
                    <td style="color: #4b5563; font-size: 0.875rem;">
                        {{ $user->updated_at ? $user->updated_at->format('d M, Y') : 'Chưa ghi nhận' }}
                    </td>
                    
                    <td style="text-align: center;">
                        <div style="display: inline-flex; align-items: center; gap: 8px;">
                            {{-- Nút xem chi tiết (Ai cũng xem được) --}}
                            <a href="{{ route('admin.users.show', $user->id) }}" class="action-btn" title="Xem chi tiết hồ sơ">
                                <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </a>
                            
                            {{-- NẾU LÀ KHÁCH HÀNG THƯỜNG -> HIỆN NÚT KHÓA & XÓA --}}
                            @if($user->role !== 'admin')
                                <form action="{{ route('admin.users.toggleStatus', $user->id) }}" method="POST" style="margin: 0; display: inline;">
                                    @csrf
                                    <button type="submit" class="action-btn {{ $user->status === 'active' ? 'btn-lock-active' : 'btn-lock-blocked' }}" title="{{ $user->status === 'active' ? 'Khóa tài khoản' : 'Mở khóa tài khoản' }}">
                                        @if($user->status === 'active')
                                            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5V6.75a4.5 4.5 0 119 0v3.75M3.75 21.75h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H3.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" /></svg>
                                        @else
                                            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" /></svg>
                                        @endif
                                    </button>
                                </form>

                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" style="margin: 0; display: inline;" onsubmit="return confirm('Cảnh báo: Bro có chắc chắn muốn xóa vĩnh viễn tài khoản này không? Mọi dữ liệu đơn hàng liên quan có thể bị ảnh hưởng!');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn btn-delete" title="Xóa tài khoản">
                                        <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                                    </button>
                                </form>
                            
                            {{-- NẾU LÀ ADMIN -> HIỆN KHIÊN BẢO VỆ --}}
                            @else
                                <span title="Tài khoản Admin được bảo vệ tuyệt đối" style="color: #fbbf24; display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px;">
                                    <svg style="width: 20px; height: 20px;" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M12.516 2.17a.75.75 0 00-1.032 0 11.209 11.209 0 01-7.877 3.08.75.75 0 00-.722.515A12.74 12.74 0 002.25 9.75c0 5.942 4.064 10.933 9.563 12.348a.749.749 0 00.374 0c5.499-1.415 9.563-6.406 9.563-12.348 0-1.39-.223-2.73-.635-3.985a.75.75 0 00-.722-.516l-.143.001c-2.996 0-5.717-1.17-7.734-3.08zm3.094 8.016a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd" /></svg>
                                </span>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <div style="margin-top: 24px;">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection