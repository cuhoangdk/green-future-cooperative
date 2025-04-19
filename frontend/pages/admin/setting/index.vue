<template>
    <div class="p-4 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        <NuxtLink to="/admin/setting/shipping-fee" class="btn">Phí vận chuyển</NuxtLink>
        <NuxtLink to="/admin/setting/carousel" class="btn">Khác</NuxtLink>
    </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'user', title: 'Cài đặt' })

</script>