import { StrictMode } from 'react'
import { createRoot } from 'react-dom/client'
import './src/index.css'
import AdminLiveApp from './src/AdminLiveApp.jsx'

const el = document.getElementById('admin-live-root')
if (el) {
  createRoot(el).render(
    <StrictMode>
      <AdminLiveApp />
    </StrictMode>
  )
}
