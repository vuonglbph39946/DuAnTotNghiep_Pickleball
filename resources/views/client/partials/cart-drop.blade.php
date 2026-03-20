<style>
    /* 1. ÉP BUỘC PARENT KHÔNG BỊ BUNG LAYOUT (CHỐNG BIẾN DẠNG) */
    #cart-dropdown-content {
        display: flex !important;
        flex-direction: column !important;
        flex-wrap: nowrap !important;
        height: calc(100vh - 100px) !important;
        overflow: hidden !important; 
        padding-top: 10px !important;
    }

    /* 2. KHU VỰC CUỘN DANH SÁCH SẢN PHẨM */
    .cart-scroll-area {
        flex: 1 1 auto !important;
        overflow-y: auto !important;
        overflow-x: hidden !important;
        padding-right: 15px;
        margin-right: -5px; 
    }

    /* Làm mượt thanh cuộn */
    .cart-scroll-area::-webkit-scrollbar { width: 4px; }
    .cart-scroll-area::-webkit-scrollbar-track { background: transparent; }
    .cart-scroll-area::-webkit-scrollbar-thumb { background: #d1d1d1; border-radius: 10px; }
    .cart-scroll-area::-webkit-scrollbar-thumb:hover { background: #aaa; }

    /* 3. KHU VỰC FOOTER CỐ ĐỊNH Ở ĐÁY */
    .cart-footer-fixed {
        flex: 0 0 auto !important;
        background: #fff;
        padding-top: 15px;
        border-top: 1px solid #e6e6e6;
        margin-top: 10px;
        padding-bottom: 20px;
    }

    /* 4. CHỈNH LẠI ITEM */
    .mini-cart-item {
        display: flex;
        position: relative;
        padding-bottom: 15px;
        margin-bottom: 15px;
        border-bottom: 1px solid #f5f5f5;
    }
    .mini-cart-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
    }
    
    /* ĐÃ SỬA: Biến khung ảnh thành thẻ block để bọc link */
    .mini-cart-img {
        display: block;
        width: 70px;
        height: 70px;
        flex-shrink: 0;
        border-radius: 4px;
        overflow: hidden;
        border: 1px solid #eee;
        margin-right: 15px;
        background: #f7f7f7;
    }
    .mini-cart-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    .mini-cart-img:hover img {
        transform: scale(1.1); /* Hiệu ứng hover cho ảnh */
    }

    .mini-cart-info {
        flex: 1;
        min-width: 0;
        padding-right: 25px; /* Chừa chỗ cho nút X */
    }
    .mini-cart-title {
        display: block;
        font-size: 13px;
        color: #333;
        font-weight: 600;
        line-height: 1.4;
        margin-bottom: 4px;
        text-transform: uppercase;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .mini-cart-variant {
        display: block;
        font-size: 12px;
        color: #888;
        margin-bottom: 10px;
    }
    .mini-cart-bottom {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .mini-cart-price {
        font-size: 14px;
        font-weight: bold;
        color: #222;
    }
    
    /* Nút Xóa (X) */
    .btn-remove-mini {
        position: absolute;
        top: -4px;
        right: -5px;
        color: #999;
        font-size: 18px;
        cursor: pointer;
        padding: 5px;
        transition: 0.3s;
        z-index: 10;
    }
    .btn-remove-mini:hover {
        color: #dc3545;
        transform: scale(1.1);
    }
</style>

<div class="cart-scroll-area">
    <ul class="w-full">
        @if(empty($cart) || count($cart) == 0)
            <li class="text-center p-t-40 p-b-40 w-full text-muted" style="font-size: 15px;">
                <i class="zmdi zmdi-shopping-cart-plus d-block m-b-10" style="font-size: 40px; opacity: 0.3;"></i>
                Giỏ hàng của bạn đang trống!
            </li>
        @else
            @foreach($cart as $key => $item)
            <li class="mini-cart-item">
                <div class="btn-remove-mini js-remove-cart-mini" data-key="{{ $key }}" title="Xóa khỏi giỏ">
                    <i class="zmdi zmdi-close"></i>
                </div>

                <a href="{{ url('product/' . ($item['slug'] ?? '')) }}" class="mini-cart-img">
                    <img src="{{ asset($item['image']) }}" alt="{{ $item['name'] }}">
                </a>

                <div class="mini-cart-info">
                    <a href="{{ url('product/' . ($item['slug'] ?? '')) }}" class="mini-cart-title hov-cl1 trans-04">
                        {{ $item['name'] }}
                    </a>
                    
                    <span class="mini-cart-variant">
                        {{ $item['variant_info'] ? str_replace('Phân loại: ', '', $item['variant_info']) : '' }}
                    </span>

                    <div class="mini-cart-bottom">
                        <div class="wrap-num-product flex-w" style="width: 75px; height: 26px; border: 1px solid #e6e6e6; border-radius: 3px;">
                            <div class="cl8 hov-btn3 trans-04 flex-c-m js-update-cart-mini" data-action="minus" data-key="{{ $key }}" style="width: 23px; height: 100%; cursor: pointer;">
                                <i class="fs-12 zmdi zmdi-minus"></i>
                            </div>

                            <input class="mtext-104 cl3 txt-center num-product js-qty-mini" type="number" value="{{ $item['quantity'] }}" readonly style="width: 27px; height: 100%; font-size: 12px; padding: 0; background: transparent; border-left: 1px solid #e6e6e6; border-right: 1px solid #e6e6e6;">

                            <div class="cl8 hov-btn3 trans-04 flex-c-m js-update-cart-mini" data-action="plus" data-key="{{ $key }}" data-max="{{ $item['max_stock'] }}" style="width: 23px; height: 100%; cursor: pointer;">
                                <i class="fs-12 zmdi zmdi-plus"></i>
                            </div>
                        </div>

                        <span class="mini-cart-price">
                            {{ number_format($item['price']) }}đ
                        </span>
                    </div>
                </div>
            </li>
            @endforeach
        @endif
    </ul>
</div>

<div class="cart-footer-fixed">
    <div class="flex-w flex-sb-m p-b-15">
        <span class="stext-105 cl3" style="text-transform: uppercase;">Tổng tiền tạm tính:</span>
        <span class="text-danger font-weight-bold" style="font-size: 18px;">{{ number_format($total ?? 0) }}đ</span>
    </div>

    <div class="header-cart-buttons flex-w w-full" style="flex-direction: column; gap: 10px;">
        <a href="{{ route('cart.index') }}" class="flex-c-m stext-101 cl0 size-107 bg3 bor2 hov-btn3 p-lr-15 trans-04 w-full" style="height: 45px; border-radius: 3px; font-weight: bold;">
            XEM GIỎ HÀNG
        </a>

        <a href="{{ route('checkout.index') }}" class="flex-c-m stext-101 cl0 size-107 bg-danger bor1 hov-btn1 p-lr-15 trans-04 w-full" style="height: 45px; border-radius: 3px; font-weight: bold;">
            THANH TOÁN
        </a>
    </div>
</div>