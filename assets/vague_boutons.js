
  document.addEventListener('pointermove', (e) => {
    document.querySelectorAll('.btn-aqua').forEach(btn => {
      const r = btn.getBoundingClientRect();
      if (e.clientX >= r.left && e.clientX <= r.right && e.clientY >= r.top && e.clientY <= r.bottom) {
        const x = e.clientX - r.left;
        const y = e.clientY - r.top;
        btn.style.setProperty('--rx', x + 'px');
        btn.style.setProperty('--ry', y + 'px');
      }
    });
  });


