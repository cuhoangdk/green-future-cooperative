<template>
    <div class="border border-gray-200 rounded-lg p-5">
        <form @submit.prevent="handleSubmit" class="space-y-2">
            <div class="divider divider-start text-xl font-bold">Thông tin danh mục bài viết</div>
            <div class="flex flex-col gap-4">
                <!-- Category Name -->
                <div class="w-full">
                    <label class="text-gray-700 font-semibold">Tên danh mục bài viết</label>
                    <input v-model="form.name" class="input input-primary w-full mt-1" placeholder="Tin tức..." required />
                </div>
                <!-- Description -->
                <div class="w-full">
                    <label class="text-gray-700 font-semibold">Mô tả</label>
                    <input v-model="form.description" class="input input-primary w-full mt-1" placeholder="Tin tức mới nhất..." required />
                </div>
            </div>
            <!-- Submit Button -->
            <div class="flex justify-end">
                <button type="submit" class="btn btn-primary" :disabled="status === 'pending'">
                    <span v-if="status === 'pending'" class="loading loading-spinner loading-md"></span>
                    <span>Thêm danh mục bài viết</span>
                </button>
            </div>
        </form>
    </div>
</template>

<script setup lang="ts">
definePageMeta({ title: 'Thêm danh mục bài viết', layout: 'user', })

import { useToast } from 'vue-toastification'
import { usePostCategories } from '~/composables/usePostCategories'

const { createPostCategory } = usePostCategories()
const toast = useToast()
const status = ref<'idle' | 'pending' | 'success' | 'error'>('idle')

// Initialize form with default values
const form = ref({
    name: '',
    description: '',
})
const handleSubmit = async () => {
    try {
        status.value = 'pending'

        // Create FormData to send data
        const formData = new FormData()
        formData.append('name', form.value.name)
        formData.append('description', form.value.description)

        // Send request to create post category
        const { error, data } = await createPostCategory(formData)

        if (error.value?.message) throw new Error(data.value?.message || 'Thêm danh mục bài viết thất bại!')

        toast.success('Thêm danh mục bài viết thành công!')
        
        // Redirect to the index page
        useRouter().push('/admin/post-categories')
    } catch (error: any) {
        toast.error(error.message || 'Thêm danh mục bài viết thất bại!')
    } finally {
        status.value = 'idle'
    }
}
</script>