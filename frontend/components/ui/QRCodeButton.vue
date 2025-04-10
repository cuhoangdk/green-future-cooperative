<template>
    <NuxtLink :to="to" class="btn btn-sm btn-square btn-neutral" title="QR Code">
        <QrCode class="w-4 h-4" />
    </NuxtLink>
</template>

<script setup lang="ts">
import { QrCode } from 'lucide-vue-next'

defineProps<{
    to: string
}>()
</script>



