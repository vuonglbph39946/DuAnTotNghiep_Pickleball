<header class="mb-6">
    <div class="flex items-center justify-between bg-white rounded-md shadow px-4 py-2">
        <!-- Search input with hamburger icon -->
        <div class="flex items-center gap-3 flex-1">
            <button id="hamburger" class="p-2 rounded hover:bg-gray-100">
                <i class="fa-solid fa-bars"></i>
            </button>
            <div class="relative bg-[#f3f3f9] flex items-center flex-1">
                <input type="text" placeholder="Search..." name="search" class="pl-4 pr-10 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-400 w-full" />
                <i class="fa-solid fa-magnifying-glass absolute right-3 text-gray-400"></i>
            </div>
        </div>

        <!-- Right side icons -->
        <div class="flex items-center gap-4">
            <button class="p-2 rounded hover:bg-gray-100"><i class="fa-solid fa-flag-usa"></i></button>
            <button class="p-2 rounded hover:bg-gray-100"><i class="fa-solid fa-th"></i></button>
            <button class="relative p-2 rounded hover:bg-gray-100">
                <i class="fa-solid fa-shopping-cart"></i>
                <span class="absolute -top-1 -right-1 bg-blue-600 text-white text-xs px-1 rounded-full">5</span>
            </button>
            <button class="p-2 rounded hover:bg-gray-100"><i class="fa-solid fa-expand"></i></button>
            <button id="themeToggleHeader" class="p-2 rounded hover:bg-gray-100"><i class="fa-solid fa-moon"></i></button>
            <button class="relative p-2 rounded hover:bg-gray-100">
                <i class="fa-solid fa-bell"></i>
                <span class="absolute -top-1 -right-1 bg-red-600 text-white text-xs px-1 rounded-full">3</span>
            </button>

            <!-- Avatar -->
            <div class="flex items-center gap-3 pl-3 border-l border-gray-100">
                <img src="https://i.pravatar.cc/40" alt="avatar" class="w-8 h-8 rounded-full" />
                <div class="hidden sm:block">
                    <div class="text-sm font-medium">Anna Adame</div>
                    <div class="text-xs text-gray-500">Founder</div>
                </div>
            </div>
        </div>
    </div>
</header>
