<template>
    <div class="flex flex-col lg:flex-row items-center space-x-2 space-y-2 lg:space-y-0">
        <span class="bg-green-600 text-sm text-white text-left px-2 py-0.5 rounded-sm">
            TIN HOT
        </span>
        <p v-if="status === 'pending'" class="skeleton text-sm lg:ml-5 text-gray-500 px-3">
            Đang tải...
        </p>
        <p v-else class="text-sm lg:ml-5">
            <a :href="'/posts/' + posts[currentIndex].slug"
                class="text-green-600 hover:text-green-400 transition-colors duration-200">
                {{ posts[currentIndex].title }}</a>
        </p>
    </div>
</template>

<script setup lang="ts">
import type { Post } from '~/types/post';
import { usePosts } from '~/composables/usePosts';

const { getHotPosts } = usePosts();
const { data, status, error } = await getHotPosts();
const posts = computed<Post[]>(() => Array.isArray(data.value?.data) ? data.value.data : data.value ? [data.value.data] : [])
const currentIndex = ref(0);

onMounted(() => {
    const interval = setInterval(() => {
        if (posts.value.length > 0) {
            currentIndex.value = (currentIndex.value + 1) % posts.value.length;
        }
    }, 2000);

    onUnmounted(() => {
        clearInterval(interval);
    });
});

if (error) {
    console.error('Failed to load hot posts:', error);
}
</script>