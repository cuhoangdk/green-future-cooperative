<template>
    <div class="p-4">
        <form @submit.prevent="handleSubmit" class="space-y-4">
            <!-- Tiêu đề -->
            <div>
                <label class="text-gray-700 font-semibold">Tiêu đề bài viết</label>
                <textarea v-model="form.title" type="text" class="textarea textarea-primary h-18 w-full mt-1"
                    placeholder="Bài viết về ..." required></textarea>
            </div>

            <!-- Tóm tắt -->
            <div>
                <label class="text-gray-700 font-semibold">Tóm tắt</label>
                <textarea v-model="form.summary" class="textarea textarea-primary h-24 w-full mt-1"
                    placeholder="Nội dung chính của bài viết là..." required></textarea>
            </div>

            <!-- Nội dung -->
            <div>
                <label class="text-gray-700 font-semibold">Nội dung</label>
                <textarea v-model="form.content" class="textarea textarea-primary h-[700px] w-full mt-1"
                    placeholder="..." required></textarea>
            </div>

            <!-- Ảnh bìa -->
            <div>
                <label class="text-gray-700 font-semibold">Ảnh bìa</label>
                <input type="file" @change="handleImageUpload" class="hidden" id="fileInput" />
                <label for="fileInput"
                    class="block w-full h-full text-center py-2 border-2 border-green-200 border-dashed rounded-lg cursor-pointer hover:bg-gray-100 transition mt-1">
                    <span v-if="!previewImage" class="flex justify-center gap-3 py-8">
                        <FileArchive /> Chọn ảnh đại diện
                    </span>
                    <div v-if="previewImage" class="flex justify-center">
                        <img v-if="previewImage" :src="previewImage"
                            class="aspect-video h-44 object-cover rounded-lg shadow" />
                    </div>
                </label>
            </div>
            <div class="flex space-x-4">
                <!-- Danh mục -->
                <div class="w-1/2">
                    <label class="text-gray-700 font-semibold">Danh mục</label>
                    <select v-model="form.category_id" class="select select-primary w-full mt-1" required>
                        <option disabled selected>Chọn danh mục của bài viết</option>
                        <option v-for="category in categories" :key="category.id" :value="category.id">
                            {{ category.name }}
                        </option>
                    </select>
                </div>
                <!-- Trạng thái bài viết -->
                <div class="w-1/2">
                    <label class="text-gray-700 font-semibold">Trạng thái</label>
                    <select v-model="form.post_status" class="select select-primary w-full mt-1">
                        <option value="draft">Nháp</option>
                        <option value="published">Xuất bản</option>
                    </select>
                </div>
            </div>

            <!-- Bài viết nổi bật & hot -->
            <div class="flex space-x-4">
                <label class="flex items-center w-1/2">
                    <input v-model="form.is_hot" type="checkbox" class="toggle toggle-primary mr-2 mt-1" /> Bài viết hot
                </label>
                <label class="flex items-center w-1/2">
                    <input v-model="form.is_featured" type="checkbox" class="toggle toggle-primary mr-2 mt-1" /> Bài
                    viết nổi
                    bật
                </label>
            </div>

            <div class="flex space-x-4">
                <!-- Thứ tự hiển thị hot và featured -->
                <div v-if="form.is_hot" class="w-1/2">
                    <label class="text-gray-700 font-semibold">Thứ tự hot</label>
                    <input v-model="form.hot_order" type="number" placeholder="nhập một số nguyên"
                        class="input input-primary w-full mt-1" />
                </div>
                <div v-else class="w-1/2"></div>

                <div v-if="form.is_featured" class="w-1/2">
                    <label class="text-gray-700 font-semibold">Thứ tự nổi bật</label>
                    <input v-model="form.featured_order" type="number" placeholder="nhập một số nguyên"
                        class="input input-primary w-full mt-1" />
                </div>
                <div v-else class="w-1/2"></div>
            </div>
            <!-- <div class="divider divider-start text-xl font-bold">SEO (Không bắt buộc nhập)</div>
            <div class="flex space-x-4">
                <div class="w-1/2">
                    <label class="text-gray-700 font-semibold">Tags</label>
                    <input v-model="form.tags" type="text" placeholder="Nhập các thẻ, cách nhau bằng dấu phẩy"
                        class="input input-primary w-full mt-1" />
                </div>

                <div class="w-1/2">
                    <label class="text-gray-700 font-semibold">Meta Title</label>
                    <input v-model="form.meta_title" type="text" placeholder="Nhập meta title"
                        class="input input-primary w-full mt-1" />
                </div>
            </div>

            <div>
                <label class="text-gray-700 font-semibold">Meta Description</label>
                <textarea v-model="form.meta_description" class="textarea textarea-primary h-24 w-full mt-1"
                    placeholder="Nhập meta description"></textarea>
            </div> -->

            <div class="flex justify-between items-center mt-4">
                <UiButtonBack/>

                <button type="submit" class="btn btn-primary" :disabled="submitStatus === 'pending'">
                    <span v-if="submitStatus === 'pending'" class="loading loading-spinner loading-md"></span>
                    <span>Tạo bài viết</span>
                </button>
            </div>
        </form>
    </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'user', title: 'Tạo bài viết mới', })

import { FileArchive } from 'lucide-vue-next'
import { usePosts } from '~/composables/usePosts'
import { usePostCategories } from '~/composables/usePostCategories'
import type { PostCategory } from '~/types/post'

const { createPost } = usePosts()
const { getAllPostCategories } = usePostCategories()
const router = useRouter()
const swal = useSwal()
const { $toast } = useNuxtApp()


// Gọi API để lấy danh mục
const { data: categoryData, status, error: categoryError } = await getAllPostCategories()
const categories = computed<PostCategory[]>(() =>
    Array.isArray(categoryData.value?.data) ? categoryData.value.data : categoryData.value ? [categoryData.value.data] : []
)

// Form data
const form = ref({
    title: '',
    summary: '',
    content: '',
    featured_image: null as File | null, // Định kiểu rõ ràng cho file
    category_id: '',
    post_status: 'published',
    is_hot: false,
    is_featured: false,
    hot_order: null as number | null,
    featured_order: null as number | null,
    tags: '',
    meta_title: '',
    meta_description: '',
})

const submitStatus = ref<'idle' | 'pending' | 'success' | 'error'>('idle')
const previewImage = ref<string | null>(null)

// Tự động điền meta_title và meta_description
watch(() => form.value.title, (newTitle) => {
    if (newTitle) {
        form.value.meta_title = newTitle // Điền meta_title từ title nếu chưa có giá trị
    }
}, { immediate: true })

watch(() => form.value.summary, (newSummary) => {
    if (newSummary) {
        form.value.meta_description = newSummary // Điền meta_description từ summary nếu chưa có giá trị
    }
}, { immediate: true })

// Upload ảnh
const handleImageUpload = (event: Event) => {
    const file = (event.target as HTMLInputElement).files?.[0]
    if (file) {
        form.value.featured_image = file
        previewImage.value = URL.createObjectURL(file) // Dùng URL.createObjectURL cho gọn
    }
}

const handleSubmit = async () => {
    submitStatus.value = 'pending' // Đặt trạng thái pending ngay khi bắt đầu
    try {
        const formData = new FormData()
        formData.append('title', form.value.title)
        formData.append('summary', form.value.summary)
        formData.append('content', form.value.content)
        formData.append('category_id', form.value.category_id)
        formData.append('post_status', form.value.post_status)
        formData.append('is_hot', form.value.is_hot ? '1' : '0')
        formData.append('is_featured', form.value.is_featured ? '1' : '0')
        formData.append('tags', form.value.tags)
        formData.append('meta_title', form.value.meta_title)
        formData.append('meta_description', form.value.meta_description)
        if (form.value.hot_order !== null) formData.append('hot_order', form.value.hot_order.toString())
        if (form.value.featured_order !== null) formData.append('featured_order', form.value.featured_order.toString())
        if (form.value.featured_image) formData.append('featured_image', form.value.featured_image)

        const { data, error, status } = await createPost(formData)

        if (error.value) {
            console.error('API Error:', error.value)
            throw new Error(error.value.message || 'Failed to create post')
        }
        submitStatus.value = status.value
        if (status.value === 'success' && data.value) {
            $toast.success('Tạo bài viết thành công!')
            await router.push('/admin/posts')
        } else {
            throw new Error('Unexpected response from server')
        }
    } catch (error) {
        const errorMessage = (error as Error).message || 'Unknown error'
        $toast.error(`Tạo bài viết thất bại: ${errorMessage}`)
    } finally {
        submitStatus.value = 'idle'
    }
}
</script>