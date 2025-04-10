<template>
    <div class="fixed inset-0 z-50 flex items-center justify-center glass">
        <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-800">Quên mật khẩu</h2>
                <button 
                    @click="$emit('close')" 
                    class="text-gray-500 hover:text-gray-700"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <p class="text-sm text-gray-600 mb-4">Nhập địa chỉ email để khôi phục mật khẩu</p>
            <input 
                :value="email" 
                @input="$emit('update:email', $event.target)" 
                type="email" 
                class="input input-primary w-full mb-4" 
                placeholder="Nhập email của bạn" 
            />
            <div class="flex justify-end space-x-2">
                <button 
                    @click="$emit('close')" 
                    class="btn btn-ghost"
                >
                    Hủy
                </button>
                <button 
                    @click="$emit('submit')" 
                    class="btn btn-primary"
                >
                    Gửi
                </button>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
const props = defineProps({
    email: {
        type: String,
        required: true
    }
})

const emits = defineEmits(['close', 'submit', 'update:email'])
</script>
