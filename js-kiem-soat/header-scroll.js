// Header Auto Hide/Show on Scroll
(function() {
    const topHeader = document.querySelector('.top-header');
    const navbar = document.querySelector('.navbar');
    let lastScrollTop = 0;
    let scrollTimeout;
    const scrollThreshold = 50; // Minimum scroll distance to trigger hide/show

    // Handle scroll event
    window.addEventListener('scroll', function() {
        if (!topHeader) return;

        let currentScroll = window.pageYOffset || document.documentElement.scrollTop;
        
        // Clear previous timeout
        clearTimeout(scrollTimeout);

        // Scrolling down - hide header
        if (currentScroll > lastScrollTop && currentScroll > scrollThreshold) {
            topHeader.classList.add('hide-header');
            if (navbar) navbar.classList.add('hide-header');
        }
        // Scrolling up - show header
        else if (currentScroll < lastScrollTop) {
            topHeader.classList.remove('hide-header');
            if (navbar) navbar.classList.remove('hide-header');
        }

        // Hide header when at top
        if (currentScroll <= 0) {
            topHeader.classList.remove('hide-header');
            if (navbar) navbar.classList.remove('hide-header');
        }

        lastScrollTop = currentScroll <= 0 ? 0 : currentScroll;
    }, false);

    // Auto hide header after 3 seconds of no scroll on mobile
    if (window.innerWidth <= 768) {
        let scrollTimer;
        
        window.addEventListener('scroll', function() {
            clearTimeout(scrollTimer);
            
            // Show header when scrolling stops
            topHeader.classList.remove('hide-header');
            if (navbar) navbar.classList.remove('hide-header');
            
            // Auto hide after 3 seconds of no scroll
            scrollTimer = setTimeout(function() {
                if (window.pageYOffset > scrollThreshold) {
                    topHeader.classList.add('hide-header');
                    if (navbar) navbar.classList.add('hide-header');
                }
            }, 3000);
        });
    }
})();
