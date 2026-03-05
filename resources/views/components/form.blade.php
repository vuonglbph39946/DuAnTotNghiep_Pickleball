{{-- 
    Form Component - Dynamic and reusable form builder
    
    Location: resources/views/components/form.blade.php
    Usage: <x-form :action="route('contacts.store')" :fields="$fields" />
--}}

@props([
    'action' => '#',
    'method' => 'POST',
    'fields' => [],
    'submitButtonText' => 'Submit',
    'submitButtonClass' => 'bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition',
    'showCancelButton' => false,
    'cancelButtonUrl' => '#',
    'id' => null,
])

<form action="{{ $action }}" method="{{ $method }}" enctype="multipart/form-data" class="space-y-6" @if($id) id="{{ $id }}" @endif>
    @csrf
    @if(in_array($method, ['PUT', 'PATCH', 'DELETE']))
        @method($method)
    @endif

    @foreach($fields as $field)
        @php
            $fieldType = $field['type'] ?? 'text';
            $fieldName = $field['name'] ?? '';
            $fieldLabel = $field['label'] ?? ucfirst(str_replace('_', ' ', $fieldName));
            $fieldValue = $field['value'] ?? old($fieldName);
            $fieldRequired = $field['required'] ?? false;
            $fieldClass = $field['class'] ?? 'w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400';
            $hasError = $errors->has($fieldName);
        @endphp

        <div>
            <!-- Label for non-checkbox/radio fields -->
            @if(!in_array($fieldType, ['checkbox', 'radio']))
                <label for="{{ $fieldName }}" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ $fieldLabel }}
                    @if($fieldRequired)
                        <span class="text-red-500">*</span>
                    @endif
                </label>
            @endif

            <!-- TEXT INPUT TYPES -->
            @if(in_array($fieldType, ['text', 'email', 'password', 'number', 'tel', 'date', 'url', 'search']))
                <input 
                    type="{{ $fieldType }}" 
                    id="{{ $fieldName }}"
                    name="{{ $fieldName }}" 
                    value="{{ $fieldValue }}"
                    class="{{ $fieldClass }} @error($fieldName) border-red-500 @enderror"
                    @if($fieldRequired) required @endif
                    placeholder="{{ $field['placeholder'] ?? '' }}"
                    @if(isset($field['min'])) min="{{ $field['min'] }}" @endif
                    @if(isset($field['max'])) max="{{ $field['max'] }}" @endif
                    @if(isset($field['step'])) step="{{ $field['step'] }}" @endif
                    @if(isset($field['pattern'])) pattern="{{ $field['pattern'] }}" @endif
                />
            @endif

            <!-- TEXTAREA -->
            @if($fieldType === 'textarea')
                <textarea 
                    id="{{ $fieldName }}"
                    name="{{ $fieldName }}" 
                    rows="{{ $field['rows'] ?? 4 }}"
                    class="{{ $fieldClass }} @error($fieldName) border-red-500 @enderror resize-none"
                    @if($fieldRequired) required @endif
                    placeholder="{{ $field['placeholder'] ?? '' }}"
                >{{ $fieldValue }}</textarea>
            @endif

            <!-- TEXT EDITOR (SUMMERNOTE) -->
            @if($fieldType === 'editor')
                <textarea 
                    id="{{ $fieldName }}"
                    name="{{ $fieldName }}" 
                    class="summernote @error($fieldName) border-red-500 @enderror"
                    @if($fieldRequired) required @endif
                >{{ $fieldValue }}</textarea>
            @endif

            <!-- SELECT / DROPDOWN -->
            @if($fieldType === 'select')
                <select 
                    id="{{ $fieldName }}"
                    name="{{ $fieldName }}"
                    class="{{ $fieldClass }} @error($fieldName) border-red-500 @enderror"
                    @if($fieldRequired) required @endif
                >
                    <option value="">-- Select {{ strtolower($fieldLabel) }} --</option>
                    @foreach($field['options'] ?? [] as $optionLabel => $optionValue)
                        <option value="{{ $optionValue }}" @selected($fieldValue === (string)$optionValue)>
                            {{ $optionLabel }}
                        </option>
                    @endforeach
                </select>
            @endif

            <!-- SINGLE FILE UPLOAD -->
            @if($fieldType === 'file')
                <input 
                    type="file" 
                    id="{{ $fieldName }}"
                    name="{{ $fieldName }}" 
                    accept="{{ $field['accept'] ?? '' }}"
                    class="{{ $fieldClass }} @error($fieldName) border-red-500 @enderror"
                    @if($fieldRequired) required @endif
                />
                @if(isset($field['help']))
                    <p class="mt-2 text-sm text-gray-500">{{ $field['help'] }}</p>
                @endif
            @endif

            <!-- MULTIPLE FILE UPLOAD -->
            @if($fieldType === 'file-multiple')
                <input 
                    type="file" 
                    id="{{ $fieldName }}"
                    name="{{ $fieldName }}[]" 
                    multiple
                    accept="{{ $field['accept'] ?? '' }}"
                    class="{{ $fieldClass }} @error($fieldName) border-red-500 @enderror"
                    @if($fieldRequired) required @endif
                />
                @if(isset($field['help']))
                    <p class="mt-2 text-sm text-gray-500">{{ $field['help'] }}</p>
                @endif
            @endif

            <!-- CHECKBOX -->
            @if($fieldType === 'checkbox')
                <div class="flex items-center">
                    <input 
                        type="checkbox" 
                        id="{{ $fieldName }}"
                        name="{{ $fieldName }}" 
                        value="{{ $field['value'] ?? 1 }}"
                        class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-2 focus:ring-blue-500"
                        @if($fieldValue) checked @endif
                        @if($fieldRequired) required @endif
                    />
                    <label for="{{ $fieldName }}" class="ml-2 block text-sm text-gray-700">
                        {{ $fieldLabel }}
                        @if($fieldRequired)
                            <span class="text-red-500">*</span>
                        @endif
                    </label>
                </div>
            @endif

            <!-- RADIO BUTTONS -->
            @if($fieldType === 'radio')
                <fieldset>
                    <legend class="block text-sm font-medium text-gray-700 mb-3">{{ $fieldLabel }}</legend>
                    <div class="space-y-2">
                        @foreach($field['options'] ?? [] as $optionLabel => $optionValue)
                            <div class="flex items-center">
                                <input 
                                    type="radio" 
                                    id="{{ $fieldName }}_{{ $optionValue }}"
                                    name="{{ $fieldName }}" 
                                    value="{{ $optionValue }}"
                                    class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-2 focus:ring-blue-500"
                                    @if($fieldValue === (string)$optionValue) checked @endif
                                    @if($fieldRequired) required @endif
                                />
                                <label for="{{ $fieldName }}_{{ $optionValue }}" class="ml-2 block text-sm text-gray-700">
                                    {{ $optionLabel }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </fieldset>
            @endif

            <!-- CHECKBOX GROUP -->
            @if($fieldType === 'checkbox-group')
                <div class="space-y-2">
                    @foreach($field['options'] ?? [] as $optionLabel => $optionValue)
                        <div class="flex items-center">
                            <input 
                                type="checkbox" 
                                id="{{ $fieldName }}_{{ $optionValue }}"
                                name="{{ $fieldName }}[]" 
                                value="{{ $optionValue }}"
                                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-2 focus:ring-blue-500"
                                @if(is_array($fieldValue) && in_array($optionValue, $fieldValue)) checked @endif
                            />
                            <label for="{{ $fieldName }}_{{ $optionValue }}" class="ml-2 block text-sm text-gray-700">
                                {{ $optionLabel }}
                            </label>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- ERROR MESSAGE -->
            @error($fieldName)
                <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>
    @endforeach

    <!-- BUTTONS -->
    <div class="flex gap-4 pt-4">
        <button 
            type="submit" 
            class="{{ $submitButtonClass }}"
        >
            {{ $submitButtonText }}
        </button>
        
        @if($showCancelButton)
            <a 
                href="{{ $cancelButtonUrl }}" 
                class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-lg font-medium transition"
            >
                Cancel
            </a>
        @endif
    </div>
</form>

@push('scripts')
    @if(collect($fields)->where('type', 'editor')->count() > 0)
        <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js"></script>
        <script>
            $(document).ready(function() {
                $('.summernote').summernote({
                    placeholder: 'Write your content here...',
                    tabsize: 2,
                    height: 300,
                    toolbar: [
                        ['style', ['style']],
                        ['font', ['bold', 'underline', 'clear']],
                        ['color', ['color']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['table', ['table']],
                        ['insert', ['link', 'picture']],
                        ['view', ['fullscreen', 'codeview', 'help']]
                    ]
                });
            });
        </script>
    @endif
@endpush
