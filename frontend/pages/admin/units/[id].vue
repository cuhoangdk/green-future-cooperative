<template>
    <div class="border border-gray-200 rounded-lg px-5 py-3 ">
        <form @submit.prevent="handleSubmit" class="space-y-2">
            <div class="divider divider-start text-xl font-bold">Chỉnh sửa đơn vị tính</div>
            <div class="flex flex-col gap-4">
                <!-- Unit Name -->
                <div class="w-full">
                    <label class="text-gray-700 font-semibold">Tên đơn vị tính</label>
                    <input v-model="form.name" class="input input-primary w-full mt-1" placeholder="Kg/g..." required />
                </div>
                <!-- Description -->
                <div class="w-full">
                    <label class="text-gray-700 font-semibold">Mô tả</label>
                    <input v-model="form.description" class="input input-primary w-full mt-1"
                        placeholder="Kilogram/Gram..." required />
                </div>
                <label class="flex items-center w-full">
                    <input v-model="form.allow_decimal" type="checkbox" class="toggle toggle-primary mr-2 mt-1" />Cho
                    phép
                    số lượng lẻ
                </label>
            </div>
            <!-- Submit Button -->
            <div class="flex justify-end">
                <button type="submit" class="btn btn-primary" :disabled="status === 'pending'">
                    <span v-if="status === 'pending'" class="loading loading-spinner loading-md"></span>
                    <span>Lưu thay đổi</span>
                </button>
            </div>
        </form>
    </div>
</template>

<script setup lang="ts">
definePageMeta({ title: 'Chỉnh sửa đơn vị tính', layout: 'user', })

import { useToast } from 'vue-toastification'
import { useRoute } from 'vue-router'
import type { Unit } from '~/types/product'

const route = useRoute()
const router = useRouter()
const { getUnitById, updateUnit } = useUnits()
const toast = useToast()
const status = ref<'idle' | 'pending' | 'success' | 'error'>('idle')


const { data: postData, error: postError, refresh: postRefresh } = await getUnitById(Number(route.params.id))
const unit = computed<Unit | null>(() => Array.isArray(postData.value?.data) ? postData.value.data[0] : postData.value?.data || null)
// Preview ảnh bìa hiện tại

// Khởi tạo form với các giá trị mặc định
const form = ref({
    name: '',
    description: '',
    allow_decimal: false,
})

watch(unit, (newUnit) => {
    if (newUnit) {
        form.value.name = newUnit.name || ''
        form.value.description = newUnit.description || ''
        form.value.allow_decimal = newUnit.allow_decimal || false
    }
}, { immediate: true })

const handleSubmit = async () => {
    try {
        status.value = 'pending'

        // Tạo FormData để gửi dữ liệu
        const formData = new FormData()
        formData.append('name', form.value.name)
        formData.append('description', form.value.description)
        formData.append('allow_decimal', form.value.allow_decimal ? '1' : '0')

        // Gửi request cập nhật đơn vị tính
        const { error, data } = await updateUnit(Number(route.params.id), formData)

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