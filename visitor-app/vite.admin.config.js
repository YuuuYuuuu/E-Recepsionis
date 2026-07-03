import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'
import path from 'path'
import { fileURLToPath } from 'url'

const __dirname = path.dirname(fileURLToPath(import.meta.url))

export default defineConfig({
  plugins: [react()],
  base: './',
  build: {
    outDir: path.resolve(__dirname, '../admin/assets/live-chat'),
    emptyOutDir: true,
    rollupOptions: {
      input: path.resolve(__dirname, 'admin.html'),
      output: {
        entryFileNames: 'assets/admin-live.js',
        chunkFileNames: 'assets/admin-live-chunk-[hash].js',
        assetFileNames: 'assets/admin-live-[name][extname]',
      },
    },
  },
})
