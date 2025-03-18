<template>
    <div class="border border-gray-200 rounded-lg">
        <!-- Header -->
        <div class="flex justify-between items-center border-gray-200 px-3 py-2">
            <h1 class="text-xl font-bold text-gray-800">Danh sách nhật ký chăm sóc</h1>
            <button @click="$router.push(`log-create`)" class="btn btn-sm btn-secondary">
                <Plus class="w-3 h-3" /> Thêm nhật ký
            </button>
        </div>

        <div class="w-full overflow-x-auto">
            <table class="table w-full border-collapse bg-white border border-gray-200">
                <thead>
                    <tr class="bg-gray-100 text-gray-700">
                        <th class="py-2 w-[5%]"></th>
                        <th class="py-2 w-[30%] text-left">Thời gian</th>
                        <th class="py-2 w-[65%] text-left">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <template v-for="log in logs.logs" :key="log.id">
                        <tr class="border-b border-gray-100 hover:bg-gray-200 cursor-pointer" @click="toggleRow(log.id)"
                            :class="{ 'bg-gray-200 hover:bg-gray-200': expandedRows.has(log.id) }">
                            <td class="py-2">
                                <component :is="expandedRows.has(log.id) ? ChevronDown : ChevronRight"
                                    class="text-green-600" />
                            </td>
                            <td class="py-2">{{ log.created_at ? new Date(log.created_at).toLocaleString('vi-VN', {
                                hour: '2-digit', minute: '2-digit', day: '2-digit', month: '2-digit', year: 'numeric'
                            })
                                : '' }}</td>
                            <td class="py-2">{{ log.activity }}</td>
                        </tr>
                        <tr v-if="expandedRows.has(log.id)" class="bg-gray-50">
                            <td colspan="7" class="p-3">
                                <div class="grid grid-cols-1 lg:grid-cols-3 gap-2 text-s mt-2">
                                    <div class="flex flex-col space-y-2">
                                        <div><span class="font-semibold">Phân bón:</span> {{ log.fertilizer_used || ""
                                        }}
                                        </div>
                                        <div><span class="font-semibold">Thuốc bảo vệ thực vật:</span> {{
                                            log.pesticide_used
                                        }}</div>
                                        <div><span class="font-semibold">Ghi chú:</span> {{ log.notes }}</div>
                                    </div>
                                    <NuxtImg v-if="log.image_url" :src="`${backendUrl}${log.image_url}`" alt="Hình ảnh"
                                        class="w-full border border-gray-100 overflow-hidden object-cover aspect-video rounded-lg bg-gray-100" />
                                    <div v-else
                                        class="flex justify-center items-center w-full aspect-video rounded-lg bg-gray-100">
                                        Không có hình ảnh</div>

                                    <iframe v-if="log.video_url" class="w-full aspect-video rounded-lg"
                                        :src="`https://www.youtube.com/embed/${log.video_url}`" frameborder="0"
                                        allowfullscreen>
                                    </iframe>
                                    <div v-else
                                        class="flex justify-center items-center w-full aspect-video rounded-lg bg-gray-100">
                                        Không có video</div>
                                </div>

                                <div class="flex space-x-4 justify-end mt-2">
                                    <button @click.stop="$router.push(`/admin/products/${log.id}`)"
                                        class="btn btn-sm btn-primary px-4">Cập nhật</button>
                                    <button @click.stop="handleDeleteLog(productId, log.id)"
                                        class="btn btn-sm btn-error px-4">Xóa</button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="flex justify-between items-center m-4">
                <div class="flex items-center space-x-2">
                    <p class="text-sm text-gray-600 w-32">{{ logs.logs.length }} / {{ logs.meta?.total }}</p>
                    <!-- <select v-model="perPage" class="select select-sm select-primary" @change="search">
                        <option v-for="n in [10, 25, 50, 100]" :value="n" :key="n">{{ n }}</option>
                    </select> -->
                </div>
                <UiPagination :links="logs.links" :meta="logs.meta" :show-first-last="true" :show-numbers="true"
                    @page-change="handlePageChange" />
            </div>
        </div>
    </div>

</template>

<script setup lang="ts">
definePageMeta({ title: 'Thêm nhật ký chăm sóc', layout: 'user', })

import type { CultivationLog } from '~/types/product'
import type { PaginationLinks, PaginationMeta } from '~/types/api'
import { ChevronDown, ChevronRight, Plus } from 'lucide-vue-next'
import { useSwal } from '~/composables/useSwal'
import { useToast } from 'vue-toastification'

const { getLogs, deleteLog } = useCultivationLogs()
const $runtimeConfig = useRuntimeConfig();
const swal = useSwal()
const toast = useToast()
const router = useRouter()
const route = useRoute()
const backendUrl = $runtimeConfig.public.backendUrl;

const productStore = useProductStore()
const product = productStore.selectedProduct

const expandedRows = ref(new Set<number>())
const currentPage = ref(1)
const perPage = ref(10)
const productId = Number(route.params.id)

const { data } = await getLogs(productId)
const logs = computed<{
    logs: CultivationLog[];
    meta: PaginationMeta | null;
    links: PaginationLinks | null;
}>(() => ({
    logs: Array.isArray(data.value?.data) ? (data.value.data as CultivationLog[]) : data.value?.data ? [data.value.data as CultivationLog] : [],
    meta: (data.value?.meta as PaginationMeta) ?? null,
    links: (data.value?.links as PaginationLinks) ?? null,
}));

const toggleRow = (id: number) => {
    expandedRows.value.has(id) ? expandedRows.value.delete(id) : expandedRows.value.add(id)
}
const handlePageChange = (page: number) => {
    currentPage.value = page
    getLogs(page)
}

async function handleDeleteLog(productId: number, logId: number) {
    const result = await swal.fire({
        title: 'Xác nhận xóa',
        text: 'Bạn có chắc muốn xóa bài viết này không?',
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
            expandedRows.value.delete(logId)
            toast.success('Bài viết đã được xóa!')
        } catch (err) {
            toast.error(`Xóa thất bại: ${(err as Error).message || 'Unknown error'}`)
        }
    }
}
</script>