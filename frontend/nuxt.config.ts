// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2024-11-01',
  devtools: { enabled: true },
  modules: ['@nuxtjs/tailwindcss', '@pinia/nuxt', '@vesp/nuxt-fontawesome', '@nuxt/image'],
  css: ['sweetalert2/dist/sweetalert2.min.css'],
  // css: ['~/assets/css/tailwind.css'],
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
    '/admin/**': { ssr: false}, // Tắt SSR cho tất cả các route trong /admin
  },
  plugins: [
    { src: '~/plugins/toast.client.ts', mode: 'client' }
  ]
})