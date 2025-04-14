<template>
    <div>
        <div class="flex flex-col md:flex-row justify-between items-start gap-3 border-gray-200 px-3 py-4">
            <div class="flex flex-col w-full md:flex-row gap-2">
                <div class="relative w-full md:w-auto">
                    <Search class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 z-10 w-5 h-5" />
                    <input v-model="searchQuery" type="search" placeholder="Tìm kiếm khách hàng..."
                        class="input input-sm input-primary w-full pl-10" @input="debouncedSearch" />
                </div>
            </div>
            <button @click="$router.push('/admin/farms/create')" class="btn btn-sm btn-primary w-full md:w-auto">
                <Plus class="w-5 h-5" /> Thêm
            </button>
        </div>
        <!-- Table -->
        <!-- Content Wrapper with Loading Overlay -->
        <div class="relative">
            <!-- Loading Overlay specific to table/grid -->
            <div v-if="status === 'pending'"
                class="absolute inset-0 bg-gray-50 opacity-25 flex justify-center items-center z-10">
                <span class="loading loading-spinner loading-lg"></span>
            </div>
            <div class="w-full overflow-x-auto">
                
                <!-- Desktop Table View -->
                <TableFarm :farms="farms.farms" :address-data="addressData"
                    :on-delete="handleDeleteFarm" />
                <!-- Mobile Card View -->
                <GridFarm :farms="farms.farms":address-data="addressData"
                    :on-delete="handleDeleteFarm" />

                <!-- Pagination -->
                <div class="flex flex-col sm:flex-row justify-between items-center m-4 gap-2">
                    <div class="flex items-center space-x-2">
                        <p class="text-sm text-gray-600">{{ farms.farms.length }} / {{ farms.meta?.total }}</p>
                        <select v-model="perPage" class="select select-sm select-primary w-18" @change="search">
                            <option v-for="n in [10, 25, 50, 100]" :value="n" :key="n">{{ n }}</option>
                        </select>
                    </div>
                    <UiPagination :links="farms.links" :meta="farms.meta" :show-first-last="true" :show-numbers="true"
                        @page-change="handlePageChange" />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'user', title: 'Nông trại', description: 'Quản lý nông trại trên trang web' })

import { Search, Plus } from 'lucide-vue-next'
import { debounce } from 'lodash-es'
import type { Farm } from '~/types/farm'
import type { PaginationMeta, PaginationLinks } from '~/types/api'

const { searchFarms, deleteFarm } = useFarms()
const { getFullAddressName } = useVietnamAddress()
const { $toast } = useNuxtApp()
const swal = useSwal()
const router = useRouter()

const currentPage = ref(1)
const perPage = ref(10)
const searchQuery = ref('')
const expandedRows = ref(new Set<number>())
const debouncedSearch = debounce(search, 500)

const { data, status } = await searchFarms({ page: currentPage.value, per_page: perPage.value }, AuthType.User)
const farms = computed<{ farms: Farm[], meta: PaginationMeta | null, links: PaginationLinks | null }>(() => ({
    farms: Array.isArray(data.value?.data) ? data.value.data : data.value?.data ? [data.value.data] : [],
    meta: data.value?.meta ?? null,
    links: data.value?.links ?? null,
}))

const addressData = ref<{ [key: number]: string }>({})
watch(() => farms.value.farms, async (newFarms) => {
    for (const farm of newFarms) {
        if (farm.address?.ward) {
            addressData.value[farm.id] = `${farm.address.street_address || ''}, ${await getFullAddressName(farm.address.ward)}`
        }
    }
}, { immediate: true })

async function search() {
    const filters: { page?: number; per_page?: number; search?: string } = {
        page: currentPage.value,
        per_page: perPage.value,
        ...(searchQuery.value && { search: searchQuery.value }),
    }

    const { data, error } = await searchFarms(filters, AuthType.User)
    if (error.value) $toast.error('Không thể tải danh sách nông trại!')
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
            $toast.success('Nông trại đã được xóa!')
        } catch (err) {
            $toast.error(`Xóa thất bại: ${(err as Error).message || 'Unknown error'}`)
        }
    }
}

const handlePageChange = (page: number) => {
    currentPage.value = page
    search()
}

</script>