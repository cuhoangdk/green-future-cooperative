<template>
  <section class="w-full mt-5">
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-left text-xl font-bold text-green-800">{{ title }}</h2>
      <div class="flex-1 h-[3px] bg-green-500 ml-4"></div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 md:grid-cols-2 gap-3 mb-4">
      <PostCard v-for="post in posts" :key="post.id" :post="post" />
    </div>

    <UiPagination :meta="meta" :links="links" :show-first-last="true" :show-numbers="true" @page-change="onPageChange" />
  </section>
</template>

<script setup lang="ts">
import type { Post } from '~/types/post';
import type { PaginationMeta, PaginationLinks } from '~/types/api';

interface Props {
  title: string;
  posts: Post[];
  meta: PaginationMeta | null;
  links: PaginationLinks | null;
}

interface Emits {
  (e: 'page-change', page: number): void
}

defineProps<Props>()
const emit = defineEmits<Emits>();

const onPageChange = (page: number) => {
  emit('page-change', page);
};
</script>


