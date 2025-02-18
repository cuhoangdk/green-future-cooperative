<script setup lang="ts">
interface FormData {
    email: string;
    password: string;
    remember: boolean;
    recaptchaToken?: string;
}

interface Alert {
    show: boolean;
    type: "success" | "error";
    message: string;
}

interface FormErrors {
    email?: string;
    password?: string;
    recaptcha?: string;
}

// Props
const props = defineProps<{
    showRecaptcha?: boolean;
}>();

// Composables
const { $recaptcha } = useNuxtApp();
const router = useRouter();

// Reactive state
const formData = ref<FormData>({
    email: "",
    password: "",
    remember: false,
});

const alert = ref<Alert>({
    show: false,
    type: "success",
    message: "",
});

const errors = ref<FormErrors>({});
const isLoading = ref(false);
const recaptchaElement = ref<HTMLElement | null>(null);

// Methods
const showAlert = (message: string, type: "success" | "error" = "success") => {
    alert.value = {
        show: true,
        type,
        message,
    };

    setTimeout(() => {
        alert.value.show = false;
    }, 5000);
};

const validateForm = (): boolean => {
    errors.value = {};

    if (!formData.value.email) {
        errors.value.email = "Email là bắt buộc";
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(formData.value.email)) {
        errors.value.email = "Email không hợp lệ";
    }

    if (!formData.value.password) {
        errors.value.password = "Mật khẩu là bắt buộc";
    }

    return Object.keys(errors.value).length === 0;
};

const handleSubmit = async () => {
    if (!validateForm()) return;

    try {
        isLoading.value = true;

        if (props.showRecaptcha) {
            formData.value.recaptchaToken = await $recaptcha?.getResponse();
        }

        // Call your login API here
        const response = await $fetch("http://127.0.0.1:8000/api/auth/login", {
            method: "POST",
            body: formData.value,
        });

        showAlert("Đăng nhập thành công");

        // Redirect or handle successful login
        await router.push("/admin/dashboard");
    } catch (error: any) {
        showAlert(error.message || "Đăng nhập thất bại", "error");
    } finally {
        isLoading.value = false;
        if (props.showRecaptcha) {
            $recaptcha?.reset();
        }
    }
};

// Lifecycle hooks
onMounted(() => {
    if (props.showRecaptcha) {
        $recaptcha?.init(recaptchaElement.value);
    }
});
</script>

<template>
    <div class="min-h-screen flex items-center justify-center p-4 bg-gradient-to-br from-green-50 to-green-100">
        <div class="w-full max-w-md">
            <!-- Login Container -->
            <div
                class="bg-white rounded-2xl shadow-xl p-8 space-y-6 transform transition-all duration-300 hover:-translate-y-1">
                <!-- Logo Section -->
                <div class="text-center space-y-2 mb-8">
                    <div class="flex justify-center mb-4">
                        <div class="p-2 bg-green-100 rounded-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-green-600" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">HTX Rau Sạch</h2>
                    <p class="text-green-600 font-medium">Đăng nhập quản trị</p>
                </div>

                <!-- Alert Message -->
                <div v-if="alert.show" :class="[
                    'px-4 py-3 rounded relative',
                    alert.type === 'success'
                        ? 'bg-green-100 border border-green-400 text-green-700'
                        : 'bg-red-100 border border-red-400 text-red-700',
                ]">
                    <span class="block sm:inline">{{ alert.message }}</span>
                </div>

                <!-- Login Form -->
                <form @submit.prevent="handleSubmit" class="space-y-6">
                    <!-- Email Field -->
                    <div class="space-y-2">
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input v-model="formData.email" type="email" id="email"
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
                            :class="{ 'border-red-500': errors.email }" placeholder="admin@example.com" required />
                        <p v-if="errors.email" class="text-red-500 text-sm mt-1">
                            {{ errors.email }}
                        </p>
                    </div>

                    <!-- Password Field -->
                    <div class="space-y-2">
                        <label for="password" class="block text-sm font-medium text-gray-700">Mật khẩu</label>
                        <input v-model="formData.password" type="password" id="password"
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
                            :class="{ 'border-red-500': errors.password }" placeholder="••••••••" required />
                        <p v-if="errors.password" class="text-red-500 text-sm mt-1">
                            {{ errors.password }}
                        </p>
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input v-model="formData.remember" type="checkbox" id="remember"
                                class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded" />
                            <label for="remember" class="ml-2 block text-sm text-gray-700">
                                Ghi nhớ đăng nhập
                            </label>
                        </div>
                        <NuxtLink to="/admin/forgot-password"
                            class="text-sm font-medium text-green-600 hover:text-green-500">
                            Quên mật khẩu?
                        </NuxtLink>
                    </div>

                    <!-- reCAPTCHA -->
                    <div v-if="showRecaptcha" class="flex justify-center">
                        <div ref="recaptchaElement"></div>
                        <p v-if="errors.recaptcha" class="text-red-500 text-sm mt-1">
                            {{ errors.recaptcha }}
                        </p>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200"
                        :disabled="isLoading">
                        <span v-if="isLoading" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            Đang xử lý...
                        </span>
                        <span v-else>Đăng nhập</span>
                    </button>
                </form>

                <!-- Security Notice -->
                <div class="text-center text-sm text-gray-500 mt-4">
                    <div class="flex items-center justify-center space-x-2">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        <span>Kết nối bảo mật</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>


