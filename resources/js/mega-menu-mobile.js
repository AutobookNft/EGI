/**
 * MEGA MENU MOBILE - Revolutionary Mobile Experience
 * Mobile-first navigation with touch gestures and smooth animations
 */

document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu management
    initializeMobileMenu();
    
    // Touch gesture support
    if (window.innerWidth <= 640) {
        initializeTouchGestures();
    }
    
    // Handle orientation change
    window.addEventListener('orientationchange', handleOrientationChange);
});

function initializeMobileMenu() {
    const isMobile = window.innerWidth <= 640;
    
    if (!isMobile) return;
    
    // Find all dropdown triggers
    const dropdownTriggers = document.querySelectorAll('[data-dropdown-toggle]');
    
    dropdownTriggers.forEach(trigger => {
        trigger.addEventListener('click', function(e) {
            e.preventDefault();
            const dropdown = this.nextElementSibling?.querySelector('.mega-menu-container');
            
            if (dropdown) {
                openMobileMenu(dropdown);
            }
        });
    });
}

function openMobileMenu(menuContainer) {
    // Add mobile overlay class
    menuContainer.classList.add('mobile-active');
    
    // Prevent body scroll
    document.body.style.overflow = 'hidden';
    document.body.style.position = 'fixed';
    document.body.style.width = '100%';
    
    // Add close button functionality
    addCloseButtonListener(menuContainer);
    
    // Add backdrop click to close
    addBackdropCloseListener(menuContainer);
    
    // Add escape key listener
    addEscapeKeyListener(menuContainer);
    
    // Trigger enter animation
    requestAnimationFrame(() => {
        menuContainer.style.transform = 'translateY(0)';
        menuContainer.style.opacity = '1';
    });
}

function closeMobileMenu(menuContainer) {
    // Remove mobile overlay class
    menuContainer.classList.remove('mobile-active');
    
    // Restore body scroll
    document.body.style.overflow = '';
    document.body.style.position = '';
    document.body.style.width = '';
    
    // Trigger exit animation
    menuContainer.style.transform = 'translateY(100%)';
    menuContainer.style.opacity = '0';
    
    // Remove event listeners
    removeAllListeners();
    
    // Close dropdown after animation
    setTimeout(() => {
        const dropdownComponent = menuContainer.closest('[x-data]');
        if (dropdownComponent && dropdownComponent.__x) {
            dropdownComponent.__x.$data.open = false;
        }
    }, 400);
}

function addCloseButtonListener(menuContainer) {
    const userHeader = menuContainer.querySelector('.user-header-card');
    
    if (userHeader) {
        userHeader.addEventListener('click', function(e) {
            // Check if click is on the close button area (top-right)
            const rect = this.getBoundingClientRect();
            const closeButtonArea = {
                left: rect.right - 80,
                top: rect.top,
                right: rect.right,
                bottom: rect.top + 80
            };
            
            if (e.clientX >= closeButtonArea.left && 
                e.clientX <= closeButtonArea.right &&
                e.clientY >= closeButtonArea.top && 
                e.clientY <= closeButtonArea.bottom) {
                closeMobileMenu(menuContainer);
            }
        });
    }
}

function addBackdropCloseListener(menuContainer) {
    menuContainer.addEventListener('click', function(e) {
        if (e.target === this) {
            closeMobileMenu(menuContainer);
        }
    });
}

function addEscapeKeyListener(menuContainer) {
    function handleEscape(e) {
        if (e.key === 'Escape') {
            closeMobileMenu(menuContainer);
        }
    }
    
    document.addEventListener('keydown', handleEscape);
    
    // Store reference for cleanup
    menuContainer._escapeHandler = handleEscape;
}

function removeAllListeners() {
    // Remove escape key listeners
    const activeMenus = document.querySelectorAll('.mega-menu-container.mobile-active');
    activeMenus.forEach(menu => {
        if (menu._escapeHandler) {
            document.removeEventListener('keydown', menu._escapeHandler);
            delete menu._escapeHandler;
        }
    });
}

function initializeTouchGestures() {
    let startY = 0;
    let currentY = 0;
    let isDragging = false;
    
    document.addEventListener('touchstart', function(e) {
        const menuContainer = e.target.closest('.mega-menu-container');
        if (!menuContainer || !menuContainer.classList.contains('mobile-active')) return;
        
        startY = e.touches[0].clientY;
        isDragging = true;
        menuContainer.style.transition = 'none';
    }, { passive: true });
    
    document.addEventListener('touchmove', function(e) {
        if (!isDragging) return;
        
        const menuContainer = e.target.closest('.mega-menu-container');
        if (!menuContainer) return;
        
        currentY = e.touches[0].clientY;
        const deltaY = currentY - startY;
        
        // Only allow downward swipe to close
        if (deltaY > 0) {
            const translateY = Math.min(deltaY, window.innerHeight);
            menuContainer.style.transform = `translateY(${translateY}px)`;
            menuContainer.style.opacity = Math.max(0, 1 - (deltaY / (window.innerHeight * 0.3)));
        }
    }, { passive: true });
    
    document.addEventListener('touchend', function(e) {
        if (!isDragging) return;
        
        const menuContainer = e.target.closest('.mega-menu-container');
        if (!menuContainer) return;
        
        isDragging = false;
        menuContainer.style.transition = '';
        
        const deltaY = currentY - startY;
        const threshold = window.innerHeight * 0.2;
        
        if (deltaY > threshold) {
            closeMobileMenu(menuContainer);
        } else {
            // Snap back to original position
            menuContainer.style.transform = 'translateY(0)';
            menuContainer.style.opacity = '1';
        }
    }, { passive: true });
}

function handleOrientationChange() {
    // Close any open mobile menus on orientation change
    const activeMobileMenus = document.querySelectorAll('.mega-menu-container.mobile-active');
    activeMobileMenus.forEach(menu => {
        closeMobileMenu(menu);
    });
    
    // Re-initialize if switching to mobile
    setTimeout(() => {
        if (window.innerWidth <= 640) {
            initializeTouchGestures();
        }
    }, 500);
}

// Handle resize events
let resizeTimeout;
window.addEventListener('resize', function() {
    clearTimeout(resizeTimeout);
    resizeTimeout = setTimeout(() => {
        const isMobile = window.innerWidth <= 640;
        const activeMobileMenus = document.querySelectorAll('.mega-menu-container.mobile-active');
        
        // Close mobile menus if switching to desktop
        if (!isMobile && activeMobileMenus.length > 0) {
            activeMobileMenus.forEach(menu => {
                closeMobileMenu(menu);
            });
        }
    }, 250);
});

// CSS injection for mobile states
if (window.innerWidth <= 640) {
    const style = document.createElement('style');
    style.textContent = `
        .mega-menu-container.mobile-active {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            z-index: 9999 !important;
        }
        
        body.mobile-menu-open {
            overflow: hidden !important;
            position: fixed !important;
            width: 100% !important;
        }
    `;
    document.head.appendChild(style);
}
