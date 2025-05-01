<template>
    <div>
        <ul class="timeline timeline-vertical lg:timeline-horizontal w-full mt-4 lg:-mb-14 min-h-[200px]">
            <li v-if="status === 'pending'">
                <span class="loading loading-spinner loading-lg"></span>
            </li>
            <li v-else v-for="(history, index) in [...histories].reverse()" :key="index">
                <hr v-if="index < histories.length" />
                <div class="timeline-start timeline-box">
                    <div class="font-bold text-sm">{{ formatStatus(history.status) }}</div>
                    <div class="text-xs">{{ history.notes }}</div>
                    <div v-if="history.changeable_type === 'App\\Models\\User'">
                        <NuxtLink :to="`/admin/users/${history.changeable?.id}/edit`" class="text-sm mt-1">{{
                            history.changeable?.full_name }}</NuxtLink>
                    </div>
                    <div v-else-if="history.changeable_type === 'App\\Models\\Customer'">
                        <NuxtLink :to="`/admin/customers/${history.changeable?.id}/edit`" class="text-sm mt-1">{{
                            history.changeable?.full_name }}</NuxtLink>
                    </div>
                </div>
                <div class="timeline-middle">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                        class="h-5 w-5 text-primary">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="timeline-end">
                    {{ formatDateTime(history.created_at) }}
                </div>
                <hr v-if="index < histories.length - 1" />
            </li>
        </ul>
    </div>
</template>

<script setup lang="ts">
import type { OrderHistory } from '~/types/order';

const { getOrderHistories } = useAdminOrder();

const props = defineProps<{
    orderId: string;
}>();
const { data, status } = await getOrderHistories(props.orderId);
const histories = computed<OrderHistory[]>(() => Array.isArray(data.value?.data) ? data.value.data : data.value ? [data.value.data] : [])

</script>