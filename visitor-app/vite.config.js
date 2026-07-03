import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'
import path from 'path'
import { fileURLToPath } from 'url'

const __dirname = path.dirname(fileURLToPath(import.meta.url))

export default defineConfig({
  plugins: [react()],
  base: './',
  server: {
    proxy: {
      '/api': {
        target: process.env.VITE_PHP_ORIGIN || 'http://127.0.0.1:8888',
        changeOrigin: true,
        rewrite: (path) => path.replace(/^\/api/, '/Recepsionis/api'),
      },
      '/socket.io': {
        target: 'http://127.0.0.1:3001',
        ws: true,
      },
    },
  },
  build: {
    outDir: path.resolve(__dirname, '../visitor/assets/landing'),
    emptyOutDir: true,
    cssCodeSplit: false,
    rollupOptions: {
      output: {
        entryFileNames: 'assets/visitor-landing.js',
        chunkFileNames: 'assets/visitor-chunk-[hash].js',
        assetFileNames: (info) => {
          if (info.names?.[0] === 'style.css' || info.name === 'style.css') {
            return 'assets/visitor-landing.css'
          }
          return 'assets/visitor-landing-[hash][extname]'
        },
      },
    },
  },
})
