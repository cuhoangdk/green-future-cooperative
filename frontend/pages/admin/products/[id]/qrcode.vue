<template>
    <div class="p-4">
        <div v-if="productStatus === 'pending'" class="skeleton w-[650px] h-[280px] rounded-lg" />
        <div v-else ref="labelRef"
            class="w-[650px] border-2 border-green-700 rounded-lg p-2 bg-white font-sans shadow-md relative overflow-hidden">
            <div class="absolute inset-0 flex items-center justify-center opacity-5 pointer-events-none">
                <img :src="logo" alt="Logo" class="w-96 h-96 object-contain" />
            </div>

            <div class="text-center border-b border-green-200">
                <h1 class="text-xl font-bold text-green-700">HTX TƯƠNG LAI XANH</h1>
            </div>

            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0 border border-green-300 p-1 rounded bg-green-50">
                    <div class="w-36 h-36 bg-gray-200 flex items-center justify-center text-xs text-gray-500">
                        <img :src="qrCodeImage || ''" alt="QR Code">
                    </div>
                </div>

                <div class="flex-1 text-sm space-y-1">
                    <p><span class="font-semibold text-green-800">Tên sản phẩm:</span> {{ product?.name }}</p>
                    <p><span class="font-semibold text-green-800">Mã sản phẩm:</span> {{ product?.id }}</p>
                    <p><span class="font-semibold text-green-800">Xuất xứ:</span> {{ addressData || 'Đang tải...' }}</p>
                    <p><span class="font-semibold text-green-800">Ngày thu hoạch:</span> {{ formatDate(havertedAt) }}</p>
                    <p><span class="font-semibold text-green-800">SĐT/Zalo:</span> 0917248016</p>
                    <p><span class="font-semibold text-green-800">Fanpage:</span> Green Future Cooperative</p>
                    <p><span class="font-semibold text-green-800">Hạn sử dụng:</span> {{ expired ? expired : "__" }} ngày kể từ ngày thu hoạch</p>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <div class="text-gray-700 font-semibold">Ngày thu hoạch</div>
            <input v-model="havertedAt" type="date" class="input input-sm input-primary" />
        </div>
        <div class="mt-2" >
            <div class="text-gray-700 font-semibold">Hạn sử dụng</div>
            <input v-model="expired" type="number" class="input input-sm input-primary" placeholder="Nhập số ngày hết hạn" />
        </div>
        
        <div class="flex gap-4">
            <button @click="$router.back()" class="mt-4 btn btn-ghost">Quay lại</button>

            <button @click="downloadLabel" class="mt-4 btn btn-primary">Tải xuống nhãn</button>
        </div>

    </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'user', title: 'QR Code' })
import type { Product } from '~/types/product'
import type { Farm } from '~/types/farm'
import { formatDate } from '~/utils/common'
import domtoimage from 'dom-to-image'

const route = useRoute()
const runtimeConfig = useRuntimeConfig()
const productId = String(route.params.id)
const logo = runtimeConfig.public.logo

const { getProductQRCode, getProductById } = useProducts()
const { getFarmById } = useFarms()
const { getFullAddressName } = useVietnamAddress()

const { data: qrCodeData } = await getProductQRCode(productId)
const qrCodeImage = computed(() => {
    if (!qrCodeData.value?.data) return null
    return `data:image/png;base64,${qrCodeData.value.data}`
})

const { data: productData, status: productStatus } = await getProductById(productId)
const product = computed<Product | null>(() => Array.isArray(productData.value?.data) ? productData.value.data[0] : productData.value?.data || null)

const farm = ref<Farm | null>(null)
const havertedAt = ref('')
const expired = ref()
watch(product, async (newProduct) => {
    if (newProduct?.farm_id) {
        const { data } = await getFarmById(Number(newProduct.farm_id))
        farm.value = data.value?.data || null
    }
    if (newProduct?.harvested_at) {
        havertedAt.value = newProduct.harvested_at
    }
    if (newProduct?.expired) {
        expired.value = newProduct.expired
    }
}, { immediate: true })

const addressData = ref('')
watch(farm, async (newFarm) => {
    if (newFarm?.address?.ward) {
        addressData.value = await getFullAddressName(newFarm.address.ward)
    }
})


const labelRef = ref<HTMLElement | null>(null)
const downloadLabel = async () => {
    if (!labelRef.value) return

    domtoimage.toPng(labelRef.value)
        .then(function (dataUrl) {
            const link = document.createElement('a')
            link.download = `label-${product.value?.id || 'product'}-${new Date().getTime()}.png`
            link.href = dataUrl
            link.click()
        })
        .catch(function (error) {
            console.error('Lỗi khi tải xuống nhãn:', error)
        })
}
</script>