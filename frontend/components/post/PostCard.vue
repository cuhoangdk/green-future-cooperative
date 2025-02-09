<template>
    <div
        class="bg-white shadow-sm rounded-lg overflow-hidden hover:shadow-md hover:-translate-y-1 duration-200 relative">
        <div class="relative w-full aspect-video">
            <img :src="post.image" :alt="post.title" class="rounded-lg rounded-b-none w-full h-full object-cover"
                loading="lazy" />
        </div>

        <div class="text-left mt-3 px-3 pb-3">
            <h3 class="text-xl font-bold text-green-800 mb-2">{{ post.title }}</h3>
            <p class="text-gray-600 text-sm line-clamp-3">{{ post.content }}</p>
            <div class="flex justify-between">
                <button class="mt-2 text-sm text-green-500 hover:text-green-600 font-semibold">
                    Đọc thêm
                </button>
                <p class="text-gray-500 text-sm mt-2 text-right">{{ post.date }}</p>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
interface Post {
    id: number
    title: string
    content: string
    image: string
    date: string
}

defineProps<{
    post: Post
}>()
</script>