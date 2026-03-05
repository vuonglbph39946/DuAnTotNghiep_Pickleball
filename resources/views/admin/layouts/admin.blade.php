<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin - {{ config('app.name') }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @vite(['resources/css/app.css'])
    @vite(['resources/js/app.js'])
    @stack('styles')
    <style>
        /* sidebar collapse behavior */
        .sidebar { 
            @apply transition-width duration-200; 
            background-color: #ffffff; 
            box-shadow: 0 2px 4px rgba(15,34,58,0.12);
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            width: 256px;
            z-index: 30;
            overflow-y: auto;
        }
        .sidebar.collapsed { width: 72px; }
        .sidebar .nav-label { @apply ml-3 whitespace-nowrap; color: #6d7080; }
        .sidebar.collapsed .nav-label { display: none; }
        .sidebar .nav-item { position: relative }
        .sidebar.collapsed .nav-item:hover::after {
            content: attr(data-label);
            position: absolute; left: 90px; top: 50%; transform: translateY(-50%);
            background: rgba(0,0,0,.85); color: #fff; padding: 6px 10px; border-radius: 6px; white-space: nowrap; z-index:50; font-size: .9rem;
        }
        /* default background color */
        body { background-color: #fbf7f4; overflow-x: hidden; }

        /* Main content area với padding cho sidebar */
        .admin-main-content {
            margin-left: 256px;
            min-height: 100vh;
            transition: margin-left 0.2s;
        }
        .sidebar.collapsed ~ .admin-main-content {
            margin-left: 72px;
        }

        /* Navbar fixed */
        .admin-navbar {
            position: sticky;
            top: 0;
            z-index: 20;
            background-color: #fbf7f4;
            padding-top: 1.5rem;
            padding-bottom: 0.5rem;
        }

        /* Admin dark theme */
        body.admin-dark { background-color: #1a1d21; color: #ffffff; }
        body.admin-dark .sidebar { background-color: #081323f0; color: #ffffff; }
        body.admin-dark .admin-navbar { background-color: #1a1d21; }
        body.admin-dark .nav-label { color: #928F7F; }
        body.admin-dark .text-gray-600 { color: rgba(255,255,255,0.9) !important; }
        body.admin-dark .hover\:bg-gray-100:hover { background-color: rgba(255,255,255,0.06) !important; }
        body.admin-dark .bg-blue-600 { background-color: #081323f0 !important; }
        /* dark-mode nav shadow */
        body.admin-dark { box-shadow: 0 2px 4px rgba(240, 221, 197, 0.12); }

        /* Responsive: trên màn hình nhỏ, sidebar chỉ hiện icon (ẩn text menu) */
        @media (max-width: 768px) {
            .sidebar {
                width: 72px;
            }

            .sidebar .nav-label {
                display: none;
            }

            .admin-main-content {
                margin-left: 72px;
            }
        }
    </style>
</head>
<body class="bg-white text-gray-900">

<div class="flex min-h-screen">
    @include('admin.includes.sidebar')

    <main class="admin-main-content flex-1 p-6">
        <div class="admin-navbar">
            @include('admin.includes.navbar')
        </div>

        <div class="mt-6">
            @yield('content')
        </div>
    </main>
</div>

{{-- Toast Notification Component --}}
<x-toast />

<script>
    (function(){
        const sidebar = document.getElementById('adminSidebar');
        const hamburger = document.getElementById('hamburger');
        const themeBtn = document.getElementById('toggleTheme');
        const themeHeaderBtn = document.getElementById('themeToggleHeader');
        
        if(hamburger) hamburger.addEventListener('click', ()=> sidebar.classList.toggle('collapsed'));
        if(themeBtn) themeBtn.addEventListener('click', ()=> document.body.classList.toggle('admin-dark'));
        if(themeHeaderBtn) themeHeaderBtn.addEventListener('click', ()=> document.body.classList.toggle('admin-dark'));

        // Hiển thị flash messages từ Laravel session thành toast
        @if(session('success'))
            if (typeof showToast === 'function') {
                showToast('{{ session('success') }}', 'success');
            }
        @endif

        @if(session('error'))
            if (typeof showToast === 'function') {
                showToast('{{ session('error') }}', 'error');
            }
        @endif

        @if(session('info'))
            if (typeof showToast === 'function') {
                showToast('{{ session('info') }}', 'info');
            }
        @endif
    })();
</script>

@stack('scripts')

</body>
</html>
