<template>
    <div
        class="bg-white border border-green-100 shadow-sm rounded-lg overflow-hidden hover:shadow-md hover:-translate-y-1 duration-200 relative z-0 h-full flex flex-col">
        <!-- Phần NuxtLink chỉ áp dụng cho hình ảnh và thông tin -->
        <NuxtLink :to="`/products/${product.slug}`" class="flex flex-col flex-grow">
            <img :src="`${img}`" :alt="product.name" class="rounded-lg aspect-[4/3] rounded-b-none object-cover w-full"
                loading="lazy" @error="() => { img = placeholderImage; }" />

            <!-- Product info -->
            <div class="text-center px-3 flex-grow">
                <h3 class="text-lg font-semibold text-green-800">{{ product.name }}</h3>
                <p class="font-bold text-red-600">
                    {{ product?.prices?.[0]?.price && product.pricing_type !== 'contact' ?
                        Math.floor(product.prices[0].price).toLocaleString('vi-VN') : 'Liên hệ' }}
                    <span class="text-sm text-gray-500"
                        v-if="product?.prices?.[0]?.price && product.pricing_type !== 'contact'">/ {{ product.unit.name
                        }}</span>
                </p>
            </div>
        </NuxtLink>

        <!-- Nút Mua tách riêng, không nằm trong NuxtLink -->
        <div class="px-3 pb-2">
            <button @click="addProductToCart" :disabled="isAddingToCart"
                class="bg-green-600 text-white font-semibold w-full py-2 rounded-full hover:bg-green-700 flex items-center justify-center">
                <div class="flex gap-2" v-if="!isAddingToCart">
                    <ShoppingCart class="w-5 h-5 mr-1" />
                    Mua
                </div>
                <span v-else class="flex items-center">
                    <span class="loading loading-spinner loading-sm mr-2"></span> Đang thêm...
                </span>
            </button>
            <p class="text-xs text-center text-gray-500 mt-1">Người trồng: {{ product.user?.full_name }}</p>

        </div>
    </div>
</template>

<script setup lang="ts">
import { ShoppingCart } from 'lucide-vue-next'
import type { Product } from "~/types/product";
import { useToast } from 'vue-toastification';

interface Props {
    product: Product;
}

const toast = useToast();
const { addCartItem } = useCart();
const config = useRuntimeConfig();
const placeholderImage = config.public.placeholderImage;
const backendUrl = config.public.backendUrl;
const props = defineProps<Props>();
const img = ref(`${backendUrl}${props.product.images?.[0]?.image_url ?? placeholderImage}`);
const isAddingToCart = ref(false)

const addProductToCart = async () => {
    try {
        isAddingToCart.value = true
        await addCartItem(props.product.id, 1);
        toast.success('Đã thêm sản phẩm vào giỏ hàng!');
    } catch (error) {
        toast.error('Có lỗi khi thêm sản phẩm vào giỏ hàng!');
    } finally {
        isAddingToCart.value = false
    }
};
</script>