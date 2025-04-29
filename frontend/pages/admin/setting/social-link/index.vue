<template>
    <div class="min-h-screen items-center flex flex-col">
        <div class="w-full lg:w-1/3 rounded-2xl p-4 sm:p-5">
            <div v-if="status === 'pending'" class="flex justify-center items-center h-screen">
                <span class="loading loading-spinner loading-lg"></span>
            </div>
            <div v-else>
                <div v-for="(link, key) in socialLinks" :key="key" class="mb-4">
                    <label class="text-gray-700 font-semibold block mb-1">
                        {{ (key as string).charAt(0).toUpperCase() + (key as string).slice(1) }}
                    </label>
                    <input
                        v-model="socialLinks[key]"
                        type="url"
                        class="input input-bordered input-primary w-full"
                        placeholder="https://www.facebook.com/"
                        @input="validateLink(key as string)"
                        :class="{ 'input-error': linkErrors[key] }"
                    />
                    <p v-if="linkErrors[key]" class="text-error text-sm mt-1">
                        Vui lòng nhập một liên kết hợp lệ.
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
definePageMeta({ layout: 'user', title: 'Liên kết mạng xã hội' })

import type { Parameter } from '~/types/parameter';

const { getSocialLinks, updateSocialLink } = useParameters()
const { $toast } = useNuxtApp()
const { data, status } = await getSocialLinks(['instagram','facebook','tiktok',])
const socialLinksData = computed<Parameter[] | null>(() => Array.isArray(data.value?.data) ? data.value.data : data.value?.data ? [data.value.data] : [])

const socialLinks = reactive<{ [key: string ]: string }>({
    instagram: '',
    facebook: '',
    tiktok: '',
})

const linkErrors = reactive<{ [key: string]: boolean }>({
    instagram: false,
    facebook: false,
    tiktok: false,
})

watch(socialLinksData, (newValue) => {
    if (newValue) {
        newValue.forEach((item: { name: string; value: string }) => {
            if (['instagram', 'facebook', 'tiktok'].includes(item.name)) {
                socialLinks[item.name] = item.value
            }
        })
    }
}, { immediate: true })

const validateLink = (key: string) => {
    const urlRegex = /^(https?:\/\/)?([\w\-]+\.)+[\w\-]+(\/[\w\-]*)*\/?$/
    linkErrors[key] = !urlRegex.test(socialLinks[key])
}

const hasErrors = computed(() => Object.values(linkErrors).some((error) => error))

const handleSubmit = async () => {
    for (const [key, value] of Object.entries(socialLinks)) {
        if (linkErrors[key]) {
            $toast.error(`Liên kết ${key} không hợp lệ`)
            return
        }
        const { status } = await updateSocialLink(key, value)
        if (status.value === 'success') {
            $toast.success(`Cập nhật liên ${key} thành công`)
        }
        if (status.value === 'error') {
            $toast.error(`Cập nhật liên ${key} thất bại`)
        }
    }
}
</script>