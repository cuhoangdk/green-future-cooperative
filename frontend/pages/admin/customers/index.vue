<template>
    <div class="border border-gray-200 rounded-lg">
        <!-- Header -->
        <div class="flex justify-between items-center border-gray-200 px-3 pt-2">
            <h1 class="text-xl font-bold text-gray-800">Danh sách khách hàng</h1>
            <button @click="$router.push('/admin/customers/create')" class="btn btn-sm btn-secondary">
                <Plus class="w-3 h-3" /> Thêm khách hàng
            </button>
        </div>

        <!-- Filters -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-2 px-3 py-3">
            <div class="relative">
                <Search class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 z-10 w-5 h-5" />
                <input v-model="searchQuery" type="search" placeholder="Tìm kiếm khách hàng..."
                    class="input input-sm input-primary w-full pl-10" @input="debouncedSearch" />
            </div>
            <select v-model="selectedStatus" class="select select-sm select-primary" @change="search">
                <option value="">Tất cả trạng thái</option>
                <option value="active">Hoạt động</option>
                <option value="inactive">Không hoạt động</option>
            </select>
            <select v-model="sortBy" class="select select-sm select-primary" @change="search">
                <option value="">Sắp xếp theo</option>
                <option value="created_at+desc">Mới nhất</option>
                <option value="created_at+asc">Cũ nhất</option>
                <option value="full_name">Theo tên</option>
                <option value="email">Theo email</option>
            </select>
        </div>

        <!-- Table -->
        <div class="w-full max-w-[90vw] overflow-x-auto">
            <table class="table w-full border-collapse bg-white border border-gray-200">
                <thead>
                    <tr class="bg-gray-100 text-gray-700">
                        <th class="py-2 w-[5%]"></th>
                        <th class="py-2 w-[25%] text-left">Họ tên</th>
                        <th class="py-2 w-[25%] text-left">Email</th>
                        <th class="py-2 w-[15%] text-left">Số điện thoại</th>
                        <th class="py-2 w-[15%] text-left">Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    <template v-for="customer in customers.customers" :key="customer.id">
                        <tr class="border-b border-gray-100 hover:bg-gray-200 cursor-pointer"
                            @click="toggleRow(customer.id)"
                            :class="{ 'bg-gray-200 hover:bg-gray-200': expandedRows.has(customer.id) }">
                            <td class="py-2">
                                <component :is="expandedRows.has(customer.id) ? ChevronDown : ChevronRight"
                                    class="text-green-600" />
                            </td>
                            <td class="py-2">{{ customer.full_name }}</td>
                            <td class="py-2">{{ customer.email }}</td>
                            <td class="py-2">{{ customer.phone_number }}</td>
                            <td class="py-2">
                                <span class="px-2 py-1 rounded-full text-xs" :class="{
                                    'bg-green-100 text-green-800': !customer.is_banned,
                                    'bg-red-100 text-red-800': customer.is_banned
                                }">
                                    {{ !customer.is_banned ? 'Hoạt động' : 'Đã bị cấm' }}
                                </span>
                            </td>
                        </tr>
                        <tr v-if="expandedRows.has(customer.id)" class="bg-gray-50">
                            <td colspan="7" class="p-3">
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 text-sm">
                                    <div>
                                        <p class="font-semibold">Địa chỉ:</p>   
                                        <p class="text-gray-600">{{ addressData[customer.id] ? addressData[customer.id] : (customer.addresses?.[0]?.address?.street_address || 'Chưa cập nhật') }}</p>
                                    </div>
                                    <div>
                                        <p class="font-semibold">Ngày tham gia:</p>
                                        <p class="text-gray-600">{{ customer.created_at ? new Date(customer.created_at).toLocaleString('vi-VN') : 'Chưa cập nhật' }}</p>
                                    </div>
                                    <div>
                                        <p class="font-semibold">Cập nhật lúc:</p>
                                        <p class="text-gray-600">{{ customer.updated_at ? new Date(customer.updated_at).toLocaleString('vi-VN') : 'Chưa cập nhật' }}</p>
                                    </div>
                                </div>
                                <div class="flex space-x-3 mt-4">
                                    <button @click.stop="handleDeleteCustomer(customer.id)"
                                        class="btn btn-sm btn-error px-4">Xóa</button>
                                    <button @click.stop="$router.push(`/admin/customers/${customer.id}/edit`)"
                                        class="btn btn-sm btn-primary px-4">Sửa</button>
                                    <button @click.stop="handleToggleStatus(customer)"
                                        class="btn btn-sm" :class="customer.is_banned ? 'btn-success' : 'btn-warning'">
                                        {{ customer.is_banned ? 'Mở khóa tài khoản' : 'Khóa tài khoản' }}
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="flex justify-between items-center gap-2 m-4">
                <div class="flex items-center space-x-2">
                    <p class="text-sm text-gray-600 w-32">{{ customers.customers.length }} / {{ customers.meta?.total }} khách hàng</p>
                    <select v-model="perPage" class="select select-sm select-primary" @change="search">
                        <option v-for="n in [10, 25, 50, 100]" :value="n" :key="n">{{ n }}</option>
                    </select>
                </div>
                <UiPagination :links="customers.links" :meta="customers.meta" :show-first-last="true" :show-numbers="true"
                    @page-change="handlePageChange" />
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'user', title: 'Khách hàng', description: 'Quản lý khách hàng trên trang web' })

import { ChevronDown, ChevronRight, Search, Plus } from 'lucide-vue-next'
import { debounce } from 'lodash-es'
import { useSwal } from '~/composables/useSwal'
import { useToast } from 'vue-toastification'
import type { PaginationMeta, PaginationLinks } from '~/types/api'
import type { Customer } from '~/types/customer'

const { searchCustomers, deleteCustomer, updateCustomer } = useCustomer()
const { getFullAddressName } = useVietnamAddress()
const swal = useSwal()
const toast = useToast()
const router = useRouter()

const currentPage = ref(Number(useRoute().query.page) || 1)
const perPage = ref(10)
const searchQuery = ref('')
const selectedStatus = ref('')
const sortBy = ref('')
const expandedRows = ref(new Set<number>())
const debouncedSearch = debounce(search, 500)

const { data } = await searchCustomers({ page: currentPage.value, per_page: perPage.value })
const customers = computed<{ customers: Customer[], meta: PaginationMeta | null, links: PaginationLinks | null }>(() => ({
    customers: Array.isArray(data.value?.data) ? data.value.data : data.value?.data ? [data.value.data] : [],
    meta: data.value?.meta ?? null,
    links: data.value?.links ?? null,
}))

const addressData = ref<{[key: number]: string}>({})
watch(() => customers.value.customers, async (newCustomers) => {
    for (const customer of newCustomers) {
        if (customer.addresses?.[0]?.address?.ward) {
            addressData.value[customer.id] = await getFullAddressName(customer.addresses?.[0]?.address?.ward)
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
        ...(sortBy.value && {
            sort_by: sortBy.value.includes('+') ? sortBy.value.split('+')[0] : sortBy.value,
            sort_direction: sortBy.value.endsWith('+desc') ? 'desc' : 'asc'
        }),
    }

    const { data, error } = await searchCustomers(filters)
    if (error.value) swal.fire('Lỗi', 'Không thể tải danh sách khách hàng!', 'error')
}

const handlePageChange = (page: number) => {
    currentPage.value = page
    search()
}

async function handleDeleteCustomer(customerId: number) {
    const result = await swal.fire({
        title: 'Xác nhận xóa',
        text: 'Bạn có chắc muốn xóa khách hàng này không?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Xóa',
        cancelButtonText: 'Hủy',
    })

    if (result.isConfirmed) {
        try {
            const { error } = await deleteCustomer(customerId)
            if (error.value) throw new Error(error.value.message)
            customers.value.customers = customers.value.customers.filter((customer: Customer) => customer.id !== customerId)
            expandedRows.value.delete(customerId)
            toast.success('Khách hàng đã được xóa!')
        } catch (err) {
            toast.error(`Xóa thất bại: ${(err as Error).message || 'Unknown error'}`)
        }
    }
}

async function handleToggleStatus(customer: Customer) {
    try {
        const newBanStatus = !customer.is_banned
        const { error } = await updateCustomer(customer.id, { is_banned: newBanStatus })
        if (error.value) throw new Error(error.value.message)
        
        customer.is_banned = newBanStatus
        toast.success(`Đã ${newBanStatus ? 'khóa' : 'mở khóa'} tài khoản thành công!`)
    } catch (err) {
        toast.error(`Thao tác thất bại: ${(err as Error).message || 'Unknown error'}`)
    }
}
</script>