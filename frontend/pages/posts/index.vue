<script setup lang="ts">
definePageMeta({
  layout: 'customer'
})

import type { Post } from '~/types/post';
import type { PostCategory } from '~/types/postcategory';
import type { PaginationMeta, PaginationLinks } from '~/types/api'
import { usePosts } from '../../composables/usePosts';
import { usePostcategories } from '#imports';
import { useRuntimeConfig } from '#app';

interface CategoryPosts {
    categoryId: number;
    categoryName: string;
    posts: Post[];
    meta: PaginationMeta | null;
    links: PaginationLinks | null;
}

const placeholderImage = '/img/banner.png'; // hoặc logo của bạn
const config = useRuntimeConfig();
const backendUrl = config.public.backendUrl;

// Thêm loading states
const isLoading = ref(true);
const isLoadingHot = ref(true);
const isLoadingFeatured = ref(true);

const categoryPosts = ref<CategoryPosts[]>([]);
const categories = ref<PostCategory[]>([]);
const hotPosts = ref<Post[]>([]);
const featuredPosts = ref<Post[]>([]);

const { fetchPostByCategoryId, fetchHotPosts, fetchFeaturedPosts } = usePosts();
const { fetchAllPostCategories } = usePostcategories();

const loadCategories = async () => {
    try {
        isLoading.value = true;
        const response = await fetchAllPostCategories();
        categories.value = response.data;
        await loadAllPosts();
    } catch (error) {
        console.error('Error loading categories:', error);
    } finally {
        isLoading.value = false;
    }
};

const loadHotPosts = async () => {
    try {
        isLoadingHot.value = true;
        const response = await fetchHotPosts();
        hotPosts.value = response.data;
    } catch (error) {
        console.error('Error loading categories:', error);
    } finally {
        isLoadingHot.value = false;
    }
}

const loadFeaturedPosts = async () => {
    try {
        isLoadingFeatured.value = true;
        const response = await fetchFeaturedPosts();
        featuredPosts.value = response.data;
    } catch (error) {
        console.error('Error loading categories:', error);
    } finally {
        isLoadingFeatured.value = false;
    }
}

const loadAllPosts = async () => {
    try {
        const postsPromises = categories.value.map(async (category) => {
            const response = await fetchPostByCategoryId(category.id, 1, 3);
            return {
                categoryId: category.id,
                categoryName: category.name.toUpperCase(),
                posts: response.data,
                meta: response.meta || null,
                links: response.links || null
            };
        });

        categoryPosts.value = await Promise.all(postsPromises);
    } catch (error) {
        console.error('Error loading posts:', error);
    }
};

const handlePageChange = async (categoryId: number, page: number) => {
    try {
        const response = await fetchPostByCategoryId(categoryId, page, 3);
        const categoryIndex = categoryPosts.value.findIndex(cp => cp.categoryId === categoryId);

        if (categoryIndex !== -1) {
            categoryPosts.value[categoryIndex] = {
                ...categoryPosts.value[categoryIndex],
                posts: response.data,
                meta: response.meta || null,
                links: response.links || null
            };
        }
    } catch (error) {
        console.error('Error changing page:', error);
    }
};

onMounted(() => {
    loadCategories();
    loadHotPosts();
    loadFeaturedPosts();
});
</script>

<template>
    <div class="min-h-screen items-center flex flex-col mt-16 pb-5 lg:mt-0">
        <div class="w-11/12 max-w-7xl mt-5 flex flex-col lg:flex-row justify-between items-center">
            <PostHotHeader :posts="hotPosts" :isLoading="isLoadingHot" />
        </div>

        <div class="w-11/12 max-w-7xl mt-5 flex flex-col lg:flex-row gap-0.5">
            <!-- Main Hot Post -->
            <div v-if="isLoadingFeatured" class="shadow-sm rounded-sm overflow-hidden relative w-full lg:w-1/2">
                <div class="relative w-full aspect-video">
                    <div class="absolute inset-0 bg-cover bg-center transition-transform duration-200 transform hover:scale-105 animate-pulse"
                        :style="{
                            backgroundImage: `linear-gradient(to top, rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0)), 
                    url(${placeholderImage})`
                        }">
                    </div>
                </div>
                <div class="absolute bottom-0 left-0 text-white p-3 w-full">
                    <h3 class="text-xl text-left font-bold">
                        Đang tải...
                    </h3>
                </div>
            </div>
            <NuxtLink v-else :to="`/posts/${hotPosts[0]?.slug}`"
                class="bg-green-900 shadow-sm rounded-sm overflow-hidden relative w-full lg:w-1/2">
                <div class="relative w-full aspect-video">
                    <div class="absolute inset-0 bg-cover bg-center transition-transform duration-200 transform hover:scale-105" 
                        :style="{
                            backgroundImage: `linear-gradient(to top, rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0)), 
                            url(${hotPosts[0].featured_image ? backendUrl + hotPosts[0].featured_image : placeholderImage})`
                        }">
                    </div>
                </div>
                <div class="absolute bottom-0 left-0 text-white p-3 w-full">
                    <h3 class="text-xl text-left font-bold" :class="{ 'animate-pulse': isLoadingFeatured }">
                        {{ hotPosts[0]?.title }}
                    </h3>
                </div>
            </NuxtLink>

            <!-- Featured Posts Grid -->
            <div class="flex lg:grid lg:grid-cols-2 gap-0.5 lg:w-1/2 w-full overflow-x-auto">
                <div v-for="i in 4" :key="i"
                    class="bg-gray-200 shadow-sm rounded-sm animate-pulse overflow-hidden relative min-w-[80%] lg:min-w-0"
                    v-if="isLoadingFeatured">
                    <div class="relative w-full aspect-video">
                        <div class="absolute inset-0 bg-cover bg-center transition-transform duration-200 transform hover:scale-105"
                            :style="{
                                backgroundImage: `linear-gradient(to top, rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0)), 
                    url(${placeholderImage})`
                            }">
                        </div>
                        <div class="absolute bottom-0 left-0 text-white p-1.5 w-full">
                            <h3 class="text-left font-semibold">
                                Đang tải...
                            </h3>
                        </div>
                    </div>
                </div>
                <NuxtLink v-else v-for="(post, index) in featuredPosts.slice(1)" :key="index"
                    v-if="featuredPosts.length > 2"
                    :to="`/posts/${post.slug}`"
                    class="bg-green-900 shadow-sm rounded-sm overflow-hidden relative min-w-[80%] h-1/2 lg:min-w-0">
                    <div class="relative w-full aspect-video">
                        <div class="absolute inset-0 bg-cover bg-center transition-transform duration-200 transform hover:scale-105"
                            :style="{
                                backgroundImage: `linear-gradient(to top, rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0)), 
                    url(${post.featured_image ? backendUrl + post.featured_image : placeholderImage})`
                            }">
                        </div>
                    </div>
                    <div class="absolute bottom-0 left-0 text-white p-1.5 w-full">
                        <h3 class="text-left font-semibold">
                            {{ post.title || 'Đang tải...' }}
                        </h3>
                    </div>
                </NuxtLink>
            </div>
        </div>

        <div class="w-11/12 max-w-7xl mt-5 flex lg:flex-row flex-col gap-x-5">
            <div class="w-full lg:w-3/4 max-w-7xl ">
                <div v-if="isLoading" class="space-y-4 mt-5">
                    <div v-for="i in 3" :key="i" class="animate-pulse">
                        <PostLoadingCard />
                    </div>
                </div>
                <div v-else v-for="category in categoryPosts" :key="category.categoryId">
                    <div v-if="category.posts.length > 0">
                        <PostList :title="category.categoryName" :posts="category.posts" :meta="category.meta"
                            :links="category.links" :maxCols="3" :gridGap="3"
                            @page-change="(page) => handlePageChange(category.categoryId, page)" />
                    </div>
                </div>
            </div>
            <div class="lg:w-1/4 w-full max-w-7xl mt-5">
                <PostCategoryList />
            </div>
        </div>
    </div>
</template>