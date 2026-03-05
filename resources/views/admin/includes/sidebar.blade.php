<aside id="adminSidebar" class="sidebar flex flex-col p-4">
    <div class="flex items-center mb-4">
        <div class="text-xl font-semibold">Admin</div>
    </div>

    <nav class="flex-1">
        <ul class="space-y-1">
            @foreach(config('admin_menu') as $menu)
                <li class="nav-item " data-label="{{ $menu['label'] }}">
                    <a href="{{ is_callable($menu['url']) ? $menu['url']() : $menu['url'] }}" class="flex gap-2 items-center px-2 py-2 rounded hover:bg-gray-100">
                        <i class="fa-solid {{ $menu['icon'] }} text-gray-600"></i>
                        <span class="nav-label">{{ $menu['label'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </nav>

    <div class="mt-4 hidden">
        <button id="toggleTheme" class="w-full px-3 py-2 rounded bg-blue-600 text-white text-sm">Toggle Theme</button>
    </div>
</aside>
