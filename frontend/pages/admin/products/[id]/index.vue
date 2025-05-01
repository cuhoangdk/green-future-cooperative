<template>
    <div class="p-4">
        <div v-if="productStatus === 'pending'" class="flex justify-center items-center h-screen">
            <span class="loading loading-spinner loading-lg"></span>
        </div>
        <form v-else @submit.prevent="handleSubmit" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="w-full">
                            <label class="text-gray-700 font-semibold">Mã sản phẩm</label>
                            <div class="input input-primary w-full mt-1 bg-gray-200">{{ product?.id }}</div>
                        </div>
                        <div class="w-full">
                            <label class="text-gray-700 font-semibold">Tên sản phẩm</label>
                            <input v-model="form.name" class="input input-primary w-full mt-1" placeholder="Rau cải"
                                required />
                        </div>
                        <div v-if="product?.status !== 'growing'">
                            <label class="text-gray-700 font-semibold">Trạng thái</label>
                            <select v-model="form.status" class="select select-primary w-full mt-1" required>
                                <option value="" disabled selected>Chọn trạng thái</option>
                                <option class="text-blue-600" value="selling">Đang bán</option>
                                <option class="text-red-600" value="stopped">Dừng bán</option>
                            </select>
                        </div>
                        <div v-if="currentUser?.is_super_admin">
                            <label class="text-gray-700 font-semibold">Thành viên</label>
                            <select
                                @change="(event) => searchFarms({ user_id: Number((event.target as HTMLSelectElement).value) })"
                                v-model="form.user_id" class="select select-primary w-full mt-1" required>
                                <option value="" disabled selected>Chọn chủ của sản phẩm</option>
                                <option v-for="user in users" :key="user.id" :value="user.id">
                                    {{ user.full_name }}
                                </option>
                            </select>
                        </div>
                        <div>
                            <label class="text-gray-700 font-semibold">Nông trại</label>
                            <select v-model="form.farm_id" class="select select-primary w-full mt-1" required>
                                <option value="" disabled selected>Chọn nông trại</option>
                                <option v-for="farm in farms" :key="farm.id" :value="farm.id">
                                    {{ farm.name }}
                                </option>
                            </select>
                        </div>
                        <div>
                            <label class="text-gray-700 font-semibold">Đơn vị</label>
                            <select v-model="form.unit_id" class="select select-primary w-full mt-1"
                                @change="!units.find(unit => unit.id === Number(form.unit_id))?.allow_decimal && form.stock_quantity !== null && (form.stock_quantity = Math.floor(form.stock_quantity))"
                                required>
                                <option value="" disabled selected>Chọn đơn vị</option>
                                <option v-for="unit in units" :key="unit.id" :value="unit.id">
                                    {{ unit.name }}
                                </option>
                            </select>
                        </div>
                        <div>
                            <label class="text-gray-700 font-semibold">Loại</label>
                            <select v-model="form.category_id" class="select select-primary w-full mt-1" required>
                                <option value="" disabled selected>Chọn loại</option>
                                <option v-for="category in productCategories" :key="category.id" :value="category.id">
                                    {{ category.name }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label class="text-gray-700 font-semibold">Mô tả</label>
                    <textarea v-model="form.description" class="textarea textarea-primary w-full h-52 mt-1"
                        placeholder="Sản phẩm được trồng..." />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="text-gray-700 font-semibold">Nhà cung cấp giống</label>
                    <input v-model="form.seed_supplier" class="input input-primary w-full mt-1"
                        placeholder="Cửa hàng..." />
                </div>

                <!-- Cultivated Area -->
                <div>
                    <label class="text-gray-700 font-semibold">Diện tích trồng (m²)</label>
                    <input v-model="form.cultivated_area" type="number" step="0.01"
                        class="input input-primary w-full mt-1" placeholder="700" />
                </div>

                <!-- Stock Quantity -->
                <div>
                    <label class="text-gray-700 font-semibold">Sản lượng ({{units.find(unit => unit.id ===
                        Number(form.unit_id))?.name || ''}})</label>
                    <input v-model="form.stock_quantity"
                        :step="form.unit_id && units.find(unit => unit.id === Number(form.unit_id))?.allow_decimal ? 0.1 : 1"
                        :class="{ 'input-primary': true, 'input-error': form.stock_quantity !== null && form.stock_quantity % 1 !== 0 && !units.find(unit => unit.id === Number(form.unit_id))?.allow_decimal }"
                        type="number" class="input w-full mt-1" placeholder="300"
                        @input="!units.find(unit => unit.id === Number(form.unit_id))?.allow_decimal && form.stock_quantity !== null && (form.stock_quantity = Math.floor(form.stock_quantity))" />
                </div>

                <!-- Expired -->
                <div>
                    <label class="text-gray-700 font-semibold">Hạn sử dụng (ngày)</label>
                    <input v-model="form.expired" type="number" step="1" class="input input-primary w-full mt-1"
                        placeholder="7" />
                </div>
                <div class="md:col-span-2">
                    <label class="text-gray-700 font-semibold">Ngày gieo hạt</label>
                    <input v-model="form.sown_at" type="date" class="input input-primary w-full mt-1" />
                </div>
                <div class="md:col-span-2">
                    <label class="text-gray-700 font-semibold">Bắt đầu thu hoạch</label>
                    <input v-model="form.harvested_at" type="date" class="input input-primary w-full mt-1" />
                </div>
            </div>

            <!-- Phần hình ảnh và giá cả (chỉ khi đã bán) -->
            <template v-if="product?.status === 'selling'">
                <!-- Harvested At -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <div class="border-t border-gray-200 pt-5">
                        <div class="text-lg font-medium text-gray-800 mb-3">Hình ảnh</div>
                        <div>
                            <div class="space-y-2">
                                <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
                                    <label
                                        class="flex items-center justify-center w-full aspect-[4/3] border-2 border-dashed border-gray-300 rounded-lg cursor-pointer">
                                        <span class="text-gray-500 text-2xl">+</span>
                                        <input @change="handleImageUpload" accept=".jpg,.jpeg,.png" type="file"
                                            class="hidden" multiple />
                                    </label>
                                    <div v-for="(image, index) in form.product_images" :key="index">
                                        <div class="relative w-full">
                                            <img :src="image.preview || `${backendUrl}${image.image_url}`"
                                                class="aspect-[4/3] w-full object-cover rounded-lg shadow" />
                                            <div
                                                class="absolute inset-0 flex flex-col justify-between items-end p-2 rounded-lg">
                                                <label
                                                    class="flex items-center text-black bg-white px-2 py-1 rounded-full">
                                                    <span class="text-sm font-semibold">Ảnh chính</span>
                                                    <input type="radio" v-model="primaryImageIndex" :value="index"
                                                        class="radio radio-primary ml-2" />
                                                </label>
                                                <button @click="removeImage(index)" type="button"
                                                    class="btn btn-error btn-sm mt-2">
                                                    Xóa
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 pt-5">
                        <div class="text-lg font-medium text-gray-800 mb-3">Thông tin giá cả</div>
                        <div class="flex space-x-4">
                            <div class="w-1/2">
                                <label class="text-gray-700 font-semibold">Kiểu giá</label>
                                <select v-model="form.pricing_type" class="select select-primary w-full mt-1" required>
                                    <option value="" disabled>Chọn kiểu giá</option>
                                    <option value="fix">Cố định</option>
                                    <option value="flexible">Theo số lượng</option>
                                    <option value="contact">Liên hệ</option>
                                </select>
                            </div>
                            <div class="w-1/2 flex items-end">
                                <button v-if="form.pricing_type === 'flexible'" @click="addPrice" type="button"
                                    class="btn btn-primary">
                                    Thêm giá
                                </button>
                                <label v-if="form.pricing_type === 'fix'" class="input w-full">
                                    <input v-model="form.product_prices[0].price" type="number" class=""
                                        placeholder="15000" required />
                                    <span class="">VNĐ</span>
                                </label>
                            </div>
                        </div>

                        <div v-if="form.pricing_type === 'flexible'">
                            <div class="flex justify-between items-center my-2">
                                <span class="text-gray-700 font-semibold">Danh sách giá</span>
                            </div>
                            <div v-for="(price, index) in form.product_prices" :key="index"
                                class="flex items-center gap-2 mb-2">
                                <label class="input">
                                    <span class="">Từ</span>
                                    <input v-model="price.quantity" type="number" class="" placeholder="0"
                                        :disabled="index === 0" required />
                                    <span class="">{{ product?.unit.name }}</span>
                                </label>

                                <label class="input">
                                    <input v-model="price.price" type="number" class="" placeholder="15000" required />
                                    <span class="">VNĐ</span>
                                </label>

                                <button v-if="form.product_prices.length > 1 && index !== 0" @click="removePrice(index)"
                                    type="button" class="btn btn-error btn-s">
                                    Xóa
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </template>

            <!-- Submit Button -->
            <div class="flex justify-between items-center">
                <UiButtonBack />
                <button type="submit" class="btn btn-primary" :disabled="status === 'pending'">
                    <span v-if="status === 'pending'" class="loading loading-spinner loading-md"></span>
                    <span>Lưu thay đổi</span>
                </button>
            </div>
        </form>
    </div>
</template>

<script setup lang="ts">
definePageMeta({ title: 'Chỉnh sửa sản phẩm', layout: 'user' })
import { useToast } from 'vue-toastification'
import type { Product, ProductCategory, Unit } from '~/types/product'
import type { User } from '~/types/user'
import type { Farm } from '~/types/farm'

const { createProduct, updateProduct, getProductById } = useProducts()
const { createImage, updateImage, deleteImage } = useProductImages()
const { createPrice, updatePrice, deletePrice } = useProductPrices()
const { getUsers } = useUsers()
const { searchFarms } = useFarms()
const { getUnits } = useUnits()
const { getProductCategories } = useProductCategories()
const { currentUser } = useUserAuth()
const toast = useToast()
const route = useRoute()
const router = useRouter()
const runtimeConfig = useRuntimeConfig()
const backendUrl = runtimeConfig.public.backendUrl

const status = ref<'idle' | 'pending' | 'success' | 'error'>('idle')
const productId = String(route.params.id)
const primaryImageIndex = ref<number>(0)
// Khởi tạo form
const form = ref({
    name: '',
    user_id: currentUser.value?.id || '',
    farm_id: '',
    unit_id: '',
    category_id: '',
    description: '',
    seed_supplier: '',
    cultivated_area: null as number | null,
    sown_at: null as string | null,
    stock_quantity: null as number | null,
    pricing_type: '',
    expired: null as number | null,
    harvested_at: null as string | null,
    status: '',
    product_images: [] as Array<{ id?: number, image_url: string | File | null, preview: string, sort_order: number, is_primary: boolean }>,
    product_prices: [{ id: undefined, quantity: '0', price: '' }] as Array<{ id?: number, quantity: string, price: string }>
})


// Lấy thông tin sản phẩm
const { data: productData, status: productStatus, refresh } = await getProductById(productId)
const product = computed<Product | null>(() => Array.isArray(productData.value?.data) ? productData.value.data[0] : productData.value?.data || null)

// Gọi API để lấy dữ liệu
const { data: usersData } = await getUsers()
const users = computed<User[]>(() => Array.isArray(usersData.value?.data) ? usersData.value.data : usersData.value ? [usersData.value.data] : [])
const { data: farmsData } = await searchFarms({ user_id: Number(form.value.user_id) })
const farms = computed<Farm[]>(() => Array.isArray(farmsData.value?.data) ? farmsData.value.data : farmsData.value ? [farmsData.value.data] : [])
const { data: unitsData } = await getUnits()
const units = computed<Unit[]>(() => Array.isArray(unitsData.value?.data) ? unitsData.value.data : unitsData.value ? [unitsData.value.data] : [])
const { data: productCategoriesData } = await getProductCategories()
const productCategories = computed<ProductCategory[]>(() => Array.isArray(productCategoriesData.value?.data) ? productCategoriesData.value.data : productCategoriesData.value ? [productCategoriesData.value.data] : [])

// Cập nhật form từ dữ liệu sản phẩm
watch(product, (newData) => {
    if (newData) {
        form.value.name = newData.name || ''
        form.value.user_id = newData.user_id?.toString() || ''
        form.value.farm_id = newData.farm_id?.toString() || ''
        form.value.unit_id = newData.unit.id?.toString() || ''
        form.value.category_id = newData.category_id?.toString() || ''
        form.value.description = newData.description || ''
        form.value.seed_supplier = newData.seed_supplier || ''
        form.value.cultivated_area = newData.cultivated_area ? parseFloat(newData.cultivated_area.toString()) : null
        form.value.sown_at = newData.sown_at ? new Date(newData.sown_at).toISOString().slice(0, 10) : null
        form.value.stock_quantity = newData.stock_quantity ? parseFloat(newData.stock_quantity.toString()) : null
        form.value.pricing_type = newData.pricing_type || ''
        form.value.expired = newData.expired || null
        form.value.harvested_at = newData.harvested_at ? new Date(newData.harvested_at).toISOString().slice(0, 10) : null
        form.value.status = newData.status || ''
        searchFarms({ user_id: Number(form.value.user_id) })
        // Load hình ảnh hiện tại
        if (newData.status === 'selling' && newData.images) {
            form.value.product_images = newData.images.map((img, index) => ({
                id: img.id,
                image_url: img.image_url,
                preview: `${backendUrl}${img.image_url}`,
                sort_order: img.sort_order,
                is_primary: img.is_primary
            }))
            primaryImageIndex.value = newData.images.findIndex(img => img.is_primary) || 0
        }

        // Load giá cả hiện tại
        if (newData.status === 'selling' && newData.prices) {
            form.value.product_prices = newData.prices.map(price => ({
                id: price.id,
                quantity: units.value.find(unit => unit.id === newData?.unit.id)?.allow_decimal
                    ? price.quantity.toString()
                    : Math.round(Number(price.quantity)).toString(),
                price: Math.round(price.price).toString()
            }))
        } else if (newData.status === 'selling' && !form.value.product_prices.length) {
            form.value.product_prices.push({ quantity: '', price: '' })
        }
    }
}, { immediate: true })

// Xử lý upload ảnh mới
const handleImageUpload = (event: Event) => {
    const files = (event.target as HTMLInputElement).files
    if (files) {
        Array.from(files).forEach((file, index) => {
            form.value.product_images.push({
                image_url: file,
                preview: URL.createObjectURL(file),
                sort_order: form.value.product_images.length,
                is_primary: form.value.product_images.length === 0
            })
        })
    }
}

// Xóa ảnh
const removeImage = async (index: number) => {
    const image = form.value.product_images[index]
    if (image.id) {
        const { error } = await deleteImage(productId, image.id)
        if (error.value) {
            toast.error('Xóa ảnh thất bại')
            return
        }
    }
    form.value.product_images.splice(index, 1)
    if (primaryImageIndex.value === index) {
        primaryImageIndex.value = 0
    }
}

// Thêm mức giá
const addPrice = () => {
    form.value.product_prices.push({ quantity: '', price: '' })
}

// Xóa mức giá
const removePrice = async (index: number) => {
    const price = form.value.product_prices[index]
    if (price.id) {
        const { error } = await deletePrice(productId, price.id)
        if (error.value) {
            toast.error('Xóa giá thất bại')
            return
        }
    }
    form.value.product_prices.splice(index, 1)
}

// Xử lý submit form
const handleSubmit = async () => {
    try {
        status.value = 'pending'

        // Tạo FormData cho thông tin sản phẩm
        const formProductData = new FormData()
        formProductData.append('name', form.value.name)
        formProductData.append('user_id', currentUser.value?.is_super_admin ? form.value.user_id?.toString() || '' : currentUser.value?.id?.toString() || '')
        formProductData.append('farm_id', form.value.farm_id?.toString() || '')
        formProductData.append('unit_id', form.value.unit_id?.toString() || '')
        formProductData.append('category_id', form.value.category_id?.toString() || '')
        formProductData.append('description', form.value.description || '')
        formProductData.append('seed_supplier', form.value.seed_supplier || '')
        formProductData.append('cultivated_area', form.value.cultivated_area?.toString() || '')
        formProductData.append('sown_at', form.value.sown_at || '')
        formProductData.append('stock_quantity', form.value.stock_quantity?.toString() || '')
        formProductData.append('expired', form.value.expired?.toString() || '')
        formProductData.append('harvested_at', form.value.harvested_at || '')
        formProductData.append('status', form.value.status || '')
        formProductData.append('pricing_type', form.value.pricing_type)

        // Cập nhật hoặc tạo sản phẩm
        const { error: productError } = product.value ? await updateProduct(productId, formProductData) : await createProduct(formProductData)
        if (productError.value) throw new Error('Thông tin sản phẩm không hợp lệ')

        // Nếu sản phẩm đã bán, xử lý hình ảnh và giá cả
        if (product.value?.status === 'selling') {
            // Xử lý hình ảnh
            const newImages = form.value.product_images.filter(img => img.image_url instanceof File)
            const existingImages = form.value.product_images.filter(img => img.id)

            // Cập nhật ảnh hiện tại
            for (const [index, image] of existingImages.entries()) {
                const imageData = new FormData()
                imageData.append('sort_order', image.sort_order.toString())
                imageData.append('is_primary', index === primaryImageIndex.value ? '1' : '0')
                const { error } = await updateImage(productId, image.id!, imageData)
                if (error.value) throw new Error('Cập nhật ảnh thất bại')
            }

            // Tạo ảnh mới
            if (newImages.length > 0) {
                const formImageData = new FormData()
                newImages.forEach((image, index) => {
                    formImageData.append(`images[${index}][image_url]`, image.image_url as Blob)
                    formImageData.append(`images[${index}][sort_order]`, image.sort_order.toString())
                    formImageData.append(`images[${index}][is_primary]`, index === primaryImageIndex.value - existingImages.length ? '1' : '0')
                })
                const { error } = await createImage(productId, formImageData)
                if (error.value) throw new Error('Thêm ảnh mới thất bại')
            }

            // Xử lý giá cả
            const newPrices = form.value.product_prices.filter(price => !price.id)
            const existingPrices = form.value.product_prices.filter(price => price.id)

            // Cập nhật giá hiện tại
            if (form.value.pricing_type === 'flexible') {
                for (const price of existingPrices) {
                    const priceData = new FormData()
                    priceData.append('quantity', price.quantity)
                    priceData.append('price', price.price)
                    const { error } = await updatePrice(productId, price.id!, priceData)
                    if (error.value) throw new Error('Cập nhật giá thất bại')
                }
                // Tạo giá mới
                if (newPrices.length > 0) {
                    const formPriceData = new FormData()
                    newPrices.forEach((price, index) => {
                        formPriceData.append(`prices[${index}][quantity]`, price.quantity)
                        formPriceData.append(`prices[${index}][price]`, price.price)
                    })
                    const { error } = await createPrice(productId, formPriceData)
                    if (error.value) throw new Error('Thêm giá mới thất bại')
                }
            } else if (form.value.pricing_type === 'fix') {
                const priceData = new FormData()
                priceData.append('quantity', '0')
                priceData.append('price', form.value.product_prices[0].price)
                const { error } = await updatePrice(productId, form.value.product_prices[0].id!, priceData)
                // Xóa tất cả các giá khác
                for (const price of form.value.product_prices.slice(1)) {
                    if (price.id) {
                        const { error } = await deletePrice(productId, price.id)
                        if (error.value) throw new Error('Xóa giá thất bại')
                    }
                }
                if (error.value) throw new Error('Cập nhật giá thất bại')
            }
        }

        toast.success('Cập nhật sản phẩm thành công!')
        refresh()
        router.push(`/admin/products`)
    } catch (error: any) {
        toast.error(error.message || 'Cập nhật sản phẩm thất bại')
    } finally {
        status.value = 'idle'
    }
}
</script>