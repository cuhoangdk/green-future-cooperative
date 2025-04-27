<template>
    <div class="min-h-screen flex flex-col items-center mt-16 lg:mt-0 p-2 ">
        <div v-if="submit != 'success'" class="w-full lg:w-8/12 bg-white border border-gray-200 rounded-2xl p-4 my-4">
            <form @submit.prevent="handleSubmit" class="space-y-6">
                <div class="border-gray-200">
                    <h3 class="text-lg font-medium text-gray-800 mb-3">Thông tin liên hệ</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Name -->
                        <div class="sm:col-span-2">
                            <label class="text-gray-700 font-semibold block mb-1">Họ và tên</label>
                            <input v-model="form.name" type="text" class="input input-bordered input-primary w-full"
                                placeholder="Nhập họ và tên của bạn" required />
                        </div>
                        <!-- Email -->
                        <div>
                            <label class="text-gray-700 font-semibold block mb-1">Email</label>
                            <input v-model="form.email" type="email" class="input input-bordered input-primary w-full"
                                placeholder="Nhập email của bạn" required />
                        </div>

                        <!-- Phone Number -->
                        <div>
                            <label class="text-gray-700 font-semibold block mb-1">Số điện thoại</label>
                            <input v-model="form.phone" type="tel" class="input input-bordered input-primary w-full"
                                placeholder="Nhập số điện thoại" required />
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
                    <button type="submit" class="btn btn-primary px-6" :disabled="submit === 'pending'">
                        <span v-if="submit === 'pending'" class="loading loading-spinner loading-md"></span>
                        Gửi liên hệ
                    </button>
                </div>
            </form>
        </div>
        <div v-else-if="submit === 'success'"
            class="flex flex-col justify-center items-center w-full h-54 lg:w-8/12 bg-white border border-gray-200 rounded-2xl p-4 my-4 text-center">
            <h3 class="text-lg font-medium text-green-600 mb-3">Gửi liên hệ thành công!</h3>
            <p class="text-gray-600">Cảm ơn bạn đã gửi liên hệ. Chúng tôi sẽ liên lạc phản hồi sớm nhất có thể.</p>
        </div>
    </div>
</template>

<script setup lang="ts">

const { createMessage } = useContactMessages();
const { currentCustomer } = useCustomerAuth();
const { $toast } = useNuxtApp();
// Form state
const form = ref({
    name: currentCustomer?.value?.full_name || '',
    email: currentCustomer?.value?.email || '',
    phone: currentCustomer?.value?.phone_number || '',
    gender: currentCustomer?.value?.gender || '',
    message: ''
});

// Form status
const submit = ref<'idle' | 'pending' | 'success' | 'error'>('idle');

// Handlers
const handleSubmit = async () => {
    submit.value = 'pending';
    const formData = new FormData();
    formData.append('name', form.value.name);
    formData.append('email', form.value.email);
    formData.append('phone', form.value.phone);
    formData.append('gender', form.value.gender);
    formData.append('message', form.value.message);

    const { status } = await createMessage(formData);
    if (status.value === 'success') {
        submit.value = 'success';
        form.value = { name: '', email: '', phone: '', gender: '', message: '' };
        $toast.success('Gửi liên hệ thành công!');
    } else {
        submit.value = 'error';
        $toast.error('Gửi liên hệ thất bại!');
    }
};

</script>