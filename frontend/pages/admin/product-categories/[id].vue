<template>
    <div v-if="status === 'pending'" class="flex justify-center items-center h-screen">
            <span class="loading loading-spinner loading-lg"></span>
        </div>
    <div v-else class="p-4">
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
            <div class="flex justify-between items-center mt-4">
                <UiButtonBack/>
                <button type="submit" class="btn btn-primary" :disabled="submit === 'pending'">
                    <span v-if="submit === 'pending'" class="loading loading-spinner loading-md"></span>
                    <span>Lưu</span>
                </button>
            </div>
        </form>
    </div>
</template>


<script setup lang="ts">
definePageMeta({ title: 'Chỉnh sửa loại sản phẩm', layout: 'user', })

import type { ProductCategory } from '~/types/product'

const route = useRoute()
const router = useRouter()
const { getProductCategoryById, updateProductCategory } = useProductCategories()
const { $toast } = useNuxtApp()
const submit = ref<'idle' | 'pending' | 'success' | 'error'>('idle')


const { data, error, status, refresh: Refresh } = await getProductCategoryById(Number(route.params.id))
const category = computed<ProductCategory | null>(() => Array.isArray(data.value?.data) ? data.value.data[0] : data.value?.data || null)

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
        submit.value = 'pending'

        const formData = new FormData()
        formData.append('name', form.value.name)
        formData.append('description', form.value.description)

        const { error, data } = await updateProductCategory(Number(route.params.id), formData)

        if (error.value?.message) throw new Error(data.value?.message || 'Cập nhật loại sản phẩm thất bại')

        $toast.success('Cập nhật loại sản phẩm thành công!')
        useRouter().push('/admin/product-categories')
    } catch (error: any) {
        $toast.error(error.message || 'Cập nhật loại sản phẩm thất bại!')
    } finally {
        submit.value = 'idle'
    }
}
</script>