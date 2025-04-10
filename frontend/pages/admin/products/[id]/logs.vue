<template>
    <div>
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start gap-3 border-gray-200 px-3 py-4">
            <h1 class="text-xl font-semibold text-gray-800">{{ product?.product_code }} - {{ product?.name }}</h1>
            <NuxtLink to="log-create" class="btn btn-sm btn-primary w-full md:w-auto">
                <Plus class="w-5 h-5" /> Thêm
            </NuxtLink>
        </div>

        <TableProductLog :logs="logs.logs" :product-id="product?.id ?? -1"  v-on:delete="handleDeleteLog" />
        <GridProductLog :logs="logs.logs" :product-id="product?.id ?? -1" v-on:delete="handleDeleteLog" />

        <div class="flex flex-col lg:flex-row justify-between items-center gap-2 m-4">
            <div class="flex items-center space-x-2">
                <p class="text-sm text-gray-600 w-32">{{ logs.logs.length }} / {{ logs.meta?.total }}</p>
                <select v-model="perPage" class="select select-sm select-primary" @change="search">
                        <option v-for="n in [10, 25, 50, 100]" :value="n" :key="n">{{ n }}</option>
                    </select>
            </div>
            <UiPagination :links="logs.links" :meta="logs.meta" :show-first-last="true" :show-numbers="true"
                @page-change="handlePageChange" />
        </div>
    </div>

</template>

<script setup lang="ts">
definePageMeta({ title: 'Nhật ký chăm sóc', layout: 'user', })

import type { CultivationLog, Product } from '~/types/product'
import type { PaginationLinks, PaginationMeta } from '~/types/api'
import { ChevronDown, ChevronRight, Plus } from 'lucide-vue-next'
import { useSwal } from '~/composables/useSwal'
import { useToast } from 'vue-toastification'

const { getLogs, deleteLog } = useCultivationLogs()
const { getProductById } = useProducts()
const swal = useSwal()
const toast = useToast()
const route = useRoute()

const currentPage = ref(1)
const perPage = ref(10)
const productId = Number(route.params.id)
// Lấy thông tin sản phẩm
const { data: productData } = await getProductById(productId)
const product = computed<Product | null>(() => Array.isArray(productData.value?.data) ? productData.value.data[0] : productData.value?.data || null)


const { data, refresh } = await getLogs(productId, currentPage.value, perPage.value)
const logs = computed<{
    logs: CultivationLog[];
    meta: PaginationMeta | null;
    links: PaginationLinks | null;
}>(() => ({
    logs: Array.isArray(data.value?.data) ? (data.value.data as CultivationLog[]) : data.value?.data ? [data.value.data as CultivationLog] : [],
    meta: (data.value?.meta as PaginationMeta) ?? null,
    links: (data.value?.links as PaginationLinks) ?? null,
}));

const handlePageChange = (page: number) => {
    currentPage.value = page
    getLogs(productId, currentPage.value, perPage.value)
}

async function handleDeleteLog(productId: number, logId: number) {
    const result = await swal.fire({
        title: 'Xác nhận xóa',
        text: 'Bạn có chắc muốn nhật ký này không?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Xóa',
        cancelButtonText: 'Hủy',
    })

    if (result.isConfirmed) {
        try {
            const { error } = await deleteLog(productId, logId)
            if (error.value) throw new Error(error.value.message)
            logs.value.logs = logs.value.logs.filter((log: CultivationLog) => log.id !== logId)
            toast.success('Nhật ký đã được xóa!')
            refresh()
        } catch (err) {
            toast.error(`Xóa thất bại: ${(err as Error).message || 'Unknown error'}`)
        }
    }
}

async function search() {
    currentPage.value = 1
    const { data, error } = await getLogs(productId, perPage.value, currentPage.value)
    if (error.value) swal.fire('Lỗi', 'Không thể tải danh sách sản phẩm!', 'error')
}
</script>