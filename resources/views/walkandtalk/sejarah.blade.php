@extends('layouts.main')

@section('title', 'Report History')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h1 class="mt-4">Report History</h1>
        <a href="#" class="btn btn-primary download-btn">
            <i class="fas fa-download me-2"></i>Download Report
        </a>
    </div>

    <!-- Filter Panel -->
    <div class="mb-3">
        @include('components.filter-panel', ['areas' => $areas, 'showStatusFilter' => false])
    </div>

    <div class="card p-3">
        <div class="table-responsive">
            <table id="sejarahTable" class="table table-bordered table-striped w-100" data-url="{{ route('sejarah.datatables') }}">
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
<div class="modal fade" id="modalPenyelesaian" tabindex="-1" aria-labelledby="modalPenyelesaianLabel" aria-hidden="true">
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

<!-- Modal untuk Deskripsi Masalah Lengkap -->
<div class="modal fade description-modal" id="descriptionModal" tabindex="-1" aria-labelledby="descriptionModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header py-2">
        <h5 class="modal-title fs-6" id="descriptionModalLabel">Problem Description Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body small" id="descriptionModalBody">
        <!-- Content will be loaded dynamically -->
      </div>
      <div class="modal-footer py-1">
        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

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
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', title: 'No', orderable: false, searchable: false},
                {data: 'Tanggal', name: 'Tanggal', title: 'Date'},
                {data: 'foto', name: 'foto', title: 'Photo', orderable: false, searchable: false, width: '100px'},
                {data: 'departemen', name: 'area.name', title: 'Area/Station'},
                {data: 'kategori_masalah', name: 'kategori_masalah', title: 'Problem Category', orderable: false, searchable: true},
                {data: 'deskripsi_masalah', name: 'deskripsi_masalah', title: 'Description', searchable: true},
                {data: 'tenggat_waktu', name: 'tenggat_waktu', title: 'Deadline'},
                {data: 'status', name: 'status', title: 'Status', orderable: false, searchable: true},
                {data: 'penyelesaian', name: 'penyelesaian', title: 'Completion', orderable: false, searchable: false},
                {data: 'aksi', name: 'aksi', title: 'Action', orderable: false, searchable: false}
            ],
            order: [[1, 'desc']],
            language: {
                processing: "Memuat...",
                search: "Search:",
                lengthMenu: "Display_MENU_",
                info: " _START_ / _TOTAL_ ",
                infoEmpty: "Tidak ada data yang ditampilkan",
                infoFiltered: "(difilter dari _MAX_ total data)",
                zeroRecords: "Data tidak ditemukan",
                paginate: {
                    first: "Awal",
                    last: "Akhir",
                    next: "»",
                    previous: "«"
                }
            },
            dom: '<"row mb-3"<"col-md-6"l><"col-md-6 text-end"f>>rtip'
        });

        table.on('draw.dt', function() {
            ensureFilterButton();
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

    // Debugging untuk membantu troubleshooting
    console.log('Sejarah page initialized with modal:', $('#descriptionModal').length ? 'found' : 'not found');
});
</script>
@endpush
