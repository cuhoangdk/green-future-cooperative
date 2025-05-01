// https://nuxt.com/docs/api/configuration/nuxt-config
import tailwindcss from "@tailwindcss/vite";
export default defineNuxtConfig({
  compatibilityDate: '2024-11-01',
  devtools: { enabled: true },
  modules: ['@pinia/nuxt', '@vesp/nuxt-fontawesome', '@nuxt/image', '@nuxtjs/leaflet'],
  css: ['sweetalert2/dist/sweetalert2.min.css', '~/assets/css/tailwind.css'],
  vite: {
    plugins: [tailwindcss()],
  },
  app: {
    head: {
      title: 'Hợp tác xã Tương Lai Xanh - Rau sạch & Hữu cơ', // Tối ưu ngắn gọn, chứa từ khóa
      htmlAttrs: {
        lang: 'vi', // Giữ nguyên
      },
      meta: [
        { charset: 'utf-8' }, // Giữ nguyên
        { name: 'viewport', content: 'width=device-width, initial-scale=1' }, // Giữ nguyên
        {
          name: 'description',
          content: 'Mua rau sạch và hữu cơ từ Hợp tác xã Tương Lai Xanh. Sản phẩm tươi ngon, an toàn, giao hàng nhanh tại Việt Nam.',
        }, // Thêm mô tả tĩnh
        {
          name: 'keywords',
          content: 'rau sạch, rau hữu cơ, thực phẩm sạch, hợp tác xã Tương Lai Xanh, rau organic, nông sản sạch, mua rau online',
        }, // Mở rộng từ khóa
        // Open Graph (OG) cho mạng xã hội
        { property: 'og:title', content: 'Hợp tác xã Tương Lai Xanh - Rau sạch & Hữu cơ' },
        {
          property: 'og:description',
          content: 'Khám phá rau sạch, hữu cơ từ Tương Lai Xanh. Sản phẩm an toàn, chất lượng cao, giao hàng nhanh chóng.',
        },
        { property: 'og:type', content: 'website' },
        { property: 'og:url', content: process.env.BASE_URL }, // Thay bằng URL thực tế
        { property: 'og:image', content: process.env.PLACEHOLDER_IMAGE || '/images/banner.png' }, // Thay bằng URL hình ảnh thực tế
        // Twitter Card
        { name: 'twitter:card', content: 'summary_large_image' },
        { name: 'twitter:title', content: 'Hợp tác xã Tương Lai Xanh - Rau sạch & Hữu cơ' },
        {
          name: 'twitter:description',
          content: 'Mua rau sạch, hữu cơ từ Tương Lai Xanh. Tươi ngon, an toàn, giao hàng nhanh.',
        },
        { name: 'twitter:image', content: process.env.PLACEHOLDER_IMAGE || '/images/banner.png' }, // Thay bằng URL hình ảnh thực tế
        // Robots
        { name: 'robots', content: 'index, follow' }, // Cho phép công cụ tìm kiếm lập chỉ mục
      ],
      link: [
        // Giữ Font Awesome
        {
          rel: 'stylesheet',
          href: 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css',
        },
        // Thêm favicon
        { rel: 'icon', type: 'image/png', href: '/favicon.ico' }, // Thay bằng favicon thực tế
        // Thêm canonical URL
        { rel: 'canonical', href: process.env.BASE_URL, }, // Thay bằng URL thực tế
      ],
      // Thêm JSON-LD cho dữ liệu có cấu trúc (Schema)
      script: [
        {
          type: 'application/ld+json',
          innerHTML: JSON.stringify({
            '@context': 'https://schema.org',
            '@type': 'Organization',
            name: 'Hợp tác xã Tương Lai Xanh',
            url: process.env.BASE_URL,
            logo: process.env.LOGO || '/images/logo.png', // Thay bằng URL logo thực tế
            description: 'Hợp tác xã Tương Lai Xanh cung cấp rau sạch và hữu cơ chất lượng cao tại Việt Nam.',
            contactPoint: {
              '@type': 'ContactPoint',
              telephone: '0917248016', // Thay bằng số điện thoại thực tế
              contactType: 'customer service',
            },
          }),
        },
      ],
    },
    pageTransition: { name: 'page', mode: 'out-in' },
  },
  runtimeConfig: {
    public: {
      apiBaseUrl: process.env.API_BASE_URL, // URL backend mặc định
      backendUrl: process.env.BACKEND_URL, // URL backend mặc định
      baseUrl: process.env.BASE_URL, // URL frontend mặc định
      placeholderImage: process.env.PLACEHOLDER_IMAGE || '/images/banner.png', // Ảnh placeholder mặc định
      logo: process.env.LOGO || '/images/logo.png', // Logo mặc định
    },
  },
  routeRules: {
    "/admin/**": { ssr: false }
  },
  plugins: [
    { src: '~/plugins/toast.client.ts', mode: 'client' }
  ]
})