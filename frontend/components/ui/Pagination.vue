<template>
  <div v-if="meta && links" class="flex flex-wrap items-center gap-2">
    <!-- Desktop version (hiển thị chi tiết trên màn hình lớn) -->
    <div class="hidden sm:flex items-center gap-1">
      <!-- Nút về đầu -->
      <!-- <button v-if="showFirstLast" @click="goToPage(1)" :disabled="meta.current_page === 1"
        class="btn btn-sm btn-square btn-outline btn-primary disabled:opacity-50 disabled:cursor-not-allowed">
        <ChevronsLeft class="w-4 h-4" />
      </button> -->

      <!-- Nút trang trước -->
      <button @click="goToPage(meta.current_page - 1)" :disabled="!links.prev"
        class="btn btn-sm btn-square btn-outline btn-primary disabled:opacity-50 disabled:cursor-not-allowed">
        <ChevronLeft class="w-4 h-4" />
      </button>

      <!-- Số trang -->
      <template v-if="showNumbers">
        <template v-for="(page, index) in getPageNumbers()" :key="index">
          <button v-if="page !== '...'" @click="goToPage(page as number)" :class="{
            'btn btn-sm btn-square btn-primary': page === meta.current_page,
            'btn btn-sm btn-square btn-outline btn-primary': page !== meta.current_page
          }" class="transition-colors duration-200">
            {{ page }}
          </button>
          <span v-else class="px-1 text-gray-500">...</span>
        </template>
      </template>

      <!-- Nút trang sau -->
      <button @click="goToPage(meta.current_page + 1)" :disabled="!links.next"
        class="btn btn-sm btn-square btn-outline btn-primary disabled:opacity-50 disabled:cursor-not-allowed">
        <ChevronRight class="w-4 h-4" />
      </button>

      <!-- Nút về cuối -->
      <!-- <button v-if="showFirstLast" @click="goToPage(meta.last_page)" :disabled="meta.current_page === meta.last_page"
        class="btn btn-sm btn-square btn-outline btn-primary disabled:opacity-50 disabled:cursor-not-allowed">
        <ChevronsRight class="w-4 h-4" />
      </button> -->
    </div>

    <!-- Mobile version (compact) -->
    <div class="flex sm:hidden items-center gap-2">
      <button @click="goToPage(meta.current_page - 1)" :disabled="!links.prev"
        class="btn btn-sm btn-square btn-outline btn-primary disabled:opacity-50 disabled:cursor-not-allowed">
        <ChevronLeft class="w-4 h-4" />
      </button>

      <span class="text-sm">
        {{ meta.current_page }} / {{ meta.last_page }}
      </span>

      <button @click="goToPage(meta.current_page + 1)" :disabled="!links.next"
        class="btn btn-sm btn-square btn-outline btn-primary disabled:opacity-50 disabled:cursor-not-allowed">
        <ChevronRight class="w-4 h-4" />
      </button>
    </div>

    <!-- Input để nhập trang (hiển thị trên cả desktop và mobile) -->
    <div class="flex items-center gap-2">
      <div class="relative">
        <input v-model="pageInputValue" @change="handlePageInput" type="number" min="1" :max="meta.last_page"
          class="input input-sm input-primary w-16 text-center" placeholder="Trang" />
      </div>
      <!-- <span class="text-sm text-gray-500 lg:hidden inline-block">
        Trang {{ meta.current_page }} / {{ meta.last_page }}
      </span> -->
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { ChevronLeft, ChevronRight, ChevronsLeft, ChevronsRight, ArrowRight } from 'lucide-vue-next'
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
const pageInputValue = ref<string>('')

const goToPage = (page: number) => {
  if (props.meta && page >= 1 && page <= props.meta.last_page) {
    emit('page-change', page)
  }
}

const handlePageInput = () => {
  const page = parseInt(pageInputValue.value)
  if (!isNaN(page) && props.meta) {
    goToPage(Math.min(Math.max(1, page), props.meta.last_page))
    pageInputValue.value = ''
  }
}

const getPageNumbers = () => {
  if (!props.meta || !props.showNumbers) return [] // Không hiển thị số trang nếu showNumbers = false
  const { current_page, last_page } = props.meta
  const pageNumbers = []

  if (last_page <= 5) {
    // Hiển thị tất cả các trang nếu chỉ có ít trang
    for (let i = 1; i <= last_page; i++) {
      pageNumbers.push(i)
    }
  } else {
    // Hiển thị trang đầu
    pageNumbers.push(1)

    // Hiển thị dấu ... nếu cần
    if (current_page > 3) pageNumbers.push('...')

    // Hiển thị các trang xung quanh trang hiện tại
    const startPage = Math.max(2, current_page - 1)
    const endPage = Math.min(last_page - 1, current_page + 1)

    for (let i = startPage; i <= endPage; i++) {
      pageNumbers.push(i)
    }

    // Hiển thị dấu ... nếu cần
    if (current_page < last_page - 2) pageNumbers.push('...')

    // Hiển thị trang cuối
    pageNumbers.push(last_page)
  }

  return pageNumbers
}
</script>