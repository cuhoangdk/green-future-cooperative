<template>
    <div>
        <div class="flex flex-col md:flex-row justify-between items-start gap-3 border-gray-200 px-3 py-4">
            <div class="flex flex-col w-full md:flex-row gap-2">
                <div class="relative w-full md:w-auto">
                    <Search class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 z-10 w-5 h-5" />
                    <input v-model="searchQuery" type="search" placeholder="Tìm kiếm khách hàng..."
                        class="input input-sm input-primary w-full pl-10" @input="debouncedSearch" />
                </div>
                <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto">
                    <select v-model="selectedRole" class="select select-sm select-primary w-full sm:w-[150px]"
                        @change="search">
                        <option value="">Tất cả vai trò</option>
                        <option value="admin">Quản trị viên</option>
                        <option value="user">Người dùng</option>
                    </select>
                    <select v-model="selectedStatus" class="select select-sm select-primary w-full sm:w-[150px]"
                        @change="search">
                        <option value="">Tất cả trạng thái</option>
                        <option value="active">Hoạt động</option>
                        <option value="inactive">Không hoạt động</option>
                    </select>
                    <select v-model="sortBy" class="select select-sm select-primary w-full sm:w-[150px]"
                        @change="search">
                        <option value="">Sắp xếp theo</option>
                        <option value="created_at+desc">Mới nhất</option>
                        <option value="created_at+asc">Cũ nhất</option>
                        <option value="full_name">Theo tên</option>
                        <option value="email">Theo email</option>
                    </select>
                </div>
            </div>
            <button @click="exportToExcel" class="btn btn-sm btn-secondary w-full md:w-auto">
                Xuất Excel
            </button>
            <button @click="$router.push('/admin/users/create')" class="btn btn-sm btn-primary w-full md:w-auto">
                <Plus class="w-5 h-5" /> Thêm
            </button>
        </div>
        <div class="relative">
            <!-- Loading Overlay specific to table/grid -->
            <div v-if="status === 'pending'"
                class="absolute inset-0 bg-gray-50 opacity-25 flex justify-center items-center z-10">
                <span class="loading loading-spinner loading-lg"></span>
            </div>
            <!-- Desktop Table View -->
            <TableUser :users="users.users" :on-toggle-status="handleToggleStatus" :on-delete="handleDeleteUser" />

            <GridUser :users="users.users" :on-toggle-status="handleToggleStatus" :on-delete="handleDeleteUser" />
        </div>
        <!-- Pagination -->
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4 m-4">
            <div class="flex items-center space-x-2">
                <p class="text-sm text-gray-600">{{ users.users.length }} / {{ users.meta?.total }}
                </p>
                <select v-model="perPage" class="select select-sm select-primary w-18" @change="search">
                    <option v-for="n in [10, 25, 50, 100]" :value="n" :key="n">{{ n }}</option>
                </select>
            </div>
            <UiPagination :links="users.links" :meta="users.meta" :show-first-last="true" :show-numbers="true"
                @page-change="handlePageChange" />
        </div>
    </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'user', title: 'Người dùng', description: 'Quản lý người dùng trên trang web' })

import { ChevronDown, ChevronRight, Search, Plus } from 'lucide-vue-next'
import { debounce } from 'lodash-es'
import { useSwal } from '~/composables/useSwal'
import * as XLSX from 'xlsx';
import type { PaginationMeta, PaginationLinks } from '~/types/api'
import type { User } from '~/types/user'

const { searchUsersWithFilters, deleteUser, updateUser } = useUsers()
const { getFullAddressName } = useVietnamAddress()
const swal = useSwal()
const router = useRouter()
const { $toast } = useNuxtApp()

const currentPage = ref(Number(useRoute().query.page) || 1)
const perPage = ref(10)
const searchQuery = ref('')
const selectedRole = ref('')
const selectedStatus = ref('')
const sortBy = ref('')
const expandedRows = ref(new Set<number>())
const debouncedSearch = debounce(search, 500)

const { data, status } = await searchUsersWithFilters({ page: currentPage.value, per_page: perPage.value }, AuthType.User)
const users = computed<{ users: User[], meta: PaginationMeta | null, links: PaginationLinks | null }>(() => ({
    users: Array.isArray(data.value?.data) ? data.value.data : data.value?.data ? [data.value.data] : [],
    meta: data.value?.meta ?? null,
    links: data.value?.links ?? null,
}))

async function search() {
    const filters: any = {
        page: currentPage.value,
        per_page: perPage.value,
        ...(searchQuery.value && { search: searchQuery.value }),
        ...(selectedRole.value && { role: selectedRole.value }),
        ...(selectedStatus.value && { status: selectedStatus.value }),
        ...(sortBy.value && {
            sort_by: sortBy.value.includes('+') ? sortBy.value.split('+')[0] : sortBy.value,
            sort_direction: sortBy.value.endsWith('+desc') ? 'desc' : 'asc'
        }),
    }

    const { data, error } = await searchUsersWithFilters(filters, AuthType.User)
    if (error.value) swal.fire('Lỗi', 'Không thể tải danh sách người dùng!', 'error')
}

const handlePageChange = (page: number) => {
    currentPage.value = page
    search()
}

async function handleDeleteUser(userId: number) {
    const result = await swal.fire({
        title: 'Xác nhận xóa',
        text: 'Bạn có chắc muốn xóa người dùng này không?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Xóa',
        cancelButtonText: 'Hủy',
    })

    if (result.isConfirmed) {
        try {
            const { error } = await deleteUser(userId)
            if (error.value) throw new Error(error.value.message)
            users.value.users = users.value.users.filter((user: User) => user.id !== userId)
            expandedRows.value.delete(userId)
            $toast.success('Người dùng đã được xóa!')
        } catch (err) {
            $toast.error(`Xóa thất bại: ${(err as Error).message || 'Unknown error'}`)
        }
    }
}

async function handleToggleStatus(user: User) {
    try {
        const newBanStatus = !user.is_banned
        const { error } = await updateUser(user.id, { is_banned: newBanStatus })
        if (error.value) throw new Error(error.value.message)

        user.is_banned = newBanStatus
        $toast.success(`Đã ${newBanStatus ? 'khóa' : 'mở khóa'} tài khoản thành công!`)
    } catch (err) {
        $toast.error(`Thao tác thất bại: ${(err as Error).message || 'Unknown error'}`)
    }
}


function exportToExcel() {
    // Kiểm tra nếu không có khách hàng
    if (!users.value.users.length) {
        $toast.error('Không có khách hàng để xuất!');
        return;
    }

    // Chuẩn bị dữ liệu khách hàng
    const exportData = users.value.users.map(user => ({
        'Mã khách hàng': user.id,
        'Họ và tên': user.full_name,
        'Email': user.email || 'N/A',
        'Số điện thoại': user.phone_number || 'N/A',
        'Trạng thái': user.is_banned ? 'Bị cấm' : 'Hoạt động',
    }));

    // Tạo workbook và worksheet
    const workbook = XLSX.utils.book_new();
    const worksheet = XLSX.utils.json_to_sheet([]); // Tạo sheet rỗng

    // Thêm dòng tiêu đề
    XLSX.utils.sheet_add_aoa(worksheet, [['BÁO CÁO KHÁCH HÀNG']], { origin: 'A1' });
    XLSX.utils.sheet_add_aoa(worksheet, [[]], { origin: 'A2' }); // Dòng trống

    // Thêm header và dữ liệu khách hàng
    XLSX.utils.sheet_add_json(worksheet, exportData, { origin: 'A3', skipHeader: false });

    // Tùy chỉnh độ rộng cột
    worksheet['!cols'] = [
        { wch: 15 }, // Mã khách hàng
        { wch: 25 }, // Họ và tên
        { wch: 30 }, // Email
        { wch: 15 }, // Số điện thoại
        { wch: 15 }  // Trạng thái
    ];

    // Thêm sheet vào workbook
    XLSX.utils.book_append_sheet(workbook, worksheet, 'Khách hàng');

    // Xuất file
    const fileName = `KhachHang_${new Date().toISOString().slice(0, 10)}.xlsx`;
    XLSX.writeFile(workbook, fileName);
}
</script>