// components/BasePagination.vue
<script setup lang="ts">
import { ChevronLeft, ChevronRight } from 'lucide-vue-next'
import type { PaginationMeta, PaginationLinks } from '~/types/api'

interface Props {
  meta: PaginationMeta | null
  links: PaginationLinks | null
}

interface Emits {
  (e: 'page-change', page: number): void
}

const props = defineProps<Props>()
const emit = defineEmits<Emits>()

const goToPage = (page: number) => {
  if (props.meta && page >= 1 && page <= props.meta.last_page) {
    emit('page-change', page)
  }
}
</script>

<template>
  <div v-if="meta && links" class="flex space-x-2 mt-4">
    <button
      @click="goToPage(meta.current_page - 1)"
      :disabled="!links.prev"
      class="border-[1px] p-1 border-green-600 bg-transparent text-green-600 rounded-md hover:bg-green-600 hover:text-white transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
    >
      <ChevronLeft class="w-4 h-4" />
    </button>
    <button
      @click="goToPage(meta.current_page + 1)"
      :disabled="!links.next"
      class="border-[1px] p-1 border-green-600 bg-transparent text-green-600 rounded-md hover:bg-green-600 hover:text-white transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
    >
      <ChevronRight class="w-4 h-4" />
    </button>
  </div>
</template>