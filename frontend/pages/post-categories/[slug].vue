<template>
    <div class="min-h-screen items-center flex flex-col mt-16 pb-5 lg:mt-0">
        <div class="w-11/12 max-w-7xl flex gap-5">
            <div class="w-3/4">
                <div v-if="status === 'pending'" class="w-11/12 max-w-7xl mt-5 space-y-4">
                    <PostSkeletonCard v-for="i in 3" :key="i" />
                </div>
                <PostList v-else-if="posts.posts.length > 0" :title="'Danh sách bài viết'" :links="posts?.links" :meta="posts?.meta" :posts="posts?.posts"
                    @page-change="handlePageChange" />
                <div v-else class="w-11/12 max-w-7xl mt-5">
                    <div class="text-center text-2xl font-bold text-green-800">Không có bài viết nào</div>
                </div>
            </div>
            <div class="w-1/4 mt-5">
                <PostCategoryList />
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { useRoute } from 'vue-router'
import { usePosts } from '~/composables/usePosts'
import type { Post } from '~/types/post'
import type { PaginationMeta, PaginationLinks } from '~/types/api'

// Runtime config
const route = useRoute()
const slug = String(route.params.slug)
const perPage = 9 // Số bài viết mỗi trang
const currentPage = ref(1) // Trang hiện tại

const { getPostsByCategorySlug } = usePosts()

// Lấy bài viết theo category slug
const { data, status, error } = await getPostsByCategorySlug(slug, currentPage.value, perPage)

// Computed properties
const posts = computed<{ posts: Post[]; meta: PaginationMeta | null; links: PaginationLinks | null }>(() => ({
    posts: Array.isArray(data.value?.data) ? data.value.data : data.value?.data ? [data.value.data] : [],
    meta: data.value?.meta ?? null,
    links: data.value?.links ?? null
}))

// Xử lý sự kiện thay đổi trang
const handlePageChange = async (page: number) => {
    currentPage.value = page
    await getPostsByCategorySlug(slug, currentPage.value, perPage)
}
</script>
