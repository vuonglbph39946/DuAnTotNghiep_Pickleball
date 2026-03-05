@extends('admin.layouts.admin')

@section('title', $mode === 'create' ? 'Create User' : ($mode === 'edit' ? 'Edit User' : 'View User'))

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">
                @if($mode === 'create')
                    Create New User
                @elseif($mode === 'edit')
                    Edit User
                @else
                    View User
                @endif
            </h1>
            <p class="mt-2 text-sm text-gray-600">
                @if($mode === 'create')
                    Add a new user to your system
                @elseif($mode === 'edit')
                    Update user information
                @else
                    View user details
                @endif
            </p>
        </div>
        
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6">
            @if($mode === 'view')
                <!-- VIEW MODE - Read Only -->
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                        <input
                            type="text"
                            value="{{ $user->name ?? '' }}"
                            disabled
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 cursor-not-allowed"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                        <input
                            type="email"
                            value="{{ $user->email ?? '' }}"
                            disabled
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 cursor-not-allowed"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                        <input
                            type="tel"
                            value="{{ $user->phone ?? 'N/A' }}"
                            disabled
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 cursor-not-allowed"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                        <input
                            type="text"
                            value="{{ $user->role->name ?? 'No Role' }}"
                            disabled
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 cursor-not-allowed"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <input
                            type="text"
                            value="{{ $user->status ? 'Active' : 'Inactive' }}"
                            disabled
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 cursor-not-allowed"
                        />
                    </div>

                    <div class="pt-4 border-t border-gray-200">
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-500">Created:</span>
                                <span class="ml-2 text-gray-900">{{ $user->created_at ? $user->created_at->format('d/m/Y H:i') : 'N/A' }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Updated:</span>
                                <span class="ml-2 text-gray-900">{{ $user->updated_at ? $user->updated_at->format('d/m/Y H:i') : 'N/A' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-4 pt-6">
                        <a
                            href="{{ route('admin.users.edit', $user->id) }}"
                            class="flex-1 text-center bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition"
                        >
                            Edit
                        </a>
                        <a
                            href="{{ route('admin.users.index') }}"
                            class="flex-1 text-center bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-lg font-medium transition"
                        >
                            Back
                        </a>
                    </div>
                </div>
            @else
                <!-- CREATE/EDIT MODE - Form -->
                <form
                    action="{{ $mode === 'create' ? route('admin.users.store') : route('admin.users.update', $user->id) }}"
                    method="POST"
                    class="space-y-6"
                >
                    @csrf
                    @if($mode === 'edit')
                        @method('PUT')
                    @endif

                    <!-- Full Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Full Name
                            <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name', $user->name ?? '') }}"
                            placeholder="Enter user full name"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400
                            @error('name') border-red-500 @enderror"
                        />
                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email Address
                            <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email', $user->email ?? '') }}"
                            placeholder="user@example.com"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400
                            @error('email') border-red-500 @enderror"
                        />
                        @error('email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                        <input
                            type="tel"
                            id="phone"
                            name="phone"
                            value="{{ old('phone', $user->phone ?? '') }}"
                            placeholder="+1 (555) 123-4567"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400
                            @error('phone') border-red-500 @enderror"
                        />
                        @error('phone')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Password
                            @if($mode === 'create')
                                <span class="text-red-500">*</span>
                            @else
                                <span class="text-gray-500 font-normal">(leave blank to keep current)</span>
                            @endif
                        </label>
                        <div class="relative">
                            <input
                                type="password"
                                id="password"
                                name="password"
                                placeholder="@if($mode === 'create')Enter password (min 8 characters)@else Leave blank to keep current password @endif"
                                @if($mode === 'create') required @endif
                                class="w-full px-4 py-2 pr-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400
                                @error('password') border-red-500 @enderror"
                            />
                            <button
                                type="button"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 transition"
                                onclick="togglePassword('password', this)"
                                tabindex="-1"
                            >
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Confirmation -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                            Confirm Password
                            @if($mode === 'create')
                                <span class="text-red-500">*</span>
                            @else
                                <span class="text-gray-500 font-normal">(leave blank to keep current)</span>
                            @endif
                        </label>
                        <div class="relative">
                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                placeholder="@if($mode === 'create')Confirm password@else Leave blank to keep current password @endif"
                                @if($mode === 'create') required @endif
                                class="w-full px-4 py-2 pr-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400
                                @error('password_confirmation') border-red-500 @enderror"
                            />
                            <button
                                type="button"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 transition"
                                onclick="togglePassword('password_confirmation', this)"
                                tabindex="-1"
                            >
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                        @error('password_confirmation')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Role -->
                    <div>
                        <label for="role_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Assign Role
                            <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="role_id"
                            name="role_id"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400
                            @error('role_id') border-red-500 @enderror"
                        >
                            <option value="">-- Select Role --</option>
                            @foreach($roles as $role)
                                <option
                                    value="{{ $role->id }}"
                                    @if(old('role_id', $user->role_id ?? null) == $role->id) selected @endif
                                >
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('role_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            Status
                            <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="status"
                            name="status"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400
                            @error('status') border-red-500 @enderror"
                        >
                            <option value="1" @if(old('status', $user->status ?? 1) == 1) selected @endif>Active</option>
                            <option value="0" @if(old('status', $user->status ?? 1) == 0) selected @endif>Inactive</option>
                        </select>
                        @error('status')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Form Actions -->
                    <div class="flex gap-4 pt-6 border-t border-gray-200">
                        <button
                            type="submit"
                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition"
                        >
                            @if($mode === 'create')
                                Create User
                            @else
                                Update User
                            @endif
                        </button>
                        <a
                            href="{{ route('admin.users.index') }}"
                            class="flex-1 text-center bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-lg font-medium transition"
                        >
                            Cancel
                        </a>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function togglePassword(fieldId, button) {
        const field = document.getElementById(fieldId);
        const icon = button.querySelector('i');
        
        if (field.type === 'password') {
            field.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            field.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
</script>
@endpush
