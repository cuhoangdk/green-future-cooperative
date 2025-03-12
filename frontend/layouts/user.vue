<template>
    <div class="min-h-screen">
        <!-- Overlay for mobile sidebar -->
        <div @click="toggleSidebar" :class="['fixed inset-0 bg-black/50 z-40 lg:hidden', { 'hidden': !isSidebarOpen }]">
        </div>

        <div class="flex">
            <!-- Sidebar -->
            <aside :class="[
                'fixed inset-y-0 left-0 w-52 h-screen bg-gray-900 text-white transition-transform duration-300 ease-in-out z-50',
                { '-translate-x-full lg:translate-x-0': !isSidebarOpen }
            ]">
                <!-- Brand -->
                <div class="p-3 bg-gray-800 text-center text-xl font-bold shadow-lg">
                    Admin Panel
                </div>

                <!-- Navigation -->
                <nav class="mt-4 px-2">
                    <ul class="space-y-1 ">
                        <li v-for="(menu, index) in menus" :key="index">
                            <NuxtLink :to="menu.route" :class="[
                                'flex items-center px-4 py-3 rounded-lg transition-colors duration-200',
                                'hover:bg-gray-800 hover:text-white',
                                { 'bg-gray-800 text-white': isCurrentRoute(menu.route) },
                                { 'text-gray-400': !isCurrentRoute(menu.route) }
                            ]">
                                <Circle class="w-6 h-6 mr-2" />
                                <span>{{ menu.label }}</span>
                            </NuxtLink>
                        </li>
                    </ul>
                </nav>
            </aside>

            <!-- Main Content -->
            <div class="flex-1 lg:ml-52 ">
                <!-- Top Bar -->
                <header class="sticky top-0 z-30 bg-white border-b border-gray-200">
                    <div class="flex items-center justify-between px-4 py-1.5">
                        <div class="flex items-center">
                            <button @click="toggleSidebar" class="p-2 rounded-lg lg:hidden hover:bg-gray-100">
                                <MenuIcon class="w-6 h-6" />
                            </button>

                            <h1 class="font-bold text-lg ml-4">
                                {{ pageTitle }}
                            </h1>
                        </div>

                        <!-- User Dropdown -->
                        <div class="relative" v-click-outside="closeUserMenu">
                            <button @click="toggleUserMenu"
                                class="flex items-center space-x-2 hover:bg-gray-100 px-3 py-1 rounded-lg">
                                <!-- <User2Icon class="w-6 h-6" /> -->
                                <span>{{ currentUser?.full_name }}</span>
                                <div class="avatar">
                                    <div class="w-8 rounded">
                                        <img :src="avatar" @error="avatar = defaultAvatar"/>
                                    </div>
                                </div>
                                <!-- <ChevronDown class="w-4 h-4" /> -->
                            </button>

                            <div v-show="isUserMenuOpen" 
                                class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-1 z-50">
                                <NuxtLink to="/admin/profile" v-click="closeUserMenu"
                                    class="px-4 py-2 text-gray-700 hover:bg-gray-100 flex gap-2">
                                    <User2Icon /> Profile
                                </NuxtLink>
                                <hr class="my-1 border-gray-200">
                                <button @click="handleLogout"
                                    class="w-full text-left px-4 py-2 text-red-600 hover:bg-gray-100 flex gap-2">
                                    <LogOut /> Logout
                                </button>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Page Content -->
                <div class="p-2">
                    <slot />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { MenuIcon, User2Icon, NewspaperIcon, ChevronDown, LogOut, Circle } from 'lucide-vue-next'
import { useRuntimeConfig } from '#app'

const { logout, currentUser } = useUserAuth()
const isSidebarOpen = ref(false)
const isUserMenuOpen = ref(false)
const route = useRoute()
const defaultAvatar = useRuntimeConfig().public.placeholderImage
const backendUrl = useRuntimeConfig().public.backendUrl
const avatar = computed(() => currentUser.value?.avatar_url ? `${backendUrl}${currentUser.value.avatar_url}` : defaultAvatar)

const handleLogout = () => {
    logout()  // Nếu logout() là async, đảm bảo chờ hoàn thành trước khi chuyển trang
    navigateTo('/admin/login')
}

// You'll need to define your menus in a configuration file or fetch from API
const menus = ref([
    { label: 'Dashboard', route: '/admin', icon: 'fas fa-home' },
    { label: 'Users', route: '/admin/users', icon: 'fas fa-users' },
    { label: 'Posts', route: '/admin/posts', icon: 'NewspaperIcon' },
    { label: 'Farms', route: '/admin/farms', icon: 'fas fa-cog' },
    { label: 'Units', route: '/admin/units', icon: 'fas fa-cog' },
    { label: 'Product Categories', route: '/admin/product-categories', icon: 'fas fa-cog' },
    // { label: 'Settings', route: '/admin/settings', icon: 'fas fa-cog' },
    // Add more menu items as needed
])

const pageTitle = computed(() => {
    // Implement your page title logic here
    return route.meta.title || 'Dashboard'
})

const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value
}

const toggleUserMenu = () => {
    isUserMenuOpen.value = !isUserMenuOpen.value
}

const closeUserMenu = () => {
    isUserMenuOpen.value = false
}

const isCurrentRoute = (path: string) => {
    return route.path === path
}

// Close sidebar on route change for mobile
watch(route, () => {
    isSidebarOpen.value = false
})

// Handle escape key to close menus
onMounted(() => {
    const handleEscape = (e: KeyboardEvent) => {
        if (e.key === 'Escape') {
            isUserMenuOpen.value = false
            if (window.innerWidth < 1024) {
                isSidebarOpen.value = false
            }
        }
    }

    window.addEventListener('keydown', handleEscape)
    onUnmounted(() => {
        window.removeEventListener('keydown', handleEscape)
    })
})
</script>