<template>
    <div class="hidden md:block w-full overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-100 text-gray-700">
                    <th class="py-2 pl-2 w-[10%] text-left">Thời gian</th>
                    <th class="py-2 w-[15%] text-left">Hành động</th>
                    <th class="py-2 w-[35%] text-left">Ghi chú</th>
                    <th class="py-2 w-[15%] text-left">Ảnh</th>
                    <th class="py-2 w-[15%] text-left">Video</th>
                    <th class="py-2 w-[5%] text-center">Xóa</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="log in logs" :key="log.id" class="border-b border-gray-100 hover:bg-gray-200 cursor-pointer">
                    <td class="py-1 pl-2">{{ formatDateTime(log.created_at || '') }}</td>
                    <td class="py-1">{{ log.activity }}</td>
                    <td class="py-1">
                        <div class="flex flex-col">
                            <div v-if="log.fertilizer_used"><span class="font-semibold">Phân bón:</span> {{ log.fertilizer_used }}</div>
                            <div v-if="log.pesticide_used"><span class="font-semibold">Thuốc:</span> {{ log.pesticide_used }}</div>
                            <div v-if="log.notes"><span class="font-semibold">Ghi chú:</span> {{ log.notes }}</div>
                        </div>
                    </td>
                    <td class="py-1">
                        <NuxtImg v-if="log.image_url" :src="`${backendUrl}${log.image_url}`" alt="Hình ảnh"
                            class="w-full border border-gray-100 overflow-hidden object-cover aspect-video rounded-lg bg-gray-100" />
                        <div v-else class="flex justify-center items-center w-full aspect-video rounded-lg bg-gray-100">
                            Không có hình ảnh</div>
                    </td>
                    <td class="p-1">
                        <iframe v-if="log.video_url" class="w-full aspect-video rounded-lg"
                            :src="`https://www.youtube.com/embed/${log.video_url}`" frameborder="0"
                            allowfullscreen></iframe>
                        <div v-else class="flex justify-center items-center w-full aspect-video rounded-lg bg-gray-100">
                            Không có video</div>
                    </td>
                    <td class="p-1">
                        <div class="flex space-x-1 items-center justify-center">
                            <UiDeleteButton :on-click="() => onDelete(productId, log.id)" />
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <!-- Open the modal using ID.showModal() method -->
<dialog id="my_modal_2" class="modal">
  <div class="modal-box">
    <h3 class="text-lg font-bold">Hello!</h3>
    <p class="py-4">Press ESC key or click outside to close</p>
  </div>
  <form method="dialog" class="modal-backdrop">
    <button>close</button>
  </form>
</dialog>
</template>

<script setup lang="ts">
import type { CultivationLog } from '~/types/product'
import { formatDateTime } from '~/utils/common'
const $runtimeConfig = useRuntimeConfig();
const backendUrl = $runtimeConfig.public.backendUrl;

defineProps<{
    productId: number
    logs: CultivationLog[]
    onDelete: (productId: number, logId: number) => void
}>()
</script>