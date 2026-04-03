// Sticky Contact Bar - Mobile Only
(function() {
    'use strict';
    
    function init() {
        // Only on mobile
        if (window.innerWidth > 768) return;
        if (document.getElementById('sticky-contact')) return;
        
        // Create container
        const bar = document.createElement('div');
        bar.id = 'sticky-contact';
        bar.style.cssText = `
            position: fixed; right: 0; top: 65px; width: 50px; 
            background: linear-gradient(180deg, #E74C3C 0%, #FF69B4 100%);
            display: flex; flex-direction: column; align-items: center;
            gap: 8px; padding: 8px 4px; z-index: 99;
            box-shadow: -3px 0 10px rgba(0,0,0,0.3);
            border-radius: 8px 0 0 8px; overflow-y: auto; max-height: calc(100vh - 65px);
        `;
        
        // Button config
        const buttons = [
            { href: 'tel:0866021711', text: '☎️', title: 'Hotline', bg: '#DC143C' },
            { href: 'https://zalo.me/0866021711', text: 'Z', title: 'Zalo', bg: '#0084FF' },
            { href: 'https://www.facebook.com/profile.php?id=61580260876532', text: 'f', title: 'Facebook', bg: '#1877F2' },
            { href: 'https://www.facebook.com/profile.php?id=61580260876532', text: 'M', title: 'Messenger', bg: '#0084FF' },
            { href: 'gio-hang.php', text: '🛒', title: 'Giỏ hàng', bg: '#E74C3C' }
        ];
        
        // Add each button
        buttons.forEach((config, idx) => {
            const btn = document.createElement('a');
            btn.href = config.href;
            btn.target = config.href.startsWith('http') ? '_blank' : '';
            btn.title = config.title;
            btn.textContent = config.text;
            btn.style.cssText = `
                width: 42px; height: 42px; background: ${config.bg} !important; 
                color: white; border: none; border-radius: 6px;
                display: flex; align-items: center; justify-content: center;
                text-decoration: none; font-weight: bold; cursor: pointer;
                font-size: ${config.text === '☎️' ? '1.3rem' : config.text === '🛒' ? '1.2rem' : '0.9rem'};
                box-shadow: 0 2px 6px rgba(0,0,0,0.2);
                flex-shrink: 0; transition: all 0.2s;
            `;
            btn.onmousedown = () => btn.style.transform = 'scale(0.95)';
            btn.onmouseup = () => btn.style.transform = 'scale(1)';
            btn.onmouseover = () => { btn.style.transform = 'scale(1.05)'; btn.style.boxShadow = '0 4px 8px rgba(0,0,0,0.3)'; };
            btn.onmouseout = () => { btn.style.transform = 'scale(1)'; btn.style.boxShadow = '0 2px 6px rgba(0,0,0,0.2)'; };
            
            bar.appendChild(btn);
        });
        
        document.body.appendChild(bar);
        console.log('✓ Sticky contact bar created');
    }
    
    // Run on load & resize
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
    
    window.addEventListener('resize', () => {
        const bar = document.getElementById('sticky-contact');
        if (window.innerWidth <= 768 && !bar) {
            init();
        } else if (window.innerWidth > 768 && bar) {
            bar.remove();
        }
    });
})();
