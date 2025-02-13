<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { usePostcategories } from '~/composables/usePostcategories'
import type { PostCategory } from '~/types/postcategory'
import type { PaginationMeta, PaginationLinks } from '~/types/api'

const isLoading = ref(true);

const categories = ref<PostCategory[]>([]);
const meta = ref<PaginationMeta | null>(null);
const links = ref<PaginationLinks | null>(null);


const { fetchAllPostCategories } = usePostcategories();

const loadCategories = async (page: number = 1, perPage = 20) => {
    try {
        isLoading.value = true;
        const response = await fetchAllPostCategories(page, perPage);
        categories.value = response.data;
        meta.value = response.meta || null;
        links.value = response.links || null;
    } catch (error) {
        console.error('Error loading categories:', error);
    } finally {
        isLoading.value = false;
    }
};

const handlePageChange = (page: number, perPage: number) => {
    loadCategories(page);
};

// Expose function để gọi từ component cha
defineExpose({
    loadCategories,
    categories
});

onMounted(() => {
    loadCategories();
});
</script>

<template>
    <div>
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-left text-xl font-bold text-green-800">Danh mục</h2>
            <div class="flex-1 h-[3px] bg-green-300 mx-4"></div>
        </div>
        <div v-if="isLoading" v-for="i in 5" :key="i" class="text-center">
            <div class="h-6 bg-gray-200 rounded w-3/4 my-5"></div>
        </div>
        <ul v-else class="text-left space-y-2">
            <li v-for="category in categories" :key="category.id">
                <NuxtLink :to="'/postcategories/' + category.slug"
                    class="text-left text-xl font-semibold text-green-600 hover:text-green-400 transition-colors duration-200">
                    {{ category.name }}
                </NuxtLink>
            </li>
        </ul>

        <!-- <BasePagination 
            :meta="meta"
            :links="links"
            @page-change="handlePageChange"
        /> -->
    </div>
</template>