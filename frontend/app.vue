<template>
  <NuxtLayout>
    <NuxtPage />
  </NuxtLayout>
</template>

<script setup lang="ts">
definePageMeta({
  pageTransition: {
    name: 'page',
    mode: 'out-in' // default
  },
})

useSeoMeta({
  title: 'Hợp tác xã Tương Lai Xanh - Rau sạch hữu cơ',
  description: 'Khám phá rau sạch, hữu cơ từ Tương Lai Xanh. Sản phẩm an toàn, chất lượng cao, giao hàng nhanh chóng.',
  ogTitle: 'Hợp tác xã Tương Lai Xanh - Rau sạch từ nông trại',
  ogDescription: 'Mua rau sạch hữu cơ tại Tương Lai Xanh. Tươi ngon, an toàn, thân thiện môi trường.',
  ogImage: '/images/banner.png', // Thay bằng URL hình ảnh thực tế
  ogUrl: import.meta.env.BASE_URL || 'https://tuonglaixanh.com',
  twitterCard: 'summary_large_image',
  twitterTitle: 'Hợp tác xã Tương Lai Xanh - Rau sạch hữu cơ',
  twitterDescription: 'Mua rau sạch hữu cơ tại Tương Lai Xanh. Tươi ngon, an toàn, thân thiện môi trường.',
  twitterImage: '/images/banner.png',
});
</script>

<style>
.page-enter-active,
.page-leave-active {
  transition: all 0.05s;
}

.page-enter-from,
.page-leave-to {
  opacity: 0;
  filter: blur(0.3rem);
}
</style>
