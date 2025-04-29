<template>
    <div class="min-h-screen items-center flex flex-col mt-16 p-2 lg:mt-0">
        <div class="w-full lg:w-8/12 bg-white border border-gray-200 rounded-2xl p-4 sm:p-5">
            <div class="relative border-gray-200 ">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="text-lg font-medium text-gray-800">Thông tin địa chỉ</h3>
                    <button type="button" class="btn btn-secondary" @click="$router.push('/account/add-address')">Thêm
                        địa chỉ</button>
                </div>
                <div v-if="status === 'pending'"
                    class="absolute inset-0 bg-gray-50 opacity-25 flex justify-center items-center z-10">
                    <span class="loading loading-spinner loading-lg"></span>
                </div>
                <div v-else v-for="address in addresses" :key="address.id">
                    <UiAddressCard :address="address" :addressDetailId="address.address.ward" :handleDelete="handleDelete" />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import type { CustomerAddress } from '~/types/customer';

const { getCustomerAddress } = useCustomerAddress();
const { deleteCustomerAddress } = useCustomerAddress();
const { $toast } = useNuxtApp();
const swal = useSwal()

const { data, status, refresh } = await getCustomerAddress();
const addresses = computed<CustomerAddress[]>(() => Array.isArray(data.value?.data) ? data.value.data : data.value ? [data.value.data] : []);

async function handleDelete(id: number) {
  const result = await swal.fire({
    title: 'Xác nhận xóa',
    text: 'Bạn có chắc muốn xóa địa chỉ này không?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Xóa',
    cancelButtonText: 'Hủy',
  })

  if (result.isConfirmed) {
    try {
      const { status } = await deleteCustomerAddress(id)
      if (status.value === 'error') throw new Error()
      refresh()
      $toast.success('Xóa địa chỉ thành công!`)')
    } catch (err) {
      $toast.error(`Xóa thất bại`)
    }
  }
}
</script>
