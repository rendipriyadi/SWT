/**
 * Helper functions to standardize status badges throughout the application
 */
document.addEventListener('DOMContentLoaded', function() {
    // Apply consistent styling to all status badges
    function initStatusBadges() {
        document.querySelectorAll('.badge[data-status]').forEach(badge => {
            const status = badge.getAttribute('data-status').toLowerCase();
            
            // Clear any existing classes
            badge.classList.remove(
                'badge-primary', 
                'badge-success', 
                'badge-info', 
                'badge-warning', 
                'badge-danger',
                'badge-ditugaskan',
                'badge-selesai',
                'badge-pending'
            );
            
            // Apply standard class
            switch(status) {
                case 'ditugaskan':
                    badge.classList.add('badge-ditugaskan');
                    break;
                case 'selesai':
                    badge.classList.add('badge-selesai');
                    break;
                case 'pending':
                    badge.classList.add('badge-pending');
                    break;
                case 'danger':
                case 'critical':
                    badge.classList.add('badge-danger');
                    break;
                default:
                    badge.classList.add('badge-secondary');
            }
        });
    }

    // Create a badge for status
    window.createStatusBadge = function(status) {
        const statusLC = status.toLowerCase();
        let badgeClass = 'badge-secondary';
        
        switch(statusLC) {
            case 'ditugaskan':
                badgeClass = 'badge-ditugaskan';
                break;
            case 'selesai':
                badgeClass = 'badge-selesai';
                break;
            case 'pending':
                badgeClass = 'badge-pending';
                break;
            case 'danger':
            case 'critical':
                badgeClass = 'badge-danger';
                break;
        }
        
        return `<span class="badge ${badgeClass}" data-status="${statusLC}">${status}</span>`;
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