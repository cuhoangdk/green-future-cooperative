<template>
    <div class="border border-gray-200 rounded-lg">
        <!-- Header -->
        <div class="flex justify-between items-center border-gray-200 px-3 pt-2">
            <h1 class="text-xl font-bold text-gray-800">Danh sách người dùng</h1>
            <button @click="$router.push('/admin/users/create')" class="btn btn-sm btn-secondary">
                <Plus class="w-3 h-3" /> Thêm người dùng
            </button>
        </div>

        <!-- Filters -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-2 px-3 py-3">
            <div class="relative">
                <Search class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 z-10 w-5 h-5" />
                <input v-model="searchQuery" type="search" placeholder="Tìm kiếm người dùng..."
                    class="input input-sm input-primary w-full pl-10" @input="debouncedSearch" />
            </div>
            <select v-model="selectedRole" class="select select-sm select-primary" @change="search">
                <option value="">Tất cả vai trò</option>
                <option value="admin">Quản trị viên</option>
                <option value="user">Người dùng</option>
                <option value="customer">Khách hàng</option>
            </select>
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
                    <template v-for="user in users.users" :key="user.id">
                        <tr class="border-b border-gray-100 hover:bg-gray-200 cursor-pointer"
                            @click="toggleRow(user.id)"
                            :class="{ 'bg-gray-200 hover:bg-gray-200': expandedRows.has(user.id) }">
                            <td class="py-2">
                                <component :is="expandedRows.has(user.id) ? ChevronDown : ChevronRight"
                                    class="text-green-600" />
                            </td>
                            <td class="py-2">{{ user.full_name }}</td>
                            <td class="py-2">{{ user.email }}</td>
                            <td class="py-2">{{ user.phone_number }}</td>
                            <td class="py-2">
                                <span class="px-2 py-1 rounded-full text-xs" :class="{
                                    'bg-green-100 text-green-800': !user.is_banned,
                                    'bg-red-100 text-red-800': user.is_banned
                                }">
                                    {{ !user.is_banned ? 'Hoạt động' : 'Đã bị cấm' }}
                                </span>
                            </td>
                        </tr>
                        <tr v-if="expandedRows.has(user.id)" class="bg-gray-50">
                            <td colspan="7" class="p-3">
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 text-sm">
                                    <div>
                                        <p class="font-semibold">Địa chỉ:</p>
                                        <p class="text-gray-600">{{ addressData[user.id] ? addressData[user.id] : (user.address?.street_address || 'Chưa cập nhật') }}</p>
                                    </div>
                                    <div>
                                        <p class="font-semibold">Ngày tham gia:</p>
                                        <p class="text-gray-600">{{ new Date(user.created_at).toLocaleString('vi-VN') }}</p>
                                    </div>
                                    <div>
                                        <p class="font-semibold">Cập nhật lúc:</p>
                                        <p class="text-gray-600">{{ new Date(user.updated_at).toLocaleString('vi-VN') }}</p>
                                    </div>
                                </div>
                                <div class="flex space-x-3 mt-4">
                                    <button @click.stop="handleDeleteUser(user.id)"
                                        class="btn btn-sm btn-error px-4">Xóa</button>
                                    <button @click.stop="$router.push(`/admin/users/${user.usercode}/edit`)"
                                        class="btn btn-sm btn-primary px-4">Sửa</button>
                                    <button @click.stop="handleToggleStatus(user)"
                                        class="btn btn-sm" :class="user.is_banned ? 'btn-success' : 'btn-warning'">
                                        {{ user.is_banned ? 'Mở khóa tài khoản' : 'Khóa tài khoản' }}
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
                    <p class="text-sm text-gray-600 w-32">{{ users.users.length }} / {{ users.meta?.total }} người dùng</p>
                    <select v-model="perPage" class="select select-sm select-primary" @change="search">
                        <option v-for="n in [10, 25, 50, 100]" :value="n" :key="n">{{ n }}</option>
                    </select>
                </div>
                <UiPagination :links="users.links" :meta="users.meta" :show-first-last="true" :show-numbers="true"
                    @page-change="handlePageChange" />
            </div>
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

const addressData = ref<{[key: number]: string}>({})
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
</script>