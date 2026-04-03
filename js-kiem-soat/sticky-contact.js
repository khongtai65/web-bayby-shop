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
        const hotlineBtn = document.createElement('a');
        hotlineBtn.href = 'tel:0866021711';
        hotlineBtn.className = 'sticky-btn sticky-hotline';
        hotlineBtn.title = 'Gọi hotline';
        hotlineBtn.textContent = '☎️';
        hotlineBtn.style.fontSize = '1.3rem';
        
        // Zalo button
        const zaloBtn = document.createElement('a');
        zaloBtn.href = 'https://zalo.me/0866021711';
        zaloBtn.target = '_blank';
        zaloBtn.className = 'sticky-btn sticky-zalo';
        zaloBtn.title = 'Zalo';
        zaloBtn.textContent = 'Z';
        
        // Facebook button
        const facebookBtn = document.createElement('a');
        facebookBtn.href = 'https://www.facebook.com/profile.php?id=61580260876532';
        facebookBtn.target = '_blank';
        facebookBtn.className = 'sticky-btn sticky-facebook';
        facebookBtn.title = 'Facebook';
        facebookBtn.textContent = 'f';
        
        // Messenger button
        const messengerBtn = document.createElement('a');
        messengerBtn.href = 'https://www.facebook.com/profile.php?id=61580260876532';
        messengerBtn.target = '_blank';
        messengerBtn.className = 'sticky-btn sticky-messenger';
        messengerBtn.title = 'Messenger';
        messengerBtn.textContent = 'M';
        
        // Cart button
        const cartBtn = document.createElement('a');
        cartBtn.href = 'gio-hang.php';
        cartBtn.className = 'sticky-btn sticky-cart';
        cartBtn.title = 'Giỏ hàng';
        cartBtn.textContent = '🛒';
        cartBtn.style.fontSize = '1.2rem';
        cartBtn.style.position = 'relative';
        
        // Add badge to cart
        const cartCount = document.getElementById('cart-count');
        if (cartCount) {
            const badge = document.createElement('span');
            badge.className = 'sticky-cart-badge';
            badge.textContent = cartCount.textContent || '0';
            cartBtn.appendChild(badge);
        }
        
        // Append buttons
        stickyBar.appendChild(hotlineBtn);
        stickyBar.appendChild(zaloBtn);
        stickyBar.appendChild(facebookBtn);
        stickyBar.appendChild(messengerBtn);
        stickyBar.appendChild(cartBtn);
        
        // Add to body
        document.body.appendChild(stickyBar);
        
        console.log('Sticky contact bar created');
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
