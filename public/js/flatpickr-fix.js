(function() {
    'use strict';
    
    function waitForFunctions() {
        window._flatpickrHistoryInitialized = false;
        window._flatpickrReportsInitialized = false;
        
        const originalInitHistory = window.initHistoryDatepickers;
        
        if (typeof originalInitHistory === 'function') {
            window.initHistoryDatepickers = function() {
                const $start = $('#history_created_start');
                const $end = $('#history_created_end');
                
                if ($start[0] && $start[0]._flatpickr && $end[0] && $end[0]._flatpickr) {
                    return;
                }
                
                try {
                    originalInitHistory.apply(this, arguments);
                    window._flatpickrHistoryInitialized = true;
                } catch(e) {
                    window._flatpickrHistoryInitialized = false;
                }
            };
        }
        
        const originalInitReports = window.initReportsDatepickers;
        
        if (typeof originalInitReports === 'function') {
            window.initReportsDatepickers = function() {
                const $start = $('#report_created_start');
                const $end = $('#report_created_end');
                
                if ($start[0] && $start[0]._flatpickr && $end[0] && $end[0]._flatpickr) {
                    return;
                }
                
                try {
                    originalInitReports.apply(this, arguments);
                    window._flatpickrReportsInitialized = true;
                } catch(e) {
                    window._flatpickrReportsInitialized = false;
                }
            };
        }
    }
    
    if (typeof jQuery !== 'undefined' && typeof window.initHistoryDatepickers === 'function') {
        waitForFunctions();
    } else {
        setTimeout(waitForFunctions, 100);
    }
    
    window.addEventListener('beforeunload', function() {
        window._flatpickrHistoryInitialized = false;
        window._flatpickrReportsInitialized = false;
    });
})();
