<template>
    <div class="min-h-screen items-center flex flex-col mt-16 pb-5 lg:mt-0">
        <div class="w-11/12 flex flex-col max-w-7xl gap-2">
            <div>
                <div class="flex flex-wrap gap-2 mt-7">
                    <div v-if="categoriesStatus === 'pending'" v-for="n in 7" :key="n" class="skeleton rounded-full w-20 btn btn-sm"></div>
                    <button v-else v-for="category in categories" :key="category?.id"
                        class="btn btn-outline btn-sm rounded-full text-[17px]">
                        {{ category?.name }}
                    </button>
                </div>
            </div>
            <div>
                <div class="flex justify-between items-center mb-4 gap-3">
                    <h2 class="text-left text-xl font-bold text-green-800">Có {{ products.meta?.total }} sản phẩm</h2>
                    <div class="flex-1 h-[3px] bg-green-500"></div>
                    <div>
                        <select class="select rounded-full" v-model="sortOrder"
                            @change="handleSortChange">
                            <option value="newest">Mới nhất</option>
                            <option value="low-to-high">Giá thấp đến cao</option>
                            <option value="high-to-low">Giá cao đến thấp</option>
                        </select>
                    </div>
                </div>
                <ProductList title="Tất cả sản phẩm" :products="products.products" :meta="products.meta" :show-numbers="true"
                    :links="products.links" :status="status" @page-change="handleProductsPageChange" />
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import type { PaginationMeta, PaginationLinks } from '~/types/api'
import type { Product, ProductCategory } from '~/types/product'

const { getProducts, searchProducts } = useProducts()
const { getProductCategories } = useProductCategories()

const perPage = 16
const currentPage = ref(1)
const sortOrder = ref('newest')

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

// State cho danh mục sản phẩm
const { data: categoriesData, status: categoriesStatus, error: categoriesError } = await getProductCategories()
const categories = computed<ProductCategory[]>(() => categoriesData.value?.data ?? [])

const handleSortChange = () => {
}

const handleProductsPageChange = (page: number) => {
    getProducts(page, perPage, AuthType.Guest)
}
</script>