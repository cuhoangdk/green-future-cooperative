<template>
    <div>
        <div class="flex justify-end items-center px-3 py-4 border-b border-gray-200">
            <button @click="handleMarkAllAsRead" class="btn btn-sm btn-primary">Đánh dấu tất cả là đã đọc</button>
        </div>
        <div class="relative">
            <div v-if="status === 'pending'"
                class="absolute inset-0 bg-gray-50 opacity-25 flex justify-center items-center z-10">
                <span class="loading loading-spinner loading-lg"></span>
            </div>
            <TableNotification :notifications="notifications.notifications" :read="handleMarkAsRead" />
            <GridNotification :notifications="notifications.notifications" :read="handleMarkAsRead" />
        </div>
        <div class="flex justify-center mt-4">
            <UiPagination :links="notifications.links" :meta="notifications.meta" :show-numbers="true" @page-change="handlePageChange" />
        </div>
    </div>
</template>

<script setup lang="ts">
definePageMeta({ title: 'Thông báo', layout: 'user' })
import { useSwal } from '~/composables/useSwal'
import type { Notification } from '~/types/notification';
import type { PaginationMeta, PaginationLinks } from '~/types/api'

const { getNotifications, markAllAsRead, markAsRead, deleteNotification } = useNotifications()
const swal = useSwal()

const currentPage = ref(1)
const perPage = ref(20)

const { data, status, error, refresh } = await getNotifications(currentPage.value, perPage.value)

const notifications = computed<{ notifications: Notification[], meta: PaginationMeta | null, links: PaginationLinks | null }>(() => ({
    notifications: Array.isArray(data.value?.data) ? data.value.data : data.value?.data ? [data.value.data] : [],
    meta: data.value?.meta ?? null,
    links: data.value?.links ?? null,
}))

const handleMarkAllAsRead = async () => {
    const { error } = await markAllAsRead()
    if (error.value) {
        swal.fire('Lỗi', 'Không thể đánh dấu tất cả là đã đọc!', 'error')
    } else {
        refresh()
    }
}

const handleMarkAsRead = async (id: number) => {
    const { error } = await markAsRead(id)
    if (error.value) {
        swal.fire('Lỗi', 'Không thể đánh dấu thông báo là đã đọc!', 'error')
    } else {
        refresh()
    }
}

const handleDelete = async (id: number) => {
    const result = await swal.fire({
        title: 'Xác nhận xóa',
        text: 'Bạn có chắc muốn xóa thông báo này không?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Xóa',
        cancelButtonText: 'Hủy',
    })

    if (result.isConfirmed) {
        const { error } = await deleteNotification(id)
        if (error.value) {
            swal.fire('Lỗi', 'Không thể xóa thông báo!', 'error')
        } else {
            refresh()
        }
    }
}

const handlePageChange = (page: number) => {
    currentPage.value = page
    getNotifications(currentPage.value, perPage.value)
}
</script>
