<template>
    <div class="p-3 border border-gray-200 rounded-lg bg-gray-50 mb-2">
        <p class="font-medium text-gray-800">
            {{ address.full_name }} - {{ address.phone_number }}
            <span class="text-sm text-gray-500">({{ address.address_type }})</span>
        </p>
        <p class="text-gray-600 text-sm">
            {{ address.address.street_address ? address.address.street_address + ', ' : '' }}
            {{ addressDetail }}
        </p>
    </div>
</template>

<script setup lang="ts">
import type { CustomerAddress } from '~/types/customer';
const { getFullAddressName } = useVietnamAddress();

const props = defineProps({
    address: {
        type: Object as () => CustomerAddress,
        required: true,
    },
    addressDetailId: {
        type: String,
        required: true,
    },
});

const addressDetail = await getFullAddressName(props.addressDetailId);
</script>
