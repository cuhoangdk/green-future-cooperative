<template>
    <div class="border border-gray-200 rounded-lg p-5">
        <form @submit.prevent="handleSubmit" class="space-y-2">
            <div class="divider divider-start text-xl font-bold">Thông tin loại sản phẩm</div>
            <div class="flex flex-col gap-4">
                <!-- Farm Name -->
                <div class="w-full">
                    <label class="text-gray-700 font-semibold">Tên loại sản phẩm</label>
                    <input v-model="form.name" class="input input-primary w-full mt-1" placeholder="Rau cải..."
                        required />
                </div>
                <!-- Danh mục -->
                <div class="w-full">
                    <label class="text-gray-700 font-semibold">Mô tả</label>
                    <input v-model="form.description" class="input input-primary w-full mt-1"
                        placeholder="Rau cải tươi..." required />
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
definePageMeta({ title: 'Chỉnh sửa loại sản phẩm', layout: 'user', })

import { useToast } from 'vue-toastification'
import { useRoute } from 'vue-router'
import type { ProductCategory } from '~/types/product'

const route = useRoute()
const router = useRouter()
const { getProductCategoryById, updateProductCategory } = useProductCategories()
const toast = useToast()
const status = ref<'idle' | 'pending' | 'success' | 'error'>('idle')


const { data, error, refresh: Refresh } = await getProductCategoryById(Number(route.params.id))
const category = computed<ProductCategory | null>(() => Array.isArray(data.value?.data) ? data.value.data[0] : data.value?.data || null)
// Preview ảnh bìa hiện tại

// Khởi tạo form với các giá trị mặc định
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

        // Tạo FormData để gửi dữ liệu
        const formData = new FormData()
        formData.append('name', form.value.name)
        formData.append('description', form.value.description)

        // Gửi request cập nhật đơn vị tính
        const { error, data } = await updateProductCategory(Number(route.params.id), formData)

        if (error.value?.message) throw new Error(data.value?.message || 'Cập nhật đơn vị tính thất bại')

        toast.success('Cập nhật đơn vị tính thành công!')
        router.back()

    } catch (error: any) {
        toast.error(error.message || 'Cập nhật đơn vị tính thất bại!')
    } finally {
        status.value = 'idle'
    }
}
</script>