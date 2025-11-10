import { fileURLToPath, URL } from 'node:url'

import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import vueDevTools from 'vite-plugin-vue-devtools'

// https://vite.dev/config/
export default defineConfig({
  plugins: [
    vue(),
    vueDevTools(),
  ],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url))
    },
  },
  server: {
    host: '0.0.0.0',
    port: 3000,
    open: true, // 自动打开浏览器
    cors: true, // 允许跨域
    // 配置代理
    proxy: {
      // '/admin': {
      //   target: 'http://localhost:8000',
      //   changeOrigin: true,
      //   rewrite: (path) => path.replace(/^\/admin/, '/admin'),
      //   secure: false, // 如果是https接口，需要配置false
      //   // 配置websocket代理
      //   ws: true,
      //   // 配置超时
      //   timeout: 5000,
      //   // 配置请求头
      //   headers: {
      //     'Proxy-Request': 'Vite-Dev-Server'
      //   }
      // },
      // '/api': {
      //   target: 'http://localhost:8000',
      //   changeOrigin: true,
      //   rewrite: (path) => path.replace(/^\/api/, '/api'),
      //   secure: false
      // }
    }
  },
  // 构建配置
  build: {
    target: 'esnext',
    minify: 'esbuild',
    sourcemap: false,
    // 配置chunk大小警告
    chunkSizeWarningLimit: 1000,
    rollupOptions: {
      output: {
        // 手动分包
        manualChunks: {
          'vendor': ['vue', 'vue-router', 'pinia'],
          'element-plus': ['element-plus'],
          'utils': ['lodash-es', 'dayjs']
        }
      }
    }
  },
  // 预览配置
  preview: {
    port: 5000,
    proxy: {
      '/admin': {
        target: 'http://localhost:8000',
        changeOrigin: true,
        rewrite: (path) => path.replace(/^\/admin/, '/admin')
      }
    }
  }
})