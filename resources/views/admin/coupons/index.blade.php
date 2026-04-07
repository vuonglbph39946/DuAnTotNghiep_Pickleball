@extends('admin.layouts.app')
@section('title', 'Quản lý Mã giảm giá')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="card-title text-primary mb-1"><i class="mdi mdi-ticket-percent me-2"></i>Mã Giảm Giá</h4>
                    </div>
                    <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary fw-bold">
                        <i class="mdi mdi-plus"></i> Thêm Mã Mới
                    </a>
                </div>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Mã Code</th>
                                <th>Loại</th>
                                <th>Giá trị</th>
                                <th>Đơn tối thiểu</th>
                                <th>Số lượng</th>
                                <th>Hạn sử dụng</th>
                                <th>Trạng thái</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($coupons as $coupon)
                                <tr>
                                    <td><strong class="text-danger">{{ $coupon->code }}</strong></td>
                                    <td>
                                        @if($coupon->discount_type == 'percent')
                                            <span class="badge bg-info text-white">Giảm %</span>
                                        @else
                                            <span class="badge bg-secondary text-white">Cố định</span>
                                        @endif
                                    </td>
                                    <td>
                                        <strong class="text-success">
                                            {{ $coupon->discount_type == 'percent' ? $coupon->discount_value.'%' : number_format($coupon->discount_value).'đ' }}
                                        </strong>
                                    </td>
                                    <td>{{ number_format($coupon->min_order_value) }}đ</td>
                                    <td>{{ $coupon->quantity }}</td>
                                    <td>
                                        @if($coupon->end_date)
                                            {{ \Carbon\Carbon::parse($coupon->end_date)->format('d/m/Y') }}
                                        @else
                                            <span class="text-muted">Không giới hạn</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($coupon->quantity <= 0)
                                            <span class="badge bg-secondary">Hết lượt</span>
                                        @elseif($coupon->status == 1)
                                            <span class="badge bg-success">Hoạt động</span>
                                        @else
                                            <span class="badge bg-danger">Đã khóa</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.coupons.edit', $coupon->id) }}" class="btn btn-sm btn-warning text-white" title="Sửa">
                                            <i class="mdi mdi-pencil"></i>
                                        </a>
                                        
                                        {{-- ĐÃ FIX: Bỏ onsubmit cũ đi, thêm class "form-delete" để dùng JS xử lý --}}
                                        <form action="{{ route('admin.coupons.destroy', $coupon->id) }}" method="POST" class="d-inline-block form-delete">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-danger btn-delete" title="Xóa">
                                                <i class="mdi mdi-delete"></i>
                                            </button>
                                        </form>
                                        
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">Chưa có mã giảm giá nào!</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-4">
                    {{ $coupons->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Thêm thư viện SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        // Bắt sự kiện khi click vào nút Xóa
        $('.btn-delete').on('click', function(e) {
            e.preventDefault(); // Chặn hành vi submit mặc định
            var form = $(this).closest('form'); // Lấy cái form đang chứa nút Xóa đó

            Swal.fire({
                title: 'Bạn có chắc chắn?',
                text: "Sau khi xóa, bạn sẽ không thể khôi phục mã giảm giá này!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Vâng, xóa nó!',
                cancelButtonText: 'Hủy bỏ'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Nếu bấm xác nhận thì mới submit form lên Server
                    form.submit();
                }
            });
        });
    });
</script>
@endpush