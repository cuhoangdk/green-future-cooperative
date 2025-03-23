<template>
    <section class="w-full"> <!-- Thêm relative để overlay có thể định vị -->
        <!-- Hiển thị sản phẩm sau khi load xong -->
        <div :class="['grid gap-3 mb-4 relative grid-cols-2 md:grid-cols-3 lg:grid-cols-5']">
            <ProductCard v-for="product in products" :key="product.id" :product="product" />
            <!-- Overlay loading -->
            <div v-if="status === 'pending'"
                class="absolute inset-0 flex items-center justify-center bg-gray-100/20 z-10">
                <span class="loading loading-spinner loading-xl"></span>
            </div>
        </div>

        <!-- Phân trang -->
        <UiPagination :meta="meta" :links="links" :show-first-last="showFirstLast" :show-numbers="showNumbers"
            @page-change="onPageChange" />
    </section>
</template>

<script setup lang="ts">
import type { Product } from '~/types/product';
import type { PaginationMeta, PaginationLinks } from '~/types/api';

interface Props {
    products: Product[];
    meta: PaginationMeta | null;
    links: PaginationLinks | null;
    status: string;
    showFirstLast?: boolean;
    showNumbers?: boolean;
}

const props = defineProps<Props>()

const emit = defineEmits<{
    (e: 'page-change', page: number): void
}>();


const onPageChange = (page: number) => {
    emit('page-change', page);
};
</script>