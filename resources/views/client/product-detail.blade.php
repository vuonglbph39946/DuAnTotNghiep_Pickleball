@extends('client.layouts.app')

@section('title', $product->name . ' - PBall Store')

@section('content')
<div class="container p-t-80">
    <div class="bread-crumb flex-w p-l-25 p-r-15 p-t-30 p-lr-0-lg">
        <a href="{{ url('/') }}" class="stext-109 cl8 hov-cl1 trans-04">
            Trang chủ
            <i class="fa fa-angle-right m-l-9 m-r-10" aria-hidden="true"></i>
        </a>
        <a href="{{ url('category/' . ($product->category->slug ?? '')) }}" class="stext-109 cl8 hov-cl1 trans-04">
            {{ $product->category->name ?? 'Danh mục' }}
            <i class="fa fa-angle-right m-l-9 m-r-10" aria-hidden="true"></i>
        </a>
        <span class="stext-109 cl4">
            {{ $product->name }}
        </span>
    </div>
</div>

<section class="sec-product-detail bg0 p-t-65 p-b-60">
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-lg-7 p-b-30">
                <div class="p-l-25 p-r-30 p-lr-0-lg">
                    <div class="wrap-slick3 flex-sb flex-w">
                        <div class="wrap-slick3-dots"></div>
                        <div class="wrap-slick3-arrows flex-sb-m flex-w"></div>

                        <div class="slick3 gallery-lb">
                            @if($product->images->count() > 0)
                                @foreach($product->images as $img)
                                <div class="item-slick3" data-thumb="{{ asset($img->image_path) }}">
                                    <div class="wrap-pic-w pos-relative">
                                        <img src="{{ asset($img->image_path) }}" alt="IMG-PRODUCT">
                                        <a class="flex-c-m size-108 how-pos1 bor0 fs-16 cl10 bg0 hov-btn3 trans-04" href="{{ asset($img->image_path) }}">
                                            <i class="fa fa-expand"></i>
                                        </a>
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <div class="item-slick3" data-thumb="{{ asset('client/images/product-01.jpg') }}">
                                    <div class="wrap-pic-w pos-relative">
                                        <img src="{{ asset('client/images/product-01.jpg') }}" alt="IMG-PRODUCT">
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            @php
                // ĐẢM BẢO CHẮC CHẮN CÓ VARIANT ID ĐỂ JS GỬI LÊN SERVER
                $variantData = [];
                $variants = \DB::table('product_variants')->where('product_id', $product->id)->get();
                foreach($variants as $v) {
                    $attrs = \DB::table('variant_attribute_values')->where('variant_id', $v->id)->pluck('attribute_value_id')->toArray();
                    sort($attrs);
                    $key = empty($attrs) ? 'default' : implode('_', $attrs);
                    $variantData[$key] = [
                        'id' => $v->id, // <-- CỰC KỲ QUAN TRỌNG ĐỂ AJAX CHẠY
                        'price' => number_format($v->price) . 'đ',
                        'sale_price' => $v->sale_price ? number_format($v->sale_price) . 'đ' : null,
                        'stock' => (int)$v->stock
                    ];
                }
            @endphp

            <div class="col-md-6 col-lg-5 p-b-30 custom-detail-block" data-variants="{{ json_encode($variantData) }}" data-default-stock="{{ $product->stock }}">
                <div class="p-r-50 p-t-5 p-lr-0-lg">
                    <h4 class="mtext-105 cl2 p-b-14 js-detail-name" style="text-transform: uppercase; font-weight: bold; font-size: 24px;">
                        {{ $product->name }}
                    </h4>

                    <p class="stext-102 cl3 p-b-20">
                        Tình trạng: <span class="js-detail-stock" style="color: #222; font-weight: bold;">Đang tải...</span>
                    </p>

                    <div class="flex-w flex-l-m p-b-30" style="gap: 10px;">
                        <span class="mtext-106 js-detail-price" style="color: red; font-weight: bold; font-size: 28px;">
                            @if($product->sale_price)
                                {{ number_format($product->sale_price) }}đ
                                <del style="color: #999; font-size: 18px; margin-left: 10px; font-weight: normal;">{{ number_format($product->price) }}đ</del>
                            @else
                                {{ number_format($product->price) }}đ
                            @endif
                        </span>
                    </div>

                    @if(!empty($product->short_description))
                    <p class="stext-102 cl3 p-b-30">
                        {{ $product->short_description }}
                    </p>
                    @endif
                    
                    <div class="p-t-10">
                        @if($colors->count() > 0)
                        <div class="flex-w flex-r-m p-b-20">
                            <div class="size-203 flex-c-m respon6" style="justify-content: flex-start; font-weight: 600; color: #333;">Màu sắc:</div>
                            <div class="size-204 flex-w">
                                @foreach($colors as $color)
                                <label class="color-variant-box tooltip100" data-tooltip="{{ $color->value }}">
                                    <input type="radio" class="js-variant-input" name="color" value="{{ $color->id }}">
                                    <span class="color-swatch" style="background-color: {{ $color->color_code ?? '#ccc' }};" title="{{ $color->value }}"></span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        @if($sizes->count() > 0)
                        <div class="flex-w flex-r-m p-b-20">
                            <div class="size-203 flex-c-m respon6" style="justify-content: flex-start; font-weight: 600; color: #333;">Kích thước:</div>
                            <div class="size-204 flex-w">
                                @foreach($sizes as $size)
                                <label class="variant-box">
                                    <input type="radio" class="js-variant-input" name="size" value="{{ $size->id }}">
                                    <span>{{ $size->value }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        @if($colors->count() > 0 || $sizes->count() > 0)
                        <div class="flex-w p-b-10">
                            <a href="#" class="js-clear-variants stext-104 cl6 hov-cl1 trans-04" style="text-decoration: underline; font-size: 13px; color: #888;">
                                ✖ Bỏ chọn
                            </a>
                        </div>
                        @endif

                        <div class="flex-w flex-r-m p-b-10">
                            <div class="size-203 flex-c-m respon6" style="justify-content: flex-start; font-weight: 600; color: #333;">Số lượng:</div>
                            <div class="size-204 flex-w flex-m respon6-next">
                                <div class="wrap-num-product flex-w m-r-20 m-tb-10">
                                    <div class="btn-num-product-down cl8 hov-btn3 trans-04 flex-c-m"><i class="fs-16 zmdi zmdi-minus"></i></div>
                                    <input class="mtext-104 cl3 txt-center num-product" type="number" name="num-product" value="1" data-max="{{ $product->stock }}">
                                    <div class="btn-num-product-up cl8 hov-btn3 trans-04 flex-c-m"><i class="fs-16 zmdi zmdi-plus"></i></div>
                                </div>
                            </div>
                        </div>  
                        
                        <div class="flex-w flex-m p-t-20" style="gap: 10px;">
                            <button class="flex-c-m stext-101 cl0 bor1 p-lr-15 trans-04 js-addcart-page" data-id="{{ $product->id }}" style="flex: 1; height: 55px; font-weight: bold; border-radius: 3px; font-size: 16px; background-color: #333; color: white;">
                                THÊM VÀO GIỎ
                            </button>
                            <button class="flex-c-m stext-101 cl0 bor1 p-lr-15 trans-04 js-buynow-page" data-id="{{ $product->id }}" style="flex: 1; height: 55px; font-weight: bold; border-radius: 3px; font-size: 16px; background-color: #dc3545; color: white;">
                                MUA NGAY
                            </button>
                        </div>
                    </div>

                    <div class="flex-w flex-m p-t-40">
                        <div class="flex-m bor9 p-r-10 m-r-11">
                            <a href="#" class="fs-18 cl3 hov-cl1 trans-04 lh-10 p-lr-5 p-tb-2 js-addwish-custom tooltip100" data-tooltip="Yêu thích">
                                <i class="zmdi zmdi-favorite-outline"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bor10 m-t-50 p-t-43 p-b-40">
            <div class="tab01">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item p-b-10">
                        <a class="nav-link active" data-toggle="tab" href="#description" role="tab">Mô tả sản phẩm</a>
                    </li>
                    <li class="nav-item p-b-10">
                        <a class="nav-link" data-toggle="tab" href="#reviews" role="tab">Đánh giá (0)</a>
                    </li>
                </ul>

                <div class="tab-content p-t-43">
                    <div class="tab-pane fade show active" id="description" role="tabpanel">
                        <div class="how-pos2 p-lr-15-md">
                            <p class="stext-102 cl6">
                                {!! $product->description ?? 'Nội dung chi tiết đang được cập nhật...' !!}
                            </p>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="reviews" role="tabpanel">
                        <div class="row">
                            <div class="col-sm-10 col-md-8 col-lg-6 m-lr-auto">
                                <p class="stext-102 cl6 txt-center">Chưa có đánh giá nào.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@if(isset($relatedProducts) && $relatedProducts->count() > 0)
<section class="sec-relate-product bg0 p-t-45 p-b-105">
    <div class="container">
        <div class="p-b-45">
            <h3 class="ltext-106 cl5 txt-center">Sản phẩm liên quan</h3>
        </div>

        <div class="wrap-slick2">
            <div class="slick2">
                @foreach($relatedProducts as $relate)
                <div class="item-slick2 p-l-15 p-r-15 p-t-15 p-b-15">
                    <div class="block2">
                        <div class="block2-pic hov-img0 pos-relative product-img-wrap" style="background-color: #f7f7f7;">
                            <a href="{{ url('product/' . $relate->slug) }}">
                                <img src="{{ asset($relate->images->first()->image_path ?? 'client/images/product-01.jpg') }}" alt="{{ $relate->name }}" class="main-img">
                                @if($relate->images->count() > 1)
                                    <img src="{{ asset($relate->images[1]->image_path) }}" alt="{{ $relate->name }}" class="hover-img">
                                @endif
                            </a>
                        </div>
                        <div class="block2-txt flex-w flex-t p-t-14">
                            <div class="block2-txt-child1 flex-col-l w-full">
                                <a href="{{ url('product/' . $relate->slug) }}" class="stext-104 cl4 hov-cl1 trans-04 js-name-b2 p-b-10" style="text-transform: uppercase; font-size: 13px; font-weight: 500; color: #333;">
                                    {{ $relate->name }}
                                </a>
                                <span class="stext-105 cl3" style="font-weight: 700; color: #111; font-size: 14px;">
                                    {{ number_format($relate->sale_price ?? $relate->price) }}đ
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif

<div class="sticky-cart-bar" id="stickyCartBar">
    <div class="container flex-w flex-sb-m p-t-10 p-b-10" style="align-items: center; flex-wrap: nowrap;">
        <div class="flex-w flex-m hidden-mobile" style="gap: 15px; flex: 1;">
            <img src="{{ asset($product->images->first()->image_path ?? 'client/images/product-01.jpg') }}" alt="IMG" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px; border: 1px solid #eee;">
            <div>
                <h4 class="stext-105 cl2" style="font-weight: bold; font-size: 14px; max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $product->name }}</h4>
                <span class="mtext-106 js-sticky-price" style="color: red; font-size: 16px; font-weight: bold;">
                    @if($product->sale_price) {{ number_format($product->sale_price) }}đ @else {{ number_format($product->price) }}đ @endif
                </span>
            </div>
        </div>

        <div class="flex-w flex-m sticky-controls-wrapper" style="gap: 12px; justify-content: flex-end;">
            @if($colors->count() > 0)
            <div class="flex-m">
                <span class="stext-102 cl3 m-r-10 hidden-mobile" style="font-weight:600;">Màu:</span>
                <select class="stext-111 cl2 plh3 p-l-10 p-r-10 bor8 js-sticky-color" style="height: 40px; border-radius: 3px; border: 1px solid #ccc; outline: none; background: #fff; min-width: 110px;">
                    <option value="">Chọn màu</option>
                    @foreach($colors as $color)
                        <option value="{{ $color->id }}">{{ $color->value }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            @if($sizes->count() > 0)
            <div class="flex-m">
                <span class="stext-102 cl3 m-r-10 hidden-mobile" style="font-weight:600;">Size:</span>
                <select class="stext-111 cl2 plh3 p-l-10 p-r-10 bor8 js-sticky-size" style="height: 40px; border-radius: 3px; border: 1px solid #ccc; outline: none; background: #fff; min-width: 110px;">
                    <option value="">Chọn size</option>
                    @foreach($sizes as $size)
                        <option value="{{ $size->id }}">{{ $size->value }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <div class="wrap-num-product flex-w hidden-mobile" style="width: 110px; height: 40px; border: 1px solid #e6e6e6; border-radius: 3px; background: #fff;">
                <div class="btn-num-product-down-sticky cl8 hov-btn3 trans-04 flex-c-m" style="width: 30px; cursor: pointer;"><i class="fs-16 zmdi zmdi-minus"></i></div>
                <input class="mtext-104 cl3 txt-center num-product-sticky" type="number" value="1" style="width: 48px; border-left: 1px solid #e6e6e6; border-right: 1px solid #e6e6e6; outline: none;">
                <div class="btn-num-product-up-sticky cl8 hov-btn3 trans-04 flex-c-m" style="width: 30px; cursor: pointer;"><i class="fs-16 zmdi zmdi-plus"></i></div>
            </div>

            <button class="flex-c-m stext-101 cl0 bor1 p-lr-15 trans-04 js-sticky-addcart" style="height: 40px; font-weight: bold; border-radius: 3px; background-color: #333; color: white;">
                THÊM VÀO GIỎ
            </button>
            <button class="flex-c-m stext-101 cl0 bor1 p-lr-15 trans-04 js-sticky-buynow" style="height: 40px; font-weight: bold; border-radius: 3px; background-color: #dc3545; color: white;">
                MUA NGAY
            </button>
        </div>
    </div>
</div>

<style>
    .variant-box { margin-right: 10px; margin-bottom: 10px; cursor: pointer; }
    .variant-box input { display: none; }
    .variant-box span { display: inline-block; padding: 6px 15px; border: 1px solid #ccc; border-radius: 3px; font-size: 13px; color: #555; transition: 0.3s; }
    .variant-box input:checked + span { border-color: #222; color: #222; font-weight: bold; position: relative; }
    .variant-box input:checked + span::after { content: '\2713'; position: absolute; top: -1px; right: -1px; background: #222; color: white; font-size: 10px; padding: 0 3px; }

    .color-variant-box { margin-right: 12px; margin-bottom: 10px; cursor: pointer; position: relative; display: inline-block; }
    .color-variant-box input { display: none; }
    .color-swatch { display: block; width: 30px; height: 30px; border-radius: 50%; border: 2px solid #e6e6e6; transition: 0.3s; position: relative; }
    .color-variant-box input:checked + .color-swatch { border-color: #222; transform: scale(1.1); box-shadow: 0 0 4px rgba(0,0,0,0.3); }
    .color-variant-box input:checked + .color-swatch::after { content: '\2713'; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: white; font-size: 14px; text-shadow: 0px 0px 3px rgba(0,0,0,0.8); font-weight: bold; }

    .product-img-wrap .hover-img { position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; opacity: 0; visibility: hidden; transition: opacity 0.4s ease, visibility 0.4s ease; }
    .product-img-wrap:hover .hover-img { opacity: 1; visibility: visible; }

    .variant-box.is-disabled, 
    .color-variant-box.is-disabled {
        pointer-events: none !important; 
        cursor: not-allowed !important;
    }
    .variant-box.is-disabled span { opacity: 0.4; position: relative; background-color: #f9f9f9; border-color: #e0e0e0; }
    .variant-box.is-disabled span::before { content: ''; position: absolute; top: 50%; left: 0; right: 0; height: 1.5px; background: #999; transform: rotate(-20deg); }
    .color-variant-box.is-disabled .color-swatch { opacity: 0.3; }
    .color-variant-box.is-disabled::before { content: '✖'; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: #555; font-size: 15px; z-index: 10; pointer-events: none; }

    .sticky-cart-bar {
        position: fixed; bottom: -100px; left: 0; width: 100%;
        background: #fff; box-shadow: 0 -3px 15px rgba(0,0,0,0.1);
        z-index: 1000; transition: bottom 0.4s ease-in-out; border-top: 1px solid #eaeaea;
    }
    .sticky-cart-bar.show { bottom: 0; }
    @media (max-width: 768px) {
        .hidden-mobile { display: none !important; }
        .sticky-controls-wrapper { width: 100%; justify-content: center; }
    }
</style>

@push('scripts')
<script>
    $(document).ready(function() {
        var detailBlock = $('.custom-detail-block');

        $(window).on('scroll', function() {
            var detailOffset = detailBlock.offset().top + 300; 
            if ($(window).scrollTop() > detailOffset) {
                $('#stickyCartBar').addClass('show');
            } else {
                $('#stickyCartBar').removeClass('show');
            }
        });

        $(document).on('click', '.js-clear-variants', function(e) {
            e.preventDefault();
            detailBlock.find('input.js-variant-input').prop('checked', false).prop('disabled', false);
            $('.js-sticky-color').val('');
            $('.js-sticky-size').val('');
            detailBlock.find('input.js-variant-input').first().trigger('change');
        });

        $('.js-variant-input').on('change', function() {
            var variantData = detailBlock.data('variants'); 
            var groups = [];
            detailBlock.find('input.js-variant-input').each(function() {
                var name = $(this).attr('name');
                if(groups.indexOf(name) === -1) groups.push(name);
            });

            var selColor = detailBlock.find('input[name="color"]:checked').val() || '';
            var selSize = detailBlock.find('input[name="size"]:checked').val() || '';

            $('.js-sticky-color').val(selColor);
            $('.js-sticky-size').val(selSize);

            var selectedAttrs = [];
            if(selColor) selectedAttrs.push(parseInt(selColor));
            if(selSize) selectedAttrs.push(parseInt(selSize));
            
            selectedAttrs.sort(function(a, b){return a-b});
            var key = selectedAttrs.length > 0 ? selectedAttrs.join('_') : 'default';
            
            var btnCart = detailBlock.find('.js-addcart-page');
            var btnBuyNow = detailBlock.find('.js-buynow-page');
            var stickyBtnCart = $('.js-sticky-addcart');
            var stickyBtnBuyNow = $('.js-sticky-buynow');

            var inputQty = detailBlock.find('.num-product');
            var stockLabel = detailBlock.find('.js-detail-stock');

            if(groups.length > 0 && selectedAttrs.length === groups.length) {
                if(variantData && variantData[key]) {
                    var v = variantData[key];
                    
                    // LƯU LẠI VARIANT ID ĐỂ GỬI LÊN SERVER AJAX
                    detailBlock.data('selected-variant', v.id);

                    if(v.sale_price) {
                        var priceHtml = v.sale_price + ' <del style="color:#999; font-size:18px; margin-left:10px; font-weight:normal;">' + v.price + '</del>';
                        detailBlock.find('.js-detail-price').html(priceHtml);
                        $('.js-sticky-price').html(priceHtml); 
                    } else {
                        detailBlock.find('.js-detail-price').text(v.price);
                        $('.js-sticky-price').text(v.price);
                    }
                    
                    if(v.stock > 0) { 
                        stockLabel.text('Còn hàng (' + v.stock + ')').css('color', '#222');
                        inputQty.attr('data-max', v.stock);
                        if(parseInt(inputQty.val()) < 1 || isNaN(inputQty.val())) inputQty.val(1);
                        
                        btnCart.prop('disabled', false).css({'background-color':'#333', 'cursor':'pointer'});
                        btnBuyNow.prop('disabled', false).css({'background-color':'#dc3545', 'cursor':'pointer'});
                        stickyBtnCart.prop('disabled', false).css({'background-color':'#333', 'cursor':'pointer'});
                        stickyBtnBuyNow.prop('disabled', false).css({'background-color':'#dc3545', 'cursor':'pointer'});
                    } else { 
                        stockLabel.text('Hết hàng').css('color', 'red');
                        inputQty.attr('data-max', 0).val(1);
                        
                        btnCart.prop('disabled', true).css({'background-color':'#ccc', 'cursor':'not-allowed'});
                        btnBuyNow.prop('disabled', true).css({'background-color':'#ccc', 'cursor':'not-allowed'});
                        stickyBtnCart.prop('disabled', true).css({'background-color':'#ccc', 'cursor':'not-allowed'});
                        stickyBtnBuyNow.prop('disabled', true).css({'background-color':'#ccc', 'cursor':'not-allowed'});
                    }
                } else { 
                    detailBlock.data('selected-variant', '');
                    stockLabel.text('Hết hàng').css('color', 'red');
                    inputQty.attr('data-max', 0).val(1);
                    
                    btnCart.prop('disabled', true).css({'background-color':'#ccc', 'cursor':'not-allowed'});
                    btnBuyNow.prop('disabled', true).css({'background-color':'#ccc', 'cursor':'not-allowed'});
                    stickyBtnCart.prop('disabled', true).css({'background-color':'#ccc', 'cursor':'not-allowed'});
                    stickyBtnBuyNow.prop('disabled', true).css({'background-color':'#ccc', 'cursor':'not-allowed'});
                }
            } else { 
                detailBlock.data('selected-variant', '');
                stockLabel.text('Vui lòng chọn phân loại hàng').css('color', '#666');
                inputQty.attr('data-max', 1);
                if(parseInt(inputQty.val()) < 1 || isNaN(inputQty.val())) inputQty.val(1);
                
                btnCart.prop('disabled', false).css({'background-color':'#333', 'cursor':'pointer'});
                btnBuyNow.prop('disabled', false).css({'background-color':'#dc3545', 'cursor':'pointer'});
                stickyBtnCart.prop('disabled', false).css({'background-color':'#333', 'cursor':'pointer'});
                stickyBtnBuyNow.prop('disabled', false).css({'background-color':'#dc3545', 'cursor':'pointer'});
            }

            var colorInputs = detailBlock.find('input.js-variant-input[name*="color"]');
            var sizeInputs = detailBlock.find('input.js-variant-input[name*="size"]');

            if (colorInputs.length > 0) {
                colorInputs.each(function() {
                    var cVal = parseInt($(this).val());
                    var hasStock = false;
                    if (selSize) {
                        var attrs = [cVal, parseInt(selSize)].sort(function(a,b){return a-b});
                        var checkKey = attrs.join('_');
                        if (variantData && variantData[checkKey] && variantData[checkKey].stock > 0) hasStock = true;
                    } else {
                        for (var k in variantData) {
                            if (k.split('_').indexOf(cVal.toString()) !== -1 && variantData[k].stock > 0) {
                                hasStock = true; break;
                            }
                        }
                    }
                    if (hasStock) {
                        $(this).parent().removeClass('is-disabled');
                        $(this).prop('disabled', false); 
                        $('.js-sticky-color option[value="'+cVal+'"]').prop('disabled', false); 
                    } else {
                        $(this).parent().addClass('is-disabled');
                        $(this).prop('disabled', true); 
                        $('.js-sticky-color option[value="'+cVal+'"]').prop('disabled', true); 
                        if($(this).is(':checked')) $(this).prop('checked', false);
                    }
                });
            }

            if (sizeInputs.length > 0) {
                sizeInputs.each(function() {
                    var sVal = parseInt($(this).val());
                    var hasStock = false;
                    if (selColor) {
                        var attrs = [sVal, parseInt(selColor)].sort(function(a,b){return a-b});
                        var checkKey = attrs.join('_');
                        if (variantData && variantData[checkKey] && variantData[checkKey].stock > 0) hasStock = true;
                    } else {
                        for (var k in variantData) {
                            if (k.split('_').indexOf(sVal.toString()) !== -1 && variantData[k].stock > 0) {
                                hasStock = true; break;
                            }
                        }
                    }
                    if (hasStock) {
                        $(this).parent().removeClass('is-disabled');
                        $(this).prop('disabled', false); 
                        $('.js-sticky-size option[value="'+sVal+'"]').prop('disabled', false); 
                    } else {
                        $(this).parent().addClass('is-disabled');
                        $(this).prop('disabled', true); 
                        $('.js-sticky-size option[value="'+sVal+'"]').prop('disabled', true); 
                        if($(this).is(':checked')) $(this).prop('checked', false);
                    }
                });
            }
        });

        $('.js-sticky-color').on('change', function() {
            var val = $(this).val();
            if(val) {
                var radio = detailBlock.find('input[name="color"][value="'+val+'"]');
                if(!radio.prop('disabled')) {
                    radio.prop('checked', true).trigger('change');
                } else {
                    $(this).val(''); 
                }
            } else {
                detailBlock.find('input[name="color"]').prop('checked', false);
                detailBlock.find('input.js-variant-input').first().trigger('change');
            }
        });

        $('.js-sticky-size').on('change', function() {
            var val = $(this).val();
            if(val) {
                var radio = detailBlock.find('input[name="size"][value="'+val+'"]');
                if(!radio.prop('disabled')) {
                    radio.prop('checked', true).trigger('change');
                } else {
                    $(this).val(''); 
                }
            } else {
                detailBlock.find('input[name="size"]').prop('checked', false);
                detailBlock.find('input.js-variant-input').first().trigger('change');
            }
        });

        $('.num-product').on('change', function() {
            var max = parseInt($(this).attr('data-max'));
            var current = parseInt($(this).val());
            if (isNaN(current) || current < 1) {
                $(this).val(1);
            } else if(!isNaN(max) && current > max && max > 0) {
                swal("Cảnh báo", "Rất tiếc! Mẫu này chỉ còn " + max + " sản phẩm trong kho.", "warning");
                $(this).val(max);
            }
            $('.num-product-sticky').val($(this).val()); 
        });

        $('.num-product-sticky').on('change', function() {
            var val = parseInt($(this).val());
            if (isNaN(val) || val < 1) val = 1;
            $('.num-product').val(val).trigger('change'); 
        });

        $('.btn-num-product-down').on('click', function() {
            var input = $(this).siblings('.num-product');
            setTimeout(function() { 
                if(parseInt(input.val()) < 1 || isNaN(input.val())) input.val(1);
                input.trigger('change'); 
            }, 50);
        });

        $('.btn-num-product-up').on('click', function() {
            var input = $(this).siblings('.num-product');
            setTimeout(function() { input.trigger('change'); }, 50);
        });

        $('.btn-num-product-down-sticky').on('click', function() { $('.btn-num-product-down').trigger('click'); });
        $('.btn-num-product-up-sticky').on('click', function() { $('.btn-num-product-up').trigger('click'); });

        function validateBeforeAdd() {
            var groups = [];
            detailBlock.find('input.js-variant-input').each(function() {
                var name = $(this).attr('name');
                if(groups.indexOf(name) === -1) groups.push(name);
            });
            var checkedCount = detailBlock.find('input.js-variant-input:checked').length;
            if(groups.length > 0 && checkedCount < groups.length) {
                swal("Chú ý", "Vui lòng chọn đầy đủ Kích thước và Màu sắc!", "warning");
                return false;
            }

            var qty = parseInt(detailBlock.find('.num-product').val());
            var max = parseInt(detailBlock.find('.num-product').attr('data-max'));
            if (qty < 1 || isNaN(qty)) {
                detailBlock.find('.num-product').val(1);
                swal("Lỗi", "Số lượng sản phẩm phải từ 1 trở lên!", "error");
                return false;
            }
            if (max === 0) {
                swal("Lỗi", "Sản phẩm này đã hết hàng!", "error");
                return false;
            }
            if(!isNaN(max) && qty > max && max > 0) {
                swal("Lỗi", "Bạn không thể mua quá " + max + " sản phẩm!", "error");
                return false;
            }
            return true;
        }

        // ĐÃ NÂNG CẤP NÚT THÊM VÀO GIỎ THÀNH AJAX
        $('.js-addcart-page').on('click', function(e) {
            e.preventDefault();
            if($(this).prop('disabled')) return; 
            if(validateBeforeAdd()) {
                var qty = parseInt(detailBlock.find('.num-product').val());
                var productId = $(this).data('id');
                var variantId = detailBlock.data('selected-variant') || null;
                var nameProduct = detailBlock.find('.js-detail-name').text().trim();

                $.ajax({
                    url: '{{ route("cart.add") }}',
                    type: 'POST',
                    data: { _token: '{{ csrf_token() }}', product_id: productId, variant_id: variantId, quantity: qty },
                    success: function(res) {
                        if(res.success) {
                            $('.icon-header-noti.js-show-cart').attr('data-notify', res.cart_count);
                            if(typeof loadCartDropdown === 'function') loadCartDropdown();
                            swal(nameProduct, "Đã thêm " + qty + " sản phẩm vào giỏ hàng!", "success");
                        }
                    },
                    error: function(err) {
                        if(err.responseJSON && err.responseJSON.error) {
                            swal("Lỗi", err.responseJSON.error, "error");
                        } else { swal("Lỗi", "Có lỗi xảy ra!", "error"); }
                    }
                });
            }
        });

        // ĐÃ NÂNG CẤP NÚT MUA NGAY THÀNH AJAX RỒI CHUYỂN HƯỚNG
        $('.js-buynow-page').on('click', function(e) {
            e.preventDefault();
            if($(this).prop('disabled')) return; 
            if(validateBeforeAdd()) {
                var qty = parseInt(detailBlock.find('.num-product').val());
                var productId = $(this).data('id');
                var variantId = detailBlock.data('selected-variant') || null;

                swal("Đang xử lý", "Đang chuyển bạn đến trang thanh toán...", "success");

                $.ajax({
                    url: '{{ route("cart.add") }}',
                    type: 'POST',
                    data: { _token: '{{ csrf_token() }}', product_id: productId, variant_id: variantId, quantity: qty },
                    success: function(res) {
                        if(res.success) {
                            window.location.href = '{{ route("checkout.index") }}';
                        }
                    },
                    error: function(err) {
                        if(err.responseJSON && err.responseJSON.error) {
                            swal("Lỗi", err.responseJSON.error, "error");
                        } else { swal("Lỗi", "Có lỗi xảy ra!", "error"); }
                    }
                });
            }
        });

        $('.js-sticky-addcart').on('click', function(e) { e.preventDefault(); $('.js-addcart-page').trigger('click'); });
        $('.js-sticky-buynow').on('click', function(e) { e.preventDefault(); $('.js-buynow-page').trigger('click'); });

        $('.js-addwish-custom').on('click', function(e){
            e.preventDefault();
            var nameProduct = detailBlock.find('.js-detail-name').text().trim();
            swal(nameProduct, "Đã lưu vào danh sách yêu thích!", "success");
            $(this).find('i').removeClass('zmdi-favorite-outline').addClass('zmdi-favorite').css('color', '#dc3545');
            $(this).off('click');
        });

        var variants = detailBlock.find('input.js-variant-input');
        if(variants.length > 0) {
            variants.first().trigger('change');
        } else {
            var defaultStock = parseInt(detailBlock.data('default-stock'));
            var btnCart = detailBlock.find('.js-addcart-page');
            var btnBuyNow = detailBlock.find('.js-buynow-page');
            var stickyBtnCart = $('.js-sticky-addcart');
            var stickyBtnBuyNow = $('.js-sticky-buynow');
            var inputQty = detailBlock.find('.num-product');
            var stockLabel = detailBlock.find('.js-detail-stock');

            if(!isNaN(defaultStock) && defaultStock > 0) {
                stockLabel.text('Còn hàng (' + defaultStock + ')').css('color', '#222');
                inputQty.attr('data-max', defaultStock);
                btnCart.prop('disabled', false).css({'background-color':'#333', 'cursor':'pointer'});
                btnBuyNow.prop('disabled', false).css({'background-color':'#dc3545', 'cursor':'pointer'});
                stickyBtnCart.prop('disabled', false).css({'background-color':'#333', 'cursor':'pointer'});
                stickyBtnBuyNow.prop('disabled', false).css({'background-color':'#dc3545', 'cursor':'pointer'});
            } else {
                stockLabel.text('Hết hàng').css('color', 'red');
                inputQty.attr('data-max', 0).val(1);
                btnCart.prop('disabled', true).css({'background-color':'#ccc', 'cursor':'not-allowed'});
                btnBuyNow.prop('disabled', true).css({'background-color':'#ccc', 'cursor':'not-allowed'});
                stickyBtnCart.prop('disabled', true).css({'background-color':'#ccc', 'cursor':'not-allowed'});
                stickyBtnBuyNow.prop('disabled', true).css({'background-color':'#ccc', 'cursor':'not-allowed'});
            }
        }
    });
</script>
@endpush
@endsection