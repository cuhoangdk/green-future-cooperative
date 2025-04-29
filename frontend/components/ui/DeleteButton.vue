<!-- components/ui/UiButtonDelete.vue -->
<template>
  <div @click.stop="onClick" class="btn btn-sm btn-square btn-error cursor-pointer" title="Xóa">
    <Trash2Icon class="w-4 h-4" />
  </div>
</template>

<script setup lang="ts">
import { Trash2Icon } from 'lucide-vue-next'

defineProps<{
  onClick: () => void
}>()
</script>