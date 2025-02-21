<script setup lang="ts">
definePageMeta({
    layout: "user",
    title: "Quản lý bài viết",
});
import { Search, Plus } from 'lucide-vue-next';
import { ref, onMounted } from "vue";
import type { Post } from "~/types/post";
import type { PaginationMeta, PaginationLinks } from "~/types/api";
import PostList from "~/components/Post/PostList.vue";

interface PostList {
    posts: Post[];
    meta: PaginationMeta | null;
    links: PaginationLinks | null;
}

const isLoadingPost = ref(true);
const postList = ref<PostList | null>(null);
const searchQuery = ref('');
const selectedStatus = ref('all');
const selectedCategory = ref('all');
const selectedAuthor = ref('all');
const { fetchPosts } = usePosts();


const loadPosts = async (page: number = 1) => {
    try {
        isLoadingPost.value = true;
        const response = await fetchPosts(page, 10, true);
        postList.value = {
            posts: response.data,
            meta: response.meta || null,
            links: response.links || null,
        };
    } catch (error) {
        console.error("Error loading posts:", error);
    } finally {
        isLoadingPost.value = false;
    }
};

const handlePageChange = (page: number) => {
    loadPosts(page);
};

// Load posts on component mount
onMounted(loadPosts);

</script>

<template>
    <div class="relative space-y-6 border border-gray-200 rounded-lg shadow">
        <!-- Header Section -->
        <div class="flex justify-between items-center border-b border-gray-200 px-5 py-3">
            <h1 class="text-2xl font-bold text-gray-800">Quản lý bài viết</h1>
            <button @click="$router.push('/admin/posts/create')" class="flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                <Plus class="w-5 h-5" />
                Thêm bài viết
            </button>
        </div>

        <!-- Filters Section -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 px-5">
            <!-- Search Box -->
            <div class="relative">
                <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-5 h-5" />
                <input v-model="searchQuery" type="text" placeholder="Tìm kiếm bài viết..."
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" />
            </div>

            <!-- Category Filter -->
            <select v-model="selectedCategory"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                <option value="all">Tất cả danh mục</option>
                <option>
                </option>
            </select>

            <!-- Status Filter -->
            <select v-model="selectedStatus"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                <option value="">Tất cả trạng thái</option>
                <option value="published">Đã xuất bản</option>
                <option value="draft">Bản nháp</option>
                <option value="archiver">Lưu trữ</option>
            </select>

            <!-- Author Filter -->
            <select v-model="selectedAuthor"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                <option value="all">Tất cả tác giả</option>
                <option></option>
            </select>
        </div>
        <PostExpandableTable :posts="postList?.posts" :isLoading="isLoadingPost" :links="postList?.links"
            :meta="postList?.meta" @page-change="handlePageChange" />
    </div>
</template>