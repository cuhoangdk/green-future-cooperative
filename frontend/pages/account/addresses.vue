<template>
    <div class="min-h-screen items-center flex flex-col mt-16 p-2 lg:mt-0">
        <div class="w-full lg:w-8/12 bg-white border border-gray-200 rounded-2xl p-4 sm:p-5">
            <div class=" border-gray-200 ">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="text-lg font-medium text-gray-800">Thông tin địa chỉ</h3>
                    <button type="button" class="btn btn-secondary"
                        @click="$router.push('/account/add-address')">Thêm địa chỉ</button>
                </div>
                <div v-for="address in addresses" :key="address.id">
                    <UiAddressCard :address="address" :addressDetailId="address.address.ward" />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import type { CustomerAddress } from '~/types/customer';

const { getCustomerAddress } = useCustomerAddress();
const { $toast } = useNuxtApp()

const { data: dataAddresses } = await getCustomerAddress();
const addresses = computed<CustomerAddress[]>(() => 
    Array.isArray(dataAddresses.value?.data) 
        ? dataAddresses.value.data 
        : dataAddresses.value 
            ? [dataAddresses.value.data] 
            : []
);

$toast.error('Không thể tải danh sách địa chỉ!')
</script>
