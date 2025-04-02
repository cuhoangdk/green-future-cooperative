<template>
    <div class="border border-gray-200 rounded-lg p-4 sm:p-5">
        <form @submit.prevent="handleSubmit" class="space-y-6">
            <!-- Phần 1: Thông tin cơ bản với avatar -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <!-- Avatar Section -->
                <div class="flex flex-col items-center md:col-span-1">
                    <div class="w-48 h-48 mb-3 cursor-pointer" @click="triggerFileInput">
                        <img :src="form.avatar_url || defaultAvatar" @error="form.avatar_url = defaultAvatar"
                            class="w-full h-full object-cover rounded-full border shadow-sm" alt="Avatar" />
                    </div>
                    <input ref="fileInput" type="file" accept="image/*"
                        class="file-input file-input-primary w-full max-w-xs" @change="handleFileChange" hidden />
                    <label class="text-gray-700 font-semibold mt-2">Ảnh đại diện</label>
                </div>

                <!-- Thông tin cá nhân chính -->
                <div class="md:col-span-3 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Thông tin nhận dạng -->
                    <div>
                        <label class="text-gray-700 font-semibold block mb-1">Họ và tên <span class="text-red-500">*</span></label>
                        <input v-model="form.full_name" class="input input-bordered input-primary w-full"
                            placeholder="Nguyen Van A" required />
                    </div>

                    <div>
                        <label class="text-gray-700 font-semibold block mb-1">Email <span class="text-red-500">*</span></label>
                        <input v-model="form.email" type="email" class="input input-bordered input-primary w-full"
                            placeholder="example@email.com" required />
                    </div>

                    <div>
                        <label class="text-gray-700 font-semibold block mb-1">Số điện thoại <span class="text-red-500">*</span></label>
                        <input v-model="form.phone_number" type="tel" class="input input-bordered input-primary w-full"
                            placeholder="0123-456-789" required />
                    </div>

                    <div>
                        <label class="text-gray-700 font-semibold block mb-1">Giới tính</label>
                        <select v-model="form.gender" class="select select-bordered select-primary w-full">
                            <option value="">Chọn giới tính</option>
                            <option value="male">Nam</option>
                            <option value="female">Nữ</option>
                            <option value="other">Khác</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-gray-700 font-semibold block mb-1">Ngày sinh</label>
                        <input v-model="form.date_of_birth" type="date" class="input input-bordered input-primary w-full" />
                    </div>

                    <div>
                        <label class="text-gray-700 font-semibold block mb-1">Đăng ký nhận tin</label>
                        <select v-model="form.newsletter_subscribed" class="select select-bordered select-primary w-full">
                            <option :value="true">Có</option>
                            <option :value="false">Không</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-gray-700 font-semibold block mb-1">Mật khẩu <span class="text-red-500">*</span></label>
                        <input v-model="form.password" type="password" class="input input-bordered input-primary w-full"
                            placeholder="********" required />
                    </div>

                    <div>
                        <label class="text-gray-700 font-semibold block mb-1">Xác nhận mật khẩu <span class="text-red-500">*</span></label>
                        <input v-model="form.password_confirmation" type="password" class="input input-bordered input-primary w-full"
                            placeholder="********" required />
                    </div>
                </div>
            </div>

            <!-- Phần 2: Thông tin địa chỉ -->
            <div class="border-t border-gray-200 pt-5">
                <h3 class="text-lg font-medium text-gray-800 mb-3">Thông tin địa chỉ</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <!-- Province -->
                    <div>
                        <label class="text-gray-700 font-semibold block mb-1">Tỉnh/Thành phố</label>
                        <select v-model="address.province"
                            @change="handleProvinceChange"
                            class="select select-bordered select-primary w-full" required>
                            <option value="" disabled>Chọn tỉnh/thành phố</option>
                            <option v-for="p in provinces" :key="p.id" :value="p.id">
                                {{ p.full_name }}
                            </option>
                        </select>
                    </div>

                    <!-- District -->
                    <div>
                        <label class="text-gray-700 font-semibold block mb-1">Quận/Huyện</label>
                        <select v-model="address.district"
                            @change="handleDistrictChange"
                            class="select select-bordered select-primary w-full" required>
                            <option value="" disabled>Chọn quận/huyện</option>
                            <option v-for="d in districts" :key="d.id" :value="d.id">
                                {{ d.full_name }}
                            </option>
                        </select>
                    </div>

                    <!-- Ward -->
                    <div>
                        <label class="text-gray-700 font-semibold block mb-1">Phường/Xã</label>
                        <select v-model="address.ward" class="select select-bordered select-primary w-full" required>
                            <option value="" disabled>Chọn phường/xã</option>
                            <option v-for="w in wards" :key="w.id" :value="w.id">
                                {{ w.full_name }}
                            </option>
                        </select>
                    </div>

                    <!-- Street Address (Full Width) -->
                    <div class="sm:col-span-3">
                        <label class="text-gray-700 font-semibold block mb-1">Địa chỉ chi tiết</label>
                        <input v-model="address.street_address" class="input input-bordered input-primary w-full"
                            placeholder="Số nhà, tên đường..." required />
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="border-t border-gray-200 pt-5 flex justify-end">
                <button type="button" @click="$router.push('/admin/customers')" class="btn btn-ghost mr-2">Hủy</button>
                <button type="submit" class="btn btn-primary px-6" :disabled="status === 'pending'">
                    <span v-if="status === 'pending'" class="loading loading-spinner loading-md"></span>
                    Thêm khách hàng
                </button>
            </div>
        </form>
    </div>
</template>

<script setup lang="ts">
definePageMeta({
    title: 'Thêm khách hàng mới',
    layout: 'user',
})

import { useToast } from 'vue-toastification';
import { useRuntimeConfig } from '#app';
import { useVietnamAddress } from '#imports';
import { useCustomer } from '#imports';

const { provinces, districts, wards, fetchProvinces, fetchDistricts, fetchWards } = useVietnamAddress();
const { createCustomer, createCustomerAddress } = useCustomer();
const toast = useToast();
const router = useRouter();

// Avatar mặc định nếu không có ảnh
const defaultAvatar = useRuntimeConfig().public.placeholderImage;

// Ref để tham chiếu đến input file và file được chọn
const fileInput = ref<HTMLInputElement | null>(null);
const selectedFile = ref<File | null>(null);
const status = ref<'idle' | 'pending' | 'success' | 'error'>('idle');

// Khởi tạo form với dữ liệu mặc định
const form = ref({
    full_name: '',
    email: '',
    phone_number: '',
    date_of_birth: '',
    gender: '',
    newsletter_subscribed: false,
    password: '',
    password_confirmation: '',
    avatar_url: defaultAvatar,
});

// Khởi tạo địa chỉ mặc định
const address = ref({
    province: '',
    district: '',
    ward: '',
    street_address: ''
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

// Lấy danh sách tỉnh/thành phố khi component được tạo
await fetchProvinces();

// Xử lý khi chọn tỉnh/thành phố
const handleProvinceChange = async () => {
    if (address.value.province) {
        await fetchDistricts(address.value.province);
        address.value.district = '';
        address.value.ward = '';
    }
};

// Xử lý khi chọn quận/huyện
const handleDistrictChange = async () => {
    if (address.value.district) {
        await fetchWards(address.value.district);
        address.value.ward = '';
    }
};


// Xử lý submit form
const handleSubmit = async () => {
    try {
        status.value = 'pending';
        // Tạo FormData để gửi dữ liệu multipart
        const formData = new FormData();
        formData.append('full_name', form.value.full_name);
        formData.append('email', form.value.email);
        formData.append('phone_number', form.value.phone_number);
        formData.append('date_of_birth', form.value.date_of_birth);
        formData.append('gender', form.value.gender);
        formData.append('newsletter_subscribed', form.value.newsletter_subscribed ? '1' : '0');
        formData.append('password', form.value.password);
        formData.append('password_confirmation', form.value.password_confirmation);

        // Thêm file avatar nếu có
        if (selectedFile.value) {
            formData.append('avatar_url', selectedFile.value);
        }

        // Gửi request với FormData
        const { data, error } = await createCustomer(formData);

        if (error.value) {
            throw new Error(error.value.message);
        }

        // Thêm địa chỉ cho khách hàng mới tạo
        if (data.value) {
            const addressData = new FormData();
            addressData.append('full_name', form.value.full_name);
            addressData.append('phone_number', form.value.phone_number);
            addressData.append('address_type', 'home');
            addressData.append('address[province]', address.value.province);
            addressData.append('address[district]', address.value.district);
            addressData.append('address[ward]', address.value.ward);
            addressData.append('address[street_address]', address.value.street_address);

            const { error: addressError } = await createCustomerAddress(data.value.data.id, addressData);
            if (addressError.value) {
                throw new Error(addressError.value.message);
            }
        }

        toast.success('Thêm khách hàng thành công!');
        router.push('/admin/customers');
    } catch (error: any) {
        toast.error(error.message || 'Thêm khách hàng thất bại!');
    } finally {
        status.value = 'idle';
    }
};
</script>
