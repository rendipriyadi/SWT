@extends('layouts.main')

@section('title', 'Beranda Utama')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="dashboard-header">
        <h1 class="mb-0 fs-5">Dashboard</h1>
        <div class="mx-2 text-muted d-none d-md-block">|</div>
        <div class="datetime fs-6 mb-0 text-secondary"></div>
    </div>

    <!-- Filter Panel -->
    <div class="mb-2">
        @include('components.filter-panel', ['areas' => $areas, 'showStatusFilter' => false])
    </div>

    <!-- Statistik Cards -->
    <div class="dashboard-cards mb-2">
        <div class="stats-card card-blue">
            <h3>TOTAL REPORTS</h3>
            <div class="number">{{ $totalLaporan }}</div>
        </div>
        <div class="stats-card card-yellow">
            <h3>ASSIGNED REPORTS</h3>
            <div class="number">{{ $laporanDitugaskan }}</div>
        </div>
        <div class="stats-card card-red">
            <h3>COMPLETED REPORTS</h3>
            <div class="number">{{ $laporanSelesai }}</div>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-body p-2">
            <div class="table-responsive">
                <table id="laporanTable" class="table table-bordered table-striped table-hover w-100 small" data-url="{{ route('dashboard.datatables') }}">
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
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

    <!-- Modal untuk Deskripsi Masalah Lengkap -->
    <div class="modal fade description-modal" id="descriptionModal" tabindex="-1" aria-labelledby="descriptionModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header py-2">
            <h5 class="modal-title fs-6" id="descriptionModalLabel">Detail Deskripsi Masalah</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body small" id="descriptionModalBody">
            <!-- Content will be loaded dynamically -->
          </div>
          <div class="modal-footer py-1">
            <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Penyelesaian -->
    <div class="modal fade" id="modalPenyelesaian" tabindex="-1" aria-labelledby="modalPenyelesaianLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <h5 class="modal-title fs-6" id="modalPenyelesaianLabel">Detail Penyelesaian</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body small" id="modalPenyelesaianBody">
                    <!-- Content will be loaded dynamically -->
                </div>
                <div class="modal-footer py-1">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function ensureFilterButton() {
    var filterBtn = $('#filterIconBtn');
    if (filterBtn.length && $('.dataTables_filter').length) {
        if (!$.contains($('.dataTables_filter')[0], filterBtn[0])) {
            $('.dataTables_filter label').before(filterBtn.show());
        } else {
            filterBtn.show();
        }
    }
}

$(document).ready(function() {
    const originalUrl = $('#laporanTable').data('url');
    
    window.refreshTable = function(filters = null) {
        if ($.fn.DataTable.isDataTable('#laporanTable')) {
            $('#laporanTable').DataTable().destroy();
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
        
        var table = $('#laporanTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: ajaxUrl,
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', title: 'No', orderable: false, searchable: false, className: 'text-center', width: '30px'},
                {data: 'Tanggal', name: 'Tanggal', title: 'Date', width: '90px'},
                {data: 'foto', name: 'foto', title: 'Photo', orderable: false, searchable: false, className: 'text-center', width: '100px'},
                {data: 'departemen', name: 'area.name', title: 'Area/Station', width: '110px'}, 
                {data: 'kategori_masalah', name: 'kategori_masalah', title: 'Category', orderable: false, width: '90px'},
                {
                    data: 'deskripsi_masalah', 
                    name: 'deskripsi_masalah', 
                    title: 'Description',
                    className: 'text-center',
                    render: function(data, type, row) {
                        if (type === 'display' && data.length > 50) {
                            return '<div class="description-cell">' + data.substr(0, 50) + '... <a href="#" class="view-description" data-description="' + data.replace(/"/g, '&quot;') + '">detail</a></div>';
                        }
                        return data;
                    }
                },
                {data: 'tenggat_waktu', name: 'tenggat_waktu', title: 'Deadline', width: '90px'},
                {data: 'status', name: 'status', title: 'Status', width: '80px'},
                {data: 'penyelesaian', name: 'penyelesaian', title: 'Completion', orderable: false, searchable: false, className: 'text-center', width: '50px'},
                {
                    data: 'aksi', 
                    name: 'aksi', 
                    title: 'Action',
                    orderable: false, 
                    searchable: false,
                    className: 'text-center',
                    width: '65px'
                }
            ],
            order: [[1, 'desc']],
            language: {
                processing: "Memuat...",
                search: "Search:",
                lengthMenu: "Display _MENU_",
                info: "_START_ / _TOTAL_",
                infoEmpty: "0 data",
                infoFiltered: "(dari _MAX_)",
                zeroRecords: "Data Not Found",
                paginate: {
                    first: "Awal",
                    last: "Akhir",
                    next: "»",
                    previous: "«"
                }
            },
            dom: '<"row"<"col-md-6"l><"col-md-6 text-end"f>>rtip',
            drawCallback: function(settings) {
                // Adjust rows to be more compact
                $(this).find('tbody tr').addClass('align-middle');
            }
        });

        table.on('draw.dt', function() {
            ensureFilterButton();
        });
    };

    refreshTable();
    ensureFilterButton();
    $('.dataTables_filter').addClass('d-flex align-items-center gap-2 justify-content-end');
    $('.dataTables_filter label').addClass('mb-0');
});
</script>
@endpush

<style>
.filter-body {
    padding: 0.8rem;
    transition: all 0.3s ease;
    overflow: hidden;
    max-height: 1000px;
}
.filter-panel.collapsed .filter-body {
    max-height: 0 !important;
    padding: 0 !important;
    overflow: hidden;
}

Card Colors
.card-blue .number { color: #0d6efd; }
.card-yellow .number { color: #ffc107; }
.card-red .number { color: #dc3545; } 

#laporanTable thead th {
    text-align: center;
}

#laporanTable thead th:nth-child(6) {
    text-align: center;
}

#laporanTable tbody td:nth-child(6) {
    text-align: justify !important;
}

#laporanTable tbody td {
    text-align: center;
}


/* SIEMENS Design System - Clean and Minimal Layout */

/* Font dan Ukuran Dasar */
/* SIEMENS Design System - Clean and Minimal Layout */

/* Font dan Ukuran Dasar*/

</style>