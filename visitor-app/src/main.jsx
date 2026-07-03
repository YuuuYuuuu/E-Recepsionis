import { StrictMode } from 'react'
import { createRoot } from 'react-dom/client'
import './index.css'
import App from './App.jsx'
import VisitorChrome from './VisitorChrome.jsx'

const landing = document.getElementById('visitor-landing-root')
const chrome = document.getElementById('visitor-chrome-root')

if (landing) {
  createRoot(landing).render(
    <StrictMode>
      <App />
    </StrictMode>
  )
}

if (chrome) {
  createRoot(chrome).render(
    <StrictMode>
      <VisitorChrome />
    </StrictMode>
  )
}
