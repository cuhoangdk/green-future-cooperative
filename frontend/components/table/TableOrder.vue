<template>
    <div class="hidden md:block w-full overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-100 text-gray-700">
                    <th class="py-2 pl-2 w-[15%] text-left">Mã</th>
                    <th class="py-2 w-[10%] text-left">Người nhận</th>
                    <th class="py-2 w-[25%] text-left">Địa chỉ</th>
                    <th class="py-2 w-[10%] text-left">Tổng tiền</th>
                    <th class="py-2 w-[10%] text-left">Trạng thái</th>
                    <th class="py-2 w-[10%] text-left">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="order in orders" :key="order.id"
                    @click="$router.push(`orders/${order.id}/`)"
                    class="border-b border-gray-100 hover:bg-gray-200 cursor-pointer">
                    <td class="py-1 pl-2">{{ order.id }}<br/>{{ formatDateTime(order.created_at) }}</td>
                    <td class="py-1">
                        <div v-if="order.full_name">
                            {{ order.full_name }}<br/>{{ order.phone_number }}
                        </div>
                        <div v-else>
                            <User class="w-4 h-4 text-gray-500" /> Bán bên ngoài
                        </div>
                    </td>
                    <td class="py-1">
                        <div v-if="order.ward">
                            {{ addressData[order.id] }}
                        </div>
                        <div v-else>
                        <span>{{ order.items[0]?.product_snapshot.product_name || 'N/A' }}</span>
                        </div>
                    </td>
                    <td v-if="currentUser?.is_super_admin" class="py-1">{{ formatCurrency(order.final_total_amount) }}</td>
                    <td v-else class="py-1">{{formatCurrency(Number(order.items.filter(item => !item.flag).reduce((sum, item) => sum + Number(item.total_item_price), 0))) }}</td>
                    <td class="py-1">
                        <OrderStatus :status="order.status" />
                    </td> 
                    <td class="py-1">
                        <div class="flex space-x-1 items-center" @click.stop>
                            <UiEditButton v-if="displayEditButton" :to="`orders/${order.id}`" />
                            <UiCancelButton  v-if="order.status != 'cancelled' && order.status != 'delivered' && currentUser?.is_super_admin" :to="`orders/${order.id}/cancel`"/>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup lang="ts">
import type { Order } from '~/types/order'
const { currentUser } = useUserAuth()

defineProps<{
    orders: Order[]
    addressData: { [key: string]: string }
    displayEditButton?: boolean
}>()
</script>