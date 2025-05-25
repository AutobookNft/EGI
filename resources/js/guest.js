// resources/js/guest.js
function setDocHeight() {
    document.documentElement.style.setProperty('--doc-height', `${window.innerHeight}px`);
    document.documentElement.style.setProperty('--app-height', `${window.innerHeight}px`);
}

// function initMobileMenu() {
//     const mobileMenuButton = document.getElementById('mobile-menu-button');
//     const mobileMenu = document.getElementById('mobile-menu');
//     const hamburgerIcon = document.getElementById('hamburger-icon');
//     const closeIcon = document.getElementById('close-icon');

//     if (mobileMenuButton && mobileMenu) {
//         mobileMenuButton.addEventListener('click', () => {
//             const expanded = mobileMenuButton.getAttribute('aria-expanded') === 'true';
//             mobileMenuButton.setAttribute('aria-expanded', !expanded);
//             mobileMenu.classList.toggle('hidden');
//             hamburgerIcon.classList.toggle('hidden');
//             closeIcon.classList.toggle('hidden');
//             document.body.classList.toggle('overflow-hidden', !expanded);
//         });
//     }
// }

// function adjustModalForMobile() {
//     const uploadModal = document.getElementById('upload-modal');
//     if (uploadModal && window.innerWidth < 768) {
//         const modalContent = uploadModal.querySelector('div[role="document"]');
//         if (modalContent) {
//             modalContent.style.maxHeight = '85vh';
//         }
//     }
// }

// document.addEventListener('DOMContentLoaded', () => {
//     setDocHeight();
//     initMobileMenu();
//     adjustModalForMobile();

//     window.addEventListener('resize', () => {
//         setDocHeight();
//         adjustModalForMobile();
//     });
//     window.addEventListener('orientationchange', setDocHeight);

//     // Lazy loading fallback
//     if (!('loading' in HTMLImageElement.prototype)) {
//         console.log('Native lazy loading not supported, consider adding a polyfill');
//     }

//     // Animazione elementi al viewport
//     if ('IntersectionObserver' in window) {
//         const animateOnScrollElements = document.querySelectorAll('.animate-on-scroll');
//         const observer = new IntersectionObserver((entries) => {
//             entries.forEach((entry) => {
//                 if (entry.isIntersecting) {
//                     entry.target.classList.add('animated');
//                     observer.unobserve(entry.target);
//                 }
//             });
//         }, { threshold: 0.1 });

//         animateOnScrollElements.forEach((el) => observer.observe(el));
//     }
// });
