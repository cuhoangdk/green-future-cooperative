<template>
  <section class="w-full mt-5">
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-left text-xl font-bold text-green-800">{{ title }}</h2>
      <div class="flex-1 h-[3px] bg-green-500 ml-4"></div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 md:grid-cols-2 gap-3">
      <PostCard v-for="post in posts" :key="post.id" :post="post" />
    </div>

    <!-- Tích hợp phân trang vào bên trong PostList -->
    <CommonBasePagination 
      :meta="meta"
      :links="links"
      @page-change="onPageChange"
    />
  </section>
</template>

<script setup lang="ts">
import type { Post } from '~/types/post';
import type { PaginationMeta, PaginationLinks } from '~/types/api';

interface Props {
  title: string
  posts: Post[]
  meta: PaginationMeta | null
  links: PaginationLinks | null
}

defineProps<Props>();

const emit = defineEmits(['page-change']);

// Hàm xử lý khi phân trang được nhấn
const onPageChange = (page: number) => {
  emit('page-change', page);  // Phát sự kiện lên cha khi người dùng thay đổi trang
};
</script>
