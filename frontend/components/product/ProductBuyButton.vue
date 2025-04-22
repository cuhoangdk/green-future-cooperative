<template>
    <button @click="handleAddToCart" :disabled="isAddingToCart || quantity > props.stockQuantity"
        class="btn bg-green-600 text-white font-semibold w-52 py-2 rounded-full hover:bg-green-700 flex items-center justify-center"
        :class="{ 'opacity-70 cursor-not-allowed': isAddingToCart || quantity > props.stockQuantity }">
        <span v-if="!isAddingToCart" class="flex items-center">
            <ShoppingCart class="w-4 h-4 mr-1" /> Thêm vào giỏ
        </span>
        <span v-else class="flex items-center">
            <span class="loading loading-spinner loading-sm mr-2"></span> Đang thêm...
        </span>
    </button>
</template>

<script setup lang="ts">
import { ShoppingCart } from 'lucide-vue-next'

const props = defineProps<{
    addProductToCart: () => Promise<void> | void
    quantity: number
    stockQuantity: number
    isAddingToCart: boolean
}>()

const handleAddToCart = async () => {
    if (props.addProductToCart) {
        await props.addProductToCart()
    }
}
</script>
