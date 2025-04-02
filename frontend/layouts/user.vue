<template>
    <div class="min-h-screen">
        <!-- Overlay for mobile sidebar -->
        <div @click="toggleSidebar" :class="['fixed inset-0 bg-black/50 z-40 lg:hidden', { 'hidden': !isSidebarOpen }]">
        </div>

        <div class="flex">
            <!-- Sidebar -->
            <aside :class="[
                'fixed inset-y-0 left-0 h-screen bg-white border-r border-gray-200 transition-all duration-300 ease-in-out z-50',
                isCollapsed ? 'w-16' : 'w-52',
                { 'translate-x-0': isSidebarOpen, '-translate-x-full': !isSidebarOpen && windowWidth < 1024 }
            ]">
                <!-- Brand -->
                <div class="p-3 bg-white border-b border-gray-200 text-center text-xl font-bold text-gray-800 flex items-center justify-between">
                    <span v-show="!isCollapsed">Admin Panel</span>
                    <button @click="toggleCollapse" class="p-1 mx-auto hover:bg-gray-100 rounded">
                        <MenuIcon class="w-5 h-5 text-gray-600" />
                    </button>
                </div>

                <!-- Navigation -->
                <nav class="mt-4 px-2">
                    <ul class="space-y-2">
                        <!-- Menu items không thuộc nhóm -->
                        <li v-for="(menu, index) in nonGroupedMenus" :key="`non-group-${index}`">
                            <NuxtLink :to="menu.route" :class="[
                                'flex items-center py-3 rounded-lg transition-colors duration-200',
                                'hover:bg-gray-100 hover:text-gray-900',
                                isCollapsed ? 'px-2 justify-center' : 'px-4',
                                { 'bg-gray-200 text-gray-900': isCurrentRoute(menu.route) },
                                { 'text-gray-600': !isCurrentRoute(menu.route) }
                            ]" :title="isCollapsed ? menu.label : ''">
                                <component :is="menu.icon" class="w-5 h-5" :class="isCollapsed ? '' : 'mr-3'" />
                                <span v-show="!isCollapsed">{{ menu.label }}</span>
                            </NuxtLink>
                        </li>

                        <!-- Menu groups -->
                        <li v-for="(group, groupIndex) in groupedMenus" :key="`group-${groupIndex}`">
                            <div class="relative">
                                <NuxtLink :to="group.route" :class="[
                                    'flex items-center w-full py-3 rounded-lg transition-colors duration-200',
                                    'hover:bg-gray-100 hover:text-gray-900',
                                    isCollapsed ? 'px-2 justify-center' : 'px-4',
                                    { 'bg-gray-200 text-gray-900': isCurrentRoute(group.route) },
                                    { 'text-gray-600': !isCurrentRoute(group.route) }
                                ]" :title="isCollapsed ? group.label : ''">
                                    <component :is="group.icon" class="w-5 h-5" :class="isCollapsed ? '' : 'mr-3'" />
                                    <span v-show="!isCollapsed" class="flex-1 text-left">{{ group.label }}</span>
                                </NuxtLink>
                                <button v-show="!isCollapsed" @click="toggleGroup(groupIndex)"
                                    class="absolute right-4 top-1/2 -translate-y-1/2 p-1 hover:bg-gray-200 rounded">
                                    <ChevronDown class="w-4 h-4 transition-transform" :class="{ 'rotate-180': group.isOpen }" />
                                </button>
                            </div>

                            <!-- Submenu -->
                            <ul v-show="group.isOpen && !isCollapsed" class="ml-2 mt-1 space-y-1">
                                <li v-for="(item, itemIndex) in group.items" :key="`item-${itemIndex}`">
                                    <NuxtLink :to="item.route" :class="[
                                        'flex items-center py-2 px-4 rounded-lg transition-colors duration-200',
                                        'hover:bg-gray-100 hover:text-gray-900',
                                        { 'bg-gray-200 text-gray-900': isCurrentRoute(item.route) },
                                        { 'text-gray-600': !isCurrentRoute(item.route) }
                                    ]">
                                        <component :is="item.icon" class="w-5 h-5 mr-3" />
                                        <span>{{ item.label }}</span>
                                    </NuxtLink>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </nav>
            </aside>

            <!-- Main Content -->
            <div :class="[
                'flex-1 transition-all duration-300',
                {
                    'ml-16': isCollapsed && windowWidth >= 1024,
                    'ml-52': !isCollapsed && windowWidth >= 1024,
                    'ml-0': windowWidth < 1024
                }
            ]">
                <!-- Top Bar -->
                <header class="sticky top-0 z-30 bg-white border-b border-gray-200">
                    <div class="flex items-center justify-between px-4 py-1.5">
                        <div class="flex items-center">
                            <button @click="toggleSidebar" class="p-2 rounded-lg lg:hidden hover:bg-gray-100">
                                <MenuIcon class="w-6 h-6 text-gray-600" />
                            </button>
                            <h1 class="font-bold text-lg ml-4 text-gray-800">
                                {{ pageTitle }}
                            </h1>
                        </div>

                        <!-- User Dropdown -->
                        <div class="relative" v-click-outside="closeUserMenu">
                            <button @click="toggleUserMenu"
                                class="flex items-center space-x-2 hover:bg-gray-100 px-3 py-1 rounded-lg">
                                <span class="text-gray-700">{{ currentUser?.full_name }}</span>
                                <div class="avatar">
                                    <div class="w-8 rounded">
                                        <img :src="avatar" @error="avatar = defaultAvatar" />
                                    </div>
                                </div>
                                <ChevronDown class="w-4 h-4 text-gray-600" />
                            </button>

                            <div v-show="isUserMenuOpen"
                                class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-1 z-50 border border-gray-200">
                                <NuxtLink to="/admin/profile" v-click="closeUserMenu"
                                    class="px-4 py-2 text-gray-700 hover:bg-gray-100 flex gap-2">
                                    <User2Icon class="w-5 h-5" /> Profile
                                </NuxtLink>
                                <hr class="my-1 border-gray-200">
                                <button @click="handleLogout"
                                    class="w-full text-left px-4 py-2 text-red-600 hover:bg-gray-100 flex gap-2">
                                    <LogOut class="w-5 h-5" /> Logout
                                </button>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Page Content -->
                <div class="p-4">
                    <slot />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import {
    MenuIcon,
    User2Icon,
    ChevronDown,
    LogOut,
    Home,
    Users,
    Newspaper,
    Sprout,
    ShoppingBag,
    Ruler,
    Layers,
    Ticket
} from 'lucide-vue-next'
import { useRuntimeConfig } from '#app'
import { useToast } from 'vue-toastification';

const { logout, currentUser } = useUserAuth()
const toast = useToast();

const isSidebarOpen = ref(false)
const isUserMenuOpen = ref(false)
const isCollapsed = ref(false)
const route = useRoute()
const defaultAvatar = useRuntimeConfig().public.placeholderImage
const backendUrl = useRuntimeConfig().public.backendUrl
const windowWidth = ref(window.innerWidth)
const avatar = computed(() => currentUser.value?.avatar_url ? `${backendUrl}${currentUser.value.avatar_url}` : defaultAvatar)

const handleLogout = async () => {
    try {
        await logout();
        toast.success('Đăng xuất thành công!');
        // Chuyển hướng về trang đăng nhập hoặc trang chủ
        useRouter().push('/admin/login');
    } catch (error: any) {
        toast.error(error.message || 'Đăng xuất thất bại!');
    }
};

// Menu không thuộc nhóm
const nonGroupedMenus = ref([
    { label: 'Trang chủ', route: '/admin', icon: Home },
    { label: 'Thành viên', route: '/admin/users', icon: Users },
    { label: 'Khách hàng', route: '/admin/customers', icon: User2Icon },
    { label: 'Nông trại', route: '/admin/farms', icon: Sprout },
    { label: 'Đơn hàng', route: '/admin/orders', icon: Ticket },
    { label: 'Cài đặt', route: '#', icon: Ruler },
])

// Menu nhóm (menu chính là trang index)
const groupedMenus = ref([
    {
        label: 'Sản phẩm',
        route: '/admin/products', // Trang index của nhóm
        icon: ShoppingBag,
        isOpen: false,
        items: [
            { label: 'Loại sản phẩm', route: '/admin/product-categories', icon: Layers },
            { label: 'Đơn vị', route: '/admin/units', icon: Ruler },
        ]
    },
    {
        label: 'Bài viết',
        route: '/admin/posts', // Trang index của nhóm
        icon: Newspaper,
        isOpen: false,
        items: [
            { label: 'Loại bài viết', route: '/admin/post-categories', icon: Layers },
        ]
    }
])

const pageTitle = computed(() => {
    return route.meta.title || 'Dashboard'
})

const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value
}

const toggleCollapse = () => {
    isCollapsed.value = !isCollapsed.value
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

const toggleGroup = (groupIndex: number) => {
    groupedMenus.value[groupIndex].isOpen = !groupedMenus.value[groupIndex].isOpen
}

const updateWindowWidth = () => {
    windowWidth.value = window.innerWidth
}

watch(route, () => {
    if (windowWidth.value < 1024) {
        isSidebarOpen.value = false
    }
})

onMounted(() => {
    window.addEventListener('resize', updateWindowWidth)
    const handleEscape = (e: KeyboardEvent) => {
        if (e.key === 'Escape') {
            isUserMenuOpen.value = false
            if (windowWidth.value < 1024) {
                isSidebarOpen.value = false
            }
        }
    }
    window.addEventListener('keydown', handleEscape)

    onUnmounted(() => {
        window.removeEventListener('resize', updateWindowWidth)
        window.removeEventListener('keydown', handleEscape)
    })
})
</script>

<style scoped>
aside {
    overflow-x: hidden;
    white-space: nowrap;
}
</style>