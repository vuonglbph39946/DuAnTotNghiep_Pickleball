@extends('admin.layouts.app')

@section('title', 'Quản lý Người dùng | PBall Store')

@section('content')
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1"><i class="mdi mdi-account-group text-primary me-2"></i>Quản lý Người dùng</h3>
        <p class="text-muted small mb-0">Tra cứu thông tin và quản lý trạng thái tài khoản khách hàng.</p>
    </div>
    <span class="badge bg-primary fs-6 px-3 py-2 shadow-sm">Tổng: {{ $totalUsers }} tài khoản</span>
</div>

<div class="card shadow-sm border-0 rounded-3">
    <div class="card-body p-4">
        
        {{-- ================= FORM TÌM KIẾM ================= --}}
        <form action="{{ route('admin.users.index') }}" method="GET" class="mb-4">
            <div class="row gx-2">
                <div class="col-md-5">
                    <div class="input-group shadow-sm rounded">
                        <span class="input-group-text bg-white border-end-0"><i class="mdi mdi-magnify text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Tìm tên, email hoặc SĐT..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="role" class="form-select shadow-sm">
                        <option value="">-- Tất cả vai trò --</option>
                        <option value="customer" {{ request('role') == 'customer' ? 'selected' : '' }}>Khách hàng (Customer)</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Quản trị viên (Admin)</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary fw-bold w-100 shadow-sm">Lọc dữ liệu</button>
                </div>
                @if(request('search') || request('role'))
                <div class="col-md-2">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-light border fw-bold w-100 text-danger">
                        <i class="mdi mdi-close me-1"></i> Xóa lọc
                    </a>
                </div>
                @endif
            </div>
        </form>

        {{-- ================= BẢNG DANH SÁCH ================= --}}
        <div class="table-responsive">
            <table class="table table-hover align-middle border mb-0">
                <thead class="table-light text-muted small">
                    <tr>
                        {{-- ĐÃ SỬA: Cột STT --}}
                        <th class="py-3 px-3 text-center" style="width: 5%;">STT</th>
                        <th class="py-3">THÔNG TIN KHÁCH HÀNG</th>
                        <th class="py-3">SỐ ĐIỆN THOẠI</th>
                        {{-- ĐÃ THÊM: Cột Ngày tham gia --}}
                        <th class="py-3 text-center">NGÀY THAM GIA</th>
                        <th class="py-3 text-center">PHÂN QUYỀN</th>
                        <th class="py-3 text-center">TRẠNG THÁI</th>
                        <th class="py-3 text-center">THAO TÁC</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        {{-- ĐÃ SỬA: Logic tính STT tự động theo phân trang --}}
                        <td class="px-3 fw-bold text-secondary text-center">
                            {{ $loop->iteration + $users->firstItem() - 1 }}
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center text-primary fw-bold me-3 border" style="width: 40px; height: 40px;">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="mb-0 fw-bold text-dark">{{ $user->name }}</p>
                                    <small class="text-muted"><i class="mdi mdi-email-outline me-1"></i>{{ $user->email }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="text-dark fw-medium">{{ $user->phone ?? 'Chưa cập nhật' }}</span>
                        </td>
                        {{-- ĐÃ THÊM: Hiển thị Ngày tham gia --}}
                        <td class="text-center">
                            <div class="badge bg-light text-dark border">
                                <i class="mdi mdi-calendar-check me-1"></i> 
                                {{ $user->created_at ? $user->created_at->format('d/m/Y') : 'N/A' }}
                            </div>
                        </td>
                        <td class="text-center">
                            @if($user->role == 'admin')
                                <span class="badge bg-danger px-2 py-1"><i class="mdi mdi-shield-crown me-1"></i>Quản trị viên</span>
                            @else
                                <span class="badge bg-info px-2 py-1 text-dark"><i class="mdi mdi-account me-1"></i>Người dùng</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($user->status == 1)
                                <span class="badge bg-success-subtle text-success border border-success px-2 py-1">Đang hoạt động</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger px-2 py-1">Bị khóa</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($user->role != 'admin')
                                <form action="{{ route('admin.users.toggle_status', $user->id) }}" method="POST" class="d-inline-block form-toggle-status">
                                    @csrf
                                    @if($user->status == 1)
                                        <button type="button" class="btn btn-sm btn-outline-danger fw-bold shadow-sm btn-lock">
                                            <i class="mdi mdi-lock-outline me-1"></i> Khóa
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-sm btn-success fw-bold shadow-sm btn-unlock text-white">
                                            <i class="mdi mdi-lock-open-outline me-1"></i> Mở khóa
                                        </button>
                                    @endif
                                </form>
                            @else
                                <span class="text-muted small fst-italic">Không thể can thiệp</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        {{-- ĐÃ SỬA: Đổi colspan thành 7 do thêm cột --}}
                        <td colspan="7" class="text-center py-5">
                            <h5 class="fw-bold text-dark mt-3">Không tìm thấy tài khoản nào!</h5>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4 d-flex justify-content-end">
            {{ $users->links('pagination::bootstrap-5') }}
        </div>
        
    </div>
</div>

<script>
    // Xử lý hiển thị Toast Message
    function showToast(msg, bgColor){
        let t = document.createElement('div');
        t.innerHTML = `<div style="position:fixed; bottom:30px; right:30px; background:${bgColor}; color:#fff; padding:14px 24px; border-radius:8px; z-index:99999; box-shadow: 0 10px 30px rgba(0,0,0,0.1); transition: 0.3s; transform: translateY(0);">
            <i class="mdi ${bgColor === '#dc3545' ? 'mdi-alert-circle' : 'mdi-check-circle'} me-2"></i>${msg}
        </div>`;
        document.body.appendChild(t);
        setTimeout(() => t.remove(), 3000); 
    }

    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success')) showToast("{{ session('success') }}", "#198754"); @endif
        @if(session('error')) showToast("{{ session('error') }}", "#dc3545"); @endif

        // Cảnh báo khi bấm Khóa
        document.querySelectorAll('.btn-lock').forEach(btn => {
            btn.addEventListener('click', function(e) {
                let form = this.closest('form');
                swal({
                    title: "Khóa tài khoản này?",
                    text: "Khách hàng sẽ bị đăng xuất và không thể đăng nhập lại cho đến khi được mở khóa.",
                    icon: "warning",
                    buttons: ["Hủy", "Tiến hành Khóa"],
                    dangerMode: true,
                }).then((willLock) => {
                    if (willLock) form.submit();
                });
            });
        });

        // Cảnh báo khi bấm Mở khóa
        document.querySelectorAll('.btn-unlock').forEach(btn => {
            btn.addEventListener('click', function(e) {
                let form = this.closest('form');
                swal({
                    title: "Mở khóa tài khoản?",
                    text: "Khách hàng sẽ được phép đăng nhập lại vào hệ thống.",
                    icon: "info",
                    buttons: ["Hủy", "Xác nhận"],
                }).then((willUnlock) => {
                    if (willUnlock) form.submit();
                });
            });
        });
    });
</script>
@endsection