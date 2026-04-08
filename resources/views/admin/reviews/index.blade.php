@extends('admin.layouts.app')

@section('title', 'Quản lý Đánh giá')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-dark">Quản lý Đánh giá & Bình luận</h3>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa fa-exclamation-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
            <h6 class="m-0 fw-bold text-primary"><i class="fa fa-list me-2"></i>Danh sách Đánh giá</h6>
            
            {{-- BỘ LỌC --}}
            <form action="{{ route('admin.reviews.index') }}" method="GET" class="d-flex">
                <select name="status" class="form-select form-select-sm me-2 shadow-none" onchange="this.form.submit()" style="border-radius: 6px;">
                    <option value="">-- Tất cả trạng thái --</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Chờ duyệt</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Đã duyệt (Hiển thị)</option>
                    <option value="2" {{ request('status') === '2' ? 'selected' : '' }}>Đã ẩn (Từ chối)</option>
                </select>
            </form>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 custom-admin-table">
                    <thead class="table-light text-muted" style="font-size: 13px; text-transform: uppercase;">
                        <tr>
                            <th width="18%">Khách hàng / SP</th>
                            <th width="10%">Đánh giá</th>
                            <th width="45%">Nội dung & Hình ảnh</th>
                            <th width="12%">Trạng thái</th>
                            <th class="text-center" width="15%">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reviews as $review)
                            <tr style="border-bottom: 1px solid #f4f4f4;">
                                <td>
                                    <div class="fw-bold text-dark" style="font-size: 14px;">{{ $review->user->name ?? 'User ẩn danh' }}</div>
                                    <div class="text-muted" style="font-size: 12px; margin-bottom: 8px;">{{ $review->user->email ?? '' }}</div>
                                    <div class="product-badge text-truncate" style="max-width: 200px;">
                                        <a href="{{ url('product/' . ($review->product->slug ?? '')) }}" target="_blank" class="text-decoration-none text-primary" style="font-size: 12px; font-weight: 500;">
                                            <i class="fa fa-box-open me-1"></i> {{ $review->product->name ?? 'Sản phẩm đã xóa' }}
                                        </a>
                                    </div>
                                    <div class="text-muted mt-1" style="font-size: 11px;"><i class="fa fa-clock-o me-1"></i>{{ $review->created_at->format('H:i d/m/Y') }}</div>
                                </td>
                                
                                <td>
                                    <div class="review-stars mb-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fa {{ $i <= $review->rating ? 'fa-star' : 'fa-star-o' }}"></i>
                                        @endfor
                                    </div>
                                    <span class="badge bg-light text-dark border">{{ $review->rating }}/5 Sao</span>
                                </td>
                                
                                <td class="py-3">
                                    {{-- NỘI DUNG KHÁCH ĐÁNH GIÁ --}}
                                    <div class="text-dark mb-2" style="font-size: 14px; line-height: 1.5;">
                                        {{ $review->comment }}
                                    </div>

                                  {{-- ẢNH ĐÍNH KÈM --}}
                                    @if($review->images && $review->images->count() > 0)
                                        <div class="d-flex gap-2 mb-2 flex-wrap">
                                            @foreach($review->images as $img)
                                                <a href="{{ asset('storage/' . $img->image_path) }}" target="_blank">
                                                    <img src="{{ asset('storage/' . $img->image_path) }}" alt="img" class="rounded border" style="width: 50px; height: 50px; object-fit: cover;">
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif

                                    {{-- SHOP PHẢN HỒI (NẾU CÓ) --}}
                                    @if($review->reply)
                                        <div class="shop-reply-box mt-2 p-2 rounded" style="background-color: #f0f7ff; border-left: 3px solid #0d6efd;">
                                            <strong class="d-block text-primary" style="font-size: 12px;"><i class="fa fa-reply me-1"></i>Shop đã phản hồi:</strong>
                                            <span class="text-dark" style="font-size: 13px;">{{ $review->reply->content }}</span>
                                        </div>
                                    @endif
                                </td>
                                
                                <td>
                                    @if($review->status == 0)
                                        <span class="badge rounded-pill bg-warning text-dark"><i class="fa fa-hourglass-half me-1"></i> Chờ duyệt</span>
                                    @elseif($review->status == 1)
                                        <span class="badge rounded-pill bg-success"><i class="fa fa-check me-1"></i> Đã hiển thị</span>
                                    @else
                                        <span class="badge rounded-pill bg-secondary"><i class="fa fa-eye-slash me-1"></i> Bị ẩn</span>
                                    @endif
                                </td>
                                
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        {{-- Nút Duyệt / Ẩn --}}
                                        <form action="{{ route('admin.reviews.update_status', $review->id) }}" method="POST">
                                            @csrf
                                            @if($review->status != 1)
                                                <input type="hidden" name="status" value="1">
                                                <button type="submit" class="btn btn-sm btn-light border text-success action-btn" title="Duyệt hiển thị">
                                                    <i class="fa fa-check"></i>
                                                </button>
                                            @else
                                                <input type="hidden" name="status" value="2">
                                                <button type="submit" class="btn btn-sm btn-light border text-warning action-btn" title="Ẩn đánh giá">
                                                    <i class="fa fa-eye-slash"></i>
                                                </button>
                                            @endif
                                        </form>

                                        {{-- Nút Phản hồi (Mở Modal) --}}
                                        <button type="button" class="btn btn-sm btn-light border text-primary action-btn" title="{{ $review->reply ? 'Sửa phản hồi' : 'Trả lời' }}" data-bs-toggle="modal" data-bs-target="#replyModal{{ $review->id }}">
                                            <i class="fa fa-reply"></i>
                                        </button>

                                        {{-- Nút Xóa --}}
                                        <form action="{{ route('admin.reviews.destroy', $review->id) }}" method="POST" onsubmit="return confirm('Cảnh báo: Bạn có chắc chắn muốn xóa vĩnh viễn đánh giá này? Toàn bộ hình ảnh đính kèm cũng sẽ bị xóa!');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light border text-danger action-btn" title="Xóa vĩnh viễn">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>

                                    {{-- =============================================== --}}
                                    {{-- MODAL NHẬP PHẢN HỒI CHO TỪNG ĐÁNH GIÁ             --}}
                                    {{-- =============================================== --}}
                                    <div class="modal fade text-start" id="replyModal{{ $review->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow">
                                                <div class="modal-header bg-light">
                                                    <h5 class="modal-title fw-bold" style="font-size: 16px;">
                                                        <i class="fa fa-reply text-primary me-2"></i> Phản hồi khách hàng
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form action="{{ route('admin.reviews.reply', $review->id) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label text-muted fw-bold" style="font-size: 13px;">Nội dung khách đánh giá:</label>
                                                            <div class="p-3 bg-white border rounded text-dark" style="font-size: 14px;">
                                                                "{{ $review->comment }}"
                                                            </div>
                                                        </div>
                                                        <div class="mb-2">
                                                            <label class="form-label fw-bold text-dark" style="font-size: 13px;">Nội dung Shop phản hồi: <span class="text-danger">*</span></label>
                                                            <textarea name="content" class="form-control shadow-none" rows="4" placeholder="Ví dụ: Cảm ơn bạn đã ủng hộ shop..." required style="font-size: 14px; border-radius: 6px;">{{ $review->reply->content ?? '' }}</textarea>
                                                            <small class="text-muted mt-1 d-block" style="font-size: 12px;">Phản hồi này sẽ được hiển thị công khai trên trang sản phẩm.</small>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-0 bg-light">
                                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Hủy</button>
                                                        <button type="submit" class="btn btn-primary btn-sm px-4">Lưu phản hồi</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- KẾT THÚC MODAL --}}

                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan=\"5\" class=\"text-center py-5 text-muted\">
                                    <i class="fa fa-comments-o fs-1 mb-3 d-block" style="color: #ccc;"></i>
                                    Chưa có đánh giá nào trong hệ thống.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-top py-3">
            {{ $reviews->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

<style>
    .custom-admin-table th { font-weight: 600; letter-spacing: 0.5px; }
    .review-stars { color: #ffc107; font-size: 14px; }
    .action-btn { transition: 0.2s ease; border-radius: 6px; padding: 5px 10px; }
    .action-btn:hover { background-color: #f8f9fa; transform: translateY(-1px); box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
    .product-badge a:hover { text-decoration: underline !important; }
</style>
@endsection