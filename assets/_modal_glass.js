
(() => {
  const modal = document.querySelector("#psDeleteModal");
  if (!modal) return;

  const titleEl = modal.querySelector("#psDeleteTitle");
  const subtitleEl = modal.querySelector("#psDeleteSubtitle");
  const bodyEl = modal.querySelector("#psDeleteBody");
  const formEl = modal.querySelector("#psDeleteForm");
  const tokenEl = modal.querySelector("#psDeleteToken");
  const kickerEl = modal.querySelector("#psDeleteKicker");
  const confirmBtn = modal.querySelector("#psDeleteConfirm");
  const cancelBtn = modal.querySelector("#psDeleteCancel");
  const dialogEl = modal.querySelector(".ps-modal__dialog");

  let lastTrigger = null;

  const FOCUSABLE =
    'a[href], area[href], button:not([disabled]), input:not([disabled]):not([type="hidden"]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])';

  const getFocusable = () => {
    const root = dialogEl || modal;
    return Array.from(root.querySelectorAll(FOCUSABLE)).filter((el) => {
      return !!(el.offsetWidth || el.offsetHeight || el.getClientRects().length);
    });
  };

  const setConfirmDefault = () => {
    if (!confirmBtn) return;
    confirmBtn.textContent = "Oui, supprimer";
    confirmBtn.classList.remove("ps-btn--danger");
    confirmBtn.classList.add("ps-btn--primary");
  };

  const open = () => {
    modal.classList.add("is-open");
    modal.setAttribute("aria-hidden", "false");
    document.documentElement.classList.add("ps-modal-lock");
    document.body.classList.add("ps-modal-lock");

    const focusables = getFocusable();
    (cancelBtn || focusables[0] || dialogEl || modal).focus?.();
  };

  const close = () => {
    modal.classList.remove("is-open");
    modal.setAttribute("aria-hidden", "true");
    document.documentElement.classList.remove("ps-modal-lock");
    document.body.classList.remove("ps-modal-lock");

    setConfirmDefault();

    lastTrigger?.focus?.();
    lastTrigger = null;
  };

  document.addEventListener("click", (e) => {
    const openBtn = e.target.closest('[data-ps-open="#psDeleteModal"]');
    if (openBtn) {
      lastTrigger = openBtn;

      modal.dataset.kind = openBtn.dataset.deleteKind || "article";

      if (kickerEl) kickerEl.textContent = openBtn.dataset.deleteKicker || "Suppression";

      if (titleEl) titleEl.textContent = openBtn.dataset.deleteTitle || "Supprimer ?";
      if (subtitleEl)
        subtitleEl.textContent = openBtn.dataset.deleteSubtitle || "Cette action est irréversible.";
      if (bodyEl) bodyEl.innerHTML = openBtn.dataset.deleteBody || "Confirme la suppression.";

      if (confirmBtn) {
        confirmBtn.textContent = openBtn.dataset.confirmLabel || "Oui, supprimer";
        const variant = openBtn.dataset.confirmVariant || "primary";
        confirmBtn.classList.remove("ps-btn--primary", "ps-btn--danger");
        confirmBtn.classList.add(`ps-btn--${variant}`);
      }

      if (formEl) formEl.action = openBtn.dataset.deleteAction || "#";
      if (tokenEl) tokenEl.value = openBtn.dataset.deleteToken || "";

      open();
      return;
    }

    const closeBtn = e.target.closest("[data-ps-close]");
    if (closeBtn && closeBtn.closest("#psDeleteModal")) close();
  });

  document.addEventListener("keydown", (e) => {
  if (!modal.classList.contains("is-open")) return;

  // ESC
  if (e.key === "Escape") {
    e.preventDefault();
    close();
    return;
  }

  // Focus trap
  if (e.key === "Tab") {
    const focusables = getFocusable();
    if (focusables.length === 0) return;

    const first = focusables[0];
    const last = focusables[focusables.length - 1];
    const active = document.activeElement;

    if (e.shiftKey && active === first) {
      e.preventDefault();
      last.focus();
      return;
    }

    if (!e.shiftKey && active === last) {
      e.preventDefault();
      first.focus();
      return;
    }

    return; // important : on sort ici
  }

  // Navigation gauche / droite entre Annuler et Confirmer
  if (e.key === "ArrowLeft" || e.key === "ArrowRight") {
    const active = document.activeElement;

    const isTypingField =
      active &&
      (active.tagName === "INPUT" ||
        active.tagName === "TEXTAREA" ||
        active.tagName === "SELECT");

    if (isTypingField) return;
    if (!cancelBtn || !confirmBtn) return;

    e.preventDefault();
    (e.key === "ArrowLeft" ? cancelBtn : confirmBtn).focus();
    return;
  }

  // Enter ne submit QUE si focus est sur "Confirmer"
  if (e.key === "Enter") {
    const active = document.activeElement;

    const isTypingField =
      active &&
      (active.tagName === "INPUT" ||
        active.tagName === "TEXTAREA" ||
        active.tagName === "SELECT");

    if (isTypingField) return;

    if (confirmBtn && active === confirmBtn) {
      e.preventDefault();
      formEl?.requestSubmit?.();
      return;
    }

    // Evite les validations accidentelles
    e.preventDefault();
  }
});

  setConfirmDefault();
})();






