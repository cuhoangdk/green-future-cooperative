<template>
    <div>
        <div class="flex flex-col md:flex-row justify-between items-start gap-3 border-gray-200 px-3 py-4">
            <div class="flex flex-col w-full md:flex-row gap-2">
                <div class="relative w-full md:w-auto">
                    <Search class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 z-10 w-5 h-5" />
                    <input v-model="searchQuery" type="search" placeholder="Tìm kiếm bài viết..."
                        class="input input-sm input-primary w-full pl-10" @input="debouncedSearch" />
                </div>
                <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto">
                    <select v-model="selectedCategory" class="select select-sm select-primary w-full sm:w-[150px]"
                        @change="search">
                        <option value="">Tất cả danh mục</option>
                        <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name
                        }}
                        </option>
                    </select>
                    <select v-model="selectedStatus" class="select select-sm select-primary w-full sm:w-[150px]"
                        @change="search">
                        <option value="">Tất cả trạng thái</option>
                        <option value="published">Đã xuất bản</option>
                        <option value="draft">Bản nháp</option>
                        <option value="archived">Lưu trữ</option>
                    </select>
                    <select v-model="sortBy" class="select select-sm select-primary w-full sm:w-[150px]"
                        @change="search">
                        <option value="">Sắp xếp theo</option>
                        <option value="created_desc">Mới nhất</option>
                        <option value="created_asc">Cũ nhất</option>
                        <option value="title">Theo tên</option>
                        <option value="category_id">Theo danh mục</option>
                        <option value="user_id">Theo tác giả</option>
                        <option value="post_status">Theo trạng thái</option>
                    </select>
                    <label class="lg:col-span-1 col-span-2 flex items-center justify-center gap-5">
                        <div class="flex items-center gap-2">
                            <input v-model="checkboxIsHot" type="checkbox" class="checkbox checkbox-primary"
                                @change="search" /> Nổi bật
                        </div>
                        <div class="flex items-center gap-2">
                            <input v-model="checkboxIsFeatured" type="checkbox" class="checkbox checkbox-primary"
                                @change="search" /> Hot
                        </div>
                    </label>
                </div>
            </div>
            <button @click="$router.push('/admin/posts/create')" class="btn btn-sm btn-primary w-full md:w-auto">
                <Plus class="w-5 h-5" /> Thêm
            </button>
        </div>
        <div class="relative">
            <!-- Loading Overlay specific to table/grid -->
            <div v-if="status === 'pending'"
                class="absolute inset-0 bg-gray-50 opacity-25 flex justify-center items-center z-10">
                <span class="loading loading-spinner loading-lg"></span>
            </div>
            <TablePost :posts="posts.posts" :on-delete="handleDeletePost" />

            <GridPost :posts="posts.posts" :on-delete="handleDeletePost" />
        </div>
        <div class="flex flex-col sm:flex-row justify-between items-center m-4 gap-2">
            <div class="flex items-center space-x-2">
                <p class="text-sm text-gray-600 w-24">{{ posts.posts.length }} / {{ posts.meta?.total }}</p>
                <select v-model="perPage" class="select select-sm select-primary" @change="search">
                    <option v-for="n in [10, 25, 50, 100]" :value="n" :key="n">{{ n }}</option>
                </select>
            </div>
            <UiPagination :links="posts.links" :meta="posts.meta" :show-first-last="true" :show-numbers="true"
                @page-change="handlePageChange" />
        </div>
    </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'user', title: 'Bài viết', description: 'Quản lý bài viết trên trang web' })

import { Search, Plus } from 'lucide-vue-next'
import { usePosts, usePostCategories } from '#imports'
import { debounce } from 'lodash-es'
import { useSwal } from '~/composables/useSwal'
import type { PaginationMeta, PaginationLinks } from '~/types/api'
import type { Post, PostCategory } from '~/types/post'

const { searchPosts, deletePost } = usePosts()
const { getAllPostCategories } = usePostCategories()
const swal = useSwal()
const { $toast } = useNuxtApp()

const currentPage = ref(Number(useRoute().query.page) || 1)
const perPage = ref(10)
const searchQuery = ref('')
const selectedStatus = ref('')
const selectedCategory = ref('')
const checkboxIsHot = ref(false)
const checkboxIsFeatured = ref(false)
const sortBy = ref('')
const expandedRows = ref(new Set<number>())
const debouncedSearch = debounce(search, 500)

const { data: categoryData } = await getAllPostCategories()
const categories = computed<PostCategory[]>(() =>
    Array.isArray(categoryData.value?.data) ? categoryData.value.data : categoryData.value ? [categoryData.value.data] : []
)

const { data, status } = await searchPosts({ page: currentPage.value, per_page: perPage.value, status: selectedStatus.value }, AuthType.User)
const posts = computed<{ posts: Post[], meta: PaginationMeta | null, links: PaginationLinks | null }>(() => ({
    posts: Array.isArray(data.value?.data) ? data.value.data : data.value?.data ? [data.value.data] : [],
    meta: data.value?.meta ?? null,
    links: data.value?.links ?? null,
}))

async function search() {
    const filters: any = {
        page: currentPage.value,
        per_page: perPage.value,
        ...(searchQuery.value && { search: searchQuery.value }),
        ...(selectedStatus.value && { status: selectedStatus.value }),
        ...(selectedCategory.value && { category_id: Number(selectedCategory.value) }),
        ...(checkboxIsHot.value && { is_hot: 1 }),
        ...(checkboxIsFeatured.value && { is_featured: 1 }),
        ...(sortBy.value && {
            sort_by: sortBy.value.startsWith('created_') ? 'created_at' : sortBy.value,
            sort_direction: sortBy.value === 'created_desc' ? 'desc' : 'asc'
        }),
    }

    const { error } = await searchPosts(filters, AuthType.User)
    if (error.value) $toast.error('Không thể tải danh sách bài viết!')
}

const handlePageChange = (page: number) => {
    currentPage.value = page
    search()
}

async function handleDeletePost(postId: number) {
    const result = await swal.fire({
        title: 'Xác nhận xóa',
        text: 'Bạn có chắc muốn xóa bài viết này không?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Xóa',
        cancelButtonText: 'Hủy',
    })

    if (result.isConfirmed) {
        try {
            const { error } = await deletePost(postId)
            if (error.value) throw new Error(error.value.message)
            posts.value.posts = posts.value.posts.filter((post: Post) => post.id !== postId)
            expandedRows.value.delete(postId)
            $toast.success('Bài viết đã được xóa!')
        } catch (err) {
            $toast.error(`Xóa thất bại: ${(err as Error).message || 'Unknown error'}`)
        }
    }
}
</script>