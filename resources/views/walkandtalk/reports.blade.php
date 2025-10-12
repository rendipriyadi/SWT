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
                <i class="fas fa-redo me-1"></i>Reset
            </button>
            <div class="dropdown">
                <button class="btn btn-outline-secondary btn-sm border border-secondary fs-6" type="button" id="reportsCreatedBtn" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                    <i class="fas fa-calendar-alt me-1"></i>Filter
                </button>
                <div class="dropdown-menu dropdown-menu-end p-3" aria-labelledby="reportsCreatedBtn" style="min-width: 420px;" onclick="event.stopPropagation();">
                    <h6 class="dropdown-header px-0">Filter by Created Date</h6>
                    <div class="row g-2 mb-2">
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-bold" for="report_created_start">Start</label>
                            <input type="text" id="report_created_start" class="form-control form-control-sm" placeholder="Start date" />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-bold" for="report_created_end">End</label>
                            <input type="text" id="report_created_end" class="form-control form-control-sm" placeholder="End date" />
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-primary btn-sm" id="report_created_apply">
                            <i class="fas fa-filter me-1"></i>Apply
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="table-responsive">
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
#rowDetailModal .value { font-size: 1.05rem; color: var(--text-primary); }

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

/* Override any conflicting styles for date column */
#laporanTable tbody td:nth-child(2).wrap-cell {
  white-space: normal !important;
  word-break: break-word !important;
  height: auto !important;
}

/* Ensure table rows have consistent height when content wraps */
#laporanTable tbody tr {
  height: auto !important;
  min-height: 60px;
}

/* Better text wrapping for all table cells */
#laporanTable td {
  word-wrap: break-word;
  overflow-wrap: break-word;
}
</style>

<script>
// Data for filter dropdowns
window.areasData = @json(\App\Models\Area::all(['id', 'name']));
window.categoriesData = @json(\App\Models\ProblemCategory::active()->ordered()->get(['id', 'name']));
</script>
@endpush
