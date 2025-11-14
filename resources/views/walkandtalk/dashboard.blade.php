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
                <!-- Filter Row 1: Main Filters -->
                <div class="row g-3 align-items-end mb-3">
                    <!-- Category Filter -->
                    <div class="col-lg-4 col-md-6">
                        <label for="category_filter" class="form-label fw-semibold mb-2">
                            <i class="fas fa-filter me-1 text-primary"></i>Filter by Category
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

                    <!-- Date Filter -->
                    <div class="col-lg-2 col-md-6">
                        <label for="date_filter" class="form-label fw-semibold mb-2">
                            <i class="fas fa-calendar-day me-1 text-warning"></i>Specific Date
                        </label>
                        <input type="date" class="form-control" id="date_filter" name="date"
                               value="{{ request('date') }}">
                    </div>

                    <!-- Filter Actions -->
                    <div class="col-lg-2 col-md-6">
                        <div class="d-flex gap-2 h-100 align-items-end">
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="fas fa-search me-1"></i>Filter
                            </button>
                            @if(request()->hasAny(['category_id', 'month', 'year', 'date']))
                                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary flex-fill">
                                    <i class="fas fa-times me-1"></i>Clear
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Active Filters Display -->
                @if(request()->hasAny(['category_id', 'month', 'year', 'date']))
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

                            @if(request('date'))
                                <span class="badge bg-warning">
                                    {{ \Carbon\Carbon::parse(request('date'))->format('M d, Y') }}
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
                    <div class="stats-icon bg-primary text-white rounded-circle me-3">
                        <i class="fas fa-cog fa-spin"></i>
                    </div>
                    <div>
                        <h3 class="mb-1">In Progress Reports</h3>
                        <div class="number text-primary">{{ $laporanInProgress }}</div>
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
            <div class="card h-100 recent-reports-card">
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
                        <div class="card-footer text-center">
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
            <div class="card h-100 recent-reports-card">
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
                        <div class="card-footer text-center">
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

// Check if we have data
if (categoryPerBulanData && categoryPerBulanData.length > 0) {
    const categoryPerBulanChart = new Chart(categoryPerBulanCtx, {
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
    const categoryPerBulanChart = new Chart(categoryPerBulanCtx, {
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
.recent-reports-list {
    max-height: 400px;
    overflow-y: auto;
}

.recent-report-item:last-child {
    border-bottom: none !important;
}

.recent-report-item:hover {
    background-color: #f8f9fa;
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

<!-- Dashboard Filters JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterCollapse = document.getElementById('filterCollapse');
    const filterToggleIcon = document.getElementById('filterToggleIcon');
    
    if (filterCollapse && filterToggleIcon) {
        // Handle collapse show/hide events
        filterCollapse.addEventListener('show.bs.collapse', function() {
            filterToggleIcon.style.transform = 'rotate(180deg)';
        });
        
        filterCollapse.addEventListener('hide.bs.collapse', function() {
            filterToggleIcon.style.transform = 'rotate(0deg)';
        });
    }
});
</script>
<script src="{{ asset('js/dashboard-filters.js') }}"></script>