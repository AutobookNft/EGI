<section class="py-16 nft-stats-section bg-gray-900/50 backdrop-blur-sm">
    <div class="container px-4 mx-auto">
        <div class="grid grid-cols-2 gap-6 md:grid-cols-4">
            <x-egi-stat-card type="egi_created" :animate="$animate" />
            <x-egi-stat-card type="active_collectors" :animate="$animate" />
            <x-egi-stat-card type="environmental_impact" suffix="€" :animate="$animate" />
            <x-egi-stat-card type="supported_projects" :animate="$animate" />
        </div>
    </div>
</section>

@if ($animate)
<script>
    function animateCounters() {
        const counters = document.querySelectorAll('[data-counter]');

        counters.forEach(counter => {
            const target = parseFloat(counter.dataset.counter);
            const duration = 2000;
            const increment = target / (duration / 16);
            let current = 0;

            const updateCounter = () => {
                current += increment;
                if (current < target) {
                    counter.textContent = Math.floor(current).toLocaleString();
                    requestAnimationFrame(updateCounter);
                } else {
                    counter.textContent = Number.isInteger(target)
                        ? target.toLocaleString()
                        : target.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
                }
            };

            updateCounter();
        });
    }

    // Trigger on scroll
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCounters();
                observer.disconnect();
            }
        });
    });

    const statsSection = document.querySelector('.nft-stats-section');
    if (statsSection) observer.observe(statsSection);
</script>
@endif
