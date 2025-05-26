<!-- components/customer/CustomerGrid.vue -->
<template>
    <div class="grid grid-cols-1 gap-4 p-3 md:hidden">
        <div v-for="order in orders" :key="order.id"
            class="card bg-base-100 border border-gray-400 hover:shadow-md transition-shadow cursor-pointer">
            <div @click="$router.push(`orders/${order.id}/`)" class="card-body p-3">
                <div class="flex justify-between items-start">
                    <h3 class="card-title text-base line-clamp-2">{{ order.id }}</h3>
                </div>

                <div class="space-y-1 text-sm">
                    <div class="flex gap-1 items-center">
                        <OrderStatus :status="order.status" />
                    </div>
                    <div v-if="order.full_name" class="flex gap-2 items-center">
                        <User class="w-4 h-4 text-gray-500" /> Người đặt:
                        <span>{{ order.full_name }} </span>
                    </div>
                    <div v-if="order.phone_number" class="flex gap-2 items-center">
                        <Phone class="w-4 h-4 text-gray-500" /> Số điện thoại:
                        <span>{{ order.phone_number }}</span>
                    </div>
                    <div class="flex gap-2 items-center">
                        <DollarSign class="w-4 h-4 text-gray-500" /> Tổng tiền:
                        <span>{{ formatCurrency(order.final_total_amount) }}</span>
                    </div>
                    <div v-if="order.ward" class="flex gap-2 items-center">
                        <span>{{ addressData[order.id] }}</span>
                    </div>
                    <div v-else class="flex gap-2 items-center">
                        <User class="w-4 h-4 text-gray-500" />
                        <span>Bán bên ngoài</span>
                    </div>
                </div>
                <div class="card-actions justify-end border-t pt-2 border-gray-100">
                    <div class="flex space-x-1 items-center">
                        <UiEditButton :to="`orders/${order.id}/edit`" />
                        <UiCancelButton v-if="order.status != 'cancelled' && order.status != 'delivered' && currentUser?.is_super_admin" :to="`orders/${order.id}/cancel`"/>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import type { Order } from '~/types/order'
import { User, Phone, DollarSign } from 'lucide-vue-next'

const { currentUser } = useUserAuth()

defineProps<{
    orders: Order[]
    addressData: { [key: string]: string }
}>()
</script>