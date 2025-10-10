/**
 * Helper functions to standardize status badges throughout the application
 */
document.addEventListener('DOMContentLoaded', function() {
    // Normalize and style all status badges
    function initStatusBadges() {
        document.querySelectorAll('[data-status]').forEach(el => {
            const raw = (el.getAttribute('data-status') || '').toLowerCase();
            let variant = '';
            if (raw === 'in progress') variant = 'status-in-progress';
            else if (raw === 'selesai' || raw === 'completed') variant = 'status-completed';
            else if (raw === 'pending') variant = 'status-pending';
            else if (raw === 'danger' || raw === 'critical') variant = 'status-danger';

            // Ensure base class
            el.classList.add('status-badge');

            // Remove old variants and apply new
            el.classList.remove('badge-ditugaskan','badge-selesai','badge-pending','badge-danger','badge','bg-warning','bg-success','bg-info');
            ['status-assigned','status-completed','status-pending','status-danger'].forEach(c => el.classList.remove(c));
            if (variant) el.classList.add(variant);

            // Compress text label for in-progress to save space on small columns
            const currentText = (el.textContent || '').trim();
            if (variant === 'status-in-progress' && currentText.length > 0) {
                el.textContent = 'PROCESS';
            }
        });
    }

    // Create a badge for status (preferred renderer)
    window.createStatusBadge = function(status) {
        const text = String(status || '');
        const lc = text.toLowerCase();
        let variant = 'status-in-progress'; // Default to in-progress for new reports
        if (lc === 'in progress') variant = 'status-in-progress';
        else if (lc === 'selesai' || lc === 'completed') variant = 'status-completed';
        else if (lc === 'danger' || lc === 'critical') variant = 'status-danger';
        const label = variant === 'status-in-progress' ? 'PROCESS' : text;
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