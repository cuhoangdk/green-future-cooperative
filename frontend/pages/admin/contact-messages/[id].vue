<template>
    <div class="p-4">
        <div v-if="status === 'pending'" class="flex justify-center items-center h-screen">
            <span class="loading loading-spinner loading-lg"></span>
        </div>
        <div v-else class="space-y-6">
            <div class="border-gray-200">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <!-- Name -->
                    <div>
                        <label class="text-gray-700 font-semibold block mb-1">Họ và tên</label>
                        <div class="input input-bordered input-primary w-full">{{ message?.name }}</div>
                    </div>
                    <!-- Email -->
                    <div>
                        <label class="text-gray-700 font-semibold block mb-1">Email</label>
                        <div class="input input-bordered input-primary w-full">{{ message?.email }}</div>
                    </div>

                    <!-- Phone Number -->
                    <div>
                        <label class="text-gray-700 font-semibold block mb-1">Số điện thoại</label>
                        <div class="input input-bordered input-primary w-full">{{ message?.phone }}</div>
                    </div>


                    <!-- Message Content -->
                    <div class="sm:col-span-3">
                        <label class="text-gray-700 font-semibold block mb-1">Nội dung</label>
                        <div class="textarea textarea-bordered textarea-primary w-full h-32">{{ message?.message }}</div>
                    </div>
                </div>
            </div>
            <UiButtonBack/>
        </div>
    </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'user', title: 'Liên hệ', });
import type { ContactMessage } from '~/types/contact-message'
import type { PaginationMeta, PaginationLinks } from '~/types/api'

const id = Number(useRoute().params.id)

const { getMessageById, deleteMessage } = useContactMessages()
const { data, status } = await getMessageById(id);
const message = computed<ContactMessage | null>(() => (data.value ? data.value.data : null))

</script>