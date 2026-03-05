@extends('admin.layouts.admin')

@section('title','View News')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <!-- Header -->
        <div class="p-6 border-b border-gray-200 bg-gray-50">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $news->title }}</h1>
                    <div class="flex items-center gap-4 text-sm text-gray-600">
                        <span>Slug: <code class="bg-gray-100 px-2 py-1 rounded">{{ $news->slug }}</code></span>
                        <span>•</span>
                        <span>Views: {{ $news->views }}</span>
                        <span>•</span>
                        <span>Created: {{ $news->created_at->format('d/m/Y H:i') }}</span>
                        @if($news->published_at)
                            <span>•</span>
                            <span>Published: {{ $news->published_at->format('d/m/Y H:i') }}</span>
                        @endif
                    </div>
                </div>
                <div class="flex gap-2">
                    @php
                        $statuses = [
                            0 => ['text' => 'Draft', 'class' => 'bg-gray-100 text-gray-800'],
                            1 => ['text' => 'Published', 'class' => 'bg-green-100 text-green-800'],
                            2 => ['text' => 'Archived', 'class' => 'bg-yellow-100 text-yellow-800']
                        ];
                        $status = $statuses[$news->status] ?? $statuses[0];
                    @endphp
                    <span class="px-3 py-1 rounded text-sm font-medium {{ $status['class'] }}">
                        {{ $status['text'] }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="p-6 border-b border-gray-200 bg-white">
            <div class="flex gap-3">
                <a 
                    href="{{ route('admin.news.edit', $news->id) }}"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium"
                >
                    <i class="fa-solid fa-edit mr-2"></i>
                    Edit
                </a>
                <button 
                    onclick="openDeleteNewsDialog({{ $news->id }})"
                    class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium"
                >
                    <i class="fa-solid fa-trash mr-2"></i>
                    Delete
                </button>
                <a 
                    href="{{ route('admin.news.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition font-medium"
                >
                    <i class="fa-solid fa-arrow-left mr-2"></i>
                    Back to List
                </a>
            </div>
        </div>

        <!-- Content -->
        <div class="p-8">
            <!-- Category Info -->
            @if($news->category)
                <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-medium text-gray-500">Category:</span>
                        <a href="{{ route('admin.categories.show', $news->category->id) }}" class="text-blue-600 hover:underline font-medium">
                            {{ $news->category->name }}
                        </a>
                    </div>
                </div>
            @endif

            @if($news->thumbnail)
                <div class="mb-6">
                    <img src="{{ $news->thumbnail }}" alt="{{ $news->title }}" class="w-full rounded-lg shadow-md max-h-96 object-cover">
                </div>
            @endif

            @if($news->summary)
                <div class="mb-6 p-4 bg-blue-50 border-l-4 border-blue-500 rounded">
                    <h2 class="text-lg font-semibold text-gray-900 mb-2">Summary</h2>
                    <p class="text-gray-700">{{ $news->summary }}</p>
                </div>
            @endif

            <div class="prose max-w-none">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Content</h2>
                <div class="text-gray-700">
                    {!! $news->content !!}
                </div>
            </div>

            <!-- Additional Info -->
            <div class="mt-8 pt-8 border-t border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Additional Information</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <span class="text-sm font-medium text-gray-500">Category:</span>
                        <span class="text-sm text-gray-900 ml-2">
                            @if($news->category)
                                <a href="{{ route('admin.categories.show', $news->category->id) }}" class="text-blue-600 hover:underline">
                                    {{ $news->category->name }}
                                </a>
                            @else
                                N/A
                            @endif
                        </span>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Author ID:</span>
                        <span class="text-sm text-gray-900 ml-2">{{ $news->author_id ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Created At:</span>
                        <span class="text-sm text-gray-900 ml-2">{{ $news->created_at->format('d/m/Y H:i:s') }}</span>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Updated At:</span>
                        <span class="text-sm text-gray-900 ml-2">{{ $news->updated_at->format('d/m/Y H:i:s') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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
    function openDeleteNewsDialog(newsId) {
        document.getElementById('deleteNewsForm').action = '{{ route("admin.news.destroy", ":id") }}'.replace(':id', newsId);
        openDialog('deleteNewsDialog');
    }

    document.addEventListener('DOMContentLoaded', function() {
        const deleteForm = document.getElementById('deleteNewsForm');

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
                        // Hiển thị toast thành công
                        if (typeof showToast === 'function') {
                            showToast(data.message || 'News deleted successfully!', 'success');
                        }
                        // Redirect sau 1 giây để người dùng thấy toast
                        setTimeout(() => {
                            window.location.href = '{{ route("admin.news.index") }}';
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
