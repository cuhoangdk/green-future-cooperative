<template>
    <div class="p-4">
        <form @submit.prevent="handleSubmit" class="space-y-4">
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
                <button type="button" class="btn btn-ghost mr-2" @click="router.back()">Hủy</button>
                <button type="submit" class="btn btn-primary" :disabled="status === 'pending'">
                    <span v-if="status === 'pending'" class="loading loading-spinner loading-md"></span>
                    <span>Thêm</span>
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
const router = useRouter()
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
        // Redirect to the index page
        useRouter().push('/admin/product-categories')
    } catch (error: any) {
        toast.error(error.message || 'Thêm loại sản phẩm thất bại!')
    } finally {
        status.value = 'idle'
    }
}
</script>