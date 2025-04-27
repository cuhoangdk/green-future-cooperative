<template>
    <div class="min-h-screen items-center flex flex-col">
        <div class="w-full lg:w-1/3 rounded-2xl p-4 sm:p-5">
            <div v-if="status === 'pending'" class="flex justify-center items-center h-screen">
                <span class="loading loading-spinner loading-lg"></span>
            </div>
            <div v-else>
                <div>
                    <label class="text-gray-700 font-semibold block mb-1">Số điện thoại</label>
                    <input
                        v-model="shippingFee"
                        type="tel"
                        class="input input-bordered input-primary w-full"
                        placeholder="Nhập số điện thoại"
                        pattern="[0-9]{10,11}"
                        minlength="10"
                        maxlength="11"
                        @input="validatePhoneNumber"
                        :class="{ 'input-error': phoneError }"
                    />
                    <p v-if="phoneError" class="text-error text-sm mt-1">Số điện thoại phải có 10 hoặc 11 số.</p>
                </div>
                <div class="mt-4 flex justify-between items-center">
                    <UiButtonBack />
                    <button @click="handleSubmit()" class="btn btn-primary" :disabled="phoneError">Cập nhật</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'user', title: 'Số điện thoại liên hệ' })

import type { Parameter } from '~/types/parameter'

const { getPhoneContact, updatePhoneContact } = useParameters()
const { $toast } = useNuxtApp()
const { data, status } = await getPhoneContact()

const phone = computed<Parameter | null>(() => Array.isArray(data.value?.data) ? data.value.data[0] : data.value?.data || null)

const shippingFee = ref<string | null>(null)
const phoneError = ref(false)

watch(phone, (newValue) => {
    if (newValue) {
        shippingFee.value = newValue.value
    }
}, { immediate: true })

const validatePhoneNumber = () => {
    if (!shippingFee.value) {
        phoneError.value = true
        return
    }
    const phoneRegex = /^[0-9]{10,11}$/
    phoneError.value = !phoneRegex.test(shippingFee.value)
}

const handleSubmit = async () => {
    if (!shippingFee.value || phoneError.value) {
        $toast.error('Vui lòng nhập số điện thoại hợp lệ (10-11 số)')
        return
    }
    const { status } = await updatePhoneContact(shippingFee.value)
    if (status.value === 'success') {
        $toast.success('Cập nhật số điện thoại liên hệ thành công')
    } else {
        $toast.error('Cập nhật số điện thoại liên hệ thất bại')
    }
}
</script>