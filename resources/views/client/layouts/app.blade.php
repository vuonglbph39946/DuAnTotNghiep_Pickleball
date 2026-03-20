<!DOCTYPE html>
<html lang="vi">
<head>
	<title>@yield('title', 'PBall Store')</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
    
    <link rel="icon" type="image/png" href="{{ asset('client/images/icons/favicon.png') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('client/vendor/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('client/fonts/font-awesome-4.7.0/css/font-awesome.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('client/fonts/iconic/css/material-design-iconic-font.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('client/fonts/linearicons-v1.0.0/icon-font.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('client/vendor/animate/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('client/vendor/css-hamburgers/hamburgers.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('client/vendor/animsition/css/animsition.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('client/vendor/select2/select2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('client/vendor/daterangepicker/daterangepicker.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('client/vendor/slick/slick.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('client/vendor/MagnificPopup/magnific-popup.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('client/vendor/perfect-scrollbar/perfect-scrollbar.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('client/css/util.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('client/css/main.css') }}">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&family=Noto+Serif+Display:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* 1. GHI ĐÈ TOÀN BỘ CÁC CLASS CHỮ CỦA TEMPLATE ĐỂ TRỊ TẬN GỐC LỖI DẤU TIẾNG VIỆT */
        body, h1, h2, h3, h4, h5, h6, p, a, button, input, label, textarea,
        [class*="stext-"], [class*="mtext-"], [class*="ltext-"] {
            font-family: 'Montserrat', sans-serif !important;
        }
        
        /* 2. Font phụ Noto Serif Display (dành cho tiêu đề nếu bạn thích) */
        .font-secondary {
            font-family: 'Noto Serif Display', serif !important;
        }

        /* 3. Gom CSS bắt lỗi dùng chung */
        .bor-red { border: 1px solid #dc3545 !important; }
        input:-webkit-autofill { -webkit-box-shadow: 0 0 0 30px white inset !important; }
        .form-label-custom { font-weight: 500 !important; color: #333 !important; }

        /* ==============================================================
           4. FIX TRIỆT ĐỂ LỖI HEADER BỊ RỚT DÒNG BIẾN DẠNG Ở ZOOM 100% 
           ============================================================== */
        @media (min-width: 992px) {
            /* Nới rộng khung Header để có đủ chỗ chứa 8 mục Menu và font Montserrat (dáng chữ to ngang) */
            .limiter-menu-desktop {
                max-width: 1350px !important;
            }
            .limiter-menu-desktop .logo {
                margin-right: 20px !important;
            }
            /* Giảm khoảng cách giữa các chữ trong Menu */
            .main-menu > li {
                padding: 0 8px !important; 
            }
            /* Ép size chữ nhỏ lại và gọn gàng */
            .main-menu > li > a {
                font-size: 13px !important; 
                letter-spacing: 0 !important; 
            }
            /* Cụm Icon bên phải ép sát lại */
            .wrap-icon-header .icon-header-item {
                padding-left: 10px !important;
                padding-right: 10px !important;
            }
        }
    </style>
</head>
<body class="animsition">
	
	@include('client.layouts.header')

	<main>
		@yield('content')
	</main>

	@include('client.layouts.footer')

    <script src="{{ asset('client/vendor/jquery/jquery-3.2.1.min.js') }}"></script>
    <script src="{{ asset('client/vendor/animsition/js/animsition.min.js') }}"></script>
    <script src="{{ asset('client/vendor/bootstrap/js/popper.js') }}"></script>
	<script src="{{ asset('client/vendor/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('client/vendor/select2/select2.min.js') }}"></script>
	<script>
		$(".js-select2").each(function(){
			$(this).select2({
				minimumResultsForSearch: 20,
				dropdownParent: $(this).next('.dropDownSelect2')
			});
		})
	</script>
    <script src="{{ asset('client/vendor/daterangepicker/moment.min.js') }}"></script>
	<script src="{{ asset('client/vendor/daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('client/vendor/slick/slick.min.js') }}"></script>
	<script src="{{ asset('client/js/slick-custom.js') }}"></script>
    <script src="{{ asset('client/vendor/parallax100/parallax100.js') }}"></script>
	<script>
        $('.parallax100').parallax100();
	</script>
    <script src="{{ asset('client/vendor/MagnificPopup/jquery.magnific-popup.min.js') }}"></script>
	<script>
		$('.gallery-lb').each(function() { 
			$(this).magnificPopup({
		        delegate: 'a', 
		        type: 'image',
		        gallery: {
		        	enabled:true
		        },
		        mainClass: 'mfp-fade'
		    });
		});
	</script>
    <script src="{{ asset('client/vendor/isotope/isotope.pkgd.min.js') }}"></script>
    <script src="{{ asset('client/vendor/sweetalert/sweetalert.min.js') }}"></script>
	<script>
		$('.js-addwish-b2').on('click', function(e){
			e.preventDefault();
		});

		$('.js-addwish-b2').each(function(){
			var nameProduct = $(this).parent().parent().find('.js-name-b2').html();
			$(this).on('click', function(){
				swal(nameProduct, "Đã thêm vào danh sách yêu thích !", "success");
				$(this).addClass('js-addedwish-b2');
				$(this).off('click');
			});
		});

		$('.js-addcart-detail').each(function(){
			var nameProduct = $(this).parent().parent().parent().parent().find('.js-name-detail').html();
			$(this).on('click', function(){
				swal(nameProduct, "Đã thêm vào giỏ hàng !", "success");
			});
		});
	</script>
    <script src="{{ asset('client/vendor/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
	<script>
		$('.js-pscroll').each(function(){
			$(this).css('position','relative');
			$(this).css('overflow','hidden');
			var ps = new PerfectScrollbar(this, {
				wheelSpeed: 1,
				scrollingThreshold: 1000,
				wheelPropagation: false,
			});

			$(window).on('resize', function(){
				ps.update();
			})
		});
	</script>
    <script src="{{ asset('client/js/main.js') }}"></script>

	@stack('scripts')
</body>
</html>