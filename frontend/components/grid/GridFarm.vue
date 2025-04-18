<template>
    <div class="grid grid-cols-1 gap-4 p-3 md:hidden">
        <div v-for="farm in farms" :key="farm.id"
            @click="$router.push(`farms/${farm.id}`)"
            class="card bg-base-100 border border-gray-400 hover:shadow-md transition-shadow cursor-pointer">
            <div class="card-body p-3">
                <div class="flex justify-between items-start">
                    <h3 class="card-title text-base">{{ farm.name }}</h3>
                </div>

                <div class="space-y-1 text-sm">
                    <div class="flex gap-2 items-center">
                        <User2Icon class="w-4 h-4 text-gray-500" />
                        <span>{{ farm.user.full_name }}</span>
                    </div>
                    <div class="flex gap-2 items-center">
                        <MapPinIcon class="w-4 h-4 text-gray-500" />
                        <span>{{ addressData[farm.id] }}</span>
                    </div>
                </div>

                <div class="card-actions justify-end border-t pt-2 border-gray-100">
                    <UiEditButton :to="`farms/${farm.id}`" />
                    <UiDeleteButton :on-click="() => onDelete(farm.id)" />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { User2Icon, MapPinIcon } from 'lucide-vue-next'
import type { Farm } from '~/types/farm'

defineProps<{
    farms: Farm[]
    addressData: { [key: number]: string }
    onDelete: (customerId: number) => void
}>()
</script>