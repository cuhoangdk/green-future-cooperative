<!-- components/customer/CustomerGrid.vue -->
<template>
    <div class="grid grid-cols-1 gap-4 p-3 md:hidden">
        <div v-for="log in logs" :key="log.id"
            class="card bg-base-100 border border-gray-400 hover:shadow-md transition-shadow cursor-pointer">
            <div class="card-body p-3">
                <div class="flex items-start">
                    <h3 class="card-title text-base">{{ formatDateTime(log.created_at || '') }}: {{ log.activity }}</h3>
                </div>

                <div class="space-y-1 text-sm">
                    <div v-if="log.fertilizer_used" class="flex gap-2 items-center">
                        <Leaf class="w-4 h-4 text-gray-500" /> Phân bón:
                        <span>{{ log.fertilizer_used }}</span>
                    </div>
                    <div v-if="log.pesticide_used" class="flex gap-2 items-center">
                        <Leaf class="w-4 h-4 text-gray-500" /> Thuốc bảo vệ thực vật:
                        <span>{{ log.pesticide_used }}</span>
                    </div>
                    <div v-if="log.notes" class="flex gap-2 items-center">
                        <Notebook class="w-4 h-4 text-gray-500" /> Ghi chú:
                        <span>{{ log.notes }}</span>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <NuxtImg v-if="log.image_url" :src="`${backendUrl}${log.image_url}`" alt="Hình ảnh"
                            class="w-full border border-gray-100 overflow-hidden object-cover aspect-video rounded-lg bg-gray-100" />
                        <div v-else class="flex justify-center items-center w-full aspect-video rounded-lg bg-gray-100">
                            Không có hình ảnh</div>
                        <iframe v-if="log.video_url" class="w-full aspect-video rounded-lg"
                            :src="`https://www.youtube.com/embed/${log.video_url}`" frameborder="0"
                            allowfullscreen></iframe>
                        <div v-else class="flex justify-center items-center w-full aspect-video rounded-lg bg-gray-100">
                            Không có video</div>
                    </div>

                </div>

                <div class="card-actions justify-end border-t pt-2 border-gray-100">
                    <div class="flex space-x-1 items-center">
                        <UiDeleteButton :on-click="() => onDelete(productId, log.id)" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import type { CultivationLog } from '~/types/product'
import { Notebook, Leaf } from 'lucide-vue-next'
import { formatDateTime } from '~/utils/common'
const $runtimeConfig = useRuntimeConfig();
const backendUrl = $runtimeConfig.public.backendUrl;

defineProps<{
    productId: string
    logs: CultivationLog[]
    onDelete: (productId: string, logId: number) => void
}>()
</script>