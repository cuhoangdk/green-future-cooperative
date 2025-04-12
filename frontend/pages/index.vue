<template>
    <div class="min-h-screen items-center flex flex-col gap-4 mt-16 pb-5 lg:my-5">
        <div class="w-11/12 flex flex-col max-w-7xl gap-2">
            <div>
                <div class="flex justify-between items-center mb-4 gap-3">
                    <h2 class="text-left text-xl font-bold text-green-800">SẢN PHẨM MỚI</h2>
                    <div class="flex-1 h-[3px] bg-green-500"></div>
                    <NuxtLink to="/products"
                        class="bg-green-600 text-white font-semibold px-5 py-1 rounded-full hover:bg-green-700">
                        Xem tất cả
                    </NuxtLink>
                </div>
                <ProductList title="Tất cả sản phẩm" :products="products.products" :meta="products.meta"
                    :links="products.links" :status="status" @page-change="handleProductsPageChange" />
            </div>
        </div>

        <div class="w-11/12 flex max-w-7xl gap-2">
            <div>
                <div class="flex justify-between items-center mb-4 gap-3">
                    <h2 class="text-left text-xl font-bold text-green-800">BÀI VIẾT</h2>
                    <div class="flex-1 h-[3px] bg-green-500"></div>
                    <NuxtLink to="/posts"
                        class="bg-green-600 text-white font-semibold px-5 py-1 rounded-full hover:bg-green-700">
                        Xem tất cả
                    </NuxtLink>
                </div>
                <PostList :posts="postsData.posts" :meta="postsData.meta" :links="postsData.links" :status="postStatus" :max-columns="4"
                    @page-change="handlePostsPageChange" />
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

const perPage = 10
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