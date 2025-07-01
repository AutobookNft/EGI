// Like functionality migliorata
    document.querySelectorAll('.like-button').forEach(button => {
        button.addEventListener('click', async function() {
            const collectionId = this.dataset.collectionId;
            const likeUrl = this.dataset.likeUrl;
            const icon = this.querySelector('.icon-heart');
            const text = this.querySelector('.like-text');
            const countDisplay = this.querySelector('.like-count-display');

            // Visual feedback immediato
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = 'scale(1)';
            }, 150);

            try {
                const response = await fetch(likeUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    },
                });

                const data = await response.json();

                if (data.success) {
                    this.classList.toggle('is-liked', data.is_liked);

                    if (data.is_liked) {
                        icon.textContent = 'favorite';
                        text.textContent = 'Liked';
                        this.style.background = 'linear-gradient(135deg, #ec4899 0%, #be185d 100%)';
                    } else {
                        icon.textContent = 'favorite_border';
                        text.textContent = 'Like Collection';
                        this.style.background = 'linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%)';
                    }

                    countDisplay.textContent = `(${data.likes_count ?? 0})`;

                    // Celebrazione visiva
                    if (data.is_liked) {
                        icon.style.animation = 'heartBeat 0.6s ease-in-out';
                        setTimeout(() => {
                            icon.style.animation = '';
                        }, 600);
                    }
                }
            } catch (error) {
                console.error('Error toggling like:', error);
            }
        });
    });

    // View Toggle
    document.querySelectorAll('.view-toggle').forEach(button => {
        button.addEventListener('click', function() {
            const view = this.dataset.view;
            const container = document.getElementById('egis-container');

            // Update buttons
            document.querySelectorAll('.view-toggle').forEach(btn => {
                btn.classList.remove('active', 'bg-indigo-600', 'text-white');
                btn.classList.add('text-gray-400');
            });

            this.classList.add('active', 'bg-indigo-600', 'text-white');
            this.classList.remove('text-gray-400');

            // Update grid
            if (view === 'list') {
                container.className = 'space-y-4';
            } else {
                container.className = 'egi-grid';
            }
        });
    });

    // Parallax effect (performance-conscious)
    let ticking = false;

    function updateParallax() {
        const scrolled = window.pageYOffset;
        const parallax = document.querySelector('.parallax-banner');

        if (parallax) {
            const speed = scrolled * 0.5;
            parallax.style.transform = `translateY(${speed}px)`;
        }

        ticking = false;
    }

    function requestTick() {
        if (!ticking) {
            requestAnimationFrame(updateParallax);
            ticking = true;
        }
    }

    window.addEventListener('scroll', requestTick);

    // Copy to clipboard utility
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            // Show toast notification
            const toast = document.createElement('div');
            toast.className = 'fixed z-50 px-4 py-2 text-sm font-medium text-white transform -translate-x-1/2 bg-green-600 rounded-lg bottom-4 left-1/2';
            toast.textContent = 'Link copied to clipboard!';
            document.body.appendChild(toast);

            setTimeout(() => {
                toast.remove();
            }, 3000);
        });
    }

    // Enhanced scroll animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animationPlayState = 'running';
            }
        });
    }, observerOptions);

    // Observe all animated elements
    document.querySelectorAll('.egi-item, .stat-card').forEach(el => {
        observer.observe(el);
    });
