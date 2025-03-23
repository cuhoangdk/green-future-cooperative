<template>
    <div class="min-h-screen flex items-center justify-center p-4 bg-gradient-to-br from-white to-green-50">
        <div class="w-full max-w-md">
            <div
                class="bg-white rounded-2xl shadow-xl p-8 space-y-6 transform transition-all duration-300 hover:-translate-y-0.5">
                <!-- Logo Section -->
                <div class="text-center space-y-2 mb-8">
                    <div class="flex justify-center mb-4">
                        <img src="/images/logo.jpg" alt="Logo" class="h-28 w-28 rounded-full">
                    </div>
                    <h1 class="text-3xl font-bold text-gray-800">Green Future</h1>
                    <p class="text-green-600 font-medium">Đăng nhập quản trị</p>
                </div>
                <form @submit.prevent="handleSubmit" class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tên đăng nhập</label>
                        <input v-model="form.email" type="email" class="input input-primary w-full"
                            placeholder="greenfuture@gmail.com" required />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Mật khẩu</label>
                        <input v-model="form.password" type="password" class="input input-primary w-full"
                            placeholder="••••••••" required />
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary w-full" :disabled="status === 'pending'">
                            <span v-if="status === 'pending'" class="loading loading-spinner loading-md"></span>
                            <span>Đăng nhập</span>
                        </button>
                    </div>
                </form>
            </div>
            <pre>{{ currentUser }}</pre>
            <pre>{{ isAuthenticated }}</pre>
            <pre>{{ accessToken }}</pre>
            <pre>{{ refreshToken }}</pre>
        </div>
    </div>
</template>

<script setup lang="ts">
definePageMeta({
    layout: false
})

import { useUserAuth } from '#imports'

const form = reactive({
    email: '',
    password: ''
})

const status = ref<'idle' | 'pending' | 'success' | 'error'>('idle')
const { login, currentUser, isAuthenticated, accessToken, refreshToken } = useUserAuth()
const router = useRouter()

const handleSubmit = async () => {
    try {
        status.value = 'pending'
        await login(form.email, form.password)
        status.value = 'success'
        // Redirect to admin dashboard after successful login
        await router.push('/admin')
    } catch (error) {
        status.value = 'error'
        console.error('Login failed:', error)
        // Handle login error (e.g., show an error message to the user)
    }
}
</script>