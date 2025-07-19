
import confetti from 'canvas-confetti';

document.addEventListener('DOMContentLoaded', () => {
    const likeButtons = document.querySelectorAll('.like-button');

    likeButtons.forEach(button => {
        button.addEventListener('click', async (e) => {
            e.preventDefault();

            const url = button.dataset.url;
            button.disabled = true;

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) throw new Error('Erreur serveur');

                const data = await response.json();

                // Met à jour le HTML du bouton
                button.innerHTML = `
                    <i class="bi ${data.liked ? 'bi-heart-fill' : 'bi-heart'} me-1"></i>
                    <span class="like-count">${data.likeCount}</span>
                `;

                const icon = button.querySelector('i');

                if (data.liked === true) {
    button.classList.add('liked');
    icon.classList.add('text-danger');

    // Forcer le reflow pour garantir le déclenchement de l'animation
    void icon.offsetWidth;

    // Battement de cœur sur l’icône
    icon.classList.add('beating');
    setTimeout(() => {
    icon.classList.remove('beating');
    }, 500);


    confetti({
        particleCount: 80,
        spread: 70,
        origin: { y: 0.6 }
    });
    const merciMessage = button.closest('.position-relative').querySelector ('.merci-message');

if (merciMessage) {
    // Retire la classe d-none si présente
    merciMessage.classList.remove('d-none');

    // Puis applique l'animation
    merciMessage.classList.add('show-merci');

    setTimeout(() => {
        merciMessage.classList.remove('show-merci');
        merciMessage.classList.add('d-none'); // la remet au repos
    }, 1000);
}
                } else {
                    button.classList.remove('liked');
                    icon.classList.remove('text-danger');
                }

                // Animation de clic
                button.classList.add('animated');
                setTimeout(() => {
                    button.classList.remove('animated');
                    button.disabled = false;
                }, 400);

            } catch (err) {
                console.error('Erreur lors du like :', err);
                button.disabled = false;
            }
        });
    });
});











