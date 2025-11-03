document.addEventListener('DOMContentLoaded', function () {
    // Sidebar is now handled by main.blade.php inline script
    // This prevents conflict and flicker on page load
    
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