/**
 * Vanilla Desktop Mega Menu - Pure JavaScript Implementation
 * Revolutionary desktop experience with smooth animations
 */

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    initializeDesktopMegaMenu();
    initializeDesktopCollectionsDropdowns();
    initializeDesktopActionButtons();
});

function initializeDesktopMegaMenu() {
    const dropdownContainers = document.querySelectorAll('[data-dropdown-container]');

    dropdownContainers.forEach(container => {
        const trigger = container.querySelector('[data-dropdown-trigger]');
        const content = container.querySelector('[data-dropdown-content]');

        if (!trigger || !content) return;

        let isOpen = false;
        let timeoutId = null;

        // Click to toggle
        trigger.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            if (isOpen) {
                closeDropdown(content);
            } else {
                // Close other dropdowns first
                closeAllDropdowns();
                openDropdown(content);
            }
            isOpen = !isOpen;
        });

        // Hover to open (after a small delay)
        trigger.addEventListener('mouseenter', function() {
            if (timeoutId) clearTimeout(timeoutId);

            timeoutId = setTimeout(() => {
                if (!isOpen) {
                    closeAllDropdowns();
                    openDropdown(content);
                    isOpen = true;
                }
            }, 200); // Small delay to prevent accidental opens
        });

        // Keep open when hovering content
        content.addEventListener('mouseenter', function() {
            if (timeoutId) clearTimeout(timeoutId);
        });

        // Close when leaving both trigger and content
        container.addEventListener('mouseleave', function() {
            if (timeoutId) clearTimeout(timeoutId);

            timeoutId = setTimeout(() => {
                if (isOpen) {
                    closeDropdown(content);
                    isOpen = false;
                }
            }, 300); // Delay to allow moving between trigger and content
        });

        // Store state in container for global access
        container._isOpen = () => isOpen;
        container._close = () => {
            closeDropdown(content);
            isOpen = false;
        };
    });

    // Close on outside click
    document.addEventListener('click', function(e) {
        const isClickInsideDropdown = e.target.closest('[data-dropdown-container]');
        if (!isClickInsideDropdown) {
            closeAllDropdowns();
        }
    });

    // Close on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeAllDropdowns();
        }
    });

    // Close on scroll (optional - prevents floating menus)
    window.addEventListener('scroll', function() {
        closeAllDropdowns();
    }, { passive: true });
}

function openDropdown(content) {
    // Ensure content is positioned correctly
    content.style.zIndex = '9999';

    // Show with animation
    content.classList.remove('opacity-0', 'invisible', 'scale-95');
    content.classList.add('opacity-100', 'visible', 'scale-100');

    // Add entrance animation for cards
    const cards = content.querySelectorAll('.mega-card');
    cards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.05}s`;
        card.classList.add('animate-card-entrance');
    });

    // Focus management for accessibility
    const firstFocusable = content.querySelector('a, button, [tabindex]:not([tabindex="-1"])');
    if (firstFocusable) {
        setTimeout(() => firstFocusable.focus(), 100);
    }
}

function closeDropdown(content) {
    // Hide with animation
    content.classList.remove('opacity-100', 'visible', 'scale-100');
    content.classList.add('opacity-0', 'invisible', 'scale-95');

    // Remove card animations
    const cards = content.querySelectorAll('.mega-card');
    cards.forEach(card => {
        card.style.animationDelay = '';
        card.classList.remove('animate-card-entrance');
    });
}

function closeAllDropdowns() {
    const containers = document.querySelectorAll('[data-dropdown-container]');
    containers.forEach(container => {
        if (container._close) {
            container._close();
        }
    });
}

// CSS Animation for card entrance
const style = document.createElement('style');
style.textContent = `
    @keyframes cardEntrance {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-card-entrance {
        animation: cardEntrance 0.3s ease-out backwards;
    }

    /* Enhanced dropdown transitions */
    [data-dropdown-content] {
        transition: opacity 0.2s ease-out, visibility 0.2s ease-out, transform 0.2s ease-out;
    }

    /* Mobile responsive behavior */
    @media (max-width: 640px) {
        [data-dropdown-content] {
            position: fixed !important;
            top: 60px !important;
            left: 10px !important;
            right: 10px !important;
            max-width: none !important;
            min-width: none !important;
        }

        .mega-menu-container {
            max-height: calc(100vh - 80px);
            overflow-y: auto;
        }
    }
`;
document.head.appendChild(style);

// Export for potential external use
window.VanillaDesktopMenu = {
    closeAll: closeAllDropdowns,
    init: initializeDesktopMegaMenu
};

// --- Extra behaviors aligned with mobile ---
function initializeDesktopCollectionsDropdowns() {
    // Guest layout
    const guestBtn = document.getElementById('desktop-collection-list-dropdown-button');
    const guestMenu = document.getElementById('desktop-collection-list-dropdown-menu');
    // App layout
    const appBtn = document.getElementById('desktop-collection-list-dropdown-button-app');
    const appMenu = document.getElementById('desktop-collection-list-dropdown-menu-app');

    if (guestBtn && guestMenu) setupDropdown(guestBtn, guestMenu, 'guest');
    if (appBtn && appMenu) setupDropdown(appBtn, appMenu, 'app');
}

function setupDropdown(button, menu, layout) {
    let isOpen = false;
    function toggle() {
        isOpen = !isOpen;
        button.setAttribute('aria-expanded', isOpen);
        menu.classList.toggle('hidden', !isOpen);
        if (isOpen) loadCollections(layout);
    }
    button.addEventListener('click', (e) => { e.preventDefault(); e.stopPropagation(); toggle(); });
    document.addEventListener('click', (e) => {
        if (!button.contains(e.target) && !menu.contains(e.target) && isOpen) {
            isOpen = false;
            menu.classList.add('hidden');
            button.setAttribute('aria-expanded', false);
        }
    });
}

async function loadCollections(layout) {
    const suffix = layout === 'app' ? '-app' : '';
    const loading = document.getElementById(`desktop-collection-list-loading${suffix}`);
    const empty = document.getElementById(`desktop-collection-list-empty${suffix}`);
    const error = document.getElementById(`desktop-collection-list-error${suffix}`);
    loading?.classList.remove('hidden');
    empty?.classList.add('hidden');
    error?.classList.add('hidden');
    try {
        await new Promise(r => setTimeout(r, 800));
        loading?.classList.add('hidden');
        empty?.classList.remove('hidden');
    } catch (e) {
        console.error('Failed to load collections (desktop):', e);
        loading?.classList.add('hidden');
        error?.classList.remove('hidden');
    }
}

function initializeDesktopActionButtons() {
    document.querySelectorAll('.js-create-egi-contextual-button').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault(); e.stopPropagation();
            const authType = btn.getAttribute('data-auth-type');
            handleCreateEgiActionDesktop(authType);
        });
    });
    document.querySelectorAll('[data-action="open-create-collection-modal"]').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault(); e.stopPropagation();
            handleCreateCollectionActionDesktop();
        });
    });
}

function handleCreateEgiActionDesktop(authType) {
    if (authType === 'authenticated') {
        if (typeof window.openCreateEgiModal === 'function') {
            window.openCreateEgiModal();
        } else if (typeof window.createEgiFlow === 'function') {
            window.createEgiFlow();
        } else {
            console.log('No EGI creation function found (desktop)');
        }
    } else {
        if (typeof window.showLoginModal === 'function') {
            window.showLoginModal();
        } else {
            console.log('No auth modal function found (desktop)');
        }
    }
}

function handleCreateCollectionActionDesktop() {
    if (typeof window.openCreateCollectionModal === 'function') {
        window.openCreateCollectionModal();
    } else if (typeof window.createCollectionFlow === 'function') {
        window.createCollectionFlow();
    } else {
        console.log('No collection creation function found (desktop)');
    }
}
