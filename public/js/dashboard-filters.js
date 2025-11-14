/**
 * Dashboard Filters JavaScript
 * Handles filter interactions and chart updates
 */

class DashboardFilters {
    constructor() {
        this.initializeElements();
        this.bindEvents();
        this.setupKeyboardShortcuts();
        this.setupTooltips();
        this.charts = {};
        
        // Setup filter toggle functionality
        this.setupFilterToggle();
        
        console.log('Dashboard filters initialized');
    }

    initializeElements() {
        this.categoryFilter = document.getElementById('category_filter');
        this.monthFilter = document.getElementById('month_filter');
        this.yearFilter = document.getElementById('year_filter');
        this.dateFilter = document.getElementById('date_filter');
        this.filterForm = document.getElementById('dashboardFilters');
        this.autoSubmitEnabled = false; // Set to true for auto-submit
    }

    bindEvents() {
        // Auto-submit on filter change (if enabled)
        if (this.autoSubmitEnabled) {
            [this.categoryFilter, this.monthFilter, this.yearFilter, this.dateFilter]
                .forEach(filter => {
                    if (filter) {
                        filter.addEventListener('change', () => {
                            setTimeout(() => this.submitFilters(), 300);
                        });
                    }
                });
        }

        // Date filter logic - clear month/year when specific date is selected
        if (this.dateFilter) {
            this.dateFilter.addEventListener('change', () => {
                if (this.dateFilter.value) {
                    if (this.monthFilter) this.monthFilter.value = '';
                    if (this.yearFilter) this.yearFilter.value = '';
                }
            });
        }

        // Month/Year filter logic - clear specific date when month/year is selected
        [this.monthFilter, this.yearFilter].forEach(filter => {
            if (filter) {
                filter.addEventListener('change', () => {
                    if (filter.value && this.dateFilter) {
                        this.dateFilter.value = '';
                    }
                });
            }
        });

        // Filter form validation and submission
        if (this.filterForm) {
            this.filterForm.addEventListener('submit', (e) => {
                // Don't prevent default - allow normal form submission for page reload
                this.handleFormSubmit();
            });
        }

        // Filter change events - only for UI interactions, no auto-update
        [this.categoryFilter, this.monthFilter, this.yearFilter, this.dateFilter]
            .forEach(filter => {
                if (filter) {
                    filter.addEventListener('change', (e) => {
                        console.log('Filter changed:', filter.name || filter.id, 'value:', filter.value);
                        // Note: Charts will only update when Filter button is clicked
                        // No automatic updates on filter change
                    });
                }
            });
    }

    setupKeyboardShortcuts() {
        document.addEventListener('keydown', (e) => {
            // Ctrl/Cmd + F to focus on category filter
            if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
                e.preventDefault();
                if (this.categoryFilter) {
                    this.categoryFilter.focus();
                }
            }
            
            // Escape to clear all filters
            if (e.key === 'Escape') {
                this.clearAllFilters();
            }

            // Enter to submit filters
            if (e.key === 'Enter' && e.target.matches('#category_filter, #month_filter, #year_filter')) {
                e.preventDefault();
                this.submitFilters();
            }
        });
    }

    setupTooltips() {
        const filterHints = {
            category_filter: 'Filter reports by problem category (Ctrl+F to focus)',
            month_filter: 'Filter by specific month',
            year_filter: 'Filter by specific year', 
            date_filter: 'Filter by specific date (overrides month/year)'
        };

        Object.keys(filterHints).forEach(filterId => {
            const element = document.getElementById(filterId);
            if (element) {
                element.title = filterHints[filterId];
            }
        });
    }

    handleFormSubmit() {
        const hasFilters = this.categoryFilter?.value || 
                          this.monthFilter?.value || 
                          this.yearFilter?.value || 
                          this.dateFilter?.value;
        
        if (!hasFilters) {
            // Redirect to clear all filters
            window.location.href = window.dashboardRoutes.dashboard;
            return false;
        }

        // Show loading state and allow form submission to proceed
        console.log('🔘 Filter button clicked - form will submit with page reload...');
        this.showLoadingState();
        
        // Return true to allow form submission
        return true;
    }

    submitFilters() {
        // Show loading state
        this.showLoadingState();
        
        // Submit form normally (page reload)
        this.filterForm.submit();
    }

    clearAllFilters() {
        window.location.href = window.dashboardRoutes.dashboard;
    }

    showLoadingState() {
        const submitBtn = this.filterForm.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Filtering...';
        }
    }

    resetLoadingState() {
        const submitBtn = this.filterForm.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-search me-1"></i>Filter';
        }
    }

    // Note: AJAX method disabled - using page reload for reliability
    // This ensures active filter badges and all UI elements update correctly
    async updateChartsWithFilters() {
        console.log('ℹ️ AJAX update disabled - using page reload for better reliability');
        // Page reload ensures:
        // - Active filter badges update correctly
        // - All UI elements sync properly  
        // - No partial state issues
        // - Consistent user experience
    }

    updateStatisticsCards(stats) {
        // Update statistics cards with filtered data
        const totalElement = document.querySelector('[data-stat="total"]');
        const inProgressElement = document.querySelector('[data-stat="in_progress"]');
        const completedElement = document.querySelector('[data-stat="completed"]');

        if (totalElement) totalElement.textContent = stats.total;
        if (inProgressElement) inProgressElement.textContent = stats.in_progress;
        if (completedElement) completedElement.textContent = stats.completed;
    }
    updateCharts(chartData) {
        console.log('Updating charts with data:', chartData);
        
        // Update monthly reports chart
        if (window.laporanPerBulanChart && chartData.laporanPerBulan) {
            console.log('💡 Use debugPieChart() in console to check chart state');
            this.updateMonthlyChart(chartData.laporanPerBulan);
        }

        // Update area reports chart
        if (window.areaPerBulanChart && chartData.areaPerBulan) {
            console.log('Updating area chart');
            this.updateAreaChart(chartData.areaPerBulan);
        }

        // Update category chart (always update, even with empty data)
        if (window.categoryPerBulanChart) {
            console.log('Updating category chart');
            this.updateCategoryChart(chartData.categoryPerBulan || []);
        } else {
            console.log('Category chart not found!');
        }
    }

    updateMonthlyChart(data) {
        const chart = window.laporanPerBulanChart;
        if (!chart) return;

        // Helper function to get last 12 months (same as in dashboard)
        const getLast12Months = () => {
            const months = [];
            const now = new Date();
            const currentYear = now.getFullYear();
            const currentMonth = now.getMonth(); // 0-11
            
            for (let i = 11; i >= 0; i--) {
                let targetMonth = currentMonth - i;
                let targetYear = currentYear;
                
                if (targetMonth < 0) {
                    targetMonth += 12;
                    targetYear--;
                }
                
                const monthNames = [
                    'January', 'February', 'March', 'April', 'May', 'June',
                    'July', 'August', 'September', 'October', 'November', 'December'
                ];
                
                months.push({
                    name: `${monthNames[targetMonth]} ${targetYear}`,
                    number: targetMonth + 1,
                    year: targetYear
                });
            }
            return months;
        };

        // For filtered data, we need to handle different scenarios
        if (this.hasActiveFilters()) {
            // If filters are active, show only filtered data
            const chartData = [];
            const chartLabels = [];
            
            if (data && data.length > 0) {
                // Sort data by year and month
                const sortedData = data.sort((a, b) => {
                    if (a.tahun !== b.tahun) return a.tahun - b.tahun;
                    return a.bulan - b.bulan;
                });
                
                sortedData.forEach(item => {
                    const monthNames = [
                        'January', 'February', 'March', 'April', 'May', 'June',
                        'July', 'August', 'September', 'October', 'November', 'December'
                    ];
                    chartLabels.push(`${monthNames[item.bulan - 1]} ${item.tahun}`);
                    chartData.push(item.total);
                });
            } else {
                // No data for filtered period
                chartLabels.push('No Data');
                chartData.push(0);
            }
            
            chart.data.labels = chartLabels;
            chart.data.datasets[0].data = chartData;
        } else {
            // No filters active, use 12-month view
            const monthsData = getLast12Months();
            const chartData = new Array(12).fill(0);
            
            // Map data to 12-month chart
            data.forEach(item => {
                const monthIndex = monthsData.findIndex(m => m.number === item.bulan && m.year === item.tahun);
                if (monthIndex >= 0 && monthIndex < 12) {
                    chartData[monthIndex] = item.total;
                }
            });

            chart.data.labels = monthsData.map(m => m.name);
            chart.data.datasets[0].data = chartData;
        }

        chart.update('none');
    }

    updateAreaChart(data) {
        const chart = window.areaPerBulanChart;
        if (!chart) return;

        if (!data || data.length === 0) {
            // No data - clear all datasets
            chart.data.datasets.forEach(dataset => {
                dataset.data = new Array(12).fill(0);
            });
            chart.update('none');
            return;
        }

        // Update area chart with filtered data
        const areaNames = [...new Set(data.map(item => item.area_name))];
        
        // Clear all datasets first
        chart.data.datasets.forEach(dataset => {
            dataset.data = new Array(12).fill(0);
        });
        
        // Update each dataset with filtered data
        chart.data.datasets.forEach((dataset, index) => {
            const areaName = dataset.label;
            const areaData = data.filter(item => item.area_name === areaName);
            
            if (this.hasActiveFilters()) {
                // For filtered data, show only filtered months
                const chartData = new Array(12).fill(0);
                areaData.forEach(item => {
                    if (item.bulan >= 1 && item.bulan <= 12) {
                        chartData[item.bulan - 1] = item.total;
                    }
                });
                dataset.data = chartData;
            } else {
                // For unfiltered data, use 12-month mapping
                const chartData = new Array(12).fill(0);
                areaData.forEach(item => {
                    if (item.bulan >= 1 && item.bulan <= 12) {
                        chartData[item.bulan - 1] = item.total;
                    }
                });
                dataset.data = chartData;
            }
        });
        
        chart.update('none');
    }

    updateCategoryChart(data) {
        const chart = window.categoryPerBulanChart;
        console.log('Updating category chart with data:', data);
        
        if (!chart) {
            console.error('Category chart not found!');
            return;
        }

        if (data && data.length > 0) {
            chart.data.labels = data.map(item => 
                item.problem_category ? item.problem_category.name : 'No Category'
            );
            chart.data.datasets[0].data = data.map(item => item.total);
            chart.data.datasets[0].backgroundColor = data.map(item => 
                item.problem_category ? item.problem_category.color : '#C9CBCF'
            );
        } else {
            // No data case
            console.log('No category data - showing empty state');
            chart.data.labels = ['No Data'];
            chart.data.datasets[0].data = [1];
            chart.data.datasets[0].backgroundColor = ['#e9ecef'];
        }
        
        console.log('Category chart updated with:', {
            labels: chart.data.labels,
            data: chart.data.datasets[0].data
        });
        
        chart.update('none');
    }

    setupFilterToggle() {
        const filterCollapse = document.getElementById('filterCollapse');
        const toggleIcon = document.getElementById('filterToggleIcon');
        
        if (filterCollapse && toggleIcon) {
            // Listen for collapse events with smooth rotation
            filterCollapse.addEventListener('show.bs.collapse', () => {
                toggleIcon.style.transform = 'rotate(180deg)';
                console.log('Filter section expanded');
            });
            
            filterCollapse.addEventListener('hide.bs.collapse', () => {
                toggleIcon.style.transform = 'rotate(0deg)';
                console.log('Filter section collapsed');
            });
            
            // Save collapse state to localStorage
            filterCollapse.addEventListener('shown.bs.collapse', () => {
                localStorage.setItem('filterCollapsed', 'false');
            });
            
            filterCollapse.addEventListener('hidden.bs.collapse', () => {
                localStorage.setItem('filterCollapsed', 'true');
            });
            
            // Always start with filter collapsed (ignore localStorage)
            if (typeof bootstrap !== 'undefined') {
                setTimeout(() => {
                    const bsCollapse = new bootstrap.Collapse(filterCollapse, {
                        toggle: false
                    });
                    if (filterCollapse.classList.contains('show')) {
                        // If somehow it's open, close it
                        bsCollapse.hide();
                    }
                    // Update icon rotation to 0 (down arrow)
                    toggleIcon.style.transform = 'rotate(0deg)';
                }, 100);
            }
        }
    }

    // Utility methods
    getCurrentFilters() {
        return {
            category_id: this.categoryFilter?.value || '',
            month: this.monthFilter?.value || '',
            year: this.yearFilter?.value || '',
            date: this.dateFilter?.value || ''
        };
    }

    hasActiveFilters() {
        const filters = this.getCurrentFilters();
        return Object.values(filters).some(value => value !== '');
    }

    // Public methods for external access
    static getInstance() {
        if (!window.dashboardFiltersInstance) {
            window.dashboardFiltersInstance = new DashboardFilters();
        }
        return window.dashboardFiltersInstance;
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Initialize dashboard filters
    window.dashboardFilters = DashboardFilters.getInstance();
    
    // Make it globally accessible for debugging
    window.DashboardFilters = DashboardFilters;
});

// Export for module systems (if needed)
if (typeof module !== 'undefined' && module.exports) {
    module.exports = DashboardFilters;
}
