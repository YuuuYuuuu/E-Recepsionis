/**
 * URL absolut ke api/live_categories.php (diset visitor/index.php dari BASE_URL),
 * atau fallback dari pathname / env Vite saat dev.
 */
export function getLiveCategoriesUrl() {
  if (typeof window === 'undefined') {
    return '/api/live_categories.php'
  }
  const fromWindow = window.__LIVE_CATEGORIES_URL__
  if (fromWindow && String(fromWindow).trim()) {
    return String(fromWindow).trim()
  }
  const envUrl = import.meta.env?.VITE_LIVE_CATEGORIES_URL
  if (envUrl && String(envUrl).trim()) {
    return String(envUrl).trim()
  }
  if (import.meta.env?.DEV) {
    return '/api/live_categories.php'
  }
  const path = window.location.pathname || ''
  const m = path.match(/^(.*)\/visitor(?:\/|$)/i)
  if (m) {
    const base = m[1] || ''
    return `${window.location.origin}${base}/api/live_categories.php`
  }
  try {
    return new URL('../api/live_categories.php', window.location.href).href
  } catch {
    return `${window.location.origin}/api/live_categories.php`
  }
}
