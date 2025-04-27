<template>
    <div class="p-4">
        <!-- Dashboard filters and date range -->
        <div class="mb-6 p-4 bg-white rounded-lg shadow">
            <div class="flex flex-col lg:flex-row gap-4 items-center justify-between">
                <h2 class="text-lg font-semibold">Thống kê từ {{ formatDate(input.startDate) }} đến {{
                    formatDate(input.endDate) }}</h2>
                <div class="flex space-x-2">
                    <input type="date" v-model="input.startDate" class="input" />
                    <input type="date" v-model="input.endDate" class="input" />
                    <button @click="getStatistics(input.startDate, input.endDate)" class="btn btn-soft btn-primary">
                        Thống kê
                    </button>
                </div>
            </div>
        </div>

        <!-- Summary cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white p-4 rounded-lg shadow">
                <h3 class="text-gray-500 text-sm">Đơn hàng mới</h3>
                <p class="text-2xl font-bold">{{ statistic.new_orders || 0 }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <h3 class="text-gray-500 text-sm">Sản phẩm mới</h3>
                <p class="text-2xl font-bold">{{ statistic.new_products || 0 }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <h3 class="text-gray-500 text-sm">Khách hàng mới</h3>
                <p class="text-2xl font-bold">{{ statistic.new_customers || 0 }}</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <h3 class="text-gray-500 text-sm">Tổng doanh thu</h3>
                <p class="text-2xl font-bold">{{ formatCurrency(statistic.total_revenue) }}</p>
            </div>
        </div>

        <!-- Charts grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Pie Charts -->
            <div v-if="statistic.products_by_status" class="bg-white p-4 rounded-lg shadow">
                <ChartProductStatusPie :data="statistic.products_by_status" />
            </div>

            <div v-if="statistic.orders_by_status" class="bg-white p-4 rounded-lg shadow">
                <ChartOrderStatusPie :data="statistic.orders_by_status" />
            </div>

            <div v-if="statistic.products_by_category" class="bg-white p-4 rounded-lg shadow">
                <ChartProductsByCategory :data="statistic.products_by_category" />
            </div>

            <div v-if="statistic.purchased_by_category" class="bg-white p-4 rounded-lg shadow">
                <ChartPurchasedByCategory :data="statistic.purchased_by_category" />
            </div>

            <!-- Line Chart -->
            <div v-if="statistic.orders_per_day" class="bg-white p-4 rounded-lg shadow md:col-span-2">
                <ChartOrdersPerDay :data="statistic.orders_per_day" />
            </div>

            <div v-if="statistic.top_products_by_revenue" class="bg-white p-4 rounded-lg shadow md:col-span-2">
                <ChartTopProductsRevenue :data="statistic.top_products_by_revenue" />
            </div>

            <!-- <div v-if="statistic.top_products_by_revenue" class="bg-white p-4 rounded-lg shadow md:col-span-2">
                <ChartTopProductsRevenue :data="statistic.top_products_by_quantity" />
            </div> -->

            <!-- Bar Charts - These take full width on all devices -->
            <div v-if="statistic.top_customers_by_revenue" class="bg-white p-4 rounded-lg shadow md:col-span-2">
                <ChartTopCustomersRevenue :data="statistic.top_customers_by_revenue" />
            </div>

            <div v-if="statistic.top_users_by_revenue" class="bg-white p-4 rounded-lg shadow md:col-span-2">
                <ChartTopUsersByRevenue :data="statistic.top_users_by_revenue" />
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
definePageMeta({ title: 'Trang chủ', layout: 'user', })
import type { Statistics } from '~/types/statistics'
const input = reactive({
    startDate: new Date(new Date().setDate(new Date().getDate() - 30)).toISOString().split('T')[0],
    endDate: new Date().toISOString().split('T')[0],
})

const { getStatistics } = useStatistics()
const { data } = await getStatistics(input.startDate, input.endDate)

const statistic = computed<Statistics>(() => data.value?.data ?? {} as Statistics)
</script>