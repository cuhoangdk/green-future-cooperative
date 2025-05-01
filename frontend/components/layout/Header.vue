<template>
    <div class="bg-white shadow-sm text-black lg:relative fixed top-0 w-full z-50">
        <!-- Top row with logo and user actions -->
        <div class="bg-green-600">
            <div class="max-w-9xl mx-auto px-4 lg:px-8">
                <div class="flex items-center justify-between py-1 gap-2">
                    <div class="flex items-center gap-2">
                        <img src="~/assets/images/logo.png" alt="Logo Green Future"
                            class="rounded-full w-12 h-12 lg:w-14 lg:h-14 object-cover" />
                        <h1 class="text-2xl lg:text-3xl font-bold text-white hidden lg:block">
                            <NuxtLink to="/" class="">Green Future</NuxtLink>
                        </h1>
                    </div>

                    <!-- Search bar -->
                    <div class="w-1/2">
                        <ProductSearchInput />
                    </div>

                    <!-- Cart and Login -->
                    <div class="flex items-center gap-2">
                        <NuxtLink to="/cart" class="relative">
                            <ShoppingCart
                                class="w-6 h-6 text-white hover:text-green-200 transition-colors duration-200" />
                            <!-- <span
                                class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                                
                            </span> -->
                        </NuxtLink>

                        <NuxtLink v-if="!refreshToken" to="/login"
                            class="hidden lg:block bg-white font-semibold text-green-600 px-4 py-2 rounded-full hover:bg-green-100 transition-colors duration-200">
                            Đăng nhập
                        </NuxtLink>
                        <div v-else class="dropdown dropdown-bottom dropdown-end">
                            <button tabindex="0"
                                class="hidden lg:block text-white underline font-semibold hover:text-green-200 transition-colors duration-200">
                                {{ currentCustomer?.full_name || 'Name' }}
                            </button>
                            <ul tabindex="0"
                                class="dropdown-content menu bg-base-100 rounded-box z-1 w-52 p-2 shadow-sm">
                                <li>
                                    <NuxtLink to="/account" @click="closeDropdown">
                                        Tài khoản
                                    </NuxtLink>
                                </li>
                                <li>
                                    <NuxtLink to="/orders" @click="closeDropdown">
                                        Đơn hàng
                                    </NuxtLink>
                                </li>
                                <li>
                                    <NuxtLink to="/account/addresses" @click="closeDropdown">
                                        Địa chỉ
                                    </NuxtLink>
                                </li>
                                <li>
                                    <button @click="handleLogout" class="w-full text-left">
                                        Đăng xuất
                                    </button>
                                </li>
                            </ul>
                        </div>
                        <button @click="toggleMobileMenu"
                            :class="['lg:hidden z-50 p-2 rounded-lg text-white transition-colors duration-200', isMobileMenuOpen ? 'bg-green-500' : 'hover:bg-green-500']">
                            <Menu class="w-6 h-6" />
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Desktop Navigation -->
        <nav class="hidden lg:block max-w-7xl mx-auto px-4 lg:px-8 py-3">
            <div class="flex justify-center space-x-6 font-semibold">
                <NuxtLink v-for="link in navLinks" :key="link.path" :to="link.path"
                    :class="['transition-colors duration-200', isActive(link.path) ? 'text-green-500' : 'text-gray-600 hover:text-green-500']">
                    {{ link.text }}
                </NuxtLink>
            </div>
        </nav>

        <!-- Mobile Navigation Menu -->
        <nav :class="['fixed top-14 right-0 p-4 bg-white z-40 lg:hidden transform transition-transform duration-300 w-2/3 h-full shadow-lg',
            isMobileMenuOpen ? 'translate-x-0' : 'translate-x-full']">
            <div class="text-left flex flex-col items-start justify-start h-full space-y-3">
                <NuxtLink v-for="link in mobileNavLinks" :key="link.path" :to="link.path" @click="closeMobileMenu"
                    :class="['text-2xl font-semibold transition-colors duration-200 bg-gray-100 p-2 rounded-lg w-full', isActive(link.path) ? 'text-green-500 bg-green-100' : 'text-gray-600 hover:text-green-500 hover:bg-gray-200']">
                    {{ link.text }}
                </NuxtLink>
            </div>
        </nav>
    </div>
</template>

<script setup lang="ts">
import { Menu, ShoppingCart, Search as SearchIcon } from 'lucide-vue-next'

const { logout ,currentCustomer, refreshToken, isAuthenticated } = useCustomerAuth()
const route = useRoute()
const router = useRouter()
const { $toast } = useNuxtApp()
const isMobileMenuOpen = ref(false)

// Danh sách link cho cả desktop và mobile
const navLinks = [
    { path: '/', text: 'Trang chủ' },
    { path: '/products', text: 'Sản phẩm' },
    { path: '/posts', text: 'Bài viết' },
    { path: '/about', text: 'Về chúng tôi' },
    { path: '/contact', text: 'Liên hệ' },
]

const mobileNavLinks = [
    ...navLinks,
    ...(isAuthenticated ? [] : [{ path: '/login', text: 'Đăng nhập / Đăng ký' }])
]

// Hàm kiểm tra trang đang active
const isActive = (path: string) => {
    return route.path === path || (path !== '/' && route.path.startsWith(path))
}

// Toggle menu mobile
const toggleMobileMenu = () => {
    isMobileMenuOpen.value = !isMobileMenuOpen.value
}

// Đóng menu mobile khi chuyển trang
const closeMobileMenu = () => {
    isMobileMenuOpen.value = false
}

// Đóng dropdown khi chọn một mục
const closeDropdown = () => {
    const dropdownButton = document.activeElement as HTMLElement
    if (dropdownButton) {
        dropdownButton.blur() // Xóa focus khỏi button để đóng dropdown
    }
}

const handleLogout = async () => {
    try {
        await logout();
        $toast.success('Đăng xuất thành công!');
        // Chuyển hướng về trang đăng nhập hoặc trang chủ
        useRouter().push('/login');
    } catch (error: any) {
        $toast.error(error.message || 'Đăng xuất thất bại!');
    }
};

</script>