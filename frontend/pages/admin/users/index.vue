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
            <button @click="$router.push('/admin/users/create')" class="btn btn-sm btn-primary w-full md:w-auto">
                <Plus class="w-5 h-5" /> Thêm
            </button>
        </div>

        <!-- Desktop Table View -->
        <TableUser :users="users.users" :on-toggle-status="handleToggleStatus" :on-edit="handleEdit"
            :on-delete="handleDeleteUser" />

        <GridUser :users="users.users" :on-toggle-status="handleToggleStatus" :on-edit="handleEdit"
            :on-delete="handleDeleteUser" />

        <!-- Pagination -->
        <div class="flex justify-between items-center gap-2 m-4">
            <div class="flex items-center space-x-2">
                <p class="text-sm text-gray-600 w-32">{{ users.users.length }} / {{ users.meta?.total }} người dùng
                </p>
                <select v-model="perPage" class="select select-sm select-primary" @change="search">
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
import { useToast } from 'vue-toastification'
import type { PaginationMeta, PaginationLinks } from '~/types/api'
import type { User } from '~/types/user'

const { searchUsersWithFilters, deleteUser, updateUser } = useUsers()
const { getFullAddressName } = useVietnamAddress()
const swal = useSwal()
const toast = useToast()
const router = useRouter()

const currentPage = ref(Number(useRoute().query.page) || 1)
const perPage = ref(10)
const searchQuery = ref('')
const selectedRole = ref('')
const selectedStatus = ref('')
const sortBy = ref('')
const expandedRows = ref(new Set<number>())
const debouncedSearch = debounce(search, 500)

const { data } = await searchUsersWithFilters({ page: currentPage.value, per_page: perPage.value }, AuthType.User)
const users = computed<{ users: User[], meta: PaginationMeta | null, links: PaginationLinks | null }>(() => ({
    users: Array.isArray(data.value?.data) ? data.value.data : data.value?.data ? [data.value.data] : [],
    meta: data.value?.meta ?? null,
    links: data.value?.links ?? null,
}))

const addressData = ref<{ [key: number]: string }>({})
watch(() => users.value.users, async (newUsers) => {
    for (const user of newUsers) {
        if (user.address?.ward) {
            addressData.value[user.id] = await getFullAddressName(user.address.ward)
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
            toast.success('Người dùng đã được xóa!')
        } catch (err) {
            toast.error(`Xóa thất bại: ${(err as Error).message || 'Unknown error'}`)
        }
    }
}

async function handleToggleStatus(user: User) {
    try {
        const newBanStatus = !user.is_banned
        const { error } = await updateUser(user.id, { is_banned: newBanStatus })
        if (error.value) throw new Error(error.value.message)

        user.is_banned = newBanStatus
        toast.success(`Đã ${newBanStatus ? 'khóa' : 'mở khóa'} tài khoản thành công!`)
    } catch (err) {
        toast.error(`Thao tác thất bại: ${(err as Error).message || 'Unknown error'}`)
    }
}

const handleEdit = (customerId: number) => {
    router.push(`/admin/users/${customerId}/edit`)
}
</script>