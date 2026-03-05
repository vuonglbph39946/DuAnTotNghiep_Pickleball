@extends('admin.layouts.admin')

@section('title','News Management')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">News Management</h1>
        <a 
            href="{{ route('admin.news.create') }}"
            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium"
        >
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            Add News
        </a>
    </div>

    <!-- News Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6">
            @php
                $columns = [
                    ['label' => '#', 'key' => '__index', 'class' => 'w-12', 'td_class' => 'font-medium'],
                    ['label' => 'Title', 'key' => 'title'],
                    ['label' => 'Category', 'key' => 'category.name'],
                    ['label' => 'Status', 'key' => 'status', 'type' => 'status-select'],
                    ['label' => 'Published At', 'key' => 'published_at'],
                    ['label' => 'Views', 'key' => 'views'],
                ];

                $actions = [
                    [
                        'label' => 'View',
                        'icon' => 'fa-eye',
                        'url' => 'admin.news.show',
                        'class' => 'text-green-600 hover:text-green-800',
                    ],
                    [
                        'label' => 'Edit',
                        'icon' => 'fa-edit',
                        'url' => 'admin.news.edit',
                        'class' => 'text-blue-600 hover:text-blue-800',
                    ],
                    [
                        'label' => 'Delete',
                        'icon' => 'fa-trash',
                        'onclick' => 'openDeleteNewsDialog({id})',
                        'class' => 'text-red-600 hover:text-red-800',
                    ],
                ];
            @endphp

            <x-common-table :columns="$columns" :rows="$rows" :showPagination="true" :actions="$actions" />
        </div>
    </div>
</div>

<!-- UPDATE STATUS CONFIRM DIALOG -->
<x-dialog 
    title="Update Status" 
    subtitle="Are you sure you want to change the status?"
    size="md"
    id="updateStatusDialog"
>
    <div class="space-y-4">
        <p class="text-gray-700">
            Change status from <span id="currentStatusText" class="font-semibold"></span> to <span id="newStatusText" class="font-semibold"></span>?
        </p>
        <form id="updateStatusForm" action="" method="POST" class="mt-4">
            @csrf
            @method('PATCH')
            <input type="hidden" name="status" id="newStatusValue" value="">
            <div class="flex gap-4 justify-end">
                <button 
                    type="button"
                    onclick="closeUpdateStatusDialog()"
                    class="px-6 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-lg font-medium transition"
                >
                    Cancel
                </button>
                <button 
                    type="submit"
                    class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition"
                >
                    Update
                </button>
            </div>
        </form>
    </div>
</x-dialog>

<!-- DELETE NEWS CONFIRM DIALOG -->
<x-dialog 
    title="Delete News" 
    subtitle="Are you sure you want to delete this news item?"
    size="md"
    id="deleteNewsDialog"
>
    <div class="space-y-4">
        <p class="text-gray-700">This action cannot be undone.</p>
        <form id="deleteNewsForm" action="" method="POST" class="mt-4">
            @csrf
            @method('DELETE')
            <div class="flex gap-4 justify-end">
                <button 
                    type="button"
                    onclick="closeDialog('deleteNewsDialog')"
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
    let pendingStatusUpdate = {
        newsId: null,
        newStatus: null,
        oldStatus: null,
        selectElement: null
    };

    const statusTexts = {
        0: 'Draft',
        1: 'Published',
        2: 'Archived'
    };

    function confirmUpdateStatus(newsId, newStatus, oldStatus, selectElement) {
        if (newStatus == oldStatus) {
            return; // Không thay đổi gì
        }

        pendingStatusUpdate = {
            newsId: newsId,
            newStatus: parseInt(newStatus),
            oldStatus: parseInt(oldStatus),
            selectElement: selectElement
        };

        // Set form action và values
        document.getElementById('updateStatusForm').action = '{{ route("admin.news.updateStatus", ":id") }}'.replace(':id', newsId);
        document.getElementById('newStatusValue').value = newStatus;
        document.getElementById('currentStatusText').textContent = statusTexts[oldStatus];
        document.getElementById('newStatusText').textContent = statusTexts[newStatus];

        openDialog('updateStatusDialog');
    }

    function closeUpdateStatusDialog() {
        // Reset select về giá trị cũ
        if (pendingStatusUpdate.selectElement) {
            pendingStatusUpdate.selectElement.value = pendingStatusUpdate.oldStatus;
        }
        closeDialog('updateStatusDialog');
        pendingStatusUpdate = {
            newsId: null,
            newStatus: null,
            oldStatus: null,
            selectElement: null
        };
    }

    function openDeleteNewsDialog(newsId) {
        document.getElementById('deleteNewsForm').action = '{{ route("admin.news.destroy", ":id") }}'.replace(':id', newsId);
        openDialog('deleteNewsDialog');
    }

    document.addEventListener('DOMContentLoaded', function() {
        const updateStatusForm = document.getElementById('updateStatusForm');
        const deleteForm = document.getElementById('deleteNewsForm');

        if (updateStatusForm) {
            updateStatusForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.textContent;

                submitBtn.disabled = true;
                submitBtn.textContent = 'Updating...';

                fetch(this.action, {
                    method: 'POST',
                    body: new FormData(this),
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        closeDialog('updateStatusDialog');
                        // Cập nhật màu sắc của select
                        const select = pendingStatusUpdate.selectElement;
                        const statuses = {
                            0: 'bg-gray-100 text-gray-800',
                            1: 'bg-green-100 text-green-800',
                            2: 'bg-yellow-100 text-yellow-800'
                        };
                        select.className = 'status-select px-2 py-1 rounded text-xs font-medium border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-400 cursor-pointer ' + statuses[data.news.status];
                        select.setAttribute('data-current-status', data.news.status);
                        // Hiển thị toast thành công
                        if (typeof showToast === 'function') {
                            showToast(data.message || 'Status updated successfully!', 'success');
                        }
                        // Reset pending update
                        pendingStatusUpdate = {
                            newsId: null,
                            newStatus: null,
                            oldStatus: null,
                            selectElement: null
                        };
                    } else {
                        if (typeof showToast === 'function') {
                            showToast(data.message || 'Something went wrong', 'error');
                        } else {
                            alert('Error: ' + (data.message || 'Something went wrong'));
                        }
                        // Reset select về giá trị cũ
                        if (pendingStatusUpdate.selectElement) {
                            pendingStatusUpdate.selectElement.value = pendingStatusUpdate.oldStatus;
                        }
                        submitBtn.disabled = false;
                        submitBtn.textContent = originalText;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    if (typeof showToast === 'function') {
                        showToast('Error: Something went wrong', 'error');
                    } else {
                        alert('Error: Something went wrong');
                    }
                    // Reset select về giá trị cũ
                    if (pendingStatusUpdate.selectElement) {
                        pendingStatusUpdate.selectElement.value = pendingStatusUpdate.oldStatus;
                    }
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                });
            });
        }

        if (deleteForm) {
            deleteForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.textContent;

                submitBtn.disabled = true;
                submitBtn.textContent = 'Deleting...';

                fetch(this.action, {
                    method: 'POST',
                    body: new FormData(this),
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        closeDialog('deleteNewsDialog');
                        // Hiển thị toast thành công
                        if (typeof showToast === 'function') {
                            showToast(data.message || 'News deleted successfully!', 'success');
                        }
                        // Reload sau 1 giây để người dùng thấy toast
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        if (typeof showToast === 'function') {
                            showToast(data.message || 'Something went wrong', 'error');
                        } else {
                            alert('Error: ' + (data.message || 'Something went wrong'));
                        }
                        submitBtn.disabled = false;
                        submitBtn.textContent = originalText;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    if (typeof showToast === 'function') {
                        showToast('Error: Something went wrong', 'error');
                    } else {
                        alert('Error: Something went wrong');
                    }
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                });
            });
        }
    });
</script>

@endsection

