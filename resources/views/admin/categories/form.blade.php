@extends('admin.layouts.admin')

@section('title', ucfirst($mode) . ' Category')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($mode === 'view')
            <!-- VIEW MODE -->
            <!-- Header -->
            <div class="p-6 border-b border-gray-200 bg-gray-50">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $category->name }}</h1>
                        <div class="flex items-center gap-4 text-sm text-gray-600">
                            <span>Slug: <code class="bg-gray-100 px-2 py-1 rounded">{{ $category->slug }}</code></span>
                            <span>•</span>
                            <span>Created: {{ $category->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        @if($category->status)
                            <span class="px-3 py-1 rounded text-sm font-medium bg-green-100 text-green-800">Active</span>
                        @else
                            <span class="px-3 py-1 rounded text-sm font-medium bg-gray-100 text-gray-800">Inactive</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="p-6 border-b border-gray-200 bg-white">
                <div class="flex gap-3">
                    <a 
                        href="{{ route('admin.categories.form', ['category' => $category->id]) }}?mode=edit"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium"
                    >
                        <i class="fa-solid fa-edit mr-2"></i>
                        Edit
                    </a>
                    <button 
                        onclick="openDeleteCategoryDialog({{ $category->id }})"
                        class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium"
                    >
                        <i class="fa-solid fa-trash mr-2"></i>
                        Delete
                    </button>
                    <a 
                        href="{{ route('admin.categories.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition font-medium"
                    >
                        <i class="fa-solid fa-arrow-left mr-2"></i>
                        Back to List
                    </a>
                </div>
            </div>

            <!-- Content -->
            <div class="p-8">
                <div class="grid grid-cols-2 gap-6 mb-8">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Parent Category</h3>
                        <p class="text-gray-900">
                            @if($category->parent)
                                <a href="{{ route('admin.categories.form', ['category' => $category->parent->id]) }}?mode=view" class="text-blue-600 hover:underline">
                                    {{ $category->parent->name }}
                                </a>
                            @else
                                <span class="text-gray-400">Root Category</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Status</h3>
                        <p class="text-gray-900">
                            @if($category->status)
                                <span class="px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800">Active</span>
                            @else
                                <span class="px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-800">Inactive</span>
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Subcategories -->
                @if($category->children->count() > 0)
                    <div class="mt-8 pt-8 border-t border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Subcategories ({{ $category->children->count() }})</h3>
                        <div class="grid grid-cols-2 gap-4">
                            @foreach($category->children as $child)
                                <div class="p-4 bg-gray-50 rounded-lg">
                                    <a href="{{ route('admin.categories.form', ['category' => $child->id]) }}?mode=view" class="text-blue-600 hover:underline font-medium">
                                        {{ $child->name }}
                                    </a>
                                    <p class="text-sm text-gray-500 mt-1">{{ $child->slug }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- News in this category -->
                @if($category->news->count() > 0)
                    <div class="mt-8 pt-8 border-t border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">News in this Category ({{ $category->news->count() }})</h3>
                        <div class="space-y-2">
                            @foreach($category->news->take(10) as $newsItem)
                                <div class="p-3 bg-gray-50 rounded-lg">
                                    <a href="{{ route('admin.news.show', $newsItem->id) }}" class="text-blue-600 hover:underline font-medium">
                                        {{ $newsItem->title }}
                                    </a>
                                </div>
                            @endforeach
                            @if($category->news->count() > 10)
                                <p class="text-sm text-gray-500 mt-2">And {{ $category->news->count() - 10 }} more...</p>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Additional Info -->
                <div class="mt-8 pt-8 border-t border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Additional Information</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="text-sm font-medium text-gray-500">Created At:</span>
                            <span class="text-sm text-gray-900 ml-2">{{ $category->created_at->format('d/m/Y H:i:s') }}</span>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-500">Updated At:</span>
                            <span class="text-sm text-gray-900 ml-2">{{ $category->updated_at->format('d/m/Y H:i:s') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- CREATE/EDIT MODE -->
            <div class="p-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                    {{ $mode === 'create' ? 'Create New Category' : 'Edit Category' }}
                </h1>
                <p class="text-gray-600 mb-8">
                    {{ $mode === 'create' ? 'Add a new category to your system' : 'Update category information' }}
                </p>

                <x-form
                    action="{{ $mode === 'create' ? route('admin.categories.store') : route('admin.categories.update', $category->id) }}"
                    method="{{ $mode === 'create' ? 'POST' : 'PUT' }}"
                    :fields="[
                        [
                            'name' => 'name',
                            'label' => 'Category Name',
                            'type' => 'text',
                            'placeholder' => 'Enter category name',
                            'value' => $mode === 'edit' && $category ? old('name', $category->name) : old('name'),
                            'required' => true,
                        ],
                        [
                            'name' => 'slug',
                            'label' => 'Slug',
                            'type' => 'text',
                            'placeholder' => 'Auto-generate if empty',
                            'value' => $mode === 'edit' && $category ? old('slug', $category->slug) : old('slug'),
                        ],
                        [
                            'name' => 'parent_id',
                            'label' => 'Parent Category',
                            'type' => 'select',
                            'options' => collect(['' => 'None (Root Category)'])->merge($parentCategories->mapWithKeys(function($cat) {
                                return [$cat->name => $cat->id];
                            }))->toArray(),
                            'value' => $mode === 'edit' && $category ? old('parent_id', $category->parent_id) : old('parent_id'),
                        ],
                        [
                            'name' => 'status',
                            'label' => 'Status',
                            'type' => 'select',
                            'options' => [
                                'Active' => 1,
                                'Inactive' => 0,
                            ],
                            'value' => $mode === 'edit' && $category ? old('status', $category->status) : (old('status', 1)),
                            'required' => true,
                        ],
                    ]"
                    submitButtonText="{{ $mode === 'create' ? 'Create Category' : 'Update Category' }}"
                    submitButtonClass="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition"
                    :showCancelButton="true"
                    cancelButtonUrl="{{ route('admin.categories.index') }}"
                />
            </div>
        @endif
    </div>
</div>

@if($mode === 'view')
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
    function openDeleteCategoryDialog(categoryId) {
        document.getElementById('deleteCategoryForm').action = '{{ route("admin.categories.destroy", ":id") }}'.replace(':id', categoryId);
        openDialog('deleteCategoryDialog');
    }

    document.addEventListener('DOMContentLoaded', function() {
        const deleteForm = document.getElementById('deleteCategoryForm');

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
                        if (typeof showToast === 'function') {
                            showToast(data.message || 'Category deleted successfully!', 'success');
                        }
                        setTimeout(() => {
                            window.location.href = '{{ route("admin.categories.index") }}';
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
@endif

@endsection
