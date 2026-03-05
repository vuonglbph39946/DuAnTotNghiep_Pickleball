@extends('admin.layouts.admin')

@section('title','Create News')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Create New News</h1>
            <p class="text-gray-600 mb-8">Add a new news article to your system</p>

            <x-form
                action="{{ route('admin.news.store') }}"
                method="POST"
                :fields="[
                    [
                        'name' => 'title',
                        'label' => 'Title',
                        'type' => 'text',
                        'placeholder' => 'Enter news title',
                        'required' => true,
                    ],
                    [
                        'name' => 'slug',
                        'label' => 'Slug',
                        'type' => 'text',
                        'placeholder' => 'Auto-generate if empty',
                    ],
                    [
                        'name' => 'summary',
                        'label' => 'Summary',
                        'type' => 'textarea',
                        'rows' => 3,
                        'placeholder' => 'Short summary of the news...',
                    ],
                    [
                        'name' => 'content',
                        'label' => 'Content',
                        'type' => 'editor',
                        'required' => true,
                    ],
                    [
                        'name' => 'thumbnail',
                        'label' => 'Thumbnail URL',
                        'type' => 'text',
                        'placeholder' => 'https://example.com/image.jpg',
                    ],
                    [
                        'name' => 'category_id',
                        'label' => 'Category',
                        'type' => 'select',
                        'options' => ['' => 'Select Category'] + $categories->pluck('name', 'id')->toArray(),
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
                        'required' => true,
                    ],
                    [
                        'name' => 'published_at',
                        'label' => 'Published At',
                        'type' => 'datetime-local',
                    ],
                ]"
                submitButtonText="Create News"
                submitButtonClass="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition"
                :showCancelButton="true"
                cancelButtonUrl="{{ route('admin.news.index') }}"
            />
        </div>
    </div>
</div>
@endsection
