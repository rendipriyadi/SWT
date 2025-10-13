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

    

    <div class="card">
        <div class="card-body">
        <!-- Creation date filter dropdown (visible, right-aligned) -->
        <div id="reportsCreatedDropdownContainer" class="d-flex justify-content-end align-items-center gap-2 mb-2">
            <button type="button" id="reportsCreatedResetExternal" class="btn btn-outline-secondary btn-sm d-none border border-secondary fs-6">
                <i class="fas fa-redo me-1"></i><span class="btn-text">Reset</span>
            </button>
            <div class="dropdown">
                <button class="btn btn-outline-secondary btn-sm border border-secondary fs-6" type="button" id="reportsCreatedBtn" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                    <i class="fas fa-calendar-alt me-1"></i><span class="btn-text">Filter</span>
                </button>
                <div class="dropdown-menu dropdown-menu-end p-3 date-filter-dropdown" aria-labelledby="reportsCreatedBtn" onclick="event.stopPropagation();">
                    <h6 class="dropdown-header px-0 mb-2">Filter by Created Date</h6>
                    <div class="mb-2">
                        <label class="form-label small fw-bold mb-1" for="report_created_start">Start</label>
                        <input type="text" id="report_created_start" class="form-control form-control-sm" placeholder="Start date" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold mb-1" for="report_created_end">End</label>
                        <input type="text" id="report_created_end" class="form-control form-control-sm" placeholder="End date" />
                    </div>
                    <div class="d-grid">
                        <button type="button" class="btn btn-primary btn-sm" id="report_created_apply">
                            <i class="fas fa-filter me-1"></i>Apply
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Desktop Table -->
        <div class="table-responsive d-none d-md-block">
            <table id="laporanTable" class="table table-bordered table-striped table-hover small mb-0" data-url="{{ route('dashboard.datatables') }}">
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

        <!-- Mobile Table -->
        <div class="d-block d-md-none">
            <div class="table-responsive">
                <table id="laporanTableMobile" class="table table-bordered small mb-0" data-url="{{ route('dashboard.datatables') }}">
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
<!-- Flatpickr for consistent datepicker UI (same as History) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js"></script>
<style>
/* Ensure flatpickr overlays above dropdown and table */
.flatpickr-calendar { z-index: 2005 !important; }
/* Lebar kolom dipindahkan ke columnDefs DataTables untuk sinkron thead/td */

/* Make table rows look clickable and add hover feedback */
#laporanTable tbody tr.clickable-row { cursor: pointer; }
#laporanTable tbody tr.clickable-row:hover { background-color: rgba(13,110,253,0.08); }

/* Bigger typography for Report Details modal */
#rowDetailModal .modal-dialog { max-width: 900px; }
#rowDetailModal .modal-body { font-size: 1rem; }
#rowDetailModal .modal-title { font-size: 1.25rem; }
#rowDetailModal .fw-semibold { color: #6c757d; }
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

/* Date column specific styling - allow text wrapping and proper spacing */
#laporanTable tbody td:nth-child(2) {
  white-space: normal !important;
  word-wrap: break-word !important;
  word-break: break-word !important;
  line-height: 1.3 !important;
  vertical-align: middle !important;
  padding: 0.75rem 0.5rem !important;
  min-height: 50px !important;
}

/* Mobile table styles */
.mobile-table-row {
  cursor: pointer;
}
.mobile-table-row:hover {
  background-color: rgba(13,110,253,0.08);
}
/* Allow text wrapping in mobile table cells */
#laporanTableMobile {
  table-layout: fixed !important;
  width: 100% !important;
}
#laporanTableMobile td {
  white-space: normal !important;
  word-wrap: break-word !important;
  word-break: break-word !important;
  overflow-wrap: break-word !important;
}
#laporanTableMobile th {
  white-space: nowrap;
}
/* Specific column widths */
#laporanTableMobile td:nth-child(1) {
  width: 60px !important;
}
#laporanTableMobile td:nth-child(2) {
  width: 35% !important;
}
#laporanTableMobile td:nth-child(3) {
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
/* Force detail rows to be hidden by default - override Bootstrap collapse */
#laporanTableMobile tbody tr.detail-row {
  display: none !important;
}
#laporanTableMobile tbody tr.detail-row.show {
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

/* Date filter dropdown responsive width */
.date-filter-dropdown {
  min-width: 420px;
}

/* Force hide desktop table on mobile */
@media (max-width: 767.98px) {
  #laporanTable_wrapper {
    display: none !important;
  }
  .d-none.d-md-block {
    display: none !important;
  }
}

/* Force hide mobile table on desktop */
@media (min-width: 768px) {
  #laporanTableMobile_wrapper {
    display: none !important;
  }
  .d-block.d-md-none {
    display: none !important;
  }
}

/* Hide button text on mobile, show only icons */
@media (max-width: 767.98px) {
  #reportsCreatedResetExternal .btn-text,
  #reportsCreatedBtn .btn-text {
    display: none;
  }
  #reportsCreatedResetExternal,
  #reportsCreatedBtn {
    padding: 0.375rem 0.75rem;
  }
  #reportsCreatedResetExternal i,
  #reportsCreatedBtn i {
    margin-right: 0 !important;
  }
  
  /* Make date filter dropdown fit mobile screen - align to right */
  .dropdown-menu.date-filter-dropdown {
    position: absolute !important;
    min-width: calc(100vw - 32px) !important;
    width: calc(100vw - 32px) !important;
    max-width: calc(100vw - 32px) !important;
    right: 0 !important;
    left: auto !important;
    margin: 0 !important;
    transform: translateX(calc(-100vw + 100% + 16px)) !important;
  }
  
  /* Ensure inputs are full width */
  .date-filter-dropdown .form-control {
    width: 100% !important;
  }
}
</style>

<script>
// Data for filter dropdowns
window.areasData = @json(\App\Models\Area::all(['id', 'name']));
window.categoriesData = @json(\App\Models\ProblemCategory::active()->ordered()->get(['id', 'name']));

// Mobile collapse handler for Reports - Manual toggle with auto-close
$(document).on('click', '#laporanTableMobile .mobile-table-row', function(e) {
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
    $('#laporanTableMobile .mobile-table-row').each(function() {
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

// Force dropdown width on mobile - multiple approaches
function adjustDropdownWidth() {
    if (window.innerWidth < 768) {
        const dropdown = $('.date-filter-dropdown');
        if (dropdown.length && dropdown.hasClass('show')) {
            const viewportWidth = window.innerWidth;
            const dropdownWidth = viewportWidth - 32; // Full width minus 16px padding each side
            
            dropdown.css({
                'width': dropdownWidth + 'px !important',
                'min-width': dropdownWidth + 'px !important',
                'max-width': dropdownWidth + 'px !important',
                'right': '16px !important',
                'left': 'auto !important'
            });
            
            // Also set via attr for higher priority
            dropdown.attr('style', 
                'width: ' + dropdownWidth + 'px !important; ' +
                'min-width: ' + dropdownWidth + 'px !important; ' +
                'max-width: ' + dropdownWidth + 'px !important; ' +
                'right: 16px !important; ' +
                'left: auto !important;'
            );
        }
    }
}

// Multiple event listeners to ensure it works
$('#reportsCreatedBtn').on('shown.bs.dropdown', adjustDropdownWidth);
$('#reportsCreatedBtn').on('click', function() {
    setTimeout(adjustDropdownWidth, 100);
});

// Also adjust on window resize
$(window).on('resize', adjustDropdownWidth);
</script>
@endpush
