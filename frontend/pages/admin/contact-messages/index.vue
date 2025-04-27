<template>
    <div class="relative">
        <div v-if="status === 'pending'"
            class="absolute inset-0 bg-gray-50 opacity-25 flex justify-center items-center z-10">
            <span class="loading loading-spinner loading-lg"></span>
        </div>
        <TableContactMessage :messages="messages.messages" v-on:delete="handleDelete" />
        <GridContactMessage :messages="messages.messages" v-on:delete="handleDelete" />
        <div class="flex flex-col sm:flex-row justify-between items-center m-4 gap-2">
            <div class="flex items-center space-x-2">
                <p class="text-sm text-gray-600">{{ messages.messages.length }} / {{ messages.meta?.total }}</p>
                <select v-model="perPage" class="select select-sm select-primary w-18" @change="getMessages(currentPage, perPage)">
                    <option v-for="n in [10, 25, 50, 100]" :value="n" :key="n">{{ n }}</option>
                </select>
            </div>
            <UiPagination :links="messages.links" :meta="messages.meta" :show-first-last="true" :show-numbers="true"
                @page-change="handlePageChange" />
        </div>
    </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'user', title: 'Liên hệ', });
import type { ContactMessage } from '~/types/contact-message'
import type { PaginationMeta, PaginationLinks } from '~/types/api'

const { getMessages, deleteMessage } = useContactMessages()
const perPage = ref(10)
const currentPage = ref(1)


const { data, status, refresh } = await getMessages(currentPage.value, perPage.value);
const messages = computed<{ messages: ContactMessage[], meta: PaginationMeta | null, links: PaginationLinks | null }>(() => ({
    messages: Array.isArray(data.value?.data) ? data.value.data : data.value?.data ? [data.value.data] : [],
    meta: data.value?.meta ?? null,
    links: data.value?.links ?? null,
}))

const handleDelete = async (id: number) => {
    await deleteMessage(id)
    refresh()
}

const handlePageChange = (page: number) => {
    currentPage.value = page
    getMessages(currentPage.value, perPage.value)
}


</script>