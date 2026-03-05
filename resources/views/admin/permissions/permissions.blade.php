@extends('admin.layouts.admin')

@section('title','Permissions Management')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">Permissions Management</h1>
        <button 
            onclick="openPermissionDialog()" 
            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium"
        >
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            Add Permission
        </button>
    </div>

    <!-- Permissions Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6">
            @php
                $columns = [
                    ['label' => '#', 'key' => '__index', 'class' => 'w-12', 'td_class' => 'font-medium'],
                    ['label' => 'Name', 'key' => 'name'],
                    ['label' => 'Modules', 'key' => 'modules', 'render' => function($value) {
                        if (!$value) return '-';
                        $modules = is_string($value) ? json_decode($value, true) : $value;
                        if (is_array($modules)) {
                            return implode(', ', array_map('ucfirst', $modules));
                        }
                        return ucfirst($value);
                    }],
                    ['label' => 'Description', 'key' => 'description'],
                    ['label' => 'Created', 'key' => 'created_at'],
                ];

                $actions = [
                    [
                        'label' => 'Edit',
                        'icon' => 'fa-edit',
                        'onclick' => 'openPermissionDialog({id})',
                        'class' => 'text-blue-600 hover:text-blue-800',
                    ],
                    [
                        'label' => 'Delete',
                        'icon' => 'fa-trash',
                        'onclick' => 'openDeletePermissionDialog({id})',
                        'class' => 'text-red-600 hover:text-red-800',
                    ],
                ];
            @endphp

            <x-common-table :columns="$columns" :rows="$permissions" :showPagination="true" :actions="$actions" />
        </div>
    </div>
</div>

<!-- CREATE/EDIT PERMISSION DIALOG -->
<x-dialog 
    title="Permission" 
    subtitle=""
    size="lg"
    id="permissionDialog"
>
    <form
        action=""
        method="POST"
        id="permissionForm"
        class="space-y-5"
    >
        @csrf
        <input type="hidden" name="_method" id="methodInput" value="POST">

        {{-- ===== MODE: CREATE (multi) / EDIT (single) ===== --}}
        {{-- Khi CREATE: hiện checkboxes multi; khi EDIT: hiện input name đơn --}}

        {{-- ==== Configuration Area (Always Shown) ==== --}}
        <div>
            {{-- Modules --}}
            <div class="mb-4">
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-sm font-medium text-gray-700">
                        Module <span class="text-red-500">*</span>
                    </label>
                    <button type="button" id="toggleAllModulesBtn" onclick="toggleAllCheckboxes('module-cb', this)"
                        class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                        Chọn tất cả
                    </button>
                </div>
                <div class="grid grid-cols-3 gap-2 border border-gray-200 rounded-lg p-3 bg-gray-50">
                    @foreach(array_keys($modules) as $mod)
                    <label class="flex items-center gap-2 cursor-pointer hover:bg-white rounded px-2 py-1 transition">
                        <input type="checkbox" name="modules[]" value="{{ $mod }}"
                            data-module-name="{{ $mod }}"
                            class="module-cb w-4 h-4 text-blue-600 border-gray-300 rounded"
                            onchange="handleSelectionChange('module', this)">
                        <span class="text-sm text-gray-700">{{ ucfirst($mod) }}</span>
                    </label>
                    @endforeach
                    <label class="flex items-center gap-2 cursor-pointer hover:bg-white rounded px-2 py-1 transition col-span-3 border-t border-dashed border-gray-200 mt-1 pt-2">
                        <input type="checkbox" id="customModuleCb" class="module-cb w-4 h-4 text-purple-600 border-gray-300 rounded"
                            onchange="toggleCustomModuleInput(); handleSelectionChange('module', this)">
                        <span class="text-sm text-gray-500 italic">✏️ Tên khác:</span>
                        <input type="text" id="customModuleText"
                            class="flex-1 text-sm border-b border-dashed border-gray-300 bg-transparent focus:outline-none focus:border-purple-400 px-1"
                            placeholder="tên_module..."
                            oninput="updatePreview()" disabled>
                    </label>
                </div>
            </div>

            {{-- Actions --}}
            <div class="mb-4">
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-sm font-medium text-gray-700">
                        Action <span class="text-red-500">*</span>
                    </label>
                    <button type="button" id="toggleAllActionsBtn" onclick="toggleAllCheckboxes('action-cb', this)"
                        class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                        Chọn tất cả
                    </button>
                </div>
                <div class="grid grid-cols-2 gap-2 border border-gray-200 rounded-lg p-3 bg-gray-50">
                    @foreach(['create' => '🟢', 'read' => '🔵', 'update' => '🟡', 'delete' => '🔴'] as $act => $emoji)
                    <label class="flex items-center gap-2 cursor-pointer hover:bg-white rounded px-2 py-1.5 transition">
                        <input type="checkbox" name="actions[]" value="{{ $act }}"
                            data-action-name="{{ $act }}"
                            class="action-cb w-4 h-4 text-blue-600 border-gray-300 rounded"
                            onchange="handleSelectionChange('action', this)">
                        <span class="text-sm text-gray-700">{{ $emoji }} {{ ucfirst($act) }}</span>
                    </label>
                    @endforeach
                    <label class="flex items-center gap-2 cursor-pointer hover:bg-white rounded px-2 py-1 transition col-span-2 border-t border-dashed border-gray-200 mt-1 pt-2">
                        <input type="checkbox" id="customActionCb" class="action-cb w-4 h-4 text-purple-600 border-gray-300 rounded"
                            onchange="toggleCustomActionInput(); handleSelectionChange('action', this)">
                        <span class="text-sm text-gray-500 italic">✏️ Action khác:</span>
                        <input type="text" id="customActionText"
                            class="flex-1 text-sm border-b border-dashed border-gray-300 bg-transparent focus:outline-none focus:border-purple-400 px-1"
                            placeholder="tên_action..."
                            oninput="updatePreview()" disabled>
                    </label>
                </div>
            </div>

            {{-- Preview / Result --}}
            <div id="permissionPreview">
                <label for="name" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">
                    Permission Name <span class="text-red-500">*</span>
                </label>
                <div class="flex gap-2 items-center">
                    <input type="text" id="name" name="name" readonly
                        class="flex-1 px-4 py-2 bg-blue-50 border border-blue-100 rounded-lg text-blue-900 font-mono font-bold focus:outline-none"
                        placeholder="module.action">
                </div>
                <div id="previewTags" class="flex flex-wrap gap-1.5 mt-2"></div>
                <p id="nameError" class="text-red-500 text-sm mt-1 hidden"></p>
            </div>
        </div>

        {{-- ===== Description ===== --}}
        <div>
            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                Description
            </label>
            <textarea
                id="description"
                name="description"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"
                placeholder="Mô tả ngắn về permission này"
                rows="2"
            ></textarea>
        </div>

        {{-- ===== Status ===== --}}
        <div>
            <label class="flex items-center">
                <input
                    type="checkbox"
                    id="status"
                    name="status"
                    value="1"
                    checked
                    class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-2 focus:ring-blue-500"
                />
                <span class="ml-2 text-sm font-medium text-gray-700">Active Status</span>
            </label>
        </div>

        {{-- ===== Buttons ===== --}}
        <div class="flex gap-4 pt-2">
            <button
                type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition"
                id="submitBtn"
            >
                Save Permission
            </button>

            <button
                type="button"
                onclick="closeDialog('permissionDialog')"
                class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-lg font-medium transition"
            >
                Cancel
            </button>
        </div>
    </form>
</x-dialog>

<!-- DELETE PERMISSION DIALOG -->
<x-dialog 
    title="Delete Permission" 
    subtitle="Are you sure you want to delete this permission?"
    size="md"
    id="deletePermissionDialog"
>
    <div class="space-y-4">
        <p class="text-gray-700">This action cannot be undone.</p>
        <form id="deletePermissionForm" action="" method="POST" class="mt-4">
            @csrf
            @method('DELETE')
            <div class="flex gap-4 justify-end">
                <button 
                    type="button"
                    onclick="closeDialog('deletePermissionDialog')"
                    class="px-6 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-lg font-medium transition"
                >
                    Cancel
                </button>
                <button 
                    type="submit"
                    class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition"
                >
                    Delete
                </button>
            </div>
        </form>
    </div>
</x-dialog>
<script>
    let currentPermissionId = null;
    let isEditMode = false;

    // =============================================
    // Toggle All Checkboxes (module / action)
    // =============================================
    function toggleAllCheckboxes(cls, btn) {
        const checkboxes = document.querySelectorAll('.' + cls + ':not(#customModuleCb):not(#customActionCb)');
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        checkboxes.forEach(cb => cb.checked = !allChecked);
        btn.textContent = allChecked ? 'Chọn tất cả' : 'Bỏ chọn tất cả';
        updatePreview();
    }

    function toggleCustomModuleInput() {
        const cb  = document.getElementById('customModuleCb');
        const inp = document.getElementById('customModuleText');
        inp.disabled = !cb.checked;
        if (cb.checked) inp.focus();
        else inp.value = '';
    }

    function toggleCustomActionInput() {
        const cb  = document.getElementById('customActionCb');
        const inp = document.getElementById('customActionText');
        inp.disabled = !cb.checked;
        if (cb.checked) inp.focus();
        else inp.value = '';
    }

    // =============================================
    // Lấy danh sách modules / actions được chọn
    // =============================================
    function getSelectedValues(namedCheckboxes, customCbId, customInputId) {
        const values = Array.from(document.querySelectorAll(namedCheckboxes + ':checked'))
                            .map(cb => cb.value.trim().toLowerCase())
                            .filter(v => v);

        const customCb  = document.getElementById(customCbId);
        const customInp = document.getElementById(customInputId);
        if (customCb && customCb.checked && customInp && customInp.value.trim()) {
            values.push(customInp.value.trim().toLowerCase().replace(/\s+/g, '_'));
        }
        return values;
    }

    function getSelectedModules() {
        return getSelectedValues('input[name="modules[]"]', 'customModuleCb', 'customModuleText');
    }

    function getSelectedActions() {
        return getSelectedValues('input[name="actions[]"]', 'customActionCb', 'customActionText');
    }

    function handleSelectionChange(type, el) {
        if (isEditMode) {
            const cls = (type === 'module') ? '.module-cb' : '.action-cb';
            document.querySelectorAll(cls).forEach(cb => {
                if (cb !== el) cb.checked = false;
            });
        }
        updatePreview();
    }

    // =============================================
    // Preview — hiện tất cả tổ hợp sẽ tạo
    // =============================================
    function updatePreview() {
        const mods = getSelectedModules();
        const acts = getSelectedActions();
        const previewTags = document.getElementById('previewTags');
        const submitBtn   = document.getElementById('submitBtn');
        const nameInput   = document.getElementById('name');

        if (mods.length === 0 || acts.length === 0) {
            previewTags.innerHTML = '';
            submitBtn.textContent = isEditMode ? 'Cập nhật' : 'Tạo Permissions';
            nameInput.value = '';
            return;
        }

        const combinations = [];
        mods.forEach(m => acts.forEach(a => combinations.push(m + '.' + a)));

        if (isEditMode) {
            // Edit mode: single selection expected
            nameInput.value = combinations[0] || '';
            previewTags.innerHTML = '';
            submitBtn.textContent = 'Cập nhật Permission';
        } else {
            // Create mode: multi selection
            nameInput.value = combinations.join(','); // will be ignored by batch backend but useful for display
            previewTags.innerHTML = combinations.map(p =>
                `<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700 border border-blue-200">${p}</span>`
            ).join('');
            const total = combinations.length;
            submitBtn.textContent = `Tạo ${total} Permission${total > 1 ? 's' : ''}`;
        }
    }

    // =============================================
    // Reset về trạng thái CREATE
    // =============================================
    function resetToCreateMode() {
        isEditMode = false;
        
        // Show all buttons
        document.getElementById('toggleAllModulesBtn').classList.remove('hidden');
        document.getElementById('toggleAllActionsBtn').classList.remove('hidden');

        // Uncheck all
        document.querySelectorAll('.module-cb, .action-cb').forEach(cb => cb.checked = false);
        document.getElementById('customModuleText').value   = '';
        document.getElementById('customModuleText').disabled = true;
        document.getElementById('customActionText').value   = '';
        document.getElementById('customActionText').disabled = true;

        document.getElementById('description').value = '';
        document.getElementById('status').checked    = true;

        updatePreview();
        clearPermissionErrors();
    }

    // =============================================
    // Open Dialog — CREATE / EDIT
    // =============================================
    function openPermissionDialog(permissionId = null) {
        const dialog      = document.getElementById('permissionDialog');
        const dialogHeader = dialog.querySelector('.flex.items-center.justify-between');
        const dialogTitle  = dialogHeader.querySelector('h2');
        let   dialogSubtitle = dialogHeader.querySelector('p');
        const form         = document.getElementById('permissionForm');
        const submitBtn    = form.querySelector('button[type="submit"]');
        const methodInput  = document.getElementById('methodInput');

        currentPermissionId = permissionId;

        if (permissionId) {
            // ---------- EDIT MODE ----------
            isEditMode = true;

            const row = document.querySelector(`[data-row-id="${permissionId}"]`);
            if (row) {
                const data = JSON.parse(row.getAttribute('data-row-data'));

                dialogTitle.textContent = 'Sửa Permission';
                if (!dialogSubtitle) {
                    dialogSubtitle = document.createElement('p');
                    dialogSubtitle.className = 'text-sm text-gray-600 mt-1';
                    dialogHeader.querySelector('div').appendChild(dialogSubtitle);
                }
                dialogSubtitle.textContent = 'Cập nhật thông tin permission';
                submitBtn.textContent = 'Cập nhật';

                form.action = '{{ route("admin.permissions.permissions.update", ":id") }}'.replace(':id', permissionId);
                methodInput.value = 'PUT';

                document.getElementById('name').value        = data.name || '';
                document.getElementById('description').value = data.description || '';
                document.getElementById('status').checked    = data.status ? true : false;

                // --- Auto-fill checkboxes ---
                resetToCreateMode(); // Clear everything first
                isEditMode = true;   // Re-set it
                
                // Hide "Select All" buttons in edit mode
                document.getElementById('toggleAllModulesBtn').classList.add('hidden');
                document.getElementById('toggleAllActionsBtn').classList.add('hidden');

                const nameParts = (data.name || '').split('.');
                if (nameParts.length === 2) {
                    const modName = nameParts[0];
                    const actName = nameParts[1];

                    const modCb = document.querySelector(`.module-cb[data-module-name="${modName}"]`);
                    if (modCb) modCb.checked = true;
                    else {
                        document.getElementById('customModuleCb').checked = true;
                        document.getElementById('customModuleText').disabled = false;
                        document.getElementById('customModuleText').value = modName;
                    }

                    const actCb = document.querySelector(`.action-cb[data-action-name="${actName}"]`);
                    if (actCb) actCb.checked = true;
                    else {
                        document.getElementById('customActionCb').checked = true;
                        document.getElementById('customActionText').disabled = false;
                        document.getElementById('customActionText').value = actName;
                    }
                }
                updatePreview();
                clearPermissionErrors();
            }
        } else {
            // ---------- CREATE MODE ----------
            dialogTitle.textContent = 'Tạo Permissions mới';
            if (!dialogSubtitle) {
                dialogSubtitle = document.createElement('p');
                dialogSubtitle.className = 'text-sm text-gray-600 mt-1';
                dialogHeader.querySelector('div').appendChild(dialogSubtitle);
            }
            dialogSubtitle.textContent = 'Chọn modules × actions để tạo hàng loạt';

            form.action   = '{{ route("admin.permissions.permissions.store") }}';
            methodInput.value = 'POST';
            resetToCreateMode();
        }

        openDialog('permissionDialog');
    }

    function openDeletePermissionDialog(permissionId) {
        document.getElementById('deletePermissionForm').action =
            '{{ route("admin.permissions.permissions.destroy", ":id") }}'.replace(':id', permissionId);
        openDialog('deletePermissionDialog');
    }

    // =============================================
    // Validation errors
    // =============================================
    function clearPermissionErrors() {
        const el = document.getElementById('nameError');
        if (el) { el.textContent = ''; el.classList.add('hidden'); }
    }

    function displayPermissionErrors(errors) {
        clearPermissionErrors();
        if (errors.name && errors.name[0]) {
            const el = document.getElementById('nameError');
            if (el) { el.textContent = errors.name[0]; el.classList.remove('hidden'); }
        }
        const firstErr = Object.values(errors)[0];
        if (firstErr && typeof showToast === 'function') {
            showToast(Array.isArray(firstErr) ? firstErr[0] : firstErr, 'error');
        }
    }

    // =============================================
    // Form submit
    // =============================================
    document.addEventListener('DOMContentLoaded', function () {
        const form       = document.getElementById('permissionForm');
        const deleteForm = document.getElementById('deletePermissionForm');

        // ----- Permission Form -----
        if (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();

                const submitBtn    = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.textContent;
                clearPermissionErrors();

                let formData = new FormData(this);

                if (isEditMode) {
                    // ---- EDIT: validate name ----
                    const name = (document.getElementById('name').value || '').trim();
                    if (!name) {
                        if (typeof showToast === 'function') showToast('Permission name là bắt buộc', 'error');
                        return;
                    }
                    if (!name.includes('.')) {
                        if (typeof showToast === 'function') showToast('Tên phải theo dạng module.action (VD: users.create)', 'error');
                        return;
                    }
                    // Tự động thêm modules[] từ tên
                    formData.delete('modules[]');
                    formData.append('modules[]', name.split('.')[0]);

                } else {
                    // ---- CREATE: validate multi ----
                    const mods = getSelectedModules();
                    const acts = getSelectedActions();

                    if (mods.length === 0) {
                        if (typeof showToast === 'function') showToast('Vui lòng chọn ít nhất 1 Module', 'error');
                        return;
                    }
                    if (acts.length === 0) {
                        if (typeof showToast === 'function') showToast('Vui lòng chọn ít nhất 1 Action', 'error');
                        return;
                    }

                    // Rebuild formData với đúng modules[] và actions[]
                    formData.delete('modules[]');
                    formData.delete('actions[]');
                    mods.forEach(m => formData.append('modules[]', m));
                    acts.forEach(a => formData.append('actions[]', a));
                }

                submitBtn.disabled    = true;
                submitBtn.textContent = 'Đang lưu...';

                const url = this.getAttribute('action');
                const method = document.getElementById('methodInput').value;

                try {
                    const options = { reload: true, delay: 1000 };
                    if (method === 'PUT') {
                        await HttpService.put(url, formData, options);
                    } else {
                        await HttpService.post(url, formData, options);
                    }
                    closeDialog('permissionDialog');
                } catch (err) {
                    if (err.response?.status === 422 && err.response.data.errors) {
                        displayPermissionErrors(err.response.data.errors);
                    }
                    submitBtn.disabled    = false;
                    submitBtn.textContent = originalText;
                }
            });
        }

        if (deleteForm) {
            deleteForm.addEventListener('submit', async function (e) {
                e.preventDefault();
                const submitBtn    = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.textContent;
                
                submitBtn.disabled    = true;
                submitBtn.textContent = 'Deleting...';

                try {
                    await HttpService.delete(this.getAttribute('action'), { reload: true, delay: 1000 });
                    closeDialog('deletePermissionDialog');
                } catch (error) {
                    submitBtn.disabled    = false;
                    submitBtn.textContent = originalText;
                }
            });
        }
    });
</script>

@endsection

