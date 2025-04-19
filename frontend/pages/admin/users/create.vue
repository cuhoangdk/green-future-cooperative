<template>
    <div class="p-4">
        <form @submit.prevent="handleSubmit" class="space-y-6">
            <!-- Phần 1: Thông tin cơ bản với avatar -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
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
                            placeholder="0998989839" required />
                    </div>

                    <div>
                        <label class="text-gray-700 font-semibold block mb-1">Giới tính</label>
                        <div class="flex items-center space-x-4">
                            <label class="flex items-center space-x-2">
                                <input type="radio" v-model="form.gender" value="male" class="radio radio-primary" />
                                <span>Nam</span>
                            </label>
                            <label class="flex items-center space-x-2">
                                <input type="radio" v-model="form.gender" value="female" class="radio radio-primary" />
                                <span>Nữ</span>
                            </label>
                            <label class="flex items-center space-x-2">
                                <input type="radio" v-model="form.gender" value="other" class="radio radio-primary" />
                                <span>Khác</span>
                            </label>
                        </div>
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

                    <div>
                        <label class="text-gray-700 font-semibold block mb-1">Ngày sinh</label>
                        <input v-model="form.date_of_birth" type="date" class="input input-bordered input-primary w-full" />
                    </div>

                    <div>
                        <label class="text-gray-700 font-semibold block mb-1">Vai trò</label>
                        <select v-model="form.is_super_admin" class="select select-bordered select-primary w-full">
                            <option :value="true">Quản trị viên</option>
                            <option :value="false">Người dùng</option>
                        </select>
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
                        <select v-model="form.address.province"
                            @change="handleProvinceChange"
                            class="select select-bordered select-primary w-full">
                            <option value="" disabled>Tỉnh/thành phố</option>
                            <option v-for="p in provinces" :key="p.id" :value="p.id">
                                {{ p.full_name }}
                            </option>
                        </select>
                    </div>

                    <!-- District -->
                    <div>
                        <label class="text-gray-700 font-semibold block mb-1">Quận/Huyện</label>
                        <select v-model="form.address.district"
                            @change="handleDistrictChange"
                            class="select select-bordered select-primary w-full">
                            <option value="" disabled>Quận/huyện</option>
                            <option v-for="d in districts" :key="d.id" :value="d.id">
                                {{ d.full_name }}
                            </option>
                        </select>
                    </div>

                    <!-- Ward -->
                    <div>
                        <label class="text-gray-700 font-semibold block mb-1">Phường/Xã</label>
                        <select v-model="form.address.ward" class="select select-bordered select-primary w-full">
                            <option value="" disabled>Phường/xã</option>
                            <option v-for="w in wards" :key="w.id" :value="w.id">
                                {{ w.full_name }}
                            </option>
                        </select>
                    </div>

                    <!-- Street Address (Full Width) -->
                    <div class="sm:col-span-3">
                        <label class="text-gray-700 font-semibold block mb-1">Địa chỉ chi tiết</label>
                        <input v-model="form.address.street_address" class="input input-bordered input-primary w-full"
                            placeholder="Số nhà, tên đường..." />
                    </div>
                </div>
            </div>

            <!-- Phần 3: Thông tin tài chính và khác -->
            <div class="border-t border-gray-200 pt-5">
                <h3 class="text-lg font-medium text-gray-800 mb-3">Thông tin tài chính</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Bank Name -->
                    <div>
                        <label class="text-gray-700 font-semibold block mb-1">Tên ngân hàng</label>
                        <input v-model="form.bank_name" class="input input-bordered input-primary w-full" placeholder="Techcombank" />
                    </div>

                    <!-- Bank Account Number -->
                    <div>
                        <label class="text-gray-700 font-semibold block mb-1">Số tài khoản ngân hàng</label>
                        <input v-model="form.bank_account_number" class="input input-bordered input-primary w-full"
                            placeholder="123456789" />
                    </div>
                </div>
            </div>

            <!-- Phần 4: Tiểu sử -->
            <div class="border-t border-gray-200 pt-5">
                <h3 class="text-lg font-medium text-gray-800 mb-3">Thông tin bổ sung</h3>
                <div>
                    <label class="text-gray-700 font-semibold block mb-1">Tiểu sử</label>
                    <textarea v-model="form.bio" class="textarea textarea-bordered textarea-primary w-full h-32"
                        placeholder="Giới thiệu về bạn..." />
                </div>
            </div>

            <!-- Submit Button -->
            <div class="border-t border-gray-200 pt-4 flex justify-between items-center">
                <UiButtonBack />
                <button type="submit" class="btn btn-primary px-6" :disabled="status === 'pending'">
                    <span v-if="status === 'pending'" class="loading loading-spinner loading-md"></span>
                    Thêm
                </button>
            </div>
        </form>
    </div>
</template>

<script setup lang="ts">
definePageMeta({
    title: 'Thêm người dùng mới',
    layout: 'user',
})

import { useUserAuth } from '#imports';
import { useNuxtApp } from '#app';
import { useVietnamAddress } from '#imports';
import { useUsers } from '#imports';

const { provinces, districts, wards, fetchProvinces, fetchDistricts, fetchWards } = useVietnamAddress();
const { createUser } = useUsers();
const { $toast } = useNuxtApp();
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
    is_super_admin: false,
    password: '',
    password_confirmation: '',
    bank_name: '',
    bank_account_number: '',
    bio: '',
    avatar_url: defaultAvatar,
    address: {
        province: '',
        district: '',
        ward: '',
        street_address: ''
    },
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

// Xử lý khi chọn tỉnh/thành phố
const handleProvinceChange = async () => {
    if (form.value.address.province) {
        await fetchDistricts(form.value.address.province);
        form.value.address.district = '';
        form.value.address.ward = '';
    }
};

// Xử lý khi chọn quận/huyện
const handleDistrictChange = async () => {
    if (form.value.address.district) {
        await fetchWards(form.value.address.district);
        form.value.address.ward = '';
    }
};

// Lấy danh sách tỉnh/thành phố khi component được tạo
await fetchProvinces();

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
        formData.append('is_super_admin', form.value.is_super_admin ? '1' : '0');
        formData.append('password', form.value.password);
        formData.append('password_confirmation', form.value.password_confirmation);
        formData.append('bank_name', form.value.bank_name || '');
        formData.append('bank_account_number', form.value.bank_account_number || '');
        formData.append('bio', form.value.bio || '');

        // Thêm file avatar nếu có
        if (selectedFile.value) {
            formData.append('avatar_url', selectedFile.value);
        }

        // Thêm address
        formData.append('address[province]', form.value.address.province);
        formData.append('address[district]', form.value.address.district);
        formData.append('address[ward]', form.value.address.ward);
        formData.append('address[street_address]', form.value.address.street_address);

        // Gửi request với FormData
        const { error } = await createUser(formData);

        if (error.value) {
            throw new Error(error.value.message);
        }

        $toast.success('Thêm người dùng thành công!');
        router.push('/admin/users');
    } catch (error: any) {
        $toast.error(error.message || 'Thêm người dùng thất bại!');
    } finally {
        status.value = 'idle';
    }
};
</script>