<template>
    <div class="hidden md:block w-full overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-100 text-gray-700">
                    <th class="py-2 pl-2 w-[15%] text-left">Mã</th>
                    <th class="py-2 w-[10%] text-left">Người nhận</th>
                    <th class="py-2 w-[10%] text-left">Tổng tiền</th>
                    <th class="py-2 w-[25%] text-left">Địa chỉ</th>
                    <th class="py-2 w-[10%] text-left">Trạng thái</th>
                    <th class="py-2 w-[10%] text-left">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="order in orders" :key="order.id"
                    @click="$router.push(`orders/${order.id}/`)"
                    class="border-b border-gray-100 hover:bg-gray-200 cursor-pointer">
                    <td class="py-1 pl-2">{{ order.order_code }}</td>
                    <td class="py-1">{{ order.full_name }}<br/>{{ order.phone_number }}</td>
                    <td class="py-1">{{ formatCurrency(order.final_total_amount) }}</td>
                    <td class="py-1">{{ addressData[order.id] }}</td>
                    <td class="py-1">
                        <span v-if="order.status === 'pending'" class="badge badge-neutral">Chờ xác nhận</span>
                        <span v-else-if="order.status === 'processing'" class="badge badge-warning">Đang xử lý</span>
                        <span v-else-if="order.status === 'delivered'" class="badge badge-primary">Đang giao</span>
                        <span v-else-if="order.status === 'cancelled'" class="badge badge-error">Đã hủy</span>
                    </td>
                    <td class="py-1">
                        <div class="flex space-x-1 items-center" @click.stop>
                            <UiEditButton v-if="displayEditButton" :to="`orders/${order.id}`" />
                            <UiCancelButton v-if="order.status != 'cancelled'" :to="`orders/${order.id}/cancel`"/>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup lang="ts">
import type { Order } from '~/types/order'
import { formatNumber, formatCurrency } from '~/utils/common'

defineProps<{
    orders: Order[]
    addressData: { [key: number]: string }
    displayEditButton?: boolean
}>()
</script>