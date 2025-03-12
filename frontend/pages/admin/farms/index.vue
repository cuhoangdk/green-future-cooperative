<template>
    <div class="border border-gray-200 rounded-lg">
        <!-- Header -->
        <div class="flex justify-between items-center border-b border-gray-200 px-3 py-2">
            <h1 class="text-xl font-bold text-gray-800">Danh sách nông trại</h1>
            <button @click="$router.push('/admin/farms/create')" class="btn btn-sm btn-secondary">
                <Plus class="w-3 h-3" /> Thêm nông trại
            </button>
        </div>

        <!-- Search -->
        <div class="px-3 py-3">
            <div class="relative">
                <Search class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 z-10 w-5 h-5" />
                <input v-model="searchQuery" type="search" placeholder="Tìm kiếm nông trại..."
                    class="input input-sm input-primary pl-10" @input="debouncedSearch" />
            </div>
        </div>

        <!-- Table -->
        <div class="w-full overflow-x-auto">
            <table class="table w-full border-collapse bg-white border border-gray-200">
                <thead>
                    <tr class="bg-gray-100 text-gray-700">
                        <th class="py-2 w-[3%]"></th>
                        <th class="py-2 w-[3%]">ID</th>
                        <th class="py-2 w-[15%] text-left">Tên</th>
                        <th class="py-2 w-[15%] text-left">Chủ sở hữu</th>
                        <th class="py-2 w-[40%] text-left">Địa chỉ</th>
                    </tr>
                </thead>
                <tbody>
                    <template v-if="farms?.farms" v-for="farm in farms.farms" :key="farm.id">
                        <tr class="border-b border-gray-100 hover:bg-gray-200 cursor-pointer"
                            @click="toggleRow(farm.id)"
                            :class="{ 'bg-gray-200 hover:bg-gray-200': expandedRows.has(farm.id) }">
                            <td class="py-2">
                                <component :is="expandedRows.has(farm.id) ? ChevronDown : ChevronRight"
                                    class="text-green-600 w-5 h-5" />
                            </td>
                            <td class="py-2">{{ farm.id }}</td>
                            <td class="py-2">{{ farm.name }}</td>
                            <td class="py-2">{{ farm.user.full_name }}</td>
                            <td class="py-2">{{ farm.address }}</td>
                        </tr>
                        <tr v-if="expandedRows.has(farm.id)" class="bg-gray-50">
                            <td colspan="5" class="p-3">
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2 text-sm">
                                    <div><span class="font-semibold">Tóm tắt:</span>
                                        <p class="text-gray-600 line-clamp-2">{{ farm.description }}</p>
                                    </div>
                                    <div><span class="font-semibold">Ngày tạo:</span> {{ new
                                        Date(farm.created_at).toLocaleString("vi-VN") }}</div>
                                    <div><span class="font-semibold">Chủ sở hữu:</span> {{ farm.user?.full_name }}</div>
                                    <div><span class="font-semibold">Địa chỉ:</span> {{ farm.address.street_address }}
                                    </div>
                                    <div><span class="font-semibold">Loại đất:</span> {{ farm.soil_type || "Chưa xác định" }}</div>
                                    <div><span class="font-semibold">Phương pháp canh tác:</span> {{
                                        farm.irrigation_method || "Chưa xác định" }}</div>
                                    <div><span class="font-semibold">Kích thước:</span> {{ farm.farm_size || "Chưa xác định" }}</div>
                                    <div>
                                        <span class="font-semibold">Kinh độ:</span> {{ farm.latitude || "Chưa xác định"
                                        }}<br>
                                        <span class="font-semibold">Vĩ độ:</span> {{ farm.longitude || "Chưa xác định"
                                        }}
                                    </div>
                                    <div class="flex space-x-2 mt-2 sm:col-span-2 lg:col-span-4">
                                        <button @click.stop="$router.push(`/admin/farms/${farm.id}`)"
                                            class="btn btn-sm btn-primary">Sửa</button>
                                        <button @click.stop="handleDeleteFarm(farm.id)"
                                            class="btn btn-sm btn-error">Xóa</button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="flex flex-col sm:flex-row justify-between items-center m-4 gap-2">
                <div class="flex items-center space-x-2">
                    <p class="text-sm text-gray-600">{{ farms.farms.length }} / {{ farms.meta?.total }} bài viết</p>
                    <select v-model="perPage" class="select select-sm select-primary" @change="search">
                        <option v-for="n in [10, 25, 50, 100]" :value="n" :key="n">{{ n }}</option>
                    </select>
                </div>
                <UiPagination :links="farms.links" :meta="farms.meta" :show-first-last="true" :show-numbers="true"
                    @page-change="handlePageChange" />
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'user', title: 'Quản lý nông trại' })

import { ChevronDown, ChevronRight, Search, Plus } from 'lucide-vue-next'
import { debounce } from 'lodash-es'
import { useToast } from 'vue-toastification'
import type { Farm } from '~/types/farm'
import type { PaginationMeta, PaginationLinks } from '~/types/api'

const { searchFarms, deleteFarm } = useFarms()
const toast = useToast()
const swal = useSwal()

const currentPage = ref(1)
const perPage = ref(10)
const searchQuery = ref('')
const expandedRows = ref(new Set<number>())
const debouncedSearch = debounce(search, 500)

const { data } = await searchFarms({ page: currentPage.value, per_page: perPage.value }, AuthType.User)
const farms = computed(() => ({
    farms: Array.isArray(data.value?.data) ? data.value.data : data.value?.data ? [data.value.data] : [],
    meta: data.value?.meta ?? null,
    links: data.value?.links ?? null,
}))

// watch(data, (newData) => {
//     farms.value = {
//         farms: Array.isArray(newData?.data) ? newData.data : newData?.data ? [newData.data] : [],
//         meta: newData?.meta ?? null,
//         links: newData?.links ?? null,
//     }
// })

async function search() {
    const filters: { page?: number; per_page?: number; search?: string } = {
        page: currentPage.value,
        per_page: perPage.value,
        ...(searchQuery.value && { search: searchQuery.value }),
    }

    const { data, error } = await searchFarms(filters, AuthType.User)
    if (error.value) toast.error('Không thể tải danh sách nông trại!')
}

async function handleDeleteFarm(farmId: number) {
    const result = await swal.fire({
        title: 'Xác nhận xóa',
        text: 'Bạn có chắc muốn xóa nông trại này không?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Xóa',
        cancelButtonText: 'Hủy',
    })

    if (result.isConfirmed) {
        try {
            const { error } = await deleteFarm(farmId)
            if (error.value) throw new Error(error.value.message)
            farms.value.farms = farms.value.farms.filter(farm => farm.id !== farmId)
            expandedRows.value.delete(farmId)
            toast.success('Nông trại đã được xóa!')
        } catch (err) {
            toast.error(`Xóa thất bại: ${(err as Error).message || 'Unknown error'}`)
        }
    }
}

const handlePageChange = (page: number) => {
    currentPage.value = page
    search()
}

const toggleRow = (id: number) => {
    expandedRows.value.has(id)
        ? expandedRows.value.delete(id)
        : expandedRows.value.add(id)
}
</script>