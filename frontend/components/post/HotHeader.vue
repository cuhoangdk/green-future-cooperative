<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'
import type { Post } from '~/types/post'

interface Props {
    posts: Post[]
    isLoading: boolean
}
const props = defineProps<Props>();

const currentIndex = ref(0);

onMounted(() => {
    const interval = setInterval(() => {
        currentIndex.value = (currentIndex.value + 1) % props.posts.length;
    }, 3000);

    onUnmounted(() => {
        clearInterval(interval);
    });
});
</script>

<template>
    <div class="flex flex-col lg:flex-row items-center space-x-2 space-y-2 lg:space-y-0">
        <span class="bg-green-600 text-sm text-white text-left px-2 py-0.5 rounded-sm">
            TIN HOT
        </span>
        <p v-if="props.isLoading" class="text-sm lg:ml-5 text-gray-500">
            Đang tải...
        </p>
        <p v-else-if="props.posts.length" class="text-sm lg:ml-5">
            <a :href="'/posts/' + props.posts[currentIndex].slug" class="text-green-600 hover:text-green-400 transition-colors duration-200">
            {{ props.posts[currentIndex].title }}</a>
        </p>
    </div>
</template>