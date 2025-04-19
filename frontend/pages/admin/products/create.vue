<template>
    <div class="p-4">
        <form @submit.prevent="handleSubmit" class="space-y-4">
            <div class="">
                <label class="text-gray-700 font-semibold">Tên sản phẩm</label>
                <input v-model="form.name" class="input input-primary w-full mt-1" placeholder="Rau cải" required />
            </div>

            <div v-if="currentUser?.is_super_admin">
                <label class="text-gray-700 font-semibold">Thành viên</label>
                <select @change="(event) => searchFarms({ user_id: Number((event.target as HTMLSelectElement).value) })"
                    v-model="form.user_id" class="select select-primary w-full mt-1" required>
                    <option value="" disabled selected>Chọn chủ của sản phẩm</option>
                    <option v-for="user in users" :key="user.id" :value="user.id">
                        {{ user.full_name }}
                    </option>
                </select>
            </div>
            <div class="flex gap-2">
                <div class="w-1/3">
                    <label class="text-gray-700 font-semibold">Nông trại</label>
                    <select v-model="form.farm_id" class="select select-primary w-full mt-1" required>
                        <option value="" disabled selected>Chọn nông trại</option>
                        <option v-for="farm in farms" :key="farm.id" :value="farm.id">
                            {{ farm.name }}
                        </option>
                    </select>
                </div>

                <div class="w-1/3">
                    <label class="text-gray-700 font-semibold">Đơn vị</label>
                    <select v-model="form.unit_id" class="select select-primary w-full mt-1" required>
                        <option value="" disabled selected>Chọn đơn vị</option>
                        <option v-for="unit in units" :key="unit.id" :value="unit.id">
                            {{ unit.name }}
                        </option>
                    </select>
                </div>

                <div class="w-1/3">
                    <label class="text-gray-700 font-semibold">Loại</label>
                    <select v-model="form.category_id" class="select select-primary w-full mt-1" required>
                        <option value="" disabled selected>Chọn loại</option>
                        <option v-for="category in productCategories" :key="category.id" :value="category.id">
                            {{ category.name }}
                        </option>
                    </select>
                </div>
            </div>

            <div>
                <label class="text-gray-700 font-semibold">Mô tả</label>
                <textarea v-model="form.description" class="textarea textarea-primary w-full h-24 mt-1"
                    placeholder="Sẩn phẩm được trồng..." />
            </div>

            <div class="flex flex-col md:flex-row gap-2">
                <div class="w-full md:w-1/2">
                    <label class="text-gray-700 font-semibold">Ngày gieo hạt</label>
                    <input v-model="form.sown_at" type="date" class="input input-primary w-full mt-1"
                        placeholder="dd/mm/yyyy" />
                </div>
                <div class="w-full md:w-1/2">
                    <label class="text-gray-700 font-semibold">Nhà cung cấp giống</label>
                    <input v-model="form.seed_supplier" class="input input-primary w-full mt-1"
                        placeholder="Cửa hàng..." />
                </div>
            </div>

            <div class="flex flex-col md:flex-row gap-2">
                <div class="w-full md:w-1/2">
                    <label class="text-gray-700 font-semibold">Diện tích trồng (mét vuông)</label>
                    <input v-model="form.cultivated_area" type="number" step="0.01"
                        class="input input-primary w-full mt-1" placeholder="700" />
                </div>
                <div class="w-full md:w-1/2">
                    <label class="text-gray-700 font-semibold">Sản lượng dự kiến{{ units.find(unit => unit.id === Number(form.unit_id))?.name ? ` (${units.find(unit => unit.id === Number(form.unit_id))?.name})` : '' }}</label>
                    <input v-model="form.stock_quantity" type="number" step="0.1"
                        class="input input-primary w-full mt-1" placeholder="300" required/>
                </div>
            </div>

            <div class="w-full md:w-1/2">
                <label class="text-gray-700 font-semibold">Hạn sử dụng (ngày từ khi thu hoạch)</label>
                <input v-model="form.expired" type="number" step="1" class="input input-primary w-full mt-1"
                    placeholder="7" />
            </div>

            <div class="flex justify-between items-center">
                <UiButtonBack />
                <button type="submit" class="btn btn-primary" :disabled="status === 'pending'">
                    <span v-if="status === 'pending'" class="loading loading-spinner loading-md"></span>
                    <span>Thêm</span>
                </button>
            </div>
        </form>
    </div>
</template>

<script setup lang="ts">
definePageMeta({ title: 'Thêm sản phẩm', layout: 'user', })
import type { Product, ProductCategory, Unit } from '~/types/product'
import type { User } from '~/types/user'
import type { Farm } from '~/types/farm'

const { createProduct } = useProducts()
const { getUsers } = useUsers()
const { searchFarms } = useFarms()
const { getUnits } = useUnits()
const { getProductCategories } = useProductCategories()
const { currentUser } = useUserAuth()
const { $toast } = useNuxtApp()
const router = useRouter()

const status = ref<'idle' | 'pending' | 'success' | 'error'>('idle')


// Khởi tạo form với các giá trị mặc định
const form = ref({
    name: '',
    user_id: currentUser.value?.id || '',
    farm_id: '',
    unit_id: '',
    category_id: '',
    description: '',
    seed_supplier: '',
    cultivated_area: null as number | null,
    sown_at: new Date().toISOString().split('T')[0] as string | null,
    stock_quantity: null as number | null,
    expired: 4,
    meta_title: '',
    meta_description: '',
    meta_keywords: '',
})

// Gọi API để lấy user
const { data: usersData } = await getUsers()
const users = computed<User[]>(() => Array.isArray(usersData.value?.data) ? usersData.value.data : usersData.value ? [usersData.value.data] : [])
// Gọi API để lấy danh sách nông trại
const { data: farmsData } = await searchFarms({ user_id: Number(form.value.user_id) })
const farms = computed<Farm[]>(() => Array.isArray(farmsData.value?.data) ? farmsData.value.data : farmsData.value ? [farmsData.value.data] : [])
// Gọi API để lấy danh sách đơn vị
const { data: unitsData } = await getUnits()
const units = computed<Unit[]>(() => Array.isArray(unitsData.value?.data) ? unitsData.value.data : unitsData.value ? [unitsData.value.data] : [])
// Gọi API để lấy danh sách danh mục sản phẩm
const { data: productCategoriesData } = await getProductCategories()
const productCategories = computed<ProductCategory[]>(() => Array.isArray(productCategoriesData.value?.data) ? productCategoriesData.value.data : productCategoriesData.value ? [productCategoriesData.value.data] : [])


// Xử lý submit form
const handleSubmit = async () => {
    try {
        status.value = 'pending'

        // Tạo FormData để gửi dữ liệu
        const formData = new FormData()
        formData.append('name', form.value.name)
        formData.append('user_id', currentUser.value?.is_super_admin ? form.value.user_id?.toString() || '' : currentUser.value?.id?.toString() || '')
        formData.append('description', form.value.description || '')
        formData.append('farm_id', form.value.farm_id?.toString() || '')
        formData.append('unit_id', form.value.unit_id?.toString() || '')
        formData.append('category_id', form.value.category_id?.toString() || '')
        formData.append('seed_supplier', form.value.seed_supplier || '')
        formData.append('cultivated_area', form.value.cultivated_area?.toString() || '')
        formData.append('sown_at', form.value.sown_at || '')
        formData.append('stock_quantity', form.value.stock_quantity?.toString() || '')
        formData.append('meta_title', form.value.meta_title || '')
        formData.append('meta_description', form.value.meta_description || '')
        formData.append('meta_keywords', form.value.meta_keywords || '')
        formData.append('pricing_type', 'flexible')
        formData.append('expired', form.value.expired?.toString() || '')

        const { error } = await createProduct(formData)

        if (error.value) throw new Error(error.value.message || 'Tạo sản phẩm thất bại')

        $toast.success('Tạo sản phẩm thành công!')
        router.push(`/admin/products`)
    } catch (error: any) {
        $toast.error(error.message || 'Tạo sản phẩm thất bại!')
    } finally {
        status.value = 'idle'
    }
}
</script>