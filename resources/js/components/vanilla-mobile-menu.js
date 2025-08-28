/**
 * Vanilla Mobile Navigation Menu - Pure JavaScript Implementation
 * Revolutionary mobile experience with touch gestures and smooth animations
 */

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('Vanilla Mobile Menu JS loaded');
    initializeMobileMenu();
    initializeTouchGestures();
    initializeCollectionDropdown();
    initializeActionButtons();
});

function initializeMobileMenu() {
    const trigger = document.querySelector('[data-mobile-menu-trigger]');
    const menu = document.querySelector('[data-mobile-menu]');
    const overlay = document.querySelector('[data-mobile-overlay]');
    const content = document.querySelector('[data-mobile-content]');
    const closeBtn = document.querySelector('[data-mobile-close]');
    const hamburgerIcon = trigger?.querySelector('.hamburger-icon');
    const closeIcon = trigger?.querySelector('.close-icon');

    if (!trigger || !menu || !content) return;

    let isOpen = false;

    // Toggle menu
    function toggleMenu() {
        if (isOpen) {
            closeMobileMenu();
        } else {
            openMobileMenu();
        }
    }

    // Open menu
    function openMobileMenu() {
        isOpen = true;

        // Show menu container
        menu.classList.remove('hidden');

        // Prevent body scroll
        document.body.style.overflow = 'hidden';
        document.body.style.position = 'fixed';
        document.body.style.width = '100%';

        // Animate content slide in
        requestAnimationFrame(() => {
            content.classList.remove('translate-x-full');
            content.classList.add('translate-x-0');
        });

        // Update button icons
        if (hamburgerIcon && closeIcon) {
            hamburgerIcon.classList.add('hidden');
            closeIcon.classList.remove('hidden');
        }

        // Add staggered animations to cards
        const cards = content.querySelectorAll('.mobile-card');
        cards.forEach((card, index) => {
            card.style.animationDelay = `${(index + 1) * 0.1}s`;
            card.classList.add('animate-slide-in-left');
        });

        // Focus management
        setTimeout(() => {
            const firstFocusable = content.querySelector('a, button, [tabindex]:not([tabindex="-1"])');
            if (firstFocusable) {
                firstFocusable.focus();
            }
        }, 300);
    }

    // Close menu
    function closeMobileMenu() {
        isOpen = false;

        // Animate content slide out
        content.classList.remove('translate-x-0');
        content.classList.add('translate-x-full');

        // Restore body scroll
        document.body.style.overflow = '';
        document.body.style.position = '';
        document.body.style.width = '';

        // Update button icons
        if (hamburgerIcon && closeIcon) {
            hamburgerIcon.classList.remove('hidden');
            closeIcon.classList.add('hidden');
        }

        // Remove card animations
        const cards = content.querySelectorAll('.mobile-card');
        cards.forEach(card => {
            card.style.animationDelay = '';
            card.classList.remove('animate-slide-in-left');
        });

        // Hide menu container after animation
        setTimeout(() => {
            if (!isOpen) { // Double check it wasn't reopened
                menu.classList.add('hidden');
            }
        }, 300);

        // Return focus to trigger
        trigger.focus();
    }

    // Event listeners
    trigger.addEventListener('click', toggleMenu);

    if (closeBtn) {
        closeBtn.addEventListener('click', closeMobileMenu);
    }

    if (overlay) {
        overlay.addEventListener('click', closeMobileMenu);
    }

    // Close on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && isOpen) {
            closeMobileMenu();
        }
    });

    // Close on resize to desktop
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 640 && isOpen) {
            closeMobileMenu();
        }
    });

    // Store methods globally for external access
    window.VanillaMobileMenu = {
        open: openMobileMenu,
        close: closeMobileMenu,
        toggle: toggleMenu,
        isOpen: () => isOpen
    };
}

function initializeTouchGestures() {
    const content = document.querySelector('[data-mobile-content]');
    if (!content) return;

    let startX = 0;
    let currentX = 0;
    let startY = 0;
    let currentY = 0;
    let isDragging = false;
    let isVerticalScroll = false;

    // Touch start
    content.addEventListener('touchstart', function(e) {
        startX = e.touches[0].clientX;
        startY = e.touches[0].clientY;
        isDragging = true;
        isVerticalScroll = false;
        content.style.transition = 'none';
    }, { passive: true });

    // Touch move
    content.addEventListener('touchmove', function(e) {
        if (!isDragging) return;

        currentX = e.touches[0].clientX;
        currentY = e.touches[0].clientY;

        const deltaX = currentX - startX;
        const deltaY = currentY - startY;

        // Determine if this is a vertical scroll gesture
        if (!isVerticalScroll && Math.abs(deltaY) > Math.abs(deltaX)) {
            isVerticalScroll = true;
            return; // Let the scroll happen naturally
        }

        // Horizontal swipe to close (only to the right)
        if (!isVerticalScroll && deltaX > 0) {
            e.preventDefault(); // Prevent scrolling during horizontal swipe

            const progress = Math.min(deltaX / content.offsetWidth, 1);
            const translateX = deltaX;

            content.style.transform = `translateX(${translateX}px)`;
            content.style.opacity = Math.max(0.3, 1 - progress * 0.7);
        }
    }, { passive: false });

    // Touch end
    content.addEventListener('touchend', function(e) {
        if (!isDragging || isVerticalScroll) {
            isDragging = false;
            return;
        }

        isDragging = false;
        content.style.transition = '';

        const deltaX = currentX - startX;
        const threshold = content.offsetWidth * 0.3; // 30% of width to trigger close

        if (deltaX > threshold) {
            // Close the menu
            if (window.VanillaMobileMenu) {
                window.VanillaMobileMenu.close();
            }
        } else {
            // Snap back to original position
            content.style.transform = 'translateX(0)';
            content.style.opacity = '1';
        }
    }, { passive: true });
}

// Initialize Collection Dropdown functionality
function initializeCollectionDropdown() {
    // Guest layout dropdown
    const guestDropdownButton = document.getElementById('mobile-collection-list-dropdown-button');
    const guestDropdownMenu = document.getElementById('mobile-collection-list-dropdown-menu');

    // App layout dropdown
    const appDropdownButton = document.getElementById('mobile-collection-list-dropdown-button-app');
    const appDropdownMenu = document.getElementById('mobile-collection-list-dropdown-menu-app');

    // Initialize dropdown for guest layout
    if (guestDropdownButton && guestDropdownMenu) {
        initDropdownBehavior(guestDropdownButton, guestDropdownMenu, 'guest');
    }

    // Initialize dropdown for app layout
    if (appDropdownButton && appDropdownMenu) {
        initDropdownBehavior(appDropdownButton, appDropdownMenu, 'app');
    }
}

function initDropdownBehavior(button, menu, layout) {
    let isOpen = false;

    function toggleDropdown() {
        isOpen = !isOpen;
        button.setAttribute('aria-expanded', isOpen);

        if (isOpen) {
            menu.classList.remove('hidden');
            loadUserCollections(layout);
        } else {
            menu.classList.add('hidden');
        }
    }

    button.addEventListener('click', toggleDropdown);

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!button.contains(e.target) && !menu.contains(e.target) && isOpen) {
            isOpen = false;
            button.setAttribute('aria-expanded', false);
            menu.classList.add('hidden');
        }
    });
}

async function loadUserCollections(layout) {
    const loadingEl = document.getElementById(`mobile-collection-list-loading${layout === 'app' ? '-app' : ''}`);
    const emptyEl = document.getElementById(`mobile-collection-list-empty${layout === 'app' ? '-app' : ''}`);
    const errorEl = document.getElementById(`mobile-collection-list-error${layout === 'app' ? '-app' : ''}`);
    const menuEl = document.getElementById(`mobile-collection-list-dropdown-menu${layout === 'app' ? '-app' : ''}`);

    // Show loading state
    loadingEl?.classList.remove('hidden');
    emptyEl?.classList.add('hidden');
    errorEl?.classList.add('hidden');

    try {
        // This would typically fetch from your collections API
        // For now, simulate the API call
        await new Promise(resolve => setTimeout(resolve, 1000));

        // Hide loading and show empty (you'd replace this with real data)
        loadingEl?.classList.add('hidden');
        emptyEl?.classList.remove('hidden');

    } catch (error) {
        console.error('Failed to load collections:', error);
        loadingEl?.classList.add('hidden');
        errorEl?.classList.remove('hidden');
    }
}

// Initialize Action Buttons functionality
function initializeActionButtons() {
    // Create EGI buttons
    const createEgiButtons = document.querySelectorAll('.js-create-egi-contextual-button');
    createEgiButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const authType = this.getAttribute('data-auth-type');
            handleCreateEgiAction(authType);
        });
    });

    // Create Collection buttons
    const createCollectionButtons = document.querySelectorAll('[data-action="open-create-collection-modal"]');
    createCollectionButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            handleCreateCollectionAction();
        });
    });
}

function handleCreateEgiAction(authType) {
    console.log('Create EGI clicked, auth type:', authType);

    if (authType === 'authenticated') {
        // User is logged in, proceed with EGI creation
        console.log('Opening EGI creation for authenticated user');

        // Chiudi il menu mobile prima di navigare
        if (window.VanillaMobileMenu) {
            window.VanillaMobileMenu.close();
        }

        // Triggera l'evento per aprire il modal/flow di creazione EGI
        // Se esiste una funzione globale per creare EGI, la chiama
        if (typeof window.openCreateEgiModal === 'function') {
            window.openCreateEgiModal();
        } else if (typeof window.createEgiFlow === 'function') {
            window.createEgiFlow();
        } else {
            // No fallback redirect - evita navigazione indesiderata
            console.log('No EGI creation function found - check your global functions');
        }
    } else {
        // User is guest, show login/register options
        console.log('Showing auth options for guest user');

        // Chiudi il menu mobile
        if (window.VanillaMobileMenu) {
            window.VanillaMobileMenu.close();
        }

        // Mostra modal di login o redirect
        if (typeof window.showLoginModal === 'function') {
            window.showLoginModal();
        } else {
            // No fallback redirect - evita navigazione indesiderata  
            console.log('No auth modal function found - check your global functions');
        }
    }
}

function handleCreateCollectionAction() {
    console.log('Create Collection clicked');

    // Chiudi il menu mobile
    if (window.VanillaMobileMenu) {
        window.VanillaMobileMenu.close();
    }

    // Triggera l'evento per aprire il modal di creazione collezione
    if (typeof window.openCreateCollectionModal === 'function') {
        window.openCreateCollectionModal();
    } else if (typeof window.createCollectionFlow === 'function') {
        window.createCollectionFlow();
    } else {
        // No fallback redirect - evita navigazione indesiderata
        console.log('No collection creation function found - check your global functions');
    }
}// Handle orientation change
window.addEventListener('orientationchange', function() {
    setTimeout(() => {
        if (window.VanillaMobileMenu && window.VanillaMobileMenu.isOpen()) {
            // Re-adjust for new orientation
            const content = document.querySelector('[data-mobile-content]');
            if (content) {
                content.style.transform = 'translateX(0)';
                content.style.opacity = '1';
            }
        }
    }, 100);
});

// CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInLeft {
        from {
            opacity: 0;
            transform: translateX(-20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .animate-slide-in-left {
        animation: slideInLeft 0.4s ease-out backwards;
    }

    /* Enhanced transitions for mobile content */
    [data-mobile-content] {
        transition: transform 0.3s ease-out, opacity 0.3s ease-out;
        background: white !important;
        color: black !important;
    }

    /* Ensure content is visible */
    .mobile-menu-container {
        background: white !important;
        color: black !important;
        z-index: 99999 !important;
    }

    /* Icon transitions */
    [data-mobile-menu-trigger] svg {
        transition: opacity 0.2s ease-in-out;
    }

    /* Touch feedback */
    [data-mobile-menu-trigger]:active {
        transform: scale(0.95);
    }

    /* Improve touch targets */
    .mobile-nav-item {
        min-height: 44px; /* iOS accessibility guideline */
    }

    /* Fix Create EGI button contrast */
    .js-create-egi-contextual-button {
        color: #374151 !important;
    }

    /* Smooth scrolling for mobile content */
    .mobile-menu-content {
        scroll-behavior: smooth;
        -webkit-overflow-scrolling: touch;
    }
`;
document.head.appendChild(style);
