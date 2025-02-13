<script setup lang="ts">
import { ref } from 'vue'
import type { Post } from '~/types/post'

const props = defineProps<{ post: Post }>()

const currentImage = ref(props.post.image)

const handleImageError = () => {
    // Sử dụng path từ thư mục public
    currentImage.value = '/img/banner.png'
}
</script>

<template>
    <NuxtLink :to="`/posts/${post.slug}`"
        class="bg-white border border-amber-100 shadow-sm rounded-lg overflow-hidden hover:shadow-md hover:-translate-y-1 duration-200 relative z-10">
        <div class="relative w-full aspect-video">
            <NuxtImg :src="currentImage" :alt="post.title" class="rounded-lg rounded-b-none w-full h-full object-cover"
                loading="lazy" @error="handleImageError" />
        </div>

        <div class="text-left mt-3 px-3 pb-3">
            <h3 class="text-xl font-bold text-green-800 mb-2">{{ post.title }}</h3>
            <p class="text-gray-600 text-sm line-clamp-3">{{ post.summary }}</p>
            <div class="flex justify-between">
                <div class="mt-2 text-sm text-green-500 hover:text-green-600 font-semibold">
                    Đọc thêm
                </div>
                <p class="text-gray-500 text-sm mt-2 text-right">{{ post.date }}</p>
            </div>
        </div>
    </NuxtLink>
</template>
