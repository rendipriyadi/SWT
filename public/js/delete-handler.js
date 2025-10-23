$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

// Helper: Get SweetAlert2 theme options based on current mode
function getSwalTheme() {
    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    if (isDark) {
        return {
            background: '#1e1e1e',
            color: '#e0e0e0',
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d'
        };
    }
    return {
        background: '#ffffff',
        color: '#212529',
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d'
    };
}

document.addEventListener('DOMContentLoaded', function() {
    // Delegasi event untuk menangani tombol delete di dalam dropdown
    $(document).on('click', '.delete-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const deleteUrl = $(this).data('delete-url');
        const returnUrl = $(this).data('return-url');
        const id = $(this).data('id');
        
        // Show confirmation dialog using SweetAlert2
        const theme = getSwalTheme();
        Swal.fire({
            title: 'Delete Confirmation',
            text: "Are you sure you want to delete this report?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Delete',
            cancelButtonText: 'Cancel',
            reverseButtons: true,
            ...theme
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading state
                Swal.fire({
                    title: 'Processing...',
                    text: 'Deleting report...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    },
                    ...theme
                });
                
                // Send AJAX DELETE request
                $.ajax({
                    url: deleteUrl,
                    type: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        ref: returnUrl.includes('sejarah') ? 'sejarah' : 'dashboard'
                    },
                    success: function(response) {
                        if (response.success) {
                            // Show success message
                            Swal.fire({
                                title: 'Success!',
                                text: response.message,
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false,
                                ...theme
                            }).then(() => {
                                // Reload DataTable
                                $('.dataTable').DataTable().ajax.reload();
                            });
                        } else {
                            // Handle non-success response
                            Swal.fire({
                                title: 'Error!',
                                text: response.message || 'An error occurred while deleting the report.',
                                icon: 'error',
                                ...theme
                            });
                        }
                    },
                    error: function(xhr) {
                        console.error('Delete error:', xhr);
                        // Show error message with details if available
                        let errorMsg = 'An error occurred while deleting the report.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        
                        Swal.fire({
                            title: 'Error!',
                            text: errorMsg,
                            icon: 'error',
                            ...theme
                        });
                    }
                });
            }
        });
    });
});