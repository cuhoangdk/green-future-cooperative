<template>
    <div class="order-status-timeline">
        <ul v-if="status != 'cancelled'" class="timeline timeline-vertical lg:timeline-horizontal">
            <li>
                <div
                    :class="['timeline-start timeline-box', { 'bg-blue-500 text-white font-bold': isStatusActive('pending') }]">
                    Chờ xác nhận
                </div>
                <div class="timeline-middle">
                    <svg v-if="isStatusPassed('pending')" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                        fill="currentColor" class="h-5 w-5">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z"
                            clip-rule="evenodd" />
                    </svg>
                    <div v-else class="w-5 h-5 rounded-full border-2 border-gray-300"></div>
                </div>
                <hr :class="{ 'bg-blue-500': isStatusActive('pending') || isStatusPassed('pending') }"
                    class="transition-colors duration-300" />
            </li>

            <li>
                <hr :class="{ 'bg-blue-500': isStatusActive('processing') || isStatusPassed('processing') }"
                    class="transition-colors duration-300" />
                <div
                    :class="['timeline-start timeline-box', { 'bg-blue-500 text-white font-bold': isStatusActive('processing') }]">
                    Đang xử lý
                </div>
                <div class="timeline-middle">
                    <svg v-if="isStatusPassed('processing')" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                        fill="currentColor" class="h-5 w-5">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z"
                            clip-rule="evenodd" />
                    </svg>
                    <div v-else class="w-5 h-5 rounded-full border-2 border-gray-300"></div>
                </div>
                <hr :class="{ 'bg-blue-500': isStatusActive('processing') || isStatusPassed('processing') }"
                    class="transition-colors duration-300" />
            </li>

            <li>
                <hr :class="{ 'bg-blue-500': isStatusActive('delivering') || isStatusPassed('delivering') }"
                    class="transition-colors duration-300" />
                <div
                    :class="['timeline-start timeline-box', { 'bg-blue-500 text-white font-bold': isStatusActive('delivering') }]">
                    Đang giao hàng
                </div>
                <div class="timeline-middle">
                    <svg v-if="isStatusPassed('delivering')" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                        fill="currentColor" class="h-5 w-5">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z"
                            clip-rule="evenodd" />
                    </svg>
                    <div v-else class="w-5 h-5 rounded-full border-2 border-gray-300"></div>
                </div>
                <hr :class="{ 'bg-blue-500': isStatusActive('delivering') || isStatusPassed('delivering') }"
                    class="transition-colors duration-300" />
            </li>

            <li>
                <hr :class="{ 'bg-blue-500': isStatusActive('delivered') || isStatusPassed('delivered') }"
                    class="transition-colors duration-300" />
                <div
                    :class="['timeline-start timeline-box', { 'bg-blue-500 text-white font-bold': isStatusActive('delivered') }]">
                    Đã giao hàng
                </div>
                <div class="timeline-middle">
                    <svg v-if="status === 'delivered'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                        fill="currentColor" class="h-5 w-5">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z"
                            clip-rule="evenodd" />
                    </svg>
                    <div v-else class="w-5 h-5 rounded-full border-2 border-gray-300"></div>
                </div>
            </li>
        </ul>
        <!-- Hiển thị trạng thái hủy đơn nếu có -->
        <div v-else class="timeline-box bg-red-500 text-white p-3 rounded-lg">
            <div class="flex items-center">
                <X class="w-5 h-5 mr-2" />
                Đơn hàng đã bị hủy
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { X } from 'lucide-vue-next'

const props = defineProps<{
    status: 'processing' | 'cancelled' | 'pending' | 'delivering' | 'delivered'
}>();

// Định nghĩa thứ tự các trạng thái
const statusOrder = {
    'pending': 1,
    'processing': 2,
    'delivering': 3,
    'delivered': 4,
    'cancelled': -1 // Trạng thái đặc biệt
};

// Kiểm tra trạng thái hiện tại có đang active không
const isStatusActive = (checkStatus: string) => {
    if (props.status === 'cancelled') return false;
    return props.status === checkStatus;
};

// Kiểm tra trạng thái đã hoàn thành chưa
const isStatusPassed = (checkStatus: string) => {
    if (props.status === 'cancelled') return false;
    return statusOrder[props.status as keyof typeof statusOrder] > statusOrder[checkStatus as keyof typeof statusOrder];
};
</script>
