@extends('client.layouts.app')

@section('title', 'Tài khoản của tôi - PBall Store')

@section('content')
<div class="container">
    <div class="bread-crumb flex-w p-l-25 p-r-15 p-t-30 p-lr-0-lg">
        <a href="{{ url('/') }}" class="stext-109 cl8 hov-cl1 trans-04">
            Trang chủ
            <i class="fa fa-angle-right m-l-9 m-r-10" aria-hidden="true"></i>
        </a>
        <span class="stext-109 cl4">
            Tài khoản của tôi
        </span>
    </div>
</div>

<section class="bg0 p-t-40 p-b-120">
    <div class="container">
        <div class="row">
            <div class="col-md-4 col-lg-3 p-b-30">
                <div class="p-all-15 m-b-15 flex-w flex-sb-m" style="background-color: #f8f9fa; border-radius: 8px;">
                    <div style="width: calc(100% - 55px);">
                        <div class="stext-102 cl2" style="font-weight: 600; font-size: 16px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            {{ $user->name }}
                        </div>
                        <div class="stext-111 cl6" style="font-size: 13px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            {{ $user->email }}
                        </div>
                    </div>
                    <div class="flex-c-m stext-102 cl0" style="width: 45px; height: 45px; border-radius: 50%; background-color: #333; font-weight: bold; font-size: 18px;">
                        {{ mb_strtoupper(mb_substr($user->name, 0, 1, 'UTF-8'), 'UTF-8') }}
                    </div>
                </div>

                <div class="p-t-10 p-b-10" style="background-color: #f8f9fa; border-radius: 8px;">
                    <a href="javascript:void(0)" class="tab-link flex-w flex-sb-m p-all-15 trans-04" data-target="tab-profile">
                        <span class="stext-102 cl2 tab-text"><i class="zmdi zmdi-account-o m-r-10 tab-icon"></i> Thông tin cá nhân</span>
                        <i class="zmdi zmdi-chevron-right cl6"></i>
                    </a>
                    
                    <a href="javascript:void(0)" class="tab-link flex-w flex-sb-m p-all-15 trans-04" data-target="tab-password">
                        <span class="stext-102 cl2 tab-text"><i class="zmdi zmdi-lock-outline m-r-10 tab-icon"></i> Đổi mật khẩu</span>
                        <i class="zmdi zmdi-chevron-right cl6"></i>
                    </a>

                    <a href="javascript:void(0)" class="tab-link flex-w flex-sb-m p-all-15 trans-04 active-menu-tab" data-target="tab-orders">
                        <span class="stext-102 cl2 tab-text"><i class="zmdi zmdi-assignment m-r-10 tab-icon"></i> Lịch sử đơn hàng</span>
                        <i class="zmdi zmdi-chevron-right cl6"></i>
                    </a>
                    
                    <a href="javascript:void(0)" class="tab-link flex-w flex-sb-m p-all-15 trans-04" data-target="tab-addresses">
                        <span class="stext-102 cl2 tab-text"><i class="zmdi zmdi-pin m-r-10 tab-icon"></i> Sổ địa chỉ</span>
                        <i class="zmdi zmdi-chevron-right cl6"></i>
                    </a>
                </div>
            </div>

            <div class="col-md-8 col-lg-9 p-b-30">
                
                {{-- TAB: HỒ SƠ --}}
                <div class="tab-content" id="tab-profile" style="display: none;">
                    <h4 class="mtext-105 cl2 p-b-20" style="font-weight: 600; font-size: 20px; border-bottom: 1px solid #e6e6e6; margin-bottom: 20px;">
                        Hồ sơ của tôi
                    </h4>
                    
                    @if(session('success_profile'))
                        <div class="alert alert-success">{{ session('success_profile') }}</div>
                    @endif

                    <form action="{{ route('account.update_profile') }}" method="POST" style="max-width: 500px;">
                        @csrf
                        <div class="p-b-20 pos-relative">
                            <label class="stext-102 cl3 form-label-custom">Email đăng nhập (Không thể thay đổi)</label>
                            <input class="stext-111 cl2 plh3 size-116 p-l-15 p-r-15 bor8" type="text" value="{{ $user->email }}" readonly style="background-color: #f7f7f7; color: #888;">
                        </div>

                        <div class="p-b-20 pos-relative">
                            <label class="stext-102 cl3 form-label-custom">Họ và tên <span class="text-danger">*</span></label>
                            <input class="stext-111 cl2 plh3 size-116 p-l-15 p-r-15 @error('name') bor-red @else bor8 @enderror" type="text" name="name" value="{{ old('name', $user->name) }}">
                            @error('name') <span class="text-danger stext-102">{{ $message }}</span> @enderror
                        </div>

                        <div class="p-b-20 pos-relative">
                            <label class="stext-102 cl3 form-label-custom">Số điện thoại <span class="text-danger">*</span></label>
                            <input class="stext-111 cl2 plh3 size-116 p-l-15 p-r-15 @error('phone') bor-red @else bor8 @enderror" type="text" name="phone" value="{{ old('phone', $user->phone) }}">
                            @error('phone') <span class="text-danger stext-102">{{ $message }}</span> @enderror
                        </div>

                        <button type="submit" class="flex-c-m stext-101 cl0 size-116 bg3 bor1 hov-btn3 p-lr-15 trans-04 pointer" style="max-width: 200px;">
                            Lưu thay đổi
                        </button>
                    </form>
                </div>

                {{-- TAB: MẬT KHẨU --}}
                <div class="tab-content" id="tab-password" style="display: none;">
                    <h4 class="mtext-105 cl2 p-b-20" style="font-weight: 600; font-size: 20px; border-bottom: 1px solid #e6e6e6; margin-bottom: 20px;">
                        Đổi mật khẩu
                    </h4>

                    @if(session('success_password'))
                        <div class="alert alert-success">{{ session('success_password') }}</div>
                    @endif

                    <form action="{{ route('account.update_password') }}" method="POST" style="max-width: 500px;">
                        @csrf
                        <div class="p-b-20 pos-relative">
                            <label class="stext-102 cl3 form-label-custom">Mật khẩu hiện tại <span class="text-danger">*</span></label>
                            <input class="stext-111 cl2 plh3 size-116 p-l-15 p-r-15 @error('current_password') bor-red @else bor8 @enderror" type="password" name="current_password">
                            @error('current_password') <span class="text-danger stext-102">{{ $message }}</span> @enderror
                        </div>

                        <div class="p-b-20 pos-relative">
                            <label class="stext-102 cl3 form-label-custom">Mật khẩu mới <span class="text-danger">*</span></label>
                            <input class="stext-111 cl2 plh3 size-116 p-l-15 p-r-15 @error('new_password') bor-red @else bor8 @enderror" type="password" name="new_password">
                            @error('new_password') <span class="text-danger stext-102">{{ $message }}</span> @enderror
                        </div>

                        <div class="p-b-20 pos-relative">
                            <label class="stext-102 cl3 form-label-custom">Xác nhận mật khẩu mới <span class="text-danger">*</span></label>
                            <input class="stext-111 cl2 plh3 size-116 p-l-15 p-r-15 bor8" type="password" name="new_password_confirmation">
                        </div>

                        <button type="submit" class="flex-c-m stext-101 cl0 size-116 bg3 bor1 hov-btn3 p-lr-15 trans-04 pointer" style="max-width: 200px;">
                            Cập nhật mật khẩu
                        </button>
                    </form>
                </div>

                {{-- ======================================================= --}}
                {{-- TAB: LỊCH SỬ ĐƠN HÀNG                                   --}}
                {{-- ======================================================= --}}
                <div class="tab-content" id="tab-orders" style="display: block;">
                    <h4 class="mtext-105 cl2 p-b-20" style="font-weight: 600; font-size: 20px;">
                        Lịch sử đơn hàng
                    </h4>

                    @if(session('success_order'))
                        <div class="alert alert-success">{{ session('success_order') }}</div>
                    @endif
                    @if(session('error_order'))
                        <div class="alert alert-danger">{{ session('error_order') }}</div>
                    @endif

                    @php 
                        $cStatus = request('status', 'all'); 
                        $cSearch = request('search', '');
                    @endphp

                    <form action="{{ route('account.index') }}" method="GET" class="m-b-20">
                        <input type="hidden" name="status" value="{{ $cStatus }}">
                        <div class="bor8 pos-relative" style="background-color: #fff;">
                            <input class="stext-103 cl2 plh3 size-116 p-l-45 p-r-15" type="text" name="search" value="{{ $cSearch }}" placeholder="Tìm theo mã đơn hàng hoặc tên sản phẩm" style="height: 45px; border-radius: 5px;">
                            
                            <button type="submit" class="pos-absolute flex-c-m pointer trans-04" style="top: 0; left: 0; height: 100%; width: 45px; background: transparent; border: none;">
                                <i class="zmdi zmdi-search fs-20" style="color: #888;"></i>
                            </button>

                            @if($cSearch)
                                <a href="{{ route('account.index') }}" class="pos-absolute flex-c-m" style="top: 0; right: 0; height: 100%; width: 45px; color: #dc3545;" title="Xóa tìm kiếm">
                                    <i class="zmdi zmdi-close fs-20"></i>
                                </a>
                            @endif
                        </div>
                    </form>

                    <div class="flex-w m-b-20 tab-order-status" style="border-bottom: 1px solid #e6e6e6; background-color: #fff; overflow-x: auto; flex-wrap: nowrap;">
                        <a href="javascript:void(0)" class="tab-order-item js-filter-order {{ $cStatus == 'all' ? 'active-tab' : '' }}" data-filter="all">Tất cả</a>
                        <a href="javascript:void(0)" class="tab-order-item js-filter-order {{ $cStatus == 'pending' ? 'active-tab' : '' }}" data-filter="pending">Chờ xác nhận</a>
                        <a href="javascript:void(0)" class="tab-order-item js-filter-order {{ $cStatus == 'confirmed' ? 'active-tab' : '' }}" data-filter="confirmed">Đã xác nhận</a>
                        <a href="javascript:void(0)" class="tab-order-item js-filter-order {{ $cStatus == 'shipping' ? 'active-tab' : '' }}" data-filter="shipping">Đang giao</a>
                        <a href="javascript:void(0)" class="tab-order-item js-filter-order {{ $cStatus == 'completed' ? 'active-tab' : '' }}" data-filter="completed">Đã giao</a>
                        <a href="javascript:void(0)" class="tab-order-item js-filter-order {{ $cStatus == 'cancelled' ? 'active-tab' : '' }}" data-filter="cancelled">Đã huỷ</a>
                    </div>

                    <div class="custom-scrollbar" style="max-height: 600px; overflow-y: auto; overflow-x: hidden; padding-right: 8px;">
                        
                        @foreach($orders as $order)
                        <div class="bor8 m-b-20 p-all-20 shadow-sm js-order-card" data-status="{{ $order->order_status }}" style="background-color: #fff; transition: 0.3s;">
                            <div class="flex-w flex-sb-m p-b-15" style="border-bottom: 1px dashed #e6e6e6;">
                                <span class="stext-105 cl2" style="font-weight: bold;">Mã ĐH: <span style="color: #dc3545;">#{{ $order->order_code }}</span></span>
                                <span class="stext-105" style="color: #fbbc04; font-weight: bold; text-transform: uppercase;">
                                    @if($order->order_status == 'pending') Chờ xác nhận
                                    @elseif($order->order_status == 'confirmed') Đã xác nhận
                                    @elseif($order->order_status == 'shipping') Đang giao 
                                    @elseif($order->order_status == 'completed') Đã giao 
                                    @elseif($order->order_status == 'cancel_requested') Yêu cầu hủy
                                    @elseif($order->order_status == 'cancelled') Đã hủy
                                    @elseif($order->order_status == 'returned') Trả hàng
                                    @endif
                                </span>
                            </div>

                            <div class="p-t-15 p-b-15" style="border-bottom: 1px solid #e6e6e6;">
                                @foreach($order->items->take(2) as $item)
                                <div class="flex-w flex-m m-b-10">
                                    <a href="{{ $item->product ? url('product/'.$item->product->slug) : 'javascript:void(0)' }}" class="wrap-pic-w size-w-50 m-r-15 border rounded d-block" style="width: 60px; height: 60px; overflow: hidden;">
                                        <img src="{{ $item->product && $item->product->images->first() ? asset($item->product->images->first()->image_path) : 'https://placehold.co/60' }}" alt="IMG" style="width: 100%; height: 100%; object-fit: cover;">
                                    </a>
                                    <div style="width: calc(100% - 160px);">
                                        <a href="{{ $item->product ? url('product/'.$item->product->slug) : 'javascript:void(0)' }}" class="stext-105 cl2 hov-cl1 trans-04 d-block fw-bold" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            {{ $item->product->name ?? 'Sản phẩm đã xóa' }}
                                        </a>
                                        @if($item->variant_info)
                                            <span class="stext-111 cl6 d-block">Phân loại: {{ $item->variant_info }}</span>
                                        @endif
                                        <span class="stext-111 cl6 d-block">Số lượng: x{{ $item->quantity }}</span>
                                    </div>
                                    <div class="ms-auto stext-102" style="color: #dc3545; font-weight: bold;">
                                        {{ number_format($item->price) }}đ
                                    </div>
                                </div>
                                @endforeach
                                
                                @if($order->items->count() > 2)
                                    <div class="txt-center stext-111 cl6 m-t-10">
                                        <i class="zmdi zmdi-chevron-down"></i> Và {{ $order->items->count() - 2 }} sản phẩm khác
                                    </div>
                                @endif
                            </div>

                            <div class="p-t-15 flex-w flex-sb-m">
                                <div>
                                    <span class="stext-111 cl6 d-block p-b-5">Ngày đặt: {{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i') }}</span>
                                    <span class="stext-105 cl2" style="font-size: 16px; font-weight: bold;">Tổng tiền: <span style="color: #dc3545;">{{ number_format($order->total_amount, 0, ',', '.') }}đ</span></span>
                                </div>
                                
                                <div class="d-flex align-items-center">
                                    <a href="{{ route('account.orders.show', $order->order_code) }}" class="flex-c-m stext-101 cl0 bg3 bor1 hov-btn3 p-lr-20 trans-04 pointer" style="height: 40px; border-radius: 3px;">
                                        Xem chi tiết
                                    </a>

                                    {{-- ĐÃ FIX: Hiển thị Nút Hủy chung cho cả COD và Online, miễn là đang Pending hoặc Confirmed --}}
                                    @if(in_array($order->order_status, ['pending', 'confirmed']))
                                        <form action="{{ route('account.orders.cancel', $order->order_code) }}" method="POST" class="ms-2 mb-0">
                                            @csrf
                                            <button type="button" class="btn btn-outline-danger flex-c-m stext-101 p-lr-20 trans-04 pointer fw-bold js-btn-cancel-order" style="height: 40px; border-radius: 3px;">
                                                Hủy đơn
                                            </button>
                                        </form>
                                    @endif

                                    {{-- NEW: Nút Đã nhận hàng (nếu đang giao) --}}
                                    @if($order->order_status == 'shipping')
                                        <form action="{{ route('account.orders.receive', $order->order_code) }}" method="POST" class="ms-2 mb-0">
                                            @csrf
                                            <button type="button" class="btn btn-success flex-c-m stext-101 p-lr-20 trans-04 pointer fw-bold js-btn-receive-order" style="height: 40px; border-radius: 3px; color: #fff;">
                                                Đã nhận hàng
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach

                        <div class="flex-col-c-m js-empty-order" style="display: {{ $orders->count() == 0 ? 'flex' : 'none' }}; background-color: #f8f9fa; border-radius: 8px; min-height: 300px;">
                            <div style="width: 80px; height: 80px; border-radius: 50%; background-color: #e9ecef; display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                                <i class="zmdi zmdi-search" style="font-size: 40px; color: #ced4da;"></i>
                            </div>
                            <span class="stext-102 cl2 js-empty-text" style="font-size: 15px; font-weight: bold;">Không có đơn hàng nào!</span>
                            @if($cSearch)
                                <a href="{{ route('account.index') }}" class="stext-101 cl0 bg3 bor1 hov-btn3 p-lr-15 trans-04 pointer m-t-15 p-tb-10">Xóa bộ lọc tìm kiếm</a>
                            @endif
                        </div>

                    </div>
                </div>

                {{-- TAB: SỔ ĐỊA CHỈ --}}
                <div class="tab-content" id="tab-addresses" style="display: none;">
                    <div class="flex-w flex-sb-m p-b-20" style="border-bottom: 1px solid #e6e6e6; margin-bottom: 20px;">
                        <h4 class="mtext-105 cl2" style="font-weight: 600; font-size: 20px;">
                            Địa chỉ của tôi
                        </h4>
                        <button class="flex-c-m stext-101 cl0 bg3 bor1 hov-btn3 p-lr-15 trans-04 pointer" data-toggle="modal" data-target="#modalAddAddress" style="height: 40px; border-radius: 3px;">
                            + Thêm địa chỉ mới
                        </button>
                    </div>

                    @if(session('success_address')) <div class="alert alert-success">{{ session('success_address') }}</div> @endif
                    @if(session('error_address')) <div class="alert alert-danger">{{ session('error_address') }}</div> @endif

                    @if($addresses->count() == 0)
                        <div class="flex-col-c-m" style="background-color: #f8f9fa; border-radius: 8px; min-height: 250px;">
                            <span class="stext-102 cl2">Bạn chưa lưu địa chỉ nào</span>
                        </div>
                    @else
                        @foreach($addresses as $addr)
                        <div class="bor8 m-b-15 p-all-20 flex-w flex-sb-m">
                            <div style="width: calc(100% - 130px);">
                                <div class="p-b-5">
                                    <span class="stext-105 cl2" style="font-weight: bold; font-size: 16px;">{{ $addr->customer_name }}</span>
                                    <span class="stext-111 cl6 m-l-10 m-r-10">|</span>
                                    <span class="stext-111 cl6">{{ $addr->customer_phone }}</span>
                                </div>
                                <div class="stext-111 cl6 p-b-5">{{ $addr->specific_address }}</div>
                                <div class="stext-111 cl6">{{ $addr->ward_name }}, {{ $addr->district_name }}, {{ $addr->province_name }}</div>
                                
                                @if($addr->is_default)
                                    <div class="m-t-10"><span class="stext-111" style="color: #e65540; border: 1px solid #e65540; padding: 2px 8px; border-radius: 3px; font-size: 12px;">Mặc định</span></div>
                                @endif
                            </div>

                            <div class="flex-col-r-m" style="width: 120px;">
                                <div class="flex-w m-b-10">
                                    @if(!$addr->is_default)
                                        <form action="{{ route('account.addresses.destroy', $addr->id) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button type="button" class="stext-104 cl6 hov-cl-red trans-04 js-btn-delete">Xóa</button>
                                        </form>
                                    @endif
                                </div>
                                
                                @if(!$addr->is_default)
                                    <form action="{{ route('account.addresses.set_default', $addr->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="flex-c-m stext-104 cl0 bg3 bor1 hov-btn3 p-lr-15 trans-04 pointer" style="height: 35px; border-radius: 3px; font-size: 13px;">Thiết lập mặc định</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    @endif
                </div>

            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="modalAddAddress" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 8px;">
            <div class="modal-header" style="border-bottom: 1px solid #eee;">
                <h5 class="modal-title stext-105 cl2" style="font-weight: 600;">Địa chỉ mới</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <form action="{{ route('account.addresses.store') }}" method="POST">
                @csrf
                <div class="modal-body p-all-25">
                    <div class="row">
                        <div class="col-6 p-b-15">
                            <input class="stext-111 cl2 plh3 size-116 p-l-15 p-r-15 bor8" type="text" name="customer_name" placeholder="Họ và tên *" required>
                        </div>
                        <div class="col-6 p-b-15">
                            <input class="stext-111 cl2 plh3 size-116 p-l-15 p-r-15 bor8" type="text" name="customer_phone" placeholder="Số điện thoại *" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 p-b-15">
                            <select class="stext-111 cl2 plh3 size-116 p-l-15 p-r-15 bor8" id="api_province" name="province_id" required>
                                <option value="">Chọn Tỉnh / Thành phố *</option>
                            </select>
                            <input type="hidden" id="province_name" name="province_name">
                        </div>
                        <div class="col-12 p-b-15">
                            <select class="stext-111 cl2 plh3 size-116 p-l-15 p-r-15 bor8" id="api_district" name="district_id" disabled required>
                                <option value="">Chọn Quận / Huyện *</option>
                            </select>
                            <input type="hidden" id="district_name" name="district_name">
                        </div>
                        <div class="col-12 p-b-15">
                            <select class="stext-111 cl2 plh3 size-116 p-l-15 p-r-15 bor8" id="api_ward" name="ward_id" disabled required>
                                <option value="">Chọn Phường / Xã *</option>
                            </select>
                            <input type="hidden" id="ward_name" name="ward_name">
                        </div>
                    </div>

                    <div class="p-b-15">
                        <input class="stext-111 cl2 plh3 size-116 p-l-15 p-r-15 bor8" type="text" name="specific_address" placeholder="Tên đường, Tòa nhà, Số nhà *" required>
                    </div>

                    <div class="flex-w flex-m p-t-10">
                        <input class="m-r-10" type="checkbox" name="is_default" id="is_default" value="1">
                        <label class="stext-111 cl2 m-b-0 pointer" for="is_default">Đặt làm địa chỉ mặc định</label>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: none; padding-bottom: 25px;">
                    <button type="button" class="stext-101 cl6 hov-cl-red trans-04 p-r-15 pointer" data-dismiss="modal" style="background: none; border: none;">Trở lại</button>
                    <button type="submit" class="flex-c-m stext-101 cl0 bg3 bor1 hov-btn3 p-lr-15 trans-04 pointer" style="height: 40px; border-radius: 3px;">Hoàn thành</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .modal { z-index: 100000 !important; }
    .modal-backdrop { z-index: 99999 !important; }

    .tab-link.active-menu-tab { background-color: #fff; border-left: 3px solid #e65540; box-shadow: 0 2px 5px rgba(0,0,0,0.02); }
    .tab-link.active-menu-tab .tab-text, .tab-link.active-menu-tab .tab-icon { color: #e65540 !important; font-weight: 600; }
    .tab-link.active-menu-tab .cl6 { color: #e65540; }
    .tab-link:not(.active-menu-tab):hover { background-color: #ffffff; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
    .tab-icon { font-size: 20px; color: #333; width: 20px; text-align: center; }
    
    .tab-order-status { display: flex; width: 100%; border-bottom: 1px solid #e6e6e6; }
    .tab-order-status::-webkit-scrollbar { height: 4px; }
    .tab-order-status::-webkit-scrollbar-thumb { background-color: #ccc; border-radius: 4px; }
    .tab-order-item {
        flex: 1; text-align: center; padding: 15px 10px; font-size: 15px; color: #555;
        font-weight: 500; border-bottom: 2px solid transparent; transition: all 0.3s;
        margin-bottom: -1px; white-space: nowrap; 
    }
    .tab-order-item:hover { color: #fbbc04; }
    .tab-order-item.active-tab { color: #fbbc04 !important; font-weight: 600; border-bottom: 2px solid #fbbc04; }

    select.bor8 { outline: none; -webkit-appearance: none; -moz-appearance: none; background: url('data:image/svg+xml;utf8,<svg viewBox="0 0 140 140" xmlns="http://www.w3.org/2000/svg"><g><path d="m121.3,34.6c-1.6-1.6-4.2-1.6-5.8,0l-51,51.1-51.1-51.1c-1.6-1.6-4.2-1.6-5.8,0-1.6,1.6-1.6,4.2 0,5.8l53.9,53.9c0.8,0.8 1.8,1.2 2.9,1.2 1,0 2.1-0.4 2.9-1.2l53.9-53.9c1.7-1.6 1.7-4.2 0.1-5.8z"/></g></svg>') no-repeat right 15px center/12px auto #fff; }
    select.bor8:disabled { background-color: #f7f7f7; cursor: not-allowed; }
    .wrap-pic-w:hover img { transform: scale(1.05); }

    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f8f9fa; border-radius: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #dc3545; border-radius: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #c82333; }
</style>

@push('scripts')
<script>
    $(document).ready(function() {
        
        $('.tab-link').on('click', function() {
            var target = $(this).data('target');
            $('.tab-link').removeClass('active-menu-tab');
            $(this).addClass('active-menu-tab');
            $('.tab-content').hide();
            $('#' + target).fadeIn(300);
        });

        @if(request()->has('status') || request()->has('search') || session('success_order') || session('error_order'))
            $('.tab-link').removeClass('active-menu-tab');
            $('[data-target="tab-orders"]').addClass('active-menu-tab');
            $('.tab-content').hide();
            $('#tab-orders').show();
        @endif

        $('.js-filter-order').on('click', function() {
            $('.js-filter-order').removeClass('active-tab');
            $(this).addClass('active-tab');

            let filter = $(this).data('filter');
            let visibleCount = 0;

            if (filter === 'all') {
                $('.js-order-card').show();
                visibleCount = $('.js-order-card').length;
                $('.js-empty-text').text('Không có đơn hàng nào!');
            } else {
                $('.js-order-card').each(function() {
                    if ($(this).data('status') === filter) {
                        $(this).show();
                        visibleCount++;
                    } else {
                        $(this).hide();
                    }
                });
                $('.js-empty-text').text('Không có đơn hàng nào ở trạng thái này!');
            }

            if (visibleCount === 0) {
                $('.js-empty-order').css('display', 'flex');
            } else {
                $('.js-empty-order').hide();
            }
        });

        if ($('.js-filter-order.active-tab').length) {
            $('.js-filter-order.active-tab').trigger('click');
        }

        @if(session('success_profile') || $errors->has('name') || $errors->has('phone'))
            $('[data-target="tab-profile"]').click();
        @elseif(session('success_password') || $errors->has('current_password') || $errors->has('new_password'))
            $('[data-target="tab-password"]').click();
        @elseif(session('success_address') || session('error_address') || $errors->has('specific_address'))
            $('[data-target="tab-addresses"]').click();
        @endif

        @if($errors->has('customer_name') || $errors->has('customer_phone') || $errors->has('specific_address'))
            $('#modalAddAddress').modal('show');
        @endif

        // SCRIPT XÁC NHẬN HỦY ĐƠN HÀNG
        $('.js-btn-cancel-order').on('click', function(e) {
            e.preventDefault();
            var form = $(this).closest('form');
            swal({
                title: "Xác nhận hủy đơn?",
                text: "Bạn có chắc chắn muốn hủy đơn hàng này không? Thao tác này không thể hoàn tác.",
                icon: "warning",
                buttons: ["Đóng lại", "Đồng ý hủy"],
                dangerMode: true,
            }).then(function(willCancel) {
                if (willCancel) {
                    form.submit();
                }
            });
        });

        // THÊM SCRIPT XÁC NHẬN ĐÃ NHẬN HÀNG
        $('.js-btn-receive-order').on('click', function(e) {
            e.preventDefault();
            var form = $(this).closest('form');
            swal({
                title: "Xác nhận đã nhận hàng?",
                text: "Vui lòng chỉ xác nhận khi bạn đã nhận được gói hàng nguyên vẹn và thanh toán đầy đủ (nếu có).",
                icon: "info",
                buttons: ["Đóng lại", "Xác nhận"],
            }).then(function(willConfirm) {
                if (willConfirm) {
                    form.submit();
                }
            });
        });

        $('.js-btn-delete').on('click', function(e) {
            e.preventDefault();
            var form = $(this).closest('form');
            swal({ title: "Xóa địa chỉ?", text: "Bạn có chắc chắn muốn xóa địa chỉ này không?", icon: "warning", type: "warning", showCancelButton: true, confirmButtonColor: "#d33", confirmButtonText: "Xóa ngay", cancelButtonText: "Hủy", buttons: ["Hủy", "Xóa ngay"], dangerMode: true,
            }).then(function(willDelete) { if (willDelete) { form.submit(); } }, function(isConfirm) { if (isConfirm === true) { form.submit(); } });
        });

        const host = "https://provinces.open-api.vn/api/";
        $.ajax({ url: host + "p/", method: "GET", success: function(data) { let row = '<option value="">Chọn Tỉnh / Thành phố *</option>'; data.forEach(element => { row += `<option value="${element.code}" data-name="${element.name}">${element.name}</option>`; }); $("#api_province").html(row); } });
        $("#api_province").change(function() { let province_id = $(this).val(); $("#province_name").val($(this).find(':selected').data('name')); $("#api_ward").html('<option value="">Chọn Phường / Xã *</option>').prop('disabled', true); if (province_id) { $.ajax({ url: host + "p/" + province_id + "?depth=2", method: "GET", success: function(data) { let row = '<option value="">Chọn Quận / Huyện *</option>'; data.districts.forEach(element => { row += `<option value="${element.code}" data-name="${element.name}">${element.name}</option>`; }); $("#api_district").html(row).prop('disabled', false); } }); } else { $("#api_district").html('<option value="">Chọn Quận / Huyện *</option>').prop('disabled', true); } });
        $("#api_district").change(function() { let district_id = $(this).val(); $("#district_name").val($(this).find(':selected').data('name')); if (district_id) { $.ajax({ url: host + "d/" + district_id + "?depth=2", method: "GET", success: function(data) { let row = '<option value="">Chọn Phường / Xã *</option>'; data.wards.forEach(element => { row += `<option value="${element.code}" data-name="${element.name}">${element.name}</option>`; }); $("#api_ward").html(row).prop('disabled', false); } }); } else { $("#api_ward").html('<option value="">Chọn Phường / Xã *</option>').prop('disabled', true); } });
        $("#api_ward").change(function() { $("#ward_name").val($(this).find(':selected').data('name')); });
    });
</script>
@endpush
@endsection