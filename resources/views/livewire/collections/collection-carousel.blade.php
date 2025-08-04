<script>
    console.log('🎠 Collection Carousel - JavaScript Version');

    class CollectionCarousel {
        constructor(containerId) {
            this.container = document.getElementById(containerId);
            this.totalSlides = {{ count($collections) }};
            this.activeSlide = 0;
            this.itemsPerView = 1;
            this.maxSlide = 0;

            this.init();
        }

        init() {
            this.calculateItemsPerView();
            this.calculateMaxSlide();
            this.updateTransform();
            this.updateButtons();
            this.updateIndicators();

            // Event listeners
            window.addEventListener('resize', () => {
                this.calculateItemsPerView();
                this.calculateMaxSlide();
                this.updateTransform();
                this.updateButtons();
            });

            console.log('🎠 Carousel initialized:', {
                totalSlides: this.totalSlides,
                itemsPerView: this.itemsPerView,
                maxSlide: this.maxSlide
            });
        }

        calculateItemsPerView() {
            const width = window.innerWidth;

            if (width >= 1200) this.itemsPerView = 4;
            else if (width >= 900) this.itemsPerView = 3;
            else if (width >= 600) this.itemsPerView = 2;
            else this.itemsPerView = 1;
        }

        calculateMaxSlide() {
            this.maxSlide = Math.max(0, this.totalSlides - this.itemsPerView);

            // Ensure activeSlide doesn't exceed maxSlide
            if (this.activeSlide > this.maxSlide) {
                this.activeSlide = this.maxSlide;
            }
        }

        updateTransform() {
            const slider = this.container.querySelector('.carousel-slider');
            if (!slider) return;

            const slideWidth = 100 / this.itemsPerView;
            const translateX = -this.activeSlide * slideWidth;

            slider.style.transform = `translateX(${translateX}%)`;

            console.log('🎠 Transform updated:', {
                activeSlide: this.activeSlide,
                translateX: `${translateX}%`,
                itemsPerView: this.itemsPerView
            });
        }

        updateButtons() {
            const prevBtn = this.container.querySelector('.prev-btn');
            const nextBtn = this.container.querySelector('.next-btn');

            if (prevBtn) {
                prevBtn.style.display = this.activeSlide > 0 ? 'block' : 'none';
            }

            if (nextBtn) {
                nextBtn.style.display = this.activeSlide < this.maxSlide ? 'block' : 'none';
            }
        }

        updateIndicators() {
            const indicators = this.container.querySelectorAll('.carousel-indicator');
            indicators.forEach((indicator, index) => {
                if (index === this.activeSlide) {
                    indicator.classList.add('bg-blue-500');
                    indicator.classList.remove('bg-gray-300');
                } else {
                    indicator.classList.add('bg-gray-300');
                    indicator.classList.remove('bg-blue-500');
                }
            });
        }

        prevSlide() {
            if (this.activeSlide > 0) {
                this.activeSlide--;
                this.updateTransform();
                this.updateButtons();
                this.updateIndicators();
                console.log('🎠 Previous slide:', this.activeSlide);
            }
        }

        nextSlide() {
            if (this.activeSlide < this.maxSlide) {
                this.activeSlide++;
                this.updateTransform();
                this.updateButtons();
                this.updateIndicators();
                console.log('🎠 Next slide:', this.activeSlide);
            }
        }

        goToSlide(slideIndex) {
            if (slideIndex >= 0 && slideIndex <= this.maxSlide) {
                this.activeSlide = slideIndex;
                this.updateTransform();
                this.updateButtons();
                this.updateIndicators();
                console.log('🎠 Go to slide:', this.activeSlide);
            }
        }
    }
</script>

<div id="collection-carousel" class="relative w-full overflow-hidden">

    <h3 class="mt-6 text-xl">{{ __('collection.collections') }}</h3>

    @if ($collections && count($collections) > 0)
        <!-- Contenitore del carousel -->
        <div class="relative">
            <div class="overflow-hidden">
                <div class="carousel-slider flex transition-transform duration-500 ease-in-out">

                    @foreach ($collections as $index => $collection)
                        <div class="w-full flex-shrink-0 px-2 sm:w-1/2 lg:w-1/3 xl:w-1/4">
                            <x-collection-card :id="$collection->id" :editable="false" imageType="card" />
                        </div>
                    @endforeach

                </div>
            </div>

            <!-- Controlli Carousel -->
            @if (count($collections) > 1)
                <!-- Pulsante Precedente -->
                <button
                    class="prev-btn btn btn-circle absolute left-2 top-1/2 z-50 -translate-y-1/2 transform bg-white/80 shadow-lg hover:bg-white"
                    onclick="window.carouselInstance.prevSlide()">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                        </path>
                    </svg>
                </button>

                <!-- Pulsante Successivo -->
                <button
                    class="next-btn btn btn-circle absolute right-2 top-1/2 z-50 -translate-y-1/2 transform bg-white/80 shadow-lg hover:bg-white"
                    onclick="window.carouselInstance.nextSlide()">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            @endif
        </div>

        <!-- Indicatori del Carousel -->
        @if (count($collections) > 1)
            <div class="mt-4 flex justify-center space-x-2">
                @for ($i = 0; $i < min(count($collections), 5); $i++)
                    <button
                        class="carousel-indicator h-3 w-3 rounded-full bg-gray-300 transition-all duration-300 hover:bg-gray-400"
                        onclick="window.carouselInstance.goToSlide({{ $i }})">
                    </button>
                @endfor

                @if (count($collections) > 5)
                    <span class="text-sm text-gray-500">+{{ count($collections) - 5 }}</span>
                @endif
            </div>
        @endif
    @else
        <!-- Stato vuoto -->
        <div class="py-8 text-center">
            <div class="mb-4 text-gray-500">
                <svg class="mx-auto mb-4 h-16 w-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012 2v2M7 7h10">
                    </path>
                </svg>
            </div>
            <p class="text-lg font-medium">{{ __('collection.no_collections') }}</p>
            <p class="text-gray-600">{{ __('collection.create_first_collection') }}</p>
        </div>
    @endif
</div>

<script>
    // Initialize carousel when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        if ({{ count($collections) }} > 0) {
            window.carouselInstance = new CollectionCarousel('collection-carousel');
        }
    });
</script>
