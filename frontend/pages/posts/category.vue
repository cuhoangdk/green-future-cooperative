<template>
    <div class="min-h-screen items-center flex flex-col mt-16 pb-5 lg:mt-0">
        <div class="w-11/12 max-w-7xl flex gap-5">
            <div class="w-3/4">
                <div class="flex justify-between items-center my-5 gap-3">
                    <h2 class="text-left text-xl font-bold text-green-800">BÀI VIẾT</h2>
                    <div class="flex-1 h-[3px] bg-green-500"></div>
                </div>
                <PostList  :links="posts?.links" :meta="posts?.meta" :posts="posts?.posts"
                    @page-change="handlePageChange"  :status="status"/>
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
const slug = String(route.query.slug)
const category_id = ref(Number(route.query.id)) 
const perPage = 6 // Số bài viết mỗi trang
const currentPage = ref(1) // Trang hiện tại

const { searchPosts } = usePosts()

const search = async () => {
    await searchPosts({
        page: currentPage.value,
        per_page: perPage,
        category_id: category_id.value,
    }, AuthType.Guest)
}

watch(() => route.query.id, async (newId) => {
    category_id.value = Number(newId)
    search()
})

const { data, status, error } = await searchPosts({
    page: currentPage.value,
    per_page: perPage,
    category_id: category_id.value,
}, AuthType.Guest)

const posts = computed<{ posts: Post[]; meta: PaginationMeta | null; links: PaginationLinks | null }>(() => ({
    posts: Array.isArray(data.value?.data) ? data.value.data : data.value?.data ? [data.value.data] : [],
    meta: data.value?.meta ?? null,
    links: data.value?.links ?? null
}))

const handlePageChange = async (page: number) => {
    currentPage.value = page
    search()
}
</script>
