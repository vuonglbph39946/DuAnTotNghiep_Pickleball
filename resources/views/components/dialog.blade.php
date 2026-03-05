{{-- 
    Dialog / Modal Component
    Location: resources/views/components/dialog.blade.php
    
    Usage:
    <x-dialog title="Title" size="md" id="myDialog">
        <p>Your content here</p>
    </x-dialog>
    
    Or with component inside:
    <x-dialog title="Create User">
        <x-form :fields="$fields" action="{{ route('users.store') }}" />
    </x-dialog>
--}}

@props([
    'title' => 'Dialog',
    'subtitle' => '',
    'size' => 'md',
    'id' => 'dialog-' . uniqid(),
    'closeButton' => true,
    'backdrop' => true,
    'animated' => true,
])

@php
    $sizeClasses = [
        'sm' => 'max-w-sm',
        // Mặc định ~500px và responsive: mobile full width, desktop 500px
        'md' => 'w-full max-w-[500px] md:w-[500px]',
        'lg' => 'max-w-lg',
        'xl' => 'max-w-xl',
        '2xl' => 'max-w-2xl',
        'full' => 'max-w-full',
    ];
    
    $dialogSize = $sizeClasses[$size] ?? $sizeClasses['md'];
@endphp

<!-- Dialog Backdrop -->
<div 
    id="{{ $id }}-backdrop" 
    class="hidden fixed inset-0 z-40 @if($animated) transition-opacity duration-300 @endif"
    style="background-color: rgba(0, 0, 0, 0.5);" {{-- mờ 50% nền xung quanh --}}
    @click="closeDialog('{{ $id }}')"
></div>

<!-- Dialog -->
<div 
    id="{{ $id }}" 
    class="hidden fixed inset-0 flex items-center justify-center z-50 p-4 @if($animated) transition-all duration-300 @endif"
>
    <div 
        class="bg-white rounded-lg shadow-xl overflow-hidden md:w-[500px] w-full max-h-screen overflow-y-auto @if($animated) transform transition-all duration-300 scale-95 opacity-0 @endif"
        id="{{ $id }}-content"
        @click.stop
    >
        <!-- Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200 bg-gray-50">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">{{ $title }}</h2>
                @if($subtitle)
                    <p class="text-sm text-gray-600 mt-1">{{ $subtitle }}</p>
                @endif
            </div>
            @if($closeButton)
                <button 
                    type="button"
                    onclick="closeDialog('{{ $id }}')"
                    class="text-gray-400 hover:text-gray-600 focus:outline-none"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            @endif
        </div>

        <!-- Body -->
        <div class="p-6">
            {{ $slot }}
        </div>
    </div>
</div>

<script>
    function openDialog(dialogId) {
        const dialog = document.getElementById(dialogId);
        const backdrop = document.getElementById(dialogId + '-backdrop');
        const content = document.getElementById(dialogId + '-content');
        
        if (dialog && backdrop) {
            dialog.classList.remove('hidden');
            backdrop.classList.remove('hidden');
            
            // Trigger animation
            setTimeout(() => {
                backdrop.classList.add('opacity-100');
                if (content) {
                    content.classList.remove('scale-95', 'opacity-0');
                    content.classList.add('scale-100', 'opacity-100');
                }
            }, 10);
            
            // Prevent body scroll
            document.body.style.overflow = 'hidden';
        }
    }

    function closeDialog(dialogId) {
        const dialog = document.getElementById(dialogId);
        const backdrop = document.getElementById(dialogId + '-backdrop');
        const content = document.getElementById(dialogId + '-content');
        
        if (dialog && backdrop) {
            backdrop.classList.add('opacity-0');
            if (content) {
                content.classList.add('scale-95', 'opacity-0');
            }
            
            // Wait for animation to finish
            setTimeout(() => {
                dialog.classList.add('hidden');
                backdrop.classList.add('hidden');
                document.body.style.overflow = '';
            }, 300);
        }
    }

    // Close dialog on Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            const dialogs = document.querySelectorAll('[id*="-backdrop"]:not(.hidden)');
            dialogs.forEach(backdrop => {
                const dialogId = backdrop.id.replace('-backdrop', '');
                closeDialog(dialogId);
            });
        }
    });
</script>

@push('scripts')
    @if($animated)
        <style>
            .opacity-0 { opacity: 0; }
            .opacity-100 { opacity: 1; }
            .scale-95 { transform: scale(0.95); }
            .scale-100 { transform: scale(1); }
            .transition-opacity { transition-property: opacity; }
            .transition-all { transition-property: all; }
            .duration-300 { transition-duration: 300ms; }
        </style>
    @endif
@endpush
