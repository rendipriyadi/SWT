$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

document.addEventListener('DOMContentLoaded', function() {
    // Delegasi event untuk menangani tombol delete di dalam dropdown
    $(document).on('click', '.dropdown-item.delete-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const deleteUrl = $(this).data('delete-url');
        const returnUrl = $(this).data('return-url');
        const id = $(this).data('id');
        
        console.log('Delete requested for ID:', id);
        console.log('Delete URL:', deleteUrl);
        
        // Show confirmation dialog using SweetAlert2
        Swal.fire({
             title: 'Delete Confirmation',
            text: "Are you sure you want to delete this report?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, Delete',
            cancelButtonText: 'Cancel',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading state
                Swal.fire({
                    title: 'Processing...',
                    text: 'Deleting report...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
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
                        console.log('Delete response:', response);
                        if (response.success) {
                            // Show success message
                            Swal.fire({
                                title: 'Success!',
                                text: response.message,
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                // Reload DataTable
                                $('.dataTable').DataTable().ajax.reload();
                            });
                        } else {
                            // Handle non-success response
                            Swal.fire(
                                'Error!',
                                response.message || 'An error occurred while deleting the report.',
                                'error'
                            );
                        }
                    },
                    error: function(xhr) {
                        console.error('Delete error:', xhr);
                        // Show error message with details if available
                        let errorMsg = 'An error occurred while deleting the report.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        
                        Swal.fire(
                            'Error!',
                            errorMsg,
                            'error'
                        );
                    }
                });
            }
        });
    });
});