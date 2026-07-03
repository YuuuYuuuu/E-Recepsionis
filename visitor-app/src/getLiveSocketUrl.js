export function getLiveSocketUrl() {
  if (typeof window !== 'undefined' && window.__LIVE_SOCKET_URL__) {
    return String(window.__LIVE_SOCKET_URL__).replace(/\/$/, '')
  }
  const env = import.meta.env?.VITE_SOCKET_URL
  if (env) return String(env).replace(/\/$/, '')
  return 'http://127.0.0.1:3001'
}
