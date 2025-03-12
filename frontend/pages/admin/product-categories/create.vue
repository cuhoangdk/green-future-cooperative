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
            </div>
            <!-- Submit Button -->
            <div class="flex justify-end">
                <button type="submit" class="btn btn-primary" :disabled="status === 'pending'">
                    <span v-if="status === 'pending'" class="loading loading-spinner loading-md"></span>
                    <span>Thêm loại sản phẩm</span>
                </button>
            </div>
        </form>
    </div>
</template>

<script setup lang="ts">
definePageMeta({ title: 'Thêm loại sản phẩm', layout: 'user', })

import { useToast } from 'vue-toastification'

const { createProductCategory } = useProductCategories()
const toast = useToast()
const status = ref<'idle' | 'pending' | 'success' | 'error'>('idle')


// Khởi tạo form với các giá trị mặc định
const form = ref({
    name: '',
    description: '',
    allow_decimal: true,
})
const data1 = ref<any>(null)
const handleSubmit = async () => {
    try {
        status.value = 'pending'

        // Tạo FormData để gửi dữ liệu
        const formData = new FormData()
        formData.append('name', form.value.name)
        formData.append('description', form.value.description)

        // Gửi request tạo nông trại
        const { error, data } = await createProductCategory(formData)
        
        if (error.value?.message) throw new Error(data.value?.message || 'Thêm loại sản phẩm thất bại!')

        toast.success('Thêm loại sản phẩm thành công!')
        // Có thể redirect hoặc reset form sau khi tạo thành công
        form.value = {
            name: '',
            description: '',
            allow_decimal: true,
        }
    } catch (error: any) {
        toast.error(error.message || 'Thêm loại sản phẩm thất bại!')
    } finally {
        status.value = 'idle'
    }
}
</script>