@extends('layouts.main')

@section('title', 'Report History')

@section('content')
<div class="container-fluid px-4">
    <div class="page-header mb-3 d-flex align-items-center justify-content-between">
        <div>
            <h3 class="mb-1">Report History</h3>
            <p class="text-muted mb-0">Completed and archived reports</p>
        </div>
        <a href="{{ route('sejarah.download') }}" id="historyExportPdf" class="btn btn-primary">
            <i class="fas fa-file-pdf me-2"></i>Export PDF
            </a>
    </div>

    <div class="card">
        <div class="card-body">
        <!-- History date filter dropdown (visible, right-aligned) -->
        <div id="historyControlsContainer" class="d-flex justify-content-end align-items-center gap-2 mb-2">
            <button type="button" id="historyResetExternal" class="btn btn-outline-secondary btn-sm d-none border border-secondary fs-6 mt-4">
                <i class="fas fa-redo me-1"></i>Reset
            </button>
            <div class="dropdown">
                <button class="btn btn-outline-secondary btn-sm border border-secondary fs-6 mt-4" type="button" id="historyCreatedBtn" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                    <i class="fas fa-calendar-alt me-1"></i>Filter
                </button>
                <div class="dropdown-menu dropdown-menu-end p-3" aria-labelledby="historyCreatedBtn" style="min-width: 420px;" onclick="event.stopPropagation();">
                    <h6 class="dropdown-header px-0">Filter by Created Date</h6>
                    <div class="row g-2 mb-2">
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-bold" for="history_created_start">Start</label>
                            <input type="text" id="history_created_start" class="form-control form-control-sm" placeholder="Start date" />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-bold" for="history_created_end">End</label>
                            <input type="text" id="history_created_end" class="form-control form-control-sm" placeholder="End date" />
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-primary btn-sm" id="history_created_apply">
                            <i class="fas fa-filter me-1"></i>Apply
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="p-0">
            <div class="table-responsive">
                <table id="sejarahTable" class="table table-bordered table-striped table-hover small mb-0" data-url="{{ route('sejarah.datatables') }}">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Date</th>
                            <th>Photo</th>
                            <th>Area/Station</th>
                            <th>Problem Category</th>
                            <th>Description</th>
                            <th>Deadline</th>
                            <th>Status</th>
                            <th>Completion</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
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
@endsection

@push('scripts')
<!-- Flatpickr for consistent datepicker UI and disabled dates -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js"></script>
<style>
/* Ensure flatpickr overlays above dropdown and table */
.flatpickr-calendar { z-index: 2005 !important; }
/* Lebar kolom dipindahkan ke columnDefs DataTables untuk sinkron thead/td */

/* Make table rows look clickable and add hover feedback */
#sejarahTable tbody tr.clickable-row { cursor: pointer; }
#sejarahTable tbody tr.clickable-row:hover { background-color: rgba(13,110,253,0.08); }

/* Bigger typography for Report Details modal */
#rowDetailModal .modal-dialog { max-width: 900px; }
#rowDetailModal .modal-body { font-size: 1rem; }
#rowDetailModal .modal-title { font-size: 1.25rem; }
#rowDetailModal .fw-semibold { font-size: 0.95rem; color: var(--text-secondary); }
#rowDetailModal .value { font-size: 1.05rem; color: var(--text-primary); }

/* Remove inner border on scroll wrapper when placed inside a card to maximize usable width */
.card .table-scroll-x { border: 0 !important; border-radius: 0 !important; }

/* Date column specific styling - allow text wrapping and proper spacing */
#sejarahTable tbody td:nth-child(2) {
  white-space: normal !important;
  word-wrap: break-word !important;
  word-break: break-word !important;
  line-height: 1.3 !important;
  vertical-align: middle !important;
  padding: 0.75rem 0.5rem !important;
  min-height: 50px !important;
}

/* Override any conflicting styles for date column */
#sejarahTable tbody td:nth-child(2).wrap-cell {
  white-space: normal !important;
  word-break: break-word !important;
  height: auto !important;
}

/* Ensure table rows have consistent height when content wraps */
#sejarahTable tbody tr {
  height: auto !important;
  min-height: 60px;
}

/* Better text wrapping for all table cells */
#sejarahTable td {
  word-wrap: break-word;
  overflow-wrap: break-word;
}
</style>
<script>
// DataTables initialization for #sejarahTable handled globally in public/js/datatables-init.js

// Handle completion view button clicks
$(document).on('click', '.lihat-penyelesaian-btn', function() {
    const reportId = $(this).data('id');
    
    $.ajax({
        url: `/laporan/penyelesaian/${reportId}`,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                let modalBody = $('#modalPenyelesaianBody');
                modalBody.empty();
                
                // Add completion date
                modalBody.append(`
                    <div class="mb-3">
                        <strong>Completion Date:</strong><br>
                        <span class="text-muted">${response.Tanggal}</span>
                    </div>
                `);
                
                // Add completion photos if any
                if (response.Foto && response.Foto.length > 0) {
                    modalBody.append('<div class="mb-3"><strong>Completion Photos:</strong><br><div class="d-flex flex-wrap gap-2">');
                    response.Foto.forEach((photoUrl, index) => {
                        modalBody.append(`
                            <img src="${photoUrl}" alt="Completion Photo ${index + 1}" 
                                 class="img-thumbnail" 
                                 style="width: 100px !important; height: 100px !important; object-fit: cover; cursor: pointer;"
                                 data-bs-toggle="modal" data-bs-target="#modalFotoFull" 
                                 data-photos='${JSON.stringify(response.Foto)}'>
                        `);
                    });
                    modalBody.append('</div></div>');
                }
                
                // Add completion description
                modalBody.append(`
                    <div class="mb-3">
                        <strong>Completion Description:</strong><br>
                        <div class="text-muted" style="white-space: pre-wrap;">${response.deskripsi_penyelesaian}</div>
                    </div>
                `);
                
                $('#modalPenyelesaian').modal('show');
                        } else {
                alert('No completion details found for this report.');
            }
        },
        error: function() {
            alert('Error loading completion details.');
        }
    });
});

// Handle photo preview in completion modal
$(document).on('click', '#modalPenyelesaian img[data-photos]', function() {
    const photos = JSON.parse($(this).attr('data-photos') || '[]');
    const inner = $('#photoCarousel .carousel-inner');
    inner.empty();
    photos.forEach((url, idx) => {
        inner.append('<div class="carousel-item '+(idx===0?'active':'')+'"><img src="'+url+'" class="d-block w-100" style="max-height:70vh; object-fit:contain;"></div>');
            });
        });

// Row click functionality for history table
$(document).ready(function() {
    // Add clickable class to table rows after DataTables is initialized
    $('#sejarahTable').on('draw.dt', function() {
        $(this).find('tbody tr').addClass('clickable-row');
    });
    
    // Handle row click to open detail modal (ignore clicks on buttons/links/photo modal triggers)
    $('#sejarahTable tbody').on('click', 'tr', function(e) {
        if (
            $(e.target).closest('a,button,.btn,input,label,select').length ||
            $(e.target).closest('[data-bs-toggle="modal"]').length
        ) return;

        // Ignore clicks on the Photo column (index 2)
        const td = $(e.target).closest('td');
        if (td.length && td.index() === 2) return;
        
        const dt = $('#sejarahTable').DataTable();
        const data = dt.row(this).data();
        if (!data) return;
        
        // Populate modal with row data
        $('#rd_date').text($(this).find('td:eq(1)').text() || '');
        $('#rd_area').html(data.departemen || '');
        $('#rd_person_in_charge').text(data.person_in_charge || 'Not assigned');
        $('#rd_category').html(data.problem_category || '');
        $('#rd_status').html(data.status || '');
        $('#rd_deadline').text($(this).find('td:eq(6)').text() || '');
        // Gunakan field full description dari server jika tersedia
        const fullDesc = (data && (data.deskripsi_masalah_full || data.deskripsi_masalah))
            ? $('<div>').html(String(data.deskripsi_masalah_full || data.deskripsi_masalah)).text()
            : ($(this).find('td:eq(5)').text() || '');
        $('#rd_description').text(fullDesc);
        
        $('#rowDetailModal').modal('show');
    });
    
});

// Data for filter dropdowns
window.areasData = @json(\App\Models\Area::all(['id', 'name']));
window.categoriesData = @json(\App\Models\ProblemCategory::active()->ordered()->get(['id', 'name']));
</script>
@endpush


