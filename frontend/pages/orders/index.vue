<template>
    <div class="min-h-screen items-center flex flex-col mt-16 p-2 lg:mt-0">
        <div class="w-full lg:w-11/12 bg-white border border-gray-200 rounded-2xl shadow-sm">
            <div>
                <h1 class="text-xl m-4 font-bold">Danh sách đơn hàng</h1>
            </div>
            <div class="relative">
                <div v-if="status === 'pending'"
                    class="absolute inset-0 bg-gray-50 opacity-25 flex justify-center items-center z-10">
                    <span class="loading loading-spinner loading-lg"></span>
                </div>
                <TableOrder :orders="orders.orders" :addressData="addressData" />
                <GridOrder :orders="orders.orders" :addressData="addressData" />
                <div class="flex flex-col sm:flex-row justify-between items-center m-4 gap-2">
                    <div class="flex items-center space-x-2">
                        <p class="text-sm text-gray-600">{{ orders.orders.length }} / {{ orders.meta?.total }}</p>
                        <!-- <select v-model="perPage" class="select select-sm select-primary w-18" @change="search">
                    <option v-for="n in [10, 25, 50, 100]" :value="n" :key="n">{{ n }}</option>
                </select> -->
                    </div>
                    <UiPagination :links="orders.links" :meta="orders.meta" :show-first-last="true" :show-numbers="true"
                        @page-change="handlePageChange" />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import type { Order } from '~/types/order'
import type { PaginationMeta, PaginationLinks } from '~/types/api'

const currentPage = ref(Number(useRoute().query.page) || 1)
const perPage = ref(10)
const { getFullAddressName } = useVietnamAddress()
const { getOrders } = useOrder()
const { data, status } = await getOrders(currentPage.value, perPage.value)
const orders = computed<{ orders: Order[], meta: PaginationMeta | null, links: PaginationLinks | null }>(() => ({
    orders: Array.isArray(data.value?.data) ? data.value.data : data.value?.data ? [data.value.data] : [],
    meta: data.value?.meta ?? null,
    links: data.value?.links ?? null,
}))

const addressData = ref<{ [key: string]: string }>({})
watch(() => orders.value.orders, async (newData) => {
    for (const order of newData) {
        if (order.ward) {
            addressData.value[order.id] = `${order.street_address}, ${await getFullAddressName(order.ward)}`
        }
    }
}, { immediate: true })

const handlePageChange = (page: number) => {
    currentPage.value = page
    getOrders(currentPage.value, perPage.value)
}
</script>