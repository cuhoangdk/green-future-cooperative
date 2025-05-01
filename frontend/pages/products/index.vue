<template>
    <div class="min-h-screen items-center flex flex-col mt-16 pb-5 lg:mt-0">
        <div class="w-11/12 flex flex-col max-w-7xl gap-2">
            <div>
                <div class="flex flex-wrap gap-2 mt-7">
                    <button @click="selectedCategory = ''; search()"
                        :class="['btn btn-outline btn-sm rounded-full text-[17px]', selectedCategory === '' ? 'btn-active' : '']">
                        Tất cả danh mục
                    </button>
                    <div v-if="categoriesStatus === 'pending'" v-for="n in 7" :key="n"
                        class="skeleton rounded-full w-20 btn btn-sm"></div>
                    <button v-else v-for="category in categories" :key="category?.id"
                        @click="selectedCategory = String(category?.id); search()"
                        :class="['btn btn-outline btn-sm rounded-full text-[17px]', selectedCategory === String(category?.id) ? 'btn-active' : '']">
                        {{ category?.name }}
                    </button>
                </div>
            </div>
            <div>
                <div class="flex justify-between items-center mb-4 gap-3">
                    <h2 class="text-left text-xl font-bold text-green-800">Có {{ products.meta?.total }} sản phẩm</h2>
                    <div class="flex-1 h-[3px] bg-green-500"></div>
                    <div>
                        <select class="select rounded-full" v-model="sortBy" @change="search()">
                            <option value="">Sắp xếp theo</option>
                            <option value="sown_at+desc">Mới nhất</option>
                            <option value="sown_at+asc">Cũ nhất</option>
                            <option value="price+asc">giá</option>

                            <option value="name">Theo tên</option>
                        </select>
                    </div>
                </div>
                <ProductList title="Tất cả sản phẩm" :products="products.products" :meta="products.meta"
                    :show-numbers="true" :links="products.links" :status="status"
                    @page-change="handleProductsPageChange" />
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import type { PaginationMeta, PaginationLinks } from '~/types/api'
import type { Product, ProductCategory } from '~/types/product'

const { searchProducts } = useProducts()
const { getProductCategories } = useProductCategories()
const { $toast } = useNuxtApp()
const route = useRoute()

const perPage = 15
const currentPage = ref(1)

const searchQuery = ref<string>(route.query.search ? route.query.search : '')
const selectedCategory = ref('')
const sortBy = ref('')


// State cho phân trang
const { data, status, error } = await searchProducts({ page: currentPage.value, per_page: perPage, search: searchQuery.value }, AuthType.Guest)
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


const handleProductsPageChange = (page: number) => {
    currentPage.value = page
    search() 
}

async function search() {
    const filters: any = {
        page: currentPage.value,
        per_page: perPage,
        ...(searchQuery.value && { search: searchQuery.value }),
        // ...(selectedStatus.value && { status: selectedStatus.value }),
        ...(selectedCategory.value && { category_id: Number(selectedCategory.value) }),
        // ...(selectedOwner.value && { user_id: Number(selectedOwner.value) }),
        ...(sortBy.value && {
            sort_by: sortBy.value.includes('+') ? sortBy.value.split('+')[0] : sortBy.value,
            sort_direction: sortBy.value.endsWith('+desc') ? 'desc' : 'asc'
        }),
    }

    const { error } = await searchProducts(filters, AuthType.Guest)
    if (error.value) {
        $toast.error('Không thể tải danh sách sản phẩm!')
    }
}

watch(() => route.query.search, async (newSearch) => {
    searchQuery.value = newSearch ? String(newSearch) : ''
    search()
})
</script>