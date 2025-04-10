<template>
    <NuxtLink :to="to" class="btn btn-sm btn-warning" title="Mở bán">
        <ShoppingBasket class="w-4 h-4" /> Mở bán
    </NuxtLink>
</template>

<script setup lang="ts">
import { ShoppingBasket } from 'lucide-vue-next'

defineProps<{
    to: string
}>()
</script>