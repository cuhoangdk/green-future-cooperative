<template>
    <div class="p-4">
        <div v-if="status === 'pending'" class="flex justify-center items-center h-screen">
            <span class="loading loading-spinner loading-lg"></span>
        </div>
        <form v-else @submit.prevent="handleSubmit" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <!-- Avatar Section -->
                <div class="flex flex-col items-center justify-between md:col-span-1">
                    <div class="w-48 h-48 mb-3 cursor-pointer" @click="triggerFileInput">
                        <img :src="form.avatar_url || defaultAvatar" @error="form.avatar_url = defaultAvatar"
                            class="w-full h-full object-cover rounded-full border shadow-sm" alt="Avatar" />
                        <div class="text-center text-gray-700 font-semibold mt-2">Ảnh đại diện</div>
                    </div>

                    <input ref="fileInput" type="file" accept="image/*"
                        class="file-input file-input-primary w-full max-w-xs" @change="handleFileChange" hidden />
                </div>

                <div class="md:col-span-3 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- User Code - Luôn hiển thị đầu tiên -->
                    <div class="sm:col-span-2">
                        <label class="text-gray-700 font-semibold block mb-1">Mã khách hàng</label>
                        <label class="input w-full bg-gray-200">
                            <span class="label">KH</span>
                            <div class="">
                                {{ route.params.id }}
                            </div>
                        </label>
                    </div>

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
                        <div class="flex items-center space-x-4">
                            <label class="flex items-center">
                                <input type="radio" v-model="form.gender" value="male" class="radio radio-primary" />
                                <span class="ml-2">Nam</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" v-model="form.gender" value="female" class="radio radio-primary" />
                                <span class="ml-2">Nữ</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" v-model="form.gender" value="other" class="radio radio-primary" />
                                <span class="ml-2">Khác</span>
                            </label>
                        </div>
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

            <div class="border-t border-gray-200 pt-5">
                <h3 class="text-lg font-medium text-gray-800 mb-3">Thông tin địa chỉ</h3>
                <div v-for="address in customer?.addresses" :key="address.id">
                    <UiAddressCard :address="address" :addressDetailId="address.address.ward" />
                </div>
            </div>

            <div class="border-t border-gray-200 pt-5 flex justify-between items-center">
                <UiButtonBack />
                <div>
                    <div type="button" @click="$router.push(`/admin/customers/${id}/change-password`)" class="btn mr-2">
                        Đổi mật khẩu
                    </div>
                    <button type="submit" class="btn btn-primary px-6" :disabled="submit === 'pending'">
                        <span v-if="submit === 'pending'" class="loading loading-spinner loading-md"></span>
                        Lưu
                    </button>
                    <button v-if="customer?.verified_at === null" type="button" @click="handleVerify"
                        class="btn btn-secondary px-6 ml-2" :disabled="submit === 'pending'">
                        <span v-if="submit === 'pending'" class="loading loading-spinner loading-md"></span>
                        Xác thực
                    </button>
                </div>
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
import type { Customer } from '~/types/customer';

const route = useRoute();
const { getFullAddressName } = useVietnamAddress();
const { getCustomerById, updateCustomer } = useCustomer();
const { $toast } = useNuxtApp()
const router = useRouter();

// Avatar mặc định nếu không có ảnh
const defaultAvatar = useRuntimeConfig().public.placeholderImage;
const backEndUrl = useRuntimeConfig().public.backendUrl;

// Ref để tham chiếu đến input file và file được chọn
const fileInput = ref<HTMLInputElement | null>(null);
const selectedFile = ref<File | null>(null);
const submit = ref<'idle' | 'pending' | 'success' | 'error'>('idle');
const id = Number(route.params.id);
const { data: customerData, status, refresh } = await getCustomerById(id);
const customer = computed<Customer | null>(() => Array.isArray(customerData.value?.data) ? customerData.value.data[0] : customerData.value?.data || null)

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

// Lấy tên địa chỉ đầy đủ từ wardId
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
        submit.value = 'pending';
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

        $toast.success('Cập nhật thông tin khách hàng thành công!');
        refresh();
        router.push('/admin/customers');
    } catch (error: any) {
        $toast.error(error.message || 'Cập nhật thông tin khách hàng thất bại!');
    } finally {
        submit.value = 'idle';
    }
};

const handleVerify = async () => {
    try {
        submit.value = 'pending';
        // Tạo FormData để gửi dữ liệu multipart
        const formData = new FormData();
        // Gửi request với FormData
        const { error } = await updateCustomer(
            Number(customer?.value?.id),
            formData
        );
        if (error.value) {
            throw new Error(error.value.message);
        }
        $toast.success('Cập nhật thông tin khách hàng thành công!');
        refresh();
        router.push('/admin/customers');
    } catch (error: any) {
        $toast.error(error.message || 'Cập nhật thông tin khách hàng thất bại!');
    } finally {
        submit.value = 'idle';
    }
};
</script>
