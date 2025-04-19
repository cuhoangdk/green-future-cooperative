<template>
  <div class="hidden md:block w-full overflow-x-auto">
    <table class="w-full">
      <thead>
        <tr class="bg-gray-100 text-gray-700">
          <th class="py-2 pl-2 w-[15%] text-left">Họ tên</th>
          <th class="py-2 w-[25%] text-left">Liên hệ</th>
          <th class="py-2 w-[15%] text-left">Trạng thái</th>
          <th class="py-2 w-[5%] text-left">Thao tác</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="user in users" :key="user.id" @click="$router.push(`users/${user.id}/edit`)"
          class="border-b border-gray-100 hover:bg-gray-200 cursor-pointer">
          <td class="py-1 pl-2">
            <div class="text-sm text-gray-500">{{ user.id }}</div>
            <div>{{ user.full_name }}</div>
          </td>
          <td class="py-1">
            <div class="flex flex-col">
              <div class="flex items-center gap-1">
                <MailIcon class="w-4 h-4 text-gray-500" />
                <span>{{ user.email }}</span>
              </div>
              <div class="flex items-center gap-1">
                <PhoneIcon class="w-4 h-4 text-gray-500" />
                <span>{{ user.phone_number }}</span>
              </div>
            </div>
          </td>
          <td class="py-1">
            <span v-if="!user.is_banned" class="badge badge-primary">
              Hoạt động
            </span>
            <span v-else class="badge badge-error">
              Đã cấm
            </span>
          </td>
          <td class="py-1">
            <div class="flex space-x-1 items-center">
              <UiBanButton :is-banned="user.is_banned" :on-click="() => onToggleStatus(user)" />
              <UiEditButton :to="`users/${user.id}/edit`" />
              <UiDeleteButton :on-click="() => onDelete(user.id)" />
            </div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup lang="ts">
import { Mail, Phone } from 'lucide-vue-next'
import type { User } from '~/types/user'

const MailIcon = Mail
const PhoneIcon = Phone

defineProps<{
  users: User[]
  onToggleStatus: (user: User) => void
  onDelete: (userId: number) => void
}>()
</script>