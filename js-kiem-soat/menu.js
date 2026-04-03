// Menu Toggle Handler
document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.getElementById('menuToggle');
    const categoryMenu = document.getElementById('categoryMenu');
    
    if (!menuToggle || !categoryMenu) return;
    
    // Toggle menu on button click
    menuToggle.addEventListener('click', function(e) {
        e.preventDefault();
        categoryMenu.classList.toggle('active');
        menuToggle.classList.toggle('active');
    });
    
    // Close menu when clicking on a link
    const categoryLinks = categoryMenu.querySelectorAll('.category-link');
    categoryLinks.forEach(link => {
        link.addEventListener('click', function() {
            categoryMenu.classList.remove('active');
            menuToggle.classList.remove('active');
        });
    });
    
    // Close menu when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.navbar')) {
            categoryMenu.classList.remove('active');
            menuToggle.classList.remove('active');
        }
    });
});
