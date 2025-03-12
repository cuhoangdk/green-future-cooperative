<template>
    <div class="border border-gray-200 rounded-lg">
        <!-- Header -->
        <div class="flex justify-between items-center border-gray-200 px-3 pt-2">
            <h1 class="text-xl font-bold text-gray-800">Danh sách đơn vị tính</h1>
            <button @click="$router.push('/admin/units/create')" class="btn btn-sm btn-secondary">
                <Plus class="w-3 h-3" /> Thêm đơn vị
            </button>
        </div>
        <!-- Table -->
        <div class="w-full overflow-x-auto mt-3">
            <table class="table w-full border-collapse bg-white border border-gray-200">
                <thead>
                    <tr class="bg-gray-100 text-gray-700">
                        <th class="py-2 w-[10%]">ID</th>
                        <th class="py-2 w-[30%] text-left">Tên</th>
                        <th class="py-2 w-[30%] text-left">Số lẻ</th>
                        <th class="py-2 w-[20%] text-left">Mô tả</th>
                        <th class="py-2 w-[10%]">Tao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <template v-for="unit in units" :key="unit.id">
                        <tr class="border-b border-gray-100 hover:bg-gray-200 cursor-pointer">
                            <td class="py-2">{{ unit.id }}</td>
                            <td class="py-2">{{ unit.name }}</td>
                            <td class="py-2">{{ unit.allow_decimal ? 'Có' : 'Không' }}</td>
                            <td class="py-2">{{ unit.description }}</td>
                            <td class="py-2">
                                <div class="dropdown dropdown-left dropdown-center">
                                    <div tabindex="0" role="button" class="btn btn-xs"><MoreHorizontal /></div>
                                    <ul tabindex="0"
                                        class="dropdown-content menu bg-base-100 rounded-box z-1 m-1 p-1 px-3 shadow-sm">
                                        <div class="flex gap-1">
                                        <li><button @click="$router.push(`/admin/units/${unit.id}`)"
                                            class="btn btn-sm btn-primary">Sửa</button></li>
                                        <li><button @click.stop="handleDeleteUnit(unit.id)"
                                            class="btn btn-sm btn-error">Xóa</button></li>
                                        </div>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup lang="ts">
definePageMeta({ title: 'Quản lý đơn vị', layout: 'user', })

import { Plus, MoreHorizontal } from 'lucide-vue-next'
import type { Unit } from '~/types/product'
import { useSwal } from '~/composables/useSwal'
import { useToast } from 'vue-toastification'

const { getUnits, deleteUnit } = useUnits()
const swal = useSwal()
const toast = useToast()
const { data, refresh } = await getUnits()
const units = computed<Unit[]>(() => Array.isArray(data.value?.data) ? data.value.data : data.value ? [data.value.data] : [])

async function handleDeleteUnit(unitId: number) {
    const result = await swal.fire({
        title: 'Xác nhận xóa',
        text: 'Bạn có chắc muốn xóa bài viết này không?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Xóa',
        cancelButtonText: 'Hủy',
    })

    if (result.isConfirmed) {
        try {
            const { error } = await deleteUnit(unitId)
            if (error.value) throw new Error(error.value.message)
            refresh()
            toast.success('Bài viết đã được xóa!')
        } catch (err) {
            toast.error(`Xóa thất bại: ${(err as Error).message || 'Unknown error'}`)
        }
    }
}
</script>