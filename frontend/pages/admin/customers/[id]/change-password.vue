<template>
    <div class="min-h-screen items-center flex flex-col">
        <div class="w-full lg:w-1/3 rounded-2xl p-4 sm:p-5">
            <form @submit.prevent="handleSubmit" class="space-y-4">
                <div>
                    <label class="text-gray-700 font-semibold block mb-1">Mật khẩu <span
                            class="text-red-500">*</span></label>
                    <input v-model="form.password" type="password" class="input input-bordered input-primary w-full"
                        placeholder="********" required />
                </div>

                <div>
                    <label class="text-gray-700 font-semibold block mb-1">Xác nhận mật khẩu <span
                            class="text-red-500">*</span></label>
                    <input v-model="form.password_confirmation" type="password"
                        class="input input-bordered input-primary w-full" placeholder="********" required />
                </div>

                <!-- Submit Button -->
                <div class="border-t border-gray-200 pt-4 flex justify-between items-center">
                    <UiButtonBack />
                    <button type="submit" class="btn btn-primary px-6" :disabled="status === 'pending'">
                        <span v-if="status === 'pending'" class="loading loading-spinner loading-md"></span>
                        Đổi mật khẩu
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup lang="ts">
definePageMeta({ title: 'Đổi mật khẩu khách hàng', layout: 'user', })

const route = useRoute();
const { changeCustomerPassword } = useCustomer();
const { $toast } = useNuxtApp()
const router = useRouter();
const userId = Number(route.params.id);
const status = ref<'idle' | 'pending' | 'success' | 'error'>('idle');

const form = ref({
    password: '',
    password_confirmation: '',
});

// Xử lý submit form
const handleSubmit = async () => {
    try {
        status.value = 'pending';
        const formData = new FormData();
        formData.append('password', form.value.password);
        formData.append('password_confirmation', form.value.password_confirmation);

        const { error } = await changeCustomerPassword(userId, {
            password: form.value.password,
            password_confirmation: form.value.password_confirmation,
        });
        if (error.value) {
            throw new Error(error.value.message);
        }

        $toast.success('Đổi mật khẩu thành công!');
        router.push('/admin/users');
    } catch (error: any) {
        $toast.error('Đổi mật khẩu thất bại!');
    } finally {
        status.value = 'idle';
    }
};
</script>