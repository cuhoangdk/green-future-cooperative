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
        <NuxtLink v-else :to="`/posts/${posts[0]?.slug}`" class="bg-green-900 shadow-sm rounded-sm overflow-hidden relative w-full lg:w-1/2">
            <div class="relative w-full aspect-video">
                <div class="absolute inset-0 bg-cover bg-center transition-transform duration-200 transform hover:scale-105"
                    :style="{ backgroundImage: `linear-gradient(to top, rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0)), url(${backendUrl + posts[0].featured_image})` }">
                </div>
            </div>
            <div class="absolute bottom-0 left-0 text-white p-3 w-full">
                <h3 class="text-2xl text-left font-bold">{{ posts[0]?.title }}</h3>
            </div>
        </NuxtLink>

        <!-- Featured Posts Grid -->
        <div class="flex lg:grid lg:grid-cols-2 gap-0.5 lg:w-1/2 w-full overflow-x-auto">
            <div v-if="status === 'pending'" v-for="i in 4" :key="i"class="animate-pulse bg-gray-200 shadow-sm rounded-sm overflow-hidden relative min-w-[80%] lg:min-w-0">
                <div class="relative w-full aspect-video">
                    <div class="absolute inset-0 bg-cover bg-center transition-transform duration-200 transform hover:scale-105"
                        :style="{ backgroundImage: `linear-gradient(to top, rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0)), url(${placeholderImage})` }">
                    </div>
                    <div class="absolute bottom-0 left-0 text-white p-1.5 w-full">
                        <h3 class="text-left font-semibold">Đang tải...</h3>
                    </div>
                </div>
            </div>
            <NuxtLink v-else v-for="(post, index) in posts.slice(1)" :key="index" v-if="posts.length > 2"
                :to="`/posts/${post.slug}`"
                class="bg-green-900 shadow-sm rounded-sm overflow-hidden relative min-w-[80%] lg:min-w-0"
                :class="{ 'h-1/2': posts.length === 3, '': posts.length > 3 }">
                <div class="relative w-full aspect-video">
                    <div class="absolute inset-0 bg-cover bg-center transition-transform duration-200 transform hover:scale-105"
                        :style="{ backgroundImage: `linear-gradient(to top, rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0)), url(${backendUrl + post.featured_image})` }">
                    </div>
                </div>
                <div class="absolute bottom-0 left-0 text-white p-1.5 w-full">
                    <h3 class="text-left font-semibold">{{ post.title }}</h3>
                </div>
            </NuxtLink>
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
const posts = computed<Post[]>(() => Array.isArray(data.value?.data) ? data.value.data : data.value ? [data.value.data] : [])

if (error) {
    console.error('Failed to load featured posts:', error);
}
</script>