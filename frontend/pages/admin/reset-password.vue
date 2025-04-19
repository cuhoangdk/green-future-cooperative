<template>
    <div class="w-full max-w-md z-10">
        <div
            class=" bg-white rounded-3xl shadow-2xl p-8 space-y-6 border border-green-100 relative transform transition-all duration-300 hover:shadow-3xl hover:-translate-y-[1px]">
            <div class="absolute inset-0.5 bg-gradient-to-br from-transparent to-green-50 rounded-3xl -z-10 opacity-50">
            </div>

            <form @submit.prevent="handleSubmit" class="space-y-3">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center z-10">
                            <Mail class="h-5 w-5 text-gray-400" />
                        </span>
                        <div type="email" class="input input-primary w-full pl-10 rounded-full">{{ email }}</div>
                    </div>
                </div>

                <!-- Password Input -->
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-sm font-medium text-gray-700">Mật khẩu</label>
                    </div>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center z-10">
                            <Lock class="h-5 w-5 text-gray-400" />
                        </span>
                        <input v-model="form.password" :type="showPassword ? 'text' : 'password'"
                            class="input input-primary w-full pl-10 pr-10 rounded-full"
                            :class="{ 'input-error': errorMessage }" placeholder="••••••••" required />
                        <button type="button" @click="showPassword = !showPassword"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-green-600">
                            <EyeClosed v-if="!showPassword" class="h-5 w-5" />
                            <Eye v-else class="h-5 w-5" />
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Xác nhận mật khẩu</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center z-10">
                            <Lock class="h-5 w-5 text-gray-400" />
                        </span>
                        <input v-model="form.password_confirmation" :type="showPasswordConfirmed ? 'text' : 'password'"
                            class="input input-primary w-full pl-10 pr-10 rounded-full"
                             placeholder="••••••••" required />
                        <button type="button" @click="showPasswordConfirmed = !showPasswordConfirmed"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-green-600">
                            <EyeClosed v-if="!showPasswordConfirmed" class="h-5 w-5" />
                            <Eye v-else class="h-5 w-5" />
                        </button>
                    </div>
                </div>

                <!-- Error Message -->
                <div v-if="errorMessage" class="text-red-600 text-sm text-center animate-fade-in">
                    {{ errorMessage }}
                </div>

                <!-- Login Button -->
                <div class="flex flex-col space-y-4 justify-center items-center mt-6">
                    <button type="submit"
                        class="btn btn-primary w-full bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 transition-all duration-300 transform hover:scale-[1.02] active:scale-[0.98] rounded-full"
                        :disabled="status === 'pending'">
                        <span v-if="status === 'pending'" class="loading loading-spinner loading-md mr-2"></span>
                        Đổi mật khẩu
                    </button>
                    <NuxtLink href="/login" class="text-sm text-green-600 hover:text-green-800 transition-colors ">
                        Quay lại
                    </NuxtLink>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'empty' })
import { Mail, Lock, Eye, EyeClosed } from 'lucide-vue-next'

const { resetPassword } = useUserAuth()
const router = useRouter()
const { $toast } = useNuxtApp()

const token = useRoute().query.token as string
const email = useRoute().query.email as string

const showPassword = ref(false)
const showPasswordConfirmed = ref(false)
const status = ref<'idle' | 'pending' | 'success' | 'error'>('idle')
const errorMessage = ref<string | null>(null) // Thêm state để lưu thông báo lỗi
const form = reactive({
    password: '',
    password_confirmation: '', // Thêm trường xác nhận mật khẩu
})

const handleSubmit = async () => {
    try {
        status.value = 'pending'
        errorMessage.value = null 
        await resetPassword(email ,token, form.password, form.password_confirmation)
        status.value = 'success'
        $toast.success("Đổi mật khẩu thành công") 
        await router.push('/admin/login')
    } catch (error: any) {
        status.value = 'error'
        errorMessage.value = error
        $toast.error("Đổi mật khẩu thất bại") 
    }
}
</script>