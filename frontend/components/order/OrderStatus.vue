<template>
    <div>
        <div v-if="status === 'processing'" class="badge badge-lg badge-warning">
            Đang xử lý
        </div>
        <div v-else-if="status === 'cancelled'" class="badge badge-lg badge-error">
            Đã hủy
        </div>
        <div v-else-if="status === 'pending'" class="badge badge-lg badge-info">
            Chờ xác nhận
        </div>
        <div v-else-if="status === 'delivering'" class="badge badge-lg badge-primary">
            Đang giao
        </div>
        <div v-else-if="status === 'delivered'" class="badge badge-lg badge-success">
            Đã giao
        </div>
        <div v-else class="badge badge-lg badge-secondary">
            Không xác định
        </div>
    </div>
</template>

<script setup lang="ts">

defineProps<{
    status: 'processing' | 'cancelled' | 'pending' | 'delivering' | 'delivered'
}>()
</script>