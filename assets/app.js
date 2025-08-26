

// Stimulus bridge
import './bootstrap.js';

// Importation des styles principaux
import './styles/app.scss';

// Import Bootstrap JS avec Popper inclus (obligatoire pour dropdowns, modals, etc.)
import 'bootstrap/dist/js/bootstrap.bundle.min.js';

// Import du fichier like.js
import './like.js';

import './vague_boutons.js';

// (Optionnel) Import de CSS pur 
// import './styles/app.css';

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

// Activation des tooltips Bootstrap
document.addEventListener('DOMContentLoaded', () => {
  const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
  tooltipTriggerList.forEach(t => new bootstrap.Tooltip(t));
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




