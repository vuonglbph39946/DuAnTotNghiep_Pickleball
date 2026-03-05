@extends('admin.layouts.admin')

@section('title','Edit News')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Edit News</h1>
            <p class="text-gray-600 mb-8">Update news article information</p>

            <x-form
                action="{{ route('admin.news.update', $news->id) }}"
                method="PUT"
                :fields="[
                    [
                        'name' => 'title',
                        'label' => 'Title',
                        'type' => 'text',
                        'placeholder' => 'Enter news title',
                        'value' => old('title', $news->title),
                        'required' => true,
                    ],
                    [
                        'name' => 'slug',
                        'label' => 'Slug',
                        'type' => 'text',
                        'placeholder' => 'Auto-generate if empty',
                        'value' => old('slug', $news->slug),
                    ],
                    [
                        'name' => 'summary',
                        'label' => 'Summary',
                        'type' => 'textarea',
                        'rows' => 3,
                        'placeholder' => 'Short summary of the news...',
                        'value' => old('summary', $news->summary),
                    ],
                    [
                        'name' => 'content',
                        'label' => 'Content',
                        'type' => 'editor',
                        'value' => old('content', $news->content),
                        'required' => true,
                    ],
                    [
                        'name' => 'thumbnail',
                        'label' => 'Thumbnail URL',
                        'type' => 'text',
                        'placeholder' => 'https://example.com/image.jpg',
                        'value' => old('thumbnail', $news->thumbnail),
                    ],
                    [
                        'name' => 'category_id',
                        'label' => 'Category',
                        'type' => 'select',
                        'options' => ['' => 'Select Category'] + $categories->pluck('name', 'id')->toArray(),
                        'value' => old('category_id', $news->category_id),
                    ],
                    [
                        'name' => 'status',
                        'label' => 'Status',
                        'type' => 'select',
                        'options' => [
                            'Draft' => 0,
                            'Published' => 1,
                            'Archived' => 2,
                        ],
                        'value' => old('status', $news->status),
                        'required' => true,
                    ],
                    [
                        'name' => 'published_at',
                        'label' => 'Published At',
                        'type' => 'datetime-local',
                        'value' => old('published_at', $news->published_at ? $news->published_at->format('Y-m-d\TH:i') : ''),
                    ],
                ]"
                submitButtonText="Update News"
                submitButtonClass="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition"
                :showCancelButton="true"
                cancelButtonUrl="{{ route('admin.news.index') }}"
            />
        </div>
    </div>
</div>
@endsection
