// Inject sticky contact bar on mobile
(function() {
    // Check if mobile
    function isMobile() {
        return window.innerWidth <= 768;
    }
    
    // Create sticky contact bar
    function createStickyBar() {
        if (!isMobile()) return;
        
        // Check if already exists
        if (document.getElementById('sticky-contact')) return;
        
        const stickyBar = document.createElement('div');
        stickyBar.id = 'sticky-contact';
        stickyBar.className = 'sticky-contact';
        
        // Hotline button
        const hotnlineBtn = document.createElement('a');
        hotnlineBtn.href = 'tel:0866021711';
        hotnlineBtn.className = 'sticky-btn sticky-hotline';
        hotnlineBtn.title = 'Gọi hotline';
        hotnlineBtn.innerHTML = '☎️<br><small>0866<br>021<br>711</small>';
        
        // Zalo button
        const zaloBtn = document.createElement('a');
        zaloBtn.href = 'https://zalo.me/0866021711';
        zaloBtn.target = '_blank';
        zaloBtn.className = 'sticky-btn sticky-zalo';
        zaloBtn.title = 'Zalo';
        zaloBtn.innerHTML = 'Z';
        
        // Facebook button
        const facebookBtn = document.createElement('a');
        facebookBtn.href = 'https://www.facebook.com/profile.php?id=61580260876532';
        facebookBtn.target = '_blank';
        facebookBtn.className = 'sticky-btn sticky-facebook';
        facebookBtn.title = 'Facebook';
        facebookBtn.innerHTML = 'f';
        
        // Messenger button
        const messengerBtn = document.createElement('a');
        messengerBtn.href = 'https://www.facebook.com/profile.php?id=61580260876532';
        messengerBtn.target = '_blank';
        messengerBtn.className = 'sticky-btn sticky-messenger';
        messengerBtn.title = 'Messenger';
        messengerBtn.innerHTML = 'M';
        
        // Append buttons
        stickyBar.appendChild(hotnlineBtn);
        stickyBar.appendChild(zaloBtn);
        stickyBar.appendChild(facebookBtn);
        stickyBar.appendChild(messengerBtn);
        
        // Add to body
        document.body.appendChild(stickyBar);
    }
    
    // Initialize on page load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', createStickyBar);
    } else {
        createStickyBar();
    }
    
    // Re-create on window resize
    window.addEventListener('resize', function() {
        const stickyBar = document.getElementById('sticky-contact');
        if (isMobile() && !stickyBar) {
            createStickyBar();
        } else if (!isMobile() && stickyBar) {
            stickyBar.remove();
        }
    });
})();
