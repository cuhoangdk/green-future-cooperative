<script setup lang="ts">
const layout = "customer";

definePageMeta({
  pageTransition: {
    name: 'bounce',
    mode: 'out-in' // default
  }
})

definePageMeta({
  layout: 'customer'
})

</script>

<template>
  <NuxtLayout >
    <NuxtPage />
  </NuxtLayout>
</template>

<style>
.page-enter-active,
.page-leave-active {
  transition: all 0.6s;
}
.page-enter-from,
.page-leave-to {
  opacity: 0;
  filter: blur(1rem);
}
</style>
