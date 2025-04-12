<template>
  <div class="p-4 ">
    <div v-if="status === 'pending'" class="flex justify-center items-center h-screen">
            <span class="loading loading-spinner loading-lg"></span>
        </div>
    <form @submit.prevent="handleSubmit" class="space-y-2">
      <!-- Tiêu đề -->
      <div class="flex space-x-4">
        <div class="w-1/2">
          <label class="text-gray-700 font-semibold">Mã bài viết</label>
          <input v-model="form.id" class="input input-primary w-full mt-1 bg-gray-100" placeholder="Bài viết về ..." readonly
            required />
        </div>
        <div class="w-1/2">
          <label class="text-gray-700 font-semibold">Tác giả</label>
          <input v-model="form.user" class="input input-primary w-full mt-1 bg-gray-100" placeholder="Bài viết về ..." readonly
            required />
        </div>
      </div>
      <div>
        <label class="text-gray-700 font-semibold">Đường dẫn đến bài viết</label>
        <input v-model="form.slug" class="input input-primary w-full mt-1" readonly />
      </div>
      <div>
        <label class="text-gray-700 font-semibold">Tiêu đề bài viết</label>
        <textarea v-model="form.title" class="textarea textarea-primary h-18 w-full mt-1" placeholder="Bài viết về ..."
          required />
      </div>
      <!-- Tóm tắt -->
      <div>
        <label class="text-gray-700 font-semibold">Tóm tắt</label>
        <textarea v-model="form.summary" class="textarea textarea-primary h-24 w-full mt-1"
          placeholder="Nội dung chính..." required />
      </div>

      <!-- Nội dung -->
      <div>
        <label class="text-gray-700 font-semibold">Nội dung</label>
        <textarea v-model="form.content" rows="30" class="textarea textarea-primary h-[700px] w-full mt-1"
          placeholder="..." required />
      </div>

      <!-- Ảnh bìa -->
      <div>
        <label class="text-gray-700 font-semibold">Ảnh bìa</label>
        <input type="file" @change="handleImageUpload" class="hidden" id="fileInput" />
        <label for="fileInput"
          class="block w-full text-center py-2 border-2 border-green-200 border-dashed rounded-lg cursor-pointer hover:bg-gray-100 transition mt-1">
          <span v-if="!previewImage" class="flex justify-center gap-3 py-8">
            <FileArchive /> Chọn ảnh đại diện
          </span>
          <div v-else class="flex justify-center">
            <img :src="previewImage" class="aspect-video h-52 object-cover rounded-lg shadow" />
          </div>
        </label>
      </div>

      <!-- Danh mục & Trạng thái -->
      <div class="flex space-x-4">
        <div class="w-1/2">
          <label class="text-gray-700 font-semibold">Danh mục</label>
          <select v-model="form.category_id" class="select select-primary w-full mt-1" required>
            <option disabled value="">Chọn danh mục</option>
            <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
          </select>
        </div>
        <div class="w-1/2">
          <label class="text-gray-700 font-semibold">Trạng thái</label>
          <select v-model="form.post_status" class="select select-primary w-full mt-1">
            <option value="draft">Nháp</option>
            <option value="published">Xuất bản</option>
          </select>
        </div>
      </div>

      <!-- Hot & Nổi bật -->
      <div class="flex space-x-4">
        <label class="flex items-center w-1/2">
          <input v-model="form.is_hot" type="checkbox" class="toggle toggle-primary mr-2 mt-1" /> Bài viết hot
        </label>
        <label class="flex items-center w-1/2">
          <input v-model="form.is_featured" type="checkbox" class="toggle toggle-primary mr-2 mt-1" /> Bài viết nổi bật
        </label>
      </div>

      <!-- Thứ tự -->
      <div class="flex space-x-4">
        <div v-if="form.is_hot" class="w-1/2">
          <label class="text-gray-700 font-semibold">Thứ tự hot</label>
          <input v-model="form.hot_order" type="number" placeholder="Số nguyên"
            class="input input-primary w-full mt-1" />
        </div>
        <div v-else class="w-1/2" />
        <div v-if="form.is_featured" class="w-1/2">
          <label class="text-gray-700 font-semibold">Thứ tự nổi bật</label>
          <input v-model="form.featured_order" type="number" placeholder="Số nguyên"
            class="input input-primary w-full mt-1" />
        </div>
        <div v-else class="w-1/2" />
      </div>

      <!-- SEO -->
      <!-- <div class="divider divider-start text-xl font-bold">SEO (Không bắt buộc nhập)</div>
      <div class="flex space-x-4">
        <div class="w-1/2">
          <label class="text-gray-700 font-semibold">Tags</label>
          <input v-model="form.tags" type="text" placeholder="Thẻ, cách nhau bằng dấu phẩy"
            class="input input-primary w-full mt-1" />
        </div>
        <div class="w-1/2">
          <label class="text-gray-700 font-semibold">Meta Title</label>
          <input v-model="form.meta_title" type="text" placeholder="Meta title"
            class="input input-primary w-full mt-1" />
        </div>
      </div>
      <div>
        <label class="text-gray-700 font-semibold">Meta Description</label>
        <textarea v-model="form.meta_description" class="textarea textarea-primary h-24 w-full mt-1"
          placeholder="Meta description" />
      </div> -->
      <div class="flex justify-end mt-6">
        <button type="button" @click="$router.back()" class="btn btn-ghost mr-2">Hủy</button>
        <button type="submit" class="btn btn-primary" :disabled="submitStatus === 'pending'">
          <span v-if="submitStatus === 'pending'" class="loading loading-spinner loading-md"></span>
          <span>Lưu</span>
        </button>
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'user', title: 'Chỉnh sửa bài viết' })

import { FileArchive } from 'lucide-vue-next'
import { usePosts } from '~/composables/usePosts'
import { usePostCategories } from '~/composables/usePostCategories'
import { useSwal } from '~/composables/useSwal'
import type { PostCategory } from '~/types/post'
import type { Post } from '~/types/post'
import { useToast } from 'vue-toastification';

const toast = useToast()
const route = useRoute()
const router = useRouter()
const swal = useSwal()
const { getPostById, updatePost } = usePosts()
const { getAllPostCategories } = usePostCategories()

// Lấy ID từ route params
const postId = route.params.id
// Lấy dữ liệu bài viết hiện tại
const { data: postData, error: postError, status, refresh: postRefresh } = await getPostById(Number(postId))
const post = computed<Post | null>(() => Array.isArray(postData.value?.data) ? postData.value.data[0] : postData.value?.data || null)
// Preview ảnh bìa hiện tại
const previewImage = ref<string | null>(post.value?.featured_image ? `${useRuntimeConfig().public.backendUrl}${post.value.featured_image}` : null)

// Lấy danh mục
const { data: categoryData } = await getAllPostCategories()
const categories = computed<PostCategory[]>(() =>
  Array.isArray(categoryData.value?.data) ? categoryData.value.data : categoryData.value ? [categoryData.value.data] : []
)

if (postError.value) {
  await swal.fire('Lỗi', 'Không thể tải bài viết!', 'error')
  router.push('/admin/posts')
}

// Tải bài viết liên quan khi post sẵn sàng
const form = ref({
  title: post.value?.title || '',
  summary: post.value?.summary || '',
  content: post.value?.content || '',
  featured_image: null as File | null,
  category_id: post.value?.category?.id || '',
  post_status: post.value?.post_status || 'published',
  is_hot: post.value?.is_hot || false,
  is_featured: post.value?.is_featured || false,
  hot_order: post.value?.hot_order || null,
  featured_order: post.value?.featured_order || null,
  tags: post.value?.tags || '',
  meta_title: post.value?.meta_title || '',
  meta_description: post.value?.meta_description || '',
  slug: post.value?.slug || '',
  created_at: post.value?.created_at || '',
  updated_at: post.value?.updated_at || '',
  deleted_at: post.value?.deleted_at || '',
  published_at: post.value?.published_at || '',
  id: post.value?.id || '',
  user: post.value?.user?.full_name || '',
})

// Hàm điền dữ liệu từ bài viết hiện tại
const fillFormData = () => {
  form.value = {
    title: post.value?.title || '',
    summary: post.value?.summary || '',
    content: post.value?.content || '',
    featured_image: null as File | null,
    category_id: post.value?.category?.id || '',
    post_status: post.value?.post_status || 'published',
    is_hot: post.value?.is_hot || false,
    is_featured: post.value?.is_featured || false,
    hot_order: post.value?.hot_order || null,
    featured_order: post.value?.featured_order || null,
    tags: post.value?.tags || '',
    meta_title: post.value?.meta_title || '',
    meta_description: post.value?.meta_description || '',
    slug: post.value?.slug ? `${window.location.origin}/posts/${post.value.slug}` : '',
    created_at: post.value?.created_at ? new Date(post.value.created_at).toLocaleDateString('vi-VN') : '',
    updated_at: post.value?.updated_at ? new Date(post.value.updated_at).toLocaleDateString('vi-VN') : '',
    deleted_at: post.value?.deleted_at ? new Date(post.value.deleted_at).toLocaleDateString('vi-VN') : '',
    published_at: post.value?.published_at ? new Date(post.value.published_at).toLocaleDateString('vi-VN') : '',
    id: post.value?.id || '',
    user: post.value?.user?.full_name || '',
  }
  previewImage.value = post.value?.featured_image ? `${useRuntimeConfig().public.backendUrl}${post.value.featured_image}` : null
}

// Gọi hàm điền dữ liệu khi post sẵn sàng
watch(
  () => post.value?.id,
  async (postId) => {
    if (postId) {
      fillFormData()
    }
  },
  { immediate: true }
)


// Upload ảnh mới
const handleImageUpload = (event: Event) => {
  const file = (event.target as HTMLInputElement).files?.[0]
  if (file) {
    form.value.featured_image = file
    previewImage.value = URL.createObjectURL(file)
  }
}

const submitStatus = ref<'idle' | 'pending' | 'success' | 'error'>('idle')

// Submit form
const handleSubmit = async () => {
  submitStatus.value = 'pending'
  const formData = new FormData()
  formData.append('title', form.value.title)
  formData.append('summary', form.value.summary)
  formData.append('content', form.value.content)
  formData.append('category_id', String(form.value.category_id))
  formData.append('post_status', form.value.post_status)
  formData.append('is_hot', form.value.is_hot ? '1' : '0')
  formData.append('is_featured', form.value.is_featured ? '1' : '0')
  if (form.value.hot_order !== null) formData.append('hot_order', String(form.value.hot_order))
  if (form.value.featured_order !== null) formData.append('featured_order', String(form.value.featured_order))
  if (form.value.featured_image) formData.append('featured_image', form.value.featured_image)
  formData.append('tags', form.value.tags)
  formData.append('meta_title', form.value.meta_title)
  formData.append('meta_description', form.value.meta_description)

  try {
    const { data, error, status } = await updatePost(Number(postId), formData)
    if (error.value) throw new Error(error.value.message || 'Failed to update post')
    submitStatus.value = status.value
    if (status.value === 'success' && data.value) {
      toast.success('Cập nhật thông tin thành công!');
      await postRefresh() // Làm mới dữ liệu sau khi cập nhật
      router.back()
    }
  } catch (error) {
    submitStatus.value = 'error'
    toast.error(`Cập nhật thất bại: ${(error as Error).message || 'Unknown error'}`)
  } finally {
    submitStatus.value = 'idle'
  }
}
</script>