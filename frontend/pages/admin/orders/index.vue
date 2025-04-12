<template>
    <div>
        <div class="flex flex-col md:flex-row justify-between items-start gap-3 border-gray-200 px-3 py-4">
            <div class="flex flex-col w-full md:flex-row gap-2">
                <div class="relative w-full md:w-auto">
                    <Search class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 z-10 w-5 h-5" />
                    <input v-model="searchQuery" type="search" placeholder="Tìm kiếm đơn hàng..."
                        class="input input-sm input-primary w-full pl-10" @input="debouncedSearch" />
                </div>
            </div>
            <NuxtLink to="orders/create" class="btn btn-sm btn-primary w-full md:w-auto">
                <Plus class="w-5 h-5" /> Thêm
            </NuxtLink>
        </div>
        <div class="relative">
            <!-- Loading Overlay specific to table/grid -->
            <div v-if="status === 'pending'"
                class="absolute inset-0 bg-gray-50 opacity-25 flex justify-center items-center z-10">
                <span class="loading loading-spinner loading-lg"></span>
            </div>
            <TableOrder :orders="orders.orders" :addressData="addressData" @delete="handleDelete" :display-edit-button="true" />

            <GridOrder :orders="orders.orders" :addressData="addressData" @delete="handleDelete" :display-edit-button="true"/>
            
        </div>
        <div class="flex flex-col sm:flex-row justify-between items-center m-4 gap-2">
            <div class="flex items-center space-x-2">
                <p class="text-sm text-gray-600">{{ orders.orders.length }} / {{ orders.meta?.total }}</p>
                <select v-model="perPage" class="select select-sm select-primary w-18" @change="search">
                    <option v-for="n in [10, 25, 50, 100]" :value="n" :key="n">{{ n }}</option>
                </select>
            </div>
            <UiPagination :links="orders.links" :meta="orders.meta" :show-first-last="true" :show-numbers="true"
                @page-change="handlePageChange" />
        </div>
    </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'user', title: 'Đơn hàng', description: 'Quản lý đơn hàng' })

import { Search, Plus } from 'lucide-vue-next'
import { debounce } from 'lodash-es'
import { useSwal } from '~/composables/useSwal'
import { useToast } from 'vue-toastification'
import type { PaginationMeta, PaginationLinks } from '~/types/api'
import type { Product } from '~/types/product'
import type { Order } from '~/types/order'

const { getAdminOrders, cancelAdminOrder } = useAdminOrder()
const { getProductCategories } = useProductCategories()
const { getUsers } = useUsers()
const { getFullAddressName } = useVietnamAddress()
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

const { data, status } = await getAdminOrders(currentPage.value, perPage.value)
const orders = computed<{ orders: Order[], meta: PaginationMeta | null, links: PaginationLinks | null }>(() => ({
    orders: Array.isArray(data.value?.data) ? data.value.data : data.value?.data ? [data.value.data] : [],
    meta: data.value?.meta ?? null,
    links: data.value?.links ?? null,
}))

const addressData = ref<{ [key: number]: string }>({})
watch(() => orders.value.orders, async (newData) => {
    for (const order of newData) {
        if (order.ward) {
            addressData.value[order.id] = `${order.street_address}, ${await getFullAddressName(order.ward)}`
        }
    }
}, { immediate: true })

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

    const { data, error } = await getAdminOrders(filters)
    if (error.value) swal.fire('Lỗi', 'Không thể tải danh sách sản phẩm!', 'error')
}

const handlePageChange = (page: number) => {
    currentPage.value = page
    search()
}

async function handleDelete(productId: number) {
    const result = await swal.fire({
        title: 'Xác nhận xóa',
        text: 'Bạn có chắc muốn xóa sản phẩm này không?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Xóa',
        cancelButtonText: 'Hủy',
    })

    // if (result.isConfirmed) {
    //     try {
    //         const { error } = await cancelAdminOrder(productId)
    //         if (error.value) throw new Error(error.value.message)
    //         orders.value.orders = orders.value.orders.filter((product: Product) => product.id !== productId)
    //         expandedRows.value.delete(productId)
    //         toast.success('Sản phẩm đã được xóa!')
    //     } catch (err) {
    //         toast.error(`Xóa thất bại: ${(err as Error).message || 'Unknown error'}`)
    //     }
    // }
}

function goToLogs(product: Product) {
    productStore.setSelectedProduct(product)
    router.push(`products/${product.id}/logs`)
}
</script>