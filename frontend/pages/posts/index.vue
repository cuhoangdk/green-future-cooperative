<template>
    <div class="min-h-screen items-center flex flex-col mt-16 pb-5 lg:mt-0">
        <!-- Marquee Section -->
        <div class="w-11/12 max-w-7xl mt-5">
            <PostMarquee />
        </div>

        <!-- Featured Posts Section -->
        <div class="w-11/12 max-w-7xl mt-5">
            <PostFeatured />
        </div>

        <!-- Main Content: Posts List and Categories -->
        <div class="w-11/12 max-w-7xl mt-5 flex lg:flex-row flex-col gap-x-5">
            <!-- Posts List -->
            <div class="w-full lg:w-3/4 max-w-7xl">
                <div v-if="isLoading" class="space-y-4 mt-5">
                    <PostSkeletonCard v-for="i in 3" :key="i" />
                </div>
                <PostList v-else title="Bài đăng gần đây" :posts="posts" :meta="meta" :links="links"
                    @page-change="handlePageChange" />
            </div>

            <!-- Categories Sidebar -->
            <div class="lg:w-1/4 w-full max-w-7xl mt-5">
                <PostCategoryList />
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue'
import type { Post } from '~/types/post'
import type { PaginationMeta, PaginationLinks } from '~/types/api'
import { useRuntimeConfig } from '#app'
import { usePosts } from '~/composables/usePosts'

// Runtime config
const { public: { backendUrl } } = useRuntimeConfig()
const perPage = 9 // Số bài viết mỗi trang

// Khởi tạo usePosts
const { getPosts } = usePosts()

// State cho phân trang
const currentPage = ref(1)
const { data, status, error } = await getPosts(currentPage.value, perPage, false)

// Computed properties
const posts = computed<Post[]>(() => Array.isArray(data.value?.data) ? data.value.data : data.value?.data ? [data.value.data] : [])
const meta = computed<PaginationMeta | null>(() => data.value?.meta ?? null)
const links = computed<PaginationLinks | null>(() => data.value?.links ?? null)
const isLoading = computed(() => status.value === 'pending')

// Xử lý sự kiện thay đổi trang
const handlePageChange = async (page: number) => {
    currentPage.value = page
    await getPosts(page, perPage, false)
}

// Log lỗi ban đầu nếu có
if (error.value) {
    console.error('Failed to load initial posts:', error.value)
}
</script>