/**
 * URL ke api/live_session_guest_status.php (polling cadangan tamu).
 */
export function getLiveGuestStatusUrl() {
  if (typeof window === 'undefined') {
    return '/api/live_session_guest_status.php'
  }
  const fromWindow = window.__LIVE_GUEST_STATUS_URL__
  if (fromWindow && String(fromWindow).trim()) {
    return String(fromWindow).trim()
  }
  const envUrl = import.meta.env?.VITE_LIVE_GUEST_STATUS_URL
  if (envUrl && String(envUrl).trim()) {
    return String(envUrl).trim()
  }
  if (import.meta.env?.DEV) {
    return '/api/live_session_guest_status.php'
  }
  const path = window.location.pathname || ''
  const m = path.match(/^(.*)\/visitor(?:\/|$)/i)
  if (m) {
    const base = m[1] || ''
    return `${window.location.origin}${base}/api/live_session_guest_status.php`
  }
  try {
    return new URL('../api/live_session_guest_status.php', window.location.href).href
  } catch {
    return `${window.location.origin}/api/live_session_guest_status.php`
  }
}
