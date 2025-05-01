<!-- components/customer/CustomerGrid.vue -->
<template>
    <div class="grid grid-cols-1 gap-4 p-3 md:hidden">
        <div v-for="notification in notifications" :key="notification.id" @click="read(notification.id)"
            class="card bg-base-100 border border-gray-400 hover:shadow-md transition-shadow cursor-pointer">
            <div class="card-body p-3">
                <div class="flex justify-between items-start">
                    <h3 class="card-title text-base">{{ formatDateTime(notification.created_at) }} <br/> {{ notification.title }}</h3>
                    <div v-if="notification.is_read == false" class="badge badge-info">Mới</div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import type { Notification } from '~/types/notification'

defineProps<{
    notifications: Notification[]
    read: (id: number) => void
}>()
</script>