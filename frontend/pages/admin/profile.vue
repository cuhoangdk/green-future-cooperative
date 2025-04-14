<template>
    <div class="p-4">
        <form @submit.prevent="handleSubmit" class="space-y-4">
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
                    <!-- User Code - Luôn hiển thị đầu tiên -->
                    <div class="sm:col-span-2">
                        <label class="text-gray-700 font-semibold block mb-1">Mã thành viên</label>
                        <div class="input input-bordered w-full flex items-center bg-gray-100 font-medium">
                            {{ currentUser?.usercode }}
                        </div>
                    </div>

                    <!-- Thông tin nhận dạng -->
                    <div>
                        <label class="text-gray-700 font-semibold block mb-1">Họ và tên</label>
                        <input v-model="form.full_name" class="input input-bordered input-primary w-full"
                            placeholder="Nguyen Van A" required />
                    </div>

                    <div>
                        <label class="text-gray-700 font-semibold block mb-1">Email</label>
                        <input v-model="form.email" type="email" class="input input-bordered input-primary w-full"
                            placeholder="example@email.com" required />
                    </div>

                    <div>
                        <label class="text-gray-700 font-semibold block mb-1">Số điện thoại</label>
                        <input v-model="form.phone_number" type="tel" class="input input-bordered input-primary w-full"
                            placeholder="0123-456-789" />
                    </div>

                    <div>
                        <label class="text-gray-700 font-semibold block mb-1">Giới tính</label>
                        <select v-model="form.gender" class="select select-bordered select-primary w-full">
                            <option value="male">Nam</option>
                            <option value="female">Nữ</option>
                            <option value="other">Khác</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-gray-700 font-semibold block mb-1">Ngày sinh</label>
                        <input v-model="form.date_of_birth" type="date" class="input input-bordered input-primary w-full" />
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
                            @change="(event) => fetchDistricts((event.target as HTMLSelectElement).value)"
                            class="select select-bordered select-primary w-full">
                            <option value="" disabled>Chọn tỉnh/thành phố</option>
                            <option v-for="p in provinces" :key="p.id" :value="p.id">
                                {{ p.full_name }}
                            </option>
                        </select>
                    </div>

                    <!-- District -->
                    <div>
                        <label class="text-gray-700 font-semibold block mb-1">Quận/Huyện</label>
                        <select v-model="form.address.district"
                            @change="(event) => fetchWards((event.target as HTMLSelectElement).value)"
                            class="select select-bordered select-primary w-full">
                            <option value="" disabled>Chọn quận/huyện</option>
                            <option v-for="d in districts" :key="d.id" :value="d.id">
                                {{ d.full_name }}
                            </option>
                        </select>
                    </div>

                    <!-- Ward -->
                    <div>
                        <label class="text-gray-700 font-semibold block mb-1">Phường/Xã</label>
                        <select v-model="form.address.ward" class="select select-bordered select-primary w-full">
                            <option value="" disabled>Chọn phường/xã</option>
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
            <div class="border-t border-gray-200 pt-5 flex justify-end">
                <button type="submit" class="btn btn-primary px-6" :disabled="status === 'pending'">
                    <span v-if="status === 'pending'" class="loading loading-spinner loading-md mr-2"></span>
                    Cập nhật thông tin
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

const { provinces, districts, wards, fetchProvinces, fetchDistricts, fetchWards } = useVietnamAddress();
const { currentUser, updateProfile, isAuthenticated } = useUserAuth();
const { $toast } = useNuxtApp();

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
if (currentUser.value?.address.province) {
    await fetchDistricts(currentUser.value?.address.province);
}
if (currentUser.value?.address.district) {
    await fetchWards(currentUser.value?.address.district);
}

// Xử lý submit form
const handleSubmit = async () => {
    try {
        status.value = 'pending';
        // Tạo FormData để gửi dữ liệu multipart
        const formData = new FormData();
        if (form.value.full_name) formData.append('full_name', form.value.full_name);
        if (form.value.email) formData.append('email', form.value.email);
        if (form.value.phone_number) formData.append('phone_number', form.value.phone_number);
        if (form.value.date_of_birth) formData.append('date_of_birth', form.value.date_of_birth);
        if (form.value.gender) formData.append('gender', form.value.gender);
        if (form.value.bank_name) formData.append('bank_name', form.value.bank_name);
        if (form.value.bank_account_number) formData.append('bank_account_number', form.value.bank_account_number);
        if (form.value.bio) formData.append('bio', form.value.bio);

        // Thêm file avatar nếu có
        if (selectedFile.value) {
            formData.append('avatar_url', selectedFile.value);
        }

        // Thêm address nếu có
        if (form.value.address.province) formData.append('address[province]', form.value.address.province);
        if (form.value.address.district) formData.append('address[district]', form.value.address.district);
        if (form.value.address.ward) formData.append('address[ward]', form.value.address.ward);
        if (form.value.address.street_address) formData.append('address[street_address]', form.value.address.street_address);

        // Gửi request với FormData
        const result = await updateProfile(formData);

        if (result.success) {
            $toast.success('Cập nhật thông tin thành công!');
            // Cập nhật lại avatar_url từ server nếu cần
            if (result.user?.avatar_url) {
                form.value.avatar_url = `${useRuntimeConfig().public.backendUrl}${result.user.avatar_url}`;
            }
        }
    } catch (error: any) {
        $toast.error(error.message || 'Cập nhật thông tin thất bại!');
    } finally {
        status.value = 'idle';
    }
};
</script>
