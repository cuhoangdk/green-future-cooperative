<template>
    <div>
        <button @click="scrollToTop"
            class="fixed bottom-14 lg:bottom-8 left-4 btn btn-square btn-primary rounded-full ">
            <ArrowUp class="w-6 h-6" />
        </button>
        <div class="fixed bottom-14 lg:bottom-8 right-4 flex flex-col space-y-2">
            <a v-for="link in socialLinks" :key="link.name" :href="link.url" :class="link.bgColor" target="_blank"
                class="btn btn-square btn-ghost rounded-full text-white z-50">
                <component :is="link.icon" class="w-6 h-6" />
            </a>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ArrowUp, Phone } from 'lucide-vue-next'
import type { Parameter } from '~/types/parameter'

const { getPhoneContact, updatePhoneContact } = useParameters()
const { $toast } = useNuxtApp()
const { data, status } = await getPhoneContact()

const phone = computed<Parameter | null>(() => Array.isArray(data.value?.data) ? data.value.data[0] : data.value?.data || null)

const scrollToTop = () => {
    window.scrollTo({ top: 0, behavior: 'smooth' })
}

const socialLinks = [
    {
        name: 'Zalo',
        url: phone.value ? `https://zalo.me/${phone.value.value}` : 'https://zalo.me/yourbusiness',
        icon: () => h('img', { src: '/images/zalo_icon.png', class: 'w-6 h-6' }),
        bgColor: 'bg-blue-500'
    },
    {
        name: 'Phone',
        url: phone.value ? `tel:${phone.value.value}` : 'tel:yourbusiness',
        icon: Phone,
        bgColor: 'bg-blue-500'
    },
]
</script>
