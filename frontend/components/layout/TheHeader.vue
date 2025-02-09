<template>
    <header class="bg-white shadow-sm text-black lg:relative fixed top-0 w-full z-50">
        <!-- Top row with logo and user actions -->
        <div class="bg-green-600 border-b border-green-700">
            <div class="max-w-7xl mx-auto px-4 lg:px-8">
                <div class="flex flex-wrap lg:flex-nowrap items-center justify-between py-1">
                    <div class="flex items-center space-x-4">
                        <img src="../../assets/images/logo.jpg" alt="Logo Green Future"
                            class="rounded-full w-12 h-12 lg:w-18 lg:h18 object-cover" />
                        <h1 class="text-2xl lg:text-3xl font-bold text-white hidden lg:block">
                            <NuxtLink to="/" class="">Green Future</NuxtLink>
                        </h1>
                    </div>

                    <!-- Search bar -->
                    <div class="w-1/2">
                        <div class="relative">
                            <input type="search" placeholder="Tìm kiếm..."
                                class="pl-4 pr-12 py-2 rounded-full border-2 border-green-500 bg-green-700 text-white placeholder-green-200 focus:border-green-400 focus:outline-none w-full" />
                            <SearchIcon
                                class="w-8 h-8 p-1.5 bg-green-500 text-white hover:bg-white hover:text-gray-800 transition-colors duration-150 rounded-full absolute right-2 top-1/2 transform -translate-y-1/2" />
                        </div>
                    </div>

                    <!-- Cart and Login -->
                    <div class="flex items-center space-x-4">
                        <button class="relative">
                            <ShoppingCart
                                class="w-6 h-6 text-white hover:text-green-200 transition-colors duration-200" />
                            <span
                                class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                                0
                            </span>
                        </button>

                        <button
                            class="hidden lg:block bg-white font-semibold text-green-600 px-4 py-2 rounded-full hover:bg-green-100 transition-colors duration-200">
                            Đăng nhập
                        </button>

                        <button @click="toggleMobileMenu" class="lg:hidden z-50 p-2 rounded-lg bg-green-500 text-white">
                            <Menu class="w-6 h-6" />
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Desktop Navigation -->
        <nav class="hidden lg:block max-w-7xl mx-auto px-4 lg:px-8 py-3">
            <div class="flex justify-center space-x-6 font-semibold">
                <NuxtLink to="/" class="text-green-500 transition-colors duration-200">Trang chủ</NuxtLink>
                <NuxtLink to="/products" class="text-gray-600 hover:text-green-500 transition-colors duration-200">Sản
                    phẩm</NuxtLink>
                <NuxtLink to="/posts" class="text-gray-600 hover:text-green-500 transition-colors duration-200">Bài viết
                </NuxtLink>
                <NuxtLink to="/about" class="text-gray-600 hover:text-green-500 transition-colors duration-200">Về chúng
                    tôi</NuxtLink>
                <NuxtLink to="/contact" class="text-gray-600 hover:text-green-500 transition-colors duration-200">Liên
                    hệ</NuxtLink>
            </div>
        </nav>

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
    </header>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { Menu, ShoppingCart, Search as SearchIcon } from 'lucide-vue-next'

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