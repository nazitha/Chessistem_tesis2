// Responsive functionality for ChessSystem

document.addEventListener('DOMContentLoaded', function() {
    
    // Mobile menu functionality
    // Si el layout usa Alpine para el menú móvil, no re-anexar listeners
    const usingAlpine = document.querySelector('nav[x-data]');
    if (!usingAlpine) {
        initMobileMenu();
    }
    
    // DataTables responsive improvements
    initDataTablesResponsive();
    
    // Touch improvements
    initTouchImprovements();
    
    // Window resize handler
    initResizeHandler();
    
    // Print functionality
    initPrintFunctionality();
});

function initMobileMenu() {
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');
    
    if (mobileMenuButton && mobileMenu) {
        console.log('Mobile menu elements found:', { mobileMenuButton, mobileMenu });
        
        mobileMenuButton.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Mobile menu button clicked');
            
            mobileMenu.classList.toggle('hidden');
            
            // Toggle aria-expanded
            const isExpanded = !mobileMenu.classList.contains('hidden');
            mobileMenuButton.setAttribute('aria-expanded', isExpanded);
            
            console.log('Mobile menu toggled, isExpanded:', isExpanded);
        });
        
        // Close menu when clicking on links
        const mobileLinks = mobileMenu.querySelectorAll('a');
        mobileLinks.forEach(link => {
            link.addEventListener('click', function() {
                mobileMenu.classList.add('hidden');
                mobileMenuButton.setAttribute('aria-expanded', 'false');
            });
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            if (!mobileMenuButton.contains(event.target) && !mobileMenu.contains(event.target)) {
                mobileMenu.classList.add('hidden');
                mobileMenuButton.setAttribute('aria-expanded', 'false');
            }
        });
        
        // Close menu on escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && !mobileMenu.classList.contains('hidden')) {
                mobileMenu.classList.add('hidden');
                mobileMenuButton.setAttribute('aria-expanded', 'false');
            }
        });
    } else {
        console.log('Mobile menu elements not found');
    }
}

function initDataTablesResponsive() {
    // Wait for DataTables to be initialized
    const checkDataTables = setInterval(() => {
        if (typeof $.fn.DataTable !== 'undefined') {
            clearInterval(checkDataTables);
            
            // Add responsive classes to DataTables
            $('.dataTables_wrapper').addClass('table-responsive');
            
            // Improve mobile DataTables
            if (window.innerWidth <= 768) {
                $('.dataTables_wrapper .dataTables_length').addClass('mobile-hidden');
                $('.dataTables_wrapper .dataTables_filter input').addClass('w-full');
            }
        }
    }, 100);
}

function initTouchImprovements() {
    // Add touch-friendly classes to buttons
    const buttons = document.querySelectorAll('.btn, .nav-link, .paginate_button');
    buttons.forEach(button => {
        button.style.minHeight = '44px';
        button.style.minWidth = '44px';
    });
    
    // Improve touch scrolling
    const scrollableElements = document.querySelectorAll('.table-responsive, .modal-body');
    scrollableElements.forEach(element => {
        element.style.webkitOverflowScrolling = 'touch';
    });
}

function initResizeHandler() {
    let resizeTimeout;
    
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(function() {
            handleResize();
        }, 250);
    });
}

function handleResize() {
    const width = window.innerWidth;
    
    // Update mobile menu visibility
    const mobileMenu = document.getElementById('mobile-menu');
    if (width >= 768 && mobileMenu) {
        mobileMenu.classList.add('hidden');
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        if (mobileMenuButton) {
            mobileMenuButton.setAttribute('aria-expanded', 'false');
        }
    }
    
    // Update DataTables responsive behavior
    if (typeof $.fn.DataTable !== 'undefined') {
        $('.dataTables_wrapper').each(function() {
            const table = $(this).find('table').DataTable();
            if (table) {
                table.columns.adjust().responsive.recalc();
            }
        });
    }
    
    // Update card layouts
    updateCardLayouts(width);
}

function updateCardLayouts(width) {
    const cards = document.querySelectorAll('.card-responsive');
    
    cards.forEach(card => {
        if (width <= 640) {
            card.classList.add('mobile-full');
        } else {
            card.classList.remove('mobile-full');
        }
    });
}

function initPrintFunctionality() {
    // Add print button functionality
    const printButtons = document.querySelectorAll('.print-btn');
    printButtons.forEach(button => {
        button.addEventListener('click', function() {
            window.print();
        });
    });
    
    // Hide non-printable elements when printing
    window.addEventListener('beforeprint', function() {
        const nonPrintElements = document.querySelectorAll('.no-print, .navbar, .footer, .btn');
        nonPrintElements.forEach(element => {
            element.style.display = 'none';
        });
    });
    
    window.addEventListener('afterprint', function() {
        const nonPrintElements = document.querySelectorAll('.no-print, .navbar, .footer, .btn');
        nonPrintElements.forEach(element => {
            element.style.display = '';
        });
    });
}

// Utility functions
function isMobile() {
    return window.innerWidth <= 768;
}

function isTablet() {
    return window.innerWidth > 768 && window.innerWidth <= 1024;
}

function isDesktop() {
    return window.innerWidth > 1024;
}

// Export functions for global use
window.ResponsiveUtils = {
    isMobile,
    isTablet,
    isDesktop,
    handleResize
};

// DataTables responsive configuration
if (typeof $.fn.DataTable !== 'undefined') {
    $.extend($.fn.dataTable.defaults, {
        responsive: true,
        language: {
            "lengthMenu": "Mostrar _MENU_ registros por página",
            "zeroRecords": "No se encontraron registros",
            "info": "Mostrando página _PAGE_ de _PAGES_",
            "infoEmpty": "No hay registros disponibles",
            "infoFiltered": "(filtrado de _MAX_ registros totales)",
            "search": "Buscar:",
            "paginate": {
                "first": "Primero",
                "last": "Último",
                "next": "Siguiente",
                "previous": "Anterior"
            }
        },
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
             '<"row"<"col-sm-12"tr>>' +
             '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"]],
        autoWidth: false,
        scrollX: true
    });
}
