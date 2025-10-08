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
          <div class="col-md-6"><div class="fw-semibold">Person in Charge</div><div id="rd_person_in_charge" class="value"></div></div>
          <div class="col-md-6"><div class="fw-semibold">Problem Category</div><div id="rd_category" class="value"></div></div>
          <div class="col-md-6"><div class="fw-semibold">Status</div><div id="rd_status" class="value"></div></div>
          <div class="col-md-6"><div class="fw-semibold">Deadline</div><div id="rd_deadline" class="value"></div></div>
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
/* Column widths mapping (by order):
   1 No, 2 Date, 3 Photo, 4 Area/Station, 5 Problem Category,
   6 Description, 7 Deadline, 8 Status, 9 Completion, 10 Action
*/
/* Date (col 2) and Deadline (col 7) */
#reportsTable th:nth-child(2),
#reportsTable td:nth-child(2) { width: 90px !important; min-width: 90px !important; text-align: center;}
#reportsTable th:nth-child(7),
#reportsTable td:nth-child(7) { width: 110px !important; min-width: 110px !important; text-align: center;}
/* Description (col 6) */
#reportsTable th:nth-child(6),
#reportsTable td:nth-child(6) { width: 200px !important; min-width: 200px !important;}
/* No (col 1) */
#reportsTable th:nth-child(1),
#reportsTable td:nth-child(1) { width: 35px !important; min-width: 35px !important; text-align: center;}
/* Photo (col 3) */
#reportsTable th:nth-child(3),
#reportsTable td:nth-child(3) { width: 80px !important; min-width: 80px !important; text-align: center;}
/* Area/Station (col 4) */
#reportsTable th:nth-child(4),
#reportsTable td:nth-child(4) { width: 100px !important; min-width: 100px !important; text-align: center;}
/* Problem Category (col 5) */
#reportsTable th:nth-child(5),
#reportsTable td:nth-child(5) { width: 80px !important; min-width: 80px !important; text-align: center;}
/* Status (col 8) */
#reportsTable th:nth-child(8),
#reportsTable td:nth-child(8) { width: 100px !important; min-width: 100px !important; text-align: center;}
/* Completion (col 9) */
#reportsTable th:nth-child(9),
#reportsTable td:nth-child(9) { width: 80px !important; min-width: 80px !important; text-align: center;}
/* Action (col 10) */
#reportsTable th:nth-child(10),
#reportsTable td:nth-child(10) { width: 100px !important; min-width: 100px !important; text-align: center;}
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
            {data: 'DT_RowIndex', name: 'DT_RowIndex', title: 'No', orderable: false, searchable: false, className: 'text-center'},
            {data: 'Tanggal', name: 'Tanggal', title: 'Date'},
            {data: 'foto', name: 'foto', title: 'Photo', orderable: false, searchable: false},
            {data: 'departemen', name: 'area.name', title: 'Area/Station'},
            {data: 'problem_category', name: 'problemCategory.name', title: 'Problem Category', orderable: false, searchable: true},
            {data: 'deskripsi_masalah', name: 'deskripsi_masalah', title: 'Description', searchable: true},
            {data: 'tenggat_waktu', name: 'tenggat_waktu', title: 'Deadline'},
            {data: 'status', name: 'status', title: 'Status', orderable: false, searchable: true},
            {data: 'penyelesaian', name: 'penyelesaian', title: 'Completion', orderable: false, searchable: false},
            {data: 'aksi', name: 'aksi', title: 'Action', orderable: false, searchable: false}
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
                $('#rd_person_in_charge').text(data.penanggung_jawab || 'Not assigned');
                $('#rd_category').html(data.problem_category || '');
                $('#rd_status').html(data.status || '');
                $('#rd_deadline').text($(this).find('td:eq(6)').text() || '');
                $('#rd_description').text($(this).find('td:eq(5)').text() || '');
                $('#rowDetailModal').modal('show');
            });
            
            // Handle delete button click
            $(document).off('click', '.delete-btn').on('click', '.delete-btn', function(e) {
                e.preventDefault();
                const deleteUrl = $(this).data('delete-url');
                const reportTitle = $(this).data('title') || 'this report';
                $('#deleteReportTitle').text(reportTitle);
                $('#deleteReportModal').data('delete-url', deleteUrl);
                $('#deleteReportModal').modal('show');
            });
            
            // Handle confirm delete button
            $('#confirmDeleteReport').on('click', function() {
                const deleteUrl = $('#deleteReportModal').data('delete-url');
                const $button = $(this);
                const originalText = $button.html();
                $button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Deleting...');
                $.ajax({
                    url: deleteUrl,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $('#deleteReportModal').modal('hide');
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({ icon: 'success', title: 'Success!', text: response.message || 'Report deleted successfully', timer: 3000, showConfirmButton: false });
                        } else {
                            alert(response.message || 'Report deleted successfully');
                        }
                        if ($.fn.DataTable.isDataTable('#reportsTable')) {
                            $('#reportsTable').DataTable().ajax.reload();
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Failed to delete report';
                        if (xhr.responseJSON && xhr.responseJSON.message) { errorMessage = xhr.responseJSON.message; }
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({ icon: 'error', title: 'Error!', text: errorMessage });
                        } else {
                            alert(errorMessage);
                        }
                    },
                    complete: function() { $button.prop('disabled', false).html(originalText); }
                });
            });
        }
    });
});
</script>
@endpush



