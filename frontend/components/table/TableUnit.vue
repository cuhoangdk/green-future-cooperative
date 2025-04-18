<template>
    <div class="w-full overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-100 text-gray-700">
                    <th class="py-2 pl-2 w-[30%] text-left">Tên</th>
                    <th class="py-2 w-[30%] text-left">Mô tả</th>
                    <th class="py-2 w-[20%] text-left">Cho phép mua lẻ</th>
                    <th class="py-2 w-[20%] text-left">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="unit in units" :key="unit.id"
                    @click="$router.push(`units/${unit.id}`)"
                    class="border-b border-gray-100 hover:bg-gray-200 cursor-pointer">
                    <td class="py-1 pl-2">{{ unit.name }}</td>
                    <td class="py-1">{{ unit.description }}</td>
                    <td class="py-1">{{ unit.allow_decimal ? 'Có' : 'Không' }}</td>
                    <td class="py-1 pr-2">
                        <div class="flex space-x-1 items-center">
                            <UiEditButton :to="`units/${unit.id}`"/>
                            <UiDeleteButton :on-click="() => onDelete(unit.id)" />
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup lang="ts">
import type { Unit } from '~/types/product'

defineProps<{
    units: Unit[]
    onDelete: (id: number) => void
}>()
</script>