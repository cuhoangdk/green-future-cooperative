<template>
    <div class="flex flex-col items-center mt-16 lg:mt-0 p-2 ">
        <div class="w-full lg:w-8/12 bg-white border border-gray-200 rounded-2xl p-4 my-4">
            <form @submit.prevent="handleSubmit" class="space-y-6">
                <div class="border-gray-200">
                    <h3 class="text-lg font-medium text-gray-800 mb-3">Thông tin liên hệ</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Email -->
                        <div class="sm:col-span-2">
                            <label class="text-gray-700 font-semibold block mb-1">Email</label>
                            <input v-model="form.email" type="email" class="input input-bordered input-primary w-full"
                                placeholder="Nhập email của bạn" required />
                        </div>

                        <!-- Phone Number -->
                        <div class="sm:col-span-2">
                            <label class="text-gray-700 font-semibold block mb-1">Số điện thoại</label>
                            <input v-model="form.phone_number" type="tel"
                                class="input input-bordered input-primary w-full" placeholder="Nhập số điện thoại"
                                required />
                        </div>

                        <!-- Gender -->
                        <div class="sm:col-span-2">
                            <label class="text-gray-700 font-semibold block mb-1">Giới tính</label>
                            <div class="flex space-x-4">
                                <label class="flex items-center space-x-2">
                                    <input v-model="form.gender" type="radio" value="male" class="radio radio-primary"
                                        required />
                                    <span>Nam</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input v-model="form.gender" type="radio" value="female" class="radio radio-primary"
                                        required />
                                    <span>Nữ</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input v-model="form.gender" type="radio" value="other" class="radio radio-primary"
                                        required />
                                    <span>Khác</span>
                                </label>
                            </div>
                        </div>

                        <!-- Message Content -->
                        <div class="sm:col-span-2">
                            <label class="text-gray-700 font-semibold block mb-1">Nội dung</label>
                            <textarea v-model="form.message"
                                class="textarea textarea-bordered textarea-primary w-full h-32"
                                placeholder="Nhập nội dung liên hệ..." required></textarea>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="border-t border-gray-200 pt-5 flex justify-end">
                    <button type="submit" class="btn btn-primary px-6" :disabled="status === 'pending'">
                        <span v-if="status === 'pending'" class="loading loading-spinner loading-md"></span>
                        Gửi liên hệ
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';

// Form state
const form = ref({
    email: '',
    phone_number: '',
    gender: '',
    message: ''
});

// Form status
const status = ref<'idle' | 'pending' | 'success' | 'error'>('idle');

// Handlers
const handleSubmit = async () => {
    // Implement submission logic here
};

const cancelForm = () => {
    // Implement cancel logic here
};
</script>