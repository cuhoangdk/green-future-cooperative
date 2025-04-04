<!-- components/ui/UiButtonDelete.vue -->
<template>
    <button
      @click.stop="onClick"
      class="btn btn-sm btn-square btn-error"
      title="Xóa"
    >
      <Trash2Icon class="w-4 h-4" />
    </button>
  </template>
  
  <script setup lang="ts">
  import { Trash2Icon } from 'lucide-vue-next'
  
  defineProps<{
    onClick: () => void
  }>()
  </script>