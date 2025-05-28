

import confetti from 'canvas-confetti';

document.addEventListener('DOMContentLoaded', () => {
    const likeButtons = document.querySelectorAll('.like-button');

    likeButtons.forEach(button => {
        button.addEventListener('click', async (e) => {
            e.preventDefault(); // ⛔ empêche tout rechargement

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

                // ✅ Met à jour le compteur
                button.querySelector('.like-count').textContent = data.likeCount;

                // 🎉 Confettis si un like a été ajouté
                if (data.liked === true) {
                    confetti({
                        particleCount: 80,
                        spread: 70,
                        origin: { y: 0.6 }
                    });
                }

                // 💥 Animation visuelle
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







