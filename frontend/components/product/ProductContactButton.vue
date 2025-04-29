<template>
    <div>
        <a :href="`tel:${parameters.phone_contact}`"
            class="btn bg-yellow-600 text-white w-full rounded-full hover:bg-yellow-700 flex gap-2 items-center justify-center">
            <Phone class="w-5 h-5" /> Liên hệ
        </a>
    </div>
</template>

<script setup lang="ts">
import { Phone } from 'lucide-vue-next'
const { parameters } = useParametersStore()
</script>