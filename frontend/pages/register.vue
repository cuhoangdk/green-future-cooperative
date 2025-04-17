<template>
    <div class="w-full max-w-xl z-10">
        <!-- Form đăng ký -->
        <div v-if="status !== 'success'"
            class="bg-white rounded-3xl shadow-2xl p-8 space-y-6 border border-green-100 relative transform transition-all duration-300 hover:shadow-3xl hover:-translate-y-[1px]">
            <!-- Gradient Border Effect -->
            <div class="absolute inset-0.5 bg-gradient-to-br from-transparent to-green-50 rounded-3xl -z-10 opacity-50">
            </div>

            <!-- Logo Section -->
            <div class="text-center space-y-4 mb-6">
                <h1 class="text-3xl font-bold text-gray-800 tracking-tight">Đăng ký tài khoản</h1>
            </div>

            <form @submit.prevent="handleSubmit" class="space-y-2">
                <!-- Email Input -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center z-10">
                            <Mail class="h-5 w-5 text-gray-400" />
                        </span>
                        <input v-model="form.email" type="email" class="input input-primary w-full pl-10 rounded-full"
                            :class="{ 'input-error': errors.email }" placeholder="greenfuture@gmail.com" required />
                    </div>
                    <div v-if="errors.email" class="text-red-600 text-sm mt-1 animate-fade-in">
                        {{ errors.email[0] }}
                    </div>
                </div>

                <!-- Full Name Input -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Họ và tên</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center z-10">
                            <User2 class="h-5 w-5 text-gray-400" />
                        </span>
                        <input v-model="form.full_name" class="input input-primary w-full pl-10 rounded-full"
                            :class="{ 'input-error': errors.full_name }" placeholder="Nguyễn Văn A" required />
                    </div>
                    <div v-if="errors.full_name" class="text-red-600 text-sm mt-1 animate-fade-in">
                        {{ errors.full_name[0] }}
                    </div>
                </div>

                <!-- Phone Number Input -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Số điện thoại</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center z-10">
                            <Phone class="h-5 w-5 text-gray-400" />
                        </span>
                        <input v-model="form.phone_number" class="input input-primary w-full pl-10 rounded-full"
                            :class="{ 'input-error': errors.phone_number }" placeholder="0989898989" required />
                    </div>
                    <div v-if="errors.phone_number" class="text-red-600 text-sm mt-1 animate-fade-in">
                        {{ errors.phone_number[0] }}
                    </div>
                </div>

                <!-- Password Input -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mật khẩu</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center z-10">
                            <Lock class="h-5 w-5 text-gray-400" />
                        </span>
                        <input v-model="form.password" :type="showPassword ? 'text' : 'password'"
                            class="input input-primary w-full pl-10 pr-10 rounded-full"
                            :class="{ 'input-error': errors.password }" placeholder="••••••••" required />
                        <button type="button" @click="showPassword = !showPassword"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-green-600">
                            <EyeClosed v-if="!showPassword" class="h-5 w-5" />
                            <Eye v-else class="h-5 w-5" />
                        </button>
                    </div>
                    <div v-if="errors.password" class="text-red-600 text-sm mt-1 animate-fade-in">
                        {{ errors.password[0] }}
                    </div>
                </div>

                <!-- Password Confirmation Input -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Xác nhận mật khẩu</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center z-10">
                            <Lock class="h-5 w-5 text-gray-400" />
                        </span>
                        <input v-model="form.password_confirmation" :type="showPasswordConfirmed ? 'text' : 'password'"
                            class="input input-primary w-full pl-10 pr-10 rounded-full"
                            :class="{ 'input-error': errors.password_confirmation }" placeholder="••••••••" required />
                        <button type="button" @click="showPasswordConfirmed = !showPasswordConfirmed"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-green-600">
                            <EyeClosed v-if="!showPasswordConfirmed" class="h-5 w-5" />
                            <Eye v-else class="h-5 w-5" />
                        </button>
                    </div>
                    <div v-if="errors.password_confirmation" class="text-red-600 text-sm mt-1 animate-fade-in">
                        {{ errors.password_confirmation[0] }}
                    </div>
                </div>

                <!-- General Error Message -->
                <div v-if="errorMessage" class="text-red-600 text-sm text-center animate-fade-in">
                    {{ errorMessage }}
                </div>

                <!-- Submit Button -->
                <div class="flex flex-col space-y-4 justify-center items-center mt-6">
                    <button type="submit"
                        class="btn btn-primary w-full bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 transition-all duration-300 transform hover:scale-[1.02] active:scale-[0.98] rounded-full"
                        :disabled="status === 'pending'">
                        <span v-if="status === 'pending'" class="loading loading-spinner loading-md mr-2"></span>
                        Đăng kí
                    </button>
                    <NuxtLink href="/login" class="text-sm text-green-600 hover:text-green-800 transition-colors">
                        Đăng nhập
                    </NuxtLink>
                </div>
            </form>
        </div>

        <!-- Thông báo đăng ký thành công -->
        <div v-else
            class="bg-white rounded-3xl shadow-2xl p-8 space-y-6 border border-green-100 relative transform transition-all duration-300 animate-fade-in">
            <div class="absolute inset-0.5 bg-gradient-to-br from-transparent to-green-50 rounded-3xl -z-10 opacity-50"></div>
            <div class="text-center space-y-4">
                <h1 class="text-3xl font-bold text-green-600 tracking-tight">Đăng ký thành công!</h1>
                <p class="text-gray-600">
                    Một email xác thực đã được gửi đến <span class="font-semibold">{{ form.email }}</span>. 
                    Vui lòng kiểm tra hộp thư (hoặc thư rác) và làm theo hướng dẫn để kích hoạt tài khoản của bạn.
                </p>
                <NuxtLink to="/login"
                    class="inline-block mt-4 px-6 py-2 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-full hover:from-green-600 hover:to-green-700 transition-all duration-300 transform hover:scale-[1.02]">
                    Quay lại đăng nhập
                </NuxtLink>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'empty' })
import { User2, Lock, Eye, EyeClosed, Phone, Mail } from 'lucide-vue-next'

const form = reactive({
    email: '',
    full_name: '',
    phone_number: '',
    password: '',
    password_confirmation: '',
})

const status = ref<'idle' | 'pending' | 'success' | 'error'>('idle')
const errorMessage = ref<string | null>(null)
const errors = reactive<Record<string, string[]>>({
    email: [],
    full_name: [],
    phone_number: [],
    password: [],
    password_confirmation: [],
})
const { register } = useCustomerAuth()
const { $toast } = useNuxtApp()
const showPassword = ref(false)
const showPasswordConfirmed = ref(false)

const handleSubmit = async () => {
    try {
        status.value = 'pending'
        errorMessage.value = null
        Object.keys(errors).forEach((key) => {
            errors[key] = []
        })

        await register(
            form.email,
            form.full_name,
            form.phone_number,
            form.password,
            form.password_confirmation
        )

        status.value = 'success'
        $toast.success('Đăng ký thành công!')
    } catch (error: any) {
        // Gán thông báo lỗi chung
        status.value = 'error'
        errorMessage.value = "Đăng ký thất bại! Vui lòng kiểm tra lại thông tin.";
        $toast.error(errorMessage.value);
    }
}
</script>

<style scoped>
.animate-fade-in {
    animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>