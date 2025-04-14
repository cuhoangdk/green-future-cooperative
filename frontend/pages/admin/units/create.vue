<template>
    <div class="p-4">
        <form @submit.prevent="handleSubmit" class="space-y-4">
            <div class="flex flex-col gap-4">
                <!-- Farm Name -->
                <div class="w-full">
                    <label class="text-gray-700 font-semibold">Tên đơn vị tính</label>
                    <input v-model="form.name" class="input input-primary w-full mt-1" placeholder="Kg/g..."
                        required />
                </div>
                <!-- Danh mục -->
                <div class="w-full">
                    <label class="text-gray-700 font-semibold">Mô tả</label>
                    <input v-model="form.description" class="input input-primary w-full mt-1"
                        placeholder="Kilogram/Gram..." required />
                </div>
                <label class="flex items-center w-full">
                    <input v-model="form.allow_decimal" type="checkbox" class="checkbox checkbox-secondary mr-2" />Cho phép số lượng lẻ
                </label>
            </div>
            <!-- Submit Button -->
            <div class="flex justify-end">
                <button type="button" @click="$router.back()" class="btn btn-ghost mr-2">Hủy</button>
                <button type="submit" class="btn btn-primary" :disabled="status === 'pending'">
                    <span v-if="status === 'pending'" class="loading loading-spinner loading-md"></span>
                    <span>Thêm</span>
                </button>
            </div>
        </form>
    </div>
</template>

<script setup lang="ts">
definePageMeta({ title: 'Thêm đơn vị tính', layout: 'user', })

const { createUnit } = useUnits()
const { $toast } = useNuxtApp()
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
        formData.append('allow_decimal', form.value.allow_decimal ? '1' : '0')

        // Gửi request tạo nông trại
        const { error, data } = await createUnit(formData)
        
        if (error.value?.message) throw new Error(data.value?.message || 'Tạo nông trại thất bại')

        $toast.success('Tạo nông trại thành công!')
        // Có thể redirect hoặc reset form sau khi tạo thành công
        form.value = {
            name: '',
            description: '',
            allow_decimal: true,
        }
    } catch (error: any) {
        $toast.error(error.message || 'Tạo nông trại thất bại!')
    } finally {
        status.value = 'idle'
    }
}
</script>