<script setup lang="ts">
import { ref } from "vue";
import type { Post } from "~/types/post";
import { useRuntimeConfig } from "#app";
const config = useRuntimeConfig();
const backendUrl = config.public.backendUrl;

const props = defineProps<{ post: Post }>();

const currentImage = ref(`${backendUrl}${props.post.featured_image}`);

const handleImageError = () => {
    // Sử dụng path từ thư mục public
    currentImage.value = "/img/banner.png";
};
</script>

<template>
    <NuxtLink :to="`/posts/${post.slug}`"
        class="bg-white border border-green-100 shadow-sm rounded-lg overflow-hidden hover:shadow-md hover:-translate-y-1 duration-200 relative z-0 h-full flex flex-col">
        <NuxtImg :src="`${currentImage}`" :alt="post.title"
            class="rounded-lg rounded-b-none object-cover relative w-full aspect-video" loading="lazy"
            @error="handleImageError" />

        <div class="text-left mt-3 px-3 pb-3 flex-1 flex flex-col justify-between">
            <div>
                <h3 class="text-xl font-bold text-green-800 mb-2">{{ post.title }}</h3>
                <p class="text-gray-600 text-sm line-clamp-3">{{ post.summary }}</p>
            </div>

            <!-- Cố định ở đáy -->
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
