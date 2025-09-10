document.addEventListener('DOMContentLoaded', function() {
    const toastEl = document.getElementById('mainToast');
    
    if (toastEl) {
        const toast = new bootstrap.Toast(toastEl, {
            delay: 5000,
            animation: true
        });
        
        // Function to show standardized toasts
        window.showToast = function(message, type = 'info') {
            const toastBody = document.getElementById('mainToastBody');
            const toastIcon = document.getElementById('mainToastIcon');
            
            if (!toastBody || !toastIcon) return;
            
            // Remove all status classes
            toastEl.classList.remove('bg-success', 'bg-danger', 'bg-warning', 'bg-info', 'toast-success', 'toast-danger', 'toast-warning', 'toast-info');
            
            // Format message if it has multiple errors
            if (typeof message === 'string' && message.includes('<br>')) {
                let errors = message.split('<br>');
                message = errors.map(err => `• ${err.trim()}`).join('<br>');
            }
            
            // Set content
            toastBody.innerHTML = message;
            
            // Apply proper styling based on type
            switch (type) {
                case 'success':
                    toastEl.classList.add('toast-success');
                    toastIcon.innerHTML = '<i class="fas fa-check-circle text-success fs-5"></i>';
                    break;
                case 'danger':
                case 'error':
                    toastEl.classList.add('toast-danger');
                    toastIcon.innerHTML = '<i class="fas fa-exclamation-circle text-danger fs-5"></i>';
                    break;
                case 'warning':
                    toastEl.classList.add('toast-warning');
                    toastIcon.innerHTML = '<i class="fas fa-exclamation-triangle text-warning fs-5"></i>';
                    break;
                case 'info':
                default:
                    toastEl.classList.add('toast-info');
                    toastIcon.innerHTML = '<i class="fas fa-info-circle text-info fs-5"></i>';
                    break;
            }
            
            // Show the toast
            toast.show();
        };
        
        // Auto show if has content
        const toastBody = document.getElementById('mainToastBody');
        if (toastBody && toastBody.innerHTML.trim() !== '') {
            // Determine type from classes
            let type = 'info';
            if (toastEl.classList.contains('bg-success')) {
                type = 'success';
            } else if (toastEl.classList.contains('bg-danger')) {
                type = 'danger';
            } else if (toastEl.classList.contains('bg-warning')) {
                type = 'warning';
            }
            
            window.showToast(toastBody.innerHTML, type);
        }
    }
});