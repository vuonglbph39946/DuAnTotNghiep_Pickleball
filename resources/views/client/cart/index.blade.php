@extends('client.layouts.app')
@section('title', 'Giỏ hàng của bạn - PBall Store')

@section('content')
<div class="container p-t-80 p-b-80">
    <div class="bread-crumb flex-w p-b-30">
        <a href="{{ url('/') }}" class="stext-109 cl8 hov-cl1 trans-04">Trang chủ <i class="fa fa-angle-right m-l-9 m-r-10" aria-hidden="true"></i></a>
        <span class="stext-109 cl4">Giỏ hàng ({{ array_sum(array_column($cart, 'quantity')) }})</span>
    </div>

    <div class="row">
        <div class="col-lg-8 col-xl-8 m-b-30">
            
            <div class="flex-w flex-sb-m p-b-20" style="border-bottom: 1px solid #e6e6e6; margin-bottom: 20px;">
                <h3 class="mtext-105 cl2" style="font-size: 24px; font-weight: 600;">Giỏ hàng của bạn</h3>
                <div class="stext-111 cl6">
                    Bạn đang có <strong class="text-dark" id="cart-count-page">{{ array_sum(array_column($cart, 'quantity')) }} sản phẩm</strong> trong giỏ hàng
                </div>
            </div>
                
            @if(empty($cart) || count($cart) == 0)
                <div class="text-center p-t-50 p-b-50 bor10 bg0 shadow-sm p-all-30">
                    <i class="zmdi zmdi-shopping-cart-plus" style="font-size: 80px; color: #e6e6e6;"></i>
                    <p class="stext-111 cl6 p-t-20">Chưa có sản phẩm nào trong giỏ hàng.</p>
                    <a href="{{ url('/') }}" class="flex-c-m stext-101 cl0 size-116 bg-danger bor1 hov-btn1 p-lr-15 trans-04 pointer mt-4 mx-auto" style="max-width: 200px; border-radius: 4px;">
                        Tiếp tục mua sắm
                    </a>
                </div>
            @else
                @foreach($cart as $key => $item)
                <div class="cart-item-row flex-w flex-sb-t p-all-20 m-b-20 bg0 shadow-sm" style="border: 1px solid #f0f0f0; border-radius: 8px; position: relative;" data-key="{{ $key }}">
                    
                    <div class="js-remove-cart pointer" data-key="{{ $key }}" style="position: absolute; top: -8px; left: -8px; width: 26px; height: 26px; background: #888; color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 9px; font-weight: bold; z-index: 10; transition: 0.3s; border: 2px solid #fff;" title="Xóa">
                        <i class="zmdi zmdi-close" style="font-size: 14px;"></i>
                    </div>
                    
                    <div class="wrap-pic-w size-w-50" style="width: 100px; border: 1px solid #eee; border-radius: 5px; padding: 5px;">
                        <a href="{{ url('product/' . ($item['slug'] ?? '')) }}">
                            <img src="{{ asset($item['image']) }}" alt="IMG" style="width: 100%; border-radius: 3px;">
                        </a>
                    </div>

                    <div class="size-w-flex1 flex-w flex-sb" style="padding-left: 20px;">
                        <div style="width: 60%;">
                            <a href="{{ url('product/' . ($item['slug'] ?? '')) }}" class="mtext-102 cl2 hov-cl1 trans-04 font-weight-bold" style="font-size: 15px; text-transform: uppercase;">
                                {{ $item['name'] }}
                            </a>
                            
                            <div class="m-t-8 m-b-10">
                                @php
                                    $variants = \App\Models\ProductVariant::where('product_id', $item['product_id'])->where('stock', '>', 0)->get();
                                @endphp
                                
                                @if($variants->count() > 0)
                                    <select class="variant-changer stext-111 cl6 p-all-5" data-old-key="{{ $key }}" data-pid="{{ $item['product_id'] }}" data-qty="{{ $item['quantity'] }}" style="outline: none; cursor: pointer; border: 1px solid #ddd; border-radius: 4px; background: #f8f9fa;">
                                        @foreach($variants as $v)
                                            @php
                                                $attrs = \DB::table('variant_attribute_values')
                                                    ->join('attribute_values', 'variant_attribute_values.attribute_value_id', '=', 'attribute_values.id')
                                                    ->where('variant_attribute_values.variant_id', $v->id)
                                                    ->pluck('attribute_values.value')
                                                    ->toArray();
                                                $vName = implode(' - ', $attrs);
                                            @endphp
                                            <option value="{{ $v->id }}" {{ $item['variant_id'] == $v->id ? 'selected' : '' }}>
                                                Size/Màu: {{ $vName }}
                                            </option>
                                        @endforeach
                                    </select>
                                @else
                                    @if($item['variant_info'])
                                        <span class="stext-111 cl6 bg-light p-all-5" style="border: 1px solid #ddd; border-radius: 4px;">{{ $item['variant_info'] }}</span>
                                    @endif
                                @endif
                            </div>

                            <div class="stext-111" style="color: #999;">
                                {{ number_format($item['price']) }}đ 
                                <span class="m-l-10" style="color: #28a745; font-size: 12px; font-weight: 500;">(Còn {{ $item['max_stock'] }} sản phẩm)</span>
                            </div>
                        </div>

                        <div class="flex-col-sb-m text-right" style="align-items: flex-end;">
                            <span class="mtext-104 cl2 font-weight-bold item-total-price" style="font-size: 16px;">
                                {{ number_format($item['price'] * $item['quantity']) }}đ
                            </span>
                            
                            <div class="flex-w m-t-20" style="width: 90px; height: 30px; border: 1px solid #e6e6e6; border-radius: 4px;">
                                <div class="cl8 hov-bg-light trans-04 flex-c-m js-update-cart" data-action="minus" style="width: 28px; height: 100%; cursor: pointer;">
                                    <i class="fs-12 zmdi zmdi-minus"></i>
                                </div>
                                <input class="mtext-104 cl3 txt-center input-qty" type="number" value="{{ $item['quantity'] }}" readonly style="width: 32px; height: 100%; font-size: 13px; background: transparent; border-left: 1px solid #e6e6e6; border-right: 1px solid #e6e6e6; padding: 0;">
                                <div class="cl8 hov-bg-light trans-04 flex-c-m js-update-cart" data-action="plus" data-max="{{ $item['max_stock'] }}" style="width: 28px; height: 100%; cursor: pointer;">
                                    <i class="fs-12 zmdi zmdi-plus"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach

                
            @endif
        </div>

        <div class="col-lg-4 col-xl-4 m-b-30">
            <div class="p-all-25 bg0 shadow-sm" style="border: 1px solid #f0f0f0; border-radius: 8px;">
                <h4 class="mtext-105 cl2 p-b-20" style="font-size: 18px; font-weight: 600;">Thông tin đơn hàng</h4>
                
                <div class="flex-w flex-sb-m p-b-15 p-t-15" style="border-top: 1px solid #e6e6e6;">
                    <span class="mtext-101 cl2" style="font-size: 16px; font-weight: bold;">Tổng tiền:</span>
                    <span class="mtext-101 text-danger font-weight-bold cart-total-price" style="font-size: 24px;">{{ number_format($total) }}đ</span>
                </div>
                
                <p class="stext-111 cl6 p-b-20" style="font-size: 13px;">
                    • Phí vận chuyển sẽ được tính ở trang thanh toán.
                </p>
                
                @if(count($cart) > 0)
                    <a href="{{ route('checkout.index') }}" class="flex-c-m stext-101 cl0 size-116 bg-danger bor1 hov-btn1 p-lr-15 trans-04 pointer w-full" style="font-weight: bold; border-radius: 4px; height: 45px;">
                        THANH TOÁN
                    </a>
                @else
                    <button disabled class="flex-c-m stext-101 cl0 size-116 bg-secondary bor1 p-lr-15 trans-04 w-full" style="font-weight: bold; border-radius: 4px; height: 45px; cursor: not-allowed;">
                        THANH TOÁN
                    </button>
                @endif
            </div>
            
            </div>
    </div>
</div>

<style>
    .js-remove-cart:hover { background-color: #dc3545 !important; border-color: #dc3545 !important; transform: scale(1.1); }
    .hov-bg-light:hover { background-color: #f8f9fa; }
</style>

@push('scripts')
<script>
    $(document).ready(function() {

        // 1. TĂNG GIẢM SỐ LƯỢNG MƯỢT MÀ
        $('.js-update-cart').on('click', function() {
            var btn = $(this);
            var row = btn.closest('.cart-item-row');
            var key = row.data('key');
            var input = row.find('.input-qty');
            var currentQty = parseInt(input.val());
            var action = btn.data('action');
            var maxStock = parseInt(btn.data('max')) || 999;

            var newQty = currentQty;
            if(action === 'plus') {
                if(currentQty >= maxStock) {
                    swal("Cảnh báo", "Sản phẩm này chỉ còn " + maxStock + " cái trong kho!", "warning");
                    return;
                }
                newQty = currentQty + 1;
            } else if(action === 'minus') {
                if(currentQty <= 1) return; // CHẶN KHÔNG CHO GIẢM XUỐNG 0 (Đã hoạt động vì gỡ class theme)
                newQty = currentQty - 1;
            }

            // Giao diện nhảy số lập tức
            input.val(newQty);
            btn.closest('.wrap-num-product').css('opacity', '0.5').css('pointer-events', 'none');

            // Bắn AJAX ngầm
            $.ajax({
                url: '{{ route("cart.update") }}',
                type: 'POST',
                data: { _token: '{{ csrf_token() }}', cart_key: key, quantity: newQty },
                success: function(res) {
                    btn.closest('.wrap-num-product').css('opacity', '1').css('pointer-events', 'auto');
                    if(res.success) {
                        row.find('.item-total-price').text(res.item_total);
                        $('.cart-total-price').text(res.total);
                        $('#cart-count-page').text(res.cart_count);
                        $('.icon-header-noti.js-show-cart').attr('data-notify', res.cart_count);
                        if(typeof loadCartDropdown === 'function') loadCartDropdown();
                    } else {
                        input.val(currentQty); // Lỗi thì trả lại số cũ
                        swal("Cảnh báo", res.msg, "warning");
                    }
                },
                error: function() {
                    input.val(currentQty);
                    btn.closest('.wrap-num-product').css('opacity', '1').css('pointer-events', 'auto');
                    swal("Lỗi", "Lỗi kết nối Server!", "error");
                }
            });
        });

        // 2. XÓA SẢN PHẨM VỚI SWEETALERT
        $('.js-remove-cart').on('click', function() {
            var btn = $(this);
            var row = btn.closest('.cart-item-row');
            var key = row.data('key');

            swal({
                title: "Xóa sản phẩm?",
                text: "Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?",
                icon: "warning",
                buttons: ["Hủy", "Xóa"],
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    $.ajax({
                        url: '{{ route("cart.remove") }}',
                        type: 'POST',
                        data: { _token: '{{ csrf_token() }}', cart_key: key },
                        success: function(res) {
                            if(res.success) {
                                if(res.cart_count == 0) {
                                    location.reload(); 
                                } else {
                                    row.slideUp(300, function() { $(this).remove(); });
                                    $('.cart-total-price').text(res.total);
                                    $('#cart-count-page').text(res.cart_count);
                                    $('.icon-header-noti.js-show-cart').attr('data-notify', res.cart_count);
                                    if(typeof loadCartDropdown === 'function') loadCartDropdown();
                                }
                            }
                        }
                    });
                }
            });
        });

        // 3. ĐỔI PHÂN LOẠI TRỰC TIẾP TRONG GIỎ HÀNG (THUẬT TOÁN THÔNG MINH)
        $('.variant-changer').on('change', function() {
            var select = $(this);
            var oldKey = select.data('old-key');
            var pid = select.data('pid');
            var newVid = select.val();
            var qty = select.data('qty');

            // Nếu khách chọn lại đúng cái cũ thì bỏ qua
            if(oldKey.split('_')[1] == newVid) return; 

            // Khóa nút select tránh spam
            select.prop('disabled', true);

            // BƯỚC 1: Thêm biến thể mới vào giỏ
            $.ajax({
                url: '{{ route("cart.add") }}',
                type: 'POST',
                data: { _token: '{{ csrf_token() }}', product_id: pid, variant_id: newVid, quantity: qty },
                success: function(res) {
                    if(res.success) {
                        // BƯỚC 2: Xóa biến thể cũ đi
                        $.ajax({
                            url: '{{ route("cart.remove") }}',
                            type: 'POST',
                            data: { _token: '{{ csrf_token() }}', cart_key: oldKey },
                            success: function() {
                                // Load lại trang để cập nhật giao diện
                                location.reload();
                            }
                        });
                    }
                },
                error: function(err) {
                    swal("Lỗi", "Không thể đổi phân loại. Có thể mẫu này đã hết hàng!", "error");
                    // Trả lại giá trị select như cũ
                    select.val(oldKey.split('_')[1]);
                    select.prop('disabled', false);
                }
            });
        });

    });
</script>
@endpush
@endsection