<template>
    <div class="min-h-screen items-center flex flex-col">
        <div class="w-full lg:w-1/3 rounded-2xl p-4 sm:p-5">
            <div v-if="status === 'pending'" class="flex justify-center items-center h-screen">
                <span class="loading loading-spinner loading-lg"></span>
            </div>
            <div v-else>
                <div v-for="(value, key) in contactInfo" :key="key" class="mb-4">
                    <label class="text-gray-700 font-semibold block mb-1">
                        {{ key === 'phone_contact' ? 'Số điện thoại' : key === 'email_contact' ? 'Email' : 'Địa chỉ' }}
                    </label>
                    <input
                        v-model="contactInfo[key]"
                        :type="key === 'email_contact' ? 'email' : 'text'"
                        class="input input-bordered input-primary w-full"
                        :placeholder="key === 'phone_contact' ? 'Nhập số điện thoại' : key === 'email_contact' ? 'Nhập email' : 'Nhập địa chỉ'"
                        @input="validateField(key as string)"
                        :class="{ 'input-error': contactErrors[key] }"
                    />
                    <p v-if="contactErrors[key]" class="text-error text-sm mt-1">
                        {{ key === 'phone_contact' ? 'Số điện thoại phải có 10 hoặc 11 số.' : key === 'email_contact' ? 'Vui lòng nhập email hợp lệ.' : 'Địa chỉ không được để trống.' }}
                    </p>
                </div>
                <div class="mt-4 flex justify-between items-center">
                    <UiButtonBack />
                    <button @click="handleSubmit()" class="btn btn-primary" :disabled="hasErrors">Cập nhật</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'user', title: 'Thông tin liên hệ' })

import type { Parameter } from '~/types/parameter'

const { getParameters, updateParameters } = useParameters()
const { $toast } = useNuxtApp()
const { data, status } = await getParameters(['phone_contact', 'email_contact', 'address'])

const contactData = computed<Parameter[] | null>(() => Array.isArray(data.value?.data) ? data.value.data : data.value?.data ? [data.value.data] : [])

const contactInfo = reactive<{ [key: string]: string }>({
    phone_contact: '',
    email_contact: '',
    address: '',
})

const contactErrors = reactive<{ [key: string]: boolean }>({
    phone_contact: false,
    email_contact: false,
    address: false,
})

watch(contactData, (newValue) => {
    if (newValue) {
        newValue.forEach((item: { name: string; value: string }) => {
            if (['phone_contact', 'email_contact', 'address'].includes(item.name)) {
                contactInfo[item.name] = item.value
            }
        })
    }
}, { immediate: true })

const validateField = (key: string) => {
    if (key === 'phone_contact') {
        const phoneRegex = /^[0-9]{10,11}$/
        contactErrors[key] = !phoneRegex.test(contactInfo[key])
    } else if (key === 'email_contact') {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
        contactErrors[key] = !emailRegex.test(contactInfo[key])
    } else if (key === 'address') {
        contactErrors[key] = contactInfo[key].trim() === ''
    }
}

const hasErrors = computed(() => Object.values(contactErrors).some((error) => error))

const handleSubmit = async () => {
    for (const [key, value] of Object.entries(contactInfo)) {
        if (contactErrors[key]) {
            $toast.error(`Thông tin ${key} không hợp lệ`)
            return
        }
        const { status } = await updateParameters(key, value)
        if (status.value === 'success') {
            $toast.success(`Cập nhật ${key} thành công`)
        } else {
            $toast.error(`Cập nhật ${key} thất bại`)
        }
    }
}
</script>