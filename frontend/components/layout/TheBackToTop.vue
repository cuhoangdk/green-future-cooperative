<template>
    <button @click="scrollToTop"
        class="fixed bottom-8 right-4 bg-green-500 text-white p-2 rounded-full shadow-lg hover:bg-green-600 transition-colors duration-200">
        <ArrowUp class="w-6 h-6" />
    </button>
</template>

<script setup lang="ts">
import { ArrowUp } from 'lucide-vue-next'
const scrollToTop = () => {
    window.scrollTo({ top: 0, behavior: 'smooth' })
}
</script>