// resources/js/polyfills.js
if (!('IntersectionObserver' in window)) {
    document.write('<script src="https://polyfill.io/v3/polyfill.min.js?features=IntersectionObserver"><\/script>');
}
