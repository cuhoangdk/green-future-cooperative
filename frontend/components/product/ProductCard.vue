<template>
    <NuxtLink :to="`/products/${product.slug}`"
        class="bg-white border border-green-100 shadow-sm rounded-lg overflow-hidden hover:shadow-md hover:-translate-y-1 duration-200 relative z-0 h-full flex flex-col">
        <img :src="`${img}`" :alt="product.name" class="rounded-lg aspect-[4/3] rounded-b-none object-cover w-full"
            loading="lazy" @error="() => { img = placeholderImage; }" />

        <!-- Product info -->
        <div class="text-center px-3 pb-2">
            <h3 class="text-lg font-semibold text-green-800">{{ product.name }}</h3>
            <p class="font-bold text-red-600">
                {{ product?.prices?.[0]?.price && product.pricing_type !== 'contact' ? Math.floor(product.prices[0].price).toLocaleString('vi-VN') : 'Liên hệ' }} <span class="text-sm text-gray-500" v-if="product?.prices?.[0]?.price && product.pricing_type !== 'contact'">/ {{ product.unit.name }}</span>
            </p>
            <button
                class="bg-green-600 text-white text-sm w-10/12 py-2 mt-2 rounded-full hover:bg-green-700 transition-colors duration-200 flex items-center justify-center mx-auto">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-1.6 8M17 13l1.6 8M9 21h6"></path>
                </svg>
                Mua
            </button>
            <p class="text-xs text-gray-500 mt-1">Người trồng: anh Hiệu</p>
        </div>
    </NuxtLink>
</template>

<script setup lang="ts">
import type { Product } from "~/types/product";
import { useRuntimeConfig } from "#app";

interface Props {
    product: Product;
}

const config = useRuntimeConfig();
const placeholderImage = config.public.placeholderImage;
const backendUrl = config.public.backendUrl;
const props = defineProps<Props>();
const img = ref(`${backendUrl}${props.product.images?.[0]?.image_url ?? placeholderImage}`);
</script>
