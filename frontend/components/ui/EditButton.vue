<template>
    <button @click="onClick" class="btn btn-sm btn-square btn-primary" title="Chỉnh sửa">
        <Edit2Icon class="w-4 h-4" />
    </button>
</template>

<script setup lang="ts">
import { Edit2Icon } from 'lucide-vue-next'

defineProps<{
    onClick: () => void
}>()
</script>