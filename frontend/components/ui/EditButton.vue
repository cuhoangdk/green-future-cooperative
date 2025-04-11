<template>
    <NuxtLink :to="to" class="btn btn-sm btn-square btn-primary" title="Chỉnh sửa">
        <Edit2Icon class="w-4 h-4" />
    </NuxtLink>
</template>

<script setup lang="ts">
import { Edit2Icon } from 'lucide-vue-next'

defineProps<{
    to: string
}>()
</script>