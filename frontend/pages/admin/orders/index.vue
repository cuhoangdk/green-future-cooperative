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
            <select v-model="sortBy" class="select select-sm select-primary" @change="search">
                <option value="">Sắp xếp theo</option>
                <option value="sown_at+desc">Mới nhất</option>
                <option value="sown_at+asc">Cũ nhất</option>
                <option value="stock_quantity+desc">Tồn nhiều nhất</option>
                <option value="stock_quantity+asc">Tồn ít nhất</option>
                <option value="name">Theo tên</option>
            </select>
        </div>
        <div class="w-full max-w-[90vw] overflow-x-auto">
            <table class="table w-full border-collapse bg-white border border-gray-200">
                <thead>
                    <tr class="bg-gray-100 text-gray-700">
                        <th class="py-2 w-[5%]"></th>
                        <th class="py-2 w-[20%] text-left">Mã</th>
                        <th class="py-2 w-[25%] text-left">Tên người mua</th>
                        <th class="py-2 w-[35%] text-left">SĐT</th>
                        <th class="py-2 w-[15%] text-left">Tổng tiền</th>
                        <th class="py-2 w-[15%] text-left">Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    <template v-for="order in orders.orders" :key="order.id">
                        <tr class="border-b border-gray-100 hover:bg-gray-200 cursor-pointer"
                            @click="toggleRow(order.id)"
                            :class="{ 'bg-gray-200 hover:bg-gray-200': expandedRows.has(order.id) }">
                            <td class="py-2">
                                <component :is="expandedRows.has(order.id) ? ChevronDown : ChevronRight"
                                    class="text-green-600" />
                            </td>
                            <td class="py-2">{{ order.order_code }}</td>
                            <td class="py-2">{{ order.full_name }}</td>
                            <td class="py-2">{{ order.phone_number }}</td>
                            <td class="py-2">{{ order.final_total_amount }}</td>
                            <td class="py-2">{{ order.status }}</td>
                        </tr>
                        <tr v-if="expandedRows.has(order.id)" class="bg-gray-50">
                            <td colspan="7" class="p-3">
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2 text-sm">
                                    <div class="col-span-2"><span class="font-semibold">Ghi chú:</span>
                                        <p class="text-gray-600 line-clamp-2">{{ order.notes }}</p>
                                    </div>
                                    <div class="col-span-2"><span class="font-semibold">Địa chỉ:
                                    </span>{{ order.street_address }},
                                            {{ addressData[order.id] }}
                                    </div>
                                    <div><span class="font-semibold">Ngày tạo:</span> {{
                                        new Date(order.created_at).toLocaleString('vi-VN') }}</div>
                                </div>
                                <div class="flex space-x-3 mt-2">
                                    <!-- <button @click.stop="handleDeleteProduct(product.id)"
                                    class="btn btn-sm btn-error px-4">Xóa</button>
                                    <button @click.stop="$router.push(`/admin/products/${product.id}/qrcode`)" class="btn btn-sm btn-info px-4">QR Code</button>
                                    <button @click.stop="$router.push(`/admin/products/${product.id}/publish`)"
                                        v-if="product.status === 'growing'" class="btn btn-sm btn-accent px-4">Mở
                                        bán</button>
                                    <button @click.stop="$router.push(`/admin/products/${product.id}`)"
                                        class="btn btn-sm btn-primary px-4">Sửa</button>
                                        <button @click="goToLogs(product)" class="btn btn-sm btn-neutral px-4">Nhật ký chăm
                                            sóc</button> -->
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="flex justify-between items-center gap-2 m-4">
                <div class="flex items-center space-x-2">
                    <p class="text-sm text-gray-600 w-32">{{ orders.orders.length }} / {{ orders.meta?.total }} SP
                    </p>
                    <select v-model="perPage" class="select select-sm select-primary" @change="search">
                        <option v-for="n in [10, 25, 50, 100]" :value="n" :key="n">{{ n }}</option>
                    </select>
                </div>
                <UiPagination :links="orders.links" :meta="orders.meta" :show-first-last="true" :show-numbers="true"
                    @page-change="handlePageChange" />
            </div>
            <pre>{{ orders }}</pre>
        </div>
    </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'user', title: 'Sản phẩm', description: 'Quản lý sản phẩm trên trang web' })

import { ChevronDown, ChevronRight, Search, Plus } from 'lucide-vue-next'
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

const { data } = await getAdminOrders(currentPage.value, perPage.value)
const orders = computed<{ orders: Order[], meta: PaginationMeta | null, links: PaginationLinks | null }>(() => ({
    orders: Array.isArray(data.value?.data) ? data.value.data : data.value?.data ? [data.value.data] : [],
    meta: data.value?.meta ?? null,
    links: data.value?.links ?? null,
}))

const addressData = ref<{ [key: number]: string }>({})
watch(() => orders.value.orders, async (newData) => {
    for (const order of newData) {
        if (order.ward) {
            addressData.value[order.id] = await getFullAddressName(order.ward)
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

async function handleDeleteProduct(productId: number) {
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