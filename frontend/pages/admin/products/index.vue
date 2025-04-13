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
                        <option value="growing">Đang trồng</option>
                        <option value="selling">Đang bán</option>
                        <option value="stopped">Ngừng hoạt động</option>
                    </select>
                    <select v-model="selectedOwner" class="select select-sm select-primary w-full sm:w-[150px]"
                        @change="search">
                        <option value="">Tất cả chủ sở hữu</option>
                        <option v-for="owner in owners" :key="owner.id" :value="owner.id">{{ owner.full_name }}</option>
                    </select>
                    <select v-model="sortBy" class="select select-sm select-primary w-full sm:w-[150px]"
                        @change="search">
                        <option value="">Sắp xếp theo</option>
                        <option value="sown_at+desc">Mới nhất</option>
                        <option value="sown_at+asc">Cũ nhất</option>
                        <option value="stock_quantity+desc">Tồn nhiều nhất</option>
                        <option value="stock_quantity+asc">Tồn ít nhất</option>
                        <option value="name">Theo tên</option>
                    </select>
                </div>
            </div>
            <NuxtLink to="products/create" class="btn btn-sm btn-primary w-full md:w-auto">
                <Plus class="w-5 h-5" /> Thêm
            </NuxtLink>
        </div>
        <div class="relative">
            <!-- Loading Overlay specific to table/grid -->
            <div v-if="status === 'pending'"
                class="absolute inset-0 bg-gray-50 opacity-25 flex justify-center items-center z-10">
                <span class="loading loading-spinner loading-lg"></span>
            </div>
        <TableProduct :products="products.products" v-on:delete="handleDeleteProduct" />
        <GridProduct :products="products.products" v-on:delete="handleDeleteProduct" />
        </div>
        <div class="flex flex-col lg:flex-row justify-between items-center gap-2 m-4">
            <div class="flex items-center space-x-2">
                <p class="text-sm text-gray-600">{{ products.products.length }} / {{ products.meta?.total }}
                </p>
                <select v-model="perPage" class="select select-sm select-primary w-18" @change="search">
                    <option v-for="n in [10, 25, 50, 100]" :value="n" :key="n">{{ n }}</option>
                </select>
            </div>
            <UiPagination :links="products.links" :meta="products.meta" :show-first-last="true" :show-numbers="true"
                @page-change="handlePageChange" />
        </div>
    </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'user', title: 'Sản phẩm', description: 'Quản lý sản phẩm trên trang web' })

import { Search, Plus } from 'lucide-vue-next'
import { debounce } from 'lodash-es'
import { useSwal } from '~/composables/useSwal'
import { useToast } from 'vue-toastification'
import type { PaginationMeta, PaginationLinks } from '~/types/api'
import type { Product, ProductCategory } from '~/types/product'
import type { User } from '~/types/user'

const { searchProducts, deleteProduct } = useProducts()
const { getProductCategories } = useProductCategories()
const { getUsers } = useUsers()
const swal = useSwal()
const toast = useToast()
const productStore = useProductStore()
const router = useRouter()

const currentPage = ref(Number(useRoute().query.page) || 1)
const perPage = ref(10)
const searchQuery = ref('')
const selectedStatus = ref('')
const selectedCategory = ref('')
const selectedOwner = ref('')
const sortBy = ref('')
const expandedRows = ref(new Set<number>())
const debouncedSearch = debounce(search, 500)

// Lấy danh sách danh mục
const { data: categoryData } = await getProductCategories()
const categories = computed<ProductCategory[]>(() =>
    Array.isArray(categoryData.value?.data) ? categoryData.value.data : categoryData.value?.data ? [categoryData.value.data] : []
)

// Lấy danh sách người dùng
const { data: userData } = await getUsers()
const owners = computed<User[]>(() =>
    Array.isArray(userData.value?.data) ? userData.value.data : userData.value?.data ? [userData.value.data] : []
)

const { data, status } = await searchProducts({ page: currentPage.value, per_page: perPage.value }, AuthType.User)
const products = computed<{ products: Product[], meta: PaginationMeta | null, links: PaginationLinks | null }>(() => ({
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
        ...(selectedStatus.value && { status: selectedStatus.value }),
        ...(selectedCategory.value && { category_id: Number(selectedCategory.value) }),
        ...(selectedOwner.value && { user_id: Number(selectedOwner.value) }),
        ...(sortBy.value && {
            sort_by: sortBy.value.includes('+') ? sortBy.value.split('+')[0] : sortBy.value,
            sort_direction: sortBy.value.endsWith('+desc') ? 'desc' : 'asc'
        }),
    }

    const { error } = await searchProducts(filters, AuthType.User)
    if (error.value) swal.fire('Lỗi', 'Không thể tải danh sách sản phẩm!', 'error')
}

const handlePageChange = (page: number) => {
    currentPage.value = page
    search()
}

async function handleDeleteProduct(productId: number) {
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
            const { error } = await deleteProduct(productId)
            if (error.value) throw new Error(error.value.message)
            products.value.products = products.value.products.filter((product: Product) => product.id !== productId)
            expandedRows.value.delete(productId)
            toast.success('Sản phẩm đã được xóa!')
        } catch (err) {
            toast.error(`Xóa thất bại: ${(err as Error).message || 'Unknown error'}`)
        }
    }
}
</script>