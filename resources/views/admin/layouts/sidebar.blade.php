<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav">
    
    <li class="nav-item {{ request()->is('admin/dashboard') ? 'active' : '' }}">
      <a class="nav-link" href="{{ route('admin.dashboard') }}">
        <i class="mdi mdi-grid-large menu-icon"></i>
        <span class="menu-title">Thống kê</span>
      </a>
    </li>

    <li class="nav-item nav-category">Cửa Hàng</li>
    
    {{-- ĐÃ SỬA: Dùng routeIs để nhận diện Active chuẩn xác hơn --}}
    <li class="nav-item {{ request()->routeIs('admin.orders.index') || request()->routeIs('admin.orders.show') ? 'active' : '' }}">
      <a class="nav-link" href="{{ route('admin.orders.index') }}">
        <i class="mdi mdi-receipt menu-icon"></i>
        <span class="menu-title">Đơn Hàng</span>
      </a>
    </li>

    {{-- ======================================================== --}}
    {{-- ĐÃ BỔ SUNG: MENU QUẢN LÝ YÊU CẦU HỦY ĐƠN RIÊNG BIỆT --}}
    {{-- ======================================================== --}}
    <li class="nav-item {{ request()->routeIs('admin.orders.cancel_requests') ? 'active' : '' }}">
      <a class="nav-link" href="{{ route('admin.orders.cancel_requests') }}">
        <i class="mdi mdi-text-box-remove-outline menu-icon text-danger"></i>
        <span class="menu-title text-danger fw-bold">Quản lý hủy đơn</span>
      </a>
    </li>

    <li class="nav-item {{ request()->is('admin/products*') ? 'active' : '' }}">
      <a class="nav-link" href="{{ route('admin.products.index') }}">
        <i class="mdi mdi-cube-outline menu-icon"></i>
        <span class="menu-title">Sản Phẩm</span>
      </a>
    </li>

    <li class="nav-item {{ request()->is('admin/categories*') ? 'active' : '' }}">
      <a class="nav-link" href="{{ route('admin.categories.index') }}">
        <i class="mdi mdi-folder-outline menu-icon"></i>
        <span class="menu-title">Danh Mục</span>
      </a>
    </li>

    <li class="nav-item {{ request()->is('admin/attributes*') ? 'active' : '' }}">
      <a class="nav-link" href="{{ route('admin.attributes.index') }}">
        <i class="mdi mdi-layers-outline menu-icon"></i>
        <span class="menu-title">Thuộc Tính</span>
      </a>
    </li>

    <li class="nav-item {{ request()->is('admin/banners*') ? 'active' : '' }}">
      <a class="nav-link" href="{{ route('admin.banners.index') }}">
        <i class="mdi mdi-image-multiple-outline menu-icon"></i>
        <span class="menu-title">Banner</span>
      </a>
    </li>

    {{-- ĐÃ SỬA LẠI ĐƯỜNG DẪN COUPON Ở ĐÂY CHO KHỚP VỚI CONTROLLER --}}
    <li class="nav-item {{ request()->is('admin/coupons*') ? 'active' : '' }}">
      <a class="nav-link" href="{{ route('admin.coupons.index') }}">
        <i class="mdi mdi-ticket-percent-outline menu-icon"></i>
        <span class="menu-title">Mã Giảm Giá</span>
      </a>
    </li>

    <li class="nav-item {{ request()->is('admin/reviews*') ? 'active' : '' }}">
      <a class="nav-link" href="{{ route('admin.reviews.index') }}">
        <i class="mdi mdi-star-circle-outline menu-icon"></i>
        <span class="menu-title">Đánh Giá-Bình Luận</span>
      </a>
    </li>

    {{-- ======================================================== --}}
    {{-- ĐÃ BỔ SUNG: PHÂN MỤC HỆ THỐNG VÀ QUẢN LÝ NGƯỜI DÙNG --}}
    {{-- ======================================================== --}}
    <li class="nav-item nav-category">Hệ Thống</li>

    <li class="nav-item {{ request()->is('admin/users*') || request()->routeIs('admin.users.*') ? 'active' : '' }}">
      <a class="nav-link" href="{{ route('admin.users.index') }}">
        <i class="mdi mdi-account-group-outline menu-icon text-info"></i>
        <span class="menu-title fw-bold">Quản lý Người dùng</span>
      </a>
    </li>

  </ul>
</nav>