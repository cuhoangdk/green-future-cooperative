<template>
  <div v-if="meta && links" class="flex space-x-1 items-center">
    <!-- Nút về đầu -->
    <button v-if="showFirstLast" @click="goToPage(1)" :disabled="meta.current_page === 1"
      class="btn btn-xs btn-square btn-outline btn-primary disabled:opacity-50 disabled:cursor-not-allowed">
      <ChevronsLeft class="w-4 h-4" />
    </button>

    <!-- Nút trang trước -->
    <button @click="goToPage(meta.current_page - 1)" :disabled="!links.prev"
      class="btn btn-sm btn-square btn-outline btn-primary disabled:opacity-50 disabled:cursor-not-allowed">
      <ChevronLeft class="w-4 h-4" />
    </button>

    <!-- Số trang -->
    <template v-if="showNumbers">
      <template v-for="(page, index) in getPageNumbers()" :key="index">
        <button v-if="page !== '...'" @click="goToPage(page as number)" :class="{
          'btn btn-square btn-primary': page === meta.current_page,
          'btn btn-sm btn-square btn-outline btn-primary': page !== meta.current_page
        }" class="transition-colors duration-200">
          {{ page }}
        </button>
        <span v-else class="px-2 text-gray-500">...</span>
      </template>
    </template>

    <!-- Nút trang sau -->
    <button @click="goToPage(meta.current_page + 1)" :disabled="!links.next"
      class="btn btn-sm btn-square btn-outline btn-primary disabled:opacity-50 disabled:cursor-not-allowed">
      <ChevronRight class="w-4 h-4" />
    </button>

    <!-- Nút về cuối -->
    <button v-if="showFirstLast" @click="goToPage(meta.last_page)" :disabled="meta.current_page === meta.last_page"
      class="btn btn-xs btn-square btn-outline btn-primary disabled:opacity-50 disabled:cursor-not-allowed">
      <ChevronsRight class="w-4 h-4" />
    </button>
  </div>
</template>

<script setup lang="ts">
import { ChevronLeft, ChevronRight, ChevronsLeft, ChevronsRight } from 'lucide-vue-next'
import type { PaginationMeta, PaginationLinks } from '~/types/api'

interface Props {
  meta: PaginationMeta | null
  links: PaginationLinks | null
  showNumbers?: boolean
  showFirstLast?: boolean
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

const getPageNumbers = () => {
  if (!props.meta || !props.showNumbers) return [] // Không hiển thị số trang nếu showNumbers = false
  const { current_page, last_page } = props.meta
  const pageNumbers = []

  if (last_page <= 7) {
    for (let i = 1; i <= last_page; i++) {
      pageNumbers.push(i)
    }
  } else {
    pageNumbers.push(1)
    if (current_page > 3) pageNumbers.push('...')
    for (let i = Math.max(2, current_page - 1); i <= Math.min(last_page - 1, current_page + 1); i++) {
      pageNumbers.push(i)
    }
    if (current_page < last_page - 2) pageNumbers.push('...')
    pageNumbers.push(last_page)
  }

  return pageNumbers
}
</script>
