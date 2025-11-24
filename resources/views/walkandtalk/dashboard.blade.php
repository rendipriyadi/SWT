@extends('layouts.main')

@section('title', 'Main Dashboard')


@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="dashboard-header mb-4">
        <div>
            <h1 class="mb-1">Dashboard</h1>
            <p class="text-muted mb-0">Safety Walk and Talk Management System</p>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center filter-header-clickable"
             data-bs-toggle="collapse" data-bs-target="#filterCollapse" aria-expanded="false" aria-controls="filterCollapse"
             style="cursor: pointer;">
            <h5 class="card-title mb-0">
                <i class="fas fa-filter me-2 text-primary"></i>Filters
            </h5>
            <i class="fas fa-chevron-down text-primary" id="filterToggleIcon"></i>
        </div>
        <div class="collapse" id="filterCollapse">
            <div class="card-body">
                <form id="dashboardFilters" method="GET" action="{{ route('dashboard') }}">
                <div class="row g-3 align-items-end">
                    <!-- Category Filter -->
                    <div class="col-lg-2 col-md-3 col-sm-6">
                        <label for="category_filter" class="form-label fw-semibold mb-2">
                            <i class="fas fa-tag me-1 text-primary"></i>Filter by Category
                        </label>
                        <select class="form-select filter-dropdown" id="category_filter" name="category_id">
                            <option value="">All Categories</option>
                            @foreach(\App\Models\ProblemCategory::active()->ordered()->get() as $category)
                                <option value="{{ $category->id }}" 
                                        {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Month Filter -->
                    <div class="col-lg-2 col-md-3 col-sm-6">
                        <label for="month_filter" class="form-label fw-semibold mb-2">
                            <i class="fas fa-calendar-alt me-1 text-info"></i>Month
                        </label>
                        <select class="form-select filter-dropdown" id="month_filter" name="month">
                            <option value="">All Months</option>
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ request('month') == $i ? 'selected' : '' }}>
                                    {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <!-- Year Filter -->
                    <div class="col-lg-2 col-md-3 col-sm-6">
                        <label for="year_filter" class="form-label fw-semibold mb-2">
                            <i class="fas fa-calendar me-1 text-info"></i>Year
                        </label>
                        <select class="form-select filter-dropdown" id="year_filter" name="year">
                            <option value="">All Years</option>
                            @for($year = date('Y'); $year >= 2020; $year--)
                                <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <!-- Date Range Dropdown -->
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label fw-semibold mb-2">
                            <i class="fas fa-calendar-alt me-1 text-warning"></i>Date Range
                        </label>
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary w-100 text-start text-truncate d-flex justify-content-between align-items-center" type="button" 
                                    id="dateRangeDropdown" data-bs-toggle="dropdown" data-bs-auto-close="outside" 
                                    aria-expanded="false" style="overflow: hidden; border: 1px solid #dee2e6; border-radius: 0.375rem;">
                                <span id="dateRangeLabel" class="flex-grow-1 text-truncate">
                                    @if(request('start_date') || request('end_date'))
                                        @if(request('start_date') && request('end_date'))
                                            {{ \Carbon\Carbon::parse(request('start_date'))->format('d/m/Y') }} - {{ \Carbon\Carbon::parse(request('end_date'))->format('d/m/Y') }}
                                        @elseif(request('start_date'))
                                            From {{ \Carbon\Carbon::parse(request('start_date'))->format('d/m/Y') }}
                                        @else
                                            Until {{ \Carbon\Carbon::parse(request('end_date'))->format('d/m/Y') }}
                                        @endif
                                    @else
                                        Select date range
                                    @endif
                                </span>
                                <i class="fas fa-chevron-down ms-2"></i>
                            </button>
                            <div class="dropdown-menu p-3" aria-labelledby="dateRangeDropdown" style="min-width: 350px;" onclick="event.stopPropagation();">
                                <h6 class="dropdown-header px-0">Filter by Created Date</h6>
                                <div class="row g-2 mb-2">
                                    <div class="col-12 col-md-6">
                                        <label class="form-label small fw-bold" for="dashboard_start_date">Start</label>
                                        <input type="date" id="dashboard_start_date" class="form-control form-control-sm" 
                                               value="{{ request('start_date') }}" 
                                               data-date-format="dd/mm/yyyy" />
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label small fw-bold" for="dashboard_end_date">End</label>
                                        <input type="date" id="dashboard_end_date" class="form-control form-control-sm" 
                                               value="{{ request('end_date') }}" 
                                               data-date-format="dd/mm/yyyy"
                                               {{ !request('start_date') ? 'disabled' : '' }} />
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end gap-2">
                                    <button type="button" class="btn btn-primary btn-sm" id="dashboard_date_apply">
                                        <i class="fas fa-filter me-1"></i>Apply
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filter Actions -->
                    <div class="col-lg-3 col-md-6">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="fas fa-search me-1"></i>Filter
                            </button>
                            @php
                                $hasFilters = request('category_id') || request('month') || request('year') || request('start_date') || request('end_date');
                            @endphp
                            @if($hasFilters)
                                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary flex-fill">
                                    <i class="fas fa-times me-1"></i>Clear
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Active Filters Display -->
                @php
                    $hasActiveFilters = request('category_id') || request('month') || request('year') || request('start_date') || request('end_date');
                @endphp
                @if($hasActiveFilters)
                    <div class="mt-3 pt-3 border-top">
                        <div class="d-flex align-items-center flex-wrap gap-2">
                            <span class="text-muted fw-semibold">Active Filters:</span>

                            @if(request('category_id'))
                                @php
                                    $selectedCategory = \App\Models\ProblemCategory::find(request('category_id'));
                                @endphp
                                @if($selectedCategory)
                                    <span class="badge" style="background-color: {{ $selectedCategory->color }}; color: white;">
                                        {{ $selectedCategory->name }}
                                    </span>
                                @endif
                            @endif

                            @if(request('month'))
                                <span class="badge bg-info">
                                    {{ DateTime::createFromFormat('!m', request('month'))->format('F') }}
                                </span>
                            @endif

                            @if(request('year'))
                                <span class="badge bg-secondary">
                                    {{ request('year') }}
                                </span>
                            @endif

                            @if(request('start_date') || request('end_date'))
                                <span class="badge bg-warning">
                                    @if(request('start_date') && request('end_date'))
                                        {{ \Carbon\Carbon::parse(request('start_date'))->format('M d, Y') }} - {{ \Carbon\Carbon::parse(request('end_date'))->format('M d, Y') }}
                                    @elseif(request('start_date'))
                                        From: {{ \Carbon\Carbon::parse(request('start_date'))->format('M d, Y') }}
                                    @else
                                        Until: {{ \Carbon\Carbon::parse(request('end_date'))->format('M d, Y') }}
                                    @endif
                                </span>
                            @endif
                        </div>
                    </div>
                @endif
                </form>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-lg-4 col-md-6">
            <div class="stats-card">
                <div class="d-flex align-items-center">
                    <div class="stats-icon bg-primary text-white rounded-circle me-3">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div>
                        <h3 class="mb-1">Total Reports</h3>
                        <div class="number text-primary">{{ $totalLaporan }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="stats-card">
                <div class="d-flex align-items-center">
                    <div class="stats-icon bg-warning text-white rounded-circle me-3">
                        <i class="fas fa-cog fa-spin"></i>
                    </div>
                    <div>
                        <h3 class="mb-1">In Progress Reports</h3>
                        <div class="number text-warning">{{ $laporanInProgress }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="stats-card">
                <div class="d-flex align-items-center">
                    <div class="stats-icon bg-success text-white rounded-circle me-3">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div>
                        <h3 class="mb-1">Completed Reports</h3>
                        <div class="number text-success">{{ $laporanSelesai }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row g-4 mb-4">
        <!-- Grafik Garis - Laporan per Bulan -->
        <div class="col-lg-8">
    <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-line me-2"></i>
                        Monthly Reports In a Year
                    </h5>
            </div>
                <div class="card-body">
                    <canvas id="laporanPerBulanChart" height="300"></canvas>
            </div>
          </div>
        </div>

        <!-- Grafik Pie - Category per Bulan -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-pie me-2"></i>
                        This Month Problem Report
                    </h5>
    </div>
                <div class="card-body">
                    <canvas id="categoryPerBulanChart" height="300"></canvas>
          </div>
        </div>
      </div>
    </div>

    <!-- Grafik Batang - Area yang Melapor per Bulan -->
    <div class="row g-4 mb-4">
        <div class="col-12">
    <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-bar me-2"></i>
                        Reports by Area In a Year
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="areaPerBulanChart" height="400"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Reports Section -->
    <div class="row g-4 mb-4">
        <!-- Recent Assigned Reports Card -->
        <div class="col-lg-6 col-md-12">
            <div class="card recent-reports-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-clipboard-list me-2 text-warning"></i>
                        Recent Assigned Reports
                    </h5>
                    <span class="badge text-dark" style="background-color: #cfe2ff;">{{ count($recentAssigned) }}</span>
                </div>
                <div class="card-body p-0">
                    @if(count($recentAssigned) > 0)
                        <div class="recent-reports-list">
                            @foreach($recentAssigned as $report)
                                <div class="recent-report-item border-bottom">
                                    <div class="d-flex justify-content-between align-items-start p-3">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center mb-1 flex-wrap">
                                                <span class="badge bg-light text-dark me-2">{{ $report['tanggal'] }}</span>
                                                <span class="text-muted small me-2">{{ $report['area_name'] }}</span>
                                                @if($report['station'])
                                                    <span class="text-muted small me-2">, {{ $report['station'] }}</span>
                                                @endif
                                                <span class="mx-1">•</span>
                                                <span class="text-muted small">
                                                    <i class="fas fa-calendar me-1"></i>Due: {{ $report['tenggat_waktu'] }}
                                                </span>
                                            </div>
                                            <h6 class="mb-0 text-truncate">{{ $report['deskripsi'] }}</h6>
                                        </div>
                                        <a href="{{ route('laporan.show', encrypt($report['id'])) }}"
                                           class="btn btn-sm btn-outline-primary ms-2">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="card-footer text-center p-2">
                            <a href="{{ route('laporan.index') }}"
                               class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-list me-1"></i>View All Assigned
                            </a>
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-clipboard-check fa-3x mb-3 opacity-50"></i>
                            <p class="mb-0">No recent assigned reports</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Completed Reports Card -->
        <div class="col-lg-6 col-md-12">
            <div class="card recent-reports-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-check-circle me-2 text-success"></i>
                        Recent Completed Reports
                    </h5>
                    <span class="badge bg-success">{{ count($recentCompleted) }}</span>
                </div>
                <div class="card-body p-0">
                    @if(count($recentCompleted) > 0)
                        <div class="recent-reports-list">
                            @foreach($recentCompleted as $report)
                                <div class="recent-report-item border-bottom">
                                    <div class="d-flex justify-content-between align-items-start p-3">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center mb-1 flex-wrap">
                                                <span class="badge bg-light text-dark me-2">{{ $report['tanggal'] }}</span>
                                                <span class="text-muted small me-2">{{ $report['area_name'] }}</span>
                                                @if($report['station'])
                                                    <span class="text-muted small me-2">, {{ $report['station'] }}</span>
                                                @endif
                                                <span class="mx-1">•</span>
                                                <span class="text-muted small">
                                                    <i class="fas fa-check me-1"></i>Completed: {{ $report['tenggat_waktu'] }}
                                                </span>
                                            </div>
                                            <h6 class="mb-0 text-truncate">{{ $report['deskripsi'] }}</h6>
                                        </div>
                                        <a href="{{ route('laporan.show', encrypt($report['id'])) }}"
                                           class="btn btn-sm btn-outline-success ms-2">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="card-footer text-center p-2">
                            <a href="{{ route('sejarah.index') }}"
                               class="btn btn-outline-success btn-sm">
                                <i class="fas fa-list me-1"></i>View All Completed
                            </a>
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-tasks fa-3x mb-3 opacity-50"></i>
                            <p class="mb-0">No recent completed reports</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals removed on dashboard to avoid unused code -->
@endsection

@push('scripts')
<!-- Chart.js CDN -->
<script src="{{ asset('js/chart.js') }}"></script>

<script>
// Chart data from PHP
const laporanPerBulanData = @json($laporanPerBulan);
const areaPerBulanData = @json($areaPerBulan);
const categoryPerBulanData = @json($categoryPerBulan);

// Helper function to get month names
function getMonthName(monthNumber) {
    const months = [
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'
    ];
    return months[monthNumber - 1] || '';
}

// Helper function to get fixed January-December months for a specific year
function getFixedMonths(year) {
    const months = [];
    for (let i = 1; i <= 12; i++) {
        months.push({
            name: getMonthName(i),
            number: i,
            year: year
        });
    }
    return months;
}

// Get current year or from URL filter
function getSelectedYear() {
    const urlParams = new URLSearchParams(window.location.search);
    const yearFromUrl = urlParams.get('year');
    if (yearFromUrl) {
        return parseInt(yearFromUrl, 10);
    }

    // Get latest year from data or current year
    const availableYears = laporanPerBulanData.map(item => item.tahun);
    return availableYears.length ? Math.max(...availableYears) : new Date().getFullYear();
}

// 1. Grafik Garis - Laporan per Bulan
const laporanPerBulanCtx = document.getElementById('laporanPerBulanChart').getContext('2d');
const selectedYear = getSelectedYear();
const monthsData = getFixedMonths(selectedYear);
const laporanPerBulanChart = new Chart(laporanPerBulanCtx, {
    type: 'line',
    data: {
        labels: monthsData.map(m => m.name),
        datasets: [{
            label: `Reports ${selectedYear}`,
            data: (() => {
                const data = new Array(12).fill(0);
                laporanPerBulanData.forEach(item => {
                    const monthIndex = monthsData.findIndex(m => m.number === item.bulan && m.year === item.tahun);
                    if (monthIndex >= 0 && monthIndex < 12) {
                        data[monthIndex] = item.total;
                    }
                });
                return data;
            })(),
            borderColor: '#007bff',
            backgroundColor: 'rgba(0, 123, 255, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#007bff',
            pointBorderColor: '#ffffff',
            pointBorderWidth: 2,
            pointRadius: 6,
            pointHoverRadius: 8
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true,
                position: 'top'
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(0,0,0,0.1)'
                }
            },
            x: {
                grid: {
                    color: 'rgba(0,0,0,0.1)'
                }
            }
        },
        interaction: {
            intersect: false,
            mode: 'index'
        }
    }
});

// 2. Grafik Pie - Category per Bulan
const categoryPerBulanCtx = document.getElementById('categoryPerBulanChart').getContext('2d');

// Destroy existing chart if exists
if (window.categoryPerBulanChartInstance) {
    window.categoryPerBulanChartInstance.destroy();
}

// Check if we have data
if (categoryPerBulanData && categoryPerBulanData.length > 0) {
    window.categoryPerBulanChartInstance = new Chart(categoryPerBulanCtx, {
        type: 'doughnut',
        data: {
            labels: categoryPerBulanData.map(item => item.problem_category ? item.problem_category.name : 'No Category'),
            datasets: [{
                data: categoryPerBulanData.map(item => item.total),
                backgroundColor: categoryPerBulanData.map(item => item.problem_category ? item.problem_category.color : '#C9CBCF'),
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true
                    }
                }
            }
        }
    });
} else {
    // Show "No Data" message
    window.categoryPerBulanChartInstance = new Chart(categoryPerBulanCtx, {
        type: 'doughnut',
        data: {
            labels: ['No Data'],
            datasets: [{
                data: [1],
                backgroundColor: ['#e9ecef'],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true
                    }
                }
            }
        }
    });
}

// 3. Grafik Batang - Area yang Melapor per Bulan
const areaPerBulanCtx = document.getElementById('areaPerBulanChart').getContext('2d');

// Prepare data for grouped bar chart
const areaNames = [...new Set(areaPerBulanData.map(item => item.area_name))];

const areaPerBulanChart = new Chart(areaPerBulanCtx, {
    type: 'bar',
    data: {
        labels: monthsData.map(m => m.name),
        datasets: areaNames.map((areaName, index) => {
            const colors = [
                '#E74C4C', '#36A2EB', '#FFCE56', '#4BC0C0',
                '#9966FF', '#FF9F40', '#FF6384', '#C9CBCF'
            ];
            return {
                label: areaName,
                data: monthsData.map((monthData, monthIndex) => {
                    const item = areaPerBulanData.find(d =>
                        d.area_name === areaName && d.bulan === monthData.number && d.tahun === monthData.year
                    );
                    return item ? item.total : 0;
                }),
                backgroundColor: colors[index % colors.length],
                borderColor: colors[index % colors.length],
                borderWidth: 1
            };
        })
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true,
                position: 'top'
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                stacked: false,
                grid: {
                    color: 'rgba(0,0,0,0.1)'
                }
            },
            x: {
                stacked: false,
                grid: {
                    color: 'rgba(0,0,0,0.1)'
                }
            }
        }
    }
});

// Dashboard charts are ready
</script>
@endpush

<style>
/* Stats Icon Styling */
.stats-icon {
    width: 3rem;
    height: 3rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}

/* Dashboard Header Styling */
.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 1rem;
}

.dashboard-header h1 {
    font-size: 2rem;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0;
}

.dashboard-header p {
    font-size: 1rem;
    color: var(--text-secondary);
    margin: 0;
}

/* Chart Card Styling */
.card {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    border: 1px solid #e9ecef;
    border-radius: 8px;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
    padding: 1rem 1.25rem;
}

.card-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #495057;
    margin: 0;
}

.card-body {
    padding: 1.25rem;
}

/* Chart Container */
canvas {
    max-height: 400px;
}

/* Filter Header Toggle Icon */
#filterToggleIcon {
    transition: transform 0.3s ease;
    display: inline-block;
}

/* Filter Collapse Smooth Animation */
#filterCollapse {
    transition: all 0.3s ease;
}

#filterCollapse.collapsing {
    transition: height 0.3s ease;
}

/* Filter Row Alignment */
.row.g-3.align-items-end {
    align-items: flex-end !important;
}

.row.g-3.align-items-end > div {
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
}

/* Filter Dropdown with Dark Scrollbar */
.filter-dropdown {
    overflow-y: scroll;
    scrollbar-width: thin;
    scrollbar-color: #6c757d #f8f9fa;
    height: 38px;
}

/* Webkit browsers (Chrome, Safari, Edge) */
.filter-dropdown::-webkit-scrollbar {
    width: 10px;
}

.filter-dropdown::-webkit-scrollbar-track {
    background: #f8f9fa;
}

.filter-dropdown::-webkit-scrollbar-thumb {
    background: #6c757d;
    border-radius: 5px;
}

.filter-dropdown::-webkit-scrollbar-thumb:hover {
    background: #495057;
}

/* Form Control Alignment */
.form-control,
.form-select {
    height: 38px;
}

/* Recent Reports Styling */
.recent-reports-card {
    display: flex;
    flex-direction: column;
}

.recent-reports-card .card-body {
    flex: 0 1 auto;
}

.recent-reports-list {
    max-height: none;
    overflow-y: visible;
}

.recent-report-item {
    padding: 0 !important;
}

.recent-report-item:last-child {
    border-bottom: none !important;
}

.recent-report-item:hover {
    background-color: #f8f9fa;
}

.recent-reports-card .btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .dashboard-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .dashboard-header h1 {
        font-size: 1.5rem;
    }

    .stats-icon {
        width: 2.5rem;
        height: 2.5rem;
        font-size: 1rem;
    }

    .card-body {
        padding: 1rem;
    }

    canvas {
        max-height: 300px;
    }
}
</style>

<!-- Dashboard Routes Configuration -->
<script>
    window.dashboardRoutes = {
        dashboard: "{{ route('dashboard') }}"
    };
</script>

<!-- Date Range Dropdown Handler -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const startDateInput = document.getElementById('dashboard_start_date');
    const endDateInput = document.getElementById('dashboard_end_date');
    const applyBtn = document.getElementById('dashboard_date_apply');
    const dateRangeLabel = document.getElementById('dateRangeLabel');
    const dropdown = document.getElementById('dateRangeDropdown');
    const monthFilter = document.getElementById('month_filter');
    const yearFilter = document.getElementById('year_filter');
    
    // Convert YYYY-MM-DD to DD/MM/YYYY for display after Elegant Datepicker converts
    function convertToDisplayFormat(yyyymmdd) {
        if (!yyyymmdd) return '';
        const parts = yyyymmdd.split('-');
        if (parts.length === 3) {
            return parts[2] + '/' + parts[1] + '/' + parts[0];
        }
        return yyyymmdd;
    }
    
    // Convert DD/MM/YYYY back to YYYY-MM-DD for server
    function convertToServerFormat(ddmmyyyy) {
        if (!ddmmyyyy) return '';
        const parts = ddmmyyyy.split('/');
        if (parts.length === 3) {
            return parts[2] + '-' + parts[1] + '-' + parts[0];
        }
        return ddmmyyyy;
    }
    
    // After Elegant Datepicker converts the inputs, set the correct display value
    setTimeout(function() {
        if (startDateInput && startDateInput.value && startDateInput.value.includes('-')) {
            const displayValue = convertToDisplayFormat(startDateInput.value);
            $(startDateInput).datepicker('update', displayValue);
        }
        if (endDateInput && endDateInput.value && endDateInput.value.includes('-')) {
            const displayValue = convertToDisplayFormat(endDateInput.value);
            $(endDateInput).datepicker('update', displayValue);
        }
    }, 500);
    
    // Clear date range when month or year is selected
    if (monthFilter) {
        monthFilter.addEventListener('change', function() {
            if (monthFilter.value) {
                clearDateRange();
            }
        });
    }
    
    if (yearFilter) {
        yearFilter.addEventListener('change', function() {
            if (yearFilter.value) {
                clearDateRange();
            }
        });
    }
    
    function clearDateRange() {
        // Clear dropdown inputs
        if (startDateInput) {
            startDateInput.value = '';
            // Trigger change event for elegant datepicker
            $(startDateInput).trigger('change');
        }
        if (endDateInput) {
            endDateInput.value = '';
            endDateInput.disabled = true;
            $(endDateInput).trigger('change');
        }
        
        // Clear hidden inputs
        const hiddenStart = document.getElementById('start_date');
        const hiddenEnd = document.getElementById('end_date');
        if (hiddenStart) hiddenStart.value = '';
        if (hiddenEnd) hiddenEnd.value = '';
        
        // Reset label
        dateRangeLabel.textContent = 'Select date range';
    }
    
    // Handle start date change to enable/disable end date
    if (startDateInput) {
        // Use jQuery change event to work with Bootstrap Datepicker
        $(startDateInput).on('change changeDate', function() {
            const startValue = startDateInput.value;
            
            if (startValue) {
                // Enable end date
                endDateInput.disabled = false;
                
                // Set min date for native HTML5 validation
                endDateInput.setAttribute('min', startValue);
                
                // Set startDate for Bootstrap Datepicker (Elegant Datepicker)
                if ($(endDateInput).data('datepicker')) {
                    $(endDateInput).datepicker('setStartDate', new Date(startValue));
                }
                
                // Clear end date if it's before start date
                if (endDateInput.value && endDateInput.value < startValue) {
                    endDateInput.value = '';
                    if ($(endDateInput).data('datepicker')) {
                        $(endDateInput).datepicker('update', '');
                    }
                }
            } else {
                // Disable end date if start is empty
                endDateInput.disabled = true;
                endDateInput.value = '';
                endDateInput.removeAttribute('min');
                
                // Remove startDate restriction
                if ($(endDateInput).data('datepicker')) {
                    $(endDateInput).datepicker('setStartDate', null);
                    $(endDateInput).datepicker('update', '');
                }
            }
        });
        
        // Trigger on page load if start date has value
        if (startDateInput.value) {
            $(startDateInput).trigger('change');
        }
    }
    
    // Apply button
    if (applyBtn) {
        applyBtn.addEventListener('click', function() {
            // Create or update hidden inputs
            let hiddenStart = document.getElementById('start_date');
            let hiddenEnd = document.getElementById('end_date');
            const form = document.getElementById('dashboardFilters');
            
            if (!hiddenStart) {
                hiddenStart = document.createElement('input');
                hiddenStart.type = 'hidden';
                hiddenStart.id = 'start_date';
                hiddenStart.name = 'start_date';
                form.appendChild(hiddenStart);
            }
            
            if (!hiddenEnd) {
                hiddenEnd = document.createElement('input');
                hiddenEnd.type = 'hidden';
                hiddenEnd.id = 'end_date';
                hiddenEnd.name = 'end_date';
                form.appendChild(hiddenEnd);
            }
            
            // Convert DD/MM/YYYY back to YYYY-MM-DD for server
            const startValue = startDateInput.value;
            const endValue = endDateInput.value;
            
            const serverStartDate = convertToServerFormat(startValue);
            const serverEndDate = convertToServerFormat(endValue);
            
            hiddenStart.value = serverStartDate;
            hiddenEnd.value = serverEndDate;
            
            // Update label (use server format for consistency)
            updateDateRangeLabel();
            
            // Close dropdown
            const bsDropdown = bootstrap.Dropdown.getInstance(dropdown);
            if (bsDropdown) bsDropdown.hide();
            
            // Clear month/year filters when date range is applied
            const monthFilter = document.getElementById('month_filter');
            const yearFilter = document.getElementById('year_filter');
            if (monthFilter) monthFilter.value = '';
            if (yearFilter) yearFilter.value = '';
        });
    }
    
    function updateDateRangeLabel() {
        const start = startDateInput.value;
        const end = endDateInput.value;
        
        if (start && end) {
            // Input might be DD/MM/YYYY or YYYY-MM-DD, handle both
            dateRangeLabel.textContent = formatDateForLabel(start) + ' - ' + formatDateForLabel(end);
        } else if (start) {
            dateRangeLabel.textContent = 'From ' + formatDateForLabel(start);
        } else if (end) {
            dateRangeLabel.textContent = 'Until ' + formatDateForLabel(end);
        } else {
            dateRangeLabel.textContent = 'Select date range';
        }
    }
    
    function formatDateForLabel(dateStr) {
        if (!dateStr) return '';
        
        // Check if already in DD/MM/YYYY format
        if (dateStr.includes('/')) {
            return dateStr; // Already formatted
        }
        
        // Convert from YYYY-MM-DD to DD/MM/YYYY
        const parts = dateStr.split('-');
        if (parts.length === 3) {
            return parts[2] + '/' + parts[1] + '/' + parts[0];
        }
        return dateStr;
    }
});
</script>

<!-- Dashboard Filters JavaScript -->
<script src="{{ asset('js/dashboard-filters.js') }}?v={{ time() }}"></script>