<template>
  <div class="border border-gray-200 rounded-lg p-5">
    <form @submit.prevent="handleSubmit" class="space-y-2">
      <!-- Tiêu đề -->
      <div class="divider divider-start text-xl font-bold">Thông tin nông trại</div>
      <div class="flex space-x-4">
        <div class="w-1/2">
          <label class="text-gray-700 font-semibold">Mã nông trại</label>
          <input v-model="form.id" class="input input-primary w-full mt-1" disabled />
        </div>
        <div class="w-1/2">
          <label class="text-gray-700 font-semibold">Chủ sở hữu</label>
          <input v-model="form.user" class="input input-primary w-full mt-1" disabled />
        </div>
      </div>

      <!-- Tên nông trại -->
      <div>
        <label class="text-gray-700 font-semibold">Tên nông trại</label>
        <input v-model="form.name" class="input input-primary w-full mt-1" placeholder="Nông trại XYZ" required />
      </div>

      <!-- Mô tả -->
      <div>
        <label class="text-gray-700 font-semibold">Mô tả</label>
        <textarea v-model="form.description" class="textarea textarea-primary h-24 w-full mt-1"
          placeholder="Mô tả về nông trại..." />
      </div>

      <!-- Chi tiết nông trại -->
      <div class="flex space-x-4">
        <div class="w-1/2">
          <label class="text-gray-700 font-semibold">Kích thước (ha)</label>
          <input v-model="form.farm_size" type="number" step="0.01" class="input input-primary w-full mt-1"
            placeholder="0.5" />
        </div>
        <div class="w-1/2">
          <label class="text-gray-700 font-semibold">Loại đất</label>
          <input v-model="form.soil_type" class="input input-primary w-full mt-1" placeholder="Đất cát trắng" />
        </div>
      </div>
      <div class="w-full">
        <label class="text-gray-700 font-semibold">Phương pháp canh tác</label>
        <textarea v-model="form.irrigation_method" class="textarea textarea-primary h-24 w-full mt-1"
          placeholder="Tưới nhỏ giọt"></textarea>
      </div>


      <!-- Địa chỉ -->
      <div class="divider divider-start text-xl font-bold py-3 pt-7">Địa chỉ</div>
      <div class="flex space-x-4">
        <div class="w-1/3">
          <label class="text-gray-700 font-semibold">Tỉnh/Thành phố</label>
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
      <div>
        <label class="text-gray-700 font-semibold">Địa chỉ chi tiết</label>
        <input v-model="form.address.street_address" class="input input-primary w-full mt-1"
          placeholder="Số nhà, tên đường..." />
      </div>

      <!-- Vị trí -->
      <div class="flex space-x-4">
        <div class="w-1/2">
          <label class="text-gray-700 font-semibold">Kinh độ</label>
          <input v-model="form.latitude" type="number" step="0.000001" class="input input-primary w-full mt-1"
            placeholder="21.0285" />
        </div>
        <div class="w-1/2">
          <label class="text-gray-700 font-semibold">Vĩ độ</label>
          <input v-model="form.longitude" type="number" step="0.000001" class="input input-primary w-full mt-1"
            placeholder="105.8542" />
        </div>
      </div>
      <!-- Submit -->
      <div class="flex justify-end mt-6">
        <button type="submit" class="btn btn-primary mt-1" :disabled="submitStatus === 'pending'">
          <span v-if="submitStatus === 'pending'" class="loading loading-spinner loading-md"></span>
          <span>Cập nhật</span>
        </button>
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'user', title: 'Chỉnh sửa nông trại' })

import { useFarms } from '~/composables/useFarms'
import { useVietnamAddress, useUserAuth } from '#imports'
import { useToast } from 'vue-toastification'
import type { Farm } from '~/types/farm'

const route = useRoute()
const router = useRouter()
const toast = useToast()
const { currentUser } = useUserAuth()
const { getFarmById, updateFarm } = useFarms()
const { provinces, districts, wards, fetchProvinces, fetchDistricts, fetchWards } = useVietnamAddress()

// Lấy ID từ route params
const farmId = route.params.id

// Lấy dữ liệu nông trại hiện tại
const { data: farmData, error: farmError, refresh: farmRefresh } = await getFarmById(Number(farmId))
const farm = computed<Farm | null>(() =>
  Array.isArray(farmData.value?.data) ? farmData.value.data[0] : farmData.value?.data || null
)

if (farmError.value) {
  toast.error('Không thể tải dữ liệu nông trại!')
  router.push('/admin/farms')
}

// Khởi tạo form
const form = ref({
  id: '',
  user_id: '',
  user: '',
  name: '',
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

// Hàm điền dữ liệu từ nông trại hiện tại
const fillFormData = async () => {
  await fetchProvinces()
  if (farm.value?.address.province) await fetchDistricts(farm.value.address.province)
  if (farm.value?.address.district) await fetchWards(farm.value.address.district)

  form.value = {
    id: farm.value?.id.toString() || '',
    user_id: farm.value?.user_id || '',
    user: farm.value?.user?.full_name || '',
    name: farm.value?.name || '',
    description: farm.value?.description || '',
    farm_size: farm.value?.farm_size || null,
    soil_type: farm.value?.soil_type || '',
    irrigation_method: farm.value?.irrigation_method || '',
    latitude: farm.value?.latitude || null,
    longitude: farm.value?.longitude || null,
    address: {
      province: farm.value?.address.province || '',
      district: farm.value?.address.district || '',
      ward: farm.value?.address.ward || '',
      street_address: farm.value?.address.street_address || '',
    },
  }
}

// Gọi hàm điền dữ liệu khi farm sẵn sàng
watch(
  () => farm.value?.id,
  async (farmId) => {
    if (farmId) {
      await fillFormData()
    }
  },
  { immediate: true }
)

const submitStatus = ref<'idle' | 'pending' | 'success' | 'error'>('idle')

// Submit form
const handleSubmit = async () => {
  submitStatus.value = 'pending'
  const formData = new FormData()
  formData.append('name', form.value.name)
  formData.append('user_id', currentUser.value?.is_super_admin ? form.value.user_id || '' : currentUser.value?.id?.toString() || '')
  formData.append('description', form.value.description || '')
  if (form.value.farm_size !== null) formData.append('farm_size', form.value.farm_size.toString())
  formData.append('soil_type', form.value.soil_type || '')
  formData.append('irrigation_method', form.value.irrigation_method || '')
  if (form.value.latitude !== null) formData.append('latitude', form.value.latitude.toString())
  if (form.value.longitude !== null) formData.append('longitude', form.value.longitude.toString())
  formData.append('address[province]', form.value.address.province || '')
  formData.append('address[district]', form.value.address.district || '')
  formData.append('address[ward]', form.value.address.ward || '')
  formData.append('address[street_address]', form.value.address.street_address || '')

  try {
    const { error, status, refresh } = await updateFarm(Number(farmId), formData)
    if (error.value) throw new Error(error.value.message || 'Cập nhật nông trại thất bại')
    submitStatus.value = status.value
    if (status.value === 'success') {
      toast.success('Cập nhật nông trại thành công!')
      await farmRefresh() // Làm mới dữ liệu sau khi cập nhật
      router.back()
    }
  } catch (error) {
    submitStatus.value = 'error'
    toast.error(`Cập nhật thất bại: ${(error as Error).message || 'Unknown error'}`)
  } finally {
    submitStatus.value = 'idle'
  }
}
</script>