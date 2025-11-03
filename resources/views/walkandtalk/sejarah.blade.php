@extends('layouts.main')

@section('title', 'Report History')

@php
    use App\Models\Area;
    use App\Models\ProblemCategory;
@endphp

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
                <i class="fas fa-redo me-1"></i><span class="btn-text">Reset</span>
            </button>
            <div class="dropdown">
                <button class="btn btn-outline-secondary btn-sm border border-secondary fs-6 mt-4" type="button" id="historyCreatedBtn" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                    <i class="fas fa-calendar-alt me-1"></i><span class="btn-text">Filter</span>
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
            <!-- Desktop Table -->
            <div class="d-none d-md-block">
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

            <!-- Mobile Table -->
            <div class="d-block d-md-none">
                <div class="table-responsive">
                    <table id="sejarahTableMobile" class="table table-bordered small mb-0" data-url="{{ route('sejarah.datatables') }}">
                        <thead>
                            <tr>
                                <th style="width: 60px;">No</th>
                                <th style="width: 35%;">Date</th>
                                <th style="width: auto;">Area</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Preview Foto Full -->
<div class="modal fade" id="modalFotoFull" tabindex="-1" aria-labelledby="modalFotoFullLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content bg-transparent border-0">
      <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close" style="z-index: 1060; opacity: 1;"></button>
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
#rowDetailModal .fw-semibold { font-size: 0.95rem; color: #6c757d; }
#rowDetailModal .value { font-size: 1.05rem; color: var(--text-primary); }

/* Dark mode styling for modal */
:root[data-theme="dark"] #rowDetailModal .modal-header {
  background-color: var(--bg-surface) !important;
  border-bottom-color: var(--border-color) !important;
}
:root[data-theme="dark"] #rowDetailModal .modal-footer {
  background-color: var(--bg-surface) !important;
  border-top-color: var(--border-color) !important;
}
:root[data-theme="dark"] #rowDetailModal .modal-title {
  color: #e9ecef !important;
}
:root[data-theme="dark"] #rowDetailModal .modal-title .text-primary {
  color: #6ea8fe !important;
}
:root[data-theme="dark"] #rowDetailModal .fw-semibold {
  color: #adb5bd !important;
}
:root[data-theme="dark"] #rowDetailModal .value {
  color: #e9ecef !important;
}

/* Remove inner border on scroll wrapper when placed inside a card to maximize usable width */
.card .table-scroll-x { border: 0 !important; border-radius: 0 !important; }

/* Better table layout and column widths */
#sejarahTable {
  table-layout: fixed !important;
  width: 100% !important;
}

/* Column widths - fixed layout for consistent sizing */
#sejarahTable th:nth-child(1), #sejarahTable td:nth-child(1) { width: 60px !important; } /* No */
#sejarahTable th:nth-child(2), #sejarahTable td:nth-child(2) { width: 120px !important; } /* Date */
#sejarahTable th:nth-child(3), #sejarahTable td:nth-child(3) { width: 100px !important; } /* Photo */
#sejarahTable th:nth-child(4), #sejarahTable td:nth-child(4) { width: 150px !important; } /* Area */
#sejarahTable th:nth-child(5), #sejarahTable td:nth-child(5) { width: 180px !important; } /* Category */
#sejarahTable th:nth-child(6), #sejarahTable td:nth-child(6) { width: 200px !important; } /* Description */
#sejarahTable th:nth-child(7), #sejarahTable td:nth-child(7) { width: 120px !important; } /* Deadline */
#sejarahTable th:nth-child(8), #sejarahTable td:nth-child(8) { width: 120px !important; } /* Status */
#sejarahTable th:nth-child(9), #sejarahTable td:nth-child(9) { width: 120px !important; } /* Completion */
#sejarahTable th:nth-child(10), #sejarahTable td:nth-child(10) { width: 100px !important; } /* Action */

/* Text wrapping and alignment for all cells */
#sejarahTable td, #sejarahTable th {
  white-space: normal !important;
  word-wrap: break-word !important;
  word-break: break-word !important;
  overflow-wrap: break-word !important;
  vertical-align: middle !important;
  text-align: center !important;
  padding: 0.75rem 0.5rem !important;
  line-height: 1.4 !important;
}

/* Header styling */
#sejarahTable th {
  background-color: #f8f9fa !important;
  font-weight: 600 !important;
  font-size: 0.875rem !important;
  white-space: nowrap !important;
}

/* Row styling */
#sejarahTable tbody tr {
  height: auto !important;
  min-height: 60px !important;
}

/* Specific column alignments */
#sejarahTable td:nth-child(1) { text-align: center !important; } /* No - center */
#sejarahTable td:nth-child(6),
#sejarahTable tbody td:nth-child(6),
table#sejarahTable tbody td:nth-child(6) { 
    text-align: center !important; 
    padding-left: 0.5rem !important;
    padding-right: 0.5rem !important;
} /* Description - center align */

/* Mobile table styles */
.mobile-table-row {
  cursor: pointer;
}
.mobile-table-row:hover {
  background-color: rgba(13,110,253,0.08);
}

/* Mobile table layout */
#sejarahTableMobile {
  table-layout: fixed !important;
  width: 100% !important;
}
#sejarahTableMobile td {
  white-space: normal !important;
  word-wrap: break-word !important;
  word-break: break-word !important;
  overflow-wrap: break-word !important;
}
#sejarahTableMobile th {
  white-space: nowrap;
}

/* Specific mobile column widths */
#sejarahTableMobile td:nth-child(1) {
  width: 60px !important;
}
#sejarahTableMobile td:nth-child(2) {
  width: 35% !important;
}
#sejarahTableMobile td:nth-child(3) {
  width: auto !important;
}

.mobile-arrow {
  transition: transform 0.3s ease;
  font-size: 0.75rem;
  display: inline-block;
  transform: rotate(0deg);
}
.mobile-arrow.rotate-180 {
  transform: rotate(180deg) !important;
}

.mobile-details {
  background-color: #f8f9fa;
  border-top: 1px solid #dee2e6;
  text-align: center;
  padding: 1rem;
}
.mobile-details > div {
  padding: 0.75rem 0;
  border-bottom: 1px solid #e9ecef;
}
.mobile-details > div:last-child {
  border-bottom: none;
  padding-bottom: 0;
}
.mobile-details strong {
  display: block;
  color: #6c757d;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 0.35rem;
}
.mobile-details span {
  display: block;
  color: #212529;
  font-size: 0.9rem;
  font-weight: 500;
}

/* Force detail rows to be hidden by default */
#sejarahTableMobile tbody tr.detail-row {
  display: none !important;
}
#sejarahTableMobile tbody tr.detail-row.show {
  display: table-row !important;
}

:root[data-theme="dark"] .mobile-details {
  background-color: var(--bg-surface);
  border-top-color: var(--border-color);
}
:root[data-theme="dark"] .mobile-details > div {
  border-bottom-color: var(--border-color);
}
:root[data-theme="dark"] .mobile-details strong {
  color: #adb5bd;
}
:root[data-theme="dark"] .mobile-details span {
  color: #e9ecef;
}

.mobile-action-buttons {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
  justify-content: center;
  margin-top: 0.5rem;
}
.mobile-action-btn {
  flex: 0 1 auto;
}

/* Force hide desktop table on mobile */
@media (max-width: 767.98px) {
  #sejarahTable_wrapper {
    display: none !important;
  }
  .d-none.d-md-block {
    display: none !important;
  }
}

/* Force hide mobile table on desktop */
@media (min-width: 768px) {
  #sejarahTableMobile_wrapper {
    display: none !important;
  }
  .d-block.d-md-none {
    display: none !important;
  }
}

/* Hide button text on mobile - show icons only */
@media (max-width: 767.98px) {
  #historyResetExternal .btn-text,
  #historyCreatedBtn .btn-text {
    display: none !important;
  }
  #historyResetExternal {
    padding: 0.375rem 0.75rem;
  }
  #historyCreatedBtn {
    padding: 0.375rem 0.75rem;
  }
  #historyResetExternal i,
  #historyCreatedBtn i {
    margin-right: 0 !important;
  }
}
</style>
<script>
// DataTables initialization for #sejarahTable handled globally in public/js/datatables-init.js

// Note: Completion view button handler is now centralized in modal-handlers.js
// No need for duplicate handler here - it will use window.routes.penyelesaian

// Photo preview handler is also centralized in modal-handlers.js

// Row click functionality for history table
$(document).ready(function() {
    // Add clickable class to table rows after DataTables is initialized
    $('#sejarahTable').on('draw.dt', function() {
        $(this).find('tbody tr').addClass('clickable-row');
    });
    
    // Handle row click to redirect to detail page (ignore clicks on buttons/links/photo modal triggers)
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
        
        if (!data || !data.encrypted_id) {
            return;
        }
        
        // Redirect to detail page using route
        const detailUrl = window.routes && window.routes.laporanShow 
            ? window.routes.laporanShow.replace(':id', data.encrypted_id)
            : '/laporan/' + data.encrypted_id;
        window.location.href = detailUrl;
        return;
        
        // OLD: Populate modal with row data (disabled)
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

// Mobile collapse handler for History - Manual toggle
$(document).on('click', '#sejarahTableMobile .mobile-table-row', function(e) {
    // Don't trigger if clicking on action buttons
    if ($(e.target).closest('a, button').length > 0) return;
    
    e.preventDefault();
    e.stopPropagation();
    
    const $clickedRow = $(this);
    const targetId = $clickedRow.attr('data-bs-target');
    const $detailRow = $(targetId);
    
    // Check if detail row is currently visible
    const isCurrentlyExpanded = $detailRow.hasClass('show') && $detailRow.css('display') === 'table-row';
    
    // First, close ALL rows
    $('#sejarahTableMobile .mobile-table-row').each(function() {
        const $row = $(this);
        const rowTargetId = $row.attr('data-bs-target');
        const $rowDetail = $(rowTargetId);
        
        $rowDetail.removeClass('show').css('display', 'none');
        $row.find('.mobile-arrow').removeClass('rotate-180');
    });
    
    // Then, if the clicked row was collapsed, expand it
    if (!isCurrentlyExpanded) {
        $detailRow.addClass('show').css('display', 'table-row');
        $clickedRow.find('.mobile-arrow').addClass('rotate-180');
    }
});

// Data for filter dropdowns
window.areasData = @json(Area::all(['id', 'name']));
window.categoriesData = @json(ProblemCategory::active()->ordered()->get(['id', 'name']));
</script>
@endpush


