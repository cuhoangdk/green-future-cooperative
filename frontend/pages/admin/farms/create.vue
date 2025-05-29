<template>
    <div class="p-4">
        <form @submit.prevent="handleSubmit" class="space-y-4">
            <!-- Section 1: Farm Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Farm Name -->
                <div>
                    <label class="text-gray-700 font-semibold block mb-1">Tên nông trại <span
                            class="text-red-500">*</span></label>
                    <input v-model="form.name" class="input input-bordered input-primary w-full"
                        placeholder="Nông trại XYZ" required />
                </div>
                <!-- User Selection -->
                <div v-if="currentUser?.is_super_admin">
                    <label class="text-gray-700 font-semibold block mb-1">Thành viên <span
                            class="text-red-500">*</span></label>
                    <select v-model="form.user_id" class="select select-bordered select-primary w-full" required>
                        <option value="" disabled selected>Chọn chủ của nông trại</option>
                        <option v-for="user in users" :key="user.id" :value="user.id">
                            {{ user.full_name }}
                        </option>
                    </select>
                </div>
                <div v-else>
                    <label class="text-gray-700 font-semibold block mb-1">Thành viên <span
                            class="text-red-500">*</span></label>
                    <div class="input w-full">
                        {{ currentUser?.full_name }}

                    </div>
                </div>
            </div>

            <!-- Description -->
            <div>
                <label class="text-gray-700 font-semibold block mb-1">Mô tả</label>
                <textarea v-model="form.description" class="textarea textarea-bordered textarea-primary w-full h-24"
                    placeholder="Mô tả về nông trại..."></textarea>
            </div>

            <!-- Section 2: Farm Details -->
            <!-- <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-gray-700 font-semibold block mb-1">Kích thước (m2)</label>
                    <input v-model="form.farm_size" type="number" step="0.01"
                        class="input input-bordered input-primary w-full" placeholder="0.5" />
                </div>
                <div>
                    <label class="text-gray-700 font-semibold block mb-1">Loại đất</label>
                    <input v-model="form.soil_type" class="input input-bordered input-primary w-full"
                        placeholder="Đất cát trắng" />
                </div>
            </div> -->

            <!-- Section 3: Address -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <div class="border-t border-gray-200 pt-5">
                    <h3 class="text-lg font-medium text-gray-800 mb-3">Địa chỉ</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-gray-700 font-semibold block mb-1">Tỉnh/T.Phố</label>
                            <select v-model="form.address.province"
                                @change="(event) => fetchDistricts((event.target as HTMLSelectElement).value)"
                                class="select select-bordered select-primary w-full" required>
                                <option value="" disabled>Tỉnh/thành phố</option>
                                <option v-for="p in provinces" :key="p.id" :value="p.id">{{ p.name }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-gray-700 font-semibold block mb-1">Quận/Huyện</label>
                            <select v-model="form.address.district"
                                @change="(event) => fetchWards((event.target as HTMLSelectElement).value)"
                                class="select select-bordered select-primary w-full" required>
                                <option value="" disabled>Quận/huyện</option>
                                <option v-for="d in districts" :key="d.id" :value="d.id">{{ d.name }}</option>
                            </select>
                        </div>
                        <!-- Phần select xã/phường -->
                        <div>
                            <label class="text-gray-700 font-semibold block mb-1">Phường/Xã</label>
                            <select v-model="form.address.ward" @change="updateMapPosition"
                                class="select select-bordered select-primary w-full" required>
                                <option value="" disabled>Phường/xã</option>
                                <option v-for="w in wards" :key="w.id" :value="w.id">{{ w.name }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-gray-700 font-semibold block mb-1">Địa chỉ chi tiết</label>
                            <input v-model="form.address.street_address"
                                class="input input-bordered input-primary w-full" placeholder="Số nhà, tên đường..."
                                required />
                        </div>

                        <div>
                            <label class="text-gray-700 font-semibold block mb-1">Kinh độ</label>
                            <input v-model="form.latitude" type="number" step="0.000000001"
                                class="input input-bordered input-primary w-full" placeholder="21.0285" />
                        </div>
                        <div>
                            <label class="text-gray-700 font-semibold block mb-1">Vĩ độ</label>
                            <input v-model="form.longitude" type="number" step="0.000000001"
                                class="input input-bordered input-primary w-full" placeholder="105.8542" />
                        </div>
                    </div>
                </div>
                <div class="border-t border-gray-200 pt-5">
                    <h3 class="text-lg font-medium text-gray-800 mb-3">Vị trí</h3>
                    <div class="mb-4">
                        <LMap class="z-1" ref="map" style="height: 350px" :zoom="12"
                            :center="[form.latitude ?? 0, form.longitude ?? 0]" :use-global-leaflet="true">
                            <LTileLayer url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
                                attribution='&copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors'
                                layer-type="base" name="OpenStreetMap" />
                            <LMarker :lat-lng="[form.latitude ?? 0, form.longitude ?? 0]" draggable
                                @moveend="updateMarkerPosition" />
                        </LMap>
                    </div>
                </div>
            </div>


            <!-- Section 4: Location -->

            <!-- Submit Button -->
            <div class="border-t border-gray-200 pt-5 flex justify-between items-center">
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
    title: 'Thêm nông trại',
    layout: 'user',
})

import { useFarms } from '#imports'
import { useVietnamAddress, useUserAuth } from '#imports'
import type { User } from '~/types/user'

const { createFarm } = useFarms()
const { getUsers } = useUsers()
const { provinces, districts, wards, fetchProvinces, fetchDistricts, fetchWards } = useVietnamAddress()
const { currentUser } = useUserAuth()
const { $toast } = useNuxtApp()

const status = ref<'idle' | 'pending' | 'success' | 'error'>('idle')

// Gọi API để lấy user
const { data: usersData } = await getUsers()
const users = computed<User[]>(() =>
    Array.isArray(usersData.value?.data) ? usersData.value.data : usersData.value ? [usersData.value.data] : []
)

// Khởi tạo form với các giá trị mặc định
const form = ref({
    name: 'Nông trại của ' + currentUser.value?.full_name,
    user_id: currentUser.value?.id || '',
    description: '',
    farm_size: null as number | null,
    soil_type: '',
    irrigation_method: '',
    latitude: 9.275622 as number | null,
    longitude: 105.693978 as number | null,
    address: {
        province: '',
        district: '',
        ward: '',
        street_address: '',
    },
})
// Xử lý kéo thả marker
const updateMarkerPosition = (event: any) => {
    const latLng = event.target.getLatLng()
    form.value.latitude = Number(latLng.lat.toFixed(6))
    form.value.longitude = Number(latLng.lng.toFixed(6))
}

// Cập nhật bản đồ khi chọn xã/phường
const updateMapPosition = () => {
    const selectedWard = wards.value.find((w: any) => w.id === form.value.address.ward)
    if (selectedWard && selectedWard.latitude && selectedWard.longitude) {
        form.value.latitude = Number(selectedWard.latitude)
        form.value.longitude = Number(selectedWard.longitude)
    }
}

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

        $toast.success('Tạo nông trại thành công!')
        navigateTo('/admin/farms')
    } catch (error: any) {
        $toast.error(error.message || 'Tạo nông trại thất bại!')
    } finally {
        status.value = 'idle'
    }
}
</script>