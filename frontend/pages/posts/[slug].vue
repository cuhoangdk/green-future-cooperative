<template>
    <div class="min-h-screen items-center flex flex-col pb-5 ">
        <!-- Skeleton khi tải bài chính -->
        <div v-if="postStatus === 'pending'" class="w-11/12 max-w-7xl mt-5">
            <div class="skeleton h-12 bg-gray-300 rounded w-3/4 mb-2 mt-8"></div>
            <div class="skeleton h-4 bg-gray-300 rounded w-1/2 mb-4"></div>
            <div class="skeleton h-64 bg-gray-300 rounded mb-4 mt-10"></div>
            <div class="skeleton h-4 bg-gray-300 rounded w-full mb-2"></div>
            <div class="skeleton h-4 bg-gray-300 rounded w-5/6 mb-2"></div>
            <div class="skeleton h-4 bg-gray-300 rounded w-4/6 mb-2"></div>
        </div>

        <!-- Nội dung khi có bài viết -->
        <template v-else-if="post">
            <!-- Tiêu đề bài viết -->
            <div class="lg:w-full w-11/12 max-w-7xl mt-5 flex flex-col justify-center items-center">
                <h1 class="text-4xl text-left font-semibold lg:w-10/12 w-full mt-5">
                    {{ post.title }}
                </h1>
                <h2 class="text-sm text-gray-600 text-left font-semibold lg:w-10/12 w-full mt-3">
                    {{ post.category?.name }}
                </h2>
                <h2 class="text-sm text-gray-600 text-left font-semibold lg:w-10/12 w-full mt-3">
                    {{ formatDate(post.published_at) }}
                </h2>
                <hr class="border-gray-300 w-full mt-4" />
            </div>

            <!-- Nội dung bài viết -->
            <div class="w-11/12 max-w-7xl flex flex-col lg:flex-row justify-between items-start gap-x-5 mt-5">
                <!-- Nội dung chính -->
                <div class="lg:w-3/4 w-full max-w-7xl mt-5">
                    <div v-html="post.content" class="text-left"></div>
                    <div v-html="`<em>${post.user?.full_name}</em>`" class="mt-3 font-bold text-green-800"></div>
                </div>

                <!-- Bài viết nổi bật -->
                <div class="lg:w-1/4 w-full max-w-7xl mt-5">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-left text-xl font-bold text-green-800">Bài viết nổi bật</h2>
                        <div class="flex-1 h-[3px] bg-green-300 mx-4"></div>
                    </div>
                    <div v-if="featuredPostsStatus === 'pending'" class="space-y-4">
                        <div v-for="n in 3" :key="n" class="flex space-x-4">
                            <div class="skeleton w-1/2 h-24 bg-gray-300 rounded"></div>
                            <div class="w-1/2 space-y-2">
                                <div class="skeleton h-4 bg-gray-300 rounded w-3/4"></div>
                                <div class="skeleton h-4 bg-gray-300 rounded w-1/2"></div>
                            </div>
                        </div>
                    </div>
                    <div v-else>
                        <NuxtLink v-for="featuredPost in featuredPosts" :key="featuredPost.id"
                            :to="`/posts/${featuredPost.slug}`"
                            class="bg-[#FFFFFF] overflow-hidden duration-200 flex items-start mt-3">
                            <div class="w-1/2 h-full border border-green-100 rounded overflow-hidden">
                                <img :src="`${backendUrl}${featuredPost.featured_image}`" :alt="featuredPost.title"
                                    class="min-h-24 w-full rounded aspect-video object-cover transition-transform duration-200 hover:scale-105"
                                    loading="lazy" />
                            </div>
                            <div class="w-1/2 px-2 py-0">
                                <h3
                                    class="text-left font-semibold text-green-800 hover:text-green-600 duration-200 line-clamp-4">
                                    {{ featuredPost.title }}
                                </h3>
                            </div>
                        </NuxtLink>
                    </div>
                </div>
            </div>

            <!-- Bài viết liên quan -->
            <div class="w-11/12 max-w-7xl flex flex-col lg:flex-row gap-5">
                <div class="w-full lg:w-3/4">
                    <div class="flex justify-between items-center my-5 gap-3">
                        <h2 class="text-left text-xl font-bold text-green-800">BÀI VIẾT</h2>
                        <div class="flex-1 h-[3px] bg-green-500"></div>
                    </div>
                    <PostList :posts="relatedPosts.posts" :meta="relatedPosts.meta" status=""
                        :links="relatedPosts.links" @page-change="handlePageChange" />
                </div>
                <div class="w-full lg:w-1/4 mt-5">
                    <PostCategoryList />
                </div>
            </div>
        </template>

        <!-- Không tìm thấy bài viết -->
        <div v-else class="flex justify-center items-center min-h-[400px]">
            <p class="text-gray-600">Không tìm thấy bài viết</p>
        </div>
    </div>
</template>

<script setup lang="ts">
import type { Post } from '~/types/post'
import type { PaginationMeta, PaginationLinks } from '~/types/api'
import { usePosts } from '~/composables/usePosts'

const config = useRuntimeConfig();
const placeholderImage = config.public.placeholderImage;
const backendUrl = config.public.backendUrl;
const route = useRoute()
const slug = String(route.params.slug)
const perPage = 3 // Số bài viết mỗi trang
const currentPage = ref(1) // Trang hiện tại

// Khởi tạo usePosts
const { getPostBySlug, getFeaturedPosts, getPostsByCategoryId } = usePosts()

// Lấy bài đăng chính
const { data: postData, status: postStatus, error: postError } = await getPostBySlug(slug)
const post = computed<Post | null>(() => Array.isArray(postData.value?.data) ? postData.value.data[0] : postData.value?.data || null)

// Lấy bài viết nổi bật
const { data: featuredPostsData, status: featuredPostsStatus, error: featuredPostsError } = await getFeaturedPosts()
const featuredPosts = computed<Post[]>(() => Array.isArray(featuredPostsData.value?.data) ? featuredPostsData.value.data : featuredPostsData.value?.data ? [featuredPostsData.value.data] : [])

// Lấy bài viết liên quan (dựa trên category của bài chính)
const relatedPostsData = ref<any>(null) // Lưu trữ dữ liệu liên quan
const fetchRelatedPosts = async (categoryId: number, page: number) => {
    const { data, status, error } = await getPostsByCategoryId(categoryId, page, perPage)
    relatedPostsData.value = data.value
    if (error.value) {
        console.error('Failed to load related posts:', error.value)
    }
}

// Tải bài viết liên quan khi post sẵn sàng
watch(
    () => post.value?.category?.id,
    async (categoryId) => {
        if (categoryId) {
            await fetchRelatedPosts(categoryId, currentPage.value)
        }
    },
    { immediate: true }
)

// Computed properties cho bài viết liên quan
const relatedPosts = computed<{ posts: Post[]; meta: PaginationMeta | null; links: PaginationLinks | null }>(() => ({
    posts: Array.isArray(relatedPostsData.value?.data) ? relatedPostsData.value.data : relatedPostsData.value?.data ? [relatedPostsData.value.data] : [],
    meta: relatedPostsData.value?.meta ?? null,
    links: relatedPostsData.value?.links ?? null
}))

// Xử lý sự kiện thay đổi trang
const handlePageChange = async (page: number) => {
    currentPage.value = page
    if (post.value?.category?.id) {
        await fetchRelatedPosts(post.value.category.id, page)
    }
}

useHead({
  title: `${post.value?.title} - Hợp tác xã Tương Lai Xanh`,
  meta: [
    { name: 'description', content: post.value?.summary },
    { name: 'keywords', content: post.value?.category?.name },
    { property: 'og:title', content: post.value?.title },
    { property: 'og:description', content: post.value?.summary },
    { property: 'og:image', content: `${backendUrl}${post.value?.featured_image}` },
    { property: 'og:url', content: `${config.public.frontendUrl}/posts/${post.value?.slug}` },
  ],
});
</script>