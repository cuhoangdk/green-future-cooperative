<template>
    <div class="min-h-screen items-center flex flex-col mt-16 p-2 lg:mt-0">
        <div class="w-full lg:w-8/12 bg-white border border-gray-200 rounded-2xl p-4 sm:p-5">
            <form @submit.prevent="handleSubmit" class="space-y-6">
                <div class="border-gray-200">
                    <h3 class="text-lg font-medium text-gray-800 mb-3">Thông tin địa chỉ</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <!-- Full Name -->
                        <div class="sm:col-span-3">
                            <label class="text-gray-700 font-semibold block mb-1">Họ và tên người nhận</label>
                            <input v-model="form.full_name" class="input input-bordered input-primary w-full" placeholder="Nhập họ và tên" required/>
                        </div>

                        <!-- Phone Number -->
                        <div class="sm:col-span-3">
                            <label class="text-gray-700 font-semibold block mb-1">Số điện thoại người nhận</label>
                            <input v-model="form.phone_number" type="tel" class="input input-bordered input-primary w-full" placeholder="Nhập số điện thoại" required/>
                        </div>

                        <!-- Address Type -->
                        <div class="sm:col-span-3">
                            <label class="text-gray-700 font-semibold block mb-1">Loại địa chỉ</label>
                            <select v-model="form.address_type" class="select select-bordered select-primary w-full" required>
                                <option value="" disabled>Chọn loại địa chỉ</option>
                                <option value="home">Nhà riêng</option>
                                <option value="work">Nơi làm việc</option>
                            </select>
                        </div>
                        <!-- Province -->
                        <div>
                            <label class="text-gray-700 font-semibold block mb-1">Tỉnh/Thành phố</label>
                            <select v-model="form.province" @change="handleProvinceChange"
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
                            <select v-model="form.district" @change="handleDistrictChange"
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
                            <select v-model="form.ward" class="select select-bordered select-primary w-full" required>
                                <option value="" disabled>Chọn phường/xã</option>
                                <option v-for="w in wards" :key="w.id" :value="w.id">
                                    {{ w.full_name }}
                                </option>
                            </select>
                        </div>

                        <!-- Street Address (Full Width) -->
                        <div class="sm:col-span-3">
                            <label class="text-gray-700 font-semibold block mb-1">Địa chỉ chi tiết</label>
                            <input v-model="form.street_address" class="input input-bordered input-primary w-full"
                                placeholder="Số nhà, tên đường..." required/>
                        </div>
                    </div>
                </div>
                            <!-- Submit Button -->
            <div class="border-t border-gray-200 pt-5 flex justify-end">
                <button type="button" @click="$router.push('/account')" class="btn btn-ghost mr-2">Hủy</button>
                <button type="submit" class="btn btn-primary px-6" :disabled="status === 'pending'">
                    <span v-if="status === 'pending'" class="loading loading-spinner loading-md"></span>
                    Thêm địa chỉ
                </button>
            </div>
            </form>
        </div>
    </div>
</template>

<script setup lang="ts">
import { useToast } from 'vue-toastification';

const { provinces, districts, wards, fetchProvinces, fetchDistricts, fetchWards } = useVietnamAddress();
const { addCustomerAddress } = useCustomerAddress();
const { currentCustomer } = useCustomerAuth();
const toast = useToast();
const router = useRouter();
const status = ref<'idle' | 'pending' | 'success' | 'error'>('idle');

// Khởi tạo form với dữ liệu mặc định
const form = ref({
    full_name: currentCustomer.value?.full_name || '',
    phone_number: currentCustomer.value?.phone_number || '',
    address_type: '',
    province: '',
    district: '',
    ward: '',
    street_address: ''
});

// Lấy danh sách tỉnh/thành phố khi component được tạo
await fetchProvinces();

// Xử lý khi chọn tỉnh/thành phố
const handleProvinceChange = async () => {
    if (form.value.province) {
        await fetchDistricts(form.value.province);
        form.value.district = '';
        form.value.ward = '';
    }
};

// Xử lý khi chọn quận/huyện
const handleDistrictChange = async () => {
    if (form.value.district) {
        await fetchWards(form.value.district);
        form.value.ward = '';
    }
};

// Xử lý submit form
const handleSubmit = async () => {
    try {
        status.value = 'pending';
        // Tạo FormData để gửi dữ liệu multipart
        const formData = new FormData();
        formData.append('full_name', form.value.full_name);
        formData.append('phone_number', form.value.phone_number);
        formData.append('address_type', form.value.address_type);
        formData.append('address[province]', form.value.province);
        formData.append('address[district]', form.value.district);
        formData.append('address[ward]', form.value.ward);
        formData.append('address[street_address]', form.value.street_address);

        const { error } = await addCustomerAddress(formData);
        
        if (error.value) {
            throw new Error(error.value.message);
        }

        toast.success('Thêm địa chỉ thành công!');
        router.push('/account');
    } catch (error: any) {
        toast.error(error.message || 'Thêm địa chỉ thất bại!');
    } finally {
        status.value = 'idle';
    }
};
</script>