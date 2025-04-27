<!-- components/customer/CustomerGrid.vue -->
<template>
    <div class="grid grid-cols-1 gap-4 p-3 md:hidden">
        <div v-for="message in messages" :key="message.id" @click="$router.push(`customers/${message.id}/edit`)"
            class="card bg-base-100 border border-gray-400 hover:shadow-md transition-shadow cursor-pointer">
            <div class="card-body p-3">
                <div class="flex justify-between items-start">
                    <h3 class="card-title text-base">{{ message.name }}</h3>
                </div>

                <div class="space-y-1 text-sm">
                    <div class="flex gap-2 items-center">
                        <MailIcon class="w-4 h-4 text-gray-500" />
                        <span>{{ message.email }}</span>
                    </div>
                    <div class="flex gap-2 items-center">
                        <PhoneIcon class="w-4 h-4 text-gray-500" />
                        <span>{{ message.phone }}</span>
                    </div>
                </div>

                <div class="card-actions justify-end border-t pt-2 border-gray-100">
                    <UiViewButton :to="`customers/${message.id}/edit`" />
                    <UiDeleteButton :on-click="() => onDelete(message.id)" />
                </div>
            </div>
        </div>
    </div>
</template>


<script setup lang="ts">
import { Mail, Phone } from 'lucide-vue-next'
import type { ContactMessage } from '~/types/contact-message'

const MailIcon = Mail
const PhoneIcon = Phone

defineProps<{
    messages: ContactMessage[]
    onDelete: (id: number) => void
}>()
</script>