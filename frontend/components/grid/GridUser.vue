<!-- components/customer/CustomerGrid.vue -->
<template>
  <div class="grid grid-cols-1 gap-4 p-3 md:hidden">
    <div v-for="user in users" :key="user.id"
      @click="$router.push(`users/${user.id}/edit`)"
      class="card bg-base-100 border border-gray-400 hover:shadow-md transition-shadow cursor-pointer">
      <div class="card-body p-3">
        <div class="flex justify-between items-start">
          <h3 class="card-title text-base">{{ user.full_name }}</h3>
          <span v-if="!user.is_banned" class="badge badge-primary">
            Hoạt động
          </span>
          <span v-else class="badge badge-error">
            Bị cấm
          </span>
        </div>

        <div class="space-y-1 text-sm">
          <div class="flex gap-2 items-center">
            <MailIcon class="w-4 h-4 text-gray-500" />
            <span>{{ user.email }}</span>
          </div>
          <div class="flex gap-2 items-center">
            <PhoneIcon class="w-4 h-4 text-gray-500" />
            <span>{{ user.phone_number }}</span>
          </div>
        </div>

        <div class="card-actions justify-end border-t pt-2 border-gray-100">
          <UiBanButton :is-banned="user.is_banned" :on-click="() => onToggleStatus(user)" />
          <UiEditButton :to="`users/${user.id}/edit`" />
          <UiDeleteButton :on-click="() => onDelete(user.id)" />
        </div>
      </div>
    </div>
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