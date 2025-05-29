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
                <h2 class="text-2xl font-bold text-green-800 mb-3">Hợp tác xã Tương Lai Xanh</h2>
                <p class="text-gray-700">
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
                <ProductSlide :products="products.products" :status="status"  :auto-play="true"
                    :auto-play-interval="5000" />
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

        <div class="w-11/12 max-w-7xl p-5 my-5 border border-gray-200 shadow-sm rounded-2xl bg-white">
            <h2 class="text-2xl font-bold text-green-800 mb-3">🌿 Trải nghiệm đặc biệt</h2>
            <p class="text-gray-700">
                Khách hàng có thể ghé thăm HTX vào cuối tuần để trải nghiệm hái rau và chăm sóc rau ngay tại vườn.
                Đây là cơ hội tuyệt vời để tận hưởng không gian xanh mát và lưu lại những bức ảnh đẹp cùng những
                khoảnh khắc đáng nhớ. Nếu bạn muốn tham quan, vui lòng liên hệ với fanpage của HTX: Green Future
                Cooperative.
            </p>
            <p class="text-gray-700 mt-2">
                Nếu bạn yêu thích sản phẩm, hãy like và theo dõi fanpage của HTX để cập nhật những sản phẩm mới
                trong thời gian tới.
            </p>
        </div>
    </div>
</template>

<script setup lang="ts">
import type { PaginationMeta, PaginationLinks } from '~/types/api'
import type { Product } from '~/types/product'
import type { Post } from '~/types/post'

const { getProducts } = useProducts()
const { getPosts } = usePosts()

const perPage = 30
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