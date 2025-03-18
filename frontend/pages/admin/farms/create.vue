<template>
    <div class="border border-gray-200 rounded-lg p-5">
        <form @submit.prevent="handleSubmit" class="space-y-2">
            <div class="divider divider-start text-xl font-bold">Thông tin nông trại</div>
            <div class="flex space-x-4">
                <!-- Farm Name -->
                <div :class="currentUser?.is_super_admin ? 'w-1/2' : 'w-full'">
                    <label class="text-gray-700 font-semibold">Tên nông trại</label>
                    <input v-model="form.name" class="input input-primary w-full mt-1" placeholder="Nông trại XYZ"
                        required />
                </div>
                <!-- Danh mục -->
                <div class="w-1/2"  v-if="currentUser?.is_super_admin">
                    <label class="text-gray-700 font-semibold">Thành viên</label>
                    <select v-model="form.user_id" class="select select-primary w-full mt-1" required>
                        <option  value=""  disabled selected>Chọn chủ của nông trại</option>
                        <option v-for="user in users" :key="user.id" :value="user.id">
                            {{ user.full_name }}
                        </option>
                    </select>
                </div>
            </div>

            <!-- Description -->
            <div class="col-span-3">
                <label class="text-gray-700 font-semibold">Mô tả</label>
                <textarea v-model="form.description" class="textarea textarea-primary w-full h-24 mt-1"
                    placeholder="Mô tả về nông trại..." />
            </div>

            <!-- Farm Details -->
            <div class="flex space-x-4">
                <div class="w-1/2">
                    <label class="text-gray-700 font-semibold">Kích thước (ha)</label>
                    <input v-model="form.farm_size" type="number" step="0.01" class="input input-primary w-full mt-1"
                        placeholder="0.5" />
                </div>
                <div class="w-1/2">
                    <label class="text-gray-700 font-semibold">Loại đất</label>
                    <input v-model="form.soil_type" class="input input-primary w-full mt-1"
                        placeholder="Đất cát trắng" />
                </div>
            </div>
            <div>
                <label class="text-gray-700 font-semibold">Phương pháp canh tác</label>
                <input v-model="form.irrigation_method" class="input input-primary w-full mt-1"
                    placeholder="Không sử dụng thuốc bảo vệ thực vật hóa học" />
            </div>

            <!-- Address Section -->
            <div class="divider divider-start text-xl font-bold py-3 pt-7">Địa chỉ</div>
            <div class="flex space-x-4">
                <div class="w-1/3">
                    <label class="text-gray-700 font-semibold">Tỉnh/T.Phố</label>
                    <select v-model="form.address.province"
                        @change="(event) => fetchDistricts((event.target as HTMLSelectElement).value)"
                        class="select select-primary w-full mt-1" required>
                        <option value="" disabled>Chọn tỉnh/thành phố</option>
                        <option v-for="p in provinces" :key="p.id" :value="p.id">{{ p.name }}</option>
                    </select>
                </div>
                <div class="w-1/3">
                    <label class="text-gray-700 font-semibold">Quận/Huyện</label>
                    <select v-model="form.address.district"
                        @change="(event) => fetchWards((event.target as HTMLSelectElement).value)"
                        class="select select-primary w-full mt-1" required>
                        <option value="" disabled>Chọn quận/huyện</option>
                        <option v-for="d in districts" :key="d.id" :value="d.id">{{ d.name }}</option>
                    </select>
                </div>
                <div class="w-1/3">
                    <label class="text-gray-700 font-semibold">Phường/Xã</label>
                    <select v-model="form.address.ward" class="select select-primary w-full mt-1" required>
                        <option value="" disabled>Chọn phường/xã</option>
                        <option v-for="w in wards" :key="w.id" :value="w.id">{{ w.name }}</option>
                    </select>
                </div>
            </div>
            <div class="col-span-3">
                <label class="text-gray-700 font-semibold">Địa chỉ chi tiết</label>
                <input v-model="form.address.street_address" class="input input-primary w-full mt-1"
                    placeholder="Số nhà, tên đường..." />
            </div>

            <!-- Location -->
            <div class="flex space-x-4">
                <div class="w-1/2">
                    <label class="text-gray-700 font-semibold">Kinh độ</label>
                    <input v-model="form.latitude" type="number" step="0.000001" class="input input-primary w-full mt-1"
                        placeholder="21.0285" />
                </div>
                <div class="w-1/2">
                    <label class="text-gray-700 font-semibold">Vĩ độ</label>
                    <input v-model="form.longitude" type="number" step="0.000001"
                        class="input input-primary w-full mt-1" placeholder="105.8542" />
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end">
                <button type="submit" class="btn btn-primary" :disabled="status === 'pending'">
                    <span v-if="status === 'pending'" class="loading loading-spinner loading-md"></span>
                    <span>Tạo nông trại</span>
                </button>
            </div>
        </form>
    </div>
</template>



<script setup lang="ts">
definePageMeta({
    title: 'Thêm nông trại',
    layout: 'user',
})

import { useFarms } from '#imports'
import { useToast } from 'vue-toastification'
import { useVietnamAddress, useUserAuth } from '#imports'
import type { User } from '~/types/user'

const { createFarm } = useFarms()
const { getUsers } = useUsers()
const { provinces, districts, wards, fetchProvinces, fetchDistricts, fetchWards } = useVietnamAddress()
const { currentUser } = useUserAuth()
const toast = useToast()

const status = ref<'idle' | 'pending' | 'success' | 'error'>('idle')

// Gọi API để lấy user
const { data: usersData } = await getUsers()
const users = computed<User[]>(() =>
    Array.isArray(usersData.value?.data) ? usersData.value.data : usersData.value ? [usersData.value.data] : []
)

// Khởi tạo form với các giá trị mặc định
const form = ref({
    name: '',
    user_id: currentUser.value?.id || '',
    description: '',
    farm_size: null as number | null,
    soil_type: '',
    irrigation_method: '',
    latitude: null as number | null,
    longitude: null as number | null,
    address: {
        province: '',
        district: '',
        ward: '',
        street_address: '',
    },
})

// Tải danh sách tỉnh/thành phố khi khởi tạo
await fetchProvinces()

// Xử lý submit form
const handleSubmit = async () => {
    try {
        status.value = 'pending'

        // Tạo FormData để gửi dữ liệu
        const formData = new FormData()
        formData.append('name', form.value.name)
        formData.append('user_id', currentUser.value?.is_super_admin ? form.value.user_id?.toString() || '' : currentUser.value?.id?.toString() || '')
        formData.append('description', form.value.description || '')
        if (form.value.farm_size) formData.append('farm_size', form.value.farm_size.toString())
        formData.append('soil_type', form.value.soil_type || '')
        formData.append('irrigation_method', form.value.irrigation_method || '')
        if (form.value.latitude) formData.append('latitude', form.value.latitude.toString())
        if (form.value.longitude) formData.append('longitude', form.value.longitude.toString())
        formData.append('address[province]', form.value.address.province || '')
        formData.append('address[district]', form.value.address.district || '')
        formData.append('address[ward]', form.value.address.ward || '')
        formData.append('address[street_address]', form.value.address.street_address || '')


        // Gửi request tạo nông trại
        const { error } = await createFarm(formData)

        if (error.value) throw new Error(error.value.message || 'Tạo nông trại thất bại')

        toast.success('Tạo nông trại thành công!')
        // Có thể redirect hoặc reset form sau khi tạo thành công
        form.value = {
            name: '',
            user_id: '',
            description: '',
            farm_size: null,
            soil_type: '',
            irrigation_method: '',
            latitude: null,
            longitude: null,
            address: { province: '', district: '', ward: '', street_address: '' },
        }
    } catch (error: any) {
        toast.error(error.message || 'Tạo nông trại thất bại!')
    } finally {
        status.value = 'idle'
    }
}
</script>