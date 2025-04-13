<template>
  <div>
    <!-- Tiêu đề -->
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-left text-xl font-bold text-green-800">Danh mục</h2>
      <div class="flex-1 h-[3px] bg-green-300 mx-4"></div>
    </div>

    <!-- Loading skeleton -->
    <template v-if="status === 'pending'">
      <div v-for="i in 5" :key="i" class="skeleton h-6 bg-gray-200 rounded w-3/4 my-5"></div>
    </template>

    <!-- Danh mục -->
    <ul v-else class="text-left space-y-2">
      <li v-for="category in categories" :key="category.id">
        <a
          @click.prevent="updateQuery(category.slug, category.id)"
          class="text-left text-xl font-semibold text-green-600 hover:text-green-400 transition-colors duration-200 cursor-pointer"
        >
          {{ category.name }}
        </a>
      </li>
    </ul>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import type { PostCategory } from '~/types/post'
import { usePostCategories } from '~/composables/usePostCategories'

const router = useRouter()

// Sử dụng composable usePostCategories
const { getAllPostCategories } = usePostCategories()

// Gọi API để lấy danh mục
const { data, status, error } = await getAllPostCategories(AuthType.Guest)

// Xử lý danh mục và trạng thái loading
const categories = computed<PostCategory[]>(() =>
  Array.isArray(data.value?.data) ? data.value.data : data.value ? [data.value.data] : []
)

// Cập nhật query param
const updateQuery = (slug: string, id: number) => {
  if (router.currentRoute.value.name !== 'posts-category') {
    router.push({
      name: 'posts-category',
      query: { slug, id }, // Thêm slug và id vào query params
    })
  } else {
    router.push({
      query: { slug, id }, // Thêm slug và id vào query params
    })
  }
}

// Log lỗi nếu có
if (error.value) {
  console.error('Failed to load post categories:', error.value)
}
</script>