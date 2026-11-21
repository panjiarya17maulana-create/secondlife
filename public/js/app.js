// SecondLife app.js
// Auto-submit sort form
document.querySelectorAll('[data-autosubmit]').forEach(el => {
  el.addEventListener('change', () => el.closest('form').submit());
});
