<template>
    <div class="p-4">
        <!-- Loading State -->
        <div v-if="productStatus === 'pending'" class="flex justify-center items-center h-screen">
            <span class="loading loading-spinner loading-lg"></span>
        </div>

        <!-- Content when loaded -->
        <div v-else class="flex flex-col lg:flex-row gap-4">
            <!-- Header with title and back button -->
            <!-- Product info card -->
            <div class="lg:w-2/3 bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-700 mb-4 border-b pb-2">Sản phẩm</h2>
                <!-- Basic product information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="text-sm font-medium text-gray-600">Mã sản phẩm</label>
                        <div class="input input-bordered w-full bg-gray-50 mt-1">{{ product?.id || 'N/A' }}</div>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Tên sản phẩm</label>
                        <div class="input input-bordered w-full bg-gray-50 mt-1">{{ product?.name || 'N/A' }}</div>
                    </div>

                    <div v-if="currentUser?.is_super_admin">
                        <label class="text-sm font-medium text-gray-600">Thành viên</label>
                        <div class="input input-bordered w-full bg-gray-50 mt-1">{{ product?.user?.full_name || 'N/A' }}
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-600">Tồn kho</label>
                        <div class="join w-full">

                            <span class="join-item input ">{{ product?.stock_quantity ?
                                formatNumber(product?.stock_quantity ) : '0' }}
                            </span>
                            <span class="join-item input w-26">{{ product?.unit?.name }}</span>
                        </div>
                    </div>
                </div>

                <!-- Pricing information -->
                <div class="mb-6">
                    <div v-if="product?.pricing_type === 'fix'" class="bg-gray-50 p-4 rounded-md">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Giá cố định:</span>
                            <span class="font-semibold text-lg text-primary">
                                {{ product?.prices?.[0]?.price ? formatCurrency(product?.prices?.[0]?.price) : '' }}
                            </span>
                        </div>
                    </div>

                    <div v-if="product?.pricing_type === 'flexible'" class="bg-gray-50 p-4 rounded-md">
                        <div class="text-gray-600 mb-2">Danh sách giá:</div>
                        <div class="overflow-x-auto">
                            <table class="table table-compact w-full">
                                <thead>
                                    <tr>
                                        <th class="bg-gray-100">Số lượng</th>
                                        <th class="bg-gray-100">Đơn giá</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(price, index) in product?.prices" :key="index">
                                        <td>{{ formatNumber(price.quantity) }} {{ product?.unit.name }}</td>
                                        <td class="font-medium">{{ formatCurrency(price.price) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div v-if="product?.pricing_type === 'contact'" class="bg-gray-50 p-4 rounded-md">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Hình thức giá:</span>
                            <span class="font-semibold text-primary">Liên hệ để biết giá</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sale form -->
            <div class="lg:w-1/3 ">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4 border-b pb-2">Thông tin bán hàng</h2>
                    <div class="max-w-md mx-auto">
                        <div class="form-control mb-6">
                            <label class="label">
                                <span class="label-text font-medium">Số lượng bán</span>
                            </label>
                            <div class="input w-full">
                                <input v-model="quantity" type="number" min="1" :max="product?.stock_quantity" class=""
                                    placeholder="Nhập số lượng" required />
                                <span class="">
                                    {{ product?.unit?.name }}
                                </span>
                            </div>

                            <div v-if="quantity && product?.pricing_type === 'fix'" class="mt-2 text-right">
                                <span class="text-sm text-gray-500">Thành tiền:</span>
                                <span class="ml-2 font-bold text-primary">{{ calculateTotal() }} VNĐ</span>
                            </div>
                        </div>

                        <div class="flex justify-between mt-6">
                            <UiButtonBack />
                            <button @click="handleSubmit" class="btn btn-primary px-8"
                                :disabled="status === 'pending' || !quantity">
                                <span v-if="status === 'pending'"
                                    class="loading loading-spinner loading-sm mr-2"></span>
                                Hoàn tất bán
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
definePageMeta({ title: 'Bán nhanh', layout: 'user' })
import type { Product } from '~/types/product'

const { getProductById } = useProducts()
const { quickStoreAdminOrder } = useAdminOrder()
const { currentUser } = useUserAuth()
const { $toast } = useNuxtApp()
const route = useRoute()
const router = useRouter()

const status = ref<'idle' | 'pending' | 'success' | 'error'>('idle')
const productId = String(route.params.id)
const quantity = ref<number>()

// Lấy thông tin sản phẩm
const { data: productData, status: productStatus, refresh } = await getProductById(productId)
const product = computed<Product | null>(() => Array.isArray(productData.value?.data) ? productData.value.data[0] : productData.value?.data || null)

watch(product, (newProduct) => {
    if (newProduct) {
        if (newProduct.status != 'selling') {
            router.push('/admin/products')
        }
    }
}, { immediate: true })

// Tính tổng tiền (nếu giá cố định)
const calculateTotal = () => {
    if (product.value?.pricing_type === 'fix' && product.value?.prices?.[0]?.price && quantity.value) {
        return new Intl.NumberFormat('vi-VN').format(product.value.prices[0].price * quantity.value)
    }
    return '0'
}

// Xử lý submit form
const handleSubmit = async () => {
    try {
        if (!quantity.value) {
            $toast.error('Vui lòng nhập số lượng!')
            return
        }

        if (product.value?.stock_quantity && quantity.value > product.value.stock_quantity) {
            $toast.error(`Số lượng không được vượt quá ${product.value.stock_quantity} ${product.value.unit.name}!`)
            return
        }

        status.value = 'pending'
        await quickStoreAdminOrder(productId, quantity.value)
        $toast.success('Bán hàng thành công!')
        refresh()
        router.push('/admin/products')
    } catch (error: any) {
        $toast.error(error.message || 'Có lỗi xảy ra')
    } finally {
        status.value = 'idle'
    }
}
</script>