<template>
    <div class="border border-gray-200 rounded-lg">
        <!-- Header -->
        <div class="flex justify-between items-center border-gray-200 px-3 pt-2">
            <h1 class="text-xl font-bold text-gray-800">Danh sách sẩn phẩm</h1>
            <button @click="$router.push('/admin/products/create')" class="btn btn-sm btn-secondary">
                <Plus class="w-3 h-3" /> Thêm sản phẩm
            </button>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-5 gap-2 px-3 py-3">
            <div class="relative">
                <Search class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 z-10 w-5 h-5" />
                <input v-model="searchQuery" type="search" placeholder="Tìm kiếm sản phẩm..."
                    class="input input-sm input-primary w-full pl-10" @input="debouncedSearch" />
            </div>
        </div>
        <div class="w-full overflow-x-auto">
            <table class="table w-full border-collapse bg-white border border-gray-200">
                <thead>
                    <tr class="bg-gray-100 text-gray-700">
                        <th class="py-2 w-[5%]"></th>
                        <th class="py-2 w-[20%] text-left">Mã</th>
                        <th class="py-2 w-[35%] text-left">Tên</th>
                        <th class="py-2 w-[15%] text-left">Giá</th>
                        <th class="py-2 w-[10%] text-left">Còn lại</th>
                        <th class="py-2 w-[15%] text-left">Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    <template v-for="product in products.products" :key="product.id">
                        <tr class="border-b border-gray-100 hover:bg-gray-200 cursor-pointer"
                            @click="toggleRow(product.id)"
                            :class="{ 'bg-gray-200 hover:bg-gray-200': expandedRows.has(product.id) }">
                            <td class="py-2">
                                <component :is="expandedRows.has(product.id) ? ChevronDown : ChevronRight"
                                    class="text-green-600" />
                            </td>
                            <td class="py-2">{{ product.product_code }}</td>
                            <td class="py-2">{{ product.name }}</td>
                            <td class="py-2">
                                {{ product.prices.length > 0 
                                    ? (product.prices[0].price_type === 'contact' 
                                        ? 'Liên hệ' 
                                        : product.prices[0].price) 
                                    : 'Chưa bán' }}
                            </td>
                            <td class="py-2">{{ product.stock_quantity }}</td>
                            <td class="py-2">
                                <span v-if="product.status === 'growing'" class="text-blue-600">Đang trồng</span>
                                <span v-else-if="product.status === 'selling'" class="text-green-600">Đang bán</span>
                                <span v-else class="text-red-600">Ngừng hoạt động</span>
                            </td>
                        </tr>
                        <tr v-if="expandedRows.has(product.id)" class="bg-gray-50">
                            <td colspan="7" class="p-3">
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2 text-sm">
                                    <div><span class="font-semibold">Mô tả:</span> <p class="text-gray-600 line-clamp-2">{{ product.description || "Chưa xác định" }}</p></div>
                                    <div><span class="font-semibold">Ngày gieo:</span> {{ new Date(product.sown_at).toLocaleDateString('vi-VN') }}</div>
                                    <div><span class="font-semibold">Người trồng:</span> {{ product.user?.full_name }}</div>
                                    <div><span class="font-semibold">Ngày thu hoạch:</span> {{ product.harvested_at ? new
                                                    Date(product.harvested_at).toLocaleString('vi-VN') : 'Chưa thu hoạch' }}</div>
                                </div>
                                <div class="flex space-x-4 mt-auto justify-end">
                                    <button @click="goToLogs(product)"
                                    class="btn btn-sm btn-neutral px-4">Nhật ký chăm sóc</button>
                                    <button @click.stop="$router.push(`/admin/products/${product.id}/publish`)"
                                    class="btn btn-sm btn-accent px-4">Mở bán</button>
                                    <button @click.stop="$router.push(`/admin/products/${product.id}`)"
                                        class="btn btn-sm btn-primary px-4">Sửa</button>
                                    <button @click.stop="handleDeleteProduct(product.id)"
                                        class="btn btn-sm btn-error px-4">Xóa</button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="flex justify-between items-center m-4">
                <div class="flex items-center space-x-2">
                    <p class="text-sm text-gray-600 w-32">{{ products.products.length }} / {{ products.meta?.total }} SP
                    </p>
                    <select v-model="perPage" class="select select-sm select-primary" @change="search">
                        <option v-for="n in [10, 25, 50, 100]" :value="n" :key="n">{{ n }}</option>
                    </select>
                </div>
                <UiPagination :links="products.links" :meta="products.meta" :show-first-last="true" :show-numbers="true"
                    @page-change="handlePageChange" />
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'user', title: 'Quản lý sản phẩm', description: 'Quản lý sản phẩm trên trang web' })

import { ChevronDown, ChevronRight, Search, Plus } from 'lucide-vue-next'
import { debounce } from 'lodash-es'
import { useSwal } from '~/composables/useSwal'
import { useToast } from 'vue-toastification'
import type { PaginationMeta, PaginationLinks } from '~/types/api'
import type { Product, ProductCategory } from '~/types/product'

const { searchProducts, deleteProduct } = useProducts()
const { getProductCategories } = useProductCategories()
const { getUnits } = useUnits()
const swal = useSwal()
const toast = useToast()
const productStore = useProductStore()
const router = useRouter()

const currentPage = ref(1)
const perPage = ref(10)
const searchQuery = ref('')
const expandedRows = ref(new Set<number>())
const debouncedSearch = debounce(search, 500)

const { data } = await searchProducts({ page: currentPage.value, per_page: perPage.value }, AuthType.User)
const products = computed(() => ({
    products: Array.isArray(data.value?.data) ? data.value.data : data.value?.data ? [data.value.data] : [],
    meta: data.value?.meta ?? null,
    links: data.value?.links ?? null,
}))

const toggleRow = (id: number) => {
    expandedRows.value.has(id) ? expandedRows.value.delete(id) : expandedRows.value.add(id)
}

async function search() {
    const filters: any = {
        page: currentPage.value,
        per_page: perPage.value,
        ...(searchQuery.value && { search: searchQuery.value }),
        // ...(selectedStatus.value && { status: selectedStatus.value }),
        // ...(selectedCategory.value && { category_id: Number(selectedCategory.value) }),
        // ...(checkboxIsHot.value && { is_hot: 1 }),
        // ...(checkboxIsFeatured.value && { is_featured: 1 }),
        // ...(sortBy.value && {
        //     sort_by: sortBy.value.startsWith('created_') ? 'created_at' : sortBy.value,
        //     sort_direction: sortBy.value === 'created_desc' ? 'desc' : 'asc'
        // }),
    }

    const { data, error } = await searchProducts(filters, AuthType.User)
    if (error.value) swal.fire('Lỗi', 'Không thể tải danh sách bài viết!', 'error')
}

const handlePageChange = (page: number) => {
    currentPage.value = page
    search()
}

async function handleDeleteProduct(postId: number) {
    const result = await swal.fire({
        title: 'Xác nhận xóa',
        text: 'Bạn có chắc muốn xóa sản phẩm này không?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Xóa',
        cancelButtonText: 'Hủy',
    })

    if (result.isConfirmed) {
        try {
            const { error } = await deleteProduct(postId)
            if (error.value) throw new Error(error.value.message)
            products.value.products = products.value.products.filter((post: Product) => post.id !== postId)
            expandedRows.value.delete(postId)
            toast.success('Sản phẩm đã được xóa!')
        } catch (err) {
            toast.error(`Xóa thất bại: ${(err as Error).message || 'Unknown error'}`)
        }
    }
}

function goToLogs(product: Product) {
  productStore.setSelectedProduct(product)
  router.push(`products/${product.id}/logs`)
}
</script>