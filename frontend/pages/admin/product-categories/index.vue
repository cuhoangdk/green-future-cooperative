<template>
    <div>
        <div class="flex flex-col md:flex-row justify-between items-start gap-3 border-gray-200 px-3 py-4">
            <div class="flex flex-col w-full md:flex-row gap-2">
                <div class="relative w-full md:w-auto">
                    <Search class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 z-10 w-5 h-5" />
                    <input type="search" placeholder="Tìm kiếm ..." class="input input-sm input-primary w-full pl-10" />
                </div>
            </div>
            <button @click="$router.push('/admin/product-categories/create')"
                class="btn btn-sm btn-primary w-full md:w-auto">
                <Plus class="w-5 h-5" /> Thêm
            </button>
        </div>
        <div class="relative">
            <!-- Loading Overlay specific to table/grid -->
            <div v-if="status === 'pending'"
                class="absolute inset-0 bg-gray-50 opacity-25 flex justify-center items-center z-10">
                <span class="loading loading-spinner loading-lg"></span>
            </div>
        <TableProductCategory :productCategories="categories" :onDelete="handleDeleteUnit" />
        </div>
    </div>
</template>

<script setup lang="ts">
definePageMeta({ title: 'Quản lý loại sản phẩm', layout: 'user', })

import { Plus, Search } from 'lucide-vue-next'
import type { ProductCategory } from '~/types/product'
import { useSwal } from '~/composables/useSwal'

const { getProductCategories, deleteProductCategory } = useProductCategories()
const swal = useSwal()
const { $toast } = useNuxtApp()
const { data, status, refresh } = await getProductCategories()
const categories = computed<ProductCategory[]>(() => Array.isArray(data.value?.data) ? data.value.data : data.value ? [data.value.data] : [])

async function handleDeleteUnit(categoryId: number) {
    const result = await swal.fire({
        title: 'Xác nhận xóa',
        text: 'Bạn có chắc muốn xóa loại sản phẩm này không?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Xóa',
        cancelButtonText: 'Hủy',
    })

    if (result.isConfirmed) {
        try {
            const { error } = await deleteProductCategory(categoryId)
            if (error.value) throw new Error(error.value.message)
            refresh()
            $toast.success('Loại sản phẩm đã được xóa!')
        } catch (err) {
            $toast.error(`Xóa thất bại: ${(err as Error).message || 'Unknown error'}`)
        }
    }
}
</script>