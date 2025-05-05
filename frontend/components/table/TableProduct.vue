<template>
    <div class="hidden md:block w-full overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-100 text-gray-700">
                    <th class="py-2 pl-2 w-[20%] text-left">Tên</th>
                    <th class="py-2 w-[15%] text-left">Thành viên</th>
                    <th class="py-2 w-[10%] text-left">Giá (cơ bản)</th>
                    <th class="py-2 w-[10%] text-left">Trong kho</th>
                    <th class="py-2 w-[10%] text-left">Đã bán</th>
                    <th class="py-2 w-[10%] text-left">Trạng thái</th>
                    <th class="py-2 w-[30%] text-left">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="product in products" :key="product.id"
                    @click="$router.push(`products/${product.id}/`)"
                    class="border-b border-gray-100 hover:bg-gray-200 cursor-pointer">
                    <td class="py-1 pl-2">
                        <span class="text-sm">{{ product.id }}</span><br/>
                        <span class="font-semibold">{{ product.name }}</span>
                    </td>
                    <td class="py-1">{{ product.user ? product.user.full_name : 'N/A' }}</td>
                    <td class="py-1">
                        <span v-if="product.prices && product.prices.length > 0">
                            <span v-if="product.pricing_type === 'contact'">Liên hệ</span>
                            <span v-else>{{ formatCurrency(product.prices[0].price) }}</span>
                        </span>
                        <span v-else>Chưa bán</span>
                    </td>
                    <td class="py-1">
                        <span v-if="product.stock_quantity == 0" class="text-red-500">Bán hết</span>
                        <span v-else-if="product.stock_quantity < 10" class="text-red-500">
                            {{ formatNumber(product.stock_quantity) }} {{ product.unit.name }}
                        </span>
                        <span v-else>
                            {{ formatNumber(product.stock_quantity) }} {{ product.unit.name }}
                        </span>
                    </td>
                    <td class="py-1">
                        <span>{{ formatNumber(product.sold_quantity) }} {{ product.unit.name }}</span>
                    </td>
                    <td class="py-1">
                        <span v-if="product.status === 'growing'" class="badge badge-primary">Đang trồng</span>
                        <span v-else-if="product.status === 'selling'" class="badge badge-secondary">Đang bán</span>
                        <span v-else class="badge badge-error">Ngừng bán</span>
                    </td>
                    <td class="py-1">
                        <div class="flex space-x-1 items-center">
                            <UiLogButton @click.stop :to="`products/${product.id}/logs`" />
                            <UiPublishButton v-if="product.status === 'growing'" @click.stop :to="`products/${product.id}/publish`" />
                            <UiQuickSellButton v-else-if="product.status === 'selling'" @click.stop :to="`products/${product.id}/sell`" />
                            <UiQRCodeButton @click.stop :to="`products/${product.id}/qrcode`" />
                            <UiEditButton @click.stop :to="`products/${product.id}/`" />
                            <UiDeleteButton @click.stop="() => onDelete(product.id)" />
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup lang="ts">
import { UiQuickSellButton } from '#components';
import type { Product } from '~/types/product'
import { formatNumber, formatCurrency } from '~/utils/common'

defineProps<{
    products: Product[]
    onDelete: (id: string) => void
}>()
</script>