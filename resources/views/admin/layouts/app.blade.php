<!DOCTYPE html>
<html lang="en">
<head>
  <title>@yield('title', 'Trang quản trị | PBall Store')</title>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link rel="stylesheet" href="{{ asset('css/main.css') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  
  <style>
    /* ======================================================== */
    /* VERCEL/STRIPE SAAS UI - PBALL STORE SYSTEM               */
    /* ======================================================== */
    :root {
        --primary: #4f46e5;        /* Indigo 600 - Sang trọng, trendy */
        --primary-hover: #4338ca;  
        --primary-glow: rgba(79, 70, 229, 0.3);
        --primary-light: rgba(79, 70, 229, 0.08); 
        
        --bg-body: #f4f4f5;        /* Zinc 100 */
        --bg-surface: #ffffff;     
        --text-main: #09090b;      /* Zinc 950 */
        --text-muted: #71717a;     /* Zinc 500 */
        --border-color: #e4e4e7;   /* Zinc 200 */
        
        --shadow-xs: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow-sm: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        --shadow-float: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.02);
        
        --radius-sm: 8px;
        --radius-md: 12px;
        --radius-lg: 16px;
        --transition: all 0.25s cubic-bezier(0.25, 0.8, 0.25, 1);
    }

    body { 
        font-family: 'Inter', -apple-system, sans-serif; 
        background-color: var(--bg-body); 
        color: var(--text-main); 
        transition: background-color 0.3s ease; 
        -webkit-font-smoothing: antialiased;
        text-rendering: optimizeLegibility;
    }
    
    /* CUSTOM SCROLLBAR (Tuyệt chiêu làm web mượt) */
    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: #d4d4d8; border-radius: 10px; }
    ::-webkit-scrollbar-thumb:hover { background: #a1a1aa; }

    /* Layout */
    .app-right-wrapper { width: 100%; min-width: 0; box-sizing: border-box; overflow-x: hidden; display: flex; flex-direction: column; min-height: 100vh; }
    @media (min-width: 768px) { 
        .app-right-wrapper { margin-left: 260px; width: calc(100% - 260px); } 
        .app-right-wrapper .app-content { margin-left: 0 !important; flex-grow: 1; padding: 32px; } 
    }
    @media (max-width: 767px) { 
        .app-right-wrapper .app-content { margin-left: 0 !important; flex-grow: 1; padding: 16px; } 
    }

    /* Cards & Panels */
    .card, .premium-card { 
        background-color: var(--bg-surface) !important; 
        border: 1px solid var(--border-color) !important; 
        border-radius: var(--radius-md) !important; 
        box-shadow: var(--shadow-sm) !important; 
        transition: var(--transition);
    }
    .card-header { 
        background-color: transparent !important; 
        color: var(--text-main) !important; 
        border-bottom: 1px solid var(--border-color) !important; 
        font-weight: 600; padding: 16px 24px;
    }

    /* Inputs */
    .form-control, .form-select { border-radius: var(--radius-sm) !important; border: 1px solid var(--border-color); padding: 10px 14px; font-weight: 500; transition: var(--transition); color: var(--text-main); background: var(--bg-surface); }
    .form-control:focus, .form-select:focus { 
        border-color: var(--primary); 
        box-shadow: 0 0 0 3px var(--primary-light) !important; 
    }

    /* Header Premium (Glassmorphism Siêu Thực) */
    .premium-header { 
        background: rgba(255, 255, 255, 0.75) !important; 
        backdrop-filter: blur(16px) saturate(180%); -webkit-backdrop-filter: blur(16px) saturate(180%);
        border-bottom: 1px solid var(--border-color) !important; 
        box-shadow: none !important; height: 72px;
    }
    
    /* Icon Buttons */
    .icon-btn-header { 
        width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; 
        border-radius: 10px; color: var(--text-muted) !important; transition: var(--transition); 
        background: transparent; border: 1px solid transparent;
    }
    .icon-btn-header:hover { background-color: var(--bg-body); color: var(--text-main) !important; border-color: var(--border-color); transform: translateY(-1px); }
    
    .user-dropdown { border-radius: 50px; padding: 4px 12px 4px 4px; transition: var(--transition); border: 1px solid transparent; cursor: pointer; }
    .user-dropdown:hover { background-color: var(--bg-body); border-color: var(--border-color); }
    
    /* Dropdown Animation */
    .dropdown-menu { 
        border-radius: var(--radius-md); border: 1px solid var(--border-color); 
        box-shadow: var(--shadow-float) !important; padding: 8px; 
        animation: dropFloat 0.2s cubic-bezier(0.25, 0.8, 0.25, 1) forwards;
        transform-origin: top right;
    }
    .dropdown-item { border-radius: 6px; font-weight: 500; padding: 10px 16px; color: var(--text-main); transition: var(--transition); margin-bottom: 2px;}
    .dropdown-item:hover { background-color: var(--bg-body); color: var(--primary) !important; transform: translateX(4px); }
    
    @keyframes dropFloat { from { opacity: 0; transform: scale(0.95) translateY(-10px); } to { opacity: 1; transform: scale(1) translateY(0); } }

    /* ======================================================== */
    /* DEEP DARK MODE (ĐEN NHÁM OLED CHUYÊN NGHIỆP)             */
    /* ======================================================== */
    body.admin-dark-mode { 
        --bg-body: #09090b;        /* Zinc 950 */
        --bg-surface: #18181b;     /* Zinc 900 */
        --text-main: #f4f4f5;      /* Zinc 50 */
        --text-muted: #a1a1aa;     /* Zinc 400 */
        --border-color: #27272a;   /* Zinc 800 */
        --primary-light: rgba(79, 70, 229, 0.15); 
        --shadow-sm: 0 4px 6px -1px rgba(0, 0, 0, 0.3);
    }
    
    body.admin-dark-mode ::-webkit-scrollbar-thumb { background: #3f3f46; }
    body.admin-dark-mode ::-webkit-scrollbar-thumb:hover { background: #52525b; }

    body.admin-dark-mode .premium-header { background: rgba(9, 9, 11, 0.8) !important; border-bottom: 1px solid rgba(255,255,255,0.08) !important;}
    
    /* Hiệu ứng viền sáng hắt từ bên trong cho thẻ Card ở Dark Mode */
    body.admin-dark-mode .card, body.admin-dark-mode .premium-card, body.admin-dark-mode .dropdown-menu { 
        box-shadow: inset 0 1px 0 rgba(255,255,255,0.05), 0 10px 30px rgba(0,0,0,0.5) !important; 
        border: 1px solid var(--border-color) !important;
    }
    
    body.admin-dark-mode .icon-btn-header:hover { background-color: var(--bg-surface); color: #fff !important; }
    body.admin-dark-mode .user-dropdown:hover { background-color: var(--bg-surface); }
  </style>
</head>

<body onload="time(); initDayNight();" class="app sidebar-mini">
  @include('admin.layouts.sidebar')

  <div class="app-right-wrapper">
    @include('admin.layouts.header')

    <main class="app-content">
      @yield('content')
    </main>
    
    @include('admin.layouts.footer')
  </div>

  <script src="{{ asset('js/jquery-3.2.1.min.js') }}"></script>
  <script src="{{ asset('js/popper.min.js') }}"></script>
  <script src="{{ asset('js/bootstrap.min.js') }}"></script>
  <script src="{{ asset('js/main.js') }}"></script>
  <script src="{{ asset('js/plugins/pace.min.js') }}"></script>

  @yield('scripts')

  <script>
    function time() {
      var today = new Date();
      var day = ["Chủ Nhật", "Thứ Hai", "Thứ Ba", "Thứ Tư", "Thứ Năm", "Thứ Sáu", "Thứ Bảy"][today.getDay()];
      var dd = checkTime(today.getDate());
      var mm = checkTime(today.getMonth() + 1);
      var yyyy = today.getFullYear();
      var h = checkTime(today.getHours());
      var m = checkTime(today.getMinutes());
      
      var clockEl = document.getElementById("clock");
      if(clockEl) clockEl.innerHTML = `<span class="fw-medium text-muted" style="font-size: 0.85rem; letter-spacing: 0.5px;"><i class="fa-regular fa-calendar-days me-1"></i> ${day}, ${dd}/${mm}/${yyyy} <span class="mx-2 opacity-50">|</span> <i class="fa-regular fa-clock me-1"></i> ${h}:${m}</span>`;
      setTimeout(time, 1000);
    }
    function checkTime(i) { return (i < 10) ? "0" + i : i; }

    function initDayNight() {
      var isDark = localStorage.getItem('adminDarkMode') === '1';
      if (isDark) document.body.classList.add('admin-dark-mode');
      updateDayNightIcon(isDark);
      
      var btn = document.getElementById('toggleDayNight');
      if (btn) {
        btn.addEventListener('click', function() {
            document.body.classList.toggle('admin-dark-mode');
            var dark = document.body.classList.contains('admin-dark-mode');
            localStorage.setItem('adminDarkMode', dark ? '1' : '0');
            updateDayNightIcon(dark);
        });
      }
    }
    
    function updateDayNightIcon(isDark) {
      var icon = document.getElementById('iconDayNight');
      if (!icon) return;
      icon.className = isDark ? 'fa-solid fa-sun text-warning' : 'fa-solid fa-moon text-muted';
    }
  </script>
</body>
</html>