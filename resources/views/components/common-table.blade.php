@props([
    'columns' => [],        // array of ['label' => 'Name', 'key' => 'name', 'class' => '']
    'rows' => null,         // array or Collection or Paginator
    'searchPlaceholder' => 'Type a keyword...',
    'showPagination' => true,
    'actions' => [],        // array of ['label' => 'Edit', 'icon' => 'fa-edit', 'onclick' => 'editItem(id)', 'class' => 'text-blue-600']
])

<style>
    /* table header background: light and dark variants (8-digit hex includes alpha) */
    .admin-table-container { background-color: #ffffff;  }
    body.admin-dark .admin-table-container { background-color: #212529; border-color: #e9ebec; color: rgba(255,255,255,0.9); }

    .admin-table { background: transparent; }
    .admin-table thead th { background-color: #f3f6f9bf; border-bottom: 1px solid #e9ebec; }
    body.admin-dark .admin-table thead th { background-color: #282b2ebf; color: rgba(255,255,255,0.9); border-bottom: 1px solid #e9ebec; }
    .admin-table tbody td { border-bottom: 1px solid #e9ebec; }
</style>


<div class="admin-table-container rounded-md shadow-sm overflow-hidden">
    <div class="p-4  border-gray-100 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="relative">
                <input 
                    type="search" 
                    placeholder="{{ $searchPlaceholder }}" 
                    class="pl-10 pr-4 py-2 rounded-lg border border-gray-200  focus:outline-none focus:ring-2 focus:ring-blue-400 w-64"
                    data-table-search
                />
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"><i class="fa-solid fa-magnifying-glass"></i></span>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="admin-table w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    @foreach($columns as $col)
                        <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider {{ $col['class'] ?? '' }}">
                            {{ $col['label'] }}
                        </th>
                    @endforeach
                    @if(count($actions) > 0)
                        <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    @endif
                </tr>
            </thead>
            <tbody class=" divide-y divide-gray-100">
                @forelse($rows as $row)
                    <tr class="hover:bg-gray-50" data-table-row data-row-id="{{ is_array($row) ? ($row['id'] ?? '') : ($row->id ?? '') }}" data-row-data="{{ json_encode(is_array($row) ? $row : $row->toArray()) }}">
                        @foreach($columns as $col)
                            @php
                                $key = $col['key'];
                                $value = is_array($row) ? ($row[$key] ?? '') : ($row->{$key} ?? '');
                            @endphp
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 {{ $col['td_class'] ?? '' }}">
                                {{-- Cột index (#): chỉ đếm root categories, không đếm children --}}
                                @if($key === '__index')
                                    @php
                                        $rootIndex = is_array($row) ? ($row['root_index'] ?? null) : ($row->root_index ?? null);
                                        $rowLoop = $loop->parent ?? null; // vòng lặp foreach $rows
                                        $level = is_array($row) ? ($row['level'] ?? 0) : ($row->level ?? 0);
                                    @endphp
                                    @if($level > 0)
                                        {{-- Don't show index for children in tree --}}
                                    @else
                                        @if($rootIndex !== null)
                                            {{ $rootIndex }}
                                        @elseif($rowLoop && is_object($rows) && method_exists($rows, 'firstItem'))
                                            {{-- Normal pagination index --}}
                                            {{ $rows->firstItem() + $rowLoop->index }}
                                        @elseif($rowLoop)
                                            {{ $rowLoop->iteration }}
                                        @else
                                            {{ $loop->iteration }}
                                        @endif
                                    @endif
                                @elseif(isset($col['type']) && $col['type'] === 'status')
                                    @php
                                        $statuses = [
                                            0 => ['text' => 'Draft', 'class' => 'bg-gray-100 text-gray-800'],
                                            1 => ['text' => 'Published', 'class' => 'bg-green-100 text-green-800'],
                                            2 => ['text' => 'Archived', 'class' => 'bg-yellow-100 text-yellow-800']
                                        ];
                                        $status = $statuses[$value] ?? $statuses[0];
                                    @endphp
                                    <span class="px-2 py-1 rounded text-xs font-medium {{ $status['class'] }}">
                                        {{ $status['text'] }}
                                    </span>
                                @elseif(isset($col['type']) && $col['type'] === 'status-boolean')
                                    @if($value)
                                        <span class="px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800">Active</span>
                                    @else
                                        <span class="px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-800">Inactive</span>
                                    @endif
                                @elseif(isset($col['type']) && $col['type'] === 'role-display')
                                    @if($value && (is_array($value) || is_object($value)))
                                        @php
                                            $roleName = is_array($value) ? ($value['name'] ?? '') : ($value->name ?? '');
                                        @endphp
                                        @if($roleName)
                                            <span class="px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $roleName }}
                                            </span>
                                        @else
                                            <span class="text-gray-400 text-sm">No role</span>
                                        @endif
                                    @else
                                        <span class="text-gray-400 text-sm">No role</span>
                                    @endif
                                @elseif(isset($col['type']) && $col['type'] === 'roles-list')
                                    @if(is_array($value) || (is_object($value) && method_exists($value, 'toArray')))
                                        @php
                                            $roles = is_array($value) ? $value : $value->toArray();
                                        @endphp
                                        @if(count($roles) > 0)
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($roles as $role)
                                                    @php
                                                        $roleName = is_array($role) ? ($role['name'] ?? '') : ($role->name ?? '');
                                                    @endphp
                                                    <span class="px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                        {{ $roleName }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-gray-400 text-sm">No roles</span>
                                        @endif
                                    @else
                                        <span class="text-gray-400 text-sm">No roles</span>
                                    @endif
                                @elseif(isset($col['type']) && $col['type'] === 'permissions-list')
                                    @if(is_array($value) || (is_object($value) && method_exists($value, 'toArray')))
                                        @php
                                            $permissions = is_array($value) ? $value : $value->toArray();
                                        @endphp
                                        @if(count($permissions) > 0)
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($permissions as $permission)
                                                    @php
                                                        $permissionName = is_array($permission) ? ($permission['name'] ?? '') : ($permission->name ?? '');
                                                    @endphp
                                                    <span class="px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800">
                                                        {{ $permissionName }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-gray-400 text-sm">No permissions</span>
                                        @endif
                                    @else
                                        <span class="text-gray-400 text-sm">No permissions</span>
                                    @endif
                                @elseif(isset($col['type']) && $col['type'] === 'tree-name')
                                    @php
                                        $hasChildren = is_array($row) ? ($row['has_children'] ?? false) : ($row->has_children ?? false);
                                        $isLeaf = is_array($row) ? ($row['is_leaf'] ?? true) : ($row->is_leaf ?? true);
                                        $level = is_array($row) ? ($row['level'] ?? 0) : ($row->level ?? 0);
                                        $indent = $level * 24; // 24px per level
                                        $isChild = $level > 0; // Category con (có level > 0)
                                        $rowId = is_array($row) ? ($row['id'] ?? '') : ($row->id ?? '');
                                        // Chỉ hiển thị icon toggle nếu có children (không phải leaf)
                                        $showToggleIcon = $hasChildren && !$isLeaf;
                                    @endphp
                                    <div class="flex items-center" style="padding-left: {{ $isChild ? $indent : 0 }}px;">
                                        @if($showToggleIcon)
                                            <button 
                                                type="button"
                                                onclick="toggleCategoryTree({{ $rowId }})"
                                                class="category-toggle w-4 h-4 mr-2 shrink-0 text-gray-500 hover:text-gray-700 transition-transform cursor-pointer"
                                                data-category-id="{{ $rowId }}"
                                                data-expanded="false"
                                                title="Click to expand/collapse"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                </svg>
                                            </button>
                                        @elseif($isChild)
                                            {{-- Category con nhưng không có children - không có icon, chỉ có indentation --}}
                                            <span class="w-4 h-4 mr-2 shrink-0"></span>
                                        @endif
                                        <span class="font-medium text-gray-900">{{ $value }}</span>
                                    </div>
                                @elseif(isset($col['type']) && $col['type'] === 'status-select')
                                    @php
                                        $rowId = is_array($row) ? ($row['id'] ?? '') : ($row->id ?? '');
                                        $statuses = [
                                            0 => ['text' => 'Draft', 'class' => 'bg-gray-100 text-gray-800'],
                                            1 => ['text' => 'Published', 'class' => 'bg-green-100 text-green-800'],
                                            2 => ['text' => 'Archived', 'class' => 'bg-yellow-100 text-yellow-800']
                                        ];
                                        $currentStatus = $statuses[$value] ?? $statuses[0];
                                    @endphp
                                    <select 
                                        class="status-select px-2 py-1 rounded text-xs font-medium border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-400 cursor-pointer {{ $currentStatus['class'] }}"
                                        data-news-id="{{ $rowId }}"
                                        data-current-status="{{ $value }}"
                                        onchange="confirmUpdateStatus({{ $rowId }}, this.value, this.getAttribute('data-current-status'), this)"
                                    >
                                        @foreach($statuses as $statusValue => $statusInfo)
                                            <option value="{{ $statusValue }}" {{ $value == $statusValue ? 'selected' : '' }}>
                                                {{ $statusInfo['text'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                @elseif(isset($col['render']) && is_callable($col['render']))
                                    {!! $col['render']($value) !!}
                                @elseif(!empty($col['link']) && $col['link'] === true && filter_var($value, FILTER_VALIDATE_EMAIL))
                                    <a href="mailto:{{ $value }}" class="text-blue-600 hover:underline">{{ $value }}</a>
                                @else
                                    {{ $value }}
                                @endif
                            </td>
                        @endforeach
                        @if(count($actions) > 0)
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                <div class="flex items-center gap-2">
                                    @foreach($actions as $action)
                                        @php
                                            $rowId = is_array($row) ? ($row['id'] ?? $row[$action['id_key'] ?? 'id'] ?? '') : ($row->id ?? '');
                                        @endphp
                                        @if(!empty($action['url']))
                                            <a 
                                                href="{{ route($action['url'], $rowId) }}"
                                                class="{{ $action['class'] ?? 'text-gray-600 hover:text-gray-900' }} p-2 rounded hover:bg-gray-100 transition"
                                                title="{{ $action['label'] ?? '' }}"
                                            >
                                                <i class="fa-solid {{ $action['icon'] ?? 'fa-ellipsis' }}"></i>
                                            </a>
                                        @else
                                            @php
                                                $onclick = str_replace(['{id}', '{row}'], [$rowId, json_encode(is_array($row) ? $row : $row->toArray())], $action['onclick'] ?? '');
                                            @endphp
                                            <button 
                                                onclick="{{ $onclick }}"
                                                class="{{ $action['class'] ?? 'text-gray-600 hover:text-gray-900' }} p-2 rounded hover:bg-gray-100 transition"
                                                title="{{ $action['label'] ?? '' }}"
                                            >
                                                <i class="fa-solid {{ $action['icon'] ?? 'fa-ellipsis' }}"></i>
                                            </button>
                                        @endif
                                    @endforeach
                                </div>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($columns) + (count($actions) > 0 ? 1 : 0) }}" class="px-6 py-8 text-center text-sm text-gray-500">No records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($showPagination)
        <div class="p-4 border-t border-gray-100 flex items-center justify-between">
            <div class="text-sm text-gray-500">
                @if(is_object($rows) && method_exists($rows, 'total'))
                    Showing {{ $rows->firstItem() }} to {{ $rows->lastItem() }} of {{ $rows->total() }} results
                @else
                    Showing 1 to {{ is_array($rows) ? count($rows) : (is_object($rows) && isset($rows->count) ? $rows->count : 0) }} of {{ is_array($rows) ? count($rows) : (is_object($rows) && isset($rows->count) ? $rows->count : 0) }} results
                @endif
            </div>

            <div class="flex items-center gap-2">
                @if(is_object($rows) && method_exists($rows, 'links'))
                    {{-- Sử dụng phân trang Tailwind mặc định của Laravel --}}
                    {{ $rows->links() }}
                @else
                    {{-- Trường hợp rows không phải paginator: hiển thị phân trang giả (optional) --}}
                    <nav class="inline-flex -space-x-px rounded-md shadow-sm">
                        <span class="px-3 py-2 ml-0 rounded-l-md border border-gray-200 text-sm text-gray-400 cursor-not-allowed">Previous</span>
                        <span class="px-3 py-2 border border-gray-200 text-sm text-[#687cfe]">1</span>
                        <span class="px-3 py-2 rounded-r-md border border-gray-200 text-sm text-gray-400 cursor-not-allowed">Next</span>
                    </nav>
                @endif
            </div>
        </div>
    @endif
</div>

@once
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('.admin-table-container').forEach(function (container) {
                    const searchInput = container.querySelector('[data-table-search]');
                    const rows = container.querySelectorAll('tbody [data-table-row]');

                    if (!searchInput || !rows.length) return;

                    searchInput.addEventListener('input', function () {
                        const keyword = this.value.toLowerCase();

                        rows.forEach(function (row) {
                            const text = row.textContent.toLowerCase();
                            row.style.display = text.includes(keyword) ? '' : 'none';
                        });
                    });
                });
            });
        </script>
    @endpush
@endonce
