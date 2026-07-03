(function () {
  const IDLE_LIMIT_MS = 10 * 60 * 1000; // 10 menit
  const HOME_URL = 'index.php'; // relatif dari folder visitor

  let timeoutId = null;

  function redirectHome() {
    try {
      window.location.href = HOME_URL;
    } catch (e) {
      window.location.assign(HOME_URL);
    }
  }

  function resetTimer() {
    if (timeoutId) clearTimeout(timeoutId);
    timeoutId = setTimeout(redirectHome, IDLE_LIMIT_MS);
  }

  const events = ['mousemove', 'mousedown', 'keydown', 'scroll', 'touchstart'];
  events.forEach((ev) => {
    window.addEventListener(ev, resetTimer, { passive: true });
  });

  // Jika user balik lagi setelah tab ditekan, mulai hitung ulang
  document.addEventListener('visibilitychange', () => {
    if (document.visibilityState === 'visible') resetTimer();
  });

  const start = () => resetTimer();
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', start);
  } else {
    start();
  }
})();

