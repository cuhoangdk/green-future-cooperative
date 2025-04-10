<template>
    <div class="w-full overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-100 text-gray-700">
                    <th class="py-2 pl-2 w-[40%] text-left">Tên</th>
                    <th class="py-2 w-[40%] text-left">Mô tả</th>
                    <!-- <th class="py-2 w-[30%] text-left">Trạng thái</th> -->
                    <th class="py-2 w-[20%] text-left">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="productCategory in productCategories" :key="productCategory.id"
                    class="border-b border-gray-100 hover:bg-gray-200 cursor-pointer">
                    <td class="py-1 pl-2">{{ productCategory.name }}</td>
                    <td class="py-1">{{ productCategory.description }}</td>
                    <!-- <td class="py-2">
                        <span v-if="postCategory.is_active"
                            class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-800">
                            Hoạt động
                        </span>
                        <span v-else class="px-2 py-1 rounded-full text-xs bg-red-100 text-red-800">
                            Không hoạt động
                        </span>
                    </td> -->
                    <td class="py-1 pr-2">
                        <div class="flex space-x-1 items-center">
                            <UiEditButton :to="`/admin/product-categories/${productCategory.id}`" />
                            <UiDeleteButton :on-click="() => onDelete(productCategory.id)" />
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup lang="ts">
import type { ProductCategory } from '~/types/product'

defineProps<{
    productCategories: ProductCategory[]
    onDelete: (id: number) => void
}>()
</script>