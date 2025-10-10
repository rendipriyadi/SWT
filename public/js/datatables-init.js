$(document).ready(function() {
    // Global DataTables configuration (single source of truth)
    $.extend(true, $.fn.dataTable.defaults, {
        // Move both length (l) and filter (f) to the right
        // Top: Show on the left (l) and Search on the right (f); Bottom: left-aligned stacked Info over Pagination
        dom: "<'row mb-3'<'col-sm-6'l><'col-sm-6 text-end'f>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row'<'col-sm-12'ip>>",
        language: {
            processing: "Loading...",
            search: "Search:",
            lengthMenu: "Show _MENU_",
            info: "_START_-_END_ of _TOTAL_",
            infoEmpty: "No data",
            infoFiltered: "(filtered from _MAX_)",
            zeroRecords: "No data found",
            paginate: {
                first: "First",
                last: "Last",
                next: "»",
                previous: "«"
            }
        },
        pageLength: 10,
        responsive: false,
        autoWidth: false,
        scrollX: true,
        scrollCollapse: true,
        columnDefs: [
            { className: "align-middle", targets: "_all" }
        ],
        drawCallback: function(settings) {
            // Apply consistent styling to elements after each draw
            // Biarkan tinggi baris menyesuaikan konten agar badge status tidak terpotong
            $(this).find('tbody tr').css('height', 'auto');
            
            // Initialize status badges
            if (typeof window.initStatusBadges === 'function') {
                window.initStatusBadges();
            }

            // Sync columns after draw
            try { this.api().columns.adjust(); } catch(e) {}
        }
    });

    // Dashboard table
    if ($('#laporanTable').length) {
        var table = $('#laporanTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: $('#laporanTable').data('url'),
                type: 'GET',
                data: function(d) {
                    d.start_date = $('#filter_start_date').val();
                    d.end_date = $('#filter_end_date').val();
                    d.area_id = $('#filter_area').val();
                    d.category_id = $('#filter_category').val();
                }
            },
            dom: '<"row mb-3"<"col-sm-6"l><"col-sm-6 d-flex justify-content-end align-items-center gap-2"<"filter-btn-container">f>>rtip',
            scrollX: true,
            scrollCollapse: true,
            autoWidth: false,
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center', width: '25px' },
                { data: 'Tanggal', name: 'Tanggal', width: '110px' },
                { data: 'foto', name: 'foto', orderable: false, searchable: false, width: '50px', className: 'text-center' },
                { data: 'departemen', name: 'area.name', width: '110px', className: 'text-center'},
                { data: 'problem_category', name: 'problemCategory.name', orderable: false, width: '110px' },
                { 
                    data: 'deskripsi_masalah', 
                    name: 'deskripsi_masalah', 
                    render: function(data, type, row) {
                        if (type === 'display') {
                            if (!data) return '';
                            // tampilkan teks polos tanpa link "detail"
                            return $('<div>').html(String(data)).text();
                        }
                        return data;
                    }
                },
                { data: 'tenggat_waktu', name: 'tenggat_waktu', width: '110px' },
                { data: 'status', name: 'status', width: '140px', orderable: false, searchable: false },
                { data: 'penyelesaian', name: 'penyelesaian', orderable: false, searchable: false, className: 'text-center', width: '50px' },
                { data: 'aksi', name: 'aksi', orderable: false, searchable: false, width: '65px', className: 'text-center' }
            ],
            order: [[1, 'desc']],
            createdRow: function(row, data) { $(row).addClass('clickable-row'); }
        });

        // Row click -> open detail modal
        $('#laporanTable tbody').on('click', 'tr', function(e) {
            // Ignore clicks on interactive controls inside the row and photo modal triggers
            if (
                $(e.target).closest('a, button, .dropdown, .btn, input, label').length ||
                $(e.target).closest('[data-bs-toggle="modal"]').length
            ) return;

            // Ignore clicks on the Photo column (index 2)
            const td = $(e.target).closest('td');
            if (td.length && td.index() === 2) return;

            const rowData = table.row(this).data();
            if (!rowData) return;

            // Fill modal fields
            const m = document.getElementById('rowDetailModal');
            if (!m) return;
            const setHTML = (id, html) => { const el = document.getElementById(id); if (el) el.innerHTML = html || ''; };
            const setText = (id, text) => { const el = document.getElementById(id); if (el) el.textContent = text || ''; };

            setText('rd_date', rowData.Tanggal || '-');
            setHTML('rd_area', rowData.departemen || '-');

            // Person in Charge: prefer server-provided field, fallback to parsing from departemen
            (function setPIC() {
                let pic = rowData.person_in_charge || '';
                if (!pic) {
                    try {
                        const tmp = document.createElement('div');
                        tmp.innerHTML = rowData.departemen || '';
                        const plain = (tmp.textContent || '').trim();
                        const match = plain.match(/\(([^)]+)\)/);
                        if (match && match[1]) pic = match[1];
                    } catch(_) {}
                }
                setText('rd_person_in_charge', pic || '');
            })();

            setHTML('rd_category', rowData.problem_category || '-');
            setHTML('rd_status', rowData.status || '-');
            setText('rd_deadline', rowData.tenggat_waktu || '-');
            setText('rd_description', rowData.deskripsi_masalah_full || rowData.deskripsi_masalah || '');

            // Show modal
            try { new bootstrap.Modal(m).show(); } catch (_) { $(m).modal('show'); }
        });

        // Re-adjust kolom ketika sidebar ditoggle atau window di-resize
        const adjustDashboardTable = function() {
            try { table.columns.adjust().draw(false); } catch (e) {}
        };
        $(window).on('resize', adjustDashboardTable);
        window.addEventListener('sidebar:toggled', adjustDashboardTable);

        // Create filter button and dropdown
        const filterHTML = `
            <div class="dropdown">
                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-calendar-alt me-1"></i>Filter
                </button>
                <div class="dropdown-menu dropdown-menu-end p-3" aria-labelledby="filterDropdown" style="min-width: 400px;" onclick="event.stopPropagation();">
                    <h6 class="dropdown-header px-0">Filter Reports</h6>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Start Date</label>
                        <input type="date" class="form-control form-control-sm" id="filter_start_date">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">End Date</label>
                        <input type="date" class="form-control form-control-sm" id="filter_end_date">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Area</label>
                        <select class="form-select form-select-sm" id="filter_area">
                            <option value="">All Areas</option>
                            ${window.areasData ? window.areasData.map(area => `<option value="${area.id}">${area.name}</option>`).join('') : ''}
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Problem Category</label>
                        <select class="form-select form-select-sm" id="filter_category">
                            <option value="">All Categories</option>
                            ${window.categoriesData ? window.categoriesData.map(cat => `<option value="${cat.id}">${cat.name}</option>`).join('') : ''}
                        </select>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-primary btn-sm flex-fill" id="btn_filter">
                            <i class="fas fa-filter me-1"></i>Apply
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm flex-fill" id="btn_reset">
                            <i class="fas fa-redo me-1"></i>Reset
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        $('.filter-btn-container').html(filterHTML);

        // Customize search input
        $('#laporanTable_filter input').attr('placeholder', 'Search...');
        $('#laporanTable_filter label').contents().filter(function() {
            return this.nodeType === 3; // Text node
        }).remove();

        // Filter button handler
        $(document).on('click', '#btn_filter', function() {
            table.ajax.reload();
        });

        // Reset button handler
        $(document).on('click', '#btn_reset', function() {
            $('#filter_start_date').val('');
            $('#filter_end_date').val('');
            $('#filter_area').val('');
            $('#filter_category').val('');
            table.ajax.reload();
        });
    }

    // History table
    if ($('#sejarahTable').length) {
        const isMobile = false; // force horizontal scroll always
        var sejarahTable = $('#sejarahTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: $('#sejarahTable').data('url'),
                type: 'GET',
                data: function(d) {
                    d.start_date = $('#history_filter_start_date').val();
                    d.end_date = $('#history_filter_end_date').val();
                    d.area_id = $('#history_filter_area').val();
                    d.category_id = $('#history_filter_category').val();
                }
            },
            dom: '<"row mb-3"<"col-sm-6"l><"col-sm-6 d-flex justify-content-end align-items-center gap-2"<"history-filter-btn-container">f>>rtip',
            responsive: false,
            autoWidth: false,
            scrollX: true,
            scrollCollapse: true,
            language: {
                processing: "Loading...",
                search: "Search:",
                lengthMenu: "Show _MENU_",
                info: "_START_-_END_ of _TOTAL_",
                infoEmpty: "No data",
                infoFiltered: "(filtered from _MAX_)",
                zeroRecords: "No data found",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "»",
                    previous: "«"
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center', width: '25px' },
                { data: 'Tanggal', name: 'Tanggal', width: '90px' },
                { data: 'foto', name: 'foto', orderable: false, searchable: false, width: '50px', className: 'text-center' },
                { data: 'departemen', name: 'area.name', width: '110px', className: 'text-center' },
                { data: 'problem_category', name: 'problemCategory.name', orderable: false, width: '110px' },
                { 
                    data: 'deskripsi_masalah', 
                    name: 'deskripsi_masalah',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            if (!data) return '';
                            // tampilkan teks polos tanpa link "detail"
                            return $('<div>').html(String(data)).text();
                        }
                        return data;
                    }
                },
                { data: 'tenggat_waktu', name: 'tenggat_waktu', width: '90px' },
                { data: 'status', name: 'status', width: '110px', orderable: false, searchable: false },
                { data: 'penyelesaian', name: 'penyelesaian', orderable: false, searchable: false, className: 'text-center', width: '50px' },
                { data: 'aksi', name: 'aksi', orderable: false, searchable: false, width: '65px', className: 'text-center' }
            ],
            order: [[1, 'desc']],
            columnDefs: [
                { targets: 0, width: 50, className: 'text-center align-middle no-wrap' }, // No
                { targets: 1, width: 120, className: 'text-center align-middle wrap-cell' },            // Date
                { targets: 2, width: 80, className: 'text-center align-middle no-wrap' }, // Photo
                { targets: 3, width: 180, className: 'text-center align-middle wrap-cell' }, // Area/Station
                { targets: 4, width: 160, className: 'text-center align-middle wrap-cell' }, // Problem Category
                { targets: 5, width: 200, className: 'text-center align-middle wrap-cell' },           // Description
                { targets: 6, width: 100, className: 'text-center align-middle wrap-cell deadline-col' }, // Deadline
                { targets: 7, width: 120, className: 'text-center align-middle no-wrap status-col' }, // Status
                { targets: 8, width: 100, className: 'text-center align-middle no-wrap completion-cell' }, // Completion
                { targets: 9, width: 100, className: 'text-center align-middle no-wrap action-cell' } // Action
            ],
            drawCallback: function(){ try { this.api().columns.adjust(); } catch(e){} },
            createdRow: function(row, data) {
                $(row).addClass('clickable-row');
            }
        });

        // Re-adjust kolom ketika sidebar ditoggle atau window di-resize
        const adjustHistoryTable = function() {
            try { sejarahTable.columns.adjust().draw(false); } catch (e) {}
        };
        $(window).on('resize', adjustHistoryTable);
        window.addEventListener('sidebar:toggled', adjustHistoryTable);

        // Disable row-click detail on mobile for usability
        if (isMobile) {
            $('#sejarahTable tbody').off('click', 'tr');
        }

        // Create filter button and dropdown for History table
        const historyFilterHTML = `
            <div class="dropdown">
                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" id="historyFilterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-calendar-alt me-1"></i>Filter
                </button>
                <div class="dropdown-menu dropdown-menu-end p-3" aria-labelledby="historyFilterDropdown" style="min-width: 400px;" onclick="event.stopPropagation();">
                    <h6 class="dropdown-header px-0">Filter History</h6>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Start Date</label>
                        <input type="date" class="form-control form-control-sm" id="history_filter_start_date">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">End Date</label>
                        <input type="date" class="form-control form-control-sm" id="history_filter_end_date">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Area</label>
                        <select class="form-select form-select-sm" id="history_filter_area">
                            <option value="">All Areas</option>
                            ${window.areasData ? window.areasData.map(area => `<option value="${area.id}">${area.name}</option>`).join('') : ''}
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Problem Category</label>
                        <select class="form-select form-select-sm" id="history_filter_category">
                            <option value="">All Categories</option>
                            ${window.categoriesData ? window.categoriesData.map(cat => `<option value="${cat.id}">${cat.name}</option>`).join('') : ''}
                        </select>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-primary btn-sm flex-fill" id="history_btn_filter">
                            <i class="fas fa-filter me-1"></i>Apply
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm flex-fill" id="history_btn_reset">
                            <i class="fas fa-redo me-1"></i>Reset
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        $('.history-filter-btn-container').html(historyFilterHTML);

        // Customize search input for History table
        $('#sejarahTable_filter input').attr('placeholder', 'Search...');
        $('#sejarahTable_filter label').contents().filter(function() {
            return this.nodeType === 3; // Text node
        }).remove();
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

    // Global handler for description detail links
    $(document).on('click', 'a.view-description', function(e) {
        e.preventDefault();
        var fullText = $(this).data('description') || '';
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Description',
                html: '<div style="text-align:left;white-space:pre-wrap">' + fullText + '</div>',
                confirmButtonText: 'Close'
            });
        } else {
            alert(fullText);
        }
    });

    // History filter handlers
    $(document).on('click', '#history_btn_filter', function() {
        if ($('#sejarahTable').length) {
            $('#sejarahTable').DataTable().ajax.reload();
        }
    });

    $(document).on('click', '#history_btn_reset', function() {
        $('#history_filter_start_date').val('');
        $('#history_filter_end_date').val('');
        $('#history_filter_area').val('');
        $('#history_filter_category').val('');
        if ($('#sejarahTable').length) {
            $('#sejarahTable').DataTable().ajax.reload();
        }
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