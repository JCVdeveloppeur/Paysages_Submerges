// Auto-resize des <textarea class="autogrow">

// assets/js/article_edit.js
const initAutogrow = () => {
  const areas = document.querySelectorAll('textarea.autogrow');

  const resize = (ta) => {
    ta.style.height = 'auto';
    const max = parseInt(getComputedStyle(ta).getPropertyValue('--autogrow-max')) || Math.round(window.innerHeight * 0.6); // 60vh par défaut
    const newH = Math.min(ta.scrollHeight, max);
    ta.style.height = newH + 'px';
    ta.style.overflowY = (ta.scrollHeight > max) ? 'auto' : 'hidden';
  };

  areas.forEach((ta) => {
    resize(ta);
    ta.addEventListener('input', () => resize(ta));
  });
};

document.addEventListener('DOMContentLoaded', initAutogrow);
document.addEventListener('turbo:load', initAutogrow);

