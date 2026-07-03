/**
 * URL absolut ke api/call_staff.php (diset visitor/index.php dari BASE_URL),
 * atau fallback dari pathname / env Vite saat dev.
 */
export function getCallStaffUrl() {
  if (typeof window === 'undefined') {
    return '/api/call_staff.php'
  }
  const fromWindow = window.__CALL_STAFF_URL__
  if (fromWindow && String(fromWindow).trim()) {
    return String(fromWindow).trim()
  }
  const envUrl = import.meta.env?.VITE_CALL_STAFF_URL
  if (envUrl && String(envUrl).trim()) {
    return String(envUrl).trim()
  }
  if (import.meta.env?.DEV) {
    return '/api/call_staff.php'
  }
  const path = window.location.pathname || ''
  const m = path.match(/^(.*)\/visitor(?:\/|$)/i)
  if (m) {
    const base = m[1] || ''
    return `${window.location.origin}${base}/api/call_staff.php`
  }
  try {
    return new URL('../api/call_staff.php', window.location.href).href
  } catch {
    return `${window.location.origin}/api/call_staff.php`
  }
}
