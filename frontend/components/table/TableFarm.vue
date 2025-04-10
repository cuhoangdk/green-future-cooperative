<template>
    <div class="hidden md:block w-full overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-100 text-gray-700">
                    <th class="py-2 pl-2 w-[15%] text-left">Tên</th>
                    <th class="py-2 w-[15%] text-left">Chủ sở hữu</th>
                    <th class="py-2 w-[60%] text-left">Địa chỉ</th>
                    <th class="py-2 w-[10%] text-left">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="farm in farms" :key="farm.id"
                    class="border-b border-gray-100 hover:bg-gray-200 cursor-pointer">
                    <td class="py-1 pl-2">{{ farm.name }}</td>
                    <td class="py-1">{{ farm.user.full_name }}</td>
                    <td class="py-1">{{ addressData[farm.id] }}</td>
                    <td class="py-1">
                        <div class="flex space-x-1 items-center">
                            <UiEditButton :to="`farms/${farm.id}`" />
                            <UiDeleteButton :on-click="() => onDelete(farm.id)" />
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup lang="ts">
import type { Farm } from '~/types/farm'

defineProps<{
    farms: Farm[]
    addressData: { [key: number]: string }
    onDelete: (customerId: number) => void
}>()
</script>