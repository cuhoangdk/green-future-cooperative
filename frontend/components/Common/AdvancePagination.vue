<script setup lang="ts">
import { ChevronLeft, ChevronRight, ChevronsLeft, ChevronsRight } from 'lucide-vue-next'
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

// Tạo danh sách số trang hiển thị
const getPageNumbers = () => {
  if (!props.meta) return []
  const { current_page, last_page } = props.meta
  const pageNumbers = []

  if (last_page <= 7) {
    // Hiển thị tất cả nếu số trang nhỏ hơn hoặc bằng 7
    for (let i = 1; i <= last_page; i++) {
      pageNumbers.push(i)
    }
  } else {
    // Luôn hiển thị trang 1, trang cuối và một số trang gần trang hiện tại
    pageNumbers.push(1)

    if (current_page > 3) {
      pageNumbers.push('...')
    }

    for (let i = Math.max(2, current_page - 1); i <= Math.min(last_page - 1, current_page + 1); i++) {
      pageNumbers.push(i)
    }

    if (current_page < last_page - 2) {
      pageNumbers.push('...')
    }

    pageNumbers.push(last_page)
  }

  return pageNumbers
}
</script>

<template>
  <div v-if="meta && links" class="flex space-x-1 mt-4 items-center">
    <!-- Nút trang đầu -->
    <button
      @click="goToPage(1)"
      :disabled="meta.current_page === 1"
      class="border-[1px] p-1 border-green-600 bg-transparent text-green-600 rounded-md hover:bg-green-600 hover:text-white transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
    >
      <ChevronsLeft class="w-4 h-4" />
    </button>

    <!-- Nút trang trước -->
    <button
      @click="goToPage(meta.current_page - 1)"
      :disabled="!links.prev"
      class="border-[1px] p-1 border-green-600 bg-transparent text-green-600 rounded-md hover:bg-green-600 hover:text-white transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
    >
      <ChevronLeft class="w-4 h-4" />
    </button>

    <!-- Số trang -->
    <template v-for="(page, index) in getPageNumbers()" :key="index">
      <button
        v-if="page !== '...'"
        @click="goToPage(page as number)"
        :class="{
          'bg-green-600 text-white': page === meta.current_page,
          'border-green-600 text-green-600 hover:bg-green-600 hover:text-white': page !== meta.current_page
        }"
        class="border-[1px] px-3 py-1 rounded-md transition-colors duration-200"
      >
        {{ page }}
      </button>
      <span v-else class="px-2 text-gray-500">...</span>
    </template>

    <!-- Nút trang sau -->
    <button
      @click="goToPage(meta.current_page + 1)"
      :disabled="!links.next"
      class="border-[1px] p-1 border-green-600 bg-transparent text-green-600 rounded-md hover:bg-green-600 hover:text-white transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
    >
      <ChevronRight class="w-4 h-4" />
    </button>

    <!-- Nút trang cuối -->
    <button
      @click="goToPage(meta.last_page)"
      :disabled="meta.current_page === meta.last_page"
      class="border-[1px] p-1 border-green-600 bg-transparent text-green-600 rounded-md hover:bg-green-600 hover:text-white transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
    >
      <ChevronsRight class="w-4 h-4" />
    </button>
  </div>
</template>
