<template>
    <div class="border border-gray-200 rounded-lg p-5">
        <form @submit.prevent="handleSubmit" class="space-y-2">
            <div class="divider divider-start text-xl font-bold">Thông tin nhật ký</div>
            <!-- Farm Name -->
            <div class="">
                <label class="text-gray-700 font-semibold">Hoạt động <span class="text-red-500">*</span></label>
                <textarea v-model="form.activity" class="textarea textarea-primary w-full mt-1"
                    placeholder="Phun thuốc trừ sâu..." required></textarea>
            </div>
            <!-- Farm Details -->
            <div class="flex space-x-4">
                <div class="w-1/2">
                    <label class="text-gray-700 font-semibold">Phân bón</label>
                    <input v-model="form.fertilizer_used" class="input input-primary w-full mt-1"
                        placeholder="Phân hữu cơ..." />
                </div>
                <div class="w-1/2">
                    <label class="text-gray-700 font-semibold">Thuốc bảo vệ thực vật</label>
                    <input v-model="form.pesticide_used" class="input input-primary w-full mt-1"
                        placeholder="Thuốc trừ sâu vi sinh..." />
                </div>
            </div>
            <div class="flex space-x-4">
                <div class="w-1/2">
                    <label class="text-gray-700 font-semibold">Hình ảnh <span class="text-red-500">*</span></label>
                    <input @change="handleImageUpload" type="file" class="file-input file-input-primary w-full mt-1"
                        required />
                    <div v-if="previewImage" class="flex justify-center mt-2">
                        <img :src="previewImage" class="aspect-video w-2/3 object-cover rounded-lg shadow" />
                    </div>
                </div>
                <div class="w-1/2">
                    <label class="text-gray-700 font-semibold">Đường link video <span
                            class="text-red-500">*</span></label>
                    <input v-model="form.video_url" type="url"  class="input input-primary w-full mt-1"  placeholder="https://www.youtube.com/watch?v=IE02GRUWnOw" required />

                    <div v-if="form.video_url != ''" class="flex justify-center mt-2">
                        <iframe class="w-2/3 aspect-video rounded-lg"
                            :src="`https://www.youtube.com/embed/${getYouTubeVideoID(form.video_url)}`" frameborder="0"
                            allowfullscreen></iframe>
                    </div>
                </div>
            </div>
            <!-- Description -->
            <div>
                <label class="text-gray-700 font-semibold">Ghi chú</label>
                <textarea v-model="form.notes" class="textarea textarea-primary w-full h-24 mt-1"
                    placeholder="Ghi chú..." />
            </div>
            <!-- Submit Button -->
            <div class="flex justify-end">
                <button type="submit" class="btn btn-primary" :disabled="status === 'pending'">
                    <span v-if="status === 'pending'" class="loading loading-spinner loading-md"></span>
                    <span>Thêm</span>
                </button>
            </div>
        </form>
    </div>
</template>



<script setup lang="ts">
definePageMeta({ title: 'Thêm nhật ký chăm sóc', layout: 'user', })
import { useToast } from 'vue-toastification'
import type { CultivationLog } from '~/types/product'
import { getYouTubeVideoID } from '~/utils/common'

const { createLog } = useCultivationLogs()
const { currentUser } = useUserAuth()
const toast = useToast()
const route = useRoute()
const router = useRouter()


const status = ref<'idle' | 'pending' | 'success' | 'error'>('idle')
const previewImage = ref<string | null>(null)
const productId = Number(route.params.id)

// Khởi tạo form với các giá trị mặc định
const form = ref({
    activity: '',
    fertilizer_used: '',
    pesticide_used: '',
    notes: '',
    image_url: null as File | null,
    video_url: '',
})

// Upload ảnh
const handleImageUpload = (event: Event) => {
    const file = (event.target as HTMLInputElement).files?.[0]
    if (file) {
        form.value.image_url = file
        previewImage.value = URL.createObjectURL(file) // Dùng URL.createObjectURL cho gọn
    }
}


// Xử lý submit form
const handleSubmit = async () => {
    try {
        status.value = 'pending'

        // Tạo FormData để gửi dữ liệu
        const formData = new FormData()
        formData.append('activity', form.value.activity)
        formData.append('fertilizer_used', form.value.fertilizer_used)
        formData.append('pesticide_used', form.value.pesticide_used)
        formData.append('notes', form.value.notes)
        if (form.value.image_url) formData.append('image_url', form.value.image_url)
        formData.append('video_url', form.value.video_url)
        formData.append('product_id', productId.toString())

        // Gửi request tạo nông trại
        const { error } = await createLog(productId, formData)

        if (error.value) throw new Error(error.value.message || 'Tạo nhật ký thất bại')

        toast.success('Thêm nhật ký thành công!')
        router.push(`/admin/products/${productId}/logs`)
    } catch (error: any) {
        toast.error(error.message || 'Tạo nhật ký thất bại')
    } finally {
        status.value = 'idle'
    }
}
</script>