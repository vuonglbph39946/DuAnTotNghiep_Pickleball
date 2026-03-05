@extends('admin.layouts.admin')

@section('title','Roles Management')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">Roles Management</h1>
        <button 
            onclick="openRoleDialog()" 
            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium"
        >
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            Add Role
        </button>
    </div>

    <!-- Roles Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6">
            @php
                $columns = [
                    ['label' => '#', 'key' => '__index', 'class' => 'w-12', 'td_class' => 'font-medium'],
                    ['label' => 'Name', 'key' => 'name'],
                    ['label' => 'Slug', 'key' => 'slug'],
                    ['label' => 'Description', 'key' => 'description'],
                    ['label' => 'Status', 'key' => 'status', 'type' => 'status-boolean'],
                    ['label' => 'Created', 'key' => 'created_at'],
                ];

                $actions = [
                    [
                        'label' => 'Edit',
                        'icon' => 'fa-edit',
                        'onclick' => 'openRoleDialog({id})',
                        'class' => 'text-blue-600 hover:text-blue-800',
                    ],
                    [
                        'label' => 'Delete',
                        'icon' => 'fa-trash',
                        'onclick' => 'openDeleteRoleDialog({id})',
                        'class' => 'text-red-600 hover:text-red-800',
                    ],
                ];
            @endphp

            <x-common-table :columns="$columns" :rows="$roles" :showPagination="true" :actions="$actions" />
        </div>
    </div>
</div>

<!-- CREATE/EDIT ROLE DIALOG -->
<x-dialog
    title="Role"
    subtitle=""
    size="md"
    id="roleDialog"
>
    <form
        action=""
        method="POST"
        id="roleForm"
        class="space-y-6"
    >
        @csrf
        <input type="hidden" name="_method" value="POST" id="methodInput">
        <div class="space-y-4">
            <!-- Role Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Role Name
                    <span class="text-red-500">*</span>
                </label>   
                <input
                    type="text"
                    id="name"
                    name="name"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"
                    placeholder="Enter role name"
                    required
                />
                <p id="nameError" class="text-red-500 text-sm mt-1 hidden"></p>
            </div>

            <!-- Slug -->
            <div>
                <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                <input
                    type="text"
                    id="slug"
                    name="slug"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"
                    placeholder="Auto-generated from name if empty"
                />
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea
                    id="description"
                    name="description"
                    rows="4"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"
                    placeholder="Enter role description"
                ></textarea>
            </div>

            <!-- Status -->
            <div class="flex items-center pt-2">
                <input
                    type="checkbox"
                    id="status"
                    name="status"
                    value="1"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                    checked
                />
                <label for="status" class="ml-2 block text-sm text-gray-900">Active</label>
            </div>
        </div>

        <!-- Buttons -->
        <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
            <button
                type="button"
                onclick="closeDialog('roleDialog')"
                class="px-6 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-lg font-medium transition"
            >
                Cancel
            </button>
            <button
                type="submit"
                class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition"
            >
                Save Role
            </button>
        </div>
    </form>
</x-dialog>

<!-- DELETE ROLE DIALOG -->
<x-dialog 
    title="Delete Role" 
    subtitle="Are you sure you want to delete this role?"
    size="md"
    id="deleteRoleDialog"
>
    <div class="space-y-4">
        <p class="text-gray-700">This action cannot be undone.</p>
        <form id="deleteRoleForm" action="" method="POST" class="mt-4">
            @csrf
            @method('DELETE')
            <div class="flex gap-4 justify-end">
                <button 
                    type="button"
                    onclick="closeDialog('deleteRoleDialog')"
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
    const RoleManager = {
        formId: 'roleForm',
        dialogId: 'roleDialog',
        deleteFormId: 'deleteRoleForm',
        deleteDialogId: 'deleteRoleDialog',
        currentRoleId: null,

        init() {
            this.form = document.getElementById(this.formId);
            this.deleteForm = document.getElementById(this.deleteFormId);
            
            if (this.form) this.form.addEventListener('submit', (e) => this.handleSave(e));
            if (this.deleteForm) this.deleteForm.addEventListener('submit', (e) => this.handleDelete(e));
        },

        clearErrors() {
            const nameError = document.getElementById('nameError');
            if (nameError) {
                nameError.textContent = '';
                nameError.classList.add('hidden');
            }
        },

        displayErrors(errors) {
            this.clearErrors();
            if (errors.name && errors.name[0]) {
                const nameError = document.getElementById('nameError');
                if (nameError) {
                    nameError.textContent = errors.name[0];
                    nameError.classList.remove('hidden');
                }
            }
        },

        setDialogMode(isEdit, roleId) {
            const dialog = document.getElementById(this.dialogId);
            const dialogHeader = dialog.querySelector('.flex.items-center.justify-between');
            const title = dialogHeader.querySelector('h2');
            let subtitle = dialogHeader.querySelector('p');
            const submitBtn = this.form.querySelector('button[type="submit"]');
            const methodInput = document.getElementById('methodInput');

            if (!subtitle) {
                subtitle = document.createElement('p');
                subtitle.className = 'text-sm text-gray-600 mt-1';
                dialogHeader.querySelector('div').appendChild(subtitle);
            }

            if (isEdit) {
                title.textContent = 'Edit Role';
                subtitle.textContent = 'Update role information';
                submitBtn.textContent = 'Update Role';
                this.form.action = '{{ route("admin.permissions.roles.update", ":id") }}'.replace(':id', roleId);
                methodInput.value = 'PUT';
            } else {
                title.textContent = 'Create New Role';
                subtitle.textContent = 'Add a new role to your system';
                submitBtn.textContent = 'Create Role';
                this.form.action = '{{ route("admin.permissions.roles.store") }}';
                methodInput.value = 'POST';
            }
        },

        loadRoleData(roleId) {
            const row = document.querySelector(`[data-row-id="${roleId}"]`);
            if (!row) return null;
            
            return JSON.parse(row.getAttribute('data-row-data'));
        },

        populateForm(roleData) {
            if (!roleData) return;
            
            this.form.querySelector('input[name="name"]').value = roleData.name || '';
            this.form.querySelector('input[name="slug"]').value = roleData.slug || '';
            this.form.querySelector('textarea[name="description"]').value = roleData.description || '';
            this.form.querySelector('input[name="status"]').checked = roleData.status || false;
        },

        resetForm() {
            this.form.reset();
            this.form.querySelector('input[name="status"]').checked = true;
        },

        openDialog(roleId = null) {
            this.clearErrors();
            this.currentRoleId = roleId;

            if (roleId) {
                this.setDialogMode(true, roleId);
                const roleData = this.loadRoleData(roleId);
                this.populateForm(roleData);
            } else {
                this.setDialogMode(false);
                this.resetForm();
            }

            openDialog(this.dialogId);
        },

        openDeleteDialog(roleId) {
            document.getElementById(this.deleteFormId).action = 
                '{{ route("admin.permissions.roles.destroy", ":id") }}'.replace(':id', roleId);
            openDialog(this.deleteDialogId);
        },

        handleSave(e) {
            e.preventDefault();
            
            const submitBtn = this.form.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            
            submitBtn.disabled = true;
            submitBtn.textContent = 'Saving...';

            const formData = new FormData(this.form);
            // Ensure status is always sent (0 or 1)
            const statusCheckbox = this.form.querySelector('input[name="status"]');
            formData.set('status', statusCheckbox.checked ? 1 : 0);

            fetch(this.form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => {
                        throw { status: response.status, data };
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    closeDialog(this.dialogId);
                    HttpService.toast(data.message || 'Saved successfully', 'success');
                    setTimeout(() => window.location.reload(), 800);
                } else {
                    if (data.errors) {
                        this.displayErrors(data.errors);
                    }
                    HttpService.toast(data.message || 'Something went wrong', 'error');
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                }
            })
            .catch(error => {
                if (error.status === 422 && error.data?.errors) {
                    this.displayErrors(error.data.errors);
                    HttpService.toast(error.data.message || 'Validation failed', 'error');
                } else {
                    HttpService.toast('Error: Something went wrong', 'error');
                }
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            });
        },

        handleDelete(e) {
            e.preventDefault();

            const submitBtn = this.deleteForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;

            submitBtn.disabled = true;
            submitBtn.textContent = 'Deleting...';

            HttpService.delete(this.deleteForm.action, { reload: false })
                .then(data => {
                    if (data.success) {
                        closeDialog(this.deleteDialogId);
                        setTimeout(() => window.location.reload(), 800);
                    } else {
                        HttpService.toast(data.message || 'Something went wrong', 'error');
                        submitBtn.disabled = false;
                        submitBtn.textContent = originalText;
                    }
                })
                .catch(() => {
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                });
        }
    };

    // Expose to global scope for onclick handlers
    function openRoleDialog(roleId) {
        RoleManager.openDialog(roleId);
    }

    function openDeleteRoleDialog(roleId) {
        RoleManager.openDeleteDialog(roleId);
    }

    document.addEventListener('DOMContentLoaded', () => RoleManager.init());
</script>

@endsection


