<template>
    <div>
        <button v-if="action" :class="['btn', action.class]" :disabled="!action">
            {{ action.label }}
        </button>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps<{
    status: 'processing' | 'cancelled' | 'pending' | 'delivering' | 'delivered'
}>()

// Define state transitions
const actions = {
    pending: { label: 'Xác nhận xử lý', class: 'btn-primary' },
    processing: { label: 'Bắt đầu giao hàng', class: 'btn-secondary' },
    delivering: { label: 'Xác nhận giao hàng', class: 'btn-success' },
    delivered: null,
    cancelled: null,
}

// Get the action based on current status
const action = computed(() => actions[props.status])
</script>
