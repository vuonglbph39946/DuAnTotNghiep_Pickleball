<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>@yield('title', 'Trang quản trị | PBall Store')</title>
  
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