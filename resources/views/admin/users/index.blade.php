@extends('admin.layouts.admin')

@section('title','Users Management')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">Users Management</h1>
        <a
            href="{{ route('admin.users.create') }}"
            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium"
        >
            Add User
        </a>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6">
            @php
                $columns = [
                    ['label' => '#', 'key' => '__index', 'class' => 'w-12', 'td_class' => 'font-medium'],
                    ['label' => 'Name', 'key' => 'name'],
                    ['label' => 'Email', 'key' => 'email'],
                    ['label' => 'Phone', 'key' => 'phone'],
                    ['label' => 'Role', 'key' => 'role', 'type' => 'role-display'],
                    ['label' => 'Status', 'key' => 'status', 'type' => 'status-boolean'],
                    ['label' => 'Created', 'key' => 'created_at'],
                ];

                $actions = [
                    [
                        'label' => 'View',
                        'icon' => 'fa-eye',
                        'url' => 'admin.users.view',
                        'class' => 'text-green-600 hover:text-green-800',
                    ],
                    [
                        'label' => 'Edit',
                        'icon' => 'fa-edit',
                        'url' => 'admin.users.edit',
                        'class' => 'text-blue-600 hover:text-blue-800',
                    ],
                    [
                        'label' => 'Delete',
                        'icon' => 'fa-trash',
                        'onclick' => 'openDeleteUserDialog({id})',
                        'class' => 'text-red-600 hover:text-red-800',
                        
                    ],
                ];
            @endphp

            <x-common-table 
                :columns="$columns" 
                :rows="$rows" 
                :showPagination="true" 
                :actions="$actions" 
            />
        </div>
    </div>
</div>

<!-- DELETE USER CONFIRM DIALOG -->
<x-dialog 
    title="Delete User" 
    subtitle="Are you sure you want to delete this user?"
    size="md"
    id="deleteUserDialog"
>
    <div class="space-y-4">
        <div class="flex gap-4 justify-end">
            <button 
                type="button"
                onclick="closeDialog('deleteUserDialog')"
                class="px-6 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-lg font-medium transition"
            >
                Cancel
            </button>

            <button 
                id="confirmDeleteBtn"
                class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition"
            >
                Delete
            </button>
        </div>
    </div>
</x-dialog>

{{-- Route template --}}
<input type="hidden" 
       id="delete-route-template"
       value="{{ route('admin.users.destroy', ':id') }}">

@endsection

@push('scripts')
<script>
    let deleteUserId = null;

    function openDeleteUserDialog(userId) {
        deleteUserId = userId;
        openDialog('deleteUserDialog');
    }

    document.addEventListener('DOMContentLoaded', function () {

        const confirmBtn = document.getElementById('confirmDeleteBtn');

        confirmBtn.addEventListener('click', function () {

            if (!deleteUserId) return;

            const template = document.getElementById('delete-route-template').value;
            const url = template.replace(':id', deleteUserId);

            HttpService.delete(url);

            closeDialog('deleteUserDialog');
        });

    });
</script>
@endpush
