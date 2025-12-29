

// Stimulus bridge
import './bootstrap.js';

// Importation des styles principaux
import './styles/app.scss';

// Import Bootstrap JS avec Popper inclus (obligatoire pour dropdowns, modals, etc.)
import * as bootstrap from 'bootstrap';

// Import du fichier like.js
import './like.js';

import './vague_boutons.js';

// (Optionnel) Import de CSS pur 
// import './styles/app.css';

document.documentElement.classList.add("js-enabled");

// Console de test
console.log('🚀 JS chargé avec succès depuis app.js');

// Gestion de la navbar burger et overlay
document.addEventListener('DOMContentLoaded', () => {
    const navbarToggler = document.querySelector('.navbar-toggler');
    const navbarCollapse = document.querySelector('#navbarContent');
    const overlay = document.getElementById('navbar-overlay');

    if (navbarToggler && navbarCollapse && overlay) {
        navbarToggler.addEventListener('click', () => {
            overlay.classList.toggle('active');
        });

        overlay.addEventListener('click', () => {
            overlay.classList.remove('active');
            const bsCollapse = bootstrap.Collapse.getInstance(navbarCollapse);
            if (bsCollapse) {
                bsCollapse.hide();
            }
        });

        navbarCollapse.addEventListener('hidden.bs.collapse', () => {
            overlay.classList.remove('active');
        });
    }
});

// Gestion de l’effet "scroll" sur la navbar
document.addEventListener('DOMContentLoaded', () => {
    const navbar = document.querySelector('.navbar');

    if (!navbar) return;

    window.addEventListener('scroll', () => {
        if (window.scrollY > 20) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });
});



// Zoom image (modal)
document.addEventListener('click', (e) => {
  const btn = e.target.closest('.js-img-zoom');
  if (!btn) return;

  const full = btn.getAttribute('data-full');
  const alt = btn.querySelector('img')?.getAttribute('alt') || 'Aperçu';

  const modalImg   = document.getElementById('imagePreviewTag');
  const modalTitle = document.getElementById('imagePreviewTitle');
  const modalOpen  = document.getElementById('imagePreviewOpen');

  if (modalImg) { modalImg.src = full; modalImg.alt = alt; }
  if (modalTitle) modalTitle.textContent = alt;
  if (modalOpen) modalOpen.href = full;
});

// Optionnel : vider l’image quand on ferme la modale
document.getElementById('imagePreviewModal')?.addEventListener('hidden.bs.modal', () => {
  const modalImg = document.getElementById('imagePreviewTag');
  if (modalImg) modalImg.src = '';
});

// Zoom image fiche Espèces & Maladie (scroll + hover via variables CSS)
function updateZoomOnScroll() {
  document
    .querySelectorAll('.fiche-espece__image, .maladie-carnet__image')
    .forEach(img => {
      const rect = img.getBoundingClientRect();
      const ratio =
        1 -
        Math.abs(rect.top + rect.height / 2 - window.innerHeight / 2) /
          (window.innerHeight / 2);
      const zoom = Math.max(0, ratio * 0.04); // max +0.04 comme avant
      img.style.setProperty('--zoom-scroll', zoom);
    });
}

document.addEventListener('DOMContentLoaded', () => {
  // premier calcul au chargement
  updateZoomOnScroll();
  // mise à jour au scroll
  window.addEventListener('scroll', updateZoomOnScroll);
});
document.addEventListener("DOMContentLoaded", () => {
  // Bootstrap 5 requis
  const isMobileLike = () =>
    window.matchMedia("(pointer: coarse)").matches || window.innerWidth < 992;

  const els = document.querySelectorAll(".risque-info[data-bs-toggle='tooltip']");
  const instances = [];

  const initTooltips = () => {
    // destroy
    instances.forEach(i => i?.dispose?.());
    instances.length = 0;

    const trigger = isMobileLike() ? "click" : "hover focus";

    els.forEach(el => {
      const t = new bootstrap.Tooltip(el, {
        trigger,
        html: false,
        sanitize: true,
        // pour retours à la ligne dans data-bs-title
        customClass: "risque-tooltip"
      });
      instances.push(t);

      // Mobile: tap dehors => ferme
      if (isMobileLike()) {
        el.addEventListener("click", (e) => e.stopPropagation());
      }
    });

    if (isMobileLike()) {
      document.addEventListener("click", () => {
        instances.forEach(i => i.hide());
      });
    }
  };

  initTooltips();
  window.addEventListener("resize", () => initTooltips());
});
document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll(".risque-info[data-bs-toggle='tooltip']").forEach(el => {
    el.addEventListener("shown.bs.tooltip", () => el.setAttribute("aria-expanded", "true"));
    el.addEventListener("hidden.bs.tooltip", () => el.setAttribute("aria-expanded", "false"));
  });
});

// UI listes (maladies + espèces) : croix + animation résultats
document.addEventListener("DOMContentLoaded", () => {

  // Croix "effacer" (toutes les pages qui ont une searchbar)

  const initClear = (formSelector) => {
    const form = document.querySelector(formSelector);
    if (!form) return;

    // on prend le champ visible, pas le hidden "limit"
    const input =
      form.querySelector('input[type="search"]') ||
      form.querySelector('input[type="text"]') ||
      form.querySelector('input:not([type="hidden"])');

    const wrapper = form.closest(".bloc-recherche-espece");
    const clearBtn = form.querySelector(".btn-clear-search");

    if (!input || !wrapper || !clearBtn) return;

    const updateState = () => {
      wrapper.classList.toggle("is-filled", input.value.trim().length > 0);
    };

    updateState();

    input.addEventListener("input", () => {
      updateState();
      if (input.value.trim() === "") form.submit();
    });

    clearBtn.addEventListener("click", (e) => {
      e.preventDefault();
      input.value = "";
      updateState();
      form.submit();
    });
  };

  document.querySelectorAll("form .btn-clear-search").forEach(btn => {
  const form = btn.closest("form");
  if (form?.id && !form.dataset.clearInit) {
    form.dataset.clearInit = "1";
    initClear(`#${form.id}`);
  }

});

  // Animation arrivée résultats (micro délai, robuste)
  const animateResults = () => {
    document.querySelectorAll(".results-animate").forEach(el => {
  setTimeout(() => el.classList.add("is-ready"), 80);
    });
  };

  animateResults();

  //  Si l’utilisateur revient en arrière (bfcache), on relance
  window.addEventListener("pageshow", animateResults);
});

// retour en haut articles
  document.addEventListener("DOMContentLoaded", () => {
  const backToTop = document.getElementById("backToTop");
  if (!backToTop) return;

  const toggle = () => {
    backToTop.classList.toggle("is-visible", window.scrollY > 300);
  };

  toggle();
  window.addEventListener("scroll", toggle, { passive: true });

  backToTop.addEventListener("click", () => {
    window.scrollTo({ top: 0, behavior: "smooth" });
  });
});















