<template>
    <div class="w-full max-w-xl z-10 mx-auto mt-10">
        <!-- Đang xử lý -->
        <div v-if="status === 'pending'"
            class="bg-white rounded-3xl shadow-2xl p-8 border border-green-100 relative animate-fade-in text-center">
            <div class="absolute inset-0.5 bg-gradient-to-br from-transparent to-green-50 rounded-3xl -z-10 opacity-50"></div>
            <div class="flex flex-col items-center justify-center space-y-4">
                <span class="loading loading-spinner loading-lg text-green-600"></span>
                <p class="text-gray-600 text-lg">Đang xác thực tài khoản...</p>
            </div>
        </div>

        <!-- Thành công -->
        <div v-else-if="status === 'success'"
            class="bg-white rounded-3xl shadow-2xl p-8 border border-green-100 relative animate-fade-in text-center">
            <div class="absolute inset-0.5 bg-gradient-to-br from-transparent to-green-50 rounded-3xl -z-10 opacity-50"></div>
            <div class="space-y-4">
                <h1 class="text-3xl font-bold text-green-600 tracking-tight">Xác thực thành công!</h1>
                <p class="text-gray-600">
                    Tài khoản của bạn với email <span class="font-semibold">{{ email }}</span> đã được kích hoạt.
                    Bạn có thể đăng nhập để tiếp tục.
                </p>
                <NuxtLink to="/login"
                    class="inline-block mt-4 px-6 py-2 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-full hover:from-green-600 hover:to-green-700 transition-all duration-300 transform hover:scale-[1.02]">
                    Đi đến đăng nhập
                </NuxtLink>
            </div>
        </div>

        <!-- Lỗi: Tài khoản đã được xác thực (400) -->
        <div v-else-if="status === 'error' && errorStatusCode === 400"
            class="bg-white rounded-3xl shadow-2xl p-8 border border-green-100 relative animate-fade-in text-center">
            <div class="absolute inset-0.5 bg-gradient-to-br from-transparent to-green-50 rounded-3xl -z-10 opacity-50"></div>
            <div class="space-y-4">
                <h1 class="text-3xl font-bold text-yellow-600 tracking-tight">Tài khoản đã được xác thực!</h1>
                <p class="text-gray-600">
                    Tài khoản với email <span class="font-semibold">{{ email }}</span> đã được kích hoạt trước đó.
                    Vui lòng đăng nhập để tiếp tục.
                </p>
                <NuxtLink to="/login"
                    class="inline-block mt-4 px-6 py-2 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-full hover:from-green-600 hover:to-green-700 transition-all duration-300 transform hover:scale-[1.02]">
                    Đi đến đăng nhập
                </NuxtLink>
            </div>
        </div>

        <!-- Lỗi: Xác thực thất bại (422 hoặc lỗi khác) -->
        <div v-else-if="status === 'error'"
            class="bg-white rounded-3xl shadow-2xl p-8 border border-green-100 relative animate-fade-in text-center">
            <div class="absolute inset-0.5 bg-gradient-to-br from-transparent to-green-50 rounded-3xl -z-10 opacity-50"></div>
            <div class="space-y-4">
                <h1 class="text-3xl font-bold text-red-600 tracking-tight">Xác thực thất bại</h1>
                <p class="text-gray-600">{{ errorMessage }}</p>
                <div v-if="errors.email?.length" class="text-red-600 text-sm">
                    {{ errors.email[0] }}
                </div>
                <div v-if="errors.token?.length" class="text-red-600 text-sm">
                    {{ errors.token[0] }}
                </div>
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

const { verifyAccount } = useCustomerAuth()
const router = useRouter()

const token = useRoute().query.token as string
const email = useRoute().query.email as string
const status = ref<'idle' | 'pending' | 'success' | 'error'>('pending')
const errorMessage = ref<string | null>(null)
const errorStatusCode = ref<number | null>(null)
const errors = reactive<Record<string, string[]>>({
    email: [],
    token: [],
})

const verify = async () => {
    try {
        status.value = 'pending'
        errorMessage.value = null
        errorStatusCode.value = null
        Object.keys(errors).forEach((key) => {
            errors[key] = []
        })

        await verifyAccount(email, token)
        status.value = 'success'
    } catch (error: any) {
        status.value = 'error'

        // Parse lỗi từ chuỗi JSON
        let errorData;
        try {
            errorData = JSON.parse(error.message);
        } catch {
            errorData = { message: 'Xác thực tài khoản thất bại.', errors: {}, statusCode: null };
        }

        // Gán lỗi chi tiết
        Object.keys(errorData.errors).forEach((key) => {
            if (errors[key]) {
                errors[key] = errorData.errors[key];
            }
        });

        errorMessage.value = 'Xác thực tài khoản thất bại.';
        errorStatusCode.value = errorData.statusCode || null;


        setTimeout(() => {
            router.push('/login');
        }, 5000);
    }
}

verify()
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