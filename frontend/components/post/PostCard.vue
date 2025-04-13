<template>
    <NuxtLink :to="`/posts/${post.slug}`"
        class="bg-white border border-green-100 shadow-sm rounded-2xl overflow-hidden hover:shadow-md hover:-translate-y-1 duration-200 relative z-0 h-full flex flex-col">
        <!-- Khu vực ảnh với category name nổi -->
        <div class="relative w-full aspect-video">
            <img :src="`${img}`" :alt="post.title" class="rounded-2xl aspect-video rounded-b-none object-cover w-full"
                loading="lazy" @error="() => { img = placeholderImage; }" />
            <!-- Category name nổi ở góc dưới bên trái -->
            <div v-if="post.category?.name"
                class="absolute bottom-2 left-2 bg-green-600 text-white text-xs font-semibold px-2 py-1 rounded">
                {{ post.category.name }}
            </div>
        </div>

        <div class="text-left mt-3 px-3 pb-3 flex-1 flex flex-col justify-between">
            <div>
                <h3 class="text-xl font-bold text-green-800 mb-2 line-clamp-2">{{ post.title }}</h3>
                <p class="text-gray-600 text-sm line-clamp-2">{{ post.summary }}</p>
            </div>

            <div class="flex justify-between mt-auto">
                <div class="text-sm text-green-500 hover:text-green-600 font-semibold">
                    Đọc thêm
                </div>
                <p class="text-gray-500 text-sm text-right">
                    {{
                        new Date(post.published_at).toLocaleDateString("vi-VN", {
                            day: "2-digit",
                            month: "2-digit",
                            year: "numeric",
                        })
                    }}
                </p>
            </div>
        </div>
    </NuxtLink>
</template>

<script setup lang="ts">
import type { Post } from "~/types/post";
import { useRuntimeConfig } from "#app";

interface Props {
    post: Post;
}

const config = useRuntimeConfig();
const placeholderImage = config.public.placeholderImage;
const backendUrl = config.public.backendUrl;
const props = defineProps<Props>();
const img = ref(`${backendUrl}${props.post.featured_image}`);
</script>
