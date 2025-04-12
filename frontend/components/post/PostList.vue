<template>
  <section class="w-full">
    <div class="grid relative gap-3 mb-4" :class="[
      gridColsClass,
    ]">
      <PostCard v-for="post in posts" :key="post.id" :post="post" />
      <div v-if="status === 'pending'" class="absolute inset-0 flex items-center justify-center bg-gray-100/20 z-10">
        <span class="loading loading-spinner loading-xl"></span>
      </div>
    </div>

    <UiPagination :meta="meta" :links="links" :show-first-last="showFirstLast" :show-numbers="showNumbers"
      @page-change="onPageChange" />
  </section>
</template>

<script setup lang="ts">
import type { Post } from '~/types/post';
import type { PaginationMeta, PaginationLinks } from '~/types/api';

interface Props {
  posts: Post[];
  meta: PaginationMeta | null;
  links: PaginationLinks | null;
  status: string;
  showFirstLast?: boolean;
  showNumbers?: boolean;
  maxColumns?: number;
}

interface Emits {
  (e: 'page-change', page: number): void
}

const props = defineProps<Props>();
const emit = defineEmits<Emits>();

const onPageChange = (page: number) => {
  emit('page-change', page);
};

// Tính toán class cho grid columns dựa trên maxColumns
const gridColsClass = computed(() => {
  const maxCols = props.maxColumns || 3; // Giá trị mặc định là 3

  // Xác định class grid-cols cho desktop (lg)
  let lgCols = `lg:grid-cols-${Math.min(maxCols, 6)}`; // Giới hạn tối đa 6 cột cho desktop

  // Xác định class grid-cols cho tablet (md)
  // Nếu maxColumns >= 3, tablet hiển thị 2 cột, ngược lại hiển thị 1 cột
  let mdCols = maxCols >= 3 ? 'md:grid-cols-2' : 'md:grid-cols-1';

  // Cho mobile luôn là 1 cột
  return `grid-cols-1 ${mdCols} ${lgCols}`;
});
</script>