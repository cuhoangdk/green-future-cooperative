// https://nuxt.com/docs/api/configuration/nuxt-config

import tailwindcss from "@tailwindcss/vite";

export default defineNuxtConfig({
  compatibilityDate: '2024-11-01',
  devtools: { enabled: true },
  css: ['~/assets/css/main.css'],
  vite: {
    plugins: [
      tailwindcss(),
    ],
  },

  runtimeConfig: {
    apiSecret: process.env.NUXT_API_SECRET,
    public: {
      apiBase: process.env.NUXT_PUBLIC_API_BASE || 'http://127.0.0.1:8000/api',
      backendUrl: process.env.BACKEND_URL || 'http://127.0.0.1:8000'
    },
    defaultImage: "'/img/banner.png'",    
  },

  app: {
    head: {
      title: 'Green Future - Thực phẩm sạch cho tương lai xanh',
      meta: [
        { charset: 'utf-8' },
        { name: 'viewport', content: 'width=device-width, initial-scale=1' },
        { name: 'description', content: 'Green Future - Nguồn cung cấp thực phẩm sạch và an toàn hàng đầu tại Việt Nam' },
        { name: 'keywords', content: 'thực phẩm sạch, rau củ hữu cơ, nông sản sạch, green future' }
      ],
      link: [
        { rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' }
      ]
    },
    pageTransition: { name: 'page', mode: 'out-in' }

  },

  typescript: {
    strict: true
  },

  modules: ['@nuxt/image']
})