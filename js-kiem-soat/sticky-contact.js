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
        
        // Apply inline styles to bar
        stickyBar.style.cssText = `
            position: fixed;
            right: 0;
            top: 65px;
            width: 50px;
            height: auto;
            max-height: calc(100vh - 65px);
            background: linear-gradient(180deg, #E74C3C 0%, #FF69B4 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            gap: 8px;
            padding: 8px 4px;
            z-index: 98;
            box-shadow: -3px 0 10px rgba(0,0,0,0.3);
            border-radius: 8px 0 0 8px;
            overflow-y: auto;
            overflow-x: hidden;
        `;
        
        // Helper to create button
        function createButton(href, className, title, text, bgColor, textColor) {
            const btn = document.createElement('a');
            btn.href = href;
            btn.target = className.includes('phone') ? '' : '_blank';
            btn.className = `sticky-btn ${className}`;
            btn.title = title;
            btn.textContent = text;
            
            btn.style.cssText = `
                width: 42px;
                height: 42px;
                min-width: 42px;
                min-height: 42px;
                background-color: ${bgColor};
                color: ${textColor};
                border-radius: 6px;
                display: flex;
                align-items: center;
                justify-content: center;
                text-decoration: none;
                font-weight: bold;
                font-size: ${bgColor === '#E74C3C' ? '1.3rem' : '0.9rem'};
                transition: all 0.3s;
                box-shadow: 0 2px 6px rgba(0,0,0,0.2);
                flex-shrink: 0;
                cursor: pointer;
                border: none;
                padding: 0;
                line-height: 1;
            `;
            
            btn.onmousedown = function() { this.style.transform = 'scale(0.95)'; };
            btn.onmouseup = function() { this.style.transform = 'scale(1)'; };
            btn.onmouseover = function() { this.style.boxShadow = '0 4px 8px rgba(0,0,0,0.3)'; this.style.transform = 'scale(1.05)'; };
            btn.onmouseout = function() { this.style.boxShadow = '0 2px 6px rgba(0,0,0,0.2)'; this.style.transform = 'scale(1)'; };
            
            return btn;
        }
        
        // Create buttons
        const hotlineBtn = createButton('tel:0866021711', 'sticky-hotline', 'Gọi hotline', '☎️', '#DC143C', 'white');
        const zaloBtn = createButton('https://zalo.me/0866021711', 'sticky-zalo', 'Zalo', 'Z', '#0084FF', 'white');
        const facebookBtn = createButton('https://www.facebook.com/profile.php?id=61580260876532', 'sticky-facebook', 'Facebook', 'f', '#1877F2', 'white');
        const messengerBtn = createButton('https://www.facebook.com/profile.php?id=61580260876532', 'sticky-messenger', 'Messenger', 'M', '#0084FF', 'white');
        const cartBtn = createButton('gio-hang.php', 'sticky-cart', 'Giỏ hàng', '🛒', '#E74C3C', 'white');
        
        // Add cart badge
        const cartCount = document.getElementById('cart-count');
        if (cartCount && cartCount.textContent !== '0') {
            const badge = document.createElement('span');
            badge.textContent = cartCount.textContent;
            badge.style.cssText = `
                position: absolute;
                top: -8px;
                right: -8px;
                background-color: #FFD700;
                color: #333;
                border-radius: 50%;
                width: 20px;
                height: 20px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 0.75rem;
                font-weight: bold;
                border: 2px solid white;
            `;
            cartBtn.style.position = 'relative';
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
        
        console.log('✓ Sticky contact bar created successfully');
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
