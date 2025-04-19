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
        <tr v-for="customer in customers" :key="customer.id" @click="$router.push(`customers/${customer.id}/edit`)"
          class="border-b border-gray-100 hover:bg-gray-200 cursor-pointer">
          <td class="py-1 pl-2">
            <div class="text-sm text-gray-500">{{ customer.id }}</div>
            <div>{{ customer.full_name }}</div>
          </td>
          <td class="py-1">
            <div class="flex flex-col">
              <div class="flex items-center gap-1">
                <MailIcon class="w-4 h-4 text-gray-500" />
                <span>{{ customer.email }}</span>
              </div>
              <div class="flex items-center gap-1">
                <PhoneIcon class="w-4 h-4 text-gray-500" />
                <span>{{ customer.phone_number }}</span>
              </div>
            </div>
          </td>
          <td class="py-1">
            <span v-if="!customer.is_banned" class="badge badge-primary">
              Hoạt động
            </span>
            <span v-else class="badge badge-error">
              Đã bị cấm
            </span>
            <span v-if="!customer.verified_at" class="badge badge-warning">
              Chưa xác thực
            </span>
          </td>
          <td class="py-1">
            <div class="flex space-x-1 items-center">
              <UiBanButton :is-banned="customer.is_banned" :on-click="() => onToggleStatus(customer)" />
              <UiEditButton :to="`customers/${customer.id}/edit`" />
              <UiDeleteButton :on-click="() => onDelete(customer.id)" />
            </div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup lang="ts">
import { Mail, Phone } from 'lucide-vue-next'
import type { Customer } from '~/types/customer'

const MailIcon = Mail
const PhoneIcon = Phone

defineProps<{
  customers: Customer[]
  onToggleStatus: (customer: Customer) => void
  onDelete: (customerId: number) => void
}>()
</script>