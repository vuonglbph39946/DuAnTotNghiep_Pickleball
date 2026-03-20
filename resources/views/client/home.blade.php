@extends('client.layouts.app')

@section('title', 'Trang chủ - PBall Store')

@section('content')
<section class="section-slide">
    <div class="wrap-slick1">
        <div class="slick1">
            @if(isset($banners) && $banners->count() > 0)
                @foreach($banners as $banner)
                <div class="item-slick1" style="background-image: url({{ asset($banner->image_path) }}); cursor: pointer;" onclick="window.location.href='{{ $banner->link ?? url('category/vot-pickleball') }}';">
                    <div class="container h-full">
                        <div class="flex-col-l-m h-full p-t-100 p-b-30 respon5">
                            @if($banner->title)
                            <div class="layer-slick1 animated visible-false" data-appear="fadeInDown" data-delay="0">
                                <span class="ltext-101 cl2 respon2" style="background-color: rgba(255,255,255,0.7); padding: 5px 15px; border-radius: 5px;">
                                    {{ $banner->title }}
                                </span>
                            </div>
                            @endif
                            <div class="layer-slick1 animated visible-false" data-appear="zoomIn" data-delay="800">
                                <a href="{{ $banner->link ?? url('category/vot-pickleball') }}" onclick="event.stopPropagation();" class="flex-c-m stext-101 cl0 size-101 bg1 bor1 hov-btn1 p-lr-15 trans-04 m-t-20">
                                    Xem ngay
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            @else
                <div class="item-slick1" style="background-image: url({{ asset('client/images/slide-01.jpg') }}); cursor: pointer;" onclick="window.location.href='{{ url('category/vot-pickleball') }}';">
                    <div class="container h-full">
                        <div class="flex-col-l-m h-full p-t-100 p-b-30 respon5">
                            <div class="layer-slick1 animated visible-false" data-appear="fadeInDown" data-delay="0">
                                <span class="ltext-101 cl2 respon2">Chào mừng đến với PBall Store</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>

<div class="bg0 p-b-140">
    @if(isset($categories))
        @foreach($categories as $category)
            @if($category->home_products->count() > 0)
            <section class="p-t-45 p-b-20">
                <div class="container">
                    <div class="p-b-30">
                        <h3 class="ltext-103 txt-center" style="text-transform: uppercase; font-weight: 500;">
                            <a href="{{ url('category/' . $category->slug) }}" class="cl5 hov-cl1 trans-04" style="text-decoration: none;">
                                {{ $category->name }}
                            </a>
                        </h3>
                    </div>

                    <div class="row isotope-grid">
                        @foreach($category->home_products as $product)
                        <div class="col-sm-6 col-md-4 col-lg-3 p-b-40 isotope-item">
                            <div class="block2">
                                <div class="block2-pic hov-img0 pos-relative product-img-wrap" style="background-color: #f7f7f7;">
                                    <a href="{{ url('product/' . $product->slug) }}" class="dis-block pos-relative w-full">
                                        <img src="{{ asset($product->images->first()->image_path ?? 'client/images/product-01.jpg') }}" alt="{{ $product->name }}" class="main-img">
                                        @if($product->images->count() > 1)
                                            <img src="{{ asset($product->images[1]->image_path) }}" alt="{{ $product->name }}" class="hover-img">
                                        @endif
                                        
                                        @if($product->stock <= 0)
                                            <span class="stext-101 cl0 bg-dark" style="position: absolute; top: 10px; right: 10px; padding: 4px 10px; font-size: 11px; font-weight: 500;">
                                                Hết hàng
                                            </span>
                                        @endif
                                    </a>

                                    <a href="#" class="block2-btn flex-c-m stext-103 cl2 size-102 bg0 bor2 hov-btn1 p-lr-15 trans-04 js-show-modal-custom" data-target="#modal-quickview-{{ $product->id }}">
                                        Xem nhanh
                                    </a>
                                </div>

                                <div class="block2-txt flex-w flex-t p-t-14">
                                    <div class="block2-txt-child1 flex-col-l w-full">
                                        <span class="stext-105 cl3 p-b-5" style="font-size: 11px; color: #a0a0a0;">
                                            +{{ $product->variants->count() ?? 0 }} Kích thước
                                        </span>

                                        <a href="{{ url('product/' . $product->slug) }}" class="stext-104 cl4 hov-cl1 trans-04 js-name-b2 p-b-10" style="text-transform: uppercase; font-size: 13px; font-weight: 500; color: #333; letter-spacing: 0.5px;">
                                            {{ $product->name }}
                                        </a>

                                        <div class="flex-w flex-sb-m w-full p-t-2" style="align-items: center;">
                                            <span class="stext-105 cl3" style="font-weight: 700; color: #111; font-size: 14px;">
                                                @if($product->sale_price)
                                                    {{ number_format($product->sale_price) }}đ
                                                    <del style="color: #999; font-size: 12px; margin-left: 5px; font-weight: normal;">{{ number_format($product->price) }}đ</del>
                                                @else
                                                    {{ number_format($product->price) }}đ
                                                @endif
                                            </span>

                                            <div class="flex-r-m">
                                                @if($product->stock > 0)
                                                    <button class="js-addcart-card trans-04" data-id="{{ $product->id }}" data-variants="{{ $product->variants->count() }}" data-modal="#modal-quickview-{{ $product->id }}" style="font-size: 20px; color: #333; margin-right: 12px;" title="Thêm vào giỏ">
                                                        <i class="zmdi zmdi-shopping-cart-plus hover-icon-red"></i>
                                                    </button>
                                                @else
                                                    <button disabled style="font-size: 20px; color: #ccc; margin-right: 12px; cursor: not-allowed;" title="Hết hàng">
                                                        <i class="zmdi zmdi-shopping-cart-plus"></i>
                                                    </button>
                                                @endif

                                                <a href="#" class="btn-addwish-b2 pos-relative js-addwish-custom" style="font-size: 19px; display: flex; align-items: center;" title="Yêu thích">
                                                    <img class="icon-heart1 dis-block trans-04" src="{{ asset('client/images/icons/icon-heart-01.png') }}" alt="ICON" style="width: 18px;">
                                                    <img class="icon-heart2 dis-block trans-04 ab-t-l" src="{{ asset('client/images/icons/icon-heart-02.png') }}" alt="ICON" style="width: 18px;">
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </section>
            @endif
        @endforeach
    @endif
</div>

@if(isset($categories))
    @foreach($categories as $category)
        @foreach($category->home_products as $product)
            @php
                $colors = \DB::table('attribute_values')
                    ->join('variant_attribute_values', 'attribute_values.id', '=', 'variant_attribute_values.attribute_value_id')
                    ->join('product_variants', 'variant_attribute_values.variant_id', '=', 'product_variants.id')
                    ->where('product_variants.product_id', $product->id)
                    ->where('attribute_values.attribute_id', 3)
                    ->select('attribute_values.id', 'attribute_values.value', 'attribute_values.color_code')
                    ->distinct()->get();

                $sizes = \DB::table('attribute_values')
                    ->join('variant_attribute_values', 'attribute_values.id', '=', 'variant_attribute_values.attribute_value_id')
                    ->join('product_variants', 'variant_attribute_values.variant_id', '=', 'product_variants.id')
                    ->where('product_variants.product_id', $product->id)
                    ->where('attribute_values.attribute_id', 4)
                    ->select('attribute_values.id', 'attribute_values.value')
                    ->distinct()->get();

                $variantData = [];
                $variants = \DB::table('product_variants')->where('product_id', $product->id)->get();
                foreach($variants as $v) {
                    $attrs = \DB::table('variant_attribute_values')->where('variant_id', $v->id)->pluck('attribute_value_id')->toArray();
                    sort($attrs);
                    $key = empty($attrs) ? 'default' : implode('_', $attrs);
                    $variantData[$key] = [
                        'id' => $v->id, // ĐÃ FIX: Truyền ID của variant vào để JS có thể lấy được
                        'price' => number_format($v->price) . 'đ',
                        'sale_price' => $v->sale_price ? number_format($v->sale_price) . 'đ' : null,
                        'stock' => (int)$v->stock
                    ];
                }
            @endphp

            <div id="modal-quickview-{{ $product->id }}" class="wrap-modal1 p-t-60 p-b-20 custom-modal" data-variants="{{ json_encode($variantData) }}" data-default-stock="{{ $product->stock }}">
                <div class="overlay-modal1 js-hide-modal-custom"></div>

                <div class="container">
                    <div class="bg0 p-t-40 p-b-30 p-lr-15-lg how-pos3-parent" style="border-radius: 5px; max-width: 1140px; margin: 0 auto;">
                        <button class="how-pos3 hov3 trans-04 js-hide-modal-custom">
                            <img src="{{ asset('client/images/icons/icon-close.png') }}" alt="CLOSE">
                        </button>

                        <div class="row">
                            <div class="col-md-6 col-lg-6 p-b-30">
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
                            
                            <div class="col-md-6 col-lg-6 p-b-30">
                                <div class="p-r-50 p-t-5 p-lr-0-lg">
                                    <h4 class="mtext-105 cl2 p-b-10 js-modal-name" style="text-transform: uppercase; font-weight: 600;">
                                        {{ $product->name }}
                                    </h4>

                                    <p class="stext-102 cl3 p-b-20">
                                        Tình trạng: <span class="js-modal-stock" style="color: #222; font-weight: bold;">Đang tải...</span>
                                    </p>

                                    <div class="flex-w flex-l-m p-b-30" style="gap: 10px;">
                                        <span style="font-weight: 600; color: #333; font-size: 16px;">Giá:</span>
                                        <span class="mtext-106 js-modal-price" style="color: red; font-weight: bold; font-size: 24px;">
                                            @if($product->sale_price)
                                                {{ number_format($product->sale_price) }}đ
                                                <del style="color: #999; font-size: 16px; margin-left: 8px; font-weight: normal;">{{ number_format($product->price) }}đ</del>
                                            @else
                                                {{ number_format($product->price) }}đ
                                            @endif
                                        </span>
                                    </div>
                                    
                                    <div class="p-t-10">
                                        @if($colors->count() > 0)
                                        <div class="flex-w flex-r-m p-b-20">
                                            <div class="size-203 flex-c-m respon6" style="justify-content: flex-start; font-weight: 600; color: #333;">Màu sắc:</div>
                                            <div class="size-204 flex-w">
                                                @foreach($colors as $color)
                                                <label class="color-variant-box tooltip100" data-tooltip="{{ $color->value }}">
                                                    <input type="radio" class="js-variant-input" name="color_{{ $product->id }}" value="{{ $color->id }}">
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
                                                    <input type="radio" class="js-variant-input" name="size_{{ $product->id }}" value="{{ $size->id }}">
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
                                        
                                        <button class="flex-c-m stext-101 cl0 size-101 bg-danger bor1 hov-btn1 p-lr-15 trans-04 w-full m-t-10 js-addcart-modal" data-id="{{ $product->id }}" style="height: 50px; font-weight: bold; border-radius: 3px;">
                                            THÊM VÀO GIỎ
                                        </button>

                                        <div class="p-t-20">
                                            <a href="{{ url('product/' . $product->slug) }}" class="stext-104 cl3 hov-cl1 trans-04" style="text-decoration: underline;">
                                                Xem chi tiết sản phẩm »
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endforeach
@endif

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

    .hover-icon-red:hover { color: #dc3545; }
    .filter-link-active { color: #dc3545 !important; }

    .variant-box.is-disabled, 
    .color-variant-box.is-disabled {
        pointer-events: none !important;
        cursor: not-allowed !important;
    }
    
    .variant-box.is-disabled span {
        opacity: 0.4; position: relative;
        background-color: #f9f9f9; border-color: #e0e0e0;
    }
    .variant-box.is-disabled span::before {
        content: ''; position: absolute; top: 50%; left: 0; right: 0;
        height: 1.5px; background: #999; transform: rotate(-20deg);
    }

    .color-variant-box.is-disabled .color-swatch { opacity: 0.3; }
    .color-variant-box.is-disabled::before {
        content: '✖'; position: absolute; top: 50%; left: 50%;
        transform: translate(-50%, -50%); color: #555; font-size: 15px;
        z-index: 10; pointer-events: none; 
    }
    .variant-box.is-disabled input:checked + span { border-color: red; color: red; }
    .variant-box.is-disabled input:checked + span::before { background: red; }
    .color-variant-box.is-disabled input:checked ~::before { color: red; }
    .color-variant-box.is-disabled input:checked + .color-swatch { border-color: red; }
    .color-variant-box.is-disabled input:checked + .color-swatch::after { display: none; }
</style>

@push('scripts')
<script>
    $(document).ready(function(){
        $(document).on('click', '.filter-link, .pagination-wrapper a', function(e) {
            e.preventDefault();
            var url = $(this).attr('href');
            if(!url) return;

            window.history.pushState("", "", url);

            if($(this).hasClass('filter-link')) {
                $('.filter-link').removeClass('filter-link-active cl1 font-weight-bold');
                $(this).addClass('filter-link-active cl1 font-weight-bold');
            }

            $('#product-list-container').css('opacity', '0.4');

            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    var newHtml = $(response).find('#product-list-container').html();
                    $('#product-list-container').html(newHtml);
                    $('#product-list-container').css('opacity', '1');
                },
                error: function() {
                    alert('Lỗi tải dữ liệu, vui lòng thử lại!');
                    $('#product-list-container').css('opacity', '1');
                }
            });
        });

        $(document).on('click', '.js-show-modal-custom', function(e){
            e.preventDefault();
            var targetModal = $(this).data('target');
            $(targetModal).addClass('show-modal1');

            setTimeout(function() {
                $(targetModal).find('.slick3').slick('setPosition');
            }, 200);

            var variants = $(targetModal).find('input.js-variant-input');
            if(variants.length > 0) {
                variants.prop('checked', false);
                variants.prop('disabled', false); 
                variants.first().trigger('change');
            } else {
                var defaultStock = parseInt($(targetModal).data('default-stock'));
                var btnCart = $(targetModal).find('.js-addcart-modal');
                var inputQty = $(targetModal).find('.num-product');
                var stockLabel = $(targetModal).find('.js-modal-stock');

                if(!isNaN(defaultStock) && defaultStock > 0) {
                    stockLabel.text('Còn hàng (' + defaultStock + ')').css('color', '#222');
                    inputQty.attr('data-max', defaultStock);
                    inputQty.val(1);
                    btnCart.prop('disabled', false).text('THÊM VÀO GIỎ').css({'background-color':'#dc3545', 'cursor':'pointer'}).removeClass('bg-secondary');
                } else {
                    stockLabel.text('Hết hàng').css('color', 'red');
                    inputQty.attr('data-max', 0);
                    inputQty.val(1);
                    btnCart.prop('disabled', true).text('HẾT HÀNG').css({'background-color':'#6c757d', 'cursor':'not-allowed'});
                }
            }
        });

        $(document).on('click', '.js-hide-modal-custom', function(){
            $(this).closest('.custom-modal').removeClass('show-modal1');
        });

        $(document).on('click', '.js-clear-variants', function(e) {
            e.preventDefault();
            var modal = $(this).closest('.custom-modal');
            modal.find('input.js-variant-input').prop('checked', false).prop('disabled', false);
            modal.find('input.js-variant-input').first().trigger('change');
        });

        $(document).on('change', '.js-variant-input', function() {
            var modal = $(this).closest('.custom-modal');
            var variantData = modal.data('variants'); 
            
            var groups = [];
            modal.find('input.js-variant-input').each(function() {
                var name = $(this).attr('name');
                if(groups.indexOf(name) === -1) groups.push(name);
            });

            var selectedAttrs = [];
            modal.find('input.js-variant-input:checked').each(function() {
                selectedAttrs.push(parseInt($(this).val()));
            });
            
            selectedAttrs.sort(function(a, b){return a-b});
            var key = selectedAttrs.length > 0 ? selectedAttrs.join('_') : 'default';
            
            var btnCart = modal.find('.js-addcart-modal');
            var inputQty = modal.find('.num-product');
            var stockLabel = modal.find('.js-modal-stock');
            var priceLabel = modal.find('.js-modal-price');

            var originalBtnText = btnCart.data('original-text');
            if(!originalBtnText) {
                originalBtnText = btnCart.text().trim();
                btnCart.data('original-text', originalBtnText);
            }

            if(groups.length > 0 && selectedAttrs.length === groups.length) {
                if(variantData && variantData[key]) {
                    var v = variantData[key];
                    
                    // LƯU LẠI VARIANT ID ĐỂ LÁT NỮA NÚT THÊM GIỎ HÀNG NÓ BẮT ĐƯỢC
                    modal.data('selected-variant', v.id);

                    if(v.sale_price) {
                        priceLabel.html(v.sale_price + ' <del style="color:#999; font-size:16px; margin-left:8px; font-weight:normal;">' + v.price + '</del>');
                    } else {
                        priceLabel.text(v.price);
                    }
                    
                    if(v.stock > 0) {
                        stockLabel.text('Còn hàng (' + v.stock + ')').css('color', '#222');
                        inputQty.attr('data-max', v.stock);
                        if(parseInt(inputQty.val()) < 1 || isNaN(inputQty.val())) inputQty.val(1);
                        btnCart.prop('disabled', false).text(originalBtnText).css({'background-color':'#dc3545', 'cursor':'pointer'}).removeClass('bg-secondary');
                    } else {
                        stockLabel.text('Hết hàng').css('color', 'red');
                        inputQty.attr('data-max', 0);
                        inputQty.val(1);
                        btnCart.prop('disabled', true).text('HẾT HÀNG').css({'background-color':'#6c757d', 'cursor':'not-allowed'});
                    }
                } else {
                    modal.data('selected-variant', '');
                    stockLabel.text('Hết hàng').css('color', 'red');
                    inputQty.attr('data-max', 0);
                    inputQty.val(1);
                    btnCart.prop('disabled', true).text('HẾT HÀNG').css({'background-color':'#6c757d', 'cursor':'not-allowed'});
                }
            } else {
                modal.data('selected-variant', '');
                stockLabel.text('Vui lòng chọn phân loại hàng').css('color', '#666');
                inputQty.attr('data-max', 1);
                if(parseInt(inputQty.val()) < 1 || isNaN(inputQty.val())) inputQty.val(1);
                btnCart.prop('disabled', false).text(originalBtnText).css({'background-color':'#dc3545', 'cursor':'pointer'}).removeClass('bg-secondary');
            }

            var colorInputs = modal.find('input.js-variant-input[name*="color"]');
            var sizeInputs = modal.find('input.js-variant-input[name*="size"]');
            
            var selColor = colorInputs.filter(':checked').val();
            var selSize = sizeInputs.filter(':checked').val();

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
                    } else {
                        $(this).parent().addClass('is-disabled');
                        $(this).prop('disabled', true); 
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
                    } else {
                        $(this).parent().addClass('is-disabled');
                        $(this).prop('disabled', true); 
                        if($(this).is(':checked')) $(this).prop('checked', false);
                    }
                });
            }
        });

        $(document).on('change', '.num-product', function() {
            var max = parseInt($(this).attr('data-max'));
            var current = parseInt($(this).val());
            
            if (isNaN(current) || current < 1) {
                $(this).val(1);
            } 
            else if(!isNaN(max) && current > max && max > 0) {
                swal("Cảnh báo", "Rất tiếc! Mẫu này chỉ còn " + max + " sản phẩm trong kho.", "warning");
                $(this).val(max);
            }
        });

        $(document).on('click', '.btn-num-product-down', function() {
            var input = $(this).siblings('.num-product');
            setTimeout(function() { 
                if(parseInt(input.val()) < 1 || isNaN(input.val())) {
                    input.val(1);
                }
                input.trigger('change'); 
            }, 50);
        });

        $(document).on('click', '.btn-num-product-up', function() {
            var input = $(this).siblings('.num-product');
            setTimeout(function() { input.trigger('change'); }, 50);
        });

        // ==========================================
        // SỰ KIỆN 1: BẤM NÚT TRONG MODAL (CÓ CHỌN BIẾN THỂ)
        // ==========================================
        $(document).on('click', '.js-addcart-modal', function(e) {
            e.preventDefault();
            if($(this).prop('disabled')) return; 
            
            var modal = $(this).closest('.custom-modal');
            var groups = [];
            modal.find('input.js-variant-input').each(function() {
                var name = $(this).attr('name');
                if(groups.indexOf(name) === -1) groups.push(name);
            });
            var checkedCount = modal.find('input.js-variant-input:checked').length;
            if(groups.length > 0 && checkedCount < groups.length) {
                swal("Chú ý", "Vui lòng chọn đầy đủ Kích thước và Màu sắc trước khi đặt hàng!", "warning");
                return;
            }

            var qty = parseInt(modal.find('.num-product').val());
            var max = parseInt(modal.find('.num-product').attr('data-max'));
            
            if (qty < 1 || isNaN(qty)) {
                modal.find('.num-product').val(1);
                swal("Lỗi rùi", "Số lượng sản phẩm phải từ 1 trở lên!", "error");
                return;
            }
            if(!isNaN(max) && qty > max && max > 0) {
                swal("Lỗi rùi", "Bạn không thể mua quá " + max + " sản phẩm!", "error");
                return;
            }

            var productId = $(this).data('id');
            var variantId = modal.data('selected-variant') || null;
            var nameProduct = modal.find('.js-modal-name').text().trim();

            $.ajax({
                url: '{{ route("cart.add") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    product_id: productId,
                    variant_id: variantId,
                    quantity: qty
                },
                success: function(res) {
                    if(res.success) {
                        $('.icon-header-noti.js-show-cart').attr('data-notify', res.cart_count);
                        if(typeof loadCartDropdown === 'function') loadCartDropdown();
                        
                        modal.removeClass('show-modal1'); // Đóng Modal đi
                        swal(nameProduct, "Đã thêm " + qty + " sản phẩm vào giỏ hàng!", "success");
                    }
                },
                error: function(err) {
                    if(err.responseJSON && err.responseJSON.error) {
                        swal("Lỗi", err.responseJSON.error, "error");
                    } else {
                        swal("Lỗi", "Có lỗi xảy ra!", "error");
                    }
                }
            });
        });

        // ==========================================
        // SỰ KIỆN 2: BẤM NÚT THÊM NGOÀI TRANG CHỦ
        // ==========================================
        $(document).on('click', '.js-addcart-card', function(e) {
            e.preventDefault();
            var btn = $(this);
            var variantCount = parseInt(btn.data('variants'));
            var productId = btn.data('id');
            var nameProduct = btn.closest('.block2').find('.js-name-b2').text().trim();

            if(variantCount > 0) {
                // CÓ BIẾN THỂ -> HIỆN BẢNG LÊN BẮT CHỌN
                var targetModal = btn.data('modal');
                $(targetModal).addClass('show-modal1');
                setTimeout(function() {
                    $(targetModal).find('.slick3').slick('setPosition');
                }, 200);
                var variants = $(targetModal).find('input.js-variant-input');
                if(variants.length > 0) {
                    variants.prop('checked', false);
                    variants.first().trigger('change');
                }
            } else {
                // KHÔNG BIẾN THỂ -> BẮN THẲNG AJAX MUA LUÔN
                $.ajax({
                    url: '{{ route("cart.add") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        product_id: productId,
                        variant_id: null,
                        quantity: 1
                    },
                    success: function(res) {
                        if(res.success) {
                            $('.icon-header-noti.js-show-cart').attr('data-notify', res.cart_count);
                            if(typeof loadCartDropdown === 'function') loadCartDropdown();
                            swal(nameProduct, "Đã thêm 1 sản phẩm vào giỏ hàng!", "success");
                        }
                    },
                    error: function(err) {
                        if(err.responseJSON && err.responseJSON.error) {
                            swal("Lỗi", err.responseJSON.error, "error");
                        } else {
                            swal("Lỗi", "Có lỗi xảy ra!", "error");
                        }
                    }
                });
            }
        });

        $(document).on('click', '.js-addwish-custom', function(e){
            e.preventDefault();
            var nameProduct = $(this).closest('.block2').find('.js-name-b2').text().trim();
            swal(nameProduct, "Đã lưu vào danh sách yêu thích!", "success");
            $(this).addClass('js-addedwish-b2');
        });
    });
</script>
@endpush
@endsection