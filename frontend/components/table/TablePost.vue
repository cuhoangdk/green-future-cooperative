<template>
    <div class="hidden md:block w-full overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-100 text-gray-700">
                    <th class="py-2 pl-2 w-[40%] text-left">Tiêu đề</th>
                    <th class="py-2 w-[15%] text-left">Tác giả</th>
                    <th class="py-2 w-[15%] text-left">Danh mục</th>
                    <th class="py-2 w-[20%] text-left">Trạng thái</th>
                    <th class="py-2 w-[15%] text-left">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="post in posts" :key="post.id"
                    class="border-b border-gray-100 hover:bg-gray-200 cursor-pointer">
                    <td class="py-1 pl-2 line-clamp-2">{{ post.title }}</td>
                    <td class="py-1">{{ post.user?.full_name }}</td>
                    <td class="py-1">{{ post.category?.name }}</td>
                    <td class="py-1 space-x-1">
                        <div v-if="post.post_status === 'published'" class="badge badge-primary">Công khai</div>
                        <div v-else-if="post.post_status === 'draft'" class="badge badge-neutral">Nháp</div>
                        <div v-else class="badge badge-warning">Lưu trữ</div>
                        <div v-if="post.is_hot" class="badge badge-info">Nổi bật</div>
                        <div v-if="post.is_featured" class="badge badge-error">Hot</div>
                    </td>
                    <td class="py-1">
                        <div class="flex space-x-1 items-center">
                            <UiEditButton :to="`posts/${post.id}/`" />
                            <UiDeleteButton :on-click="() => onDelete(post.id)" />
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
<script setup lang="ts">
import type { Post } from '~/types/post'
import { formatNumber, formatCurrency } from '~/utils/common'

defineProps<{
    posts: Post[]
    onDelete: (id: number) => void
}>()
</script>