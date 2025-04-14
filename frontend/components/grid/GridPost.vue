<!-- components/customer/CustomerGrid.vue -->
<template>
    <div class="grid grid-cols-1 gap-4 p-3 md:hidden">
        <div v-for="post in posts" :key="post.id"
            class="card bg-base-100 border border-gray-400 hover:shadow-md transition-shadow cursor-pointer">
            <div class="card-body p-3">
                <div class="flex justify-between items-start">
                    <h3 class="card-title text-base line-clamp-2">{{ post.title }}</h3>
                </div>

                <div class="space-y-1 text-sm">
                    <div class="flex gap-1 items-center">
                        <div v-if="post.post_status === 'published'" class="badge badge-primary">Công khai</div>
                        <div v-else-if="post.post_status === 'draft'" class="badge badge-neutral">Nháp</div>
                        <div v-else class="badge badge-warning">Lưu trữ</div>
                        <div v-if="post.is_hot" class="badge badge-info">Hot</div>
                        <div v-if="post.is_featured" class="badge badge-error">Nổi Bật</div>
                    </div>
                    <div class="flex gap-2 items-center">
                        <User class="w-4 h-4 text-gray-500" /> Tác giả:
                        <span>{{ post.user?.full_name }}</span>

                    </div>
                    <div class="flex gap-2 items-center">
                        <Type class="w-4 h-4 text-gray-500" /> Loại:
                        <span>{{ post.category?.name }}</span>
                    </div>
                </div>
                <div class="card-actions justify-end border-t pt-2 border-gray-100">
                    <div class="flex space-x-1 items-center">

                        <UiEditButton :to="`posts/${post.id}/`" />
                        <UiDeleteButton :on-click="() => onDelete(post.id)" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import type { Post } from '~/types/post'
import { User, Type } from 'lucide-vue-next'
import { formatNumber, formatCurrency } from '~/utils/common'

defineProps<{
    posts: Post[]
    onDelete: (id: number) => void
}>()
</script>