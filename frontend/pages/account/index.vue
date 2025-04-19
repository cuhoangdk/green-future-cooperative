<template>
    <div class="min-h-screen items-center flex flex-col mt-16 p-2 lg:mt-0">
        <div class="w-full lg:w-7/12 bg-white border border-gray-200 rounded-2xl p-4 shadow-sm sm:p-5">
            <form @submit.prevent="handleSubmit" class="space-y-6">
                <!-- Phần 1: Thông tin cơ bản với avatar -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Avatar Section -->
                    <div class="flex flex-col items-center md:col-span-1">
                        <div class="w-44 h-44 mb-3 cursor-pointer" @click="triggerFileInput">
                            <img :src="form.avatar_url || defaultAvatar" @error="form.avatar_url = defaultAvatar"
                                class="w-full h-full object-cover rounded-full border shadow-sm" alt="Avatar" />
                        </div>
                        <input ref="fileInput" type="file" accept="image/*"
                            class="file-input file-input-primary w-full max-w-xs" @change="handleFileChange" hidden />
                        <label class="text-gray-700 font-semibold mt-2">Ảnh đại diện</label>
                    </div>

                    <!-- Thông tin cá nhân chính -->
                    <div class="md:col-span-1 grid grid-cols-1 gap-4">
                        <div>
                            <label class="text-gray-700 font-semibold block mb-1">Email</label>
                            <input v-model="form.email" type="email" class="input input-bordered input-primary bg-gray-200 w-full"
                                placeholder="example@email.com" readonly />
                        </div>

                        <!-- Thông tin nhận dạng -->
                        <div>
                            <label class="text-gray-700 font-semibold block mb-1">Họ và tên</label>
                            <input v-model="form.full_name" class="input input-bordered input-primary w-full"
                                placeholder="Nguyen Van A" required />
                        </div>

                        <div>
                            <label class="text-gray-700 font-semibold block mb-1">Số điện thoại</label>
                            <input v-model="form.phone_number" type="tel"
                                class="input input-bordered input-primary w-full" placeholder="0123-456-789" />
                        </div>

                        <div>
                            <label class="text-gray-700 font-semibold block mb-1">Giới tính</label>
                            <div class="flex items-center space-x-4">
                                <label class="flex items-center">
                                    <input v-model="form.gender" type="radio" value="male" class="radio radio-primary" />
                                    <span class="ml-2">Nam</span>
                                </label>
                                <label class="flex items-center">
                                    <input v-model="form.gender" type="radio" value="female" class="radio radio-primary" />
                                    <span class="ml-2">Nữ</span>
                                </label>
                                <label class="flex items-center">
                                    <input v-model="form.gender" type="radio" value="other" class="radio radio-primary" />
                                    <span class="ml-2">Khác</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="text-gray-700 font-semibold block mb-1">Ngày sinh</label>
                            <input v-model="form.date_of_birth" type="date"
                                class="input input-bordered input-primary w-full" />
                        </div>
                    </div>
                </div>
                <!-- Phần 2: Thông tin địa chỉ -->
                <!-- <div class="border-t border-gray-200 pt-5">
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="text-lg font-medium text-gray-800">Thông tin địa chỉ</h3>
                        <button type="button" class="btn btn-secondary"
                            @click="$router.push('/account/add-address')">Thêm địa chỉ</button>
                    </div>
                    <div v-for="address in addresses" :key="address.id">
                        <UiAddressCard :address="address" :addressDetailId="address.address.ward" />
                    </div>
                </div> -->

                <!-- Submit Button -->
                <div class="border-t border-gray-200 pt-5 flex justify-between">
                    <div>
                        <Button @click="handleLogout" class="btn btn-error mr-2">
                            <LogOut class="w-4 h-4" />
                        </Button>
                        <NuxtLink to="/account/change-password" class="btn btn-warning mr-2">
                            <Key class="w-4 h-4" />
                        </NuxtLink>
                    </div>
                    <button type="submit" class="btn btn-primary px-6" :disabled="status === 'pending'">
                        <span v-if="status === 'pending'" class="loading loading-spinner loading-md mr-2"></span>
                        Cập nhật
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup lang="ts">
import { Key, LogOut  } from 'lucide-vue-next';
import type { CustomerAddress } from '~/types/customer';

const { currentCustomer, updateProfile, logout } = useCustomerAuth();
const { getCustomerAddress } = useCustomerAddress();
const { $toast } = useNuxtApp();

// Avatar mặc định nếu không có ảnh
const defaultAvatar = useRuntimeConfig().public.placeholderImage;

// Ref để tham chiếu đến input file và file được chọn
const fileInput = ref<HTMLInputElement | null>(null);
const selectedFile = ref<File | null>(null);
const status = ref<'idle' | 'pending' | 'success' | 'error'>('idle');

// const { data: dataAddresses } = await getCustomerAddress();
// const addresses = computed<CustomerAddress[]>(() => Array.isArray(dataAddresses.value?.data) ? dataAddresses.value.data : dataAddresses.value ? [dataAddresses.value.data] : [])

// Khởi tạo form với dữ liệu từ currentCustomer
const form = ref({
    full_name: currentCustomer.value?.full_name || '',
    email: currentCustomer.value?.email || '',
    phone_number: currentCustomer.value?.phone_number || '',
    date_of_birth: currentCustomer.value?.date_of_birth
        ? new Date(currentCustomer.value.date_of_birth).toISOString().split('T')[0]
        : '',
    newsletter_subscribed: currentCustomer.value?.newsletter_subscribed || false,
    gender: currentCustomer.value?.gender || '',
    avatar_url: currentCustomer.value?.avatar_url
        ? `${useRuntimeConfig().public.backendUrl}${currentCustomer.value.avatar_url}`
        : defaultAvatar,
});

// Hàm kích hoạt input file khi click vào ảnh
const triggerFileInput = () => {
    if (fileInput.value) {
        fileInput.value.click();
    }
};

// Xử lý khi file được chọn
const handleFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];
    if (file) {
        selectedFile.value = file; // Lưu file để upload
        form.value.avatar_url = URL.createObjectURL(file); // Hiển thị tạm thời
    }
};

// Xử lý submit form
const handleSubmit = async () => {
    try {
        status.value = 'pending';
        // Tạo FormData để gửi dữ liệu multipart
        const formData = new FormData();
        if (form.value.full_name) { formData.append('full_name', form.value.full_name); }
        if (form.value.phone_number) { formData.append('phone_number', form.value.phone_number); }
        if (form.value.date_of_birth) { formData.append('date_of_birth', form.value.date_of_birth); }
        if (form.value.gender) { formData.append('gender', form.value.gender); }
        formData.append('newsletter_subscribed', form.value.newsletter_subscribed ? '1' : '0');

        // Thêm file avatar nếu có
        if (selectedFile.value) {
            formData.append('avatar_url', selectedFile.value);
        }

        // Gửi request với FormData
        const result = await updateProfile(formData);

        if (result.success) {
            $toast.success('Cập nhật thông tin thành công!');
            // Cập nhật lại avatar_url từ server nếu cần
            if (result.customer?.avatar_url) {
                form.value.avatar_url = `${useRuntimeConfig().public.backendUrl}${result.customer.avatar_url}`;
            }
        }
    } catch (error: any) {
        $toast.error(error.message || 'Cập nhật thông tin thất bại!');
    } finally {
        status.value = 'idle';
    }
};

const handleLogout = async () => {
    try {
        await logout();
        $toast.success('Đăng xuất thành công!');
        // Chuyển hướng về trang đăng nhập hoặc trang chủ
        useRouter().push('/login');
    } catch (error: any) {
        $toast.error(error.message || 'Đăng xuất thất bại!');
    }
};
</script>