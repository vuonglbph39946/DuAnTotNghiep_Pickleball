@extends('admin.layouts.admin')

@section('title','Contacts Management')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">Contacts Management</h1>
        <button 
            onclick="openContactDialog()" 
            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium"
        >
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            Add New Contact
        </button>
    </div>

    <!-- Contacts Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6">
            @php
                $columns = [
                    // Cột # lấy theo index của vòng lặp + offset phân trang
                    ['label' => '#', 'key' => '__index', 'class' => 'w-12', 'td_class' => 'font-medium'],
                    ['label' => 'Name', 'key' => 'name'],
                    ['label' => 'Email', 'key' => 'email'],
                    ['label' => 'Phone', 'key' => 'phone'],
                    ['label' => 'Subject', 'key' => 'subject'],
                    ['label' => 'Created', 'key' => 'created_at'],
                ];

                $actions = [
                    [
                        'label' => 'Edit',
                        'icon' => 'fa-edit',
                        'onclick' => 'openContactDialog({id})',
                        'class' => 'text-blue-600 hover:text-blue-800',
                    ],
                    [
                        'label' => 'Delete',
                        'icon' => 'fa-trash',
                        'onclick' => 'openDeleteDialog({id})',
                        'class' => 'text-red-600 hover:text-red-800',
                    ],
                ];
            @endphp

            <x-common-table :columns="$columns" :rows="$rows" :showPagination="true" :actions="$actions" />
        </div>
    </div>
</div>

<!-- ================================================
     CREATE/EDIT CONTACT DIALOG WITH FORM (CHUNG 1 FORM)
     ================================================ -->
<x-dialog 
    title="Contact" 
    subtitle=""
    size="lg"
    id="contactDialog"
>
    <x-form
        action=""
        method="POST"
        id="contactForm"
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
                'rows' => 4,
                'placeholder' => 'Enter message or notes...',
                'required' => true,
            ],
        ]"
        submitButtonText="Save Contact"
        submitButtonClass="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition"
        :showCancelButton="true"
        cancelButtonUrl="javascript:closeDialog('contactDialog')"
    />
</x-dialog>

<!-- ================================================
     DELETE CONFIRMATION DIALOG
     ================================================ -->
<x-dialog 
    title="Delete Contact" 
    subtitle="Are you sure you want to delete this contact?"
    size="md"
    id="deleteContactDialog"
>
    <div class="space-y-4">
        <p class="text-gray-700">This action cannot be undone. The contact will be permanently deleted.</p>
        <form id="deleteContactForm" action="" method="POST" class="mt-4">
            @csrf
            @method('DELETE')
            <div class="flex gap-4 justify-end">
                <button 
                    type="button"
                    onclick="closeDialog('deleteContactDialog')"
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
    let currentContactId = null;

    function openContactDialog(contactId = null) {
        const dialog = document.getElementById('contactDialog');
        const dialogHeader = dialog.querySelector('.flex.items-center.justify-between');
        const dialogTitle = dialogHeader.querySelector('h2');
        let dialogSubtitle = dialogHeader.querySelector('p');
        const form = document.getElementById('contactForm');
        const submitBtn = form.querySelector('button[type="submit"]');
        const methodInput = form.querySelector('input[name="_method"]');

        currentContactId = contactId;

        if (contactId) {
            // Edit mode
            const row = document.querySelector(`[data-row-id="${contactId}"]`);
            if (!row) return;

            const contactData = JSON.parse(row.getAttribute('data-row-data'));
            if (!contactData) return;

            // Update dialog title
            dialogTitle.textContent = 'Edit Contact';
            if (!dialogSubtitle) {
                dialogSubtitle = document.createElement('p');
                dialogSubtitle.className = 'text-sm text-gray-600 mt-1';
                dialogHeader.querySelector('div').appendChild(dialogSubtitle);
            }
            dialogSubtitle.textContent = 'Update contact information';
            submitBtn.textContent = 'Update';

            // Set form action và method
            form.action = '{{ route("admin.contacts.update", ":id") }}'.replace(':id', contactId);
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
            form.querySelector('input[name="name"]').value = contactData.name || '';
            form.querySelector('input[name="email"]').value = contactData.email || '';
            form.querySelector('input[name="phone"]').value = contactData.phone || '';
            form.querySelector('input[name="subject"]').value = contactData.subject || '';
            form.querySelector('textarea[name="message"]').value = contactData.message || '';
        } else {
            // Create mode
            dialogTitle.textContent = 'Create New Contact';
            if (!dialogSubtitle) {
                dialogSubtitle = document.createElement('p');
                dialogSubtitle.className = 'text-sm text-gray-600 mt-1';
                dialogHeader.querySelector('div').appendChild(dialogSubtitle);
            }
            dialogSubtitle.textContent = 'Add a new contact to your list';
            submitBtn.textContent = 'Create Contact';

            // Set form action và method
            form.action = '{{ route("admin.contacts.store") }}';
            if (methodInput) {
                methodInput.remove();
            }

            // Clear form fields
            form.reset();
        }

        openDialog('contactDialog');
    }

    function openDeleteDialog(contactId) {
        // Set form action
        document.getElementById('deleteContactForm').action = '{{ route("admin.contacts.destroy", ":id") }}'.replace(':id', contactId);

        openDialog('deleteContactDialog');
    }

    // Handle form submission với AJAX
    document.addEventListener('DOMContentLoaded', function() {
        const contactForm = document.getElementById('contactForm');
        const deleteForm = document.getElementById('deleteContactForm');

        if (contactForm) {
            contactForm.addEventListener('submit', function(e) {
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
                        closeDialog('contactDialog');
                        // Hiển thị toast thành công
                        if (typeof showToast === 'function') {
                            showToast(data.message || 'Contact saved successfully!', 'success');
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
                        closeDialog('deleteContactDialog');
                        // Hiển thị toast thành công
                        if (typeof showToast === 'function') {
                            showToast(data.message || 'Contact deleted successfully!', 'success');
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
