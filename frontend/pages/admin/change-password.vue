<template>
    <div class="min-h-screen items-center flex flex-col">
        <div class="w-full lg:w-1/3 rounded-2xl p-4 sm:p-5">
            <form @submit.prevent="handleSubmit" class="space-y-6">
                <!-- Phần 3: Thông tin tài chính và khác -->
                <div>
                    <div class="mb-4">
                        <label for="current-password" class="text-gray-700 font-semibold block mb-1">
                            Mật khẩu hiện tại <span class="text-red-500">*</span>
                        </label>
                        <input id="current-password" v-model="form.current_password" type="password"
                            class="input input-bordered input-primary w-full" placeholder="Nhập mật khẩu hiện tại"
                            required />
                    </div>
                    <div class="mb-4">
                        <label for="new-password" class="text-gray-700 font-semibold block mb-1">
                            Mật khẩu mới <span class="text-red-500">*</span>
                        </label>
                        <input id="new-password" v-model="form.new_password" type="password"
                            class="input input-bordered input-primary w-full" placeholder="Nhập mật khẩu mới"
                            required />
                    </div>
                    <div>
                        <label for="new-password-confirmation" class="text-gray-700 font-semibold block mb-1">
                            Xác nhận mật khẩu mới <span class="text-red-500">*</span>
                        </label>
                        <input id="new-password-confirmation" v-model="form.new_password_confirmation" type="password"
                            class="input input-bordered input-primary w-full" placeholder="Xác nhận mật khẩu mới"
                            required />
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="border-t border-gray-200 pt-5 flex justify-between items-center">
                    <button type="button" class="btn mr-2" @click="$router.back()">
                        Quay lại    
                    </button>
                    <button type="submit" class="btn btn-primary px-6" :disabled="status === 'pending' || !isFormValid">
                        <span v-if="status === 'pending'" class="loading loading-spinner loading-md"></span>
                        Đổi mật khẩu
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup lang="ts">
definePageMeta({title: 'Đổi mật khẩu',layout: 'user',})
const { changePassword } = useUserAuth();
const { $toast } = useNuxtApp();
const router = useRouter();
const status = ref<'idle' | 'pending' | 'success' | 'error'>('idle');

const form = ref({
    current_password: '',
    new_password: '',
    new_password_confirmation: '',
});

// Kiểm tra xem mật khẩu mới và xác nhận mật khẩu có khớp nhau không
const isFormValid = computed(() => {
    return form.value.new_password === form.value.new_password_confirmation && 
           form.value.new_password.length > 0;
});

// Xử lý submit form
const handleSubmit = async () => {
    // Kiểm tra xác nhận mật khẩu
    if (form.value.new_password !== form.value.new_password_confirmation) {
        $toast.error('Mật khẩu xác nhận không khớp với mật khẩu mới!');
        return;
    }

    try {
        status.value = 'pending';
        const { error } = await changePassword({
            current_password: form.value.current_password,
            new_password: form.value.new_password,
            new_password_confirmation: form.value.new_password_confirmation,
        });

        if (error.value) {
            throw new Error(error.value.message);
        }

        $toast.success('Đổi mật khẩu thành công!');
        router.push('/admin/profile');
    } catch (error: any) {
        $toast.error('Mật khẩu cũ không chính xác!');
    } finally {
        status.value = 'idle';
    }
};
</script>