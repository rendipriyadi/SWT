<div class="filter-panel card mb-2 position-relative collapsed">
    <button type="button" class="btn-close position-absolute end-0 top-0 m-1" aria-label="Close" id="closeFilterPanelBtn" style="z-index:10; display:none;"></button>
    <div class="card-header d-flex justify-content-between align-items-center py-2">
        <h5 class="mb-0 fs-6">
            <i class="fas fa-filter me-1"></i>Filter Data
        </h5>
        <button class="btn btn-link btn-sm p-0 toggle-filter" type="button">
            <i class="fas fa-chevron-down filter-toggle-icon"></i>
        </button>
    </div>
    <div class="card-body filter-body p-0">
        <form id="filterForm" class="p-2">
            <div class="row g-2">
                <!-- Date Range -->
                <div class="col-md-6 col-lg-3 mb-1">
                    <label class="form-label small mb-1">Report Date</label>
                    <div class="input-group input-group-sm elegant-date-range">
                        <input type="date" class="form-control form-control-sm filter-control" id="start_date" name="start_date">
                        <span class="input-group-text py-0">to</span>
                        <input type="date" class="form-control form-control-sm filter-control" id="end_date" name="end_date">
                    </div>
                </div>

                <!-- Problem Category -->
                <div class="col-md-6 col-lg-3 mb-1">
                    <label class="form-label small mb-1">Problem Category</label>
                    <select class="form-select form-select-sm filter-control" id="kategori" name="kategori">
                        <option value="">All Category</option>
                        @foreach(\App\Models\ProblemCategory::active()->ordered()->get() as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Deadline (Month) -->
                <div class="col-md-6 col-lg-3 mb-1">
                    <label class="form-label small mb-1">Deadline</label>
                    <select class="form-select form-select-sm filter-control" id="tenggat_bulan" name="tenggat_bulan">
                        <option value="">All Month</option>
                        @foreach(range(1, 12) as $month)
                            <option value="{{ $month }}">{{ date('F', mktime(0, 0, 0, $month, 1)) }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Area Dropdown -->
                <div class="col-md-6 col-lg-3 mb-1">
                    <label class="form-label small mb-1">Area</label>
                    <select class="form-select form-select-sm filter-control" id="area_id" name="area_id">
                        <option value="">All Area</option>
                        @foreach($areas as $area)
                            <option value="{{ $area->id }}">{{ $area->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Station Dropdown -->
                <div class="col-md-6 col-lg-3 mb-1">
                    <label class="form-label small mb-1">Station</label>
                    <select class="form-select form-select-sm filter-control" id="penanggung_jawab_id" name="penanggung_jawab_id">
                        <option value="">All Station</option>
                    </select>
                </div>

                <!-- Status Filter (conditional) -->
                @if(isset($showStatusFilter) && $showStatusFilter)
                <div class="col-md-6 col-lg-3 mb-1">
                    <label class="form-label small mb-1">Status</label>
                    <select class="form-select form-select-sm filter-control" id="status" name="status">
                        <option value="">All Status</option>
                        <option value="Ditugaskan">Assigned</option>
                        <option value="Selesai">Done</option>
                    </select>
                </div>
                @endif
            </div>
                            
            <div class="d-flex justify-content-between mt-2">
                <button type="button" class="btn btn-sm btn-secondary py-0 px-2" id="resetFilter">
                    <i class="fas fa-undo-alt me-1"></i>Reset
                </button>
                <button type="button" class="btn btn-sm btn-primary py-0 px-2" id="applyFilter">
                    <i class="fas fa-search me-1"></i>Apply Filter
                </button>
            </div>
        </form>
    </div>
</div>

<style>
/* Compact styles for filter panel */
.filter-panel .form-control,
.filter-panel .form-select {
    height: calc(1.4em + 0.4rem + 2px);
    padding-top: 0.1rem;
    padding-bottom: 0.1rem;
}

.filter-panel .input-group-text {
    padding-left: 0.3rem;
    padding-right: 0.3rem;
    font-size: 0.72rem;
}

.filter-panel .form-label {
    margin-bottom: 0.1rem;
}
</style>