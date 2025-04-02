<template>
    <div class="border border-gray-200 rounded-lg p-5">
        <form @submit.prevent="handleSubmit" class="space-y-2">
            <div class="divider divider-start text-xl font-bold">Thông tin danh mục bài viết</div>
            <div class="flex flex-col gap-4">
                <!-- Category Name -->
                <div class="w-full">
                    <label class="text-gray-700 font-semibold">Tên danh mục</label>
                    <input v-model="form.name" class="input input-primary w-full mt-1" placeholder="Tên danh mục..."
                        required />
                </div>
                <!-- Description -->
                <div class="w-full">
                    <label class="text-gray-700 font-semibold">Mô tả</label>
                    <input v-model="form.description" class="input input-primary w-full mt-1"
                        placeholder="Mô tả danh mục..." required />
                </div>
                <!-- Submit Button -->
                <div class="flex justify-between">
                    <button type="button" class="btn mr-2" @click="router.back()">Quay lại</button>
                    <button type="submit" class="btn btn-primary" :disabled="status === 'pending'">
                        <span v-if="status === 'pending'" class="loading loading-spinner loading-md"></span>
                        <span>Cập nhật</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</template>

<script setup lang="ts">
definePageMeta({ title: 'Chỉnh sửa danh mục bài viết', layout: 'user', })

import { useToast } from 'vue-toastification'
import { useRoute, useRouter } from 'vue-router'
import { ref, computed, watch } from 'vue'
import { usePostCategories } from '~/composables/usePostCategories'
import type { PostCategory } from '~/types/post'

const route = useRoute()
const router = useRouter()
const { getPostCategoryById, updatePostCategory } = usePostCategories()
const toast = useToast()
const status = ref<'idle' | 'pending' | 'success' | 'error'>('idle')

const { data, error, refresh: refreshCategory } = await getPostCategoryById(Number(route.params.id))
const category = computed<PostCategory | null>(() => Array.isArray(data.value?.data) ? data.value.data[0] : data.value?.data || null)

// Initialize form with default values
const form = ref({
    name: '',
    description: '',
})

watch(category, (newData) => {
    if (newData) {
        form.value.name = newData.name || ''
        form.value.description = newData.description || ''
    }
}, { immediate: true })

const handleSubmit = async () => {
    try {
        status.value = 'pending'

        // Create FormData to send data
        const formData = new FormData()
        formData.append('name', form.value.name)
        formData.append('description', form.value.description)

        // Send update request
        const { error, data } = await updatePostCategory(Number(route.params.id), formData)

        if (error.value?.message) throw new Error(data.value?.message || 'Cập nhật danh mục thất bại')

        toast.success('Cập nhật danh mục thành công!')
        router.back()

    } catch (error: any) {
        toast.error(error.message || 'Cập nhật danh mục thất bại!')
    } finally {
        status.value = 'idle'
    }
}
</script>