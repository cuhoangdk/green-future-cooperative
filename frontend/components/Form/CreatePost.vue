<template>
    <form @submit.prevent="handleSubmit" class="space-y-6">
        <!-- Tiêu đề -->
        <div>
            <label class="text-gray-700 font-semibold">Tiêu đề</label>
            <input v-model="form.title" type="text" class="input-field" placeholder="Bài viết về ..." required />
        </div>

        <!-- Tóm tắt -->
        <div>
            <label class="text-gray-700 font-semibold">Tóm tắt</label>
            <textarea v-model="form.summary" class="input-field h-24" placeholder="Nội dung chính của bài viết là..."
                required></textarea>
        </div>

        <!-- Nội dung -->
        <div>
            <label class="text-gray-700 font-semibold">Nội dung</label>
            <textarea v-model="form.content" class="input-field h-64" placeholder="..." required></textarea>
        </div>

        <!-- Ảnh bìa -->
        <div>
            <label class="text-gray-700 font-semibold">Ảnh bìa</label>
            <input type="file" @change="handleImageUpload" class="hidden" id="fileInput" />
            <label for="fileInput"
                class="block w-full h-full text-center py-2 border-2 border-dashed rounded-lg cursor-pointer hover:bg-gray-100 transition">
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
                <select v-model="form.category_id" class="input-field" required>
                    <option v-for="category in categories" :key="category.id" :value="category.id">
                        {{ category.name }}
                    </option>
                </select>
            </div>
            <!-- Trạng thái bài viết -->
            <div class="w-1/2">
                <label class="text-gray-700 font-semibold">Trạng thái</label>
                <select v-model="form.post_status" class="input-field">
                    <option value="draft">Nháp</option>
                    <option value="published">Xuất bản</option>
                </select>
            </div>
        </div>

        <!-- Bài viết nổi bật & hot -->
        <div class="flex space-x-4">
            <label class="flex items-center w-1/2">
                <input v-model="form.is_hot" type="checkbox" class="mr-2" /> Bài viết hot
            </label>
            <label class="flex items-center w-1/2">
                <input v-model="form.is_featured" type="checkbox" class="mr-2" /> Bài viết nổi bật
            </label>
        </div>

        <div class="flex space-x-4">
            <!-- Thứ tự hiển thị hot và featured -->
            <div v-if="form.is_hot" class="w-1/2">
                <label class="text-gray-700 font-semibold">Thứ tự hot</label>
                <input v-model="form.hot_order" type="number" class="input-field" />
            </div>
            <div v-else class="w-1/2"></div>

            <div v-if="form.is_featured" class="w-1/2">
                <label class="text-gray-700 font-semibold">Thứ tự nổi bật</label>
                <input v-model="form.featured_order" type="number" class="input-field" />
            </div>
            <div v-else class="w-1/2"></div>

        </div>

        <!-- Nút Submit -->
        <button type="submit"
            class="w-full bg-gradient-to-r from-green-500 to-green-700 text-white py-2 px-4 rounded-lg hover:opacity-90 transition">
            Tạo bài viết
        </button>
        <!-- <pre>{{ auth }}</pre> -->
    </form>
</template>

<script setup lang="ts">
import { FileArchive } from 'lucide-vue-next'
import { ref, onMounted } from 'vue'
import { usePosts } from '~/composables/usePosts'
import { usePostcategories } from '#imports'
import { useAuth } from '~/composables/useAdminAuth'
import type { PostCategory } from '~/types/postcategory'

const { createPost } = usePosts()
const { fetchAllPostCategories } = usePostcategories()
const auth = useAuth()

// Form data
const form = ref<any>({
    title: '',
    summary: '',
    content: '',
    featured_image: '',
    category_id: null,
    post_status: 'draft',
    is_hot: false,
    is_featured: false,
    hot_order: null,
    featured_order: null
})

// Danh sách danh mục bài viết (lấy từ API)
const categories = ref<PostCategory[]>([])

const previewImage = ref<string | null>(null)

// Lấy danh mục từ API khi component mounted
onMounted(async () => {
    try {
        const response = await fetchAllPostCategories()
        categories.value = await response.data
    } catch (error) {
        console.error('Lỗi khi lấy danh mục:', error)
    }
})

// Xử lý upload ảnh
const handleImageUpload = (event: Event) => {
    const file = (event.target as HTMLInputElement).files?.[0]
    if (!file) return
    form.value.featured_image = file // Lưu file vào form

    const reader = new FileReader()
    reader.onload = () => {
        previewImage.value = reader.result as string
    }
    reader.readAsDataURL(file)
}

const handleSubmit = async () => {
    try {
        const formData = new FormData();
        formData.append('title', form.value.title);
        formData.append('summary', form.value.summary);
        formData.append('content', form.value.content);
        formData.append('category_id', form.value.category_id);
        formData.append('post_status', form.value.post_status);
        formData.append('is_hot', form.value.is_hot ? '1' : '0');
        formData.append('is_featured', form.value.is_featured ? '1' : '0');
        if (form.value.hot_order) formData.append('hot_order', form.value.hot_order);
        if (form.value.featured_order) formData.append('featured_order', form.value.featured_order);
        if (form.value.featured_image) formData.append('featured_image', form.value.featured_image);

        const response = await createPost(formData);
        alert('Bài viết đã được tạo thành công!');
        console.log('Bài viết mới:', response);
    } catch (error) {
        console.error('Lỗi khi tạo bài viết:', error);
        alert('Đã xảy ra lỗi khi tạo bài viết.');
    }
};

</script>

<style scoped>
.input-field {
    border: 1px solid #d1d5db;
    /* border-gray-300 */
    border-radius: 0.5rem;
    /* rounded-lg */
    width: 100%;
    /* w-full */
    padding: 0.75rem;
    /* p-3 */
    transition: all 0.2s;
    /* transition */
}

.input-field:focus {
    outline: none;
    /* focus:outline-none */
    box-shadow: 0 0 0 2px #10b981;
    /* focus:ring-2 focus:ring-green-500 */
}
</style>
