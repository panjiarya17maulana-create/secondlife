function showModal(id) {
  // Validasi form sebelum tampilkan modal
  const modal = document.getElementById(id);
  if (id === 'modalEdit') {
    const form = document.getElementById('formEditProfil');
    if (!form.checkValidity()) { form.reportValidity(); return; }
  }
  if (id === 'modalPassword') {
    const form = document.getElementById('formGantiPassword');
    if (!form.checkValidity()) { form.reportValidity(); return; }
  }
  if (id === 'modalHapus') {
    const form = document.getElementById('formHapusAkun');
    if (!form.checkValidity()) { form.reportValidity(); return; }
  }
  modal.classList.add('active');
  document.body.style.overflow = 'hidden';
}

function hideModal(id) {
  const modal = document.getElementById(id);
  const inner = modal.querySelector('.confirm-modal');
  inner.style.transform = 'scale(0.85) translateY(20px)';
  inner.style.opacity = '0';
  modal.style.opacity = '0';
  setTimeout(() => {
    modal.classList.remove('active');
    modal.style.opacity = '';
    inner.style.transform = '';
    inner.style.opacity = '';
    document.body.style.overflow = '';
  }, 250);
}

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    document.querySelectorAll('.confirm-modal-overlay.active').forEach(m => hideModal(m.id));
  }
});
