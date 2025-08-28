// public/js/collection-edit-modal.js
// Gestione modale Edit meta: apertura, chiusura e salvataggio via fetch
(function(){
  const modal = document.getElementById('editMetaModal');
  if (!modal) return; // non sei creator o partial non incluso

  // Entry points
  const openBtn = document.getElementById('editMetaBtn');
  const closeBtn = modal.querySelector('[data-edit-close]');
  const cancelBtn = modal.querySelector('[data-edit-cancel]');
  const saveBtn = modal.querySelector('[data-edit-save]');
  const form = document.getElementById('editMetaForm');
  const errorsBox = document.getElementById('editMetaErrors');

  function openModal(){ modal.classList.remove('hidden'); modal.classList.add('flex'); }
  function closeModal(){ modal.classList.add('hidden'); modal.classList.remove('flex'); }

  openBtn && openBtn.addEventListener('click', openModal);
  closeBtn && closeBtn.addEventListener('click', closeModal);
  cancelBtn && cancelBtn.addEventListener('click', closeModal);

  async function save(){
    if (!form) return;
    errorsBox.classList.add('hidden');
    errorsBox.textContent = '';

    const payload = Object.fromEntries(new FormData(form).entries());
    payload.is_published = form.querySelector('input[name="is_published"]').checked ? 1 : 0;

    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const url = modal.getAttribute('data-update-url');

    try {
      const res = await fetch(url, {
        method: 'PATCH',
        headers: {
          'X-CSRF-TOKEN': csrf,
          'Accept': 'application/json',
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(payload)
      });
      const data = await res.json();
      if (!res.ok || !data.success) {
        if (data && data.errors) {
          errorsBox.innerHTML = Object.values(data.errors).flat().join('<br>');
          errorsBox.classList.remove('hidden');
        }
        throw new Error((data && data.message) || 'Update failed');
      }

      // Aggiorna DOM essenziale (titolo/descrizione)
      const c = data.collection || {};
      const titleEl = document.getElementById('collection-title');
      const descEl = document.getElementById('collection-description');
      if (titleEl && c.collection_name) titleEl.textContent = c.collection_name;
      if (descEl) {
        descEl.textContent = c.description || '';
        descEl.classList.toggle('hidden', !(c.description));
      }

  // Toast ok (testo passato dal blade via data-*)
  const toast = document.createElement('div');
  toast.className = 'fixed z-50 px-4 py-2 text-sm font-medium text-white transform -translate-x-1/2 bg-emerald-600 rounded-lg bottom-4 left-1/2';
  toast.textContent = modal.getAttribute('data-toast-updated') || 'Updated';
      document.body.appendChild(toast);
      setTimeout(() => toast.remove(), 2500);
      closeModal();
    } catch (e){
      console.error('Update error', e);
    }
  }

  saveBtn && saveBtn.addEventListener('click', save);
})();
