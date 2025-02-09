<template>
    <div class="fixed bottom-20 right-4 flex flex-col space-y-2">
        <a v-for="link in socialLinks" :key="link.name" :href="link.url" :class="link.bgColor" target="_blank"
            class="text-white p-2 rounded-full shadow-lg hover:opacity-80 transition-colors duration-200">
            <component :is="link.icon" class="w-6 h-6" />
        </a>
    </div>
</template>

<script setup lang="ts">
import { Facebook, MessageCircle, Camera } from 'lucide-vue-next'

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