<template>
    <div>
        <a :href="`tel:${shippingFeeData?.value}`"
            class="btn bg-yellow-600 text-white w-full rounded-full hover:bg-yellow-700 flex gap-2 items-center justify-center">
            <Phone class="w-5 h-5" /> Liên hệ
        </a>
    </div>
</template>

<script setup lang="ts">
import { Phone } from 'lucide-vue-next'
import type { Parameter } from '~/types/parameter'

const { getPhoneContact } = useParameters()
const { data } = await getPhoneContact()

const shippingFeeData = computed<Parameter | null>(() => Array.isArray(data.value?.data) ? data.value.data[0] : data.value?.data || null)
</script>