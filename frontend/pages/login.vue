<template>
    <div class="min-h-screen flex items-center justify-center p-4 bg-gradient-to-br from-green-50 to-green-100 relative overflow-hidden">
        <!-- Animated Background Elements -->
        <div class="absolute top-0 left-0 w-full h-full pointer-events-none">
            <div class="absolute top-10 left-10 w-32 h-32 bg-green-300 rounded-full opacity-30 animate-blob"></div>
            <div class="absolute bottom-20 right-20 w-48 h-48 bg-green-400 rounded-full opacity-20 animate-blob animation-delay-2000"></div>
        </div>

        <div class="w-full max-w-md z-10">
            <div 
                class="bg-white rounded-3xl shadow-2xl p-8 space-y-6 border border-green-100 relative 
                       transform transition-all duration-300 hover:shadow-3xl hover:-translate-y-1">
                <!-- Gradient Border Effect -->
                <div class="absolute inset-0.5 bg-gradient-to-br from-transparent to-green-50 rounded-3xl -z-10 opacity-50"></div>

                <!-- Logo Section -->
                <div class="text-center space-y-4 mb-6">
                    <div class="flex justify-center mb-4">
                        <img 
                            src="/images/logo.jpg" 
                            alt="Logo" 
                            class="h-24 w-24 rounded-full object-cover shadow-md transform transition-transform hover:scale-105"
                        />
                    </div>
                    <h1 class="text-3xl font-bold text-gray-800 tracking-tight">Green Future</h1>
                </div>

                <form @submit.prevent="handleSubmit" class="space-y-6">
                    <!-- Email Input -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center z-10">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </span>
                            <input 
                                v-model="form.email" 
                                type="email" 
                                class="input input-primary w-full pl-10 border-gray-300 focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50" 
                                placeholder="greenfuture@gmail.com" 
                                required 
                            />
                        </div>
                    </div>

                    <!-- Password Input -->
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <label class="block text-sm font-medium text-gray-700">Mật khẩu</label>
                            <a 
                                href="#" 
                                @click.prevent="showForgotPasswordModal = true" 
                                class="text-sm text-green-600 hover:text-green-800 transition-colors"
                            >
                                Quên mật khẩu?
                            </a>
                        </div>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center z-10">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </span>
                            <input 
                                v-model="form.password" 
                                :type="showPassword ? 'text' : 'password'" 
                                class="input input-primary w-full pl-10 pr-10 border-gray-300 focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50" 
                                placeholder="••••••••" 
                                required 
                            />
                            <button 
                                type="button" 
                                @click="showPassword = !showPassword" 
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-green-600"
                            >
                                <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                                <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Login Button -->
                    <div>
                        <button 
                            type="submit" 
                            class="btn btn-primary w-full bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 
                                   transition-all duration-300 transform hover:scale-[1.02] active:scale-[0.98]"
                            :disabled="status === 'pending'"
                        >
                            <span v-if="status === 'pending'" class="loading loading-spinner loading-md mr-2"></span>
                            Đăng nhập
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <ForgotPasswordModal 
            v-if="showForgotPasswordModal" 
            :email="forgotPasswordEmail" 
            @close="showForgotPasswordModal = false" 
            @submit="handleForgotPassword" 
        />
    </div>
</template>

<style scoped>
@keyframes blob {
    0% {
        transform: translate(0, 0) scale(1);
    }
    33% {
        transform: translate(30px, -50px) scale(1.1);
    }
    66% {
        transform: translate(-20px, 20px) scale(0.9);
    }
    100% {
        transform: translate(0, 0) scale(1);
    }
}

.animate-blob {
    animation: blob 10s infinite;
}

.animation-delay-2000 {
    animation-delay: 2s;
}
</style>

<script setup lang="ts">
definePageMeta({
    layout: false
})
import ForgotPasswordModal from '~/components/auth/ForgotPasswordModal.vue'

const form = reactive({
    email: '',
    password: ''
})

const status = ref<'idle' | 'pending' | 'success' | 'error'>('idle')
const { login, fetchCurrentCustomer, currentCustomer, isAuthenticated, accessToken, refreshToken } = useCustomerAuth()
const router = useRouter()

const handleSubmit = async () => {
    try {
        status.value = 'pending'
        await login(form.email, form.password)
        await fetchCurrentCustomer()
        status.value = 'success'
        // Redirect to admin dashboard after successful login
        await router.push('/')
    } catch (error) {
        status.value = 'error'
        console.error('Login failed:', error)
        // Handle login error (e.g., show an error message to the user)
    }
}

const showPassword = ref(false)
const showForgotPasswordModal = ref(false)
const forgotPasswordEmail = ref('')
const handleForgotPassword = () => {
    // Handle forgot password logic here
}
</script>