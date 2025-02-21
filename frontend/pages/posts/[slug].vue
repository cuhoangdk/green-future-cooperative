<script setup lang="ts">
definePageMeta({
    layout: "customer",
});

import { ref, onMounted } from "vue";
import type { Post } from "~/types/post";
import type { PaginationMeta, PaginationLinks } from "~/types/api";
import { useRuntimeConfig } from "#app";

interface RelatedPosts {
    posts: Post[];
    meta: PaginationMeta | null;
    links: PaginationLinks | null;
}

const route = useRoute();
const isLoadingPost = ref(true);
const isLoadingRelatedPosts = ref(true);
const isLoadingFeaturedPosts = ref(true);
const post = ref<Post | null>(null);
const relatedPosts = ref<RelatedPosts | null>(null);
const featuredPosts = ref<Post[]>([]);
const config = useRuntimeConfig();
const backendUrl = config.public.backendUrl;
const defaultImage = config.defaultImage;

const { fetchPostBySlug, fetchPostByCategoryId, fetchFeaturedPosts } =
    usePosts();

const loadPost = async () => {
    try {
        isLoadingPost.value = true;
        const postResponse = await fetchPostBySlug(route.params.slug as string);
        post.value = postResponse.data as Post;
    } catch (error) {
        console.error("Error loading post:", error);
    } finally {
        isLoadingPost.value = false;
    }
};

const loadRelatedPosts = async (page: number = 1) => {
    if (post.value?.category) {
        try {
            const response = await fetchPostByCategoryId(
                post.value.category.id,
                page,
                4
            );
            relatedPosts.value = {
                posts: response.data,
                meta: response.meta || null,
                links: response.links || null,
            };
        } catch (error) {
            console.error("Error loading related posts:", error);
        } finally {
            isLoadingRelatedPosts.value = false;
        }
    }
};

const loadFeaturedPosts = async () => {
    try {
        isLoadingFeaturedPosts.value = true;
        const featuredResponse = await fetchFeaturedPosts();
        featuredPosts.value = featuredResponse.data;
    } catch (error) {
        console.error("Error loading featured posts:", error);
    } finally {
        isLoadingFeaturedPosts.value = false;
    }
};

const handlePageChange = (page: number, perPage: number) => {
    loadRelatedPosts(page);
};

const loadData = async () => {
    await loadPost();
    await loadFeaturedPosts();
    await loadRelatedPosts();
};

onMounted(loadData);
</script>

<template>
    <main class="min-h-screen bg-white items-center flex flex-col mt-16 pb-5 lg:mt-0">
        <div v-if="isLoadingPost" class="w-11/12 max-w-7xl mt-5">
            <div class="animate-pulse">
                <div class="h-12 bg-gray-300 rounded w-3/4 mb-2 mt-8 ml-18"></div>
                <div class="h-4 bg-gray-300 rounded w-1/2 mb-4 ml-18"></div>
                <div class="h-64 bg-gray-300 rounded mb-4 mt-10"></div>
                <div class="h-4 bg-gray-300 rounded w-full mb-2"></div>
                <div class="h-4 bg-gray-300 rounded w-5/6 mb-2"></div>
                <div class="h-4 bg-gray-300 rounded w-4/6 mb-2"></div>
            </div>
        </div>

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
                    {{
                        new Date(post.published_at).toLocaleString("vi-VN", {
                            day: "2-digit",
                            month: "2-digit",
                            year: "numeric",
                            hour: "2-digit",
                            minute: "2-digit",
                        })
                    }}
                </h2>
                <hr class="border-gray-300 w-full mt-4" />
            </div>

            <!--nội dung bài viết-->
            <div class="w-11/12 max-w-7xl flex flex-col lg:flex-row justify-between items-start gap-x-5 mt-5">
                <!-- Nội dung chính -->
                <div class="lg:w-[73%] w-full max-w-7xl mt-5">
                    <div v-html="post.content" class="text-left"></div>
                    <div v-html="`<em>${post.user?.full_name}</em>`" class="mt-3 font-bold text-green-800"></div>
                </div>

                <!-- Bài viết nổi bật-->
                <div class="lg:w-[27%] w-full max-w-7xl mt-5">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-left text-xl font-bold text-green-800">
                            Bài viết nổi bật
                        </h2>
                        <div class="flex-1 h-[3px] bg-green-300 mx-4"></div>
                    </div>

                    <div v-if="isLoadingFeaturedPosts" class="space-y-4">
                        <div v-for="n in 3" :key="n" class="flex animate-pulse space-x-4">
                            <div class="w-1/2 h-24 bg-gray-300 rounded"></div>
                            <div class="w-1/2 space-y-2">
                                <div class="h-4 bg-gray-300 rounded w-3/4"></div>
                                <div class="h-4 bg-gray-300 rounded w-1/2"></div>
                            </div>
                        </div>
                    </div>
                    <div v-else>
                        <NuxtLink v-for="featuredPost in featuredPosts" :key="featuredPost.id"
                            :to="`/posts/${featuredPost.slug}`"
                            class="bg-[#FFFFFF] overflow-hidden duration-200 flex items-start mt-3">
                            <div class="w-1/2 h-full border border-green-100 rounded overflow-hidden">
                                <img :src="`${backendUrl}${featuredPost.featured_image}`" :alt="featuredPost.title"
                                    class="min-h-24 w-full rounded  aspect-video object-cover transition-transform duration-200 hover:scale-105" loading="lazy"
                                    @error="event => { const target = event.target as HTMLImageElement; if (target) target.src = defaultImage; }" />
                            </div>

                            <div class="w-1/2 px-2 py-0">
                                <h3 class="text-left font-semibold text-green-800 hover:text-green-600 duration-200">
                                    {{ featuredPost.title }}
                                </h3>
                            </div>
                        </NuxtLink>
                    </div>
                </div>
            </div>

            <!-- Bài viết liên quan -->
            <div class="w-11/12 max-w-7xl">
                <div v-if="isLoadingRelatedPosts" class="mt-5">
                    <PostLoadingCard />
                </div>
                <div v-else>
                    <PostList v-if="relatedPosts && relatedPosts.posts.length > 0" title="Bài viết liên quan"
                        :posts="relatedPosts.posts" :meta="relatedPosts.meta" :links="relatedPosts.links" :maxCols="4"
                        @page-change="handlePageChange" />
                </div>
            </div>
        </template>

        <div v-else class="flex justify-center items-center min-h-[400px]">
            <p class="text-gray-600">Không tìm thấy bài viết</p>
        </div>
    </main>
</template>
