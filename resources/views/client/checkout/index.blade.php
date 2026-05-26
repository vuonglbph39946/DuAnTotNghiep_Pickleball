@extends('client.layouts.app')
@section('title', 'Thanh toán - PBall Store')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<div class="container p-t-80 p-b-50">
    <div class="bread-crumb flex-w p-b-30">
        <a href="{{ url('/') }}" class="stext-109 cl8 hov-cl1 trans-04">Trang chủ <i class="fa fa-angle-right m-l-9 m-function refreshCheckoutCart() {-10" aria-hidden="true"></i></a>
        <a href="{{ route('cart.index') }}" class="stext-109 cl8 hov-cl1 trans-04">Giỏ hàng <i class="fa fa-angle-right m-l-9 m-r-10" aria-hidden="true"></i></a>
        <span class="stext-109 cl4">Thanh toán</span>
    </div>

    @if(session('error'))
        <div class="alert alert-danger m-b-20"><strong>Lỗi:</strong> {{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger m-b-20 shadow-sm" style="border-left: 5px solid #dc3545;">
            <strong style="font-size: 16px;"><i class="fa fa-exclamation-triangle m-r-10"></i>Vui lòng kiểm tra lại các thông tin sau:</strong>
            <ul class="m-t-10 m-l-20" style="list-style-type: circle;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('checkout.process') }}" method="POST" id="checkoutForm">
        @csrf
        <div class="row">
          <div class="col-md-7 col-lg-7 m-b-30">
    <div class="p-all-30 bor10 bg0 shadow-sm m-b-30">
        <div class="flex-w flex-sb-m p-b-20" style="border-bottom: 1px solid #e6e6e6; margin-bottom: 20px;">
            <h4 class="mtext-105 cl2" style="font-weight: 600;">1. Thông tin giao hàng</h4>
            {{-- Lời chào mặc định cho user đã đăng nhập --}}
            <span class="stext-102 cl6">Xin chào, <strong style="color: #dc3545; font-size: 15px;">{{ Auth::user()->name }}</strong>!</span>
        </div>
        
        @if($addresses->count() > 0)
            <div class="p-b-20 m-b-20" style="border-bottom: 1px dashed #e6e6e6;">
                <label class="flex-w flex-m pointer m-b-15">
                    <input type="radio" name="address_type" value="existing" {{ old('address_type', 'existing') == 'existing' ? 'checked' : '' }} class="js-toggle-addr" style="transform: scale(1.3); margin-right: 10px;">
                    <span class="stext-105 cl2 font-weight-bold" style="font-size: 16px;">Chọn địa chỉ đã lưu</span>
                </label>
                
                <div id="existing_address_block" class="p-l-25">
                    @foreach($addresses as $addr)
                    <label class="flex-w p-all-15 pointer m-b-10" style="border: 1px solid {{ $addr->is_default ? '#dc3545' : '#e6e6e6' }}; border-radius: 5px; transition: 0.3s; {{ $addr->is_default ? 'background-color: #fffafb;' : '' }}">
                        <div class="m-r-15 p-t-5">
                            <input type="radio" name="address_id" value="{{ $addr->id }}" {{ (old('address_id') == $addr->id || (is_null(old('address_id')) && $addr->is_default)) ? 'checked' : '' }} style="transform: scale(1.3);">
                        </div>
                        <div style="flex: 1;">
                            <span class="stext-105 cl2 font-weight-bold" style="font-size: 15px;">{{ $addr->customer_name }}</span>
                            <span class="stext-111 cl6 m-l-5 m-r-5">|</span>
                            <span class="stext-111 cl2 font-weight-bold">{{ $addr->customer_phone }}</span>
                            @if($addr->is_default) <span class="badge badge-danger m-l-10">Mặc định</span> @endif
                            <p class="stext-111 cl6 p-t-5">{{ $addr->specific_address }}, {{ $addr->ward_name }}, {{ $addr->district_name }}, {{ $addr->province_name }}</p>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>
        @else
            <input type="hidden" name="address_type" value="new">
            <div class="alert alert-warning m-b-20">Bạn chưa có địa chỉ, vui lòng nhập thông tin bên dưới:</div>
        @endif

        <label class="flex-w flex-m pointer m-b-15" style="{{ $addresses->count() == 0 ? 'display:none;' : '' }}">
            <input type="radio" name="address_type" value="new" {{ old('address_type') == 'new' ? 'checked' : '' }} class="js-toggle-addr" {{ $addresses->count() == 0 ? 'checked' : '' }} style="transform: scale(1.3); margin-right: 10px;">
            <span class="stext-105 cl2 font-weight-bold" style="font-size: 16px;">Sử dụng địa chỉ mới</span>
        </label>

        <div id="new_address_block" style="display: {{ (old('address_type') == 'new' || $addresses->count() == 0) ? 'block' : 'none' }}; padding: 20px; background: #f8f9fa; border-radius: 5px; border: 1px solid #eee;">
            @include('client.partials.manual-form')
        </div>
    </div>

    <div class="p-all-30 bor10 bg0 shadow-sm">
        <h4 class="mtext-105 cl2 p-b-20" style="border-bottom: 1px solid #e6e6e6; font-weight: 600;">2. Phương thức thanh toán <span class="text-danger">*</span></h4>
        
        <div class="p-t-20">
            <label class="payment-method-box flex-w flex-m p-all-15 pointer m-b-15" for="payment_cod" style="border: 1px solid #e6e6e6; border-radius: 5px; cursor: pointer; display: flex; align-items: center;">
                <input type="radio" id="payment_cod" name="payment_method" value="cod" {{ old('payment_method', 'cod') == 'cod' ? 'checked' : '' }} style="transform: scale(1.3); margin-right: 15px; cursor: pointer;">
                <span class="stext-105 cl2 font-weight-bold" style="font-size: 15px; transition: 0.3s;">Thanh toán khi nhận hàng (COD)</span>
            </label>

            <label class="payment-method-box flex-w flex-m p-all-15 pointer m-b-15" for="payment_vnpay" style="border: 1px solid #e6e6e6; border-radius: 5px; cursor: pointer; display: flex; align-items: center;">
                <input type="radio" id="payment_vnpay" name="payment_method" value="vnpay" {{ old('payment_method') == 'vnpay' ? 'checked' : '' }} style="transform: scale(1.3); margin-right: 15px; cursor: pointer;">
                <span class="stext-105 cl2 font-weight-bold" style="font-size: 15px; transition: 0.3s;">Thanh toán trực tuyến (VNPAY)</span>
            </label>
        </div>
    </div>
</div>

            <div class="col-md-5 col-lg-5 m-b-30">
                <div class="p-all-30 bor10 bg0 shadow-sm" style="border: 1px solid #e6e6e6;">
                    
                    <div id="checkout-cart-wrapper" style="position: relative;">
                        <div class="cart-loading-overlay" style="display: none; position: absolute; top:0; left:0; right:0; bottom:0; background: rgba(255,255,255,0.7); z-index: 10; flex-direction: column; align-items:center; justify-content:center;">
                            <i class="zmdi zmdi-spinner zmdi-hc-spin" style="font-size: 35px; color: #dc3545;"></i>
                        </div>

                        <h4 class="mtext-105 cl2 p-b-20" style="font-weight: 600; border-bottom: 1px dashed #e6e6e6;">Đơn hàng (<span class="checkout-qty-total">{{ array_sum(array_column($cart, 'quantity')) }}</span> sản phẩm)</h4>

                        <div class="p-t-20 p-b-20" style="border-bottom: 1px dashed #e6e6e6;">
                            @foreach($cart as $key => $item)
                            <div class="flex-w flex-t m-b-25 cart-item-row" data-key="{{ $key }}">
                                
                                @php $pModel = \App\Models\Product::with('images')->find($item['product_id']); @endphp
                                <div class="size-w-50 m-r-15 gallery-lb" style="width: 80px; position: relative;">
                                    @if($pModel && $pModel->images->count() > 0)
                                        <a href="{{ asset($pModel->images[0]->image_path) }}" title="{{ $item['name'] }}">
                                            <img src="{{ asset($item['image']) }}" alt="IMG" style="width: 100%; border: 1px solid #eee; border-radius: 4px;">
                                        </a>
                                        @foreach($pModel->images->skip(1) as $img)
                                            <a href="{{ asset($img->image_path) }}" style="display: none;"></a>
                                        @endforeach
                                    @else
                                        <img src="{{ asset($item['image']) }}" alt="IMG" style="width: 100%; border: 1px solid #eee; border-radius: 4px;">
                                    @endif
                                </div>

                                <div class="size-w-flex1 flex-w flex-sb">
                                    <div style="width: 65%;">
                                        <a href="{{ url('product/' . ($item['slug'] ?? '')) }}" class="stext-105 cl2 hov-cl1 trans-04 font-weight-bold d-block p-b-5" style="line-height: 1.3;">
                                            {{ $item['name'] }}
                                        </a>
                                        
                                        @php $variants = \App\Models\ProductVariant::where('product_id', $item['product_id'])->where('stock', '>', 0)->get(); @endphp
                                        @if($variants->count() > 0)
                                            <select class="variant-changer stext-111 cl6 p-all-2 m-b-5" data-old-key="{{ $key }}" data-pid="{{ $item['product_id'] }}" data-qty="{{ $item['quantity'] }}" style="outline: none; border: 1px solid #ddd; border-radius: 3px; max-width: 100%; width: 100%;">
                                                @foreach($variants as $v)
                                                    @php
                                                        $attrs = \DB::table('variant_attribute_values')->join('attribute_values', 'variant_attribute_values.attribute_value_id', '=', 'attribute_values.id')->where('variant_attribute_values.variant_id', $v->id)->pluck('attribute_values.value')->toArray();
                                                        $vName = implode(' - ', $attrs);
                                                    @endphp
                                                    <option value="{{ $v->id }}" {{ $item['variant_id'] == $v->id ? 'selected' : '' }}>{{ $vName }}</option>
                                                @endforeach
                                            </select>
                                        @elseif($item['variant_info'])
                                            <span class="stext-111 cl6 d-block m-b-5">{{ $item['variant_info'] }}</span>
                                        @endif
                                        
                                        <span class="stext-105 cl2 text-danger font-weight-bold d-block m-b-5">{{ number_format($item['price']) }}đ</span>
                                        <span class="stext-111 cl6 d-block text-success font-weight-bold" style="font-size: 13px;">Tồn kho: {{ $item['max_stock'] }} sản phẩm</span>
                                    </div>

                                    <div class="flex-col-sb-m" style="align-items: flex-end; width: 30%;">
                                        <span class="stext-104 cl6 pointer hov-cl-red js-remove-cart" style="text-decoration: underline; font-size: 13px;"><i class="zmdi zmdi-delete m-r-5"></i>Xóa</span>
                                        <div class="flex-w m-t-15" style="width: 80px; height: 28px; border: 1px solid #e6e6e6; border-radius: 4px;">
                                            <div class="cl8 hov-bg-light trans-04 flex-c-m js-update-cart" data-action="minus" style="width: 25px; height: 100%; cursor: pointer;"><i class="fs-12 zmdi zmdi-minus"></i></div>
                                            <input class="mtext-104 cl3 txt-center input-qty" type="number" value="{{ $item['quantity'] }}" readonly style="width: 28px; height: 100%; font-size: 13px; background: transparent; padding: 0; border-left: 1px solid #eee; border-right: 1px solid #eee;">
                                            <div class="cl8 hov-bg-light trans-04 flex-c-m js-update-cart" data-action="plus" data-max="{{ $item['max_stock'] }}" style="width: 25px; height: 100%; cursor: pointer;"><i class="fs-12 zmdi zmdi-plus"></i></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div class="p-t-20 p-b-20 pointer js-show-coupon-modal" style="border-bottom: 1px dashed #e6e6e6; transition: 0.3s;" onmouseover="this.style.backgroundColor='#fdfdfd'" onmouseout="this.style.backgroundColor='transparent'">
                            <div class="flex-w flex-sb-m">
                                <span class="stext-105 cl2 font-weight-bold" style="color: #dc3545; display: flex; align-items: center;">
                                    <i class="zmdi zmdi-card-giftcard m-r-8" style="font-size: 20px;"></i> PBall Voucher
                                </span>
                                <span class="stext-105 cl6" style="font-size: 14px;">
                                    {{-- ĐÃ THÊM MỚI TÍNH NĂNG COUPON: Highlight nếu đã áp mã --}}
                                    <span id="selected-coupon-text" style="{{ isset($couponCode) ? 'color: #dc3545; font-weight: bold;' : '' }}">
                                        {{ isset($couponCode) ? 'Mã: ' . $couponCode : 'Chọn hoặc nhập mã' }}
                                    </span>
                                    <i class="zmdi zmdi-chevron-right m-l-10" style="font-size: 16px;"></i>
                                </span>
                            </div>
                            <input type="hidden" name="coupon_code" id="applied_coupon_code" value="{{ $couponCode ?? '' }}">
                        </div>

                        <div class="p-t-20 p-b-20" style="border-bottom: 1px dashed #e6e6e6;">
                            <span class="stext-105 cl2 font-weight-bold d-block m-b-10">Ghi chú đơn hàng</span>
                            <textarea class="stext-111 cl2 plh3 size-120 p-lr-15 p-tb-15" name="order_note" placeholder="Lưu ý cho shop (ví dụ: Giao giờ hành chính)..." style="border: 1px solid #e6e6e6; border-radius: 3px; width: 100%; min-height: 60px; resize: none; outline: none;">{{ old('order_note') }}</textarea>
                        </div>

                        <div class="p-t-20">
                            <div class="flex-w flex-sb-m p-b-10">
                                <span class="stext-105 cl2">Tạm tính:</span>
                                <span class="stext-105 cl2 font-weight-bold">{{ number_format($subTotal) }}đ</span>
                            </div>
                            <div class="flex-w flex-sb-m p-b-10">
                                <span class="stext-105 cl2">Phí giao hàng:</span>
                                <span class="stext-105 cl2 font-weight-bold">{{ $shippingFee == 0 ? 'Miễn phí' : number_format($shippingFee) . 'đ' }}</span>
                            </div>
                            
                            {{-- ĐÃ THÊM MỚI TÍNH NĂNG COUPON: Cột hiển thị số tiền Giảm Giá --}}
                            <div class="flex-w flex-sb-m p-b-10 discount-row" style="{{ isset($discountAmount) && $discountAmount > 0 ? '' : 'display: none;' }}">
                                <span class="stext-105 cl2">Giảm giá Voucher:</span>
                                <span class="stext-105 font-weight-bold text-success discount-display">- {{ isset($discountAmount) ? number_format($discountAmount) : 0 }}đ</span>
                            </div>

                            <div class="flex-w flex-sb-m p-t-15 m-t-10" style="border-top: 1px solid #e6e6e6;">
                                <span class="mtext-101 cl2" style="font-size: 18px;">Tổng thanh toán:</span>
                                <span class="mtext-101 cl2 text-danger" id="final-total-display" style="font-size: 24px; font-weight: bold;">{{ number_format($totalAmount) }}đ</span>
                            </div>
                        </div>
                    </div> 
                    
                    <button type="button" class="flex-c-m stext-101 cl0 size-116 bg-danger bor1 hov-btn1 p-lr-15 trans-04 pointer mt-4 w-100 js-btn-checkout" style="border-radius: 4px; font-weight: bold;">
                        ĐẶT HÀNG NGAY
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="modal fade" id="couponModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold" style="font-size: 18px;"><i class="zmdi zmdi-card-giftcard text-danger"></i> Chọn Mã Giảm Giá</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <div class="modal-body p-all-20" style="background: #f4f6f8;">
                <div class="flex-w flex-m w-full m-b-20 p-all-10 bg0" style="border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                    <input class="stext-104 cl2 plh4 p-lr-15 m-r-10" type="text" id="manual_coupon_code" placeholder="Nhập mã ưu đãi..." style="flex: 1; height: 40px; border: 1px solid #e6e6e6; border-radius: 3px; outline: none; text-transform: uppercase;">
                    <button type="button" class="flex-c-m stext-104 cl0 bg-danger hov-btn1 p-lr-15 trans-04 pointer js-apply-manual-coupon" style="height: 40px; border-radius: 3px; width: 90px; font-weight: bold;">
                        Áp dụng
                    </button>
                </div>

               {{-- DANH SÁCH VOUCHER --}}
                <div style="max-height: 350px; overflow-y: auto; padding-right: 5px;">
                    @forelse($availableCoupons as $c)
                        @php 
                            // Xử lý Logic UX: Kiểm tra xem đơn hàng có đủ điều kiện Min Order không
                            $isEligible = $subTotal >= $c->min_order_value; 
                            $missingAmount = $c->min_order_value - $subTotal;
                        @endphp
                        
                        {{-- ĐÃ THÊM CLASS js-coupon-item ĐỂ CLICK VÀO LÀ CHỌN LUÔN --}}
                        <div class="flex-w flex-sb-m p-all-15 bg0 m-b-15 {{ $isEligible ? 'pointer js-coupon-item' : '' }}" style="border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); border-left: 4px solid {{ $isEligible ? '#dc3545' : '#b2b2b2' }}; position: relative; opacity: {{ $isEligible ? '1' : '0.6' }}; transition: 0.2s;">
                            <div style="width: 75%;">
                                <span class="stext-105 cl2 font-weight-bold d-block p-b-5" style="font-size: 16px;">
                                    @if($c->discount_type == 'percent')
                                        GIẢM {{ $c->discount_value }}%
                                    @else
                                        GIẢM {{ number_format($c->discount_value) }}đ
                                    @endif
                                </span>
                                
                                <span class="stext-111 cl6 d-block" style="font-size: 12px; line-height: 1.4;">
                                    Đơn tối thiểu {{ number_format($c->min_order_value) }}đ. 
                                    @if($c->discount_type == 'percent' && $c->max_discount_value > 0)
                                        Giảm tối đa {{ number_format($c->max_discount_value) }}đ.
                                    @endif
                                </span>

                                {{-- BỔ SUNG: NGÀY HẾT HẠN & LƯỢT DÙNG --}}
                                <span class="stext-111 cl6 d-block p-t-5 p-b-5" style="font-size: 11px;">
                                    <i class="zmdi zmdi-time text-danger"></i> HSD: 
                                    @if($c->end_date)
                                        {{ \Carbon\Carbon::parse($c->end_date)->format('d/m/Y') }}
                                    @else
                                        Không giới hạn
                                    @endif
                                    
                                    <span style="margin: 0 5px;">|</span>
                                    
                                    <i class="zmdi zmdi-shopping-cart text-success"></i> Còn: {{ $c->quantity }} lượt
                                </span>
                                
                                <span class="stext-111 d-block" style="font-size: 12px; font-weight: bold; color: {{ $isEligible ? '#dc3545' : '#666' }};">
                                    Mã: {{ $c->code }}
                                </span>

                                @if(!$isEligible)
                                    <span class="stext-111 d-block p-t-5 text-warning" style="font-size: 11px; font-style: italic;">
                                        (Mua thêm {{ number_format($missingAmount) }}đ để dùng được mã này)
                                    </span>
                                @endif
                            </div>
                            
                            <div style="width: 20%; text-align: right;">
                                @if($isEligible)
                                    <input type="radio" name="select_coupon_radio" value="{{ $c->code }}" style="transform: scale(1.5); cursor: pointer;" class="js-radio-coupon">
                                @else
                                    <input type="radio" disabled title="Chưa đủ điều kiện" style="transform: scale(1.5);">
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center p-t-20 p-b-20">
                            <i class="zmdi zmdi-card-off" style="font-size: 40px; color: #ccc;"></i>
                            <p class="stext-111 cl6 m-t-10">Hiện tại chưa có mã giảm giá nào phù hợp.</p>
                        </div>
                    @endforelse
                </div>
            </div>
            
            {{-- NÚT XÁC NHẬN NẰM Ở ĐÂY --}}
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 3px;">Trở lại</button>
                <button type="button" class="btn btn-danger js-confirm-coupon" style="border-radius: 3px;">Xác nhận chọn</button>
            </div>
        </div>
    </div>
</div>

<style>
    .payment-method-box { background-color: #fff; transition: all 0.3s ease; }
    .payment-method-box:hover { background-color: #f8f9fa; border-color: #ccc; }
    .payment-method-box.active-payment { border-color: #dc3545 !important; background-color: #fffafb !important; }
    .payment-method-box.active-payment span.stext-105 { color: #dc3545 !important; }
    .bor-red { border-color: #dc3545 !important; }
    .hov-bg-light:hover { background-color: #f8f9fa; }
    .select2-container .select2-selection--single { height: 45px; border: 1px solid #e6e6e6; border-radius: 3px; outline: none; }
    .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 45px; padding-left: 20px; color: #555; font-size: 13px; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 43px; right: 10px; }
    .select2-dropdown { border: 1px solid #e6e6e6; border-radius: 3px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
    
    /* === ĐÃ FIX: Ép Modal phải nằm trên cùng, không bị Header che === */
    .modal { z-index: 100000 !important; }
    .modal-backdrop { z-index: 99999 !important; }
    .payment-method-box { background-color: #fff; transition: all 0.3s ease; }
    .payment-method-box:hover { background-color: #f8f9fa; border-color: #ccc; }
    .payment-method-box.active-payment { border-color: #dc3545 !important; background-color: #fffafb !important; }
    .payment-method-box.active-payment span.stext-105 { color: #dc3545 !important; }
    .bor-red { border-color: #dc3545 !important; }
    .hov-bg-light:hover { background-color: #f8f9fa; }
    .select2-container .select2-selection--single { height: 45px; border: 1px solid #e6e6e6; border-radius: 3px; outline: none; }
    .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 45px; padding-left: 20px; color: #555; font-size: 13px; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 43px; right: 10px; }
    .select2-dropdown { border: 1px solid #e6e6e6; border-radius: 3px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
</style>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        $('input[name="payment_method"]').on('change', function() {
            $('.payment-method-box').removeClass('active-payment');
            if($(this).is(':checked')) {
                $(this).closest('.payment-method-box').addClass('active-payment');
            }
        });
        $('input[name="payment_method"]:checked').trigger('change');

        $('.js-toggle-addr').on('change', function() {
            if($(this).val() === 'new') {
                $('#existing_address_block').slideUp(300);
                $('#new_address_block').slideDown(300);
            } else {
                $('#new_address_block').slideUp(300);
                $('#existing_address_block').slideDown(300);
            }
        });

        function initUIComponents() {
            $('.gallery-lb').each(function() {
                $(this).magnificPopup({ delegate: 'a', type: 'image', gallery: { enabled: true }, mainClass: 'mfp-fade' });
            });
            $('.js-select2').select2({ width: '100%', dropdownPosition: 'below' }).removeAttr('required');
        }
        initUIComponents(); 

        var oldProvince = "{{ old('province_id') }}";
        var oldDistrict = "{{ old('district_id') }}";
        var oldWard = "{{ old('ward_id') }}";

        if ($('.api_province').length > 0) {
            const host = "https://provinces.open-api.vn/api/";
            
            $.ajax({
                url: host + "?depth=1", method: "GET",
                success: function(data) {
                    let row = '<option value="">Tỉnh / Thành phố</option>';
                    data.forEach(element => { row += `<option value="${element.code}" data-name="${element.name}">${element.name}</option>`; });
                    $(".api_province").html(row);
                    if (oldProvince) { $(".api_province").val(oldProvince).trigger('change'); } 
                    else { $(".api_province").trigger('change.select2'); }
                }
            });

            $(".api_province").change(function() {
                $(this).closest('.select2-wrapper').find('.js-custom-error').remove();
                let province_id = $(this).val();
                let block = $(this).closest('.row'); 
                block.find(".province_name").val($(this).find(':selected').data('name'));
                if (province_id) {
                    $.ajax({
                        url: host + "p/" + province_id + "?depth=2", method: "GET",
                        success: function(data) {
                            let row = '<option value="">Quận / Huyện</option>';
                            data.districts.forEach(element => { row += `<option value="${element.code}" data-name="${element.name}">${element.name}</option>`; });
                            block.find(".api_district").html(row).prop('disabled', false);

                            if (oldDistrict) {
                                block.find(".api_district").val(oldDistrict).trigger('change');
                                oldDistrict = ""; 
                            } else { block.find(".api_district").trigger('change.select2'); }

                            block.find(".api_ward").html('<option value="">Phường / Xã</option>').prop('disabled', true).trigger('change.select2');
                        }
                    });
                } else {
                    block.find(".api_district").html('<option value="">Quận / Huyện</option>').prop('disabled', true).trigger('change.select2');
                    block.find(".api_ward").html('<option value="">Phường / Xã</option>').prop('disabled', true).trigger('change.select2');
                }
            });

            $(".api_district").change(function() {
                $(this).closest('.select2-wrapper').find('.js-custom-error').remove();
                let district_id = $(this).val();
                let block = $(this).closest('.row');
                block.find(".district_name").val($(this).find(':selected').data('name'));
                if (district_id) {
                    $.ajax({
                        url: host + "d/" + district_id + "?depth=2", method: "GET",
                        success: function(data) {
                            let row = '<option value="">Phường / Xã</option>';
                            data.wards.forEach(element => { row += `<option value="${element.code}" data-name="${element.name}">${element.name}</option>`; });
                            block.find(".api_ward").html(row).prop('disabled', false);

                            if (oldWard) {
                                block.find(".api_ward").val(oldWard).trigger('change.select2');
                                oldWard = ""; 
                            } else { block.find(".api_ward").trigger('change.select2'); }
                        }
                    });
                } else { block.find(".api_ward").html('<option value="">Phường / Xã</option>').prop('disabled', true).trigger('change.select2'); }
            });

            $(".api_ward").change(function() { 
                $(this).closest('.select2-wrapper').find('.js-custom-error').remove();
                let block = $(this).closest('.row');
                block.find(".ward_name").val($(this).find(':selected').data('name')); 
            });
        }

       function refreshCheckoutCart() {
            $('.cart-loading-overlay').css('display', 'flex'); 
            $.ajax({
                url: window.location.pathname,
                type: 'GET',
                success: function(data) {
                    var newHtml = $(data).find('#checkout-cart-wrapper').html();
                    // === ĐÃ FIX: Lấy thêm HTML mới nhất của cái Modal từ Server ===
                    var newModalHtml = $(data).find('#couponModal').html(); 

                    if(newHtml) {
                        $('#checkout-cart-wrapper').html(newHtml);
                        
                        // === ĐÃ FIX: Cập nhật lại giao diện của Modal (Sáng/Mờ/Báo thiếu tiền) ===
                        if(newModalHtml) {
                            $('#couponModal').html(newModalHtml);
                        }

                        initUIComponents(); 
                        var newQty = $('.checkout-qty-total').first().text();
                        $('.icon-header-noti.js-show-cart').attr('data-notify', newQty);
                    } else { location.reload(); }
                },
                error: function() { location.reload(); }
            });
        }
        
        $(document).on('click', '.js-update-cart', function() {
            var btn = $(this);
            var row = btn.closest('.cart-item-row');
            var key = row.data('key');
            var input = row.find('.input-qty');
            var currentQty = parseInt(input.val());
            var action = btn.data('action');
            var maxStock = parseInt(btn.data('max')) || 999;

            var newQty = currentQty;
            if(action === 'plus') {
                if(currentQty >= maxStock) { swal("Cảnh báo", "Sản phẩm chỉ còn " + maxStock + " cái trong kho!", "warning"); return; }
                newQty = currentQty + 1;
            } else if(action === 'minus') {
                if(currentQty <= 1) return; 
                newQty = currentQty - 1;
            }

            input.val(newQty);
            $.ajax({
                url: '{{ route("cart.update") }}', type: 'POST',
                data: { _token: '{{ csrf_token() }}', cart_key: key, quantity: newQty },
                success: function(res) {
                    if(res.success) { refreshCheckoutCart(); } 
                    else { input.val(currentQty); swal("Cảnh báo", res.msg, "warning"); }
                }
            });
        });

        $(document).on('click', '.js-remove-cart', function() {
            var key = $(this).closest('.cart-item-row').data('key');
            swal({
                title: "Xóa sản phẩm?",
                text: "Xóa sản phẩm này khỏi đơn hàng?",
                icon: "warning", buttons: ["Hủy", "Xóa"], dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    $.ajax({
                        url: '{{ route("cart.remove") }}', type: 'POST',
                        data: { _token: '{{ csrf_token() }}', cart_key: key },
                        success: function(res) { 
                            if(res.success) { 
                                if(res.cart_count == 0) location.reload(); 
                                else refreshCheckoutCart(); 
                            } 
                        }
                    });
                }
            });
        });

        $(document).on('change', '.variant-changer', function() {
            var select = $(this);
            var oldKey = select.data('old-key');
            var pid = select.data('pid');
            var newVid = select.val();
            var qty = select.data('qty');
            if(oldKey.split('_')[1] == newVid) return; 
            select.prop('disabled', true);
            $.ajax({
                url: '{{ route("cart.add") }}', type: 'POST',
                data: { _token: '{{ csrf_token() }}', product_id: pid, variant_id: newVid, quantity: qty },
                success: function(res) {
                    if(res.success) {
                        $.ajax({
                            url: '{{ route("cart.remove") }}', type: 'POST',
                            data: { _token: '{{ csrf_token() }}', cart_key: oldKey },
                            success: function() { refreshCheckoutCart(); }
                        });
                    }
                }
            });
        });

        $(document).on('click', '.js-show-coupon-modal', function() { $('#couponModal').modal('show'); });

        // === THÊM MỚI TÍNH NĂNG COUPON: Logic AJAX gửi mã lên Server ===
        $(document).on('click', '.js-apply-manual-coupon', function() {
            var code = $('#manual_coupon_code').val().trim().toUpperCase();
            if(code === '') { 
                swal("Thông báo", "Vui lòng nhập mã Voucher!", "warning"); 
                return; 
            }
            
            let btn = $(this);
            let oldText = btn.html();
            btn.html('<i class="zmdi zmdi-spinner zmdi-hc-spin"></i>').prop('disabled', true);

            $.ajax({
                url: '{{ route("coupon.apply") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    coupon_code: code
                },
                success: function(res) {
                    btn.html(oldText).prop('disabled', false);

                    if(res.success) {
                        swal("Thành công!", res.msg, "success");
                        
                        // Cập nhật text UI
                        $('#applied_coupon_code').val(code);
                        $('#selected-coupon-text').text('Mã: ' + code).css({'color': '#dc3545', 'font-weight': 'bold'});
                        $('#couponModal').modal('hide');

                        // Hiển thị số tiền được giảm
                        $('.discount-row').show();
                        $('.discount-display').text('- ' + res.discount_amount);
                        
                        // Cập nhật lại Tổng tiền
                        $('#final-total-display').text(res.new_total);

                    } else {
                        swal("Rất tiếc!", res.msg, "error");
                    }
                },
                error: function() {
                    btn.html(oldText).prop('disabled', false);
                    swal("Lỗi!", "Có lỗi kết nối máy chủ.", "error");
                }
            });
        });

        $(document).on('click', '.js-confirm-coupon', function() {
            var selectedRadio = $('input[name="select_coupon_radio"]:checked');
            if(selectedRadio.length > 0) {
                $('#manual_coupon_code').val(selectedRadio.val());
                $('.js-apply-manual-coupon').click(); // Tái sử dụng logic gọi AJAX
            } else {
                $('#couponModal').modal('hide'); 
            }
        });
        // === KẾT THÚC THÊM MỚI COUPON ===

        $(document).on('click', '.js-btn-checkout', function(e) {
            e.preventDefault(); 
            var form = $('#checkoutForm');
            var isNewAddress = $('#new_address_block').is(':visible') || ($('#new_address_block').length && !$('#existing_address_block').length);

            $('.js-custom-error').remove(); 

            if (isNewAddress) {
                var fields = ['customer_name', 'customer_phone', 'email', 'customer_email'];
                for (var i = 0; i < fields.length; i++) {
                    var input = form.find('input[name="' + fields[i] + '"]')[0];
                    if (input && !input.checkValidity()) {
                        input.reportValidity(); 
                        return; 
                    }
                }

                var isSelectValid = true;
                if($('.api_province').val() === '') { 
                    $('.api_province').closest('.select2-wrapper').append('<small class="text-danger js-custom-error m-t-5 d-block" style="font-size: 13px;">Vui lòng chọn Tỉnh/Thành phố.</small>');
                    isSelectValid = false;
                }
                if($('.api_district').val() === '') { 
                    $('.api_district').closest('.select2-wrapper').append('<small class="text-danger js-custom-error m-t-5 d-block" style="font-size: 13px;">Vui lòng chọn Quận/Huyện.</small>');
                    isSelectValid = false;
                }
                if($('.api_ward').val() === '') { 
                    $('.api_ward').closest('.select2-wrapper').append('<small class="text-danger js-custom-error m-t-5 d-block" style="font-size: 13px;">Vui lòng chọn Phường/Xã.</small>');
                    isSelectValid = false;
                }

                if(!isSelectValid) {
                    $('html, body').animate({ scrollTop: $(".api_province").offset().top - 150 }, 500);
                    return; 
                }

                var specificAddress = form.find('input[name="specific_address"]')[0];
                if (specificAddress && !specificAddress.checkValidity()) {
                    specificAddress.reportValidity();
                    return;
                }

            } else {
                if (form.find('input[name="address_id"]:checked').length === 0) {
                    swal("Cảnh báo", "Vui lòng chọn một địa chỉ giao hàng!", "warning");
                    return;
                }
            }

            if (!$('input[name="payment_method"]:checked').val()) {
                swal("Cảnh báo", "Vui lòng chọn một Phương thức thanh toán!", "warning");
                $('html, body').animate({ scrollTop: $(".payment-method-box").first().offset().top - 150 }, 500);
                return;
            }

            var btn = $(this);
            btn.html('<i class="zmdi zmdi-spinner zmdi-hc-spin m-r-10 fs-20"></i> ĐANG CHỐT ĐƠN...');
            btn.css('opacity', '0.7').css('pointer-events', 'none');
            form[0].submit(); 

        
        });
            // Bấm vào khu vực thẻ Coupon sẽ tự động tick cái nút Radio bên trong
        $(document).on('click', '.js-coupon-item', function() {
            var radioBtn = $(this).find('input[type="radio"]');
            if (!radioBtn.prop('disabled')) {
                radioBtn.prop('checked', true);
            }
        });
    });
</script>
@endpush
@endsection