$(document).ready(function() {
    // Global DataTables configuration for more compact tables
    $.extend(true, $.fn.dataTable.defaults, {
        dom: "<'row'<'col-sm-6'l><'col-sm-6'f>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        language: {
            processing: "Memuat...",
            search: "Cari:",
            lengthMenu: "Tampil _MENU_",
            info: "_START_-_END_ dari _TOTAL_",
            infoEmpty: "0 data",
            infoFiltered: "(dari _MAX_)",
            zeroRecords: "Tidak ada data",
            paginate: {
                first: "Awal",
                last: "Akhir",
                next: "»",
                previous: "«"
            }
        },
        pageLength: 10,
        responsive: true,
        autoWidth: false,
        columnDefs: [
            { className: "align-middle", targets: "_all" }
        ],
        drawCallback: function(settings) {
            // Apply consistent styling to elements after each draw
            $(this).find('tbody tr').css('height', '32px');
            
            // Initialize status badges
            if (typeof window.initStatusBadges === 'function') {
                window.initStatusBadges();
            }
        }
    });

    // Dashboard table
    if ($('#laporanTable').length) {
        var table = $('#laporanTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: $('#laporanTable').data('url'),
                type: 'GET'
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center', width: '25px' },
                { data: 'Tanggal', name: 'Tanggal', width: '90px' },
                { data: 'foto', name: 'foto', orderable: false, searchable: false, width: '50px', className: 'text-center' },
                { data: 'departemen', name: 'area.name', width: '110px' },
                { data: 'problem_category', name: 'problemCategory.name', orderable: false, width: '90px' },
                { 
                    data: 'deskripsi_masalah', 
                    name: 'deskripsi_masalah', 
                    render: function(data, type, row) {
                        if (type === 'display') {
                            // Pastikan data tidak null
                            if (!data) return '';
                            
                            // Sanitasi data dan batasi panjang
                            const safeText = String(data).replace(/</g, '&lt;').replace(/>/g, '&gt;');
                            const maxLength = 50;
                            
                            // Jika teks melebihi panjang maksimum, tambahkan ellipsis dan link detail
                            if (safeText.length > maxLength) {
                                return safeText.substring(0, maxLength) + '... ' +
                                    '<a href="#" class="view-description" data-description="' + 
                                    safeText.replace(/"/g, '&quot;') + '">detail</a>';
                            }
                            return safeText;
                        }
                        return data;
                    }
                },
                { data: 'tenggat_waktu', name: 'tenggat_waktu', width: '90px' },
                { 
                    data: 'status', 
                    name: 'status', 
                    width: '80px',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            return createStatusBadge(data);
                        }
                        return data;
                    }
                },
                { data: 'penyelesaian', name: 'penyelesaian', orderable: false, searchable: false, className: 'text-center', width: '50px' },
                { 
                    data: 'aksi', 
                    name: 'aksi', 
                    orderable: false, 
                    searchable: false,
                    width: '65px',
                    className: 'text-center',
                    render: function(data, type, row, meta) {
                        const dropdownId = 'dropdown-' + row.id;
                        
                        return `<div class="dropdown action-dropdown">
                            <button class="btn btn-sm btn-secondary dropdown-toggle py-0 px-2" type="button" id="${dropdownId}" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="${dropdownId}">
                                <li><a class="dropdown-item" href="/edit${row.id}"><i class="fas fa-edit text-warning"></i> Edit</a></li>
                                <li><a class="dropdown-item" href="/laporan/${row.id}/tindakan"><i class="fas fa-check-circle text-success"></i> Tindakan</a></li>
                                <li><button class="dropdown-item delete-btn" data-id="${row.id}" data-delete-url="/laporan/${row.id}/delete" data-return-url="${window.location.pathname}"><i class="fas fa-trash text-danger"></i> Hapus</button></li>
                            </ul>
                        </div>`;
                    }
                }
            ],
            order: [[1, 'desc']]
        });
    }

    // History table
    if ($('#sejarahTable').length) {
        var sejarahTable = $('#sejarahTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: $('#sejarahTable').data('url'),
                type: 'GET'
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center', width: '25px' },
                { data: 'Tanggal', name: 'Tanggal', width: '90px' },
                { data: 'foto', name: 'foto', orderable: false, searchable: false, width: '50px', className: 'text-center' },
                { data: 'departemen', name: 'area.name', width: '110px' },
                { data: 'problem_category', name: 'problemCategory.name', orderable: false, width: '90px' },
                { 
                    data: 'deskripsi_masalah', 
                    name: 'deskripsi_masalah',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            // Pastikan data tidak null
                            if (!data) return '';
                            
                            // Sanitasi data dan batasi panjang
                            const safeText = String(data).replace(/</g, '&lt;').replace(/>/g, '&gt;');
                            const maxLength = 50;
                            
                            // Jika teks melebihi panjang maksimum, tambahkan ellipsis dan link detail
                            if (safeText.length > maxLength) {
                                return safeText.substring(0, maxLength) + '... ' +
                                    '<a href="#" class="view-description" data-description="' + 
                                    safeText.replace(/"/g, '&quot;') + '">detail</a>';
                            }
                            return safeText;
                        }
                        return data;
                    }
                },
                { 
                    data: 'status', 
                    name: 'status', 
                    width: '80px',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            return createStatusBadge(data);
                        }
                        return data;
                    }
                },
                { data: 'penyelesaian', name: 'penyelesaian', orderable: false, searchable: false, className: 'text-center', width: '50px' },
                { 
                    data: 'aksi', 
                    name: 'aksi', 
                    orderable: false, 
                    searchable: false,
                    width: '65px',
                    className: 'text-center',
                    render: function(data, type, row, meta) {
                        const dropdownId = 'dropdown-' + row.id;
                        
                        return `<div class="dropdown action-dropdown">
                            <button class="btn btn-sm btn-secondary dropdown-toggle py-0 px-2" type="button" id="${dropdownId}" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="${dropdownId}">
                                <li><a class="dropdown-item" href="#"><i class="fas fa-info-circle text-info"></i> Detail</a></li>
                                <li><button class="dropdown-item delete-btn" data-id="${row.id}" data-delete-url="/laporan/${row.id}/delete" data-return-url="${window.location.pathname}"><i class="fas fa-trash text-danger"></i> Hapus</button></li>
                            </ul>
                        </div>`;
                    }
                }
            ],
            order: [[1, 'desc']]
        });
    }

    // Ensure the filter button is shown
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

    // Add filter button after table is drawn
    $('.dataTable').on('draw.dt', function() {
        ensureFilterButton();
    });
});

// Format tanggal untuk Indonesia
function formatTanggalIndonesia(tanggal) {
    if (!tanggal) return '-';
    
    const options = { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    };
    
    return new Date(tanggal).toLocaleDateString('id-ID', options);
}