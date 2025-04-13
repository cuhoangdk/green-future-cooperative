<template>
    <div class="min-h-screen items-center flex flex-col pb-5">
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
                <div class="flex justify-between items-center my-5 gap-3">
                    <h2 class="text-left text-xl font-bold text-green-800">BÀI VIẾT</h2>
                    <div class="flex-1 h-[3px] bg-green-500"></div>
                </div>
                <PostList title="Bài đăng gần đây" :posts="posts" :meta="meta" :links="links" 
                    @page-change="handlePageChange" :status="status" />
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
const perPage = 6 // Số bài viết mỗi trang

// Khởi tạo usePosts
const { getPosts } = usePosts()

// State cho phân trang
const currentPage = ref(1)
const { data, status, error } = await getPosts(currentPage.value, perPage, AuthType.Guest)

// Computed properties
const posts = computed<Post[]>(() => Array.isArray(data.value?.data) ? data.value.data : data.value?.data ? [data.value.data] : [])
const meta = computed<PaginationMeta | null>(() => data.value?.meta ?? null)
const links = computed<PaginationLinks | null>(() => data.value?.links ?? null)

// Xử lý sự kiện thay đổi trang
const handlePageChange = async (page: number) => {
    currentPage.value = page
    await getPosts(page, perPage, AuthType.Guest)
}

// Log lỗi ban đầu nếu có
if (error.value) {
    console.error('Failed to load initial posts:', error.value)
}
</script>