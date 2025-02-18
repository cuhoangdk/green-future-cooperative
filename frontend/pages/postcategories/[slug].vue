<script setup lang="ts">
definePageMeta({
    layout: "customer",
});

import { ref, onMounted } from "vue";
import { useRoute } from "vue-router";
import { usePosts } from "#imports";
import type { PaginationMeta, PaginationLinks } from "~/types/api";
import type { Post } from "~/types/post";

interface PostsOfCategory {
    posts: Post[];
    meta: PaginationMeta | null;
    links: PaginationLinks | null;
}

const { fetchPostByCategorySlug } = usePosts();

const isLoading = ref(true);

const route = useRoute();
const posts = ref<PostsOfCategory | null>(null);
const meta = ref<PaginationMeta | null>(null);
const links = ref<PaginationLinks | null>(null);

const loadPost = async (page: number = 1) => {
    try {
        isLoading.value = true;
        const response = await fetchPostByCategorySlug(
            route.params.slug as string,
            page,
            8
        );
        posts.value = {
            posts: response.data,
            meta: response.meta || null,
            links: response.links || null,
        };
    } catch (error) {
        console.error("Error loading categories:", error);
    } finally {
        isLoading.value = false;
    }
};

const handlePageChange = (page: number) => {
    loadPost(page);
};

onMounted(() => {
    loadPost();
});
</script>

<template>
    <div v-if="isLoading" class="min-h-screen flex items-center justify-center">
        <Loader />
    </div>
    <div v-else-if="posts?.posts.length > 0 || posts?.posts === null"
        class="min-h-screen bg-white items-center flex flex-col mt-12 pb-5 lg:mt-0">
        <div class="w-11/12 max-w-7xl flex lg:flex-row flex-col gap-x-5">
            <PostList v-if="posts && posts.posts.length > 0" :title="'Danh sách bài viết'" :links="posts?.links"
                :meta="posts?.meta" :posts="posts?.posts" :maxCols="4" @page-change="handlePageChange" />
        </div>
    </div>
    <div v-else class="min-h-screen flex justify-center items-center">
        <p class="text-gray-600">Không tìm thấy bài viết</p>
    </div>
</template>
