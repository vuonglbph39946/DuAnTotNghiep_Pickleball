@extends('admin.layouts.admin')

@section('title','Permissions Management')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">Permissions Management</h1>
        <div class="flex gap-3">
            <a 
                href="{{ route('admin.permissions.roles') }}"
                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium"
            >
                <i class="fa-solid fa-user-tag mr-2"></i>
                Manage Roles
            </a>
            <a 
                href="{{ route('admin.permissions.permissions') }}"
                class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium"
            >
                <i class="fa-solid fa-key mr-2"></i>
                Manage Permissions
            </a>
        </div>
    </div>

    <!-- Tabs -->
    <div class="bg-white rounded-lg shadow">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px" aria-label="Tabs">
                <button onclick="showTab('users')" id="tab-users" class="tab-button active px-6 py-4 text-sm font-medium border-b-2 border-blue-500 text-blue-600">
                    Users & Roles
                </button>
                <button onclick="showTab('permissions')" id="tab-permissions" class="tab-button px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    Role Permissions
                </button>
            </nav>
        </div>

        <!-- Users & Roles Tab -->
        <div id="content-users" class="tab-content p-6">
            <div class="mb-4">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Users and Their Roles</h2>
                @php
                    $columns = [
                        ['label' => '#', 'key' => '__index', 'class' => 'w-12', 'td_class' => 'font-medium'],
                        ['label' => 'Name', 'key' => 'name'],
                        ['label' => 'Email', 'key' => 'email'],
                        ['label' => 'Role', 'key' => 'role', 'type' => 'role-display'],
                        ['label' => 'Status', 'key' => 'status', 'type' => 'status-boolean'],
                    ];

                    $actions = [
                        [
                            'label' => 'Edit Permissions',
                            'icon' => 'fa-key',
                            'onclick' => 'openUserPermissionsDialog({id})',
                            'class' => 'text-purple-600 hover:text-purple-800',
                        ],
                    ];
                @endphp
                <x-common-table :columns="$columns" :rows="$users" :showPagination="true" :actions="$actions" />
            </div>
        </div>

        <!-- Role Permissions Tab -->
        <div id="content-permissions" class="tab-content p-6 hidden">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-gray-900">Role Permissions Matrix</h2>
                <a 
                    href="{{ route('admin.permissions.permissions') }}"
                    class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 font-medium"
                >
                    <i class="fa-solid fa-plus mr-1"></i>
                    Thêm Permission mới
                </a>
            </div>

            @if($permissions->isEmpty())
                {{-- Empty State: chưa có permission nào --}}
                <div class="flex flex-col items-center justify-center py-16 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <i class="fa-solid fa-key text-gray-400 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Chưa có Permission nào</h3>
                    <p class="text-gray-500 mb-4">Hãy thêm permissions trước ở trang <strong>Manage Permissions</strong> để hiển thị bảng phân quyền.</p>
                    <a 
                        href="{{ route('admin.permissions.permissions') }}"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium"
                    >
                        <i class="fa-solid fa-key mr-2"></i>
                        Đến trang Manage Permissions
                    </a>
                </div>
            @elseif(empty($modules) || collect($modules)->flatten()->isEmpty())
                {{-- Có permissions nhưng không parse được module.action --}}
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                    <div class="flex items-center">
                        <i class="fa-solid fa-triangle-exclamation text-yellow-500 mr-2"></i>
                        <p class="text-yellow-800 text-sm">
                            Permissions chưa có định dạng <code class="bg-yellow-100 px-1 rounded">module.action</code> (VD: <code class="bg-yellow-100 px-1 rounded">users.create</code>). 
                            Vui lòng đặt tên permission theo chuẩn để hiển thị bảng Matrix.
                        </p>
                    </div>
                </div>

                {{-- Vẫn hiển thị danh sách permissions thô --}}
                <div class="overflow-x-auto bg-white rounded-lg shadow">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Permission Name</th>
                                @foreach($roles as $role)
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider min-w-[100px]">
                                        {{ $role->name }}
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($permissions as $permission)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="font-medium text-gray-900">{{ $permission->name }}</span>
                                        @if($permission->modules && is_array($permission->modules))
                                            <div class="flex flex-wrap gap-1 mt-1">
                                                @foreach($permission->modules as $mod)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-700">{{ ucfirst($mod) }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </td>
                                    @foreach($roles as $role)
                                        @php $hasPermission = $role->permissions->contains('id', $permission->id); @endphp
                                        <td class="px-4 py-4 whitespace-nowrap text-center">
                                            <button
                                                onclick="togglePermission({{ $role->id }}, '{{ $permission->name }}', this)"
                                                class="permission-toggle w-6 h-6 rounded border-2 transition {{ $hasPermission ? 'bg-green-500 border-green-600' : 'bg-gray-200 border-gray-300' }}"
                                                data-role-id="{{ $role->id }}"
                                                data-permission="{{ $permission->name }}"
                                                data-has-permission="{{ $hasPermission ? '1' : '0' }}"
                                                title="{{ $permission->name }}"
                                            >
                                                @if($hasPermission)
                                                    <i class="fa-solid fa-check text-white text-xs"></i>
                                                @endif
                                            </button>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                {{-- Bảng Matrix đầy đủ: Module/Action từ DB permissions --}}
                <div class="overflow-x-auto bg-white rounded-lg shadow">
                    <table class="min-w-full divide-y divide-gray-200" id="permissionsTreeTable">
                        <thead class="bg-gray-50 sticky top-0 z-10">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-64">
                                    Module / Action
                                </th>
                                @foreach($roles as $role)
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider min-w-[100px]">
                                        {{ $role->name }}
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($modules as $module => $actions)
                                @php $moduleId = 'module-' . $module; @endphp
                                {{-- Module Row (Parent) --}}
                                <tr 
                                    class="module-row bg-gray-50 hover:bg-gray-100"
                                    data-module="{{ $module }}"
                                    id="row-{{ $moduleId }}"
                                >
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            @if(count($actions) > 0)
                                                <button 
                                                    type="button"
                                                    onclick="toggleModule('{{ $module }}')"
                                                    class="module-toggle w-5 h-5 mr-3 shrink-0 text-gray-600 hover:text-gray-800 transition-transform cursor-pointer"
                                                    data-module="{{ $module }}"
                                                    data-expanded="false"
                                                    title="Click để mở rộng/thu gọn"
                                                >
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                    </svg>
                                                </button>
                                            @else
                                                <span class="w-5 h-5 mr-3 shrink-0"></span>
                                            @endif
                                            <span class="font-semibold text-gray-900">{{ ucfirst($module) }}</span>
                                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                                {{ count($actions) }} actions
                                            </span>
                                        </div>
                                    </td>
                                    @foreach($roles as $role)
                                        <td class="px-4 py-4 whitespace-nowrap text-center">
                                            @php
                                                $allActionsHavePermission = count($actions) > 0;
                                                foreach($actions as $action) {
                                                    $permissionName = $module . '.' . $action;
                                                    if (!$role->permissions->contains('name', $permissionName)) {
                                                        $allActionsHavePermission = false;
                                                        break;
                                                    }
                                                }
                                            @endphp
                                            @if(count($actions) > 0)
                                                <button
                                                    onclick="toggleModulePermission({{ $role->id }}, '{{ $module }}', this)"
                                                    class="module-permission-toggle w-6 h-6 rounded border-2 transition {{ $allActionsHavePermission ? 'bg-green-500 border-green-600' : 'bg-gray-200 border-gray-300' }}"
                                                    data-role-id="{{ $role->id }}"
                                                    data-module="{{ $module }}"
                                                    data-all-permissions="{{ $allActionsHavePermission ? '1' : '0' }}"
                                                    title="Toggle all {{ $module }} permissions"
                                                >
                                                    @if($allActionsHavePermission)
                                                        <i class="fa-solid fa-check text-white text-xs"></i>
                                                    @endif
                                                </button>
                                            @else
                                                <span class="text-gray-400 text-xs">—</span>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                                {{-- Action Rows (Children) --}}
                                @foreach($actions as $action)
                                    @php
                                        $actionId = 'action-' . $module . '-' . $action;
                                    @endphp
                                    <tr 
                                        class="action-row hidden"
                                        data-module="{{ $module }}"
                                        data-action="{{ $action }}"
                                        id="row-{{ $actionId }}"
                                    >
                                        <td class="px-6 py-3 whitespace-nowrap">
                                            <div class="flex items-center pl-12">
                                                <span class="w-2 h-2 rounded-full bg-gray-400 mr-2"></span>
                                                <span class="text-gray-700">{{ ucfirst($action) }}</span>
                                                <code class="ml-2 text-xs text-gray-400">{{ $module }}.{{ $action }}</code>
                                            </div>
                                        </td>
                                        @foreach($roles as $role)
                                            @php
                                                $permissionName = $module . '.' . $action;
                                                $hasPermission = $role->permissions->contains('name', $permissionName);
                                            @endphp
                                            <td class="px-4 py-3 whitespace-nowrap text-center">
                                                <button
                                                    onclick="togglePermission({{ $role->id }}, '{{ $permissionName }}', this)"
                                                    class="permission-toggle w-6 h-6 rounded border-2 transition {{ $hasPermission ? 'bg-green-500 border-green-600' : 'bg-gray-200 border-gray-300' }}"
                                                    data-role-id="{{ $role->id }}"
                                                    data-permission="{{ $permissionName }}"
                                                    data-has-permission="{{ $hasPermission ? '1' : '0' }}"
                                                    title="{{ ucfirst($action) }} permission for {{ $module }}"
                                                >
                                                    @if($hasPermission)
                                                        <i class="fa-solid fa-check text-white text-xs"></i>
                                                    @endif
                                                </button>
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>{{-- end content-permissions --}}
    </div>{{-- end bg-white rounded-lg shadow (tabs wrapper) --}}
</div>{{-- end space-y-6 --}}

<script>
    function showTab(tabName) {
        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });
        
        // Remove active class from all tabs
        document.querySelectorAll('.tab-button').forEach(button => {
            button.classList.remove('active', 'border-blue-500', 'text-blue-600');
            button.classList.add('border-transparent', 'text-gray-500');
        });
        
        // Show selected tab content
        document.getElementById('content-' + tabName).classList.remove('hidden');
        
        // Add active class to selected tab
        const activeTab = document.getElementById('tab-' + tabName);
        activeTab.classList.add('active', 'border-blue-500', 'text-blue-600');
        activeTab.classList.remove('border-transparent', 'text-gray-500');
    }

    // Tree functionality
    function toggleModule(module) {
        const button = document.querySelector(`[data-module="${module}"].module-toggle`);
        if (!button) return;
        
        const isExpanded = button.getAttribute('data-expanded') === 'true';
        const icon = button.querySelector('svg');
        
        // Toggle icon rotation
        if (isExpanded) {
            button.setAttribute('data-expanded', 'false');
            icon.style.transform = 'rotate(0deg)';
            hideModuleActions(module);
        } else {
            button.setAttribute('data-expanded', 'true');
            icon.style.transform = 'rotate(90deg)';
            showModuleActions(module);
        }
    }

    function hideModuleActions(module) {
        const actionRows = document.querySelectorAll(`tr.action-row[data-module="${module}"]`);
        actionRows.forEach(row => {
            row.classList.add('hidden');
        });
    }

    function showModuleActions(module) {
        const actionRows = document.querySelectorAll(`tr.action-row[data-module="${module}"]`);
        actionRows.forEach(row => {
            row.classList.remove('hidden');
        });
    }

    function toggleModulePermission(roleId, module, button) {
        const allPermissions = button.getAttribute('data-all-permissions') === '1';
        const newState = !allPermissions;

        // Get all actions for this module
        const modules = @json($modules);
        const actions = modules[module] || [];

        // Update all action permissions for this module and role
        let updateCount = 0;
        actions.forEach((action, index) => {
            const permissionName = module + '.' + action;
            const actionButton = document.querySelector(
                `button.permission-toggle[data-role-id="${roleId}"][data-permission="${permissionName}"]`
            );
            if (actionButton) {
                // Optimistic update
                updatePermissionButton(actionButton, newState);
                // Actually update permission
                updatePermission(roleId, permissionName, newState, actionButton);
                updateCount++;
            }
        });

        // Update module button
        updatePermissionButton(button, newState);
        button.setAttribute('data-all-permissions', newState ? '1' : '0');
    }

    function updatePermissionButton(button, hasPermission) {
        button.setAttribute('data-has-permission', hasPermission ? '1' : '0');
        if (hasPermission) {
            button.classList.add('bg-green-500', 'border-green-600');
            button.classList.remove('bg-gray-200', 'border-gray-300');
            button.innerHTML = '<i class="fa-solid fa-check text-white text-xs"></i>';
        } else {
            button.classList.remove('bg-green-500', 'border-green-600');
            button.classList.add('bg-gray-200', 'border-gray-300');
            button.innerHTML = '';
        }
    }

    function togglePermission(roleId, permissionName, button) {
        const hasPermission = button.getAttribute('data-has-permission') === '1';
        const newState = !hasPermission;

        // Optimistic update
        updatePermissionButton(button, newState);

        // Update permission
        updatePermission(roleId, permissionName, newState, button);

        // Update module button if all actions are checked/unchecked
        const [module, action] = permissionName.split('.');
        updateModuleButtonState(roleId, module);
    }

    async function updatePermission(roleId, permissionName, add, button) {
        const url = '{{ route("admin.permissions.roles.toggle-permission", ":id") }}'.replace(':id', roleId);
        
        try {
            await HttpService.patch(url, {
                permission_name: permissionName,
                add: add
            }, { toast: true });
        } catch (error) {
            // Revert on error
            const hasPermission = button.getAttribute('data-has-permission') === '1';
            updatePermissionButton(button, !hasPermission);
        }
    }

    function updateModuleButtonState(roleId, module) {
        const modules = @json($modules);
        const actions = modules[module] || [];
        const moduleButton = document.querySelector(
            `button.module-permission-toggle[data-role-id="${roleId}"][data-module="${module}"]`
        );
        
        if (!moduleButton) return;

        let allChecked = true;
        actions.forEach(action => {
            const permissionName = module + '.' + action;
            const actionButton = document.querySelector(
                `button.permission-toggle[data-role-id="${roleId}"][data-permission="${permissionName}"]`
            );
            if (actionButton && actionButton.getAttribute('data-has-permission') !== '1') {
                allChecked = false;
            }
        });

        updatePermissionButton(moduleButton, allChecked);
        moduleButton.setAttribute('data-all-permissions', allChecked ? '1' : '0');
    }

    function openUserPermissionsDialog(userId) {
        // Redirect to user edit page
        window.location.href = '{{ route("admin.users.form", ":id") }}?mode=edit'.replace(':id', userId);
    }
</script>

@endsection
