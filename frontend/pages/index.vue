<template>
    <div class="min-h-screen items-center flex flex-col gap-4 pb-5 ">
        <UiCarousel />

        <div
            class="w-11/12 max-w-7xl p-3 my-5 border border-gray-200 shadow-sm rounded-2xl bg-white flex flex-col lg:flex-row items-center">
            <div class="relative w-52 lg:w-64 aspect-square">
                <img src="/images/logo.png" alt="Logo Green Future"
                    class="rounded-2xl p-3 lg:p-5 w-full h-full object-cover" loading="lazy" />
            </div>
            <div class="text-left p-3 lg:p-5 w-full">
                <h2 class="text-2xl font-bold text-green-800 mb-1">Hợp tác xã Tương Lai Xanh</h2>
                <p class="text-gray-700 text-sm">
                    Với phương châm "An toàn người trồng – Sức khỏe người dùng", chúng tôi cam kết mang lại những sản
                    phẩm nông nghiệp chất lượng cao, đảm bảo an toàn cho cả người sản xuất và người tiêu dùng.
                </p>
            </div>
        </div>

        <div class="w-11/12 flex flex-col max-w-7xl gap-2">
            <div>
                <div class="flex justify-between items-center mb-4 gap-3">
                    <h2 class="text-left text-xl font-bold text-green-800">SẢN PHẨM</h2>
                    <div class="flex-1 h-[3px] bg-green-500"></div>
                    <NuxtLink to="/products"
                        class="bg-green-600 text-white font-semibold px-5 py-1 rounded-full hover:bg-green-700">
                        Tất cả
                    </NuxtLink>
                </div>
                <ProductList :products="products.products" :meta="products.meta" :links="products.links"
                    :status="status" @page-change="handleProductsPageChange" />
            </div>
        </div>

        <div class="w-11/12 flex max-w-7xl gap-2">
            <div>
                <div class="flex justify-between items-center mb-4 gap-3">
                    <h2 class="text-left text-xl font-bold text-green-800">BÀI VIẾT</h2>
                    <div class="flex-1 h-[3px] bg-green-500"></div>
                    <NuxtLink to="/posts"
                        class="bg-green-600 text-white font-semibold px-5 py-1 rounded-full hover:bg-green-700">
                        Tất cả
                    </NuxtLink>
                </div>
                <PostList :posts="postsData.posts" :meta="postsData.meta" :links="postsData.links" :status="postStatus"
                    :max-columns="4" @page-change="handlePostsPageChange" />
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import type { PaginationMeta, PaginationLinks } from '~/types/api'
import type { Product } from '~/types/product'
import type { Post } from '~/types/post'

const { getProducts } = useProducts()
const { getPosts } = usePosts()

const perPage = 5
const perPagePost = 4
const currentProductPage = ref(1)
const currentPostPage = ref(1)

// State cho phân trang
const { data, status, error } = await getProducts(currentProductPage.value, perPage, AuthType.Guest)
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
    currentProductPage.value = page
    getProducts(currentProductPage.value, perPage, AuthType.Guest)
}

const { data: posts, status: postStatus, error: postError } = await getPosts(currentPostPage.value, perPagePost, AuthType.Guest)
const postsData = computed<{
    posts: Post[];
    meta: PaginationMeta | null;
    links: PaginationLinks | null;
}>(() => ({
    posts: Array.isArray(posts.value?.data) ? (posts.value.data as Post[]) : posts.value?.data ? [posts.value.data as Post] : [],
    meta: (posts.value?.meta as PaginationMeta) ?? null,
    links: (posts.value?.links as PaginationLinks) ?? null,
}));

const handlePostsPageChange = (page: number) => {
    currentPostPage.value = page
    getPosts(currentPostPage.value, perPagePost, AuthType.Guest)
}
</script>