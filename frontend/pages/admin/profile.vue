<template>
    <div class="border border-gray-200 rounded-lg p-4 sm:p-5">
        <form @submit.prevent="handleSubmit" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="flex flex-col items-center md:col-span-1">
                    <!-- Avatar Section -->
                    <div class="w-60 h-60 mb-4 cursor-pointer" @click="triggerFileInput">
                        <img :src="form.avatar_url || defaultAvatar" @error="form.avatar_url = defaultAvatar"
                            class="w-full h-full object-cover rounded-full border " alt="Avatar" />
                    </div>
                    <input ref="fileInput" type="file" accept="image/*"
                        class="file-input file-input-primary w-full max-w-xs" @change="handleFileChange" hidden />
                    <label class="text-gray-700 font-semibold mt-2">Ảnh đại diện</label>
                </div>
                <!-- Profile Details Section -->
                <div class="md:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- User Code (Read Only) -->
                    <div class="sm:col-span-2">
                        <label class="text-gray-700 font-semibold">Mã thành viên</label>
                        <div class="input input-primary w-full mt-1 flex items-center bg-gray-100">
                            {{ currentUser?.usercode }}
                        </div>
                    </div>

                    <!-- Full Name -->
                    <div>
                        <label class="text-gray-700 font-semibold">Họ và tên</label>
                        <input v-model="form.full_name" class="input input-primary w-full mt-1"
                            placeholder="Nguyen Van A" required />
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="text-gray-700 font-semibold">Email</label>
                        <input v-model="form.email" type="email" class="input input-primary w-full mt-1"
                            placeholder="example@email.com" required />
                    </div>

                    <!-- Phone Number -->
                    <div>
                        <label class="text-gray-700 font-semibold">Số điện thoại</label>
                        <input v-model="form.phone_number" type="tel" class="input input-primary w-full mt-1"
                            placeholder="0123-456-789" />
                    </div>

                    <!-- Date of Birth -->
                    <div>
                        <label class="text-gray-700 font-semibold">Ngày sinh</label>
                        <input v-model="form.date_of_birth" type="date" class="input input-primary w-full mt-1" />
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

                <!-- Gender -->
                <div>
                    <label class="text-gray-700 font-semibold">Giới tính</label>
                    <select v-model="form.gender" class="select select-primary w-full mt-1">
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
                <!-- Province -->
                <div>
                    <label class="text-gray-700 font-semibold">Tỉnh/Thành phố</label>
                    <select v-model="form.address.province"
                        @change="(event) => fetchDistricts((event.target as HTMLSelectElement).value)"
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
                    <select v-model="form.address.district"
                        @change="(event) => fetchWards((event.target as HTMLSelectElement).value)"
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
                    <select v-model="form.address.ward" class="select select-primary w-full mt-1">
                        <option value="" disabled>Chọn phường/xã</option>
                        <option v-for="w in wards" :key="w.id" :value="w.id">
                            {{ w.full_name }}
                        </option>
                    </select>
                </div>

                <!-- Street Address (Full Width) -->
                <div class="sm:col-span-3">
                    <label class="text-gray-700 font-semibold">Địa chỉ chi tiết</label>
                    <input v-model="form.address.street_address" class="input input-primary w-full mt-1"
                        placeholder="Số nhà, tên đường..." />
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end mt-6">
                <button type="submit" class="btn btn-primary" :disabled="status === 'pending'">
                    <span v-if="status === 'pending'" class="loading loading-spinner loading-md"></span>
                    <span>Cập nhật thông tin</span>
                </button>
            </div>
        </form>
    </div>
</template>

<script setup lang="ts">
definePageMeta({
    title: 'Thông tin cá nhân',
    layout: 'user',
})

import { useUserAuth } from '#imports';
import { useToast } from 'vue-toastification';
import { useRuntimeConfig } from '#app';
import { useVietnamAddress } from '#imports';
const { provinces, districts, wards, fetchProvinces, fetchDistricts, fetchWards } = useVietnamAddress();
const { currentUser, updateProfile } = useUserAuth();
const toast = useToast();

// Avatar mặc định nếu không có ảnh
const defaultAvatar = useRuntimeConfig().public.placeholderImage;

// Ref để tham chiếu đến input file và file được chọn
const fileInput = ref<HTMLInputElement | null>(null);
const selectedFile = ref<File | null>(null);
const status = ref<'idle' | 'pending' | 'success' | 'error'>('idle');

// Khởi tạo form với dữ liệu từ currentUser
const form = ref({
    full_name: currentUser.value?.full_name || '',
    email: currentUser.value?.email || '',
    phone_number: currentUser.value?.phone_number || '',
    date_of_birth: currentUser.value?.date_of_birth
        ? new Date(currentUser.value.date_of_birth).toISOString().split('T')[0]
        : '',
    gender: currentUser.value?.gender || '',
    bank_name: currentUser.value?.bank_name || '',
    bank_account_number: currentUser.value?.bank_account_number || '',
    bio: currentUser.value?.bio || '',
    avatar_url: currentUser.value?.avatar_url
        ? `${useRuntimeConfig().public.backendUrl}${currentUser.value.avatar_url}`
        : defaultAvatar,
    address: {
        province: currentUser.value?.address?.province || '',
        district: currentUser.value?.address?.district || '',
        ward: currentUser.value?.address?.ward || '',
        street_address: currentUser.value?.address?.street_address || ''
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

await fetchProvinces();
if (form.value.address.province) {
    await fetchDistricts(form.value.address.province);
}
if (form.value.address.district) {
    await fetchWards(form.value.address.district);
}

// Xử lý submit form
const handleSubmit = async () => {
    try {
        status.value = 'pending';
        // Tạo FormData để gửi dữ liệu multipart
        const formData = new FormData();
        formData.append('full_name', form.value.full_name);
        formData.append('email', form.value.email);
        formData.append('phone_number', form.value.phone_number || '');
        formData.append('date_of_birth', form.value.date_of_birth || '');
        formData.append('gender', form.value.gender || '');
        formData.append('bank_name', form.value.bank_name || '');
        formData.append('bank_account_number', form.value.bank_account_number || '');
        formData.append('bio', form.value.bio || '');

        // Thêm file avatar nếu có
        if (selectedFile.value) {
            formData.append('avatar_url', selectedFile.value);
        }

        // Thêm address
        formData.append('address[province]', form.value.address.province || '');
        formData.append('address[district]', form.value.address.district || '');
        formData.append('address[ward]', form.value.address.ward || '');
        formData.append('address[street_address]', form.value.address.street_address || '');

        // Gửi request với FormData
        const result = await updateProfile(formData);

        if (result.success) {
            toast.success('Cập nhật thông tin thành công!');
            // Cập nhật lại avatar_url từ server nếu cần
            if (result.user?.avatar_url) {
                form.value.avatar_url = `${useRuntimeConfig().public.backendUrl}${result.user.avatar_url}`;
            }
        }
    } catch (error: any) {
        toast.error(error.message || 'Cập nhật thông tin thất bại!');
    } finally {
        status.value = 'idle';
    }
};
</script>
