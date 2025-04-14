<template>
    <div class="flex flex-col lg:flex-row gap-0.5">
        <!-- Main Hot Post -->
        <div v-if="status === 'pending'" class="animate-pulse bg-gray-200 shadow-sm rounded-sm overflow-hidden relative w-full lg:w-1/2">
            <div class="relative w-full aspect-video">
                <div class="absolute inset-0 bg-cover bg-center transition-transform duration-200 transform hover:scale-105"
                    :style="{ backgroundImage: `linear-gradient(to top, rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0)), url(${placeholderImage})` }">
                </div>
            </div>
            <div class="absolute bottom-0 left-0 text-white p-3 w-full">
                <h3 class="text-2xl text-left font-bold">Đang tải...</h3>
            </div>
        </div>
        <NuxtLink v-else-if="posts.length > 0" :to="`/posts/${posts[0]?.slug}`" class="bg-green-900 shadow-sm rounded-sm overflow-hidden relative w-full lg:w-1/2">
            <div class="relative w-full aspect-video">
                <div class="absolute inset-0 bg-cover bg-center transition-transform duration-200 transform hover:scale-105"
                    :style="{ backgroundImage: `linear-gradient(to top, rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0)), url(${posts[0]?.featured_image ? backendUrl + posts[0].featured_image : placeholderImage})` }">
                </div>
            </div>
            <div class="absolute bottom-0 left-0 text-white p-3 w-full line-clamp-3">
                <h3 class="text-2xl text-left font-bold">{{ posts[0]?.title || 'No title available' }}</h3>
            </div>
        </NuxtLink>
        <div v-else class="bg-gray-200 shadow-sm rounded-sm overflow-hidden relative w-full lg:w-1/2">
            <div class="relative w-full aspect-video">
                <div class="absolute inset-0 bg-cover bg-center"
                    :style="{ backgroundImage: `url(${placeholderImage})` }">
                </div>
            </div>
            <div class="absolute bottom-0 left-0 text-white p-3 w-full">
                <h3 class="text-2xl text-left font-bold">No featured posts available</h3>
            </div>
        </div>

        <!-- Featured Posts Grid -->
        <div class="flex lg:grid lg:grid-cols-2 gap-0.5 lg:w-1/2 w-full overflow-x-auto">
            <div v-if="status === 'pending'" v-for="i in 4" :key="i" class="animate-pulse bg-gray-200 shadow-sm rounded-sm overflow-hidden relative min-w-[80%] lg:min-w-0">
                <div class="relative w-full aspect-video">
                    <div class="absolute inset-0 bg-cover bg-center transition-transform duration-200 transform hover:scale-105"
                        :style="{ backgroundImage: `linear-gradient(to top, rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0)), url(${placeholderImage})` }">
                    </div>
                    <div class="absolute bottom-0 left-0 text-white p-1.5 w-full">
                        <h3 class="text-left font-semibold">Đang tải...</h3>
                    </div>
                </div>
            </div>
            <NuxtLink v-else v-for="(post, index) in posts.slice(1)" :key="index" v-show="post && post.slug"
                :to="`/posts/${post.slug}`"
                class="bg-green-900 shadow-sm rounded-sm overflow-hidden relative min-w-[80%] lg:min-w-0"
                :class="{ 'h-1/2': posts.length <= 3, '': posts.length > 3 }">
                <div class="relative w-full aspect-video">
                    <div class="absolute inset-0 bg-cover bg-center transition-transform duration-200 transform hover:scale-105"
                        :style="{ backgroundImage: `linear-gradient(to top, rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0)), url(${post.featured_image ? backendUrl + post.featured_image : placeholderImage})` }">
                    </div>
                </div>
                <div class="absolute bottom-0 left-0 text-white p-1.5 w-full">
                    <h3 class="text-left font-semibold line-clamp-3">{{ post.title || 'No title available' }}</h3>
                </div>
            </NuxtLink>
            <div v-if="posts.length <= 1 && status !== 'pending'" v-for="i in 4" :key="i" class="bg-gray-100 shadow-sm rounded-sm overflow-hidden relative min-w-[80%] lg:min-w-0">
                <div class="relative w-full aspect-video">
                    <div class="absolute inset-0 bg-cover bg-center"
                        :style="{ backgroundImage: `url(${placeholderImage})` }">
                    </div>
                </div>
                <div class="absolute bottom-0 left-0 text-gray-700 p-1.5 w-full">
                    <h3 class="text-left font-semibold">No additional posts</h3>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import type { Post } from '~/types/post';
import { usePosts } from '~/composables/usePosts';
import { useRuntimeConfig } from '#app';

const $runtimeConfig = useRuntimeConfig();
const backendUrl = $runtimeConfig.public.backendUrl;
const placeholderImage = '/images/banner.png';

const { getFeaturedPosts } = usePosts();
const { data, status, error } = await getFeaturedPosts();
const posts = computed<Post[]>(() => {
    if (!data.value) return [];
    if (Array.isArray(data.value?.data)) return data.value.data;
    return data.value.data ? [data.value.data] : [];
});

if (error.value) {
    console.error('Failed to load featured posts:', error.value);
}
</script>