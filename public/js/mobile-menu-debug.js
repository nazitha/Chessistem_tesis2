// Debug script for mobile menu
console.log('Mobile menu debug script loaded');

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Content Loaded');
    
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');
    
    console.log('Mobile menu button:', mobileMenuButton);
    console.log('Mobile menu:', mobileMenu);
    
    if (mobileMenuButton) {
        console.log('Adding click listener to mobile menu button');
        mobileMenuButton.addEventListener('click', function(e) {
            console.log('Mobile menu button clicked!');
            e.preventDefault();
            e.stopPropagation();
            
            if (mobileMenu) {
                console.log('Toggling mobile menu');
                mobileMenu.classList.toggle('hidden');
                console.log('Mobile menu classes:', mobileMenu.className);
            }
        });
    } else {
        console.log('Mobile menu button not found!');
    }
});
