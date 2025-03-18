<template>
    <div class="min-h-screen items-center flex flex-col mt-16 pb-5 lg:mt-0">
        <div class="w-11/12 flex max-w-7xl mt-5 gap-5">
            <div class="w-1/2">
                <!-- Ảnh lớn -->
                <div class="relative">
                    <img :src="activeImageUrl" :alt="product?.name"
                        class="w-full aspect-[4/3] object-cover rounded-xl border border-gray-200"
                        @error="event => { const target = event.target as HTMLImageElement; if (target) target.src = placeholderImage as string; }" />
                    <!-- Nút điều hướng -->
                    <button v-if="product?.images && product.images.length > 1" @click="prevImage"
                        class="absolute left-2 top-1/2 transform -translate-y-1/2 bg-gray-600 bg-opacity-25 text-white p-2 rounded-full hover:bg-opacity-75">
                        <ArrowLeft />
                    </button>
                    <button v-if="product?.images && product.images.length > 1" @click="nextImage"
                        class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-gray-600 bg-opacity-25 text-white p-2 rounded-full hover:bg-opacity-75">
                        <ArrowRight />
                    </button>
                </div>
                <!-- Danh sách ảnh nhỏ -->
                <div class="flex gap-2 mt-2 overflow-x-auto">
                    <img v-for="(image, index) in product?.images" :key="index" :src="`${backendUrl}${image.image_url}`"
                        :alt="`Image ${index + 1}`"
                        class="w-20 h-20 object-cover rounded border border-gray-200 flex-shrink-0 cursor-pointer"
                        :class="{ 'border-green-600 border-2': activeImageIndex === index }"
                        @click="activeImageIndex = index"
                        @error="event => { const target = event.target as HTMLImageElement; if (target) target.src = placeholderImage as string; }" />
                </div>
            </div>
            <div class="w-1/2">
                <div class="max-w-7xl p-3 px-6 border border-gray-200 rounded-xl">
                    <div class="flex justify-between item-center mb-3">
                        <h1 class="text-2xl font-bold">{{ product?.name }}</h1>
                        <div class="flex gap-1">
                            <EyeIcon class="w-6 h-6 text-gray-500" /> {{ product?.views }}
                        </div>
                    </div>
                    <div class="flex justify-between mb-3">
                        <p class="text-sm text-gray-500"><span>Mã: </span>{{ product?.product_code }}</p>
                        <p class="text-sm text-gray-500"><span>Đã bán: </span>{{ product?.sold_quantity }} <span>{{
                            product?.unit.name }}</span></p>
                    </div>
                    <p class="text-3xl font-bold text-green-600 mb-3">{{ Math.floor(product?.prices?.[0]?.price ??
                        0).toLocaleString('vi-VN') }}<span class="text-xl underline">đ</span><span
                            class="text-xl text-gray-500"> / {{ product?.unit.name
                            }}</span></p>
                    <hr class="border border-gray-200 mb-3">

                    <p class="text-sm text-gray-500 mb-3 h-24 overflow-y-auto">{{ product?.description }}</p>

                    <hr class="border border-gray-200 mb-3">

                    <div class="flex mb-1">
                        <p class="w-1/2 text-sm text-gray-900"><span class="text-gray-500">Nhà cung cấp giống: </span>{{
                            product?.seed_supplier }}
                        </p>
                        <p class="w-1/2 text-sm text-gray-900"><span class="text-gray-500">Diện tích canh tác: </span>{{
                            product?.cultivated_area
                        }} m²
                        </p>
                    </div>
                    <div class="flex mb-3">
                        <p class="w-1/2 text-sm text-gray-900"><span class="text-gray-500">Ngày gieo trồng: </span>{{
                            new
                                Date(product?.sown_at ??
                                    '').toLocaleDateString() }}</p>
                        <p class="w-1/2 text-sm text-gray-900"><span class="text-gray-500">Ngày thu hoạch: </span>{{ new
                            Date(product?.harvested_at
                                ?? '').toLocaleDateString() }}</p>
                    </div>
                    <hr class="border border-gray-200 mb-3">

                    <p class="w-1/2 text-sm text-gray-900 mb-1"><span class="text-gray-500">Người trồng: </span>{{
                        product?.user?.full_name }}</p>
                    <p class="w-1/2 text-sm text-gray-900 mb-1"><span class="text-gray-500">Nơi trồng: </span>{{
                        farmAddress }}</p>

                    <div class="mt-4 flex gap-4">
                        <button class="bg-green-600 text-white font-semibold px-5 py-2 rounded-full hover:bg-green-700">
                            Thêm vào giỏ hàng
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="w-11/12 flex flex-col max-w-7xl mt-5 gap-5">
            <div class="w-full">
                <div class="divider divider-primary divider-start text-xl font-semibold text-green-600">Quá trình chăm
                    sóc</div>
                <div class="relative">
                    <div v-if="logs.status === 'pending'"
                        class="absolute inset-0 flex items-center justify-center bg-gray-100/20 z-10">
                        <span class="loading loading-spinner loading-xl"></span>
                    </div>
                </div>
                <div v-for="log in logs.logs" :key="log.id">
                    <div class="collapse collapse-arrow bg-base-100 border-base-300 border my-1.5">
                        <input type="checkbox" />
                        <div class="collapse-title font-semibold">
                            {{
                                log.created_at ? new Date(log.created_at).toLocaleString('vi-VN', {
                                    hour: '2-digit', minute: '2-digit', day: '2-digit', month: '2-digit', year: 'numeric'
                                })
                                    : ''
                            }} - {{ log.activity }}
                        </div>
                        <div class="collapse-content text-sm">
                            <div class="flex flex-col space-y-2 mb-2">
                                <div v-if="log.fertilizer_used"><span class="font-semibold">Phân bón:</span> {{
                                    log.fertilizer_used }}
                                </div>
                                <div v-if="log.pesticide_used"><span class="font-semibold">Thuốc bảo vệ thực vật:</span>
                                    {{
                                        log.pesticide_used
                                    }}</div>
                                <div v-if="log.notes"><span class="font-semibold">Ghi chú:</span> {{ log.notes }}</div>
                            </div>
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-2">
                                <NuxtImg v-if="log.image_url" :src="`${backendUrl}${log.image_url}`" alt="Hình ảnh"
                                    class="w-full border border-gray-100 overflow-hidden object-cover aspect-video rounded-lg bg-gray-100" />
                                <div v-else
                                    class="flex justify-center items-center w-full aspect-video rounded-lg bg-gray-100">
                                    Không có hình ảnh</div>

                                <iframe v-if="log.video_url" class="w-full aspect-video rounded-lg"
                                    :src="`https://www.youtube.com/embed/${log.video_url}`" frameborder="0"
                                    allowfullscreen>
                                </iframe>
                                <div v-else
                                    class="flex justify-center items-center w-full aspect-video rounded-lg bg-gray-100">
                                    Không có video</div>
                            </div>
                        </div>
                    </div>
                </div>
                <UiPagination class="mt-4" :links="logs.links" :meta="logs.meta" @page-change="handleLogsPageChange" />
            </div>
            <div class="w-full">
                <ProductList title="Sản phẩm khác" :products="otherProducts.products" :meta="otherProducts.meta"
                    :links="otherProducts.links" :status="otherProductsStatus"
                    @page-change="handleProductsPageChange" />
            </div>
        </div>
        11
        <pre>{{ farm?.address }}</pre>
        11
    </div>
</template>

<script setup lang="ts">
import { EyeIcon, ArrowLeft, ArrowRight } from 'lucide-vue-next'
import type { Farm } from '~/types/farm'
import type { Product, CultivationLog } from '~/types/product'
import type { PaginationMeta, PaginationLinks } from '~/types/api'
const route = useRoute()
const slug = String(route.params.slug)
const config = useRuntimeConfig()
const placeholderImage = config.public.placeholderImage
const backendUrl = config.public.backendUrl

const { getProductBySlug, getProducts } = useProducts()
const { getFarmById } = useFarms()
const { getLogs } = useCultivationLogs()
const { getAddressNameById } = useVietnamAddress()
const currentLogPage = ref(1)
const perLogPage = 10

const { data: productData, status: productStatus, error: productError } = await getProductBySlug(slug)
const product = computed<Product | null>(() => Array.isArray(productData.value?.data) ? productData.value.data[0] : productData.value?.data || null)

// Lấy thông tin trang trại
const { data: farmData } = await getFarmById(Number(product.value?.farm_id) || -1, AuthType.Guest)
const farm = computed<Farm | null>(() => Array.isArray(farmData.value?.data) ? farmData.value.data[0] : farmData.value?.data || null)
const farmAddress = await getAddressNameById(farm.value?.address.ward?.toString() || '-1')
// Lấy thông tin logs
const logs = ref<{
    logs: CultivationLog[];
    meta: PaginationMeta | null;
    links: PaginationLinks | null;
    status: string;
}>({
    logs: [],
    meta: null,
    links: null,
    status: 'pending',
})

watch(product, async (newProduct) => {
    if (newProduct) {
        const { data: logsData, status: logsStatus } = await getLogs(Number(newProduct.id) || -1, currentLogPage.value, perLogPage)
        logs.value = {
            logs: Array.isArray(logsData.value?.data) ? (logsData.value.data as CultivationLog[]) : logsData.value?.data ? [logsData.value.data as CultivationLog] : [],
            meta: (logsData.value?.meta as PaginationMeta) ?? null,
            links: (logsData.value?.links as PaginationLinks) ?? null,
            status: logsStatus.value
        }
    }
}, { immediate: true })



const { data: otherProductsData, status: otherProductsStatus } = await getProducts()

const otherProducts = computed<{
    products: Product[];
    meta: PaginationMeta | null;
    links: PaginationLinks | null;
}>(() => ({
    products: Array.isArray(otherProductsData.value?.data) ? (otherProductsData.value.data as Product[]) : otherProductsData.value?.data ? [otherProductsData.value.data as Product] : [],
    meta: (otherProductsData.value?.meta as PaginationMeta) ?? null,
    links: (otherProductsData.value?.links as PaginationLinks) ?? null,
}));

// Quản lý ảnh đang chọn
const activeImageIndex = ref(0)
const activeImageUrl = computed(() => {
    const images = product.value?.images
    if (!images || images.length === 0) return placeholderImage
    return `${backendUrl}${images[activeImageIndex.value]?.image_url}` || placeholderImage
})

// Chuyển ảnh trước/sau
function prevImage() {
    if (product.value?.images && activeImageIndex.value > 0) {
        activeImageIndex.value--
    }
}

function nextImage() {
    if (product.value?.images && activeImageIndex.value < product.value.images.length - 1) {
        activeImageIndex.value++
    }
}

const handleLogsPageChange = (page: number) => {
    currentLogPage.value = page
    getLogs(product.value?.id || -1, page, perLogPage)
}

const handleProductsPageChange = (page: number) => {
    getProducts(page)
}
</script>