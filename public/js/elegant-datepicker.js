/**
 * Elegant Date Picker Implementation
 * Provides consistent, beautiful date inputs across the application
 */

class ElegantDatePicker {
    constructor() {
        this.defaultOptions = {
            format: 'dd/mm/yyyy',
            autoclose: true,
            todayHighlight: true,
            orientation: 'auto',
            container: 'body',
            todayBtn: 'linked',
            clearBtn: true,
            language: 'en',
            weekStart: 1,
            calendarWeeks: false,
            showOnFocus: true,
            forceParse: false,
            keyboardNavigation: true,
            templates: {
                leftArrow: '<i class="fas fa-chevron-left"></i>',
                rightArrow: '<i class="fas fa-chevron-right"></i>'
            }
        };
        
        this.init();
    }
    
    init() {
        // Wait for DOM to be ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.initializeDatePickers());
        } else {
            this.initializeDatePickers();
        }
        
        // Re-initialize on AJAX content updates (with debounce to prevent infinite loop)
        let drawTimeout;
        $(document).on('draw.dt', () => {
            clearTimeout(drawTimeout);
            drawTimeout = setTimeout(() => {
                // Only convert new date inputs that haven't been converted yet
                this.convertBasicDateInputs();
            }, 150);
        });
    }
    
    initializeDatePickers() {
        // Convert all date inputs to elegant date pickers
        this.convertBasicDateInputs();
        this.initializeCustomDatePickers();
        this.initializeDateRangePickers();
    }
    
    convertBasicDateInputs() {
        // Find all input[type="date"] and convert them
        const basicDateInputs = document.querySelectorAll('input[type="date"]:not(.elegant-converted)');
        
        basicDateInputs.forEach(input => {
            this.convertToElegantDatePicker(input);
        });
    }
    
    convertToElegantDatePicker(input) {
        // Mark as converted to prevent double conversion
        input.classList.add('elegant-converted');
        
        // Create wrapper
        const wrapper = document.createElement('div');
        wrapper.className = 'elegant-date-input';
        
        // Insert wrapper before input
        input.parentNode.insertBefore(wrapper, input);
        
        // Move input into wrapper
        wrapper.appendChild(input);
        
        // Change input type and add classes
        input.type = 'text';
        input.classList.add('form-control');
        input.readOnly = true;
        input.placeholder = 'Select date...';
        
        // Add calendar icon
        const icon = document.createElement('i');
        icon.className = 'fas fa-calendar-alt calendar-icon';
        wrapper.appendChild(icon);
        
        // Get constraints from original input
        const minDate = input.getAttribute('min');
        const maxDate = input.getAttribute('max');
        const required = input.hasAttribute('required');
        
        // Initialize datepicker
        this.initializeSingleDatePicker(input, {
            minDate: minDate,
            maxDate: maxDate,
            required: required
        });
    }
    
    initializeCustomDatePickers() {
        // Initialize existing custom date pickers
        const customDateInputs = document.querySelectorAll('.elegant-datepicker:not(.elegant-initialized)');
        
        customDateInputs.forEach(input => {
            input.classList.add('elegant-initialized');
            this.initializeSingleDatePicker(input);
        });
    }
    
    initializeSingleDatePicker(input, options = {}) {
        // Prevent re-initialization
        if (input.dataset.datepickerInitialized === 'true') {
            return;
        }
        input.dataset.datepickerInitialized = 'true';
        
        const config = { ...this.defaultOptions };
        
        // Apply constraints
        if (options.minDate) {
            config.startDate = new Date(options.minDate);
        }
        if (options.maxDate) {
            config.endDate = new Date(options.maxDate);
        }
        
        // Allow backdate for completion dates (no restriction)
        // Completion dates can be in the past, present, or future
        
        // Special handling for deadline dates (future dates only)
        if (input.name === 'tenggat_waktu' || input.classList.contains('deadline-date')) {
            config.startDate = new Date(); // Today or later only
        }
        
        // Initialize Bootstrap Datepicker
        // Prefer to attach the popup within the input wrapper so it stays aligned while scrolling
        const wrapper = input.closest('.elegant-date-input, .elegant-date-group');
        if (wrapper) {
            config.container = wrapper; // keep dropdown within the same scrolling context
        }

        $(input).datepicker(config)
            .on('show', (e) => {
                // Add elegant class to datepicker
                setTimeout(() => {
                    $('.datepicker').addClass('elegant-datepicker-popup');
                    // Recalculate position after styles applied
                    try { $(input).datepicker('place'); } catch (_) {}
                }, 10);
            })
            .on('changeDate', (e) => {
                this.handleDateChange(input, e.date, options);
            })
            .on('clearDate', (e) => {
                this.handleDateClear(input, options);
            });
        
        // Handle click on wrapper to open datepicker
        if (wrapper) {
            wrapper.addEventListener('click', (e) => {
                if (e.target === input || e.target.classList.contains('calendar-icon') || e.target.classList.contains('input-group-text')) {
                    $(input).datepicker('show');
                }
            });

            // Keep popup aligned on scroll of the nearest scrollable container
            const scrollParent = this.getScrollParent(wrapper) || window;
            const reposition = () => { try { $(input).datepicker('place'); } catch (_) {} };
            scrollParent.addEventListener('scroll', reposition, { passive: true });
            window.addEventListener('resize', reposition, { passive: true });
        }
        
        // Prevent manual typing
        input.addEventListener('keydown', (e) => {
            // Allow tab, escape, delete, backspace
            if ([9, 27, 46, 8].includes(e.keyCode) || 
                // Allow Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
                (e.keyCode === 65 && e.ctrlKey === true) || 
                (e.keyCode === 67 && e.ctrlKey === true) || 
                (e.keyCode === 86 && e.ctrlKey === true) || 
                (e.keyCode === 88 && e.ctrlKey === true)) {
                return;
            }
            // Prevent all other keys
            e.preventDefault();
        });
        
        // Handle paste events
        input.addEventListener('paste', (e) => {
            e.preventDefault();
        });
    }

    getScrollParent(element) {
        if (!element) return null;
        let parent = element.parentElement;
        while (parent && parent !== document.body) {
            const style = getComputedStyle(parent);
            const overflowY = style.overflowY;
            if (/(auto|scroll|overlay)/.test(overflowY)) {
                return parent;
            }
            parent = parent.parentElement;
        }
        return document.scrollingElement || document.documentElement;
    }
    
    handleDateChange(input, date, options = {}) {
        // Format date for display
        const displayDate = this.formatDate(date, 'dd/mm/yyyy');
        input.value = displayDate;
        
        // Handle hidden input for forms that need Y-m-d format
        const hiddenInput = document.getElementById(input.id.replace('_display', ''));
        if (hiddenInput) {
            hiddenInput.value = this.formatDate(date, 'yyyy-mm-dd');
        }
        
        // Validate date constraints
        this.validateDate(input, date, options);
        
        // Trigger change event for form validation
        input.dispatchEvent(new Event('change', { bubbles: true }));
    }
    
    handleDateClear(input, options = {}) {
        input.value = '';
        
        // Clear hidden input
        const hiddenInput = document.getElementById(input.id.replace('_display', ''));
        if (hiddenInput) {
            hiddenInput.value = '';
        }
        
        // Remove validation classes
        input.classList.remove('is-invalid', 'is-valid');
        
        // Trigger change event
        input.dispatchEvent(new Event('change', { bubbles: true }));
    }
    
    validateDate(input, date, options = {}) {
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        let isValid = true;
        let errorMessage = '';
        
        // Check if date is in the past (only for deadline dates, not completion dates)
        if (input.classList.contains('deadline-date') && date < today) {
            isValid = false;
            errorMessage = 'Date cannot be in the past';
        }
        
        // Check min/max constraints
        if (options.minDate && date < new Date(options.minDate)) {
            isValid = false;
            errorMessage = 'Date is before minimum allowed date';
        }
        
        if (options.maxDate && date > new Date(options.maxDate)) {
            isValid = false;
            errorMessage = 'Date is after maximum allowed date';
        }
        
        // Apply validation styling
        if (isValid) {
            input.classList.remove('is-invalid');
            input.classList.add('is-valid');
            input.removeAttribute('title');
        } else {
            input.classList.remove('is-valid');
            input.classList.add('is-invalid');
            input.setAttribute('title', errorMessage);
            
            // Show tooltip if available
            if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
                new bootstrap.Tooltip(input, {
                    title: errorMessage,
                    trigger: 'manual'
                }).show();
                
                setTimeout(() => {
                    bootstrap.Tooltip.getInstance(input)?.dispose();
                }, 3000);
            }
        }
    }
    
    initializeDateRangePickers() {
        // Handle date range inputs (start_date and end_date)
        const startDateInputs = document.querySelectorAll('input[name="start_date"]:not(.elegant-range-initialized)');
        const endDateInputs = document.querySelectorAll('input[name="end_date"]:not(.elegant-range-initialized)');
        
        startDateInputs.forEach(startInput => {
            startInput.classList.add('elegant-range-initialized');
            const endInput = document.querySelector('input[name="end_date"]');
            
            if (endInput) {
                this.initializeDateRangePair(startInput, endInput);
            }
        });
    }
    
    initializeDateRangePair(startInput, endInput) {
        // Convert to elegant date pickers
        this.convertToElegantDatePicker(startInput);
        this.convertToElegantDatePicker(endInput);
        
        // Link the date pickers
        $(startInput).on('changeDate', (e) => {
            $(endInput).datepicker('setStartDate', e.date);
        });
        
        $(endInput).on('changeDate', (e) => {
            $(startInput).datepicker('setEndDate', e.date);
        });
    }
    
    formatDate(date, format) {
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = date.getFullYear();
        
        switch (format) {
            case 'dd/mm/yyyy':
                return `${day}/${month}/${year}`;
            case 'yyyy-mm-dd':
                return `${year}-${month}-${day}`;
            default:
                return date.toLocaleDateString();
        }
    }
    
    // Public method to manually initialize a date picker
    static initializeElement(element, options = {}) {
        const instance = new ElegantDatePicker();
        instance.initializeSingleDatePicker(element, options);
    }
    
    // Public method to get date value in specific format
    static getDateValue(input, format = 'yyyy-mm-dd') {
        const dateStr = input.value;
        if (!dateStr) return '';
        
        // Parse dd/mm/yyyy format
        const parts = dateStr.split('/');
        if (parts.length === 3) {
            const date = new Date(parts[2], parts[1] - 1, parts[0]);
            return new ElegantDatePicker().formatDate(date, format);
        }
        
        return dateStr;
    }
}

// Auto-initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Initialize elegant date pickers
    window.elegantDatePicker = new ElegantDatePicker();
    
    // Make it globally available
    window.ElegantDatePicker = ElegantDatePicker;
});

// Re-initialize on dynamic content load (only convert new inputs)
if (typeof $ !== 'undefined') {
    $(document).on('shown.bs.modal', function() {
        setTimeout(() => {
            if (window.elegantDatePicker) {
                // Only convert new date inputs, not re-initialize everything
                window.elegantDatePicker.convertBasicDateInputs();
            }
        }, 100);
    });
}
