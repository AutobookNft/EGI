/**
 * Collection Carousel JavaScript Helper
 * Provides additional functionality and debugging for the Livewire/Alpine carousel
 */

window.CollectionCarousel = {
    /**
     * Debug function to log carousel state
     */
    debug: function(component, message, data = {}) {
        if (window.location.hostname === 'localhost' || window.location.hostname.includes('dev')) {
            console.group(`🎠 Carousel Debug: ${message}`);
            console.log('Data:', data);
            console.log('Component State:', {
                activeSlide: component.activeSlide,
                totalSlides: component.totalSlides,
                itemsPerView: component.itemsPerView,
                maxSlide: component.maxSlide
            });
            console.groupEnd();
        }
    },

    /**
     * Get responsive breakpoints for carousel
     */
    getBreakpoints: function() {
        return {
            xl: 1200,  // 4 items
            lg: 900,   // 3 items
            md: 600,   // 2 items
            sm: 0      // 1 item
        };
    },

    /**
     * Calculate items per view based on screen width
     */
    calculateItemsPerView: function(width = window.innerWidth) {
        const breakpoints = this.getBreakpoints();

        if (width >= breakpoints.xl) return 4;
        if (width >= breakpoints.lg) return 3;
        if (width >= breakpoints.md) return 2;
        return 1;
    },

    /**
     * Initialize carousel with event listeners
     */
    init: function() {
        console.log('🎠 Collection Carousel JavaScript Helper loaded');

        // Add keyboard navigation
        document.addEventListener('keydown', (e) => {
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') {
                return; // Don't interfere with form inputs
            }

            const carousel = document.querySelector('[x-data*="activeSlide"]');
            if (!carousel) return;

            if (e.key === 'ArrowLeft') {
                e.preventDefault();
                const prevBtn = carousel.querySelector('[wire\\:click="prevSlide"]');
                if (prevBtn && prevBtn.style.display !== 'none') {
                    prevBtn.click();
                }
            } else if (e.key === 'ArrowRight') {
                e.preventDefault();
                const nextBtn = carousel.querySelector('[wire\\:click="nextSlide"]');
                if (nextBtn && nextBtn.style.display !== 'none') {
                    nextBtn.click();
                }
            }
        });

        // Add touch/swipe support for mobile
        this.addTouchSupport();
    },

    /**
     * Add touch/swipe support for mobile devices
     */
    addTouchSupport: function() {
        let startX = 0;
        let startY = 0;
        let isDragging = false;

        document.addEventListener('touchstart', (e) => {
            const carousel = e.target.closest('[x-data*="activeSlide"]');
            if (!carousel) return;

            startX = e.touches[0].clientX;
            startY = e.touches[0].clientY;
            isDragging = true;
        }, { passive: true });

        document.addEventListener('touchmove', (e) => {
            if (!isDragging) return;
            // Prevent default scrolling if we're in a horizontal swipe
            const diffX = Math.abs(e.touches[0].clientX - startX);
            const diffY = Math.abs(e.touches[0].clientY - startY);

            if (diffX > diffY && diffX > 10) {
                e.preventDefault();
            }
        }, { passive: false });

        document.addEventListener('touchend', (e) => {
            if (!isDragging) return;
            isDragging = false;

            const carousel = e.target.closest('[x-data*="activeSlide"]');
            if (!carousel) return;

            const endX = e.changedTouches[0].clientX;
            const endY = e.changedTouches[0].clientY;
            const diffX = startX - endX;
            const diffY = Math.abs(startY - endY);

            // Check if it's a horizontal swipe (not vertical scroll)
            if (Math.abs(diffX) > diffY && Math.abs(diffX) > 50) {
                if (diffX > 0) {
                    // Swipe left - next slide
                    const nextBtn = carousel.querySelector('[wire\\:click="nextSlide"]');
                    if (nextBtn && nextBtn.style.display !== 'none') {
                        nextBtn.click();
                    }
                } else {
                    // Swipe right - previous slide
                    const prevBtn = carousel.querySelector('[wire\\:click="prevSlide"]');
                    if (prevBtn && prevBtn.style.display !== 'none') {
                        prevBtn.click();
                    }
                }
            }
        }, { passive: true });
    }
};

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.CollectionCarousel.init();
    });
} else {
    window.CollectionCarousel.init();
}

// Re-initialize after Livewire updates
document.addEventListener('livewire:navigated', () => {
    window.CollectionCarousel.init();
});
