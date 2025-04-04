<!-- components/ui/UiButtonBan.vue -->
<template>
    <button
      @click.stop="onClick"
      class="btn btn-sm btn-square"
      :class="isBanned ? 'btn-success' : 'btn-warning'"
      :title="isBanned ? 'Bỏ cấm' : 'Cấm'"
    >
      <BanIcon class="w-4 h-4" />
    </button>
  </template>
  
  <script setup lang="ts">
  import {BanIcon} from 'lucide-vue-next'
  
  defineProps<{
    isBanned: boolean
    onClick: () => void
  }>()
  </script>