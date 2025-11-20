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
    }

    initializeElements() {
        this.categoryFilter = document.getElementById('category_filter');
        this.monthFilter = document.getElementById('month_filter');
        this.yearFilter = document.getElementById('year_filter');
        this.startDateFilter = document.getElementById('start_date');
        this.endDateFilter = document.getElementById('end_date');
        this.filterForm = document.getElementById('dashboardFilters');
        this.autoSubmitEnabled = false; // Set to true for auto-submit
    }

    bindEvents() {
        // Auto-submit on filter change (if enabled)
        if (this.autoSubmitEnabled) {
            [this.categoryFilter, this.monthFilter, this.yearFilter, this.startDateFilter, this.endDateFilter]
                .forEach(filter => {
                    if (filter) {
                        filter.addEventListener('change', () => {
                            setTimeout(() => this.submitFilters(), 300);
                        });
                    }
                });
        }

        // Date range filter logic - clear month/year when date range is selected
        [this.startDateFilter, this.endDateFilter].forEach(dateInput => {
            if (dateInput) {
                dateInput.addEventListener('change', () => {
                    if (dateInput.value) {
                        if (this.monthFilter) this.monthFilter.value = '';
                        if (this.yearFilter) this.yearFilter.value = '';
                    }
                });
            }
        });

        // Month/Year filter logic - clear date range when month/year is selected
        [this.monthFilter, this.yearFilter].forEach(filter => {
            if (filter) {
                filter.addEventListener('change', () => {
                    if (filter.value) {
                        if (this.startDateFilter) this.startDateFilter.value = '';
                        if (this.endDateFilter) this.endDateFilter.value = '';
                    }
                });
            }
        });

        // End date validation - must be >= start date
        if (this.startDateFilter && this.endDateFilter) {
            this.startDateFilter.addEventListener('change', () => {
                if (this.startDateFilter.value) {
                    this.endDateFilter.min = this.startDateFilter.value;
                }
            });
        }

        // Filter form validation and submission
        if (this.filterForm) {
            this.filterForm.addEventListener('submit', (e) => {
                // Don't prevent default - allow normal form submission for page reload
                this.handleFormSubmit();
            });
        }

        // Filter change events - only for UI interactions, no auto-update
        [this.categoryFilter, this.monthFilter, this.yearFilter, this.startDateFilter, this.endDateFilter]
            .forEach(filter => {
                if (filter) {
                    filter.addEventListener('change', (e) => {
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
        // Check hidden inputs for date range
        const hiddenStartDate = document.getElementById('start_date');
        const hiddenEndDate = document.getElementById('end_date');
        
        const hasFilters = this.categoryFilter?.value || 
                          this.monthFilter?.value || 
                          this.yearFilter?.value || 
                          hiddenStartDate?.value ||
                          hiddenEndDate?.value;
        
        if (!hasFilters) {
            // Redirect to clear all filters (prevent empty filter submission)
            window.location.href = window.dashboardRoutes.dashboard;
            return false;
        }

        // Show loading state and allow form submission to proceed
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
        // Update monthly reports chart
        if (window.laporanPerBulanChart && chartData.laporanPerBulan) {
            this.updateMonthlyChart(chartData.laporanPerBulan);
        }

        // Update area reports chart
        if (window.areaPerBulanChart && chartData.areaPerBulan) {
            this.updateAreaChart(chartData.areaPerBulan);
        }

        // Update category chart (always update, even with empty data)
        if (window.categoryPerBulanChart) {
            this.updateCategoryChart(chartData.categoryPerBulan || []);
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
        
        if (!chart) {
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
            chart.data.labels = ['No Data'];
            chart.data.datasets[0].data = [1];
            chart.data.datasets[0].backgroundColor = ['#e9ecef'];
        }
        
        chart.update('none');
    }

    setupFilterToggle() {
        const filterCollapse = document.getElementById('filterCollapse');
        const toggleIcon = document.getElementById('filterToggleIcon');
        
        if (filterCollapse && toggleIcon) {
            // Listen for collapse events with smooth rotation
            filterCollapse.addEventListener('show.bs.collapse', () => {
                toggleIcon.style.transform = 'rotate(180deg)';
            });
            
            filterCollapse.addEventListener('hide.bs.collapse', () => {
                toggleIcon.style.transform = 'rotate(0deg)';
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

    initializeDatePicker() {
        // Wait for flatpickr to be loaded
        const initFlatpickr = () => {
            const dateFilterInput = document.getElementById('date_filter');
            
            if (!dateFilterInput) {
                console.error('Date filter input not found');
                return;
            }
            
            if (typeof flatpickr === 'undefined') {
                console.error('Flatpickr library not loaded');
                return;
            }
            
            try {
                flatpickr(dateFilterInput, {
                    dateFormat: 'Y-m-d',
                    allowInput: false,
                    disableMobile: true,
                    maxDate: 'today',
                    onChange: (selectedDates, dateStr, instance) => {
                        // Clear month and year filters when date is selected
                        if (dateStr) {
                            if (this.monthFilter) this.monthFilter.value = '';
                            if (this.yearFilter) this.yearFilter.value = '';
                        }
                    }
                });
            } catch (error) {
                console.error('Error initializing Flatpickr:', error);
            }
        };
        
        // Try to initialize, or wait for flatpickr to load
        if (typeof flatpickr !== 'undefined') {
            initFlatpickr();
        } else {
            // Wait for flatpickr to load
            setTimeout(initFlatpickr, 100);
        }
    }

    // Utility methods
    getCurrentFilters() {
        return {
            category_id: this.categoryFilter?.value || '',
            month: this.monthFilter?.value || '',
            year: this.yearFilter?.value || '',
            start_date: this.startDateFilter?.value || '',
            end_date: this.endDateFilter?.value || ''
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
