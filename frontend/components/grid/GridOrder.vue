<!-- components/customer/CustomerGrid.vue -->
<template>
    <div class="grid grid-cols-1 gap-4 p-3 md:hidden">
        <div v-for="order in orders" :key="order.id"
            class="card bg-base-100 border border-gray-400 hover:shadow-md transition-shadow cursor-pointer">
            <div  @click="$router.push(`orders/${order.id}/`)" class="card-body p-3">
                <div class="flex justify-between items-start">
                    <h3 class="card-title text-base line-clamp-2">{{ order.order_code }}</h3>
                </div>

                <div class="space-y-1 text-sm">
                    <div class="flex gap-1 items-center">
                        <span v-if="order.status === 'pending'" class="badge badge-neutral">Chờ xác nhận</span>
                        <span v-else-if="order.status === 'processing'" class="badge badge-warning">Đang xử lý</span>
                        <span v-else-if="order.status === 'delivered'" class="badge badge-primary">Đang giao</span>
                        <span v-else-if="order.status === 'cancelled'" class="badge badge-error">Đã hủy</span>
                    </div>
                    <div class="flex gap-2 items-center">
                        <User class="w-4 h-4 text-gray-500" /> Người đặt:
                        <span>{{ order.full_name }} </span>
                    </div>
                    <div class="flex gap-2 items-center">
                        <Phone class="w-4 h-4 text-gray-500" /> Số điện thoại:
                        <span>{{ order.phone_number }}</span>
                    </div>
                    <div class="flex gap-2 items-center">
                        <DollarSign class="w-4 h-4 text-gray-500" /> Tổng tiền:
                        <span>{{ formatCurrency(order.total_price) }}</span>
                    </div>
                    <div class="flex gap-2 items-center">
                        <span>{{ addressData[order.id] }}</span>
                    </div>
                </div>
                <div class="card-actions justify-end border-t pt-2 border-gray-100">
                    <div class="flex space-x-1 items-center">
                        <UiViewButton :to="`orders/${order.id}/view`" />
                        <UiEditButton :to="`orders/${order.id}/edit`" />
                        <UiCancelButton :to="`orders/${order.id}/cancel`" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import type { Order } from '~/types/order'
import { formatNumber, formatCurrency } from '~/utils/common'
import { User, Phone, MapPin, DollarSign } from 'lucide-vue-next'


defineProps<{
    orders: Order[]
    addressData: { [key: number]: string }
}>()
</script>