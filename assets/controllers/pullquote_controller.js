

import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
  static targets = [
    'text','source','position','theme','index',
    'preview','previewText','previewSource',
    'charcount','indexHelp'
  ];

  connect() {
    // textarea Contenu (pour compter les paragraphes)
    const sel = this.element.dataset.contentSelector;
    this.contentEl = sel ? document.querySelector(sel) : null;
    if (this.contentEl) {
      this.contentEl.addEventListener('input', () => this.updateIndexHelp());
    }
    this.update();           // initial
    this.updateIndexHelp();  // initial
  }

  update() {
    const t = (this.textTarget?.value || '').trim();
    const s = (this.sourceTarget?.value || '').trim();
    const pos = (this.positionTarget?.value || 'right');
    const theme = (this.themeTarget?.value || 'default');

    // compteur caractères
    if (this.hasCharcountTarget) {
      const len = t.length;
      this.charcountTarget.textContent = `${len}/1000`;
      this.charcountTarget.classList.toggle('text-danger', len > 1000);
    }

    if (!t) { this.previewTarget.hidden = true; return; }
    this.previewTarget.hidden = false;

    // classes
    this.previewTarget.className = 'pull-quote';
    this.previewTarget.classList.add(pos);
    if (theme !== 'default') this.previewTarget.classList.add(`pull-quote--${theme}`);

    // contenu
    this.previewTextTarget.textContent = t;
    this.previewSourceTarget.textContent = s;
    this.previewSourceTarget.style.display = s ? '' : 'none';
  }

  updateIndexHelp() {
    if (!this.hasIndexHelpTarget) return;

    const raw = (this.contentEl?.value || '').trim();
    let count = 0;

    if (raw.toLowerCase().includes('</p>')) {
      count = raw.split(/<\/p>/i).filter(Boolean).length;
    } else {
      // approx: 2 sauts de ligne = nouveau paragraphe
      count = raw.split(/\n{2,}/).map(s => s.trim()).filter(Boolean).length || 1;
    }

    this.indexHelpTarget.textContent = `≈ ${count} paragraphe(s) détecté(s) dans le contenu`;
    if (this.hasIndexTarget) {
      this.indexTarget.max = Math.max(1, count);
    }
    if (this.hasIndexTarget) {
      const max = Math.max(1, count);
      this.indexTarget.max = max;
      const cur = parseInt(this.indexTarget.value || '1', 10);
      if (cur > max) this.indexTarget.value = String(max);
    }
  }
}


