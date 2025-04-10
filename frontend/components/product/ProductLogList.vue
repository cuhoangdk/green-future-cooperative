<template>
    <div>
        <div class="flex justify-between items-center mb-4 gap-3">
            <h2 class="text-left text-xl font-bold text-green-800">Quá trình chăm sóc</h2>
            <div class="flex-1 h-[3px] bg-green-500"></div>
        </div>
        <div class="relative">
            <div v-if="logsStatus === 'pending'"
                class="absolute inset-0 flex items-center justify-center bg-gray-100/20 z-10">
                <span class="loading loading-spinner loading-xl"></span>
            </div>
        </div>
        <div v-for="log in logs.logs" :key="log.id">
            <div class="collapse collapse-arrow bg-base-100 border-green-500 border my-1.5">
                <input type="checkbox" />
                <div class="collapse-title font-semibold flex items-center ">
                    {{
                        log.created_at ? new Date(log.created_at).toLocaleString('vi-VN', {
                            hour: '2-digit', minute: '2-digit', day: '2-digit', month: '2-digit', year:
                                'numeric'
                        })
                            : ''
                    }} - {{ log.activity }}
                </div>
                <div class="collapse-content text-sm">
                    <div class="flex flex-col space-y-2 mb-2">
                        <div v-if="log.fertilizer_used"><span class="font-semibold">Phân bón:</span> {{
                            log.fertilizer_used }}
                        </div>
                        <div v-if="log.pesticide_used"><span class="font-semibold">Thuốc bảo vệ thực
                                vật:</span>
                            {{
                                log.pesticide_used
                            }}</div>
                        <div v-if="log.notes"><span class="font-semibold">Ghi chú:</span> {{ log.notes
                        }}
                        </div>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-2">
                        <NuxtImg v-if="log.image_url" :src="`${backendUrl}${log.image_url}`" alt="Hình ảnh"
                            class="w-full border border-gray-100 overflow-hidden object-cover aspect-video rounded-lg bg-gray-100" />
                        <div v-else class="flex justify-center items-center w-full aspect-video rounded-lg bg-gray-100">
                            Không có hình ảnh</div>

                        <iframe v-if="log.video_url" class="w-full aspect-video rounded-lg"
                            :src="`https://www.youtube.com/embed/${log.video_url}`" frameborder="0" allowfullscreen>
                        </iframe>
                        <div v-else class="flex justify-center items-center w-full aspect-video rounded-lg bg-gray-100">
                            Không có video</div>
                    </div>
                </div>
            </div>
        </div>
        <UiPagination class="mt-4" :links="logs.links" :meta="logs.meta" @page-change="handleLogsPageChange" />
    </div>
</template>

<script setup lang="ts">
import type { CultivationLog } from '~/types/product'
import type { PaginationMeta, PaginationLinks } from '~/types/api'

const { getLogs } = useCultivationLogs()
const config = useRuntimeConfig()
const backendUrl = config.public.backendUrl
const currentLogPage = ref(1)
const perLogPage = 10
interface Props {
    productId: number;
}

const props = defineProps<Props>()

const { data: logsData, status: logsStatus } = await getLogs(Number(props.productId) || -1, currentLogPage.value, perLogPage)
const logs = computed<{
    logs: CultivationLog[];
    meta: PaginationMeta | null;
    links: PaginationLinks | null;
}>(() => ({
    logs: Array.isArray(logsData.value?.data) ? (logsData.value.data as CultivationLog[]) : logsData.value?.data ? [logsData.value.data as CultivationLog] : [],
    meta: (logsData.value?.meta as PaginationMeta) ?? null,
    links: (logsData.value?.links as PaginationLinks) ?? null,
}));



const handleLogsPageChange = (page: number) => {
    currentLogPage.value = page
    getLogs(props.productId || -1, page, perLogPage)
}
</script>