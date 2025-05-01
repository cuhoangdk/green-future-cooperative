<template>
    <div class="hidden md:block w-full overflow-x-auto">
        <table class="w-full">
            <thead >
                <tr class="bg-gray-100 text-gray-700">
                    <th class="py-2 pl-2 w-[20%] text-left">Thời gian</th>
                    <th class="py-2 w-[80%] text-left">Thông báo</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="notification in notifications" :key="notification.id" @click="read(notification.id)"
                    class="border-b border-gray-100 hover:bg-gray-200 cursor-pointer">
                    <td class="py-1 pl-2">
                        {{ formatDateTime(notification.created_at) }}
                    </td>
                    <td class="py-1">
                        {{ notification.title }}
                        <div v-if="notification.is_read == false" class="badge badge-info">Mới</div>
                    </td>
                    <td class="py-1">
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup lang="ts">
import { Mail, Phone } from 'lucide-vue-next'
import type { Notification } from '~/types/notification'

const MailIcon = Mail
const PhoneIcon = Phone

defineProps<{
    notifications: Notification[]
    read: (id: number) => void
}>()
</script>