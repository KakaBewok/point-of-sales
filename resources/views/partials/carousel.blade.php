{{-- App Preview Carousel --}}
@php
$slides = [
    ['img' => '1.png', 'alt' => '', 'caption' => '', 'desc' => ''],
    ['img' => '2.png', 'alt' => '', 'caption' => '', 'desc' => ''],
    ['img' => '3.png', 'alt' => '', 'caption' => '', 'desc' => ''],
    ['img' => '4.png', 'alt' => '', 'caption' => '', 'desc' => ''],
    ['img' => '5.png', 'alt' => '', 'caption' => '', 'desc' => ''],
    ['img' => '6.png', 'alt' => '', 'caption' => '', 'desc' => ''],
];
@endphp

<section class="py-16 md:py-24 bg-gray-900 px-4 md:px-6 lg:px-8 overflow-hidden" id="preview">
    <div class="mx-auto max-w-6xl">
        <div class="text-center mb-12 md:mb-16">
            <div class="text-sm font-semibold text-blue-400 uppercase tracking-wider mb-3">Tampilan Aplikasi</div>
            <h2 class="text-2xl md:text-4xl font-extrabold tracking-tight text-white max-w-2xl mx-auto mb-4">Lihat Lebih Dekat Calapos</h2>
            <p class="text-base text-gray-400 max-w-xl mx-auto">Desain antarmuka yang bersih, modern, dan mudah digunakan untuk mengoptimalkan operasional bisnis Anda.</p>
        </div>

        {{-- Carousel --}}
        <div class="relative max-w-5xl mx-auto" id="carouselRoot">
            {{-- Track --}}
            <div class="overflow-hidden rounded-2xl">
                <div id="carouselTrack" class="flex transition-transform duration-500 ease-in-out">
                    @foreach($slides as $i => $slide)
                    <div class="carousel-slide w-full shrink-0 px-2 md:px-3" data-index="{{ $i }}">
                        <div class="bg-gray-800 rounded-2xl overflow-hidden shadow-2xl ring-1 ring-white/10 cursor-pointer group/card"
                             onclick="openModal('{{ asset('images/screenshots/' . $slide['img']) }}', '{{ $slide['caption'] }}')">
                            <div class="aspect-[16/9] relative overflow-hidden">
                                <img src="{{ asset('images/screenshots/' . $slide['img']) }}"
                                     alt="{{ $slide['alt'] }}"
                                     class="w-full h-full object-cover transition-transform duration-500 group-hover/card:scale-105"
                                     loading="lazy">
                                <div class="absolute inset-0 bg-gradient-to-t from-gray-900/60 via-transparent to-transparent opacity-0 group-hover/card:opacity-100 transition-opacity duration-300"></div>
                                <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover/card:opacity-100 transition-all duration-300">
                                    <div class="bg-white/90 backdrop-blur-sm text-gray-900 px-4 py-2 rounded-lg font-semibold text-sm shadow-lg flex items-center gap-2 transform translate-y-3 group-hover/card:translate-y-0 transition-transform duration-300">
                                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/></svg>
                                        Perbesar
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Arrow Left --}}
            <button id="carouselPrev"
                class="absolute left-3 md:-left-0 top-1/2 -translate-y-1/2 md:-translate-x-1/2 z-20 w-9 h-9 md:w-11 md:h-11 bg-white/80 backdrop-blur-sm text-gray-800 rounded-full shadow-sm hover:bg-white hover:shadow-lg hover:scale-110 focus:outline-none transition-all duration-200 disabled:opacity-30 disabled:cursor-not-allowed flex items-center justify-center"
                aria-label="Previous">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" class="w-4 h-4 md:w-5 md:h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            </button>

            {{-- Arrow Right --}}
            <button id="carouselNext"
                class="absolute right-3 md:-right-0 top-1/2 -translate-y-1/2 md:translate-x-1/2 z-20 w-9 h-9 md:w-11 md:h-11 bg-white/80 backdrop-blur-sm text-gray-800 rounded-full shadow-sm hover:bg-white hover:shadow-lg hover:scale-110 focus:outline-none transition-all duration-200 disabled:opacity-30 disabled:cursor-not-allowed flex items-center justify-center"
                aria-label="Next">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" class="w-4 h-4 md:w-5 md:h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </button>

            {{-- Dot Indicators --}}
            <div id="carouselDots" class="flex items-center justify-center gap-2 mt-8">
                @foreach($slides as $i => $slide)
                <button class="carousel-dot w-2.5 h-2.5 rounded-full transition-all duration-300 {{ $i === 0 ? 'bg-blue-500 w-8' : 'bg-gray-600 hover:bg-gray-500' }}"
                        data-index="{{ $i }}" aria-label="Go to slide {{ $i + 1 }}"></button>
                @endforeach
            </div>
        </div>

        <div class="mt-12 text-center">
            <a href="{{ route('register') }}" class="inline-flex items-center justify-center font-semibold rounded-xl transition shadow-[0_0_20px_rgba(37,99,235,0.4)] bg-blue-600 text-white hover:bg-blue-500 px-8 py-3.5 text-base hover:shadow-[0_0_25px_rgba(37,99,235,0.6)]">Coba Sekarang</a>
        </div>
    </div>
</section>

{{-- Lightbox Modal --}}
<div id="imageModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-gray-900/95 opacity-0 transition-opacity duration-300" onclick="closeModal(event)">
    <div class="absolute top-5 right-5 z-[110]">
        <button type="button" class="text-white/70 hover:text-white p-2 rounded-full hover:bg-white/10 transition" onclick="closeModal(event, true)">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="w-8 h-8"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
    <div class="relative w-full max-w-6xl mx-auto px-4 flex flex-col items-center" onclick="event.stopPropagation()">
        <img id="modalImage" src="" alt="Screenshot" class="max-w-full max-h-[85vh] object-contain rounded-2xl shadow-2xl ring-1 ring-white/10">
        <div id="modalCaption" class="text-white text-lg font-bold mt-4 text-center"></div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const track = document.getElementById('carouselTrack');
    const prevBtn = document.getElementById('carouselPrev');
    const nextBtn = document.getElementById('carouselNext');
    const dots = document.querySelectorAll('.carousel-dot');
    const slides = document.querySelectorAll('.carousel-slide');
    const total = slides.length;
    let current = 0;
    let autoplayTimer;

    function goTo(index) {
        if (index < 0) index = total - 1;
        if (index >= total) index = 0;
        current = index;
        track.style.transform = `translateX(-${current * 100}%)`;
        dots.forEach((d, i) => {
            d.classList.toggle('bg-blue-500', i === current);
            d.classList.toggle('w-8', i === current);
            d.classList.toggle('bg-gray-600', i !== current);
            d.classList.toggle('w-2.5', i !== current);
        });
    }

    prevBtn.addEventListener('click', () => { goTo(current - 1); resetAutoplay(); });
    nextBtn.addEventListener('click', () => { goTo(current + 1); resetAutoplay(); });
    dots.forEach(dot => {
        dot.addEventListener('click', () => { goTo(parseInt(dot.dataset.index)); resetAutoplay(); });
    });

    // Touch/swipe support
    let startX = 0, isDragging = false;
    track.addEventListener('touchstart', e => { startX = e.touches[0].clientX; isDragging = true; }, { passive: true });
    track.addEventListener('touchend', e => {
        if (!isDragging) return;
        const diff = startX - e.changedTouches[0].clientX;
        if (Math.abs(diff) > 50) { diff > 0 ? goTo(current + 1) : goTo(current - 1); }
        isDragging = false;
        resetAutoplay();
    }, { passive: true });

    // Keyboard
    document.addEventListener('keydown', e => {
        if (e.key === 'ArrowLeft') { goTo(current - 1); resetAutoplay(); }
        if (e.key === 'ArrowRight') { goTo(current + 1); resetAutoplay(); }
    });

    // Autoplay
    function startAutoplay() { autoplayTimer = setInterval(() => goTo(current + 1), 5000); }
    function resetAutoplay() { clearInterval(autoplayTimer); startAutoplay(); }
    startAutoplay();

    // Pause on hover
    const root = document.getElementById('carouselRoot');
    root.addEventListener('mouseenter', () => clearInterval(autoplayTimer));
    root.addEventListener('mouseleave', () => startAutoplay());
});

// Modal
const modal = document.getElementById('imageModal');
const modalImg = document.getElementById('modalImage');
const modalCap = document.getElementById('modalCaption');

function openModal(src, caption) {
    modalImg.src = src;
    modalCap.textContent = caption;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    void modal.offsetWidth;
    modal.classList.remove('opacity-0');
    modal.classList.add('opacity-100');
    document.body.style.overflow = 'hidden';
}

function closeModal(e, force = false) {
    if (force || e.target === modal) {
        modal.classList.remove('opacity-100');
        modal.classList.add('opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
        }, 300);
    }
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeModal(e, true);
});
</script>
