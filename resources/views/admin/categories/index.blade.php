@extends('admin.layouts.admin')

@section('title','Categories Management')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">Categories Management</h1>
        <button 
            onclick="openCategoryDialog()" 
            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium"
        >
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            Add Category
        </button>
    </div>

    <!-- Categories Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6">
            @php
                $columns = [
                    ['label' => '#', 'key' => '__index', 'class' => 'w-12', 'td_class' => 'font-medium'],
                    ['label' => 'Name', 'key' => 'name', 'type' => 'tree-name'],
                    ['label' => 'Slug', 'key' => 'slug'],
                    ['label' => 'Status', 'key' => 'status', 'type' => 'status-boolean'],
                    ['label' => 'Created', 'key' => 'created_at'],
                ];

                $actions = [
                    [
                        'label' => 'View',
                        'icon' => 'fa-eye',
                        'onclick' => 'openCategoryViewDialog({id})',
                        'class' => 'text-green-600 hover:text-green-800',
                    ],
                    [
                        'label' => 'Edit',
                        'icon' => 'fa-edit',
                        'onclick' => 'openCategoryDialog({id})',
                        'class' => 'text-blue-600 hover:text-blue-800',
                    ],
                    [
                        'label' => 'Delete',
                        'icon' => 'fa-trash',
                        'onclick' => 'openDeleteCategoryDialog({id})',
                        'class' => 'text-red-600 hover:text-red-800',
                    ],
                ];
            @endphp

            <x-common-table :columns="$columns" :rows="$rows" :showPagination="true" :actions="$actions" />
        </div>
    </div>
</div>

<!-- ================================================
     CREATE/EDIT CATEGORY DIALOG WITH FORM (CHUNG 1 FORM)
     ================================================ -->
<x-dialog 
    title="Category" 
    subtitle=""
    size="md"
    id="categoryDialog"
>
    <x-form
        action=""
        method="POST"
        id="categoryForm"
        :fields="[
            [
                'name' => 'name',
                'label' => 'Category Name',
                'type' => 'text',
                'placeholder' => 'Enter category name',
                'required' => true,
            ],
            [
                'name' => 'slug',
                'label' => 'Slug',
                'type' => 'text',
                'placeholder' => 'Auto-generate if empty',
            ],
            [
                'name' => 'parent_id',
                'label' => 'Parent Category',
                'type' => 'select',
                'options' => collect(['' => 'None (Root Category)'])->merge($parentCategories->mapWithKeys(function($cat) {
                    return [$cat->name => $cat->id];
                }))->toArray(),
            ],
            [
                'name' => 'status',
                'label' => 'Status',
                'type' => 'select',
                'options' => [
                    'Active' => 1,
                    'Inactive' => 0,
                ],
                'value' => 1,
                'required' => true,
            ],
        ]"
        submitButtonText="Save Category"
        submitButtonClass="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition"
        :showCancelButton="true"
        cancelButtonUrl="javascript:closeDialog('categoryDialog')"
    />
</x-dialog>

<!-- ================================================
     VIEW CATEGORY DIALOG (READ-ONLY)
     ================================================ -->
<x-dialog 
    title="View Category" 
    subtitle=""
    size="md"
    id="categoryViewDialog"
>
    <div id="categoryViewContent" class="space-y-6">
        <!-- Content will be loaded via JavaScript -->
    </div>
    <div class="mt-6 flex justify-end">
        <button 
            onclick="closeDialog('categoryViewDialog')"
            class="px-6 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-lg font-medium transition"
        >
            Close
        </button>
    </div>
</x-dialog>

<!-- DELETE CATEGORY CONFIRM DIALOG -->
<x-dialog 
    title="Delete Category" 
    subtitle="Are you sure you want to delete this category?"
    size="md"
    id="deleteCategoryDialog"
>
    <div class="space-y-4">
        <p class="text-gray-700">This action cannot be undone.</p>
        <form id="deleteCategoryForm" action="" method="POST" class="mt-4">
            @csrf
            @method('DELETE')
            <div class="flex gap-4 justify-end">
                <button 
                    type="button"
                    onclick="closeDialog('deleteCategoryDialog')"
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
    let currentCategoryId = null;
    const parentCategories = @json($parentCategories->pluck('name', 'id')->toArray());

    function openCategoryViewDialog(categoryId) {
        const dialog = document.getElementById('categoryViewDialog');
        const dialogHeader = dialog.querySelector('.flex.items-center.justify-between');
        const dialogTitle = dialogHeader.querySelector('h2');
        const contentDiv = document.getElementById('categoryViewContent');

        // Show loading
        contentDiv.innerHTML = '<div class="text-center py-4"><div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div></div>';

        // Fetch category data
        fetch('{{ route("admin.categories.show", ":id") }}'.replace(':id', categoryId), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success || !data.category) {
                if (typeof showToast === 'function') {
                    showToast('Category not found', 'error');
                }
                return;
            }

            const category = data.category;
            dialogTitle.textContent = 'View Category: ' + category.name;

            // Build view content with disabled fields
            // Get parent name - check if parent object exists or if we need to find it from parentCategories
            let parentName = 'None (Root Category)';
            if (category.parent && category.parent.name) {
                parentName = category.parent.name;
            } else if (category.parent_id) {
                // If parent object not loaded, try to find name from parentCategories
                const parentCat = parentCategories[category.parent_id];
                if (parentCat) {
                    parentName = parentCat;
                } else {
                    parentName = 'Category ID: ' + category.parent_id;
                }
            }
            const statusText = category.status ? 'Active' : 'Inactive';
            const createdDate = category.created_at ? new Date(category.created_at).toLocaleString('vi-VN') : 'N/A';
            const updatedDate = category.updated_at ? new Date(category.updated_at).toLocaleString('vi-VN') : 'N/A';

            contentDiv.innerHTML = `
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Category Name</label>
                        <input 
                            type="text" 
                            value="${(category.name || '').replace(/"/g, '&quot;')}" 
                            disabled
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 cursor-not-allowed"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Slug</label>
                        <input 
                            type="text" 
                            value="${(category.slug || '').replace(/"/g, '&quot;')}" 
                            disabled
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 cursor-not-allowed"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Parent Category</label>
                        <input 
                            type="text" 
                            value="${parentName.replace(/"/g, '&quot;')}" 
                            disabled
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 cursor-not-allowed"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <input 
                            type="text" 
                            value="${statusText}" 
                            disabled
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 cursor-not-allowed"
                        />
                    </div>
                    <div class="pt-4 border-t border-gray-200">
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-500">Created:</span>
                                <span class="ml-2 text-gray-900">${createdDate}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Updated:</span>
                                <span class="ml-2 text-gray-900">${updatedDate}</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            openDialog('categoryViewDialog');
        })
        .catch(error => {
            console.error('Error:', error);
            contentDiv.innerHTML = '<div class="text-red-600 py-4">Error loading category data</div>';
            if (typeof showToast === 'function') {
                showToast('Error loading category', 'error');
            }
        });
    }

    function openCategoryDialog(categoryId = null) {
        const dialog = document.getElementById('categoryDialog');
        const dialogHeader = dialog.querySelector('.flex.items-center.justify-between');
        const dialogTitle = dialogHeader.querySelector('h2');
        let dialogSubtitle = dialogHeader.querySelector('p');
        const form = document.getElementById('categoryForm');
        const submitBtn = form.querySelector('button[type="submit"]');
        const methodInput = form.querySelector('input[name="_method"]');

        currentCategoryId = categoryId;

        if (categoryId) {
            // Edit mode - Fetch category data via AJAX
            fetch('{{ route("admin.categories.show", ":id") }}'.replace(':id', categoryId), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (!data.category) {
                    if (typeof showToast === 'function') {
                        showToast('Category not found', 'error');
                    }
                    return;
                }

                const category = data.category;

                // Update dialog title
                dialogTitle.textContent = 'Edit Category';
                if (!dialogSubtitle) {
                    dialogSubtitle = document.createElement('p');
                    dialogSubtitle.className = 'text-sm text-gray-600 mt-1';
                    dialogHeader.querySelector('div').appendChild(dialogSubtitle);
                }
                dialogSubtitle.textContent = 'Update category information';
                submitBtn.textContent = 'Update Category';

                // Set form action và method
                form.action = '{{ route("admin.categories.update", ":id") }}'.replace(':id', categoryId);
                if (!methodInput) {
                    const methodField = document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    methodField.value = 'PUT';
                    form.appendChild(methodField);
                } else {
                    methodInput.value = 'PUT';
                }

                // Populate form fields
                form.querySelector('input[name="name"]').value = category.name || '';
                form.querySelector('input[name="slug"]').value = category.slug || '';
                form.querySelector('select[name="parent_id"]').value = category.parent_id || '';
                form.querySelector('select[name="status"]').value = category.status ? 1 : 0;

                openDialog('categoryDialog');
            })
            .catch(error => {
                console.error('Error:', error);
                if (typeof showToast === 'function') {
                    showToast('Error loading category', 'error');
                }
            });
        } else {
            // Create mode
            dialogTitle.textContent = 'Create New Category';
            if (!dialogSubtitle) {
                dialogSubtitle = document.createElement('p');
                dialogSubtitle.className = 'text-sm text-gray-600 mt-1';
                dialogHeader.querySelector('div').appendChild(dialogSubtitle);
            }
            dialogSubtitle.textContent = 'Add a new category to your system';
            submitBtn.textContent = 'Create Category';

            // Set form action và method
            form.action = '{{ route("admin.categories.store") }}';
            if (methodInput) {
                methodInput.remove();
            }

            // Clear form fields
            form.reset();
            form.querySelector('select[name="status"]').value = 1;

            openDialog('categoryDialog');
        }
    }

    function openDeleteCategoryDialog(categoryId) {
        document.getElementById('deleteCategoryForm').action = '{{ route("admin.categories.destroy", ":id") }}'.replace(':id', categoryId);
        openDialog('deleteCategoryDialog');
    }

    // Handle form submission với AJAX
    document.addEventListener('DOMContentLoaded', function() {
        const categoryForm = document.getElementById('categoryForm');
        const deleteForm = document.getElementById('deleteCategoryForm');

        if (categoryForm) {
            categoryForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.textContent;
                
                // Disable button và show loading
                submitBtn.disabled = true;
                submitBtn.textContent = 'Saving...';

                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        closeDialog('categoryDialog');
                        if (typeof showToast === 'function') {
                            showToast(data.message || 'Category saved successfully!', 'success');
                        }
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
                        closeDialog('deleteCategoryDialog');
                        if (typeof showToast === 'function') {
                            showToast(data.message || 'Category deleted successfully!', 'success');
                        }
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

    // Tree expand/collapse functionality
    const allCategories = @json($flattenedCategories ?? []);
    const categoryTree = {};
    
    // Build tree map - group by parent_id
    allCategories.forEach(cat => {
        const parentId = cat.parent_id || 'root';
        if (!categoryTree[parentId]) {
            categoryTree[parentId] = [];
        }
        categoryTree[parentId].push(cat);
    });

    function toggleCategoryTree(categoryId) {
        const button = document.querySelector(`[data-category-id="${categoryId}"]`);
        if (!button) return;
        
        const isExpanded = button.getAttribute('data-expanded') === 'true';
        const children = categoryTree[categoryId] || [];
        
        if (children.length === 0) return;
        
        // Toggle icon rotation
        const icon = button.querySelector('svg');
        if (isExpanded) {
            button.setAttribute('data-expanded', 'false');
            icon.style.transform = 'rotate(0deg)';
            // Hide children rows
            hideCategoryChildren(categoryId);
        } else {
            button.setAttribute('data-expanded', 'true');
            icon.style.transform = 'rotate(90deg)';
            // Show children rows
            showCategoryChildren(categoryId);
        }
    }

    function hideCategoryChildren(parentId) {
        const rows = document.querySelectorAll('[data-table-row]');
        rows.forEach(row => {
            const rowData = JSON.parse(row.getAttribute('data-row-data') || '{}');
            if (rowData.parent_id == parentId) {
                row.style.display = 'none';
                // Reset button state
                const childButton = document.querySelector(`[data-category-id="${rowData.id}"]`);
                if (childButton) {
                    childButton.setAttribute('data-expanded', 'false');
                    const childIcon = childButton.querySelector('svg');
                    if (childIcon) {
                        childIcon.style.transform = 'rotate(0deg)';
                    }
                }
                // Recursively hide grandchildren
                if (rowData.has_children) {
                    hideCategoryChildren(rowData.id);
                }
            }
        });
    }

    function showCategoryChildren(parentId) {
        const rows = document.querySelectorAll('[data-table-row]');
        rows.forEach(row => {
            const rowData = JSON.parse(row.getAttribute('data-row-data') || '{}');
            if (rowData.parent_id == parentId) {
                row.style.display = '';
                // If this child was previously expanded, show its children too
                const childButton = document.querySelector(`[data-category-id="${rowData.id}"]`);
                if (childButton && childButton.getAttribute('data-expanded') === 'true') {
                    showCategoryChildren(rowData.id);
                }
            }
        });
    }

    // Initialize: hide all children by default
    document.addEventListener('DOMContentLoaded', function() {
        const rows = document.querySelectorAll('[data-table-row]');
        rows.forEach(row => {
            const rowData = JSON.parse(row.getAttribute('data-row-data') || '{}');
            if (rowData.parent_id) {
                row.style.display = 'none';
            }
        });
    });
</script>

@endsection
