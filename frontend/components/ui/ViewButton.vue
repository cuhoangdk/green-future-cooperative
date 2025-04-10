<template>
    <NuxtLink :to="to" class="btn btn-sm btn-square btn-info" title="Xem">
        <Eye class="w-4 h-4" />
    </NuxtLink>
</template>

<script setup lang="ts">
import { Eye } from 'lucide-vue-next'

defineProps<{
    to: string
}>()
</script>