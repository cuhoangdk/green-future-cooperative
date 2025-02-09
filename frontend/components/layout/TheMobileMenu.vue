<template>
    <!-- Mobile Navigation Menu -->
    <nav :class="['fixed inset-0 top-16 p-4 bg-white z-40 lg:hidden transform transition-transform duration-300',
        isMobileMenuOpen ? 'translate-x-0' : 'translate-x-full']">
        <div class="text-left flex flex-col items-start justify-start h-full space-y-3">
            <NuxtLink v-for="link in mobileNavLinks" :key="link.path" :to="link.path"
                class="text-gray-600 hover:text-green-500 text-2xl font-semibold transition-colors duration-200 bg-gray-100 p-2 rounded-lg hover:bg-gray-200 w-full">
                {{ link.text }}
            </NuxtLink>
        </div>
    </nav>
</template>

<script setup lang="ts">
import { ref } from 'vue'

const isMobileMenuOpen = ref(false)

const mobileNavLinks = [
    { path: '/', text: 'Trang chủ' },
    { path: '/products', text: 'Sản phẩm' },
    { path: '/posts', text: 'Bài viết' },
    { path: '/about', text: 'Về chúng tôi' },
    { path: '/contact', text: 'Liên hệ' },
    { path: '/login', text: 'Đăng nhập / Đăng ký' }
]

const toggleMobileMenu = () => {
    isMobileMenuOpen.value = !isMobileMenuOpen.value
}
</script>