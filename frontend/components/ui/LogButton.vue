<template>
    <NuxtLink :to="to" class="btn btn-sm btn-info" title="Nhật ký chăm sóc">
        <Leaf class="w-4 h-4" /> Nhật ký
    </NuxtLink>
</template>


<script setup lang="ts">
import { Leaf } from 'lucide-vue-next'

defineProps<{
    to: string
}>()
</script>
