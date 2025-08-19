import Swiper from 'swiper/bundle';
import 'swiper/css/bundle';

// --- helper: scala le card per NON superare l'altezza del contenitore ---
function fitHeight(root){
  const H = root.clientHeight;                         // altezza disponibile (es. 70vh)
  const probe = root.querySelector('.swiper-slide > .egi-slide');
  if(!probe) return;
  // misura la card reale (senza scala)
  probe.style.transform = 'none';
  const cardH = probe.firstElementChild?.getBoundingClientRect().height || 0;
  const base = cardH ? Math.min(1, (H - 24) / cardH) : 1; // 24px margine aria
  root.style.setProperty('--egi-base-scale', String(base));
}

function init(root){
  // 1) scala per altezza disponibile
  fitHeight(root);
  // 2) init Swiper
  const sw = new Swiper(root, {
    effect: 'coverflow',
    centeredSlides: true,
    slidesPerView: 'auto',
    loop: true,
    speed: 600,
    grabCursor: true,

    // --- Coverflow "stile Colletto": effetto 3D marcato ---
    coverflowEffect: {
      rotate: 45,        // inclinazione più marcata per effetto 3D
      depth: 300,        // maggiore profondità Z per distacco visivo
      stretch: -60,      // sovrapposizione per effetto continuità
      modifier: 1.3,     // intensifica l'effetto complessivo
      slideShadows: true // ombre per realismo 3D
    },

    pagination: { el: root.querySelector('.egi-pagination'), clickable: true },
    navigation: { nextEl: root.querySelector('.egi-next'), prevEl: root.querySelector('.egi-prev') },
    keyboard: { enabled: true },
    autoplay: { delay: 4000, disableOnInteraction: true },

    // "peek" laterale responsive
    breakpoints: {
      0:    { slidesOffsetBefore: 16,  slidesOffsetAfter: 16  },
      640:  { slidesOffsetBefore: 32,  slidesOffsetAfter: 32  },
      1024: { slidesOffsetBefore: 64,  slidesOffsetAfter: 64  },
    },

    a11y: { enabled: true },
    on: {
      // ricalcola la scala se cambia il contenitore
      resize(){ fitHeight(root); },
    }
  });
}

function boot(){
  document.querySelectorAll('.egi-coverflow').forEach(el=>{
    if(el.dataset.swiperReady) return;
    el.dataset.swiperReady = '1';
    init(el);
  });
}

if(document.readyState === 'loading'){
  document.addEventListener('DOMContentLoaded', boot);
}else{ boot(); }

export {};
