@extends('layouts.main')

@section('title', 'Report History')

@section('content')
<div class="container-fluid px-4">
    <div class="page-header mb-4">
        <div>
            <h3 class="mb-2">Report History</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">History</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="#" class="btn btn-primary download-btn">
                <i class="fas fa-file-pdf me-2"></i> Export PDF
            </a>
        </div>
    </div>


    <div class="card p-3">
        <div class="table-responsive">
            <table id="sejarahTable" class="table table-bordered table-striped table-hover w-100" data-url="{{ route('sejarah.datatables') }}">
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

<!-- Modal Preview Foto Full -->
<div class="modal fade" id="modalFotoFull" tabindex="-1" aria-labelledby="modalFotoFullLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content bg-transparent border-0">
      <div class="modal-body text-center p-0">
        <div id="photoCarousel" class="carousel slide">
          <div class="carousel-inner">
            <!-- Carousel items will be added dynamically -->
          </div>
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

        <!-- Modal Penyelesaian -->
        <div class="modal fade settlement-modal" id="modalPenyelesaian" tabindex="-1" aria-labelledby="modalPenyelesaianLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPenyelesaianLabel">Settlement Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalPenyelesaianBody">
                <!-- Content will be loaded dynamically -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Row -->
<div class="modal fade" id="rowDetailModal" tabindex="-1" aria-labelledby="rowDetailModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="rowDetailModalLabel"><i class="fas fa-info-circle me-2 text-primary"></i>Report Details</h5>
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

<style>
#sejarahTable.dataTable tbody tr.clickable-row,
#sejarahTable.dataTable tbody tr.clickable-row td,
#sejarahTable.dataTable tbody tr.clickable-row td.dtr-control { cursor: pointer !important; }
#sejarahTable.dataTable tbody tr.row-hover,
#sejarahTable.dataTable tbody tr.row-hover td,
#sejarahTable.dataTable tbody tr.row-hover td.dtr-control { background-color: rgba(13,110,253,0.08) !important; transition: background-color .15s ease-in-out; }

/* Dark mode table row hover */
:root[data-theme="dark"] #sejarahTable.dataTable tbody tr.row-hover,
:root[data-theme="dark"] #sejarahTable.dataTable tbody tr.row-hover td,
:root[data-theme="dark"] #sejarahTable.dataTable tbody tr.row-hover td.dtr-control { 
  background-color: rgba(102,170,255,0.12) !important; 
}

/* Bigger typography for Report Details modal */
#rowDetailModal .modal-dialog { max-width: 1000px; }
#rowDetailModal .modal-body { font-size: 1rem; overflow-x: hidden; max-height: 85vh; }
#rowDetailModal .modal-title { font-size: 1.25rem; }
#rowDetailModal .fw-semibold { font-size: 0.95rem; color: var(--text-secondary); }
#rowDetailModal .value { font-size: 1.05rem; color: var(--text-primary); }
#rd_description { white-space: pre-wrap; overflow-wrap: anywhere; word-break: break-word; }

/* Dark mode for Report Details modal */
:root[data-theme="dark"] #rowDetailModal .modal-content {
  background-color: var(--bg-card) !important;
  color: var(--text-primary) !important;
  border-color: var(--border-color) !important;
}

:root[data-theme="dark"] #rowDetailModal .modal-header {
  border-bottom-color: var(--border-color) !important;
  background-color: var(--bg-surface) !important;
}

:root[data-theme="dark"] #rowDetailModal .modal-title {
  color: var(--text-primary) !important;
}

:root[data-theme="dark"] #rowDetailModal .modal-body {
  background-color: var(--bg-card) !important;
  color: var(--text-primary) !important;
}

:root[data-theme="dark"] #rowDetailModal .modal-footer {
  border-top-color: var(--border-color) !important;
  background-color: var(--bg-surface) !important;
}

:root[data-theme="dark"] #rowDetailModal .btn-close {
  filter: invert(1) grayscale(100%) brightness(200%);
}

:root[data-theme="dark"] #rowDetailModal .fw-semibold {
  color: var(--text-secondary) !important;
}

:root[data-theme="dark"] #rowDetailModal .value {
  color: var(--text-primary) !important;
}

/* Dark mode for Settlement modal */
:root[data-theme="dark"] #modalPenyelesaian .modal-content {
  background-color: var(--bg-card) !important;
  color: var(--text-primary) !important;
  border-color: var(--border-color) !important;
}

:root[data-theme="dark"] #modalPenyelesaian .modal-header {
  border-bottom-color: var(--border-color) !important;
  background-color: var(--bg-surface) !important;
}

:root[data-theme="dark"] #modalPenyelesaian .modal-title {
  color: var(--text-primary) !important;
}

:root[data-theme="dark"] #modalPenyelesaian .modal-body {
  background-color: var(--bg-card) !important;
  color: var(--text-primary) !important;
}

:root[data-theme="dark"] #modalPenyelesaian .modal-footer {
  border-top-color: var(--border-color) !important;
  background-color: var(--bg-surface) !important;
}

:root[data-theme="dark"] #modalPenyelesaian .btn-close {
  filter: invert(1) grayscale(100%) brightness(200%);
}

/* Dark mode for Delete History modal */
:root[data-theme="dark"] #deleteHistoryModal .modal-content {
  background-color: var(--bg-card) !important;
  color: var(--text-primary) !important;
  border-color: var(--border-color) !important;
}

:root[data-theme="dark"] #deleteHistoryModal .modal-header {
  border-bottom-color: var(--border-color) !important;
}

:root[data-theme="dark"] #deleteHistoryModal .modal-footer {
  border-top-color: var(--border-color) !important;
}

:root[data-theme="dark"] #deleteHistoryModal .modal-title {
  color: #dc3545 !important;
}

:root[data-theme="dark"] #deleteHistoryModal .modal-body {
  color: var(--text-primary) !important;
}

:root[data-theme="dark"] #deleteHistoryModal .text-danger {
  color: #ff6b6b !important;
}

:root[data-theme="dark"] #deleteHistoryModal .btn-close {
  filter: invert(1) grayscale(100%) brightness(200%);
}

/* Dark mode for Photo modal */
:root[data-theme="dark"] #modalFotoFull .modal-content {
  background-color: transparent !important;
  border: none !important;
}

:root[data-theme="dark"] #modalFotoFull .carousel-control-prev-icon,
:root[data-theme="dark"] #modalFotoFull .carousel-control-next-icon {
  filter: invert(1);
}

/* Dark mode for table content and badges */
:root[data-theme="dark"] #sejarahTable tbody tr td {
  color: var(--text-primary) !important;
  border-color: var(--border-color) !important;
}

:root[data-theme="dark"] #sejarahTable thead th {
  color: var(--text-secondary) !important;
  border-color: var(--border-color) !important;
  background-color: var(--bg-surface) !important;
}

/* Ensure badges maintain readability in dark mode */
:root[data-theme="dark"] .badge {
  filter: brightness(0.9) saturate(1.1);
}
</style>

@if ($errors->any())
<script>
document.addEventListener('DOMContentLoaded', function() {
    let errorMessages = @json($errors->all());
    let formattedErrors = errorMessages.map(msg => `• ${msg}`).join('<br>');
    let toastBody = document.getElementById('mainToastBody');
    let toastEl = document.getElementById('mainToast');
    let toastIcon = document.getElementById('mainToastIcon');
    if (toastBody && toastEl && toastIcon) {
        toastBody.innerHTML = formattedErrors;
        toastEl.classList.remove('bg-success', 'bg-info', 'bg-warning');
        toastEl.classList.add('bg-danger');
        toastIcon.innerHTML = '<i class="fas fa-exclamation-circle text-danger fs-5"></i>';
        let toast = new bootstrap.Toast(toastEl);
        toast.show();
    }
});
</script>
@endif

@if (session('success'))
<script>
document.addEventListener('DOMContentLoaded', function() {
    let toastBody = document.getElementById('mainToastBody');
    let toastEl = document.getElementById('mainToast');
    let toastIcon = document.getElementById('mainToastIcon');
    if (toastBody && toastEl && toastIcon) {
        toastBody.innerHTML = '{{ session('success') }}';
        toastEl.classList.remove('bg-danger', 'bg-info', 'bg-warning');
        toastEl.classList.add('bg-success');
        toastIcon.innerHTML = '<i class="fas fa-check-circle text-success fs-5"></i>';
        let toast = new bootstrap.Toast(toastEl);
        toast.show();
    }
});
</script>
@endif

@if (session('error'))
<script>
document.addEventListener('DOMContentLoaded', function() {
    let pesan = `{!! session('error') !!}`;
    let toastBody = document.getElementById('mainToastBody');
    let toastEl = document.getElementById('mainToast');
    let toastIcon = document.getElementById('mainToastIcon');
    if (toastBody && toastEl && toastIcon) {
        toastBody.innerHTML = pesan;
        toastEl.classList.remove('bg-success', 'bg-info', 'bg-warning');
        toastEl.classList.add('bg-danger', 'text-white');
        toastIcon.innerHTML = `<i class="fas fa-times-circle text-white"></i>`;
    }
});
</script>
@endif
@endsection

@push('scripts')
<script>
function ensureFilterButton() {
    var filterBtn = $('#filterIconBtn');
    if (filterBtn.length && $('.dataTables_filter').length) {
        if (!$.contains($('.dataTables_filter')[0], filterBtn[0])) {
            $('.dataTables_filter').prepend(filterBtn.show());
        } else {
            filterBtn.show();
        }
    }
}

$(document).ready(function() {
    const originalUrl = $('#sejarahTable').data('url');
    
    window.refreshTable = function(filters = null) {
        if ($.fn.DataTable.isDataTable('#sejarahTable')) {
            $('#sejarahTable').DataTable().destroy();
        }
        let ajaxUrl = originalUrl;
        if (filters) {
            const params = new URLSearchParams();
            if (filters.start_date) params.append('start_date', filters.start_date);
            if (filters.end_date) params.append('end_date', filters.end_date);
            if (filters.area_id) params.append('area_id', filters.area_id);
            if (filters.penanggung_jawab_id) params.append('penanggung_jawab_id', filters.penanggung_jawab_id);
            if (filters.kategori) params.append('kategori', filters.kategori);
            if (filters.status) params.append('status', filters.status);
            if (filters.tenggat_bulan) params.append('tenggat_bulan', filters.tenggat_bulan);
            ajaxUrl = `${originalUrl}?${params.toString()}`;
        }
        var table = $('#sejarahTable').DataTable({
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
            createdRow: function(row, data){
                $(row).addClass('clickable-row').css('cursor','pointer');
            }
        });

        table.on('draw.dt', function() {
            ensureFilterButton();
            var lastHeader = $('#sejarahTable thead th:last-child');
            if ($.trim(lastHeader.text()) === '') {
                lastHeader.text('Action');
            }
            renderFilterChips();

            // Ensure all rows have clickable styling on each draw
            // Add clickable class to visible rows only (ignore child rows)
            $('#sejarahTable tbody tr').each(function(){
                if (!$(this).hasClass('child')) {
                    $(this).addClass('clickable-row');
                }
            });

            // Row hover handling (add/remove class to avoid conflicts with DataTables striping)
            $('#sejarahTable tbody').off('mouseenter.rowhover mouseleave.rowhover')
                .on('mouseenter.rowhover', 'tr', function(){
                    if (!$(this).hasClass('child')) {
                        $(this).addClass('row-hover').css('cursor','pointer');
                    }
                })
                .on('mouseleave.rowhover', 'tr', function(){
                    $(this).removeClass('row-hover');
                });

            // Row click to open detail modal (ignore clicks on actionable elements)
            $('#sejarahTable tbody').off('click.rowdetail').on('click.rowdetail', 'tr', function(e){
                if ($(e.target).closest('a,button,.btn,input,label,select').length) return;
                const dt = $('#sejarahTable').DataTable();
                const data = dt.row(this).data();
                if (!data) return;
                $('#rd_date').text($(this).find('td:eq(1)').text() || '');
                $('#rd_area').html(data.departemen || '');
                $('#rd_category').html(data.problem_category || '');
                $('#rd_status').html(data.status || '');
                const rawDesc = (data.deskripsi_masalah_full || data.deskripsi_full || data.description_full || data.deskripsi_text || data.deskripsi_masalah || '').toString();
                const plainDesc = $('<div>').html(rawDesc).text();
                $('#rd_description').text(plainDesc);
                $('#rowDetailModal').modal('show');
            });
            
            // Handle delete button click
            $(document).off('click', '.delete-btn').on('click', '.delete-btn', function(e) {
                e.preventDefault();
                
                const deleteUrl = $(this).data('delete-url');
                const reportId = $(this).data('id');
                const reportTitle = $(this).data('title') || 'this report';
                
                // Set modal content and store delete URL
                $('#deleteHistoryTitle').text(reportTitle);
                $('#deleteHistoryModal').data('delete-url', deleteUrl);
                $('#deleteHistoryModal').modal('show');
            });
            
            // Handle confirm delete button for history
            $('#confirmDeleteHistory').on('click', function() {
                const deleteUrl = $('#deleteHistoryModal').data('delete-url');
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
                        $('#deleteHistoryModal').modal('hide');
                        
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
                        if ($.fn.DataTable.isDataTable('#sejarahTable')) {
                            $('#sejarahTable').DataTable().ajax.reload();
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
        });
    };

    // Initial table load
    refreshTable();

    // Pindahkan tombol filter ke sebelum input "Cari:"
    ensureFilterButton();
    $('.dataTables_filter').addClass('d-flex align-items-center gap-2 justify-content-end');
    $('.dataTables_filter label').addClass('mb-0');
    
    // Function to handle download with active filters
    function downloadWithFilters() {
        // Get all current filters
        const filters = {};
        if ($('#start_date').val()) filters.start_date = $('#start_date').val();
        if ($('#end_date').val()) filters.end_date = $('#end_date').val();
        if ($('#area_id').val()) filters.area_id = $('#area_id').val();
        if ($('#penanggung_jawab_id').val()) filters.penanggung_jawab_id = $('#penanggung_jawab_id').val();
        if ($('#kategori').val()) filters.kategori = $('#kategori').val();
        if ($('#tenggat_bulan').val()) filters.tenggat_bulan = $('#tenggat_bulan').val();
        
        // Create query parameters
        const params = new URLSearchParams(filters);
        
        // Redirect to download URL with parameters
        window.location.href = "{{ route('sejarah.download') }}?" + params.toString();
    }

    // Tambahkan event listener untuk tombol download
    $('.download-btn').on('click', function(e) {
        e.preventDefault();
        downloadWithFilters();
    });

    // Helpers: collect filters
    function collectFilters() {
        const f = {};
        if ($('#start_date').val()) f.start_date = $('#start_date').val();
        if ($('#end_date').val()) f.end_date = $('#end_date').val();
        if ($('#area_id').val()) f.area_id = $('#area_id').val();
        if ($('#penanggung_jawab_id').val()) f.penanggung_jawab_id = $('#penanggung_jawab_id').val();
        if ($('#kategori').val()) f.kategori = $('#kategori').val();
        if ($('#tenggat_bulan').val()) f.tenggat_bulan = $('#tenggat_bulan').val();
        if ($('#status').length && $('#status').val()) f.status = $('#status').val();
        return f;
    }

    // Render filter chips
    function renderFilterChips() {
        const map = [
            {id:'#start_date', label:'Start'},
            {id:'#end_date', label:'End'},
            {id:'#area_id', label:'Area'},
            {id:'#penanggung_jawab_id', label:'Station'},
            {id:'#kategori', label:'Category'},
            {id:'#tenggat_bulan', label:'Month'},
            {id:'#status', label:'Status'}
        ];
        const chips = [];
        map.forEach(m => {
            const el = $(m.id);
            if (!el.length) return;
            const val = el.val();
            if (!val) return;
            let text = el.is('select') ? el.find('option:selected').text() : val;
            chips.push(`<span class="filter-chip" data-target="${m.id}"><span class="fw-semibold">${m.label}:</span> ${text} <span class="remove" title="Clear">&times;</span></span>`);
        });
        $('#activeFilterChips').html(chips.join(''));
        $('#activeFilterChips .filter-chip .remove').off('click').on('click', function(){
            const target = $(this).closest('.filter-chip').data('target');
            const el = $(String(target));
            if (el.is('select')) el.prop('selectedIndex', 0).trigger('change'); else el.val('');
            refreshTable(collectFilters());
            setTimeout(renderFilterChips,0);
        });
    }

    // Bind apply/reset if buttons exist
    $(document).on('click', '#applyFilter', function(){
        const f = collectFilters();
        if (f.start_date && f.end_date && new Date(f.start_date) > new Date(f.end_date)) {
            alert('Start date cannot be after end date');
            return;
        }
        refreshTable(f);
    });
    $(document).on('click', '#resetFilter', function(){
        $('#filterForm')[0]?.reset();
        $('#filterForm select').trigger('change');
        refreshTable();
        setTimeout(renderFilterChips,0);
    });




    // Debugging untuk membantu troubleshooting
    console.log('Sejarah page initialized with modal:', $('#descriptionModal').length ? 'found' : 'not found');
    renderFilterChips();
});
</script>
@endpush

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteHistoryModal" tabindex="-1" aria-labelledby="deleteHistoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title text-danger" id="deleteHistoryModalLabel">
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
                <button type="button" class="btn btn-danger" id="confirmDeleteHistory">
                    <i class="fas fa-trash me-1"></i>Delete Report
                </button>
            </div>
        </div>
    </div>
</div>
