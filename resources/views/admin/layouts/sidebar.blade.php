<div class="app-sidebar__overlay" data-toggle="sidebar"></div>
<aside class="app-sidebar premium-sidebar">
  
  <div class="app-sidebar__user pb-4 pt-4 px-4 border-bottom border-light">
    <div class="avatar-container position-relative">
        <img class="app-sidebar__user-avatar shadow-sm"
             src="{{ $admin->avatar != 'default-avatar.png' ? asset('storage/' . $admin->avatar) : 'https://ui-avatars.com/api/?name=Admin&background=4f46e5&color=fff&bold=true' }}"
             width="44px" height="44px"
             style="border-radius: 12px; object-fit: cover;"
             alt="Admin Avatar">
        <span class="online-indicator"></span>
    </div>
    <div class="ms-3 mt-1 text-start overflow-hidden">
      <p class="app-sidebar__user-name mb-0 fw-bold text-truncate" style="font-size: 0.95rem; color: var(--text-main);">
        {{ $admin->full_name == 'Admin Test' ? 'Quản trị viên' : $admin->full_name }}
      </p>
      <p class="text-muted fw-medium mt-1 mb-0 text-truncate" style="font-size: 0.75rem;">Hệ thống PBall Store</p>
    </div>
  </div>

  <ul class="app-menu px-3 mt-3 pb-5">

    <li>
      <a class="app-menu__item {{ request()->is('admin/dashboard') ? 'active-pro' : '' }}" href="{{ route('admin.dashboard') }}">
        <div class="icon-wrap"><i class='fa-solid fa-chart-pie'></i></div>
        <span class="app-menu__label">POS & Tổng quan</span>
      </a>
    </li>

    <li class="menu-title">Quản trị Cửa hàng</li>
    <li>
      <a class="app-menu__item {{ request()->is('admin/orders*') ? 'active-pro' : '' }}" href="{{ route('admin.orders.index') }}">
        <div class="icon-wrap"><i class='fa-solid fa-file-invoice-dollar'></i></div>
        <span class="app-menu__label">Quản lý đơn hàng</span>
      </a>
    </li>
    <li>
      <a class="app-menu__item {{ request()->is('admin/products*') ? 'active-pro' : '' }}" href="{{ route('admin.products.index') }}">
        <div class="icon-wrap"><i class='fa-solid fa-box-open'></i></div>
        <span class="app-menu__label">Quản lý sản phẩm</span>
      </a>
    </li>
    <li>
      <a class="app-menu__item {{ request()->is('admin/categories*') ? 'active-pro' : '' }}" href="{{ route('admin.categories.index') }}">
        <div class="icon-wrap"><i class='fa-solid fa-layer-group'></i></div>
        <span class="app-menu__label">Quản lý danh mục</span>
      </a>
    </li>
    <li>
      <a class="app-menu__item {{ request()->routeIs('admin.attributes.*') ? 'active-pro' : '' }}" href="{{ route('admin.attributes.index') }}">
        <div class="icon-wrap"><i class='fa-solid fa-sliders'></i></div>
        <span class="app-menu__label">Thuộc tính & Biến thể</span>
      </a>
    </li>

    
    {{-- ĐÃ THÊM: QUẢN LÝ BANNER VÀO ĐÂY --}}
    <li>
      <a class="app-menu__item {{ request()->is('admin/banners*') ? 'active-pro' : '' }}" href="{{ route('admin.banners.index') }}">
        <div class="icon-wrap"><i class='fa-solid fa-images'></i></div>
        <span class="app-menu__label">Quản lý Banner</span>
      </a>
    </li>
    {{-- KẾT THÚC THÊM QUẢN LÝ BANNER --}}

    
    <li>
      <a class="app-menu__item {{ request()->is('admin/promotions*') ? 'active-pro' : '' }}" href="{{ route('admin.promotions.index') }}">
        <div class="icon-wrap"><i class='fa-solid fa-ticket'></i></div>
        <span class="app-menu__label">Khuyến mãi & Voucher</span>
      </a>
    </li>
    

    
    
    

    <li class="mt-4 pt-4 border-top border-light">
      <a class="app-menu__item view-website-btn" href="{{ route('home') }}" target="_blank">
        <div class="icon-wrap text-primary"><i class='fa-solid fa-arrow-up-right-from-square'></i></div>
        <span class="app-menu__label text-primary fw-semibold">Xem trang web</span>
      </a>
    </li>

  </ul>
</aside>

<style>
  .premium-sidebar {
      background-color: var(--bg-surface);
      border-right: 1px solid var(--border-color);
      box-shadow: 10px 0 30px rgba(0,0,0,0.02);
      transition: var(--transition);
  }
  
  .premium-sidebar .border-light { border-color: var(--border-color) !important; }

  .premium-sidebar .menu-title {
      margin-top: 32px; margin-bottom: 10px; padding: 0 16px;
      text-transform: uppercase; font-weight: 700; color: var(--text-muted);
      font-size: 0.65rem; letter-spacing: 0.8px;
  }

  .premium-sidebar .app-menu__item {
      border-radius: var(--radius-sm); margin-bottom: 2px; padding: 10px 14px;
      display: flex; align-items: center; color: var(--text-main);
      font-weight: 500; transition: var(--transition); position: relative; overflow: hidden;
  }

  .premium-sidebar .icon-wrap {
      display: flex; align-items: center; justify-content: center;
      width: 32px; height: 32px; margin-right: 12px; border-radius: 8px;
      color: var(--text-muted); font-size: 1.05rem; transition: var(--transition);
  }
  
  .premium-sidebar .app-menu__label { font-size: 0.88rem; transition: var(--transition);}

  /* Hover logic */
  .premium-sidebar .app-menu__item:hover { background-color: var(--bg-body); color: var(--text-main); }
  .premium-sidebar .app-menu__item:hover .app-menu__label { transform: translateX(2px); }

  /* Active Pro State - Glowing effect */
  .premium-sidebar .app-menu__item.active-pro {
      background-color: var(--primary-light); color: var(--primary); font-weight: 600;
  }
  .premium-sidebar .app-menu__item.active-pro::before {
      content: ''; position: absolute; left: 0; top: 15%; height: 70%; width: 4px;
      background-color: var(--primary); border-radius: 0 4px 4px 0;
      box-shadow: 0 0 10px var(--primary-glow);
  }
  .premium-sidebar .app-menu__item.active-pro .icon-wrap { color: var(--primary); }

  /* Online Indicator */
  .online-indicator {
      position: absolute; bottom: -2px; right: -2px; width: 12px; height: 12px;
      background-color: #10b981; border: 2px solid var(--bg-surface); border-radius: 50%;
  }

  .premium-sidebar .view-website-btn { background-color: transparent; border: 1px dashed var(--border-color); }
  .premium-sidebar .view-website-btn:hover { background-color: var(--primary-light); border-color: var(--primary); }
</style>