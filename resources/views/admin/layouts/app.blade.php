<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>@yield('title', 'Trang quản trị | PBall Store')</title>
  
  {{-- Các thư viện gốc của Template (Giữ nguyên để không vỡ layout) --}}
  <link rel="stylesheet" href="{{ asset('admin_assets/vendors/feather/feather.css') }}">
  <link rel="stylesheet" href="{{ asset('admin_assets/vendors/mdi/css/materialdesignicons.min.css') }}">
  <link rel="stylesheet" href="{{ asset('admin_assets/vendors/ti-icons/css/themify-icons.css') }}">
  <link rel="stylesheet" href="{{ asset('admin_assets/vendors/font-awesome/css/font-awesome.min.css') }}">
  <link rel="stylesheet" href="{{ asset('admin_assets/vendors/typicons/typicons.css') }}">
  <link rel="stylesheet" href="{{ asset('admin_assets/vendors/simple-line-icons/css/simple-line-icons.css') }}">
  <link rel="stylesheet" href="{{ asset('admin_assets/vendors/css/vendor.bundle.base.css') }}">
  <link rel="stylesheet" href="{{ asset('admin_assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
  <link rel="stylesheet" href="{{ asset('admin_assets/vendors/datatables.net-bs4/dataTables.bootstrap4.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('admin_assets/js/select.dataTables.min.css') }}">
  <link rel="stylesheet" href="{{ asset('admin_assets/css/style.css') }}">
  <link rel="shortcut icon" href="{{ asset('admin_assets/images/favicon.png') }}" />
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <meta name="csrf-token" content="{{ csrf_token() }}">

  {{-- ================================================================= --}}
  {{-- BỘ CSS ĐẠI TU GIAO DIỆN: CHỐNG CHÌM CHỮ, TĂNG TƯƠNG PHẢN, FIX MÉO ẢNH --}}
  {{-- ================================================================= --}}
  {{-- ================================================================= --}}
  {{-- BỘ CSS ĐẠI TU GIAO DIỆN: CHUẨN MÀU SẮC, KHÔNG BỊ CHÌM HAY ĐEN SAO --}}
  {{-- ================================================================= --}}
  <style>
      /* 1. Phông nền chuẩn và Font chữ sắc nét */
      body, .content-wrapper {
          background-color: #f1f5f9 !important; 
          font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important;
          color: #1e293b; 
      }

      /* 2. Tiêu đề đậm và rõ nét */
      h1, h2, h3, h4, h5, h6, .card-title {
          color: #0f172a !important; 
          font-weight: 700 !important;
      }

      /* 3. CỨU CÁC Ô SELECT, INPUT KHỎI BỊ CHÌM MỜ */
      select, input, .form-control, .form-select, .form-label, .table td {
          color: #1e293b !important;
          font-weight: 500;
      }

      /* 4. BẢO VỆ MÀU TRẮNG CHO DASHBOARD */
      .text-white, .text-white p, .text-white h5, .text-white div {
          color: #ffffff !important;
      }
      .text-white-50 {
          color: rgba(255, 255, 255, 0.7) !important;
      }

      /* 5. CỨU NGÔI SAO TRANG ĐÁNH GIÁ (Ép lại màu Vàng) */
      .review-stars, .review-stars i, .fa-star {
          color: #ffc107 !important;
      }

      /* 6. Làm đẹp Card và Bảng */
      .card {
          /* ĐÃ XÓA !important Ở ĐÂY ĐỂ TRẢ LẠI MÀU CHO DASHBOARD */
          background-color: #ffffff; 
          border-radius: 12px !important;
          box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05) !important;
          border: 1px solid #e2e8f0 !important;
      }
      .table thead th {
          background-color: #f8fafc !important;
          color: #0f172a !important;
          font-weight: 700 !important;
          text-transform: uppercase;
          border-bottom: 2px solid #e2e8f0 !important;
      }
      .table tbody tr {
          border-bottom: 1px solid #f1f5f9 !important;
      }
      .table tbody tr:hover {
          background-color: #f8fafc !important;
      }

      /* 7. FIX LỖI MÉO ẢNH */
      table td img, .table td img {
          object-fit: cover !important;
          border-radius: 6px !important;
          border: 1px solid #cbd5e1;
      }
  </style>
</head>

<body class="with-welcome-text">
  <div class="container-scroller">
    
    @include('admin.layouts.header')
    
    <div class="container-fluid page-body-wrapper">
      
      @include('admin.layouts.sidebar')
      
      <div class="main-panel">
        <div class="content-wrapper">
            {{-- NỘI DUNG CÁC TRANG CON SẼ ĐƯỢC ĐỔ VÀO ĐÂY --}}
            @yield('content')
        </div>
        
        @include('admin.layouts.footer')
      </div>
    </div>
  </div>

  {{-- Các Script gốc của Template --}}
  <script src="{{ asset('admin_assets/vendors/js/vendor.bundle.base.js') }}"></script>
  <script src="{{ asset('admin_assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
  <script src="{{ asset('admin_assets/vendors/chart.js/chart.umd.js') }}"></script>
  <script src="{{ asset('admin_assets/vendors/progressbar.js/progressbar.min.js') }}"></script>
  <script src="{{ asset('admin_assets/js/off-canvas.js') }}"></script>
  <script src="{{ asset('admin_assets/js/template.js') }}"></script>
  <script src="{{ asset('admin_assets/js/settings.js') }}"></script>
  <script src="{{ asset('admin_assets/js/hoverable-collapse.js') }}"></script>
  <script src="{{ asset('admin_assets/js/todolist.js') }}"></script>
  <script src="{{ asset('admin_assets/js/jquery.cookie.js') }}" type="text/javascript"></script>
  <script src="{{ asset('admin_assets/js/dashboard.js') }}"></script>
  @stack('scripts')
</body>
</html>