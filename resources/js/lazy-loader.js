/**
 * Advanced Lazy Loading System for EGI Performance Optimization
 * Prioritizes navbar rendering and implements progressive loading
 */

class EGILazyLoader {
    constructor() {
        this.imageObserver = null;
        this.componentObserver = null;
        this.loadedImages = new Set();
        this.criticalImages = new Set();
        this.stats = {
            totalImages: 0,
            loadedImages: 0,
            criticalLoaded: 0
        };

        this.init();
    }

    init() {
        // Prioritize critical navbar elements
        this.loadCriticalElements();

        // Setup intersection observers
        this.setupImageObserver();
        this.setupComponentObserver();

        // Start progressive loading after navbar is ready
        this.startProgressiveLoading();

        // Monitor performance
        this.trackPerformance();
    }

    loadCriticalElements() {
        console.log('🚀 Loading critical navbar elements...');

        // Immediately load navbar images
        const navbarImages = document.querySelectorAll('header img, .navbar-logo img');
        navbarImages.forEach(img => this.loadImageImmediately(img));

        // Mark navbar as loaded
        const navbar = document.querySelector('header[role="banner"]');
        if (navbar) {
            navbar.classList.add('navbar-loaded');
            navbar.classList.remove('navbar-loading');
        }

        // Emit navbar ready event
        document.dispatchEvent(new CustomEvent('navbarReady', {
            detail: { timestamp: performance.now() }
        }));
    }

    setupImageObserver() {
        // Different thresholds for different image types
        const options = {
            root: null,
            rootMargin: '50px', // Start loading 50px before visible
            threshold: 0.1
        };

        this.imageObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    this.loadImage(entry.target);
                    this.imageObserver.unobserve(entry.target);
                }
            });
        }, options);

        // Observe all lazy images (except critical ones)
        this.observeImages();
    }

    setupComponentObserver() {
        // Observer for heavy components that can be loaded later
        const options = {
            root: null,
            rootMargin: '100px', // Load components 100px before visible
            threshold: 0.1
        };

        this.componentObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    this.loadComponent(entry.target);
                    this.componentObserver.unobserve(entry.target);
                }
            });
        }, options);

        // Observe heavy components
        this.observeComponents();
    }

    observeImages() {
        // Select images that should be lazy loaded
        const lazyImages = document.querySelectorAll('img[data-lazy], img[loading="lazy"]');

        lazyImages.forEach(img => {
            // Skip if already in critical set
            if (!this.criticalImages.has(img)) {
                this.imageObserver.observe(img);
                this.stats.totalImages++;
            }
        });

        // Also handle background images
        const lazyBackgrounds = document.querySelectorAll('[data-lazy-bg]');
        lazyBackgrounds.forEach(el => {
            this.imageObserver.observe(el);
        });
    }

    observeComponents() {
        // Heavy components that can be loaded progressively
        const heavyComponents = document.querySelectorAll(
            '.collection-carousel, .egi-carousel, .creator-carousel, ' +
            '.payment-stats, .collection-grid, [data-heavy-component]'
        );

        heavyComponents.forEach(component => {
            component.classList.add('component-loading');
            this.componentObserver.observe(component);
        });
    }

    loadImageImmediately(img) {
        if (this.loadedImages.has(img)) return;

        this.criticalImages.add(img);
        this.loadedImages.add(img);

        // If it has data-lazy, swap src
        const lazySrc = img.dataset.lazy || img.dataset.src;
        if (lazySrc) {
            img.src = lazySrc;
            img.removeAttribute('data-lazy');
        }

        // Set as loaded
        img.classList.add('loaded');
        this.stats.criticalLoaded++;

        console.log(`✅ Critical image loaded: ${img.src?.substring(0, 50)}...`);
    }

    loadImage(img) {
        if (this.loadedImages.has(img)) return;

        this.loadedImages.add(img);

        // Handle regular lazy images
        const lazySrc = img.dataset.lazy || img.dataset.src;
        if (lazySrc) {
            const tempImg = new Image();
            tempImg.onload = () => {
                img.src = lazySrc;
                img.classList.add('loaded');
                img.removeAttribute('data-lazy');
                this.stats.loadedImages++;
                this.updateProgress();
            };
            tempImg.onerror = () => {
                img.classList.add('error');
                console.warn('Failed to load image:', lazySrc);
            };
            tempImg.src = lazySrc;
        }

        // Handle background images
        if (img.dataset.lazyBg) {
            const bgUrl = img.dataset.lazyBg;
            const tempImg = new Image();
            tempImg.onload = () => {
                img.style.backgroundImage = `url(${bgUrl})`;
                img.classList.add('loaded');
                img.removeAttribute('data-lazy-bg');
            };
            tempImg.src = bgUrl;
        }
    }

    loadComponent(component) {
        component.classList.remove('component-loading');
        component.classList.add('component-loaded');

        // Trigger component-specific initialization
        const componentType = component.dataset.heavyComponent ||
                             component.className.split(' ').find(c => c.endsWith('-carousel'));

        if (componentType) {
            this.initializeComponent(component, componentType);
        }

        console.log(`🎯 Component loaded: ${componentType || component.tagName}`);
    }

    initializeComponent(component, type) {
        // Component-specific initialization
        switch (type) {
            case 'collection-carousel':
                this.initCollectionCarousel(component);
                break;
            case 'egi-carousel':
                this.initEgiCarousel(component);
                break;
            case 'creator-carousel':
                this.initCreatorCarousel(component);
                break;
            default:
                // Generic component init
                component.style.opacity = '1';
                break;
        }
    }

    initCollectionCarousel(component) {
        // Initialize collection carousel with smooth entrance
        component.style.opacity = '0';
        component.style.transform = 'translateY(20px)';

        requestAnimationFrame(() => {
            component.style.transition = 'all 0.5s ease-out';
            component.style.opacity = '1';
            component.style.transform = 'translateY(0)';
        });
    }

    initEgiCarousel(component) {
        // Initialize EGI carousel
        this.initCollectionCarousel(component); // Same animation for now
    }

    initCreatorCarousel(component) {
        // Initialize creator carousel
        this.initCollectionCarousel(component); // Same animation for now
    }

    startProgressiveLoading() {
        // Load images progressively based on priority
        setTimeout(() => {
            this.loadAboveFoldImages();
        }, 100);

        setTimeout(() => {
            this.loadNearViewportImages();
        }, 500);

        setTimeout(() => {
            this.loadRemainingImages();
        }, 2000);
    }

    loadAboveFoldImages() {
        // Load images in the immediate viewport
        const viewportHeight = window.innerHeight;
        const aboveFoldImages = Array.from(document.querySelectorAll('img[data-lazy]'))
            .filter(img => {
                const rect = img.getBoundingClientRect();
                return rect.top < viewportHeight;
            });

        aboveFoldImages.forEach(img => this.loadImage(img));
    }

    loadNearViewportImages() {
        // Load images near the viewport
        const viewportHeight = window.innerHeight;
        const nearImages = Array.from(document.querySelectorAll('img[data-lazy]'))
            .filter(img => {
                const rect = img.getBoundingClientRect();
                return rect.top < viewportHeight * 1.5;
            });

        nearImages.forEach(img => this.loadImage(img));
    }

    loadRemainingImages() {
        // Load all remaining images when system is idle
        if ('requestIdleCallback' in window) {
            requestIdleCallback(() => {
                const remainingImages = document.querySelectorAll('img[data-lazy]');
                remainingImages.forEach(img => this.loadImage(img));
            });
        } else {
            // Fallback for browsers without requestIdleCallback
            setTimeout(() => {
                const remainingImages = document.querySelectorAll('img[data-lazy]');
                remainingImages.forEach(img => this.loadImage(img));
            }, 3000);
        }
    }

    updateProgress() {
        const progress = this.stats.totalImages > 0
            ? (this.stats.loadedImages / this.stats.totalImages) * 100
            : 100;

        // Emit progress event
        document.dispatchEvent(new CustomEvent('lazyLoadProgress', {
            detail: {
                progress: Math.round(progress),
                stats: { ...this.stats }
            }
        }));

        // Log progress in console
        if (this.stats.loadedImages % 5 === 0 || progress === 100) {
            console.log(`📈 Lazy loading progress: ${Math.round(progress)}% (${this.stats.loadedImages}/${this.stats.totalImages})`);
        }
    }

    trackPerformance() {
        // Track Core Web Vitals and loading performance
        const observer = new PerformanceObserver((list) => {
            list.getEntries().forEach((entry) => {
                if (entry.entryType === 'navigation') {
                    console.log('🔍 Navigation timing:', {
                        domContentLoaded: entry.domContentLoadedEventEnd - entry.domContentLoadedEventStart,
                        loadComplete: entry.loadEventEnd - entry.loadEventStart,
                        firstPaint: entry.responseEnd - entry.requestStart
                    });
                }
            });
        });

        observer.observe({ entryTypes: ['navigation'] });

        // Track LCP (Largest Contentful Paint)
        new PerformanceObserver((list) => {
            list.getEntries().forEach((entry) => {
                console.log('🎯 LCP:', entry.startTime);
            });
        }).observe({ entryTypes: ['largest-contentful-paint'] });
    }

    // Public methods for manual control
    forceLoadAll() {
        const allLazyImages = document.querySelectorAll('img[data-lazy]');
        allLazyImages.forEach(img => this.loadImage(img));
    }

    preloadImage(url) {
        const link = document.createElement('link');
        link.rel = 'preload';
        link.as = 'image';
        link.href = url;
        document.head.appendChild(link);
    }

    getStats() {
        return { ...this.stats };
    }
}

// Initialize the lazy loader when DOM is ready
let egiLazyLoader;

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        egiLazyLoader = new EGILazyLoader();
    });
} else {
    egiLazyLoader = new EGILazyLoader();
}

// Export for global access
window.EGILazyLoader = EGILazyLoader;
window.egiLazyLoader = egiLazyLoader;

// Event listeners for debugging and monitoring
document.addEventListener('navbarReady', (e) => {
    console.log('🚀 Navbar ready in', Math.round(e.detail.timestamp), 'ms');
});

document.addEventListener('lazyLoadProgress', (e) => {
    // You can add progress bar or loading indicators here
    if (e.detail.progress === 100) {
        console.log('✅ All images loaded!');
        document.body.classList.add('images-loaded');
    }
});
