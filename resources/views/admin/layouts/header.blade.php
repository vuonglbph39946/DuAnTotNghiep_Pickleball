<header class="navbar navbar-expand-md sticky-top px-4 px-md-5 premium-header w-100">
    <div class="d-flex align-items-center w-100 justify-content-between">
        
        <div class="d-flex align-items-center">
            <button class="navbar-toggler border-0 shadow-none p-0 me-4" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar">
                <i class="fa-solid fa-bars fs-4 text-muted"></i>
            </button>
            <div id="clock" class="d-none d-md-block"></div>
        </div>

        <div class="d-flex align-items-center gap-2 gap-md-3">
            <div class="nav-item">
                <a class="nav-link icon-btn-header position-relative" href="#" title="Thông báo">
                    <i class="fa-regular fa-bell fs-5"></i>
                    <span class="position-absolute p-1 bg-danger rounded-circle border border-2 border-white" style="top: 8px; right: 8px;"></span>
                </a>
            </div>

            <div class="nav-item">
                <button type="button" class="nav-link icon-btn-header border-0 bg-transparent" id="toggleDayNight" title="Giao diện">
                    <i class="fa-regular fa-moon fs-5" id="iconDayNight"></i>
                </button>
            </div>

            <div class="vr mx-2" style="height: 20px; align-self: center; background-color: var(--border-color);"></div>

            <div class="nav-item dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center user-dropdown" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="text-end d-none d-md-block me-3">
                        <p class="mb-0 fw-semibold text-main" style="font-size: 0.85rem; color: var(--text-main);">
                            {{ $admin->full_name == 'Admin Test' ? 'Admin' : $admin->full_name }}
                        </p>
                        
                    </div>
                    <img src="{{ $admin->avatar != 'default-avatar.png' ? asset('storage/' . $admin->avatar) : 'https://ui-avatars.com/api/?name=Admin&background=4f46e5&color=fff&bold=true' }}" 
                         class="rounded-circle shadow-sm" width="40" height="40" style="object-fit: cover;" alt="Avatar">
                </a>
                
                <ul class="dropdown-menu dropdown-menu-end mt-2" aria-labelledby="adminDropdown" style="min-width: 220px;">
                    <li class="px-4 py-3 border-bottom border-light mb-1">
                        <p class="mb-0 fw-bold" style="font-size: 0.9rem; color: var(--text-main);">{{ $admin->full_name == 'Admin Test' ? 'Quản trị viên' : $admin->full_name }}</p>
                        <small style="font-size: 0.75rem; color: var(--text-muted);">admin@pballstore.com</small>
                    </li>
                    <li><a class="dropdown-item mt-2" href="{{ route('admin.profile') }}"><i class="fa-regular fa-user me-3 text-muted"></i> Hồ sơ cá nhân</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fa-solid fa-sliders me-3 text-muted"></i> Cài đặt hệ thống</a></li>
                    <li><hr class="dropdown-divider my-2"></li>
                    <li><a class="dropdown-item hover-danger fw-medium" href="#"><i class="fa-solid fa-arrow-right-from-bracket me-3"></i> Đăng xuất</a></li>
                </ul>
            </div>
        </div>
    </div>
</header>