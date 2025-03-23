<template>
    <div class="border border-gray-200 rounded-lg p-4 sm:p-5">
        <form @submit.prevent="handleSubmit" class="space">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-x-4">
                <div class="flex flex-col items-center md:col-span-1">
                    <!-- Avatar Section -->
                    <div class="w-60 h-60 mb-4 cursor-pointer" @click="triggerFileInput">
                        <img :src="form.avatar_url || defaultAvatar" @error="form.avatar_url = defaultAvatar"
                            class="w-full h-full object-cover rounded-full border" alt="Avatar" />
                    </div>
                    <input ref="fileInput" type="file" accept="image/*"
                        class="file-input file-input-primary w-full max-w-xs" @change="handleFileChange" hidden />
                    <label class="text-gray-700 font-semibold mt-2">Ảnh đại diện</label>
                </div>

                <!-- Profile Details Section -->
                <div class="md:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Required Fields Section -->
                    <div class="sm:col-span-2 p-4 border border-primary rounded-lg">
                        <h3 class="text-lg font-semibold mb-4">Thông tin bắt buộc</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Full Name -->
                            <div>
                                <label class="text-gray-700 font-semibold">Họ và tên <span class="text-red-500">*</span></label>
                                <input v-model="form.full_name" class="input input-primary w-full mt-1"
                                    placeholder="Nguyen Van A" required />
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="text-gray-700 font-semibold">Email <span class="text-red-500">*</span></label>
                                <input v-model="form.email" type="email" class="input input-primary w-full mt-1"
                                    placeholder="example@email.com" required />
                            </div>

                            <!-- Phone Number -->
                            <div>
                                <label class="text-gray-700 font-semibold">Số điện thoại <span class="text-red-500">*</span></label>
                                <input v-model="form.phone_number" type="tel" class="input input-primary w-full mt-1"
                                    placeholder="0123-456-789" required />
                            </div>

                            <!-- Role -->
                            <div>
                                <label class="text-gray-700 font-semibold">Vai trò</label>
                                <select v-model="form.is_super_admin" class="select select-primary w-full mt-1">
                                    <option :value="true">Quản trị viên</option>
                                    <option :value="false">Người dùng</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Optional Fields -->
                    <div>
                        <label class="text-gray-700 font-semibold">Ngày sinh</label>
                        <input v-model="form.date_of_birth" type="date" class="input input-primary w-full mt-1" />
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="text-gray-700 font-semibold">Trạng thái</label>
                        <select v-model="form.is_banned" class="select select-primary w-full mt-1">
                            <option :value="false">Hoạt động</option>
                            <option :value="true">Đã bị cấm</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">
                <!-- Gender -->
                <div>
                    <label class="text-gray-700 font-semibold">Giới tính</label>
                    <select v-model="form.gender" class="select select-primary w-full mt-1">
                        <option value="">Chọn giới tính</option>
                        <option value="male">Nam</option>
                        <option value="female">Nữ</option>
                        <option value="other">Khác</option>
                    </select>
                </div>

                <!-- Bank Name -->
                <div>
                    <label class="text-gray-700 font-semibold">Tên ngân hàng</label>
                    <input v-model="form.bank_name" class="input input-primary w-full mt-1" placeholder="Techcombank" />
                </div>

                <!-- Bank Account Number -->
                <div>
                    <label class="text-gray-700 font-semibold">Số tài khoản ngân hàng</label>
                    <input v-model="form.bank_account_number" class="input input-primary w-full mt-1"
                        placeholder="123456789" />
                </div>

                <!-- Bio (Full Width) -->
                <div class="sm:col-span-3">
                    <label class="text-gray-700 font-semibold">Tiểu sử</label>
                    <textarea v-model="form.bio" class="textarea textarea-primary w-full h-24 mt-1"
                        placeholder="Giới thiệu về bạn..." />
                </div>

                <!-- Address Section -->
                <div class="sm:col-span-3">
                    <h2 class="text-lg font-semibold mb-4">Địa chỉ</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Province -->
                        <div>
                            <label class="text-gray-700 font-semibold">Tỉnh/Thành phố</label>
                            <select v-model="form.address!.province"
                                @change="handleProvinceChange"
                                class="select select-primary w-full mt-1">
                                <option value="" disabled>Chọn tỉnh/thành phố</option>
                                <option v-for="p in provinces" :key="p.id" :value="p.id">
                                    {{ p.full_name }}
                                </option>
                            </select>
                        </div>

                        <!-- District -->
                        <div>
                            <label class="text-gray-700 font-semibold">Quận/Huyện</label>
                            <select v-model="form.address!.district"
                                @change="handleDistrictChange"
                                class="select select-primary w-full mt-1">
                                <option value="" disabled>Chọn quận/huyện</option>
                                <option v-for="d in districts" :key="d.id" :value="d.id">
                                    {{ d.full_name }}
                                </option>
                            </select>
                        </div>

                        <!-- Ward -->
                        <div>
                            <label class="text-gray-700 font-semibold">Phường/Xã</label>
                            <select v-model="form.address!.ward"
                                class="select select-primary w-full mt-1">
                                <option value="" disabled>Chọn phường/xã</option>
                                <option v-for="w in wards" :key="w.id" :value="w.id">
                                    {{ w.full_name }}
                                </option>
                            </select>
                        </div>

                        <!-- Street Address -->
                        <div>
                            <label class="text-gray-700 font-semibold">Địa chỉ chi tiết</label>
                            <input v-model="form.address!.street_address" type="text" class="input input-primary w-full mt-1"
                                placeholder="Số nhà, tên đường..." />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end mt-6">
                <button type="button" @click="$router.push('/admin/users')" class="btn btn-ghost mr-2">Hủy</button>
                <button type="submit" class="btn btn-primary" :disabled="status === 'pending'">
                    <span v-if="status === 'pending'" class="loading loading-spinner loading-md"></span>
                    <span>Cập nhật người dùng</span>
                </button>
            </div>
        </form>
    </div>
</template>

<script setup lang="ts">
definePageMeta({
    title: 'Cập nhật thông tin người dùng',
    layout: 'user',
})

import { useUserAuth } from '#imports';
import { useToast } from 'vue-toastification';
import { useRuntimeConfig } from '#app';
import { useVietnamAddress } from '#imports';
import { useUsers } from '#imports';
import type { User } from '~/types/user';

const route = useRoute();
const { provinces, districts, wards, fetchProvinces, fetchDistricts, fetchWards } = useVietnamAddress();
const { getUserByCode, updateUser } = useUsers();
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
    date_of_birth: undefined as Date | undefined,
    gender: '',
    is_super_admin: false,
    is_banned: false,
    bank_name: '',
    bank_account_number: '',
    bio: '',
    avatar_url: defaultAvatar,
    address: {
        id: 0,
        province: '',
        district: '',
        ward: '',
        street_address: ''
    }
});

const { data: userData, error } = await getUserByCode(route.params.id as string);
const user = computed<User | null>(() => Array.isArray(userData.value?.data) ? userData.value.data[0] : userData.value?.data || null)

watch(user, (newVal) => {
    if (newVal) {
        form.value = {
            full_name: newVal.full_name,
            email: newVal.email,
            phone_number: newVal.phone_number,
            date_of_birth: newVal.date_of_birth || undefined,
            gender: newVal.gender,
            is_super_admin: newVal.is_super_admin,
            is_banned: newVal.is_banned,
            bank_name: newVal.bank_name,
            bank_account_number: newVal.bank_account_number,
            bio: newVal.bio,
            avatar_url: newVal.avatar_url || defaultAvatar,
            address: {
                id: newVal.address?.id || 0,
                province: newVal.address?.province || '',
                district: newVal.address?.district || '',
                ward: newVal.address?.ward || '',
                street_address: newVal.address?.street_address || ''
            }
        }
    }
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
    if (form.value.address?.province) {
        await fetchDistricts(form.value.address.province);
        if (form.value.address) {
            form.value.address.district = '';
            form.value.address.ward = '';
        }
    }
};

// Xử lý khi chọn quận/huyện
const handleDistrictChange = async () => {
    if (form.value.address?.district) {
        await fetchWards(form.value.address.district);
        if (form.value.address) {
            form.value.address.ward = '';
        }
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
        
        // Append form fields with type checking
        if (form.value.full_name) formData.append('full_name', form.value.full_name);
        if (form.value.email) formData.append('email', form.value.email);
        if (form.value.phone_number) formData.append('phone_number', form.value.phone_number);
        if (form.value.date_of_birth) formData.append('date_of_birth', form.value.date_of_birth.toString());
        if (form.value.gender) formData.append('gender', form.value.gender);
        formData.append('is_super_admin', form.value.is_super_admin ? '1' : '0');
        formData.append('is_banned', form.value.is_banned ? '1' : '0');
        if (form.value.bank_name) formData.append('bank_name', form.value.bank_name);
        if (form.value.bank_account_number) formData.append('bank_account_number', form.value.bank_account_number);
        if (form.value.bio) formData.append('bio', form.value.bio);

        // Thêm file avatar nếu có
        if (selectedFile.value) {
            formData.append('avatar_url', selectedFile.value);
        }

        // Thêm address với type checking
        if (form.value.address) {
            if (form.value.address.province) formData.append('address[province]', form.value.address.province.toString());
            if (form.value.address.district) formData.append('address[district]', form.value.address.district.toString());
            if (form.value.address.ward) formData.append('address[ward]', form.value.address.ward.toString());
            if (form.value.address.street_address) formData.append('address[street_address]', form.value.address.street_address);
        }

        // Gửi request với FormData
        const { error } = await updateUser(Number(route.params.id), formData);

        if (error.value) {
            throw new Error(error.value.message);
        }

        toast.success('Cập nhật thông tin người dùng thành công!');
        router.push('/admin/users');
    } catch (error: any) {
        toast.error(error.message || 'Cập nhật thông tin người dùng thất bại!');
    } finally {
        status.value = 'idle';
    }
};
</script>