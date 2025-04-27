<template>
  <div class="hidden md:block w-full overflow-x-auto">
    <table class="w-full">
      <thead>
        <tr class="bg-gray-100 text-gray-700">
          <th class="py-2 pl-2 w-[15%] text-left">ID</th>
          <th class="py-2 pl-2 w-[15%] text-left">Họ tên</th>
          <th class="py-2 w-[25%] text-left">Email</th>
          <th class="py-2 w-[15%] text-left">Điện thoại</th>
          <th class="py-2 w-[5%] text-left">Thao tác</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="message in messages" :key="message.id" @click="$router.push(`contact-messages/${message.id}`)"
          class="border-b border-gray-100 hover:bg-gray-200 cursor-pointer">
          <td class="py-1 pl-2">
            {{ message.id }}
          </td>
          <td class="py-1 pl-2">
            {{ message.name }}
          </td>
          <td class="py-1">
            <div class="flex flex-col">
              <div class="flex items-center gap-1">
                <MailIcon class="w-4 h-4 text-gray-500" />
                <span>{{ message.email }}</span>
              </div>
            </div>
          </td>
          <td>
            <div class="flex items-center gap-1">
              <PhoneIcon class="w-4 h-4 text-gray-500" />
              <span>{{ message.phone }}</span>
            </div>
          </td>
          <td class="py-1">
            <div class="flex space-x-1 items-center">
              <UiViewButton :to="`customers/${message.id}/edit`" />
              <UiDeleteButton :on-click="() => onDelete(message.id)" />
            </div>
          </td>
        </tr>
      </tbody>
    </table>
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