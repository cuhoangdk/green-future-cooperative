<template>
    <div>
        <button @click="scrollToTop"
            class="fixed bottom-8 right-4 bg-green-500 text-white p-2 rounded-full shadow-lg hover:bg-green-600 transition-colors duration-200">
            <ArrowUp class="w-6 h-6" />
        </button>
        <div class="fixed bottom-20 right-4 flex flex-col space-y-2">
            <a v-for="link in socialLinks" :key="link.name" :href="link.url" :class="link.bgColor" target="_blank"
                class="text-white p-2 rounded-full shadow-lg hover:opacity-80 transition-colors duration-200 z-50">
                <component :is="link.icon" class="w-6 h-6" />
            </a>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ArrowUp, Facebook, MessageCircle, Camera } from 'lucide-vue-next'

const scrollToTop = () => {
    window.scrollTo({ top: 0, behavior: 'smooth' })
}

const socialLinks = [
    {
        name: 'Facebook',
        url: 'https://www.facebook.com/yourbusiness',
        icon: Facebook,
        bgColor: 'bg-blue-600'
    },
    {
        name: 'Zalo',
        url: 'https://zalo.me/yourbusiness',
        icon: MessageCircle,
        bgColor: 'bg-blue-500'
    },
    {
        name: 'TikTok',
        url: 'https://www.tiktok.com/@yourbusiness',
        icon: Camera,
        bgColor: 'bg-black'
    }
]
</script>
