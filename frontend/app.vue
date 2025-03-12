<template>
  <NuxtLayout >
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
</script>

<style>
.page-enter-active,
.page-leave-active {
  transition: all 0.1s;
}
.page-enter-from,
.page-leave-to {
  opacity: 0;
  filter: blur(0.3rem);
}
</style>
