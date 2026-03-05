@extends('admin.layouts.admin')

@section('title','Create')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Create New Contact</h1>
            <p class="text-gray-600 mb-8">Add a new contact to your system</p>

            <x-form
                action="{{ route('contacts.store') }}"
                method="POST"
                :fields="[
                    [
                        'name' => 'name',
                        'label' => 'Full Name',
                        'type' => 'text',
                        'placeholder' => 'Enter contact name',
                        'required' => true,
                    ],
                    [
                        'name' => 'email',
                        'label' => 'Email Address',
                        'type' => 'email',
                        'placeholder' => 'contact@example.com',
                        'required' => true,
                    ],
                    [
                        'name' => 'phone',
                        'label' => 'Phone Number',
                        'type' => 'tel',
                        'placeholder' => '+1 (555) 123-4567',
                    ],
                    [
                        'name' => 'subject',
                        'label' => 'Subject',
                        'type' => 'text',
                        'placeholder' => 'What is this contact about?',
                        'required' => true,
                    ],
                    [
                        'name' => 'message',
                        'label' => 'Message',
                        'type' => 'textarea',
                        'rows' => 5,
                        'placeholder' => 'Enter message or notes...',
                        'required' => true,
                    ],
                ]"
                submitButtonText="Create"
                submitButtonClass="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition"
                :showCancelButton="true"
                cancelButtonUrl="{{ route('contacts.index') }}"
            />
        </div>
    </div>
</div>
@endsection
