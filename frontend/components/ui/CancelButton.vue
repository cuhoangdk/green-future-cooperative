<template>
    <NuxtLink :to="to" class="btn btn-sm btn-square btn-error" title="Hủy bỏ">
        <XCircle class="w-4 h-4" />
    </NuxtLink>
</template>

<script setup lang="ts">
import { XCircle } from 'lucide-vue-next'

defineProps<{
    to: string
}>()
</script>