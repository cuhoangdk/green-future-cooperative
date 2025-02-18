<script setup lang="ts">
import type { Post } from '~/types/post';
import type { PaginationMeta, PaginationLinks } from '~/types/api';
import { computed } from 'vue';

interface Props {
  title: string;
  posts: Post[];
  meta: PaginationMeta | null;
  links: PaginationLinks | null;
  maxCols?: number; // Mặc định là optional
  gridGap?: number; // Thêm option để điều chỉnh khoảng cách giữa các items
}

const props = withDefaults(defineProps<Props>(), {
  maxCols: 3, // Mặc định là 3 cột
  gridGap: 3  // Mặc định gap là 3
});

const emit = defineEmits(['page-change']);

// Computed property để tạo class cho grid
const gridClasses = computed(() => {
  return {
    'grid': true,
    'grid-cols-1': true,
    [`md:grid-cols-2`]: props.maxCols >= 2,
    [`lg:grid-cols-${props.maxCols}`]: true,
    [`gap-${props.gridGap}`]: true
  }
});

const onPageChange = (page: number) => {
  emit('page-change', page);
};
</script>

<template>
  <section class="w-full mt-5">
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-left text-xl font-bold text-green-800">{{ title }}</h2>
      <div class="flex-1 h-[3px] bg-green-500 ml-4"></div>
    </div>

    <!-- <div class="grid grid-cols-1 lg:grid-cols-3 md:grid-cols-2 gap-3"> -->
    <div :class="gridClasses">
      <PostCard v-for="post in posts" :key="post.id" :post="post" />
    </div>

    <CommonBasePagination :meta="meta" :links="links" @page-change="onPageChange" />
  </section>
</template>

