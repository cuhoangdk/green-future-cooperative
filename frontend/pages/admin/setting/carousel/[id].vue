<template>
    <div class="p-4">
        <form @submit.prevent="handleSubmit" class="space-y-6">
            <div class="flex flex-col items-center col-span-2">
                <div class="h-55 aspect-video mb-3 cursor-pointer" @click="triggerFileInput">
                    <img :src="form.image_url || defaultImage" @error="form.image_url = defaultImage"
                        class="w-full h-full object-cover rounded-lg border shadow-sm" alt="Slide Image" />
                </div>
                <input ref="fileInput" type="file" accept="image/*"
                    class="file-input file-input-primary w-full max-w-xs" @change="handleFileChange" hidden />
                <label class="text-gray-700 font-semibold mt-2">Ảnh bìa<span class="text-red-500">*</span></label>
            </div>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Title -->
                <div>
                    <label class="text-gray-700 font-semibold block mb-1">Tiêu đề:<span
                            class="text-red-500">*</span></label>
                    <input v-model="form.title" class="input input-bordered input-primary w-full"
                        placeholder="Enter slide title" required />
                </div>

                <!-- Link URL -->
                <div>
                    <label class="text-gray-700 font-semibold block mb-1">Liên kết đến</label>
                    <input v-model="form.link_url" type="url" class="input input-bordered input-primary w-full"
                        placeholder="https://example.com" />
                </div>

                <!-- Start Date -->
                <div>
                    <label class="text-gray-700 font-semibold block mb-1">Ngày bắt đầu</label>
                    <input v-model="form.start_date" type="date" class="input input-bordered input-primary w-full" />
                </div>

                <!-- End Date -->
                <div>
                    <label class="text-gray-700 font-semibold block mb-1">Ngày kết thúc</label>
                    <input v-model="form.end_date" type="date" class="input input-bordered input-primary w-full" />
                </div>
            </div>

            <!-- Submit Button -->
            <div class="border-t border-gray-200 pt-4 flex justify-between items-center">
                <UiButtonBack />
                <button type="submit" class="btn btn-primary px-6" :disabled="status === 'pending'">
                    <span v-if="status === 'pending'" class="loading loading-spinner loading-md"></span>
                    Lưu
                </button>
            </div>
        </form>
    </div>
</template>

<script setup lang="ts">
definePageMeta({
    title: 'Sửa Slide',
    layout: 'user',
})

import type { CarouselItem } from '~/types/carousel'

const { updateSlide, getSlideById } = useCarousel()
const config = useRuntimeConfig()
const { $toast } = useNuxtApp()
const router = useRouter()
const defaultImage = config.public.placeholderImage

const id = Number(useRoute().params.id)

const { data, refresh } = await getSlideById(id) // Fetch existing slide data if editing
const slide = computed<CarouselItem | null>(() => Array.isArray(data.value?.data) ? data.value.data[0] : data.value?.data || null)
const fileInput = ref<HTMLInputElement | null>(null)
const selectedFile = ref<File | null>(null)
const status = ref<'idle' | 'pending' | 'success' | 'error'>('idle')

// Form state
const form = ref({
    title: '',
    image_url: defaultImage,
    link_url: '',
    start_date: new Date().toISOString().split('T')[0], // Default to today
    end_date: new Date(new Date().setFullYear(new Date().getFullYear() + 1)).toISOString().split('T')[0], // Default to 1 year from today
})

// Hàm điền dữ liệu từ bài viết hiện tại
const fillFormData = () => {
  form.value = {
    title: slide.value?.title || '',
    image_url: slide.value?.image_url ? `${useRuntimeConfig().public.backendUrl}${slide.value.image_url}` : defaultImage,
    link_url: slide.value?.link_url || '',
    start_date: slide.value?.start_date ? new Date(slide.value.start_date).toISOString().split('T')[0] : '',
    end_date: slide.value?.end_date ? new Date(slide.value.end_date).toISOString().split('T')[0] : '',
  }
}
// Gọi hàm điền dữ liệu khi post sẵn sàng
watch(
  () => slide.value?.id,
  async (postId) => {
    if (postId) {
      fillFormData()
    }
  },
  { immediate: true }
)


// Trigger file input click
const triggerFileInput = () => {
    if (fileInput.value) {
        fileInput.value.click()
    }
}

// Handle file selection
const handleFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement
    const file = target.files?.[0]
    if (file) {
        selectedFile.value = file
        form.value.image_url = URL.createObjectURL(file)
    }
}

// Handle form submission
const handleSubmit = async () => {
    try {
        status.value = 'pending'

        // Create FormData
        const formData = new FormData()
        formData.append('title', form.value.title)
        if (selectedFile.value) {
            formData.append('image_url', selectedFile.value)
        }
        if (form.value.link_url) formData.append('link_url', form.value.link_url)
        if (form.value.start_date) formData.append('start_date', form.value.start_date)
        if (form.value.end_date) formData.append('end_date', form.value.end_date)

        // Submit to API
        await updateSlide(id, formData)

        $toast.success('Cập nhật thành công!')
        refresh() // Refresh the slide data
        router.push('/admin/setting/carousel') // Redirect to carousel settings page
    } catch (error: any) {
        $toast.error('Cập nhật thất bại')
    } finally {
        status.value = 'idle'
    }
}
</script>