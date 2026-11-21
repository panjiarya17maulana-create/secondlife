// SecondLife — minimal JS (logika utama ada di PHP)
function toast(msg, duration = 2800) {
  const t = document.getElementById('toast');
  if (!t) return;
  t.innerHTML = msg;
  t.style.display = 'flex';
  clearTimeout(t._tid);
  t._tid = setTimeout(() => t.style.display = 'none', duration);
}
