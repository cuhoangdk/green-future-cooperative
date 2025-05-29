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
                <div
                    class="p-2 bg-white border-b border-gray-200 text-center text-xl font-bold text-gray-800 flex items-center justify-between">
                    <span v-show="!isCollapsed">Green Future</span>
                    <button @click="toggleCollapse" class="p-1 mx-auto hover:bg-gray-100 rounded">
                        <Expand class="w-5 h-5 text-gray-600" />
                    </button>
                </div>

                <!-- Navigation -->
                <nav class="mt-4 px-2">
                    <ul class="space-y-2">
                        <!-- All menu items -->
                        <li v-for="(menu, index) in filteredMenus" :key="`menu-${index}`">
                            <div class="relative">
                                <NuxtLink :to="menu.route" :class="[
                                    'flex items-center w-full py-3 rounded-lg transition-colors duration-200',
                                    'hover:bg-gray-100 hover:text-gray-900',
                                    isCollapsed ? 'px-2 justify-center' : 'px-4',
                                    { 'bg-gray-200 text-gray-900': isCurrentRoute(menu.route) },
                                    { 'text-gray-600': !isCurrentRoute(menu.route) }
                                ]" :title="isCollapsed ? menu.label : ''">
                                    <component :is="menu.icon" class="w-5 h-5" :class="isCollapsed ? '' : 'mr-3'" />
                                    <span v-show="!isCollapsed" class="flex-1 text-left">{{ menu.label }}</span>
                                    <ChevronDown v-if="menu.items && !isCollapsed" class="w-4 h-4 transition-transform"
                                        :class="{ 'rotate-180': menu.isOpen }" @click.prevent="toggleGroup(index)" />
                                </NuxtLink>
                            </div>

                            <!-- Submenu -->
                            <ul v-if="menu.items && menu.isOpen && !isCollapsed" class="ml-2 mt-1 space-y-1">
                                <li v-for="(item, itemIndex) in menu.items" :key="`item-${itemIndex}`">
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
                <header class="sticky top-0 z-100 bg-white border-b border-gray-200">
                    <div class="flex items-center justify-between px-4 py-1.5">
                        <div class="flex items-center">
                            <button @click="toggleSidebar"
                                class="rounded-lg lg:hidden btn btn-sm btn-square btn-ghost mr-2">
                                <MenuIcon class="w-6 h-6 text-gray-600" />
                            </button>
                            <h1 class="font-bold text-lg text-gray-800">
                                {{ pageTitle }}
                            </h1>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="dropdown dropdown-hover *:dropdown-bottom dropdown-end">
                                <div tabindex="0" role="button" class="btn btn-sm btn-square btn-ghost mr-2 relative">
                                    <Bell class="w-6 h-6 text-gray-600" />
                                    <div v-if="hasUnreadNotifications"
                                        class="absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full"></div>
                                </div>
                                <ul tabindex="0"
                                    class="dropdown-content flex flex-col menu bg-base-100 rounded-box z-1 w-55 lg:w-96 p-2 shadow-sm">
                                    <div class="flex justify-end">
                                        <div @click="useRouter().push('/admin/notifications'); closeDropdown()"
                                            class="btn btn-ghost">Xem tất cả</div>
                                    </div>
                                    <li v-for="notification in notifications" :key="notification.id">
                                        <div @click="markAsRead(notification.id); handleNotificationRedirect(notification.title); closeDropdown()"
                                            class="flex flex-col justify-start items-start gap-1">
                                            <div class="text-sm text-gray-500">{{
                                                formatDateTime(notification.created_at) }}</div>
                                            <p class="font-semibold">{{ notification.title }}</p>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <!-- <UiNotification /> -->

                            <!-- User Dropdown -->
                            <div class="dropdown dropdown-bottom dropdown-end">
                                <button tabindex="0">
                                    <div class="avatar">
                                        <div class="w-8 rounded-full">
                                            <img :src="avatar" @error="avatar = defaultAvatar" />
                                        </div>
                                    </div>
                                </button>
                                <ul tabindex="0"
                                    class="dropdown-content menu bg-base-100 rounded-box z-1 w-52 p-2 shadow-sm">
                                    <li>
                                        <NuxtLink to="/admin/profile" @click="closeDropdown">
                                            Tài khoản
                                        </NuxtLink>
                                    </li>
                                    <li>
                                        <button @click="handleLogout" class="w-full text-left">
                                            Đăng xuất
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Page Content -->
                <div class="mb-10">
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
    Tractor,
    Ruler,
    Layers,
    Ticket,
    Expand,
    Settings,
    Mail,
    Bell
} from 'lucide-vue-next'
import { useRuntimeConfig } from '#app'
import { useToast } from 'vue-toastification';
import type { Notification } from '~/types/notification';

const { logout, currentUser } = useUserAuth()
const { getNotifications, markAsRead, handleNotificationRedirect } = useNotifications()

const toast = useToast();

const isSidebarOpen = ref(false)
const isUserMenuOpen = ref(false)
const isCollapsed = ref(true)
const route = useRoute()
const defaultAvatar = useRuntimeConfig().public.placeholderImage
const backendUrl = useRuntimeConfig().public.backendUrl
const windowWidth = ref(window.innerWidth)
const avatar = computed(() => currentUser.value?.avatar_url ? `${backendUrl}${currentUser.value.avatar_url}` : defaultAvatar)
const isAdmin = computed(() => currentUser.value?.is_super_admin === true)


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

// Unified menu with the requested order
const menus = ref([
    { label: 'Trang chủ', route: '/admin', icon: Home },
    { label: 'Nông trại', route: '/admin/farms', icon: Tractor },
    {
        label: 'Sản phẩm',
        route: '/admin/products',
        icon: Sprout,
        isOpen: false,
        items: [
            { label: 'Loại sản phẩm', route: '/admin/product-categories', icon: Layers },
            { label: 'Đơn vị', route: '/admin/units', icon: Ruler },
        ]
    },
    { label: 'Đơn hàng', route: '/admin/orders', icon: Ticket },
    { label: 'Khách hàng', route: '/admin/customers', icon: User2Icon },
    { label: 'Thành viên', route: '/admin/users', icon: Users },
    {
        label: 'Bài viết',
        route: '/admin/posts',
        icon: Newspaper,
        isOpen: false,
        items: [
            { label: 'Loại bài viết', route: '/admin/post-categories', icon: Layers },
        ]
    },

    { label: 'Tin nhắn liên hệ', route: '/admin/contact-messages', icon: Mail },
    { label: 'Cài đặt', route: '/admin/setting', icon: Settings },
])

const filteredMenus = computed(() => {
    if (isAdmin.value) {
        return menus.value
    } else {
        return menus.value.filter(menu =>
            ['Nông trại', 'Sản phẩm', 'Đơn hàng'].includes(menu.label)
        )
    }
})

const pageTitle = computed(() => {
    return route.meta.title || 'Dashboard'
})

const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value
}

const toggleCollapse = () => {
    isCollapsed.value = !isCollapsed.value
}

const isCurrentRoute = (path: string) => {
    return route.path === path
}

const toggleGroup = (menuIndex: number) => {
    menus.value[menuIndex].isOpen = !menus.value[menuIndex].isOpen
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

// Đóng dropdown khi chọn một mục
const closeDropdown = () => {
    const dropdownButton = document.activeElement as HTMLElement
    if (dropdownButton) {
        dropdownButton.blur() // Xóa focus khỏi button để đóng dropdown
    }
}

const { data, status, error, refresh } = await getNotifications(1, 5)
const notifications = computed<Notification[]>(() =>
    Array.isArray(data.value?.data) ? data.value.data : data.value?.data ? [data.value.data] : []
)
const hasUnreadNotifications = computed(() => {
    return notifications.value.some(notification => !notification.is_read);
});
</script>

<style scoped>
aside {
    overflow-x: hidden;
    white-space: nowrap;
}
</style>