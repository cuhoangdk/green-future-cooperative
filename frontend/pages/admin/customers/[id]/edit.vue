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
                    <!-- User Code - Luôn hiển thị đầu tiên -->
                    <div class="sm:col-span-2">
                        <label class="text-gray-700 font-semibold block mb-1">Mã khách hàng</label>
                        <div class="input input-bordered w-full flex items-center bg-gray-100 font-medium">
                            {{ route.params.id }}
                        </div>
                    </div>

                    <!-- Thông tin nhận dạng -->
                    <div>
                        <label class="text-gray-700 font-semibold block mb-1">Họ và tên <span
                                class="text-red-500">*</span></label>
                        <input v-model="form.full_name" class="input input-bordered input-primary w-full"
                            placeholder="Nguyen Van A" required />
                    </div>

                    <div>
                        <label class="text-gray-700 font-semibold block mb-1">Email <span
                                class="text-red-500">*</span></label>
                        <input v-model="form.email" type="email" class="input input-bordered input-primary w-full"
                            placeholder="example@email.com" required />
                    </div>

                    <div>
                        <label class="text-gray-700 font-semibold block mb-1">Số điện thoại <span
                                class="text-red-500">*</span></label>
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
                        <input v-model="form.date_of_birth" type="date"
                            class="input input-bordered input-primary w-full" />
                    </div>

                    <div>
                        <label class="text-gray-700 font-semibold block mb-1">Đăng ký nhận tin</label>
                        <select v-model="form.newsletter_subscribed"
                            class="select select-bordered select-primary w-full">
                            <option :value="true">Có</option>
                            <option :value="false">Không</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-gray-700 font-semibold block mb-1">Trạng thái</label>
                        <select v-model="form.is_banned" class="select select-bordered select-primary w-full">
                            <option :value="false">Hoạt động</option>
                            <option :value="true">Đã bị cấm</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Phần 2: Thông tin địa chỉ -->
            <div class="border-t border-gray-200 pt-5">
                <h3 class="text-lg font-medium text-gray-800 mb-3">Thông tin địa chỉ</h3>
                <div v-for="address in customer?.addresses" :key="address.id">
                        <UiAddressCard :address="address" :addressDetailId="address.address.ward" />
                    </div>
            </div>

            <!-- Submit Button -->
            <div class="border-t border-gray-200 pt-5 flex justify-end">
                <button type="button" @click="$router.push('/admin/customers')" class="btn btn-ghost mr-2">Hủy</button>
                <button type="submit" class="btn btn-primary px-6" :disabled="status === 'pending'">
                    <span v-if="status === 'pending'" class="loading loading-spinner loading-md"></span>
                    Cập nhật khách hàng
                </button>
            </div>
        </form>
    </div>
</template>

<script setup lang="ts">
definePageMeta({
    title: 'Cập nhật khách hàng',
    layout: 'user',
})

import { useToast } from 'vue-toastification';
import { useRuntimeConfig } from '#app';
import { useVietnamAddress } from '#imports';
import { useCustomer } from '#imports';
import type { Customer } from '~/types/customer';

const route = useRoute();
const { provinces, districts, wards, fetchProvinces, fetchDistricts, fetchWards, getFullAddressName } = useVietnamAddress();
const { getCustomerById, updateCustomer } = useCustomer();
const toast = useToast();
const router = useRouter();

// Avatar mặc định nếu không có ảnh
const defaultAvatar = useRuntimeConfig().public.placeholderImage;
const backEndUrl = useRuntimeConfig().public.backendUrl;

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
    is_banned: false,
    avatar_url: defaultAvatar,
    address: {
        province: '',
        district: '',
        ward: '',
        street_address: ''
    }
});

const { data: customerData, refresh } = await getCustomerById(Number(route.params.id));
const customer = computed<Customer | null>(() => Array.isArray(customerData.value?.data) ? customerData.value.data[0] : customerData.value?.data || null)

watch(customer, (newVal) => {
    if (newVal) {
        form.value = {
            full_name: newVal.full_name,
            email: newVal.email,
            phone_number: newVal.phone_number || '',
            date_of_birth: newVal.date_of_birth ? new Date(newVal.date_of_birth).toISOString().split('T')[0] : '',
            gender: newVal.gender || '',
            newsletter_subscribed: newVal.newsletter_subscribed,
            is_banned: newVal.is_banned,
            avatar_url: newVal.avatar_url ? backEndUrl + newVal.avatar_url : defaultAvatar,
            address: {
                province: newVal.addresses?.[0]?.address?.province || '',
                district: newVal.addresses?.[0]?.address?.district || '',
                ward: newVal.addresses?.[0]?.address?.ward || '',
                street_address: newVal.addresses?.[0]?.address?.street_address || ''
            }
        }
    }
});

const addressData = ref<{ [key: number]: string }>({})
watch(() => customer.value, async (newCustomer) => {
    if (newCustomer && newCustomer.addresses) {
        for (const customerAddress of newCustomer.addresses) {
            if (customerAddress.address?.ward) {
                addressData.value[customerAddress.id] = await getFullAddressName(customerAddress.address.ward)
            }
        }
    }
}, { immediate: true })

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

        // Append form fields with type checking
        if (form.value.full_name) formData.append('full_name', form.value.full_name);
        if (form.value.email) formData.append('email', form.value.email);
        if (form.value.phone_number) formData.append('phone_number', form.value.phone_number);
        if (form.value.date_of_birth) formData.append('date_of_birth', form.value.date_of_birth.toString());
        if (form.value.gender) formData.append('gender', form.value.gender);
        formData.append('newsletter_subscribed', form.value.newsletter_subscribed ? '1' : '0');
        formData.append('is_banned', form.value.is_banned ? '1' : '0');

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
        const { error } = await updateCustomer(
            Number(customer?.value?.id),
            formData
        );

        if (error.value) {
            throw new Error(error.value.message);
        }

        toast.success('Cập nhật thông tin khách hàng thành công!');
        refresh();
        router.push('/admin/customers');
    } catch (error: any) {
        toast.error(error.message || 'Cập nhật thông tin khách hàng thất bại!');
    } finally {
        status.value = 'idle';
    }
};
</script>
