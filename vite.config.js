import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'
import path from 'path'

export default defineConfig({
  plugins: [react()],
  resolve: {
    alias: {
      '@': path.resolve(__dirname, './web/src'),
    },
  },

  server: {
    proxy: {
      '/api': {
        target: 'http://localhost',
        changeOrigin: false,
        rewrite: (path) => '/Salamass' + path,
      },
      '/uploads': {
        target: 'http://localhost',
        changeOrigin: false,
        rewrite: (path) => '/Salamass' + path,
      },
    },
  },
})