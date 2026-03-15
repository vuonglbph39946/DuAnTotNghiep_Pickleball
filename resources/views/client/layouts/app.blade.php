<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PBall')</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        .tracking-widest-2 { letter-spacing: 0.2em; }
        /* Tùy chỉnh thanh cuộn cho mượt */
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: #fff; }
        ::-webkit-scrollbar-thumb { background: #000; }
    </style>
    @stack('styles')
</head>
<body class="bg-white text-black antialiased relative">

    @include('client.layouts.sidebar')

    @include('client.layouts.header')

    <main>
        @yield('content')
    </main>

    @include('client.layouts.footer')

    {{-- NÚT SCROLL TO TOP MỚI THÊM --}}
    <button id="scrollToTopBtn" onclick="scrollToTop()" class="fixed bottom-8 right-8 bg-black text-white w-12 h-12 rounded-full flex items-center justify-center shadow-2xl transform translate-y-20 opacity-0 transition-all duration-300 z-50 hover:-translate-y-1 hover:bg-gray-800">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
    </button>

    <script>
        function toggleMenu() {
            const sidebar = document.getElementById('mobile-sidebar');
            sidebar.classList.toggle('-translate-x-full');
        }

        // ==========================================
        // Lắng nghe sự kiện cuộn trang cho nút Scroll To Top
        // ==========================================
        const scrollToTopBtn = document.getElementById('scrollToTopBtn');

        window.addEventListener('scroll', () => {
            if (window.scrollY > 300) {
                // Hiện nút khi cuộn xuống quá 300px
                scrollToTopBtn.classList.remove('translate-y-20', 'opacity-0');
                scrollToTopBtn.classList.add('translate-y-0', 'opacity-100');
            } else {
                // Ẩn nút khi đang ở trên cùng
                scrollToTopBtn.classList.add('translate-y-20', 'opacity-0');
                scrollToTopBtn.classList.remove('translate-y-0', 'opacity-100');
            }
        });

        function scrollToTop() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth' // Cuộn lên thật mượt mà
            });
        }
    </script>
    @stack('scripts')
</body>
</html>