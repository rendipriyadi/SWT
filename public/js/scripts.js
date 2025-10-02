document.addEventListener('DOMContentLoaded', function () {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const layoutSidenav = document.getElementById('layoutSidenav');
    const sidebarClose = document.getElementById('sidebarClose');
    const sidenavNav = document.getElementById('layoutSidenav_nav');
    const content = document.getElementById('layoutSidenav_content');
    const body = document.body;
    let windowWidth = window.innerWidth;
    
    // Create overlay for mobile
    const overlay = document.createElement('div');
    overlay.className = 'sidebar-overlay';
    document.body.appendChild(overlay);

    // Initialize sidebar state based on screen size
    function initSidebar() {
        if (windowWidth < 768) {
            // Mobile: sidebar always hidden initially
            body.classList.remove('sb-sidenav-toggled');
        } else {
            // Desktop: use saved state or default open
            const savedState = localStorage.getItem('sb|sidebar-toggle');
            if (savedState === 'true') {
                body.classList.add('sb-sidenav-toggled');
            } else {
                body.classList.remove('sb-sidenav-toggled');
            }
        }
    }

    // Toggle sidebar state
    function toggleSidebar() {
        const isMobile = windowWidth < 768;
        
        if (isMobile) {
            // Mobile behavior (reverse logic - toggled means sidebar is visible)
            if (body.classList.contains('sb-sidenav-toggled')) {
                body.classList.remove('sb-sidenav-toggled');
            } else {
                body.classList.add('sb-sidenav-toggled');
            }
        } else {
            // Desktop behavior
            if (body.classList.contains('sb-sidenav-toggled')) {
                body.classList.remove('sb-sidenav-toggled');
                localStorage.setItem('sb|sidebar-toggle', 'false');
            } else {
                body.classList.add('sb-sidenav-toggled');
                localStorage.setItem('sb|sidebar-toggle', 'true');
            }
        }
    }

    initSidebar();

    // Event listeners
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            toggleSidebar();
        });
    }

    if (sidebarClose) {
        sidebarClose.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            if (windowWidth < 768) {
                body.classList.remove('sb-sidenav-toggled');
            } else {
                body.classList.add('sb-sidenav-toggled');
                localStorage.setItem('sb|sidebar-toggle', 'true');
            }
        });
    }

    // Overlay click handler for mobile
    overlay.addEventListener('click', function() {
        if (windowWidth < 768 && body.classList.contains('sb-sidenav-toggled')) {
            body.classList.remove('sb-sidenav-toggled');
        }
    });

    // Update window width when resizing
    window.addEventListener('resize', function() {
        const oldWidth = windowWidth;
        windowWidth = window.innerWidth;
        
        // Transition from desktop to mobile
        if (oldWidth >= 768 && windowWidth < 768) {
            body.classList.remove('sb-sidenav-toggled');
        } 
        // Transition from mobile to desktop
        else if (oldWidth < 768 && windowWidth >= 768) {
            const savedState = localStorage.getItem('sb|sidebar-toggle');
            if (savedState === 'true') {
                body.classList.add('sb-sidenav-toggled');
            } else {
                body.classList.remove('sb-sidenav-toggled');
            }
        }
    });

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
        
        const datetimeElement = document.querySelector('.datetime');
        if (datetimeElement) {
            datetimeElement.textContent = formattedDateTime;
        }
    }

    // Update datetime immediately and then every second
    if (document.querySelector('.datetime')) {
        updateDateTime();
        setInterval(updateDateTime, 1000);
    }

    // Fix scroll issues on page load
    setTimeout(function() {
        window.scrollTo(0, 0);
    }, 100);
});