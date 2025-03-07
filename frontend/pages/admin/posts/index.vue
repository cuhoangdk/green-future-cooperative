<template>
    <div class="border border-gray-200 rounded-lg">
        <!-- Header Section -->
        <div class="flex justify-between items-center border-gray-200 px-3 pt-2">
            <h1 class="text-xl font-bold text-gray-800">Danh sách bài viết</h1>
            <button @click="$router.push('/admin/posts/create')" class="btn btn-sm btn-secondary">
                <Plus class="w-3 h-3" />
                Thêm bài viết
            </button>
        </div>
        <!-- Filters Section -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-2 px-3 py-3 ">
            <div class="relative">
                <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-5 h-5" />
                <input v-model="searchQuery" type="search" placeholder="Tìm kiếm bài viết..."
                    class="input input-sm input-primary w-full pl-10" @input="debouncedSearch" />
            </div>
            <select v-model="selectedCategory" class="select select-sm select-primary" @change="search">
                <option value="">Tất cả danh mục</option>
                <option v-for="category in categories" :key="category.id" :value="category.id">
                    {{ category.name }}
                </option>
            </select>
            <select v-model="selectedStatus" class="select select-sm select-primary" @change="search">
                <option value="">Tất cả trạng thái</option>
                <option value="published">Đã xuất bản</option>
                <option value="draft">Bản nháp</option>
                <option value="archived">Lưu trữ</option>
            </select>
            <!-- <select v-model="selectedAuthor" class="select select-primary" @change="search">
                <option value="">Tất cả tác giả</option>
            </select> -->
            <select v-model="sortBy" class="select select-sm select-primary" @change="search">
                <option value="">Sắp xếp theo</option>
                <option value="created_desc">Mới nhất</option>
                <option value="created_asc">Cũ nhất</option>
                <option value="title">Theo tên</option>
                <option value="category_id">Theo danh mục</option>
                <option value="user_id">Theo tác giả</option>
                <option value="post_status">Theo trạng thái</option>
            </select>
            <label class="lg:col-span-1 col-span-2 flex items-center justify-center gap-x-5">
                <div class="flex items-center">
                    <input v-model="checkboxIsHot" type="checkbox" class="toggle toggle-sm toggle-primary mr-2"
                        @change="search" /> Hot
                </div>
                <div class="flex items-center">
                    <input v-model="checkboxIsFeatured" type="checkbox" class="toggle toggle-sm  toggle-primary mr-2"
                        @change="search" /> Nổi bật
                </div>
            </label>
        </div>
        <!-- Table Section -->
        <div class="w-full overflow-x-auto mb-3">
            <table class="table w-full border-collapse bg-white border border-gray-200">
                <thead>
                    <tr class="bg-gray-100 text-gray-700">
                        <th class="p-2 text-left" style="width: 3%;"></th>
                        <th class="p-2 text-left" style="width: 45%;">Tiêu đề</th>
                        <th class="p-2 text-left" style="width: 15%;">Danh mục</th>
                        <th class="p-2 text-left" style="width: 20%;">Đặc biệt</th>
                        <th class="p-2 text-left" style="width: 10%;">Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    <template v-for="post in posts.posts">
                        <tr class="list1-item border-b border-gray-100 hover:bg-gray-200 cursor-pointer"
                            @click="toggleRow(post.id)"
                            :class="{ 'bg-gray-200 hover:bg-gray-200': expandedRows.has(post.id) }">
                            <td class="py-2">
                                <component :is="expandedRows.has(post.id) ? ChevronDown : ChevronRight"
                                    class=" text-green-600" />
                            </td>
                            <td class="py-2">{{ post.title.length > 50 ? post.title.substring(0, 50) + '...' :
                                post.title
                                }}</td>
                            <td class="py-2">{{ post.category?.name }}</td>
                            <td class="py-2 text-left">
                                <span v-if="post.is_hot"
                                    class="px-3 py-0.5 rounded-full text-xs bg-red-100 text-red-600">Hot</span>
                                <span v-if="post.is_featured"
                                    class="px-3 py-0.5 rounded-full text-xs bg-yellow-100 text-yellow-600">Nổi
                                    bật</span>
                            </td>
                            <td class="py-2">
                                <span class="px-3 py-0.5 rounded-full text-xs" :class="post.post_status === 'published'
                                    ? 'bg-green-100 text-green-800'
                                    : post.post_status === 'draft'
                                        ? 'bg-yellow-100 text-yellow-800'
                                        : 'bg-gray-100 text-gray-800'">
                                    {{ post.post_status === 'published' ? 'Công khai' : post.post_status === 'draft' ?
                                    'Nháp' : 'Lưu trữ' }}
                                </span>
                            </td>
                        </tr>
                        <tr v-if="expandedRows.has(post.id)" class="list1-item bg-gray-50">
                            <td colspan="7" class="p-3">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="flex justify-center">
                                        <img :src="`${backendUrl}${post.featured_image}`"
                                            @error="event => { const target = event.target as HTMLImageElement; if (target) target.src = placeholderImage; }"
                                            class="w-full aspect-video object-cover rounded border border-gray-200 bg-gray-100"
                                            alt="Cover" loading="lazy" />
                                    </div>
                                    <div class="space-y-2 md:col-span-2 h-full flex flex-col justify-between">
                                        <div>
                                            <p class="font-semibold">Tóm tắt:</p>
                                            <p class="text-gray-600 line-clamp-4">{{ post.summary }}</p>
                                            <div class="mt-4 flex justify-between gap-4">
                                                <div class="w-1/2">
                                                    <p class="font-semibold">Ngày tạo:</p>
                                                    <p class="text-gray-600 mb-2">{{ new
                                                        Date(post.created_at).toLocaleString('vi-VN') }}</p>
                                                    <p class="font-semibold mt-2">Tác giả:</p>
                                                    <p>{{ post.user?.full_name }}</p>
                                                </div>
                                                <div class="w-1/2">
                                                    <p class="font-semibold">Ngày xuất bản:</p>
                                                    <p class="text-gray-600">
                                                        {{ post.published_at ? new Date(post.published_at).toLocaleString('vi-VN') : "Chưa xuất bản" }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex space-x-4 mt-auto">
                                            <button @click.stop="$router.push({ path: `/admin/posts/${post.id}` })"
                                                class="btn btn-sm btn-primary">Sửa</button>
                                            <button @click.stop="handleDeletePost(post.id)"
                                                class="btn btn-sm btn-error">Xóa</button>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
            <div class="flex justify-end mr-4">
                <UiPagination :links="posts.links" :meta="posts.meta" :show-first-last="true" :show-numbers="true"
                    @page-change="handlePageChange" />
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
definePageMeta({
    layout: 'user',
    title: 'Quản lý bài viết',
    description: 'Quản lý bài viết trên trang web',
})

import { ChevronDown, ChevronRight, Search, Plus } from 'lucide-vue-next'
import { usePosts, usePostCategories } from '#imports'
import { useRuntimeConfig } from '#app'
import { debounce } from 'lodash-es'
import { useSwal } from '~/composables/useSwal'
import { useToast } from 'vue-toastification'
import type { Post } from '~/types/post'
import type { PostCategory } from '~/types/post'
import type { PaginationMeta, PaginationLinks } from '~/types/api'

const route = useRoute()
const swal = useSwal()
const toast = useToast()
const { public: { backendUrl, placeholderImage } } = useRuntimeConfig()
const { searchPosts, deletePost } = usePosts()
const { getAllPostCategories } = usePostCategories()

const currentPage = ref(Number(route.query.page) || 1)
const perPage = 10
const searchQuery = ref('')
const selectedStatus = ref('')
const selectedCategory = ref('')
const selectedAuthor = ref('')
const checkboxIsHot = ref(false)
const checkboxIsFeatured = ref(false)
const sortBy = ref('')

// Lấy danh mục bài viết
const { data: categoryData, error: categoryError } = await getAllPostCategories()
const categories = computed<PostCategory[]>(() =>
    Array.isArray(categoryData.value?.data) ? categoryData.value.data : categoryData.value ? [categoryData.value.data] : []
)

// Hàm tìm kiếm với debounce
const debouncedSearch = debounce(() => {
    // currentPage.value = 1 // Reset về trang 1 khi tìm kiếm
    search()
}, 500) // Đợi 500ms trước khi gửi yêu cầu tìm kiếm

// Hàm tìm kiếm chính
const search = async () => {
    const filters: {
        page?: number,
        per_page?: number,
        search?: string,
        sort_by?: string,
        sort_direction?: string,
        status?: string,
        category_id?: number,
        user_id?: number,
        is_hot?: number,
        is_featured?: number,
    } = {
        page: currentPage.value,
        per_page: perPage,
    }

    if (searchQuery.value) filters.search = searchQuery.value
    if (selectedStatus.value !== '') filters.status = selectedStatus.value
    if (selectedCategory.value !== '') filters.category_id = Number(selectedCategory.value) // Giả sử category_id là số
    if (selectedAuthor.value !== '') filters.user_id = Number(selectedAuthor.value) // Giả sử user_id là số
    if (checkboxIsHot.value) filters.is_hot = 1
    if (checkboxIsFeatured.value) filters.is_featured = 1
    if (sortBy.value == 'created_desc') {
        filters.sort_by = 'created_at'
        filters.sort_direction = 'desc'
    } else if (sortBy.value == 'created_asc') {
        filters.sort_by = 'created_at'
        filters.sort_direction = 'asc'
    } else if (sortBy.value !== '') {
        filters.sort_by = sortBy.value
        filters.sort_direction = 'asc'
    }


    currentPage.value = 1 // Reset về trang 1 khi tìm kiếm

    const { data, status, error } = await searchPosts(filters, AuthType.User)

    if (error.value) {
        swal.fire('Lỗi', 'Không thể tải danh sách bài viết!', 'error')
    } else {
        posts.value = {
            posts: Array.isArray(data.value?.data) ? data.value.data : data.value?.data ? [data.value.data] : [],
            meta: data.value?.meta ?? null,
            links: data.value?.links ?? null,
        }
        // Cập nhật URL với các bộ lọc
        // router.push({ path: '/admin/posts', query: { page: String(currentPage.value), search: searchQuery.value || undefined, status: selectedStatus.value !== 'all' ? selectedStatus.value : undefined } })
    }
}

// Lấy danh sách bài viết ban đầu
const { data, status, error: initialError, refresh } = await searchPosts({ page: currentPage.value, per_page: perPage, status: selectedStatus.value }, AuthType.User)

const posts = ref<{ posts: Post[]; meta: PaginationMeta | null; links: PaginationLinks | null }>({
    posts: Array.isArray(data.value?.data) ? data.value.data : data.value?.data ? [data.value.data] : [],
    meta: data.value?.meta ?? null,
    links: data.value?.links ?? null,
})

watch(data, (newData) => {
    posts.value = {
        posts: Array.isArray(newData?.data) ? newData.data : newData?.data ? [newData.data] : [],
        meta: newData?.meta ?? null,
        links: newData?.links ?? null,
    }
})

const handlePageChange = async (page: number) => {
    currentPage.value = page
    await search() // Gọi tìm kiếm với trang mới
}

const expandedRows = ref(new Set<number>())

const toggleRow = (id: number) => {
    const newSet = new Set(expandedRows.value)
    newSet.has(id) ? newSet.delete(id) : newSet.add(id)
    expandedRows.value = newSet
}

const handleDeletePost = async (postId: number) => {
    const result = await swal.fire
        ({
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
            if (error.value) throw new Error(error.value.message || 'Failed to delete post')
            posts.value.posts = posts.value.posts.filter(post => post.id !== postId)
            expandedRows.value.delete(postId)
            toast.success('Bài viết đã được xóa!')
        } catch (err) {
            toast.error(`Xóa thất bại: ${(err as Error).message || 'Unknown error'}`)
        }
    }
}
</script>