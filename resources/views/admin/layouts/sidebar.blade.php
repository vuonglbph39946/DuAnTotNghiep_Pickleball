<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav">
    
    <li class="nav-item {{ request()->is('admin/dashboard') ? 'active' : '' }}">
      <a class="nav-link" href="{{ route('admin.dashboard') }}">
        <i class="mdi mdi-grid-large menu-icon"></i>
        <span class="menu-title">Thống kê</span>
      </a>
    </li>

    <li class="nav-item nav-category">Cửa Hàng</li>
    
    <li class="nav-item {{ request()->is('admin/orders') || (request()->is('admin/orders/*') && !request()->is('admin/orders/cancel-requests*')) ? 'active' : '' }}">
      <a class="nav-link" href="{{ route('admin.orders.index') }}">
        <i class="mdi mdi-receipt menu-icon"></i>
        <span class="menu-title">Đơn Hàng</span>
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

    <li class="nav-item">
    <a class="nav-link" href="{{ route('admin.reviews.index') }}">
        <i class="mdi mdi-star-circle-outline menu-icon"></i>
        <span class="menu-title">Đánh Giá-Bình Luận</span>
    </a>
</li>

  </ul>
</nav>