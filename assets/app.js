
import './styles/app.scss';
import 'bootstrap';
// import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');

document.addEventListener('DOMContentLoaded', function () {
    const navbarToggler = document.querySelector('.navbar-toggler');
    const navbarCollapse = document.querySelector('#navbarContent');
    const overlay = document.getElementById('navbar-overlay');

    navbarToggler.addEventListener('click', function () {
        overlay.classList.toggle('active');
    });

    // Si on clique sur l’overlay, on ferme le menu
    overlay.addEventListener('click', function () {
        overlay.classList.remove('active');
        const bsCollapse = bootstrap.Collapse.getInstance(navbarCollapse);
        if (bsCollapse) {
            bsCollapse.hide();
        }
    });

    // On ferme l’overlay si le menu se referme automatiquement
    navbarCollapse.addEventListener('hidden.bs.collapse', function () {
        overlay.classList.remove('active');
    });
});

document.addEventListener('DOMContentLoaded', function () {
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

