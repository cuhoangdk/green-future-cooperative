<template>
    <div class="p-4">
        <form @submit.prevent="handleSubmit" class="space-y-4">
            <!-- Product Id -->
            <div>
                <label class="text-gray-700 font-semibold">Mã sản phẩm</label>
                <div class="input input-primary w-full mt-1 bg-gray-200">{{ product?.id }}</div>
            </div>
        
            <!-- Product Name -->
            <div>
                <label class="text-gray-700 font-semibold">Tên sản phẩm</label>
                <input v-model="form.name" class="input input-primary w-full mt-1" placeholder="Rau cải" required />
            </div>
            <!-- Description -->
            <div>
                <label class="text-gray-700 font-semibold">Mô tả</label>
                <textarea v-model="form.description" class="textarea textarea-primary w-full h-24 mt-1"
                    placeholder="Sản phẩm..." />
            </div>
            <!-- Stock Quantity -->
            <div class="flex space-x-4">
                <div class="w-1/2">
                    <label class="text-gray-700 font-semibold">Số lượng ({{ product?.unit.name }})</label>
                    <input v-model="form.stock_quantity" type="number" min="0" class="input input-primary w-full mt-1"
                        placeholder="100" required />
                </div>
                <div class="w-1/2">
                    <label class="text-gray-700 font-semibold">Ngày thu hoạch</label>
                    <input v-model="form.harvested_at" type="date" class="input input-primary w-full mt-1" required />
                </div>
            </div>
            <!-- Multiple Images -->
            <div class="border-t border-gray-200 pt-5">
                <div class="text-lg font-medium text-gray-800 mb-3">Hình ảnh</div>
                <div class="space-y-2">
                    <input @change="handleImageUpload" type="file"
                        class="file-input file-input-primary mt-1 w-full lg:w-1/2" multiple required />
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div v-for="(image, index) in form.product_images" :key="index">
                            <div class="relative w-full">
                                <img :src="image.preview"
                                    class="aspect-video w-full object-cover rounded-lg shadow mt-2" />
                                <div class="absolute inset-0 flex flex-col justify-between items-end p-2 rounded-lg">
                                    <label class="flex items-center text-black bg-white px-2 py-1 rounded-full">
                                        <span class="text-sm font-semibold">Ảnh chính</span>
                                        <input type="radio" v-model="primaryImageIndex" :value="index"
                                            class="radio radio-primary ml-2" />
                                    </label>
                                    <input v-model="image.sort_order" type="number" min="0"
                                        class="input input-primary mt-1 w-full" placeholder="Thứ tự" hidden />
                                    <button @click="removeImage(index)" type="button" class="btn btn-error btn-sm mt-2">
                                        Xóa
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Price Type -->
            <div class="border-t border-gray-200 pt-5">
                <div class="text-lg font-medium text-gray-800 mb-3">Giá cả</div>
                <div class="flex space-x-4">

                    <div class="w-1/2">
                        <label class="text-gray-700 font-semibold">Kiểu giá</label>
                        <select v-model="form.pricing_type" class="select select-primary w-full mt-1" required>
                            <option value="" disabled selected>Chọn đơn vị</option>
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
                        <div class="flex gap-2 items-center" v-if="form.pricing_type === 'fix'">
                            <input v-model="form.product_prices[0].price" class="input input-primary w-24"
                                placeholder="15000" required />
                            <span>VNĐ</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Multiple Prices -->
            <div v-if="form.pricing_type == 'flexible'">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-gray-700 font-semibold">Danh sách giá</span>

                </div>
                <div v-for="(price, index) in form.product_prices" :key="index" class="flex items-center gap-2 mb-2">
                    <div>
                        <label class="text-gray-700 font-semibold">Từ </label>
                        <input v-model="price.quantity" class="input input-primary w-15 text-center" placeholder="0"
                            required />
                        {{ product?.unit.name }}
                        <span class="text-gray-700 font-semibold">-</span>
                    </div>
                    <input v-model="price.price" class="input input-primary w-24 text-center" placeholder="15000"
                        required />
                    <span>VNĐ</span>
                    <button v-if="form.product_prices.length > 1" @click="removePrice(index)" type="button"
                        class="btn btn-error btn-sm">
                        Xóa
                    </button>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end mt-5">
                <button type="submit" class="btn btn-primary" :disabled="status === 'pending'">
                    <span v-if="status === 'pending'" class="loading loading-spinner loading-md"></span>
                    <span>Mở bán</span>
                </button>
            </div>
        </form>
    </div>
</template>

<script setup lang="ts">
definePageMeta({ title: 'Mở bán sản phẩm', layout: 'user' })
import { useToast } from 'vue-toastification'
import type { Product } from '~/types/product'

const { createImage } = useProductImages()
const { createPrice } = useProductPrices()
const { updateProduct, getProductById } = useProducts()
const toast = useToast()
const route = useRoute()
const router = useRouter()

const status = ref<'idle' | 'pending' | 'success' | 'error'>('idle')
const productId = String(route.params.id)
const primaryImageIndex = ref<number>(0)

// Lấy thông tin sản phẩm
const { data: productData } = await getProductById(productId)
const product = computed<Product | null>(() => Array.isArray(productData.value?.data) ? productData.value.data[0] : productData.value?.data || null)

// Khởi tạo form
const form = ref({
    name: '',
    description: '',
    pricing_type: '',
    stock_quantity: '',
    harvested_at: new Date().toISOString().slice(0, 10),
    product_images: [] as Array<{
        image_url: File | null,
        preview: string,
        sort_order: number,
        is_primary: boolean
    }>,
    product_prices: [] as Array<{
        quantity: string,
        price: string
    }>
})

// Khởi tạo giá trị mặc định
watch(product, (newData) => {
    if (newData) {
        form.value.name = newData.name || ''
        form.value.description = newData.description || ''
        form.value.pricing_type = newData.pricing_type || ''
        form.value.stock_quantity = newData.stock_quantity?.toString() || ''

        // Khởi tạo một giá mặc định nếu chưa có
        if (!form.value.product_prices.length) {
            form.value.product_prices.push({ quantity: '', price: '' })
        }
    }
}, { immediate: true })

// Xử lý upload nhiều ảnh
const handleImageUpload = (event: Event) => {
    const files = (event.target as HTMLInputElement).files
    if (files) {
        Array.from(files).forEach((file, index) => {
            form.value.product_images.push({
                image_url: file,
                preview: URL.createObjectURL(file),
                sort_order: form.value.product_images.length,
                is_primary: index === 0 && form.value.product_images.length === 0
            })
        })
    }
}

// Xóa ảnh
const removeImage = (index: number) => {
    form.value.product_images.splice(index, 1)
    if (primaryImageIndex.value === index) {
        primaryImageIndex.value = 0
    }
}

// Thêm mức giá mới
const addPrice = () => {
    form.value.product_prices.push({ quantity: '', price: '' })
}

// Xóa mức giá
const removePrice = (index: number) => {
    form.value.product_prices.splice(index, 1)
}

// Xử lý submit form
const handleSubmit = async () => {
    try {
        status.value = 'pending'

        // Tạo FormData cho product
        const formProductData = new FormData()
        formProductData.append('name', form.value.name)
        formProductData.append('description', form.value.description)
        formProductData.append('pricing_type', form.value.pricing_type)
        formProductData.append('stock_quantity', form.value.stock_quantity)
        formProductData.append('harvested_at', form.value.harvested_at)
        formProductData.append('status', 'selling')

        // Tạo FormData cho images
        const formImageData = new FormData()
        form.value.product_images.forEach((image, index) => {
            formImageData.append(`images[${index}][image_url]`, image.image_url as Blob)
            formImageData.append(`images[${index}][sort_order]`, image.sort_order.toString())
            formImageData.append(`images[${index}][is_primary]`, index === primaryImageIndex.value ? '1' : '0')
        })

        // Tạo FormData cho prices
        const formPriceData = new FormData()
        form.value.product_prices.forEach((price, index) => {
            formPriceData.append(`prices[${index}][quantity]`, form.value.pricing_type === 'fix' ? '0' : price.quantity)
            formPriceData.append(`prices[${index}][price]`, price.price)
        })

        const { error: productError } = await updateProduct(productId, formProductData)
        const { error: imageError } = await createImage(productId, formImageData)
        if (form.value.pricing_type !== 'contact') {
            const { error } = await createPrice(productId, formPriceData)
            if (error.value) throw new Error('Thêm giá mới thất bại')
        }
        if (productError.value) throw new Error('Thông tin sản phẩm không hợp lệ')
        if (imageError.value) throw new Error('Ảnh không hợp lệ')

        toast.success('Mở bán thành công!')
        router.push(`/admin/products`)
    } catch (error: any) {
        toast.error(error.message || 'Mở bán thất bại')
    } finally {
        status.value = 'idle'
    }
}
</script>