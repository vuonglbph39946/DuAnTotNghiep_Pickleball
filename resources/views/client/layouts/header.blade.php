@php
    // Lấy các danh mục gốc (không có parent_id) kèm theo danh mục con
    $menus = \App\Models\Category::whereNull('parent_id')->where('status', 1)->with('children')->get();
@endphp

<header>
    <div class="container-menu-desktop">
        <div class="wrap-menu-desktop" style="background-color: #ffffff; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            <nav class="limiter-menu-desktop container">
                
                <a href="{{ url('/') }}" class="logo">
                    <img src="{{ asset('client/images/icons/logo-shop-a.png') }}" alt="IMG-LOGO" style="max-height: 40px; width: auto;">
                </a>

                <div class="menu-desktop">
                    <ul class="main-menu">
                        <li class="{{ request()->is('/') ? 'active-menu' : '' }}">
                            <a href="{{ url('/') }}">Trang chủ</a>
                        </li>
                        @foreach($menus as $menu)
                            <li>
                                <a href="{{ url('category/' . $menu->slug) }}">{{ $menu->name }}</a>
                                @if($menu->children->count() > 0)
                                    <ul class="sub-menu">
                                        @foreach($menu->children as $child)
                                            <li><a href="{{ url('category/' . $child->slug) }}">{{ $child->name }}</a></li>
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                        @endforeach
                        <li><a href="{{ url('/gioi-thieu') }}">Giới thiệu</a></li>
                        <li><a href="{{ url('/contact') }}">Liên hệ</a></li>
                    </ul>
                </div>  

                <div class="wrap-icon-header flex-w flex-r-m" style="position: relative;">
                    <div class="icon-header-item cl2 hov-cl1 trans-04 p-l-22 p-r-11 js-toggle-custom-search pointer">
                        <i class="zmdi zmdi-search"></i>
                    </div>

                    <div class="custom-search-dropdown js-search-dropdown">
                        <h4 class="text-center p-b-15" style="font-size: 16px; color: #555; font-weight: normal; letter-spacing: 1px;">TÌM KIẾM</h4>
                        
                        <form action="{{ url('/search') }}" method="GET" style="position: relative;">
                            <input class="stext-103 cl2 plh3 size-116 p-l-15 p-r-40 bor8 search-input-ajax" type="text" name="keyword" placeholder="Tìm kiếm sản phẩm..." required style="background: #f7f7f7; border: 1px solid #eee; height: 45px; width: 100%;">
                            <button type="submit" class="flex-c-m size-122 ab-t-r fs-18 cl4 hov-cl1 trans-04" style="position: absolute; right: 0; top: 0; height: 100%; width: 45px; background: transparent; border: none; cursor: pointer;">
                                <i class="zmdi zmdi-search" style="font-size: 22px; color: #888;"></i>
                            </button>
                        </form>

                        <div class="search-suggestions js-search-results" style="display: none; max-height: 350px; overflow-y: auto; margin-top: 15px; border-top: 1px solid #f0f0f0; padding-top: 10px;">
                        </div>
                    </div>

                    <div class="icon-header-item cl2 hov-cl1 trans-04 p-l-22 p-r-11 js-toggle-account pointer" style="position: relative;">
                        <i class="zmdi zmdi-account"></i>
                        
                        <div class="custom-account-dropdown js-account-dropdown">
                            <ul class="p-t-5 p-b-5" style="margin: 0;">
                                @guest
                                <li>
                                    <a href="{{ url('/login') }}" class="stext-107 trans-04 p-tb-8 dis-block hov-cl-red" style="color: #fff; border-bottom: 1px solid rgba(255,255,255,0.1);">
                                        Đăng nhập
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ url('/register') }}" class="stext-107 trans-04 p-t-8 p-b-4 dis-block hov-cl-red" style="color: #fff;">
                                        Đăng ký
                                    </a>
                                </li>
                                @endguest

                                @auth
                                <li>
                                    <a href="{{ route('account.index') }}" class="stext-107 trans-04 p-tb-8 dis-block hov-cl-red" style="color: #fff; border-bottom: 1px solid rgba(255,255,255,0.1);">
                                        Tài khoản
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('logout') }}" class="stext-107 trans-04 p-t-8 p-b-4 dis-block hov-cl-red" style="color: #fff;">
                                        Đăng xuất
                                    </a>
                                </li>
                                @endauth
                            </ul>
                        </div>
                    </div>

                    <div class="icon-header-item cl2 hov-cl1 trans-04 p-l-22 p-r-11 icon-header-noti js-show-cart" data-notify="{{ array_sum(array_column(session('cart', []), 'quantity')) }}">
                        <i class="zmdi zmdi-shopping-cart"></i>
                    </div>

                    <a href="#" class="dis-block icon-header-item cl2 hov-cl1 trans-04 p-l-22 p-r-11 icon-header-noti" data-notify="0">
                        <i class="zmdi zmdi-favorite-outline"></i>
                    </a>
                </div>
            </nav>
        </div>  
    </div>

    <div class="wrap-header-mobile">
        <div class="logo-mobile">
            <a href="{{ url('/') }}">
                <img src="{{ asset('client/images/icons/logo-shop-a.png') }}" alt="IMG-LOGO" style="max-height: 35px; width: auto;">
            </a>
        </div>
        <div class="wrap-icon-header flex-w flex-r-m m-r-15" style="position: relative;">
            
            <div class="icon-header-item cl2 hov-cl1 trans-04 p-r-11 js-toggle-custom-search pointer">
                <i class="zmdi zmdi-search"></i>
            </div>
            <div class="custom-search-dropdown js-search-dropdown mobile-search-dropdown">
                <h4 class="text-center p-b-15" style="font-size: 16px; color: #555; font-weight: normal; letter-spacing: 1px;">TÌM KIẾM</h4>
                <form action="{{ url('/search') }}" method="GET" style="position: relative;">
                    <input class="stext-103 cl2 plh3 size-116 p-l-15 p-r-40 bor8 search-input-ajax" type="text" name="keyword" placeholder="Tìm kiếm sản phẩm..." required style="background: #f7f7f7; border: 1px solid #eee; height: 45px; width: 100%;">
                    <button type="submit" class="flex-c-m size-122 ab-t-r fs-18 cl4 hov-cl1 trans-04" style="position: absolute; right: 0; top: 0; height: 100%; width: 45px; background: transparent; border: none; cursor: pointer;">
                        <i class="zmdi zmdi-search" style="font-size: 22px; color: #888;"></i>
                    </button>
                </form>
                <div class="search-suggestions js-search-results" style="display: none; max-height: 300px; overflow-y: auto; margin-top: 15px; border-top: 1px solid #f0f0f0; padding-top: 10px;">
                </div>
            </div>

            <div class="icon-header-item cl2 hov-cl1 trans-04 p-r-11 p-l-10 js-toggle-account pointer" style="position: relative;">
                <i class="zmdi zmdi-account"></i>
                <div class="custom-account-dropdown js-account-dropdown">
                    <ul class="p-t-5 p-b-5" style="margin: 0;">
                        @guest
                            <li><a href="{{ url('/login') }}" class="stext-107 trans-04 p-tb-8 dis-block hov-cl-red" style="color: #fff; border-bottom: 1px solid rgba(255,255,255,0.1);">Đăng nhập</a></li>
                            <li><a href="{{ url('/register') }}" class="stext-107 trans-04 p-t-8 p-b-4 dis-block hov-cl-red" style="color: #fff;">Đăng ký</a></li>
                        @endguest

                        @auth
                            <li><a href="{{ route('account.index') }}" class="stext-107 trans-04 p-tb-8 dis-block hov-cl-red" style="color: #fff; border-bottom: 1px solid rgba(255,255,255,0.1);">Tài khoản</a></li>
                            <li><a href="{{ route('logout') }}" class="stext-107 trans-04 p-t-8 p-b-4 dis-block hov-cl-red" style="color: #fff;">Đăng xuất</a></li>
                        @endauth
                    </ul>
                </div>
            </div>

            <div class="icon-header-item cl2 hov-cl1 trans-04 p-r-11 p-l-10 icon-header-noti js-show-cart" data-notify="{{ array_sum(array_column(session('cart', []), 'quantity')) }}">
                <i class="zmdi zmdi-shopping-cart"></i>
            </div>
            
            <a href="#" class="dis-block icon-header-item cl2 hov-cl1 trans-04 p-r-11 p-l-10 icon-header-noti" data-notify="0">
                <i class="zmdi zmdi-favorite-outline"></i>
            </a>
        </div>
        <div class="btn-show-menu-mobile hamburger hamburger--squeeze">
            <span class="hamburger-box">
                <span class="hamburger-inner"></span>
            </span>
        </div>
    </div>

    <div class="menu-mobile">
        <ul class="topbar-mobile">
            <li><div class="left-top-bar">Miễn phí giao hàng cho đơn từ 500k</div></li>
            <li>
                <div class="right-top-bar flex-w h-full">
                    <a href="#" class="flex-c-m p-lr-10 trans-04">Trợ giúp & FAQs</a>
                    <a href="{{ route('account.index') }}" class="flex-c-m p-lr-10 trans-04">Tài khoản</a>
                    <a href="#" class="flex-c-m p-lr-10 trans-04">VN</a>
                    <a href="#" class="flex-c-m p-lr-10 trans-04">VND</a>
                </div>
            </li>
        </ul>
        <ul class="main-menu-m">
            <li><a href="{{ url('/') }}">Trang chủ</a></li>
            @foreach($menus as $menu)
                <li>
                    <a href="{{ url('category/' . $menu->slug) }}">{{ $menu->name }}</a>
                    @if($menu->children->count() > 0)
                        <ul class="sub-menu-m">
                            @foreach($menu->children as $child)
                                <li><a href="{{ url('category/' . $child->slug) }}">{{ $child->name }}</a></li>
                            @endforeach
                        </ul>
                        <span class="arrow-main-menu-m"><i class="fa fa-angle-right" aria-hidden="true"></i></span>
                    @endif
                </li>
            @endforeach
            <li><a href="{{ url('/about') }}">Giới thiệu</a></li>
            <li><a href="{{ url('/contact') }}">Liên hệ</a></li>
        </ul>
    </div>
</header>

<div class="wrap-header-cart js-panel-cart">
    <div class="s-full js-hide-cart"></div>
    <div class="header-cart flex-col-l p-l-30 p-r-30 p-t-30 p-b-30" style="background-color: #fff; width: 420px; max-width: 100%;">
        
        <div class="header-cart-title flex-w flex-sb-m p-b-15" style="border-bottom: 1px solid #e6e6e6; width: 100%;">
            <span class="mtext-103 cl2" style="font-size: 20px; font-weight: 500;">
                Giỏ hàng
            </span>
            <div class="fs-35 lh-10 cl2 p-lr-5 pointer hov-cl1 trans-04 js-hide-cart">
                <i class="zmdi zmdi-close" style="font-size: 22px; font-weight: 300;"></i>
            </div>
        </div>
        
        <div class="header-cart-content flex-w js-pscroll p-t-20 w-full" id="cart-dropdown-content" style="height: calc(100vh - 220px); flex-direction: column;">
            <div class="text-center p-t-50 w-full"><i class="zmdi zmdi-spinner zmdi-hc-spin fs-30 text-muted"></i></div>
        </div>

    </div>
</div>

<style>
    /* CSS Hộp Tìm Kiếm */
    .custom-search-dropdown {
        position: absolute;
        top: calc(100% + 15px);
        right: 0;
        width: 380px;
        background: #fff;
        box-shadow: 0 5px 25px rgba(0,0,0,0.15);
        border: 1px solid #eee;
        border-radius: 5px;
        padding: 20px;
        z-index: 1000;
        visibility: hidden;
        opacity: 0;
        transform: translateY(20px); 
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .custom-search-dropdown.show {
        visibility: visible;
        opacity: 1;
        transform: translateY(0);
    }
    .custom-search-dropdown::before {
        content: ''; position: absolute; top: -8px; right: 25px; width: 15px; height: 15px;
        background: #fff; transform: rotate(45deg); border-top: 1px solid #eee; border-left: 1px solid #eee;
    }

    /* CSS Hộp Tài Khoản */
    .custom-account-dropdown {
        position: absolute;
        top: calc(100% + 15px);
        right: -20px;
        width: 150px;
        background: #2a2a2a; 
        border-radius: 5px;
        padding: 10px 20px;
        z-index: 1000;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        visibility: hidden;
        opacity: 0;
        transform: translateY(20px); 
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        text-align: left;
    }
    .custom-account-dropdown.show {
        visibility: visible;
        opacity: 1;
        transform: translateY(0);
    }
    .custom-account-dropdown::before {
        content: ''; position: absolute; top: -6px; right: 28px; width: 12px; height: 12px;
        background: #2a2a2a; transform: rotate(45deg);
    }
    .hov-cl-red:hover {
        color: #dc3545 !important;
    }
    
    @media (max-width: 576px) {
        .mobile-search-dropdown { width: 320px; right: -60px; }
        .mobile-search-dropdown::before { right: 80px; }
        
        .custom-account-dropdown { right: -15px; }
        .custom-account-dropdown::before { right: 22px; }
    }

    .search-suggestions::-webkit-scrollbar { width: 5px; }
    .search-suggestions::-webkit-scrollbar-thumb { background: #ccc; border-radius: 5px; }
    .hov-bg-gray:hover { background-color: #fcfcfc; }
    /* =================================================== */
    /* FIX LỖI TUỘT HEADER KHI VỪA LOAD TRANG */
    /* =================================================== */
    .wrap-menu-desktop {
        top: 0 !important; 
    }
    
    .container-menu-desktop {
        /* Giữ khung cứng 84px để nội dung bên dưới không bị giật lên khi cuộn */
        height: 84px !important; 
    }
</style>

@push('scripts')
<script>
    $(document).ready(function() {
        
        // 1. CLICK MỞ HỘP TÌM KIẾM
        $('.js-toggle-custom-search').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $('.js-account-dropdown').removeClass('show'); 
            var box = $(this).siblings('.js-search-dropdown');
            $('.js-search-dropdown').not(box).removeClass('show');
            box.toggleClass('show');
            if(box.hasClass('show')) {
                setTimeout(function() { box.find('.search-input-ajax').focus(); }, 100);
            }
        });

        // 2. CLICK MỞ HỘP TÀI KHOẢN
        $('.js-toggle-account').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $('.js-search-dropdown').removeClass('show'); 
            var box = $(this).find('.js-account-dropdown');
            $('.js-account-dropdown').not(box).removeClass('show'); 
            box.toggleClass('show');
        });

        // 3. ĐÓNG POP-UP
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.js-search-dropdown').length && !$(e.target).closest('.js-toggle-custom-search').length) {
                $('.js-search-dropdown').removeClass('show');
            }
            if (!$(e.target).closest('.js-account-dropdown').length && !$(e.target).closest('.js-toggle-account').length) {
                $('.js-account-dropdown').removeClass('show');
            }
        });
        $('.js-search-dropdown, .js-account-dropdown').on('click', function(e) { e.stopPropagation(); });

        // 4. TÌM KIẾM AJAX
        var searchTimeout;
        $('.search-input-ajax').on('input', function() {
            var input = $(this);
            var keyword = input.val().trim();
            var resultBox = input.closest('.js-search-dropdown').find('.js-search-results');

            clearTimeout(searchTimeout);

            if (keyword.length >= 2) { 
                resultBox.show().html('<div class="text-center p-t-10" style="color:#888;"><i class="zmdi zmdi-spinner zmdi-hc-spin"></i> Đang tìm kiếm...</div>');
                searchTimeout = setTimeout(function() {
                    $.ajax({
                        url: '{{ url("/api/search-suggest") }}',
                        type: 'GET',
                        data: { keyword: keyword },
                        success: function(res) {
                            if(res && res.length > 0) {
                                var html = '';
                                $.each(res, function(index, item) {
                                    html += `
                                    <a href="${item.url}" class="flex-w flex-m p-b-10 p-t-10 trans-04 hov-bg-gray" style="border-bottom: 1px solid #f5f5f5; text-decoration: none;">
                                        <div class="wrap-pic-w size-w-50 m-r-15" style="width: 50px;">
                                            <img src="${item.image}" alt="IMG" style="width: 100%; border-radius: 3px;">
                                        </div>
                                        <div class="size-w-flex1" style="flex: 1;">
                                            <h6 class="stext-105 cl3 hov-cl1 trans-04" style="font-size: 13px; font-weight: 500; color: #333; margin-bottom: 3px; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                                                ${item.name}
                                            </h6>
                                            <span class="stext-105" style="color: #dc3545; font-weight: bold; font-size: 14px;">
                                                ${item.price}
                                            </span>
                                        </div>
                                    </a>`;
                                });
                                resultBox.html(html);
                            } else {
                                resultBox.html('<div class="text-center p-t-15 stext-102" style="color:#888;">Không tìm thấy sản phẩm nào!</div>');
                            }
                        },
                        error: function() { resultBox.html('<div class="text-center p-t-15 stext-102" style="color:red;">Lỗi kết nối máy chủ!</div>'); }
                    });
                }, 500); 
            } else { resultBox.hide().empty(); }
        });

        // 5. GIỎ HÀNG AJAX
        window.loadCartDropdown = function() {
            $.ajax({
                url: '{{ route("cart.render") }}',
                type: 'GET',
                success: function(html) { $('#cart-dropdown-content').html(html); }
            });
        };
        loadCartDropdown();

        // 5.1 Xóa sản phẩm
        $(document).on('click', '.js-remove-cart-mini', function(e) {
            e.preventDefault();
            var key = $(this).data('key');
            $(this).css('opacity', '0.5');

            $.ajax({
                url: '{{ route("cart.remove") }}',
                type: 'POST',
                data: { _token: '{{ csrf_token() }}', cart_key: key },
                success: function(res) {
                    if(res.success) {
                        $('.icon-header-noti.js-show-cart').attr('data-notify', res.cart_count);
                        loadCartDropdown();
                        if(window.location.pathname === '/cart') { location.reload(); }
                    }
                }
            });
        });

        // 5.2 Tăng giảm số lượng 
        $(document).on('click', '.js-update-cart-mini', function(e) {
            e.preventDefault();
            var btn = $(this);
            var action = btn.data('action');
            var key = btn.data('key');
            var maxStock = parseInt(btn.data('max')) || 999;
            var input = btn.siblings('.js-qty-mini');
            var currentQty = parseInt(input.val());

            var newQty = currentQty;
            if(action === 'plus') {
                if(currentQty >= maxStock) {
                    swal("Cảnh báo", "Rất tiếc! Mẫu này chỉ còn " + maxStock + " sản phẩm trong kho.", "warning");
                    return;
                }
                newQty = currentQty + 1;
            } else if(action === 'minus') {
                if(currentQty <= 1) return; 
                newQty = currentQty - 1;
            }

            input.val(newQty);
            btn.closest('.wrap-num-product').css('opacity', '0.5').css('pointer-events', 'none');

            $.ajax({
                url: '{{ route("cart.update") }}',
                type: 'POST',
                data: { _token: '{{ csrf_token() }}', cart_key: key, quantity: newQty },
                success: function(res) {
                    if(res.success) {
                        // ĐÃ SỬA: Cập nhật luôn con số đỏ đỏ lúc bấm dấu + hoặc -
                        $('.icon-header-noti.js-show-cart').attr('data-notify', res.cart_count);
                        
                        loadCartDropdown();
                        if(window.location.pathname === '/cart') { location.reload(); }
                    } else {
                        input.val(currentQty); 
                        btn.closest('.wrap-num-product').css('opacity', '1').css('pointer-events', 'auto');
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

        // 5.3 Thêm giỏ hàng
        $(document).on('click', '.js-addcart', function(e) {
            e.preventDefault();
            var btn = $(this);
            var productId = btn.data('id');
            var variantId = $('input[name="variant_id"]').val() || null; 
            
            var qty = 1;
            var inputQty = $('.num-product').val();
            if(inputQty) qty = parseInt(inputQty);
            
            if(qty < 1) {
                swal("Lỗi", "Số lượng không hợp lệ!", "error"); return;
            }

            btn.addClass('is-loading');

            $.ajax({
                url: '{{ route("cart.add") }}',
                type: 'POST',
                data: { _token: '{{ csrf_token() }}', product_id: productId, variant_id: variantId, quantity: qty },
                success: function(res) {
                    btn.removeClass('is-loading');
                    if(res.success) {
                        $('.icon-header-noti.js-show-cart').attr('data-notify', res.cart_count);
                        loadCartDropdown();
                        $('.js-panel-cart').addClass('show-header-cart');
                    }
                },
                error: function(err) {
                    btn.removeClass('is-loading');
                    if(err.responseJSON && err.responseJSON.error) {
                        swal("Lỗi", err.responseJSON.error, "warning"); 
                    } else {
                        swal("Lỗi", "Có lỗi xảy ra, vui lòng thử lại!", "error");
                    }
                }
            });
        });
    });
</script>
@endpush