<!-- components/customer/CustomerGrid.vue -->
<template>
    <div class="grid grid-cols-1 gap-4 p-3 md:hidden">
        <div v-for="product in products" :key="product.id"
            @click="$router.push(`products/${product.id}/`)"
            class="card bg-base-100 border border-gray-400 hover:shadow-md transition-shadow cursor-pointer">
            <div class="card-body p-3">
                <div class="flex justify-between items-start">
                    <h3 class="card-title text-base">{{ product.name }}</h3>
                    <span v-if="product.status === 'growing'" class="badge badge-primary">Đang trồng</span>
                    <span v-else-if="product.status === 'selling'" class="badge badge-secondary">Đang bán</span>
                    <span v-else class="badge badge-error">Ngừng hoạt động</span>
                </div>

                <div class="space-y-1 text-sm">
                    <div class="flex gap-2 items-center">
                        <Fingerprint class="w-4 h-4 text-gray-500" /> Mã:
                        <span>{{ product.product_code }}</span>

                    </div>
                    <div class="flex gap-2 items-center">
                        <User class="w-4 h-4 text-gray-500" /> Thành viên:
                        <span>{{ product.user?.full_name }}</span>
                    </div>
                    <div class="flex gap-2 items-center">
                        <DollarSign class="w-4 h-4 text-gray-500" /> Giá cả:
                        <span v-if="product.prices && product.prices.length > 0">
                            <span v-if="product.pricing_type === 'contact'">Liên hệ</span>
                            <span v-else>{{ formatCurrency(product.prices[0].price) }}</span>
                        </span>
                        <span v-else>Chưa bán</span>
                    </div>
                    <div class="flex gap-2 items-center">
                        <Leaf class="w-4 h-4 text-gray-500" /> Số lượng:
                        <span v-if="product.stock_quantity === 0" class="text-red-500">Bán hết</span>
                        <span v-else-if="product.stock_quantity < 10" class="text-red-500">
                            {{ formatNumber(product.stock_quantity) }} {{ product.unit.name }}
                        </span>
                        <span v-else>
                            {{ formatNumber(product.stock_quantity) }} {{ product.unit.name }}
                        </span>
                    </div>
                </div>

                <div class="card-actions justify-end border-t pt-2 border-gray-100">
                    <div class="flex space-x-1 items-center">
                        <UiLogButton :to="`products/${product.id}/logs`" />
                        <UiPublishButton v-if="product.status === 'growing'" :to="`products/${product.id}/publish`" />
                        <UiQRCodeButton :to="`products/${product.id}/qrcode`" />
                        <UiEditButton :to="`products/${product.id}/`" />
                        <UiDeleteButton :on-click="() => onDelete(product.id)" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import type { Product } from '~/types/product'
import { Leaf, DollarSign, User, Fingerprint  } from 'lucide-vue-next'
import { formatNumber, formatCurrency } from '~/utils/common'

defineProps<{
    products: Product[]
    onDelete: (id: number) => void
}>()
</script>