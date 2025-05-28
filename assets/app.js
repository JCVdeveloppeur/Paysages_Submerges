
// Importation des styles principaux (SCSS)
import './styles/app.scss';

// Import Bootstrap JS avec Popper inclus (obligatoire pour dropdowns, modals, etc.)
import 'bootstrap/dist/js/bootstrap.bundle.min.js';

// Import de ton fichier like.js (gestion des likes)
import './like.js';

// (Optionnel) Import de CSS pur si tu en as besoin
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


