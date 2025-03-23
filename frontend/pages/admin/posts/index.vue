<template>
    <div class="border border-gray-200 rounded-lg">
        <div class="flex justify-between items-center border-gray-200 px-3 pt-2">
            <h1 class="text-xl font-bold text-gray-800">Danh sách bài viết</h1>
            <button @click="$router.push('/admin/posts/create')" class="btn btn-sm btn-secondary">
                <Plus class="w-3 h-3" /> Thêm bài viết
            </button>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-5 gap-2 px-3 py-3">
            <div class="relative">
                <Search class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 z-10 w-5 h-5" />
                <input v-model="searchQuery" type="search" placeholder="Tìm kiếm bài viết..."
                    class="input input-sm input-primary w-full pl-10" @input="debouncedSearch" />
            </div>
            <select v-model="selectedCategory" class="select select-sm select-primary" @change="search">
                <option value="">Tất cả danh mục</option>
                <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}
                </option>
            </select>
            <select v-model="selectedStatus" class="select select-sm select-primary" @change="search">
                <option value="">Tất cả trạng thái</option>
                <option value="published">Đã xuất bản</option>
                <option value="draft">Bản nháp</option>
                <option value="archived">Lưu trữ</option>
            </select>
            <select v-model="sortBy" class="select select-sm select-primary" @change="search">
                <option value="">Sắp xếp theo</option>
                <option value="created_desc">Mới nhất</option>
                <option value="created_asc">Cũ nhất</option>
                <option value="title">Theo tên</option>
                <option value="category_id">Theo danh mục</option>
                <option value="user_id">Theo tác giả</option>
                <option value="post_status">Theo trạng thái</option>
            </select>
            <label class="lg:col-span-1 col-span-2 flex items-center justify-center gap-5">
                <div class="flex items-center">
                    <input v-model="checkboxIsHot" type="checkbox" class="toggle toggle-sm toggle-primary mr-2"
                        @change="search" /> Hot
                </div>
                <div class="flex items-center">
                    <input v-model="checkboxIsFeatured" type="checkbox" class="toggle toggle-sm toggle-primary mr-2"
                        @change="search" /> Nổi bật
                </div>
            </label>
        </div>

        <!-- Table -->
        <div class="w-full max-w-[90vw] overflow-x-auto">
            <table class="table w-full border-collapse bg-white border border-gray-200">
                <thead>
                    <tr class="bg-gray-100 text-gray-700">
                        <th class="py-2 w-[3%]"></th>
                        <th class="py-2 w-[45%] text-left">Tiêu đề</th>
                        <th class="py-2 w-[15%] text-left">Danh mục</th>
                        <th class="py-2 w-[30%] text-left">Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    <template v-for="post in posts.posts" :key="post.id">
                        <tr class="border-b border-gray-100 hover:bg-gray-200 cursor-pointer"
                            @click="toggleRow(post.id)"
                            :class="{ 'bg-gray-200 hover:bg-gray-200': expandedRows.has(post.id) }">
                            <td class="py-2">
                                <component :is="expandedRows.has(post.id) ? ChevronDown : ChevronRight"
                                    class="text-green-600" />
                            </td>
                            <td class="py-2">{{ post.title.length > 50 ? post.title.substring(0, 50) + '...' :
                                post.title }}</td>
                            <td class="py-2">{{ post.category?.name }}</td>
                            <td class="py-2" colspan="2">
                                <div class="flex gap-2">
                                    <span class="px-3 py-0.5 rounded-full text-xs" :class="{
                                        'bg-green-100 text-green-800': post.post_status === 'published',
                                        'bg-yellow-100 text-yellow-800': post.post_status === 'draft',
                                        'bg-gray-100 text-gray-800': post.post_status === 'archived'
                                    }">
                                        {{ { published: 'Công khai', draft: 'Nháp', archived: 'Lưu trữ' }[post.post_status as 'published' | 'draft' | 'archived'] }}
                                    </span>
                                    <span v-if="post.is_hot"
                                        class="px-3 py-0.5 rounded-full text-xs bg-red-100 text-red-600">Hot</span>
                                    <span v-if="post.is_featured"
                                        class="px-3 py-0.5 rounded-full text-xs bg-yellow-100 text-yellow-600">Nổi
                                        bật</span>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="expandedRows.has(post.id)" class="bg-gray-50">
                            <td colspan="7" class="p-3">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="flex justify-center">
                                        <img :src="`${backendUrl}${post.featured_image}`"
                                            @error="e => { const target = e.target as HTMLImageElement; if (target) target.src = placeholderImage }"
                                            class="w-full aspect-video object-cover rounded border border-gray-200 bg-gray-100"
                                            alt="Cover" loading="lazy" />
                                    </div>
                                    <div class="space-y-2 md:col-span-2 flex flex-col justify-between">
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
                                                    <p class="text-gray-600">{{ post.published_at ? new
                                                        Date(post.published_at).toLocaleString('vi-VN') : 'Chưa xuất bản' }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex space-x-4 mt-2">
                                            <button @click.stop="handleDeletePost(post.id)"
                                                class="btn btn-sm btn-error">Xóa</button>
                                            <button @click.stop="$router.push(`/admin/posts/${post.id}`)"
                                                class="btn btn-sm btn-primary">Sửa</button>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="flex justify-between items-center m-4">
                <div class="flex items-center space-x-2">
                    <p class="text-sm text-gray-600">{{ posts.posts.length }} / {{ posts.meta?.total }} bài viết</p>
                    <select v-model="perPage" class="select select-sm select-primary" @change="search">
                        <option v-for="n in [10, 25, 50, 100]" :value="n" :key="n">{{ n }}</option>
                    </select>
                </div>
                <UiPagination :links="posts.links" :meta="posts.meta" :show-first-last="true" :show-numbers="true"
                    @page-change="handlePageChange" />
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'user', title: 'Bài viết', description: 'Quản lý bài viết trên trang web' })

import { ChevronDown, ChevronRight, Search, Plus } from 'lucide-vue-next'
import { usePosts, usePostCategories } from '#imports'
import { useRuntimeConfig } from '#app'
import { debounce } from 'lodash-es'
import { useSwal } from '~/composables/useSwal'
import { useToast } from 'vue-toastification'
import type { PaginationMeta, PaginationLinks } from '~/types/api'
import type { Post, PostCategory } from '~/types/post'

const { public: { backendUrl, placeholderImage } } = useRuntimeConfig()
const { searchPosts, deletePost } = usePosts()
const { getAllPostCategories } = usePostCategories()
const swal = useSwal()
const toast = useToast()

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

const { data } = await searchPosts({ page: currentPage.value, per_page: perPage.value, status: selectedStatus.value }, AuthType.User)
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

    const { data, error } = await searchPosts(filters, AuthType.User)
    if (error.value) swal.fire('Lỗi', 'Không thể tải danh sách bài viết!', 'error')
}

const handlePageChange = (page: number) => {
    currentPage.value = page
    search()
}

const toggleRow = (id: number) => {
    expandedRows.value.has(id) ? expandedRows.value.delete(id) : expandedRows.value.add(id)
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
            toast.success('Bài viết đã được xóa!')
        } catch (err) {
            toast.error(`Xóa thất bại: ${(err as Error).message || 'Unknown error'}`)
        }
    }
}
</script>