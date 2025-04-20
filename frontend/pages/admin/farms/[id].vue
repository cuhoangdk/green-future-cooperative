<template>
  <div class="p-4">
    <div v-if="status === 'pending'" class="flex justify-center items-center h-screen">
      <span class="loading loading-spinner loading-lg"></span>
    </div>
    <form @submit.prevent="handleSubmit" class="space-y-4">
      <!-- Section 1: Farm Information -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Farm Name -->
        <div>
          <label class="text-gray-700 font-semibold block mb-1">Tên nông trại <span
              class="text-red-500">*</span></label>
          <input v-model="form.name" class="input input-bordered input-primary w-full" placeholder="Nông trại XYZ"
            required />
        </div>
        <!-- User Selection -->
        <div>
          <label class="text-gray-700 font-semibold block mb-1">Thành viên<span class="text-red-500"></span></label>
          <div class="input input-bordered select-primary w-full bg-gray-100">
            {{ farm?.user?.full_name }}
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
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="text-gray-700 font-semibold block mb-1">Kích thước (m2)</label>
          <input v-model="form.farm_size" type="number" step="0.01" class="input input-bordered input-primary w-full"
            placeholder="0.5" />
        </div>
        <div>
          <label class="text-gray-700 font-semibold block mb-1">Loại đất</label>
          <input v-model="form.soil_type" class="input input-bordered input-primary w-full"
            placeholder="Đất cát trắng" />
        </div>
      </div>

      <!-- Section 3: Address -->
      <div class="border-t border-gray-200 pt-5">
        <h3 class="text-lg font-medium text-gray-800 mb-3">Địa chỉ</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
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
          <div>
            <label class="text-gray-700 font-semibold block mb-1">Phường/Xã</label>
            <select v-model="form.address.ward" class="select select-bordered select-primary w-full" required>
              <option value="" disabled>Phường/xã</option>
              <option v-for="w in wards" :key="w.id" :value="w.id">{{ w.name }}</option>
            </select>
          </div>
        </div>
        <div class="mt-4">
          <label class="text-gray-700 font-semibold block mb-1">Địa chỉ chi tiết</label>
          <input v-model="form.address.street_address" class="input input-bordered input-primary w-full"
            placeholder="Số nhà, tên đường..." required />
        </div>
      </div>

      <!-- Section 4: Location -->
      <div class="border-t border-gray-200 pt-5">
        <h3 class="text-lg font-medium text-gray-800 mb-3">Vị trí</h3>
        <div class="mb-4">
          <LMap ref="map" style="height: 350px" :zoom="12" :center="[form.latitude ?? 0, form.longitude ?? 0]"
            :use-global-leaflet="true">
            <LTileLayer url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
              attribution='&copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors'
              layer-type="base" name="OpenStreetMap" />
            <LMarker :lat-lng="[form.latitude ?? 0, form.longitude ?? 0]" draggable @moveend="updateMarkerPosition" />
          </LMap>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
          <div>
            <label class="text-gray-700 font-semibold block mb-1">Kinh độ</label>
            <input v-model.number="form.latitude" type="number" step="0.000001"
              class="input input-bordered input-primary w-full" placeholder="21.0285" />
          </div>
          <div>
            <label class="text-gray-700 font-semibold block mb-1">Vĩ độ</label>
            <input v-model.number="form.longitude" type="number" step="0.000001"
              class="input input-bordered input-primary w-full" placeholder="105.8542" />
          </div>
        </div>
      </div>

      <!-- Submit Button -->
      <div class="border-t border-gray-200 pt-5 flex justify-between items-center">
        <UiButtonBack />
        <button type="submit" class="btn btn-primary px-6" :disabled="submitStatus === 'pending'">
          <span v-if="submitStatus === 'pending'" class="loading loading-spinner loading-md"></span>
          Lưu
        </button>
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
import type { Farm } from '~/types/farm'

definePageMeta({ layout: 'user', title: 'Chỉnh sửa nông trại' })

const route = useRoute()
const router = useRouter()
const config = useRuntimeConfig()
const { $toast } = useNuxtApp()
const { currentUser } = useUserAuth()
const { getFarmById, updateFarm } = useFarms()
const { provinces, districts, wards, fetchProvinces, fetchDistricts, fetchWards } = useVietnamAddress()

// Lấy ID từ route params
const farmId = route.params.id

// Lấy dữ liệu nông trại hiện tại
const { data: farmData, error: farmError, status, refresh: farmRefresh } = await getFarmById(Number(farmId))
const farm = computed<Farm | null>(() =>
  Array.isArray(farmData.value?.data) ? farmData.value.data[0] : farmData.value?.data || null
)

if (status.value === 'error') {
  $toast.error('Không thể tải dữ liệu nông trại!')
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

// Xử lý kéo thả marker
const updateMarkerPosition = (event: any) => {
  const latLng = event.target.getLatLng()
  form.value.latitude = Number(latLng.lat.toFixed(6))
  form.value.longitude = Number(latLng.lng.toFixed(6))
}

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
    submitStatus.value = 'pending'
    if (status.value === 'success') {
      $toast.success('Cập nhật nông trại thành công!')
      await farmRefresh() // Làm mới dữ liệu sau khi cập nhật
      router.back()
    }
  } catch (error) {
    submitStatus.value = 'error'
    $toast.error(`Cập nhật thất bại: ${(error as Error).message || 'Unknown error'}`)
  } finally {
    submitStatus.value = 'idle'
  }
}
</script>