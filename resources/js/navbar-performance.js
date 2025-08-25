/**
 * Navbar Performance Monitor - Tracks and optimizes navbar loading
 */

class NavbarPerformanceMonitor {
    constructor() {
        this.startTime = performance.now();
        this.navbarReady = false;
        this.metrics = {
            navbarFirstPaint: null,
            navbarFullyLoaded: null,
            criticalImagesLoaded: 0,
            totalCriticalImages: 0,
            networkSpeed: 'unknown'
        };

        this.init();
    }

    init() {
        this.detectNetworkSpeed();
        this.optimizeForDevice();
        this.trackNavbarLoading();
        this.addPerformanceHints();
        this.setupPerformanceObserver();
    }

    detectNetworkSpeed() {
        if ('connection' in navigator) {
            const connection = navigator.connection;
            this.metrics.networkSpeed = connection.effectiveType || 'unknown';

            // Optimize based on network speed
            if (connection.effectiveType === 'slow-2g' || connection.effectiveType === '2g') {
                this.enableDataSaverMode();
            }
        }
    }

    optimizeForDevice() {
        // Detect device capabilities
        const isLowEndDevice = this.isLowEndDevice();
        const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        if (isLowEndDevice || prefersReducedMotion) {
            document.body.classList.add('performance-mode');
            this.disableAnimations();
        }
    }

    isLowEndDevice() {
        // Simple heuristic for low-end devices
        return navigator.hardwareConcurrency <= 2 ||
               (navigator.deviceMemory && navigator.deviceMemory <= 2);
    }

    disableAnimations() {
        const style = document.createElement('style');
        style.textContent = `
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        `;
        document.head.appendChild(style);
    }

    enableDataSaverMode() {
        console.log('📱 Data saver mode enabled - optimizing for slow network');

        // Replace high-res images with lower quality versions
        const images = document.querySelectorAll('img[data-lazy]');
        images.forEach(img => {
            const originalSrc = img.dataset.lazy;
            if (originalSrc && originalSrc.includes('images/')) {
                // Add quality parameter for data saving
                img.dataset.lazy = originalSrc + '?q=60&w=400';
            }
        });

        // Increase lazy loading threshold
        window.egiLazyLoader?.updateThreshold('200px');
    }

    trackNavbarLoading() {
        // Monitor when navbar becomes visible
        const navbar = document.querySelector('header[role="banner"]');
        if (navbar) {
            // Mark when navbar is first painted
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting && !this.navbarReady) {
                        this.metrics.navbarFirstPaint = performance.now() - this.startTime;
                        this.navbarReady = true;

                        console.log(`🚀 Navbar first paint: ${Math.round(this.metrics.navbarFirstPaint)}ms`);

                        // Track when all critical navbar elements are loaded
                        this.trackCriticalElements();

                        observer.unobserve(entry.target);
                    }
                });
            });

            observer.observe(navbar);
        }
    }

    trackCriticalElements() {
        const criticalImages = document.querySelectorAll('.navbar-critical, header img[loading="eager"]');
        this.metrics.totalCriticalImages = criticalImages.length;

        criticalImages.forEach(img => {
            if (img.complete) {
                this.metrics.criticalImagesLoaded++;
            } else {
                img.addEventListener('load', () => {
                    this.metrics.criticalImagesLoaded++;
                    this.checkNavbarFullyLoaded();
                });

                img.addEventListener('error', () => {
                    console.warn('Failed to load critical navbar image:', img.src);
                    this.metrics.criticalImagesLoaded++;
                    this.checkNavbarFullyLoaded();
                });
            }
        });

        this.checkNavbarFullyLoaded();
    }

    checkNavbarFullyLoaded() {
        if (this.metrics.criticalImagesLoaded >= this.metrics.totalCriticalImages) {
            this.metrics.navbarFullyLoaded = performance.now() - this.startTime;

            console.log(`✅ Navbar fully loaded: ${Math.round(this.metrics.navbarFullyLoaded)}ms`);

            // Mark navbar as fully loaded
            const navbar = document.querySelector('header[role="banner"]');
            if (navbar) {
                navbar.classList.add('navbar-loaded');
                navbar.classList.remove('navbar-loading');
            }

            // Emit event for other components
            document.dispatchEvent(new CustomEvent('navbarFullyLoaded', {
                detail: { metrics: this.metrics }
            }));

            // Start loading non-critical content
            this.loadNonCriticalContent();
        }
    }

    loadNonCriticalContent() {
        // Delay loading of heavy components until navbar is ready
        requestIdleCallback(() => {
            // Enable carousel loading
            document.body.classList.add('navbar-ready');

            // Trigger loading of below-the-fold content
            const belowFoldElements = document.querySelectorAll('.below-the-fold');
            belowFoldElements.forEach(el => {
                el.classList.add('ready-to-load');
            });
        });
    }

    addPerformanceHints() {
        // Add performance hints to the document
        const hints = [
            { rel: 'preconnect', href: 'https://fonts.googleapis.com' },
            { rel: 'preconnect', href: 'https://fonts.gstatic.com' },
            { rel: 'dns-prefetch', href: '//images.florenceegi.com' }, // If you use a CDN
        ];

        hints.forEach(hint => {
            const link = document.createElement('link');
            Object.assign(link, hint);
            document.head.appendChild(link);
        });
    }

    setupPerformanceObserver() {
        // Monitor performance metrics
        if ('PerformanceObserver' in window) {
            // Track paint timing
            const paintObserver = new PerformanceObserver((list) => {
                list.getEntries().forEach((entry) => {
                    if (entry.name === 'first-contentful-paint') {
                        console.log(`🎨 First Contentful Paint: ${Math.round(entry.startTime)}ms`);
                    }
                    if (entry.name === 'largest-contentful-paint') {
                        console.log(`📏 Largest Contentful Paint: ${Math.round(entry.startTime)}ms`);
                    }
                });
            });

            paintObserver.observe({ entryTypes: ['paint', 'largest-contentful-paint'] });

            // Track layout shifts
            const clsObserver = new PerformanceObserver((list) => {
                let clsValue = 0;
                list.getEntries().forEach((entry) => {
                    if (!entry.hadRecentInput) {
                        clsValue += entry.value;
                    }
                });

                if (clsValue > 0) {
                    console.log(`📐 Cumulative Layout Shift: ${clsValue.toFixed(4)}`);
                }
            });

            clsObserver.observe({ entryTypes: ['layout-shift'] });
        }
    }

    // Public methods for external monitoring
    getMetrics() {
        return {
            ...this.metrics,
            currentTime: performance.now() - this.startTime,
            navbarReady: this.navbarReady
        };
    }

    logPerformanceReport() {
        const metrics = this.getMetrics();
        console.group('📊 Navbar Performance Report');
        console.log('First Paint:', Math.round(metrics.navbarFirstPaint || 0) + 'ms');
        console.log('Fully Loaded:', Math.round(metrics.navbarFullyLoaded || 0) + 'ms');
        console.log('Critical Images:', `${metrics.criticalImagesLoaded}/${metrics.totalCriticalImages}`);
        console.log('Network Speed:', metrics.networkSpeed);
        console.log('Current Time:', Math.round(metrics.currentTime) + 'ms');
        console.groupEnd();
    }
}

// Initialize navbar performance monitoring
let navbarPerfMonitor;

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        navbarPerfMonitor = new NavbarPerformanceMonitor();
    });
} else {
    navbarPerfMonitor = new NavbarPerformanceMonitor();
}

// Global access for debugging
window.navbarPerfMonitor = navbarPerfMonitor;

// Auto-report performance after 5 seconds
setTimeout(() => {
    if (navbarPerfMonitor) {
        navbarPerfMonitor.logPerformanceReport();
    }
}, 5000);

// Listen for page visibility changes to pause/resume monitoring
document.addEventListener('visibilitychange', () => {
    if (document.hidden) {
        console.log('📱 Page hidden - pausing performance monitoring');
    } else {
        console.log('📱 Page visible - resuming performance monitoring');
    }
});
