<template>
    <div>
        <div class="flex flex-col md:flex-row justify-between items-start gap-3 border-gray-200 px-3 py-4">
            <div class="flex flex-col w-full md:flex-row gap-2">
                <div class="relative w-full md:w-auto">
                    <Search class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 z-10 w-5 h-5" />
                    <input v-model="searchQuery" type="search" placeholder="Tìm kiếm đơn hàng..."
                        class="input input-sm input-primary w-full pl-10" @input="debouncedSearch" />
                </div>
                <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto">
                    <select v-model="sortBy" class="select select-sm select-primary w-full sm:w-[150px]"
                        @change="search">
                        <option value="">Sắp xếp theo</option>
                        <option value="id">ID</option>
                        <option value="status">Trạng thái</option>
                        <option value="final_total_amount+desc">Giá trị cao nhất</option>
                        <option value="final_total_amount+asc">Giá trị thấp nhất</option>
                        <option value="created_at+desc">Mới nhất</option>
                        <option value="created_at+asc">Cũ nhất</option>
                    </select>

                    <select v-model="selectedStatus" class="select select-sm select-primary w-full sm:w-[150px]"
                        @change="search">
                        <option value="">Tất cả trạng thái</option>
                        <option value="pending">Chờ xử lý</option>
                        <option value="processing">Đang xử lý</option>
                        <option value="delivering">Đang giao</option>
                        <option value="delivered">Đã giao</option>
                        <option value="cancelled">Đã hủy</option>
                    </select>

                    <div class="flex flex-row gap-2">
                        <select v-model="selectedYear" class="select select-sm select-primary w-full sm:w-[120px]"
                            @change="onYearMonthChange, search()">
                            <option value="">Năm</option>
                            <option v-for="year in years" :value="year" :key="year">{{ year }}</option>
                        </select>

                        <select v-model="selectedMonth" class="select select-sm select-primary w-full sm:w-[100px]"
                            @change="onYearMonthChange, search()">
                            <option value="">Tháng</option>
                            <option v-for="month in months" :value="month.value" :key="month.value">{{ month.display }}
                            </option>
                        </select>

                        <select v-model="selectedDay" class="select select-sm select-primary w-full sm:w-[100px]"
                            @change="search">
                            <option value="">Ngày</option>
                            <option v-for="day in days" :value="day.value" :key="day.value">{{ day.display }}</option>
                        </select>
                    </div>
                </div>
            </div>
            <button class="btn btn-sm btn-secondary w-full md:w-auto" @click="exportToExcel">
                Xuất Excel
            </button>
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
            <TableOrder :orders="orders.orders" :addressData="addressData" @delete="handleDelete"
                :display-edit-button="true" />

            <GridOrder :orders="orders.orders" :addressData="addressData" @delete="handleDelete"
                :display-edit-button="true" />

        </div>
        <div class="flex flex-col sm:flex-row justify-between items-center m-4 gap-2">
            <div class="flex items-center space-x-2">
                <p class="text-sm text-gray-600">{{ orders.orders.length }} / {{ orders.meta?.total }}</p>
                <select v-model="perPage" class="select select-sm select-primary w-18" @change="search">
                    <option v-for="n in [10, 25, 50, 100, 1000, 100000]" :value="n" :key="n">{{ n }}</option>
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
import * as XLSX from 'xlsx' // Nhập thư viện xlsx
import type { PaginationMeta, PaginationLinks } from '~/types/api'
import type { Order } from '~/types/order'

const { getAdminOrders, cancelAdminOrder } = useAdminOrder()
const { getFullAddressName } = useVietnamAddress()
const { selectedYear, selectedMonth, selectedDay, years, months, days, onYearMonthChange } = useDateFilter()
const swal = useSwal()
const { $toast } = useNuxtApp()

const currentPage = ref(1)
const perPage = ref(10)
const searchQuery = ref('')
const selectedStatus = ref('')
const selectedCategory = ref('')
const selectedOwner = ref('')
const sortBy = ref('')
const debouncedSearch = debounce(search, 500)


const { data, status } = await getAdminOrders({
    page: currentPage.value,
    per_page: perPage.value,
})

const orders = computed<{ orders: Order[], meta: PaginationMeta | null, links: PaginationLinks | null }>(() => ({
    orders: Array.isArray(data.value?.data) ? data.value.data : data.value?.data ? [data.value.data] : [],
    meta: data.value?.meta ?? null,
    links: data.value?.links ?? null,
}))

const addressData = ref<{ [key: string]: string }>({})
watch(() => orders.value.orders, async (newData) => {
    for (const order of newData) {
        if (order.ward) {
            addressData.value[order.id] = `${order.street_address}, ${await getFullAddressName(order.ward)}`
        }
    }
}, { immediate: true })

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
        ...(selectedYear.value && { year: Number(selectedYear.value) }),
        ...(selectedMonth.value && { month: Number(selectedMonth.value) }),
        ...(selectedDay.value && { day: Number(selectedDay.value) }),
    }

    const { error } = await getAdminOrders(filters)
    if (error.value) {
        $toast.error('Không thể tải danh sách sản phẩm!')
    }
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

function exportToExcel() {
    // Kiểm tra nếu không có đơn hàng
    if (!orders.value.orders.length) {
        $toast.error('Không có đơn hàng để xuất!')
        return
    }

    // Tính tổng giá tiền
    const totalAmount = orders.value.orders.reduce((sum, order) => {
        return sum + (order.status === 'delivered' && order.final_total_amount ? Number(order.final_total_amount) : 0)
    }, 0)

    // Chuẩn bị dữ liệu đơn hàng
    const exportData = orders.value.orders.map(order => ({
        'Mã hóa đơn': order.id,
        'Ngày tạo': order.created_at ? new Date(order.created_at).toLocaleDateString('vi-VN') : '',
        'Trạng thái': formatStatus(order.status),
        'Tổng tiền': order.final_total_amount ? Number(order.final_total_amount).toLocaleString('vi-VN') : '',
        'Khách hàng': order.full_name || 'Bán bên ngoài (' + order.items[0]?.product_snapshot.product_name + ')',
        'Số điện thoại': order.phone_number || '',
        'Email': order.email || '',
        'Địa chỉ': addressData.value[order.id] || '',
    }))

    // Tạo workbook và worksheet
    const workbook = XLSX.utils.book_new()
    const worksheet = XLSX.utils.json_to_sheet([]) // Tạo sheet rỗng

    // Thêm dòng tiêu đề
    XLSX.utils.sheet_add_aoa(worksheet, [['BÁO CÁO ĐƠN HÀNG']], { origin: 'A1' })

    // Thêm dòng thông tin bộ lọc (năm/tháng/ngày nếu có)
    let filterInfo = ''
    if (selectedYear.value) {
        filterInfo += `Năm: ${selectedYear.value}`
        if (selectedMonth.value) {
            filterInfo += `, Tháng: ${selectedMonth.value}`
            if (selectedDay.value) {
                filterInfo += `, Ngày: ${selectedDay.value}`
            }
        }
    }
    if (filterInfo) {
        XLSX.utils.sheet_add_aoa(worksheet, [[filterInfo]], { origin: 'A2' })
    }
    XLSX.utils.sheet_add_aoa(worksheet, [[]], { origin: 'A2' }) // Dòng trống

    // Thêm header và dữ liệu đơn hàng
    XLSX.utils.sheet_add_json(worksheet, exportData, { origin: 'A3', skipHeader: false })

    // Thêm hàng tổng
    const totalRow = {
        'Mã hóa đơn': '',
        'Ngày tạo': '',
        'Trạng thái': 'Tổng cộng',
        'Tổng tiền': totalAmount.toLocaleString('vi-VN'),
        'Khách hàng': '',
        'Số điện thoại': '',
        'Email': '',
        'Địa chỉ': ''
    }
    XLSX.utils.sheet_add_json(worksheet, [totalRow], { origin: -1, skipHeader: true })

    // Tùy chỉnh độ rộng cột
    worksheet['!cols'] = [
        { wch: 30 }, // Mã hóa đơn
        { wch: 15 }, // Ngày tạo
        { wch: 20 }, // Trạng thái
        { wch: 15 }, // Tổng tiền
        { wch: 40 }, // Khách hàng
        { wch: 15 }, // Số điện thoại
        { wch: 25 }, // Email
        { wch: 50 }, // Địa chỉ
    ]

    // Định dạng tiêu đề và hàng tổng
    worksheet['A1'].s = { font: { bold: true, sz: 16 }, alignment: { horizontal: 'center' } }
    const totalRowIndex = exportData.length + 3 // Vị trí hàng tổng (sau tiêu đề + dữ liệu)
    worksheet[`A${totalRowIndex}`].s = { font: { bold: true } }
    worksheet[`G${totalRowIndex}`].s = { font: { bold: true } }

    // Gộp ô cho tiêu đề
    worksheet['!merges'] = [{ s: { r: 0, c: 0 }, e: { r: 0, c: 7 } }]

    // Thêm sheet vào workbook
    XLSX.utils.book_append_sheet(workbook, worksheet, 'Đơn hàng')

    // Xuất file
    const fileName = `DonHang_${new Date().toISOString().slice(0, 10)}.xlsx`
    XLSX.writeFile(workbook, fileName)
}
</script>