<template>
    <div class="p-4">
        <div v-if="status === 'pending'" class="flex justify-center items-center h-screen">
            <span class="loading loading-spinner loading-lg"></span>
        </div>
        <div v-else>
            <div>
                <label class="text-gray-700 font-semibold block mb-1">Phí vận chuyển (VNĐ)</label>
                <input v-model="shippingFee" class="input input-bordered input-primary w-full" type="number"
                    placeholder="Enter shipping fee" />
            </div>
            <div class="mt-4 flex justify-between items-center">
                <UiButtonBack />
                <button @click="handleSubmit()" class="btn btn-primary">Cập nhật</button>
            </div>
        </div>

    </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'user', title: 'Phí vận chuyển' })

import type { Parameter } from '~/types/parameter'

const { getShippingFee, updateShippingFee } = useShippingFee()
const { $toast } = useNuxtApp()
const { data, status } = await getShippingFee()

const shippingFeeData = computed<Parameter | null>(() => Array.isArray(data.value?.data) ? data.value.data[0] : data.value?.data || null)

const shippingFee = ref<number | null>()

watch(shippingFeeData, (newValue) => {
    if (newValue) {
        shippingFee.value = Number(newValue.value)
    }
}, { immediate: true })

const handleSubmit = async () => {
    if (shippingFee.value === null) {
        $toast.error('Vui lòng nhập phí vận chuyển')
        return
    }
    const { status } = await updateShippingFee(String(shippingFee.value ?? 0))
    if (status.value === 'success') {
        $toast.success('Cập nhật phí vận chuyển thành công')
    } else {
        $toast.error('Cập nhật phí vận chuyển thất bại')
    }
}
</script>