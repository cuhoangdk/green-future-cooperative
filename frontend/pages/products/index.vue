<template>
    <div class="min-h-screen items-center flex flex-col mt-16 pb-5 lg:mt-0">
        <div class="w-11/12 flex max-w-7xl gap-5">
            <div class="w-4/5">
                <ProductList title="Tất cả sản phẩm" :products="products.products" :meta="products.meta"
                    :links="products.links" :status="status" 
                    @page-change="handleProductsPageChange" />
            </div>
            <div class="w-1/5">
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue'
import type { PaginationMeta, PaginationLinks } from '~/types/api'
import type { Product } from '~/types/product'

const { getProducts } = useProducts()

const perPage = 16
const currentPage = ref(1)

// State cho phân trang
const { data, status, error } = await getProducts(currentPage.value, perPage, AuthType.Guest)
const products = computed<{
    products: Product[];
    meta: PaginationMeta | null;
    links: PaginationLinks | null;
}>(() => ({
    products: Array.isArray(data.value?.data) ? (data.value.data as Product[]) : data.value?.data ? [data.value.data as Product] : [],
    meta: (data.value?.meta as PaginationMeta) ?? null,
    links: (data.value?.links as PaginationLinks) ?? null,
}));

const handleProductsPageChange = (page: number) => {
    getProducts(page, perPage, AuthType.Guest)
}
</script>