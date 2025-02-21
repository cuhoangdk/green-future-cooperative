<template>
    <div class="relative space-y-6 border border-gray-200 rounded-lg shadow p-5">
        <!-- Header Section -->
        <div class="flex justify-between items-center border-b border-gray-200 pb-3">
            <h1 class="text-2xl font-bold text-gray-800">Thêm bài viết</h1>
        </div>
        <FormCreatePost />
    </div>
</template>
<script lang="ts">
definePageMeta({
    layout: "user",
    title: "Thêm bài viết",
});
</script>