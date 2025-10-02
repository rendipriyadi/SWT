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
        <div class="datetime fs-6 text-secondary">
            <i class="fas fa-clock me-1"></i>
            <span id="currentDateTime"></span>
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
                        <i class="fas fa-clock"></i>
                    </div>
                    <div>
                        <h3 class="mb-1">Assigned Reports</h3>
                        <div class="number text-warning">{{ $laporanDitugaskan }}</div>
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
                        Monthly Reporting In Last 12 Months
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
                        Monthly Area Reporting In Last 12 Months
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="areaPerBulanChart" height="400"></canvas>
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
// Update datetime every second
function updateDateTime() {
    const now = new Date();
    
    // English month names
    const months = [
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'
    ];
    
    // English day names
    const days = [
        'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'
    ];
    
    const dayName = days[now.getDay()];
    const monthName = months[now.getMonth()];
    const day = now.getDate();
    const year = now.getFullYear();
    const hours = now.getHours().toString().padStart(2, '0');
    const minutes = now.getMinutes().toString().padStart(2, '0');
    const seconds = now.getSeconds().toString().padStart(2, '0');
    
    const formattedDateTime = `${dayName}, ${day} ${monthName} ${year} ${hours}:${minutes}:${seconds}`;
    
    const el = document.getElementById('currentDateTime');
    if (el) {
        el.textContent = formattedDateTime;
    }
}

// Update datetime on page load and every second (only if element exists)
if (document.getElementById('currentDateTime')) {
updateDateTime();
setInterval(updateDateTime, 1000);
}

// Chart data from PHP
const laporanPerBulanData = @json($laporanPerBulan);
const areaPerBulanData = @json($areaPerBulan);
const categoryPerBulanData = @json($categoryPerBulan);

// Debug logging removed in production

// Helper function to get month names
function getMonthName(monthNumber) {
    const months = [
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'
    ];
    return months[monthNumber - 1] || '';
}

// Helper function to get last 12 months
function getLast12Months() {
    const months = [];
    for (let i = 11; i >= 0; i--) {
        const date = new Date();
        date.setMonth(date.getMonth() - i);
        // getMonth() returns 0-11, but we need 1-12 for database comparison
        const monthNumber = date.getMonth() + 1;
        months.push({
            name: getMonthName(monthNumber),
            number: monthNumber
        });
    }
    return months;
}

// 1. Grafik Garis - Laporan per Bulan
const laporanPerBulanCtx = document.getElementById('laporanPerBulanChart').getContext('2d');
const monthsData = getLast12Months();
const laporanPerBulanChart = new Chart(laporanPerBulanCtx, {
    type: 'line',
    data: {
        labels: monthsData.map(m => m.name),
        datasets: [{
            label: 'Total Report',
            data: (() => {
                const data = new Array(12).fill(0);
                laporanPerBulanData.forEach(item => {
                    // Find the correct month index in our months array
                    const monthIndex = monthsData.findIndex(m => m.number === item.bulan);
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
                        d.area_name === areaName && d.bulan === monthData.number
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

// Chart initialization is complete
console.log('Dashboard charts initialized successfully');

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