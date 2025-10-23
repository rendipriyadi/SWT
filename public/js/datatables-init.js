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
            search: "",
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
        scrollX: false,
        scrollCollapse: false,
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
            // No column adjustments needed in single-table mode
        }
    });

    // Dashboard table
    var laporanTable;
    if ($('#laporanTable').length) {
        // Stable in-memory state for creation-date filter
        window.reportsCreatedDateFilter = window.reportsCreatedDateFilter || { start: '', end: '' };
        laporanTable = $('#laporanTable').DataTable({
            serverSide: true,
            ajax: {
                url: $('#laporanTable').data('url'),
                type: 'GET',
                data: function(d) {
                    // Reports: creation-date filter (Tanggal) from stable state
                    var sd = (window.reportsCreatedDateFilter.start || '').trim();
                    var ed = (window.reportsCreatedDateFilter.end || '').trim();
                    var area = ($('#area_id').val() || $('#filter_area').val() || '').trim();
                    var pj = ($('#penanggung_jawab_id').val() || '').trim();
                    var cat = ($('#kategori').val() || $('#filter_category').val() || '').trim();

                    if (sd) d.start_date = sd; // Tanggal mulai
                    if (ed) d.end_date = ed;   // Tanggal akhir
                    if (area) d.area_id = area;
                    if (pj) d.penanggung_jawab_id = pj;
                    if (cat) d.kategori = cat;
                },
                error: function(xhr, status, error) {
                    console.error('laporanTable AJAX error:', status, error, 'status:', xhr.status, 'response:', xhr.responseText);
                }
            },
            // Remove inline date filter button and its handlers
            createdRow: function(row, data) { $(row).addClass('clickable-row'); },
            autoWidth: false, // keep fixed widths from columnDefs/CSS
            scrollX: false,   // use native single table (no cloned header)
            scrollCollapse: false,
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'Tanggal', name: 'Tanggal' },
                { data: 'foto', name: 'foto', orderable: false, searchable: false },
                { data: 'departemen', name: 'area.name' },
                { data: 'problem_category', name: 'problemCategory.name', orderable: false },
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
                { data: 'tenggat_waktu', name: 'tenggat_waktu' },
                { data: 'status', name: 'status', orderable: false, searchable: false },
                { data: 'penyelesaian', name: 'penyelesaian', orderable: false, searchable: false },
                { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
            ],
            order: [[1, 'desc']],
            columnDefs: [
                { targets: 0, width: 50, className: 'text-center align-middle no-wrap' }, // No
                { targets: 1, width: 150, className: 'text-center align-middle no-wrap' }, // Date
                { targets: 2, width: 80, className: 'text-center align-middle no-wrap' }, // Photo
                { targets: 3, width: 180, className: 'text-center align-middle wrap-cell' }, // Area/Station - Can wrap
                { targets: 4, width: 160, className: 'text-center align-middle wrap-cell' }, // Problem Category - Can wrap
                { targets: 5, width: 200, className: 'text-center align-middle wrap-cell' }, // Description - Can wrap
                { targets: 6, width: 120, className: 'text-center align-middle no-wrap deadline-col' }, // Deadline - Increased to 120px
                { targets: 7, width: 120, className: 'text-center align-middle no-wrap status-col' }, // Status
                { targets: 8, width: 120, className: 'text-center align-middle no-wrap completion-cell' }, // Completion
                { targets: 9, width: 100, className: 'text-center align-middle no-wrap action-cell' } // Action
            ],
            drawCallback: function(){ 
                // Do NOT call columns.adjust() to preserve our fixed widths
                // try { this.api().columns.adjust(); } catch(e){} 
            },
            createdRow: function(row, data) { $(row).addClass('clickable-row'); }
        });

        // Mobile Reports Table (simplified columns with collapse)
        var laporanTableMobile;
        if ($('#laporanTableMobile').length) {
            laporanTableMobile = $('#laporanTableMobile').DataTable({
                serverSide: true,
                ajax: {
                    url: $('#laporanTableMobile').data('url'),
                    type: 'GET',
                    data: function(d) {
                        var sd = (window.reportsCreatedDateFilter && window.reportsCreatedDateFilter.start) || '';
                        var ed = (window.reportsCreatedDateFilter && window.reportsCreatedDateFilter.end) || '';
                        var area = ($('#area_id').val() || $('#filter_area').val() || '').trim();
                        var pj = ($('#penanggung_jawab_id').val() || '').trim();
                        var cat = ($('#kategori').val() || $('#filter_category').val() || '').trim();

                        if (sd) d.start_date = sd;
                        if (ed) d.end_date = ed;
                        if (area) d.area_id = area;
                        if (pj) d.penanggung_jawab_id = pj;
                        if (cat) d.kategori = cat;
                    }
                },
                dom: "<'row'<'col-sm-12'tr>><'row'<'col-sm-12'ip>>", // table + pagination + info
                pageLength: 10,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                autoWidth: false,
                scrollX: false,
                scrollCollapse: false,
                columns: [
                    { 
                        data: 'DT_RowIndex', 
                        name: 'DT_RowIndex', 
                        orderable: false, 
                        searchable: false,
                        render: function(data, type, row) {
                            return '<span class="d-flex align-items-center gap-1 ms-2">' + data + '<i class="fas fa-chevron-down mobile-arrow"></i></span>';
                        }
                    },
                    { data: 'Tanggal', name: 'Tanggal' },
                    { data: 'departemen', name: 'area.name' }
                ],
                order: [[1, 'desc']],
                createdRow: function(row, data, dataIndex) {
                    // IMPORTANT: Use dataIndex only for consistency
                    // data.id is the database ID which may not be sequential
                    
                    $(row).addClass('mobile-table-row');
                    // Don't use data-bs-toggle to avoid Bootstrap auto-init
                    $(row).attr('data-bs-target', '#mobile-detail-' + dataIndex);
                    $(row).attr('data-row-index', dataIndex);
                    
                    // Store data in row for later use
                    $(row).data('rowData', data);
                },
                drawCallback: function() {
                    var api = this.api();
                    
                    // Remove old detail rows
                    $('#laporanTableMobile tbody tr.detail-row').remove();
                    
                    // Add detail row after each data row
                    $('#laporanTableMobile tbody tr.mobile-table-row').each(function(index) {
                        var $row = $(this);
                        var data = $row.data('rowData');
                        var dataIndex = $row.attr('data-row-index');
                        
                        if (!data || dataIndex === undefined) {
                            return;
                        }
                        
                        // Build detail content
                        var detailContent = '<div class="mobile-details ps-3 p-3">';
                        
                        if (data.foto) {
                            detailContent += '<div class="mb-2"><strong>Photo:</strong><br><span class="ms-2">' + data.foto + '</span></div>';
                        }
                        detailContent += '<div class="mb-2"><strong>Problem Category:</strong><br><span class="ms-2">' + (data.problem_category || '-') + '</span></div>';
                        
                        var desc = data.deskripsi_masalah || '-';
                        if (desc && desc.length > 100) desc = desc.substring(0, 100) + '...';
                        detailContent += '<div class="mb-2"><strong>Description:</strong><br><span class="ms-2">' + desc + '</span></div>';
                        
                        detailContent += '<div class="mb-2"><strong>Deadline:</strong><br><span class="ms-2">' + (data.tenggat_waktu || '-') + '</span></div>';
                        detailContent += '<div class="mb-2"><strong>Status:</strong><br><span class="ms-2">' + (data.status || '-') + '</span></div>';
                        detailContent += '<div class="mb-2"><strong>Completion:</strong><br><span class="ms-2">' + (data.penyelesaian || '-') + '</span></div>';
                        
                        if (data.aksi) {
                            detailContent += '<div class="mobile-action-buttons mt-3">' + data.aksi + '</div>';
                        }
                        
                        detailContent += '</div>';
                        
                        // Create and insert detail row - start collapsed, use same dataIndex
                        var detailRow = $('<tr class="collapse detail-row" id="mobile-detail-' + dataIndex + '"><td colspan="3" class="p-0">' + detailContent + '</td></tr>');
                        $row.after(detailRow);
                    });
                    
                    // After all rows inserted, ensure all are collapsed and hidden
                    $('#laporanTableMobile tbody tr.detail-row').each(function() {
                        $(this).removeClass('show').css('display', 'none');
                    });
                    
                    // Ensure all data rows have aria-expanded="false"
                    $('#laporanTableMobile tbody tr.mobile-table-row').each(function() {
                        $(this).attr('aria-expanded', 'false');
                        $(this).find('.mobile-arrow').removeClass('rotate-180');
                    });
                }
            });
            
            // Sync desktop search to mobile
            $('#laporanTable_filter input').on('keyup search', function() {
                if (laporanTableMobile) {
                    laporanTableMobile.search(this.value).draw();
                }
            });
            
            // Sync desktop page length to mobile
            $('#laporanTable_length select').on('change', function() {
                if (laporanTableMobile) {
                    laporanTableMobile.page.len($(this).val()).draw();
                }
            });
        }

        // Place DataTables length/search into our header next to Filter/Reset
        function placeReportsControls() {
            try {
                const $header = $('#reportsCreatedDropdownContainer');
                const $length = $('#laporanTable_length');
                const $filter = $('#laporanTable_filter');
                if (!($header.length && $length.length && $filter.length)) return;

                // Ensure header acts as flex row with space between
                $header.addClass('d-flex align-items-center');

                // Create right group container (search + reset + filter)
                let $right = $header.find('.reports-controls-right');
                if (!$right.length) {
                    $right = $('<div class="reports-controls-right d-flex align-items-center gap-2 flex-grow-1"></div>');
                    $header.append($right);
                }

                // Style tweaks
                $length.addClass('mb-0 me-2 flex-shrink-0');
                $filter.removeClass('mt-n4').addClass('mb-0 flex-grow-1 mt-4').css({ flex: '1 1 auto', minWidth: 0 });
                // Remove label text node and add placeholder/sizing
                const $input = $filter.find('input');
                const $label = $filter.find('label');
                $label.contents().filter(function(){ return this.nodeType === 3; }).remove();
                $label.addClass('w-100 d-flex');
                $input.attr('placeholder', 'Search...').addClass('form-control form-control-sm w-100 flex-grow-1 fs-6');

                // Enlarge Show select as well
                const $lengthSelect = $length.find('select');
                $lengthSelect.addClass('form-select form-select-sm fs-6');

                // Move Show to far left (prepend into header)
                if (!$length.data('moved-left')) { $header.prepend($length); $length.data('moved-left', true); }

                // Build right side order: Search | Reset | Filter
                const $resetBtn = $('#reportsCreatedResetExternal').removeClass('mt-n4').addClass('mt-4');
                const $filterBtnWrap = $header.find('> .dropdown');
                const $filterBtn = $('#reportsCreatedBtn').removeClass('mt-n4').addClass('mt-4');
                if ($right.length) {
                    if (!$filter.data('moved-right')) { $right.append($filter); $filter.data('moved-right', true); }
                    if ($resetBtn.length && !$resetBtn.data('moved-right')) { $right.append($resetBtn); $resetBtn.data('moved-right', true); }
                    if ($filterBtnWrap.length && !$filterBtnWrap.data('moved-right')) { $right.append($filterBtnWrap); $filterBtnWrap.data('moved-right', true); }
                }
            } catch(_) {}
        }
        placeReportsControls();
        // Also run on DataTables init (in case of reinit)
        try { $('#laporanTable').on('init.dt', placeReportsControls); } catch(_) {}

        // Use the Blade-provided creation-date dropdown as-is (no relocation)

        // ------- Reports flatpickr + Start-first (same as History) -------
        function ensureFlatpickrAssetsReports(cb){
            if (window.flatpickr) { cb && cb(); return; }
            if (document.getElementById('flatpickr-js-loader')) {
                setTimeout(function(){ if (window.flatpickr) cb && cb(); }, 200);
                return;
            }
            try {
                if (!document.querySelector('link[data-flatpickr-css]')) {
                    var l = document.createElement('link'); l.rel='stylesheet'; l.href='https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css'; l.setAttribute('data-flatpickr-css','1'); document.head.appendChild(l);
                }
                var s = document.createElement('script'); s.src='https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js'; s.id='flatpickr-js-loader'; s.onload=function(){ try{ cb && cb(); }catch(_){}}; document.head.appendChild(s);
            } catch(_) {}
        }

        function initReportsDatepickers(){
            const $start = $('#report_created_start');
            const $end = $('#report_created_end');
            if (!$start.length || !$end.length) return;
            
            // Guard: If already initialized, skip to prevent infinite loop
            if ($start[0]._flatpickr && $end[0]._flatpickr) {
                return;
            }
            
            // Destroy any other pickers and previous instances
            try { if ($start[0]._flatpickr) $start[0]._flatpickr.destroy(); } catch(_) {}
            try { if ($end[0]._flatpickr) $end[0]._flatpickr.destroy(); } catch(_) {}

            const commonOpts = { dateFormat: 'Y-m-d', allowInput: true, disableMobile: true, static: true };
            const startPicker = window.flatpickr($start[0], {
                ...commonOpts,
                onReady: function(sel,str,inst){ try{ inst.setDate($start.val() || null, false); inst._positionCalendar(); }catch(_){}}
            });
            const endPicker = window.flatpickr($end[0], {
                ...commonOpts,
                clickOpens: true
            });

            const syncMin = function(){
                const sd = ($start.val() || '').trim();
                if (sd){
                    try { endPicker.set('minDate', sd); endPicker.jumpToDate(sd, true);} catch(_) {}
                    $end.prop('disabled', false);
                    const ed = ($end.val() || '').trim();
                    if (ed && ed < sd){ $end.val(''); try { endPicker.clear(); } catch(_) {} }
                } else {
                    try { endPicker.set('minDate', null);} catch(_) {}
                    $end.prop('disabled', true).val('');
                    try { endPicker.clear(); } catch(_) {}
                }
            };
            $start.on('change.flatpickrSyncReports', syncMin);
            syncMin();
        }

        let isEnforcing = false; // Flag to prevent recursive calls
        function enforceStartFirst(){
            if (isEnforcing) return; // Prevent re-entry
            isEnforcing = true;
            
            try {
                const $start = $('#report_created_start');
                const $end = $('#report_created_end');
                if (!$start.length || !$end.length) return;
                const sd = ($start.val() || '').trim();
                if (sd){
                    $end.prop('disabled', false).attr('min', sd);
                    try { if ($end[0] && $end[0]._flatpickr){ $end[0]._flatpickr.set('minDate', sd); } } catch(_) {}
                } else {
                    $end.prop('disabled', true).removeAttr('min');
                    // Clear value without triggering events
                    if ($end[0] && $end[0]._flatpickr) {
                        try { 
                            $end[0]._flatpickr.set('minDate', null);
                            $end[0]._flatpickr.setDate(null, false); // false = don't trigger onChange
                        } catch(_) {}
                    }
                    $end.val('');
                }
            } finally {
                isEnforcing = false;
            }
        }
        // Initialize on dropdown open, input focus, and eagerly after ready
        $(document)
            .off('shown.bs.dropdown.reports1')
            .on('shown.bs.dropdown.reports1', '#reportsCreatedBtn', function(){ if (window.flatpickr) { initReportsDatepickers(); enforceStartFirst(); } else { ensureFlatpickrAssetsReports(function(){ initReportsDatepickers(); enforceStartFirst(); }); } })
            .off('shown.bs.dropdown.reports2')
            .on('shown.bs.dropdown.reports2', '#reportsCreatedDropdownContainer .dropdown', function(){ if (window.flatpickr) { initReportsDatepickers(); enforceStartFirst(); } else { ensureFlatpickrAssetsReports(function(){ initReportsDatepickers(); enforceStartFirst(); }); } });
        $(document)
            .off('focus.reports', '#report_created_start, #report_created_end')
            .on('focus.reports', '#report_created_start, #report_created_end', function(){ if (!this._flatpickr){ if (window.flatpickr) { initReportsDatepickers(); enforceStartFirst(); try { this._flatpickr && this._flatpickr.open(); } catch(_) {} } else { ensureFlatpickrAssetsReports(function(){ initReportsDatepickers(); enforceStartFirst(); try { this._flatpickr && this._flatpickr.open(); } catch(_) {} }.bind(this)); } } });
        setTimeout(function(){ if (window.flatpickr) { initReportsDatepickers(); enforceStartFirst(); } else { ensureFlatpickrAssetsReports(function(){ initReportsDatepickers(); enforceStartFirst(); }); } }, 0);
        setTimeout(function(){ var el = document.getElementById('report_created_start'); if (el && !el._flatpickr){ if (window.flatpickr){ initReportsDatepickers(); enforceStartFirst(); } } }, 400);

        // Delegated handlers for creation-date filter

        // Helper: toggle external Reset button visibility
        function toggleExternalResetButton() {
            const btn = document.getElementById('reportsCreatedResetExternal');
            if (!btn) return;
            const hasAny = Boolean((window.reportsCreatedDateFilter && (window.reportsCreatedDateFilter.start || window.reportsCreatedDateFilter.end))
                || ($('#report_created_start').val() || $('#report_created_end').val()));
            if (hasAny) btn.classList.remove('d-none'); else btn.classList.add('d-none');
        }
        // Initialize visibility on load
        toggleExternalResetButton();
        function handleReportApply(e){
            e.preventDefault(); e.stopPropagation();
            const sd = $('#report_created_start').val();
            const ed = $('#report_created_end').val();
            // Silent validation like History
            if (!sd && ed) { $('#report_created_end').val(''); $('#report_created_start').focus(); return; }
            if (sd && ed && ed < sd) { $('#report_created_end').val(''); $('#report_created_end').focus(); return; }
            // Update stable state (only on Apply)
            window.reportsCreatedDateFilter = { start: sd || '', end: ed || '' };
            toggleExternalResetButton();
            try {
                if (typeof laporanTable !== 'undefined') {
                    laporanTable.order([1,'asc']).ajax.reload(null, true);
                } else if ($.fn.DataTable.isDataTable('#laporanTable')) {
                    $('#laporanTable').DataTable().order([1,'asc']).ajax.reload(null, true);
                }
                if (typeof laporanTableMobile !== 'undefined') {
                    laporanTableMobile.order([1,'asc']).ajax.reload(null, true);
                }
            } catch(_) {}
            try { bootstrap.Dropdown.getOrCreateInstance(document.getElementById('reportsCreatedBtn')).hide(); } catch(_) {}
        }
        // Bind multiple triggers to avoid dropdown swallowing the click
        $(document)
            .off('click', '#report_created_apply')
            .on('click', '#report_created_apply', handleReportApply)
            .off('mousedown', '#report_created_apply')
            .on('mousedown', '#report_created_apply', function(e){ if (e.which === 1) handleReportApply(e); })
            .off('touchstart', '#report_created_apply')
            .on('touchstart', '#report_created_apply', handleReportApply);
        $(document).off('click', '#report_created_reset, #reportsCreatedResetExternal').on('click', '#report_created_reset, #reportsCreatedResetExternal', function(e){
            e.preventDefault(); e.stopPropagation();
            const s = document.getElementById('report_created_start');
            const e2 = document.getElementById('report_created_end');
            if (s) { s.value = ''; try { s.valueAsDate = null; } catch(_) {} }
            if (e2) { e2.value = ''; try { e2.valueAsDate = null; } catch(_) {} }
            // Clear stable state
            window.reportsCreatedDateFilter = { start: '', end: '' };
            enforceStartFirst();
            toggleExternalResetButton();
            try {
                if ($.fn.DataTable.isDataTable('#laporanTable')) {
                    $('#laporanTable').DataTable().order([1,'desc']).ajax.reload(null, true);
                } else if (typeof laporanTable !== 'undefined') {
                    laporanTable.order([1,'desc']).ajax.reload(null, true);
                }
                if (typeof laporanTableMobile !== 'undefined') {
                    laporanTableMobile.order([1,'desc']).ajax.reload(null, true);
                }
            } catch(_) {}
            try { bootstrap.Dropdown.getOrCreateInstance(document.getElementById('reportsCreatedBtn')).hide(); } catch(_) {}
        });

        // On change: only enforce constraints; do NOT update state or reload automatically. Update Reset button visibility.
        $(document).off('change', '#report_created_start, #report_created_end').on('change', '#report_created_start, #report_created_end', function(){
            enforceStartFirst();
            toggleExternalResetButton();
        });
        $(document).off('keydown', '#report_created_start, #report_created_end').on('keydown', '#report_created_start, #report_created_end', function(e){
            if (e.key === 'Enter') {
                e.preventDefault();
                $('#report_created_apply').trigger('click');
            }
        });

        // Remove any interception of datepicker Clear; allow normal behavior without auto-reset

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

            const rowData = laporanTable.row(this).data();
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
            // Do NOT call columns.adjust() to preserve our fixed widths
            // try { laporanTable.columns.adjust().draw(false); } catch (e) {}
        };
        $(window).on('resize', adjustDashboardTable);
        window.addEventListener('sidebar:toggled', adjustDashboardTable);

    }

    // Apply filter from filter panel component (GLOBAL - outside if block)
    $(document).on('click', '#applyFilter', function() {
        if ($('#laporanTable').length && typeof laporanTable !== 'undefined') {
            laporanTable.ajax.reload();
        }
        if ($('#laporanTableMobile').length && typeof laporanTableMobile !== 'undefined') {
            laporanTableMobile.ajax.reload();
        }
        if ($('#sejarahTable').length && typeof sejarahTable !== 'undefined') {
            sejarahTable.ajax.reload();
        }
        if ($('#sejarahTableMobile').length && typeof sejarahTableMobile !== 'undefined') {
            sejarahTableMobile.ajax.reload();
        }
    });

    // Reset filter from filter panel component (GLOBAL - outside if block)
    $(document).on('click', '#resetFilter', function() {
        
        $('#start_date').val('');
        $('#end_date').val('');
        $('#area_id').val('');
        $('#penanggung_jawab_id').val('').html('<option value="">All Station</option>');
        $('#kategori').val('');
        $('#tenggat_bulan').val('');
        $('#status').val('');
        
        if ($('#laporanTable').length && typeof laporanTable !== 'undefined') {
            laporanTable.ajax.reload();
        }
        if ($('#laporanTableMobile').length && typeof laporanTableMobile !== 'undefined') {
            laporanTableMobile.ajax.reload();
        }
        if ($('#sejarahTable').length && typeof sejarahTable !== 'undefined') {
            sejarahTable.ajax.reload();
        }
        if ($('#sejarahTableMobile').length && typeof sejarahTableMobile !== 'undefined') {
            sejarahTableMobile.ajax.reload();
        }
    });

    // History table
    if ($('#sejarahTable').length) {
        const isMobile = false; // force horizontal scroll always
        // Stable in-memory state for History date filter (like Reports)
        window.sejarahDateFilter = window.sejarahDateFilter || { start: '', end: '' };
        var sejarahTable = $('#sejarahTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: $('#sejarahTable').data('url'),
                type: 'GET',
                data: function(d) {
                    // Use History's in-memory date state so it persists regardless of DOM
                    var sd = (window.sejarahDateFilter.start || '').trim();
                    var ed = (window.sejarahDateFilter.end || '').trim();
                    if (sd) d.start_date = sd;
                    if (ed) d.end_date = ed;
                    // Hint backend to order created_at by asc when filter active, else desc
                    d.created_order = (sd || ed) ? 'asc' : 'desc';
                    // Keep other global filters (if present in the page)
                    d.area_id = $('#area_id').val();
                    d.penanggung_jawab_id = $('#penanggung_jawab_id').val();
                    d.kategori = $('#kategori').val();
                    d.tenggat_bulan = $('#tenggat_bulan').val();
                    d.status = $('#status').val();
                }
            },
            // Use global default DOM; we'll relocate controls into Blade header
            dom: "<'row mb-3'<'col-sm-6'l><'col-sm-6 text-end'f>><'row'<'col-sm-12'tr>><'row'<'col-sm-12'ip>>",
            // Use global config for responsive, autoWidth, scrollX, scrollCollapse, language
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'Tanggal', name: 'Tanggal' },
                { data: 'foto', name: 'foto', orderable: false, searchable: false },
                { data: 'departemen', name: 'area.name' },
                { data: 'problem_category', name: 'problemCategory.name', orderable: false },
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
                { data: 'tenggat_waktu', name: 'tenggat_waktu' },
                { data: 'status', name: 'status', orderable: false, searchable: false },
                { data: 'penyelesaian', name: 'penyelesaian', orderable: false, searchable: false },
                { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
            ],
            order: [[1, 'desc']],
            drawCallback: function(){ try { this.api().columns.adjust(); } catch(e){} },
            createdRow: function(row, data) {
                $(row).addClass('clickable-row');
            }
        });

        // Mobile History Table
        if ($('#sejarahTableMobile').length) {
            var sejarahTableMobile = $('#sejarahTableMobile').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: $('#sejarahTableMobile').data('url'),
                    type: 'GET',
                    data: function(d) {
                        // Use History's in-memory date state
                        var sd = (window.sejarahDateFilter.start || '').trim();
                        var ed = (window.sejarahDateFilter.end || '').trim();
                        if (sd) d.start_date = sd;
                        if (ed) d.end_date = ed;
                        d.created_order = (sd || ed) ? 'asc' : 'desc';
                        d.area_id = $('#area_id').val();
                        d.penanggung_jawab_id = $('#penanggung_jawab_id').val();
                        d.kategori = $('#kategori').val();
                        d.tenggat_bulan = $('#tenggat_bulan').val();
                        d.status = $('#status').val();
                    }
                },
                dom: 'tip',
                columns: [
                    { 
                        data: 'DT_RowIndex', 
                        name: 'DT_RowIndex', 
                        orderable: false, 
                        searchable: false,
                        render: function(data, type, row) {
                            return '<span class="d-flex align-items-center gap-1 ms-2">' + data + '<i class="fas fa-chevron-down mobile-arrow"></i></span>';
                        }
                    },
                    { data: 'Tanggal', name: 'Tanggal' },
                    { data: 'departemen', name: 'area.name' }
                ],
                order: [[1, 'desc']],
                createdRow: function(row, data, dataIndex) {
                    $(row).addClass('mobile-table-row');
                    $(row).attr('data-bs-target', '#mobile-detail-' + dataIndex);
                    $(row).attr('data-row-index', dataIndex);
                    $(row).data('rowData', data);
                },
                drawCallback: function() {
                    var api = this.api();
                    
                    // Remove old detail rows
                    $('#sejarahTableMobile tbody tr.detail-row').remove();
                    
                    // Add detail row after each data row
                    $('#sejarahTableMobile tbody tr.mobile-table-row').each(function(index) {
                        var $row = $(this);
                        var data = $row.data('rowData');
                        var dataIndex = $row.attr('data-row-index');
                        
                        if (!data || dataIndex === undefined) {
                            return;
                        }
                        
                        // Build detail content
                        var detailContent = '<div class="mobile-details ps-3 p-3">';
                        
                        if (data.foto) {
                            detailContent += '<div class="mb-2"><strong>Photo:</strong><br><span class="ms-2">' + data.foto + '</span></div>';
                        }
                        detailContent += '<div class="mb-2"><strong>Problem Category:</strong><br><span class="ms-2">' + (data.problem_category || '-') + '</span></div>';
                        
                        var desc = data.deskripsi_masalah || '-';
                        if (desc && desc.length > 100) desc = desc.substring(0, 100) + '...';
                        detailContent += '<div class="mb-2"><strong>Description:</strong><br><span class="ms-2">' + desc + '</span></div>';
                        
                        detailContent += '<div class="mb-2"><strong>Deadline:</strong><br><span class="ms-2">' + (data.tenggat_waktu || '-') + '</span></div>';
                        detailContent += '<div class="mb-2"><strong>Status:</strong><br><span class="ms-2">' + (data.status || '-') + '</span></div>';
                        detailContent += '<div class="mb-2"><strong>Completion:</strong><br><span class="ms-2">' + (data.penyelesaian || '-') + '</span></div>';
                        
                        if (data.aksi) {
                            detailContent += '<div class="mobile-action-buttons mt-3">' + data.aksi + '</div>';
                        }
                        
                        detailContent += '</div>';
                        
                        // Create and insert detail row
                        var detailRow = $('<tr class="collapse detail-row" id="mobile-detail-' + dataIndex + '"><td colspan="3" class="p-0">' + detailContent + '</td></tr>');
                        $row.after(detailRow);
                    });
                    
                    // Ensure all detail rows are collapsed and hidden
                    $('#sejarahTableMobile tbody tr.detail-row').each(function() {
                        $(this).removeClass('show').css('display', 'none');
                    });
                    
                    // Ensure all data rows have proper state
                    $('#sejarahTableMobile tbody tr.mobile-table-row').each(function() {
                        $(this).find('.mobile-arrow').removeClass('rotate-180');
                    });
                }
            });
            
            // Sync desktop search to mobile
            $('#sejarahTable_filter input').on('keyup search', function() {
                if (sejarahTableMobile) {
                    sejarahTableMobile.search(this.value).draw();
                }
            });
        }

        // Enforce order based on filter state before every AJAX call
        try {
            $('#sejarahTable').off('preXhr.dt.historyOrder').on('preXhr.dt.historyOrder', function(){
                const hasFilter = Boolean((window.sejarahDateFilter && (window.sejarahDateFilter.start || window.sejarahDateFilter.end))
                    || ($('#history_created_start').val() || $('#history_created_end').val()));
                if (hasFilter) {
                    sejarahTable.order([1,'asc']);
                } else {
                    sejarahTable.order([1,'desc']);
                }
            });
        } catch(_) {}

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

        // Blade already provides header controls; just relocate DataTables controls below into it

        // Place History controls similar to Reports: Show left, Search expands, Reset & Filter right
        function placeHistoryControls() {
            try {
                const $header = $('#historyControlsContainer');
                const $length = $('#sejarahTable_length');
                const $filter = $('#sejarahTable_filter');
                if (!($header.length && $length.length && $filter.length)) return;

                // Ensure header is flex row
                $header.addClass('d-flex align-items-center');

                // Right group
                let $right = $header.find('.history-controls-right');
                if (!$right.length) {
                    $right = $('<div class="history-controls-right d-flex align-items-center gap-2 flex-grow-1"></div>');
                    $header.append($right);
                }

                // Style
                $length.addClass('mb-0 me-2 flex-shrink-0');
                $filter.addClass('mb-0 flex-grow-1 mt-4').css({ flex: '1 1 auto', minWidth: 0 });
                const $input = $filter.find('input');
                const $label = $filter.find('label');
                $label.contents().filter(function(){ return this.nodeType === 3; }).remove();
                $label.addClass('w-100 d-flex');
                $input.attr('placeholder', 'Search...').addClass('form-control form-control-sm w-100 flex-grow-1 fs-6');
                const $lengthSelect = $length.find('select');
                $lengthSelect.addClass('form-select form-select-sm fs-6');

                // Left: Show
                if (!$length.data('moved-left')) { $header.prepend($length); $length.data('moved-left', true); }
                // Right: Search | Reset | Filter
                if (!$filter.data('moved-right')) { $right.append($filter); $filter.data('moved-right', true); }
                const $resetBtn = $('#historyResetExternal');
                if ($resetBtn.length && !$resetBtn.data('moved-right')) { $right.append($resetBtn); $resetBtn.data('moved-right', true); }
                const $filterWrap = $header.find('> .dropdown');
                if ($filterWrap.length && !$filterWrap.data('moved-right')) { $right.append($filterWrap); $filterWrap.data('moved-right', true); }
            } catch(_) {}
        }
        placeHistoryControls();
        try { $('#sejarahTable').on('init.dt', placeHistoryControls); } catch(_) {}

        // (Reverted) No helper: use direct Bootstrap hide on the button when needed

        // Build Export PDF URL with current filters
        function buildHistoryExportUrl() {
            const base = (document.getElementById('historyExportPdf') || {}).getAttribute('href') || '/sejarah/download';
            const params = new URLSearchParams();
            // Use in-memory state first, fallback to inputs
            const sd = (window.sejarahDateFilter && window.sejarahDateFilter.start) || ($('#history_created_start').val() || '').trim();
            const ed = (window.sejarahDateFilter && window.sejarahDateFilter.end) || ($('#history_created_end').val() || '').trim();
            if (sd) params.set('start_date', sd);
            if (ed) params.set('end_date', ed);
            // Include other filters on the page for consistency
            const area = ($('#area_id').val() || '').trim();
            const pj = ($('#penanggung_jawab_id').val() || '').trim();
            const cat = ($('#kategori').val() || '').trim();
            const month = ($('#tenggat_bulan').val() || '').trim();
            const status = ($('#status').val() || '').trim();
            if (area) params.set('area_id', area);
            if (pj) params.set('penanggung_jawab_id', pj);
            if (cat) params.set('kategori', cat);
            if (month) params.set('tenggat_bulan', month);
            if (status) params.set('status', status);
            const url = base + (base.includes('?') ? '&' : '?') + params.toString();
            return url;
        }
        // Attach click handler to Export PDF
        $(document).off('click', '#historyExportPdf').on('click', '#historyExportPdf', function(e){
            // If no filters selected, let default link work
            e.preventDefault();
            const url = buildHistoryExportUrl();
            try { window.location.href = url; } catch(_) { this.setAttribute('href', url); this.click(); }
        });

        // Ensure flatpickr assets are available (dynamic loader as fallback if CDN not yet loaded)
        function ensureFlatpickrAssets(cb){
            if (window.flatpickr) { cb && cb(); return; }
            // Prevent multiple loads
            if (document.getElementById('flatpickr-js-loader')) { 
                // Wait a bit and retry
                setTimeout(function(){ if (window.flatpickr) cb && cb(); }, 200);
                return;
            }
            try {
                // CSS
                if (!document.querySelector('link[data-flatpickr-css]')) {
                    var l = document.createElement('link');
                    l.rel = 'stylesheet';
                    l.href = 'https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css';
                    l.setAttribute('data-flatpickr-css','1');
                    document.head.appendChild(l);
                }
                // JS
                var s = document.createElement('script');
                s.src = 'https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js';
                s.id = 'flatpickr-js-loader';
                s.onload = function(){ try { cb && cb(); } catch(_) {} };
                document.head.appendChild(s);
            } catch(_) { /* ignore */ }
        }

        // Initialize datepicker widgets for History using ONLY flatpickr (avoid duplicates)
        function initHistoryDatepickers() {
            const $start = $('#history_created_start');
            const $end = $('#history_created_end');
            if (!$start.length || !$end.length) return;
            
            // Guard: If already initialized, skip to prevent infinite loop
            if ($start[0]._flatpickr && $end[0]._flatpickr) {
                return;
            }

            // 1) Destroy any previously attached non-flatpickr pickers to prevent duplicates
            try { // bootstrap-datepicker
                if (typeof $.fn.datepicker === 'function') {
                    if ($start.data('datepicker')) { try { $start.datepicker('destroy'); } catch(_) {} }
                    if ($end.data('datepicker')) { try { $end.datepicker('destroy'); } catch(_) {} }
                }
            } catch(_) {}
            try { // vanillajs-datepicker
                if (window.Datepicker) {
                    const sInst = window.Datepicker.getInstance($start[0]);
                    const eInst = window.Datepicker.getInstance($end[0]);
                    if (sInst && typeof sInst.destroy === 'function') sInst.destroy();
                    if (eInst && typeof eInst.destroy === 'function') eInst.destroy();
                }
            } catch(_) {}
            try { // Litepicker
                if ($start[0] && $start[0]._litepicker && typeof $start[0]._litepicker.destroy === 'function') $start[0]._litepicker.destroy();
                if ($end[0] && $end[0]._litepicker && typeof $end[0]._litepicker.destroy === 'function') $end[0]._litepicker.destroy();
                $start[0]._litepicker = null; $end[0]._litepicker = null;
            } catch(_) {}

            // 2) Initialize flatpickr only
            var doInit = function(){
                // Destroy existing flatpickr first to avoid duplicate instances
                try { if ($start[0]._flatpickr) $start[0]._flatpickr.destroy(); } catch(_) {}
                try { if ($end[0]._flatpickr) $end[0]._flatpickr.destroy(); } catch(_) {}

                // Render calendars statically under inputs to avoid off-screen placement inside dropdown
                const commonOpts = { dateFormat: 'Y-m-d', allowInput: true, disableMobile: true, static: true };
                const startPicker = window.flatpickr($start[0], {
                    ...commonOpts,
                    onReady: function(selDates, str, inst){ try { inst.setDate($start.val() || null, false); inst._positionCalendar(); } catch(_) {} },
                    onOpen: function(selDates, str, inst){ try { inst._positionCalendar(); } catch(_) {} }
                });
                const endPicker = window.flatpickr($end[0], {
                    ...commonOpts,
                    clickOpens: true,
                    onOpen: function(selDates, str, inst){ try { inst._positionCalendar(); } catch(_) {} }
                });

                // Sync minDate on End based on Start
                const syncMin = function(){
                    const sd = ($start.val() || '').trim();
                    if (sd) {
                        try { endPicker.set('minDate', sd); endPicker.jumpToDate(sd, true); } catch(_) {}
                        $end.prop('disabled', false);
                        const ed = ($end.val() || '').trim();
                        if (ed && ed < sd) { $end.val(''); try { endPicker.clear(); } catch(_) {} }
                    } else {
                        try { endPicker.set('minDate', null); } catch(_) {}
                        $end.prop('disabled', true).val('');
                        try { endPicker.clear(); } catch(_) {}
                    }
                };
                $start.on('change.flatpickrSync', syncMin);
                syncMin();
            };
            if (window.flatpickr) doInit(); else ensureFlatpickrAssets(doInit);
        }

        // Enforce Start-first for History: End min follows Start and End disabled until Start set
        let isEnforcingHistory = false; // Flag to prevent recursive calls
        function enforceHistoryStartFirst() {
            if (isEnforcingHistory) return; // Prevent re-entry
            isEnforcingHistory = true;
            
            try {
                const $start = $('#history_created_start');
                const $end = $('#history_created_end');
                if (!$start.length || !$end.length) return;
                const sd = ($start.val() || '').trim();
                if (sd) {
                    $end.prop('disabled', false).attr('min', sd);
                    const ed = ($end.val() || '').trim();
                    if (ed && ed < sd) { $end.val(''); }
                    // Flatpickr-only sync
                    try {
                        if ($end[0] && $end[0]._flatpickr) {
                            $end[0]._flatpickr.set('minDate', sd);
                            try { $end[0]._flatpickr.jumpToDate(sd); } catch(_) {}
                        }
                    } catch(_) {}
                } else {
                    $end.prop('disabled', true).removeAttr('min');
                    // Clear flatpickr constraint without triggering events
                    if ($end[0] && $end[0]._flatpickr) {
                        try { 
                            $end[0]._flatpickr.set('minDate', null);
                            $end[0]._flatpickr.setDate(null, false); // false = don't trigger onChange
                        } catch(_) {}
                    }
                    $end.val('');
                }
            } finally {
                isEnforcingHistory = false;
            }
        }

        // Toggle external Reset button visibility for History
        function toggleHistoryResetButton() {
            const btn = document.getElementById('historyResetExternal');
            if (!btn) return;
            const hasAny = Boolean(($('#history_created_start').val() || $('#history_created_end').val()) || (window.sejarahDateFilter.start || window.sejarahDateFilter.end));
            if (hasAny) btn.classList.remove('d-none'); else btn.classList.add('d-none');
        }
        // When dropdown opens, ensure pickers are initialized and constraints applied
        // Bind to both the toggle button and the dropdown wrapper to be safe across Bootstrap versions
        $(document)
            .off('shown.bs.dropdown.history1')
            .on('shown.bs.dropdown.history1', '#historyCreatedBtn', function(){ initHistoryDatepickers(); enforceHistoryStartFirst(); })
            .off('shown.bs.dropdown.history2')
            .on('shown.bs.dropdown.history2', '#historyControlsContainer .dropdown', function(){ initHistoryDatepickers(); enforceHistoryStartFirst(); });
        // Fallback: if user focuses an input and flatpickr is not yet initialized, init lazily
        $(document)
            .off('focus.history', '#history_created_start, #history_created_end')
            .on('focus.history', '#history_created_start, #history_created_end', function(){
                const el = this;
                if (!el._flatpickr) {
                    initHistoryDatepickers();
                    enforceHistoryStartFirst();
                    // Open immediately after lazy init for better UX
                    try { if (el._flatpickr) el._flatpickr.open(); } catch(_) {}
                }
            });
        enforceHistoryStartFirst();
        toggleHistoryResetButton();
        // External Reset click -> clear, reload latest, hide button, close dropdown if open
        $(document).off('click', '#historyResetExternal').on('click', '#historyResetExternal', function(e){
            e.preventDefault(); e.stopPropagation();
            $('#history_created_start').val('');
            $('#history_created_end').val('');
            window.sejarahDateFilter = { start: '', end: '' };
            enforceHistoryStartFirst();
            toggleHistoryResetButton();
            try { 
                if ($.fn.DataTable.isDataTable('#sejarahTable')) { 
                    $('#sejarahTable').DataTable().order([1,'desc']).ajax.reload(null, true); 
                } 
                if ($.fn.DataTable.isDataTable('#sejarahTableMobile')) { 
                    $('#sejarahTableMobile').DataTable().order([1,'desc']).ajax.reload(null, true); 
                } 
            } catch(_) {}
            try { bootstrap.Dropdown.getOrCreateInstance(document.getElementById('historyCreatedBtn')).hide(); } catch(_) {}
        });
        // Toggle button when inputs change
        // When Start changes, update End constraints; when End changes, just update Reset visibility
        $(document)
            .off('change', '#history_created_start')
            .on('change', '#history_created_start', function(){ enforceHistoryStartFirst(); toggleHistoryResetButton(); })
            .off('change', '#history_created_end')
            .on('change', '#history_created_end', function(){ toggleHistoryResetButton(); });

        // Apply handler for History (match Reports behavior)
        function handleHistoryApply(e){
            e.preventDefault(); e.stopPropagation();
            const $start = $('#history_created_start');
            const $end = $('#history_created_end');
            const sd = ($start.val() || '').trim();
            const ed = ($end.val() || '').trim();
            // Require Start first: if End set without Start, just focus Start and block silently
            if (!sd && ed) { $end.val(''); $start.focus(); return; }
            // If both set but End < Start, clear End and focus End without alert
            if (sd && ed && ed < sd) { $end.val(''); $end.focus(); return; }
            // Update state
            window.sejarahDateFilter = { start: sd || '', end: ed || '' };
            toggleHistoryResetButton();
            try {
                if ($.fn.DataTable.isDataTable('#sejarahTable')) {
                    $('#sejarahTable').DataTable().order([1,'asc']).ajax.reload(null, true);
                }
                if ($.fn.DataTable.isDataTable('#sejarahTableMobile')) {
                    $('#sejarahTableMobile').DataTable().order([1,'asc']).ajax.reload(null, true);
                }
            } catch(_) {}
            // Close dropdown like Reports
            try { bootstrap.Dropdown.getOrCreateInstance(document.getElementById('historyCreatedBtn')).hide(); } catch(_) {}
        }
        $(document)
            .off('click', '#history_created_apply')
            .on('click', '#history_created_apply', handleHistoryApply)
            .off('mousedown', '#history_created_apply')
            .on('mousedown', '#history_created_apply', function(e){ if (e.which === 1) handleHistoryApply(e); })
            .off('touchstart', '#history_created_apply')
            .on('touchstart', '#history_created_apply', handleHistoryApply);
        // Enter-to-apply on inputs (only when valid)
        $(document).off('keydown', '#history_created_start, #history_created_end').on('keydown', '#history_created_start, #history_created_end', function(e){
            if (e.key === 'Enter') {
                e.preventDefault();
                enforceHistoryStartFirst();
                $('#history_created_apply').trigger('click');
            }
        });
    }

    // (Removed) Reports floating filter helpers

    // Global handler for description detail links
    $(document).on('click', 'a.view-description', function(e) {
        e.preventDefault();
        var fullText = $(this).data('description') || '';
        if (typeof Swal !== 'undefined') {
            const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
            const theme = isDark ? {
                background: '#1e1e1e',
                color: '#e0e0e0',
                confirmButtonColor: '#0d6efd'
            } : {
                background: '#ffffff',
                color: '#212529',
                confirmButtonColor: '#0d6efd'
            };
            
            Swal.fire({
                title: 'Description',
                html: '<div style="text-align:left;white-space:pre-wrap">' + fullText + '</div>',
                confirmButtonText: 'Close',
                ...theme
            });
        } else {
            alert(fullText);
        }
    });
});

// Format tanggal untuk Indonesia
function formatTanggalIndonesia(tanggal) {
    if (!tanggal) return '-';
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(tanggal).toLocaleDateString('id-ID', options);
}