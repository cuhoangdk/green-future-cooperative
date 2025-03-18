// https://nuxt.com/docs/api/configuration/nuxt-config
import tailwindcss from "@tailwindcss/vite";
export default defineNuxtConfig({
  compatibilityDate: '2024-11-01',
  devtools: { enabled: true },
  modules: ['@pinia/nuxt', '@vesp/nuxt-fontawesome', '@nuxt/image'],
  css: ['sweetalert2/dist/sweetalert2.min.css','~/assets/css/tailwind.css'],
  // css: ['~/assets/css/tailwind.css'],
  vite: {
    plugins: [tailwindcss()],
  },
  app: {
    head: {
      link: [
        {
          rel: 'stylesheet',
          href: 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css',
        },
      ],
    },
    pageTransition: { name: 'page', mode: 'out-in' },
  },
  runtimeConfig: {
    public: {
      apiBaseUrl: process.env.API_BASE_URL || 'http://127.0.0.1:8000/api', // URL backend mặc định
      backendUrl: process.env.BACKEND_URL || 'http://127.0.0.1:8000', // URL backend mặc định
      placeholderImage: process.env.PLACEHOLDER_IMAGE || '/images/banner.png', // Ảnh placeholder mặc định
    },
  },
  routeRules: {
    "/admin/**": { ssr: false }
  },
  plugins: [
    { src: '~/plugins/toast.client.ts', mode: 'client' }
  ]
})