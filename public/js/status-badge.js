/**
 * Helper functions to standardize status badges throughout the application
 */
document.addEventListener('DOMContentLoaded', function() {
    // Normalize and style all status badges
    function initStatusBadges() {
        document.querySelectorAll('[data-status]').forEach(el => {
            const raw = (el.getAttribute('data-status') || '').toLowerCase();
            let variant = '';
            let label = '';
            
            if (raw === 'assigned') {
                variant = 'status-assigned';
                label = 'ASSIGNED';
            } else if (raw === 'completed') {
                variant = 'status-completed';
                label = 'COMPLETED';
            } else if (raw === 'pending') {
                variant = 'status-pending';
                label = 'PENDING';
            } else if (raw === 'danger' || raw === 'critical') {
                variant = 'status-danger';
                label = 'DANGER';
            }

            // Ensure base class
            el.classList.add('status-badge');

            // Remove old variants and apply new
            el.classList.remove('badge-ditugaskan','badge-selesai','badge-pending','badge-danger','badge','bg-warning','bg-success','bg-info');
            ['status-assigned','status-completed','status-pending','status-danger','status-in-progress'].forEach(c => el.classList.remove(c));
            if (variant) {
                el.classList.add(variant);
                el.textContent = label;
            }
        });
    }

    // Create a badge for status (preferred renderer)
    window.createStatusBadge = function(status) {
        const text = String(status || '');
        const lc = text.toLowerCase();
        let variant = 'status-assigned'; // Default to assigned for new reports
        let label = 'ASSIGNED';
        
        if (lc === 'assigned') {
            variant = 'status-assigned';
            label = 'ASSIGNED';
        } else if (lc === 'completed') {
            variant = 'status-completed';
            label = 'COMPLETED';
        } else if (lc === 'danger' || lc === 'critical') {
            variant = 'status-danger';
            label = 'DANGER';
        }
        
        return `<span class="status-badge ${variant}" data-status="${lc}">${label}</span>`;
    }

    // Initialize badges on page load
    initStatusBadges();

    // Re-initialize badges after AJAX updates (DataTables)
    if (typeof $.fn.dataTable !== 'undefined') {
        $(document).on('draw.dt', function() {
            initStatusBadges();
        });
    }
});