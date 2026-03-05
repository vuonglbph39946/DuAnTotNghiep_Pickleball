{{-- 
    Toast Notification Component
    Usage: Call showToast('message', 'success') or showToast('message', 'error')
--}}

<div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

<script>
    function showToast(message, type = 'success', duration = 3000) {
        const container = document.getElementById('toast-container');
        if (!container) return;

        const toastId = 'toast-' + Date.now();
        const bgColor = type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500';
        const icon = type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle';

        const toast = document.createElement('div');
        toast.id = toastId;
        toast.className = `${bgColor} text-white px-6 py-4 rounded-lg shadow-lg flex items-center gap-3 min-w-[300px] max-w-md transform transition-all duration-300 translate-x-full opacity-0`;
        
        toast.innerHTML = `
            <i class="fa-solid ${icon} text-xl"></i>
            <span class="flex-1">${message}</span>
            <button onclick="closeToast('${toastId}')" class="text-white hover:text-gray-200 focus:outline-none">
                <i class="fa-solid fa-times"></i>
            </button>
        `;

        container.appendChild(toast);

        // Trigger animation
        setTimeout(() => {
            toast.classList.remove('translate-x-full', 'opacity-0');
            toast.classList.add('translate-x-0', 'opacity-100');
        }, 10);

        // Auto close
        if (duration > 0) {
            setTimeout(() => {
                closeToast(toastId);
            }, duration);
        }
    }

    function closeToast(toastId) {
        const toast = document.getElementById(toastId);
        if (!toast) return;

        toast.classList.remove('translate-x-0', 'opacity-100');
        toast.classList.add('translate-x-full', 'opacity-0');

        setTimeout(() => {
            toast.remove();
        }, 300);
    }
</script>
