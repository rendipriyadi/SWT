@extends('layouts.main')

@section('title', 'Report')

@section('content')
<div class="container-fluid px-4">
    <div class="page-header mb-3 d-flex align-items-center justify-content-between">
        <div>
            <h3 class="mb-1">Reports In Progress</h3>
            <p class="text-muted mb-0">Open and assigned reports</p>
        </div>
        <a href="{{ route('laporan.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Create Report
        </a>
    </div>

    <div class="card p-3">
        <div class="table-responsive">
            <table id="reportsTable" class="table table-bordered table-striped table-hover w-100 small" data-url="{{ route('dashboard.datatables') }}">
            </table>
        </div>
    </div>

    <!-- Modals needed by table renderers -->
    <div class="modal-container">
        <!-- Modal Preview Foto Full -->
        <div class="modal fade" id="modalFotoFull" tabindex="-1" aria-labelledby="modalFotoFullLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bg-transparent border-0">
              <div class="modal-body text-center p-0">
                <div id="photoCarousel" class="carousel slide">
                  <div class="carousel-inner"></div>
                  <button class="carousel-control-prev" type="button" data-bs-target="#photoCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                  </button>
                  <button class="carousel-control-next" type="button" data-bs-target="#photoCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Modal Row Detail -->
        <div class="modal fade" id="rowDetailModal" tabindex="-1" aria-labelledby="rowDetailModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="rowDetailModalLabel">
                  <i class="fas fa-info-circle me-2 text-primary"></i>Report Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                <div class="row g-3 small">
                  <div class="col-md-6"><div class="fw-semibold">Date</div><div id="rd_date" class="value"></div></div>
                  <div class="col-md-6"><div class="fw-semibold">Area/Station</div><div id="rd_area" class="value"></div></div>
                  <div class="col-md-6"><div class="fw-semibold">Problem Category</div><div id="rd_category" class="value"></div></div>
                  <div class="col-md-6"><div class="fw-semibold">Status</div><div id="rd_status" class="value"></div></div>
                  <div class="col-12"><div class="fw-semibold">Description</div><div id="rd_description" class="text-pre-wrap value"></div></div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              </div>
            </div>
          </div>
        </div>

        <!-- Modal Penyelesaian -->
        <div class="modal fade settlement-modal" id="modalPenyelesaian" tabindex="-1" aria-labelledby="modalPenyelesaianLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header py-2">
                        <h5 class="modal-title fs-6" id="modalPenyelesaianLabel">Settlement Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body small" id="modalPenyelesaianBody"></div>
                    <div class="modal-footer py-1">
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteReportModal" tabindex="-1" aria-labelledby="deleteReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title text-danger" id="deleteReportModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>Delete Report
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="fas fa-trash-alt text-danger fa-3x mb-3"></i>
                </div>
                <p class="text-center mb-3">Are you sure you want to delete this report?</p>
                <p class="text-danger text-center small mb-0">
                    <i class="fas fa-exclamation-circle me-1"></i>
                    This action is permanent and cannot be reversed
                </p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancel
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteReport">
                    <i class="fas fa-trash me-1"></i>Delete Report
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<style>
/* Make table rows look clickable and add hover feedback */
#reportsTable tbody tr.clickable-row { cursor: pointer; }
#reportsTable tbody tr.clickable-row:hover { background-color: rgba(13,110,253,0.08); }
/* Bigger typography for Report Details modal */
#rowDetailModal .modal-dialog { max-width: 900px; }
#rowDetailModal .modal-body { font-size: 1rem; }
#rowDetailModal .modal-title { font-size: 1.25rem; }
#rowDetailModal .fw-semibold { font-size: 0.95rem; color: var(--text-secondary); }
#rowDetailModal .value { font-size: 1.05rem; color: var(--text-primary); }
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tableEl = document.getElementById('reportsTable');
    if (!tableEl) return;
    const ajaxUrl = tableEl.getAttribute('data-url');

    const table = $('#reportsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: ajaxUrl,
        responsive: true,
        autoWidth: false,
        scrollX: false,
        initComplete: function() {
            try {
                var api = this.api();
                var lastIdx = api.columns().indexes().toArray().slice(-1)[0];
                var hdr = api.column(lastIdx).header();
                if (hdr && $.trim($(hdr).text()) === '') {
                    $(hdr).text('Action');
                }
            } catch(e) {}
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', title: 'No', orderable: false, searchable: false, width: '60px', className: 'text-center'},
            {data: 'Tanggal', name: 'Tanggal', title: 'Date', width: '140px'},
            {data: 'foto', name: 'foto', title: 'Photo', orderable: false, searchable: false, width: '80px'},
            {data: 'departemen', name: 'area.name', title: 'Area/Station', width: '160px'},
            {data: 'problem_category', name: 'problemCategory.name', title: 'Problem Category', orderable: false, searchable: true, width: '160px'},
            {data: 'deskripsi_masalah', name: 'deskripsi_masalah', title: 'Description', searchable: true, width: '400px'},
            {data: 'tenggat_waktu', name: 'tenggat_waktu', title: 'Deadline', width: '140px'},
            {data: 'status', name: 'status', title: 'Status', orderable: false, searchable: true, width: '120px'},
            {data: 'penyelesaian', name: 'penyelesaian', title: 'Completion', orderable: false, searchable: false, width: '90px'},
            {data: 'aksi', name: 'aksi', title: 'Action', orderable: false, searchable: false, width: '100px'}
        ],
        columnDefs: [
            { targets: [0,2,8,9], orderable: false },
            { targets: '_all', createdCell: function(td){ td.style.whiteSpace='normal'; td.style.wordBreak='break-word'; } }
        ],
        order: [[1, 'desc']],
        createdRow: function(row, data) {
            $(row).addClass('clickable-row');
        },
        language: {
            processing: "Loading...",
            search: "Search:",
            lengthMenu: "Show _MENU_",
            info: " _START_ / _TOTAL_ ",
            infoEmpty: "No data to display",
            infoFiltered: "(filtered from _MAX_ total records)",
            zeroRecords: "No data found",
            paginate: {
                first: "First",
                last: "Last",
                next: "»",
                previous: "«"
            }
        },
        dom: '<"row mb-3"<"col-md-6"l><"col-md-6 text-end"f>>rtip',
        drawCallback: function() {
            // Hook up description modal
            // Hook up photo preview
            $(document).off('click', 'img[data-photos]').on('click', 'img[data-photos]', function(){
                const photos = JSON.parse($(this).attr('data-photos') || '[]');
                const inner = $('#photoCarousel .carousel-inner');
                inner.empty();
                photos.forEach((url, idx) => {
                    inner.append('<div class="carousel-item '+(idx===0?'active':'')+'"><img src="'+url+'" class="d-block w-100" style="max-height:70vh; object-fit:contain;"></div>');
                });
            });

            // Row click open detail modal (ignore clicks on buttons/links)
            $('#reportsTable tbody').off('click', 'tr').on('click', 'tr', function(e){
                if ($(e.target).closest('a,button,.btn,input,label,select').length) return;
                const dt = $('#reportsTable').DataTable();
                const data = dt.row(this).data();
                if (!data) return;
                $('#rd_date').text($(this).find('td:eq(1)').text() || '');
                $('#rd_area').html(data.departemen || '');
                $('#rd_category').html(data.problem_category || '');
                $('#rd_status').html(data.status || '');
                $('#rd_description').text($(this).find('td:eq(5)').text() || '');
                $('#rowDetailModal').modal('show');
            });
            
            // Handle delete button click
            $(document).off('click', '.delete-btn').on('click', '.delete-btn', function(e) {
                e.preventDefault();
                
                const deleteUrl = $(this).data('delete-url');
                const reportId = $(this).data('id');
                const reportTitle = $(this).data('title') || 'this report';
                
                // Set modal content and store delete URL
                $('#deleteReportTitle').text(reportTitle);
                $('#deleteReportModal').data('delete-url', deleteUrl);
                $('#deleteReportModal').modal('show');
            });
            
            // Handle confirm delete button
            $('#confirmDeleteReport').on('click', function() {
                const deleteUrl = $('#deleteReportModal').data('delete-url');
                const $button = $(this);
                const originalText = $button.html();
                
                // Disable button and show loading
                $button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Deleting...');
                
                // Send AJAX delete request
                $.ajax({
                    url: deleteUrl,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        // Close modal
                        $('#deleteReportModal').modal('hide');
                        
                        // Show success message
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message || 'Report deleted successfully',
                                timer: 3000,
                                showConfirmButton: false
                            });
                        } else {
                            alert(response.message || 'Report deleted successfully');
                        }
                        
                        // Refresh table
                        if ($.fn.DataTable.isDataTable('#reportsTable')) {
                            $('#reportsTable').DataTable().ajax.reload();
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Failed to delete report';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: errorMessage
                            });
                        } else {
                            alert(errorMessage);
                        }
                    },
                    complete: function() {
                        // Re-enable button
                        $button.prop('disabled', false).html(originalText);
                    }
                });
            });
            
            // Update filter chips
            updateFilterChips();
        }
    });

    // Month filter functionality
    $('#monthFilter').on('change', function() {
        const monthFilter = $(this).val();
        refreshTableWithMonthFilter(monthFilter);
    });

    // Clear month filter
    $('#clearMonthFilter').on('click', function() {
        $('#monthFilter').val('').trigger('change');
    });

    // Function to refresh table with month filter
    function refreshTableWithMonthFilter(monthFilter) {
        if ($.fn.DataTable.isDataTable('#reportsTable')) {
            $('#reportsTable').DataTable().destroy();
        }
        
        let ajaxUrl = originalUrl;
        if (monthFilter) {
            ajaxUrl = `${originalUrl}?month_filter=${encodeURIComponent(monthFilter)}`;
        }
        
        // Recreate table with new URL
        const table = $('#reportsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: ajaxUrl,
            responsive: true,
            autoWidth: false,
            scrollX: false,
            initComplete: function() {
                try {
                    var api = this.api();
                    var lastIdx = api.columns().indexes().toArray().slice(-1)[0];
                    var hdr = api.column(lastIdx).header();
                    if (hdr && $.trim($(hdr).text()) === '') {
                        $(hdr).text('Action');
                    }
                } catch(e) {}
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', title: 'No', orderable: false, searchable: false, width: '60px', className: 'text-center'},
                {data: 'Tanggal', name: 'Tanggal', title: 'Date', width: '140px'},
                {data: 'foto', name: 'foto', title: 'Photo', orderable: false, searchable: false, width: '80px'},
                {data: 'departemen', name: 'area.name', title: 'Area/Station', width: '160px'},
                {data: 'problem_category', name: 'problemCategory.name', title: 'Problem Category', orderable: false, searchable: true, width: '160px'},
                {data: 'deskripsi_masalah', name: 'deskripsi_masalah', title: 'Description', searchable: true, width: '400px'},
                {data: 'tenggat_waktu', name: 'tenggat_waktu', title: 'Deadline', width: '140px'},
                {data: 'status', name: 'status', title: 'Status', orderable: false, searchable: true, width: '120px'},
                {data: 'penyelesaian', name: 'penyelesaian', title: 'Completion', orderable: false, searchable: false, width: '90px'},
                {data: 'aksi', name: 'aksi', title: 'Action', orderable: false, searchable: false, width: '100px'}
            ],
            columnDefs: [
                { targets: [0,2,8,9], orderable: false },
                { targets: '_all', createdCell: function(td){ td.style.whiteSpace='normal'; td.style.wordBreak='break-word'; } }
            ],
            order: [[1, 'desc']],
            language: {
                processing: "Loading...",
                search: "Search:",
                lengthMenu: "Show _MENU_",
                info: " _START_ / _TOTAL_ ",
                infoEmpty: "No data to display",
                infoFiltered: "(filtered from _MAX_ total records)",
                zeroRecords: "No data found",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "»",
                    previous: "«"
                }
            },
            dom: '<"row mb-3"<"col-md-6"l><"col-md-6 text-end"f>>rtip',
            drawCallback: function() {
                // Hook up description modal
                $(document).off('click', '.view-description').on('click', '.view-description', function(){
                    const full = $(this).data('description') || '';
                    if (full) {
                        const formattedDescription = full
                            .replace(/\n/g, '<br>')
                            .replace(/\r/g, '')
                            .replace(/  /g, '&nbsp;&nbsp;');
                        
                        $('#descriptionModalBody').html(`
                            <div class="description-content">
                                <div class="description-text">${formattedDescription}</div>
                            </div>
                        `);
                    } else {
                        $('#descriptionModalBody').html('<div class="text-muted">No description available</div>');
                    }
                });

                // Hook up photo preview
                $(document).off('click', 'img[data-photos]').on('click', 'img[data-photos]', function(){
                    const photos = JSON.parse($(this).attr('data-photos') || '[]');
                    const inner = $('#photoCarousel .carousel-inner');
                    inner.empty();
                    photos.forEach((url, idx) => {
                        inner.append('<div class="carousel-item '+(idx===0?'active':'')+'"><img src="'+url+'" class="d-block w-100" style="max-height:70vh; object-fit:contain;"></div>');
                    });
                });
                
                // Update filter chips
                updateFilterChips();
            }
        });
    }

    // Function to update filter chips
    function updateFilterChips() {
        const monthFilter = $('#monthFilter').val();
        const chips = [];
        
        if (monthFilter) {
            let chipText = '';
            switch (monthFilter) {
                case 'this-month':
                    chipText = 'This Month';
                    break;
                case 'last-month':
                    chipText = 'Last Month';
                    break;
                case 'last-3-months':
                    chipText = 'Last 3 Months';
                    break;
                case 'last-6-months':
                    chipText = 'Last 6 Months';
                    break;
                case 'this-year':
                    chipText = 'This Year';
                    break;
                default:
                    if (monthFilter.match(/^\d{4}-\d{2}$/)) {
                        const [year, month] = monthFilter.split('-');
                        const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                                         'July', 'August', 'September', 'October', 'November', 'December'];
                        chipText = `${monthNames[parseInt(month) - 1]} ${year}`;
                    }
                    break;
            }
            
            if (chipText) {
                chips.push(`<span class="badge bg-primary me-1">Month: ${chipText} <span class="ms-1" style="cursor: pointer;" onclick="$('#monthFilter').val('').trigger('change');">&times;</span></span>`);
            }
        }
        
        $('#activeFilterChips').html(chips.join(''));
        
        // Show/hide clear button
        if (monthFilter) {
            $('#clearMonthFilter').show();
        } else {
            $('#clearMonthFilter').hide();
        }
    }

});
</script>
@endpush


