<template>
    <div>
        <div v-if="productStatus === 'pending'" class="relative h-screen w-full flex items-center justify-center">
            <span class="loading loading-spinner loading-xl"></span>
        </div>
        <div v-else-if="product" class="min-h-screen items-center flex flex-col mt-16 pb-5 lg:mt-0">
            <div class="w-11/12 flex flex-col md:flex-row max-w-7xl mt-5 gap-5">
                <div class="w-full md:w-1/2">
                    <ProductImageSection :images="product?.images ?? []" />
                </div>
                <div class="w-full md:w-1/2">
                    <div class="max-w-7xl p-3 border border-gray-200 rounded-xl shadow-sm">
                        <div class="flex justify-between item-center mb-3">
                            <h1 class="text-2xl font-bold">{{ product?.name }}</h1>
                            <div class="flex gap-1">
                                <EyeIcon class="w-6 h-6 text-gray-500" /> {{ product?.views }}
                            </div>
                        </div>
                        <div class="flex justify-between mb-3">
                            <p class="text-sm text-gray-500"><span>Mã: </span>{{ product?.id }}</p>
                            <p class="text-sm text-gray-500"><span>Trong kho: </span>{{
                                formatNumber(product?.stock_quantity) }}
                                <span>{{ product?.unit.name }}</span>
                            </p>
                        </div>
                        <div class="mb-3">
                            <p v-if="product?.status === 'stopped'" class="text-3xl font-bold text-red-600">
                                Ngưng bán
                            </p>
                            <p v-else-if="product?.pricing_type === 'contact'" class="text-3xl font-bold text-red-600">
                                Liên hệ
                            </p>
                            <p v-else-if="product?.stock_quantity == 0" class="text-3xl font-bold text-red-600">
                                Hết hàng
                            </p>
                            <p v-else v-for="(price, index) in (product?.prices ?? []).slice().reverse()" :key="index"
                                class="text-3xl font-bold text-green-600">
                                {{ formatCurrency(price.price) }}
                                <span class="text-xl text-gray-500"> / {{ product?.unit.name }}</span>
                                <span class="text-lg text-gray-500"
                                    v-if="price.quantity && (product?.prices?.length ?? 0) > 1">
                                    (Từ {{ formatNumber(price.quantity) }} {{ product.unit.name }})
                                </span>
                            </p>
                        </div>
                        <hr class="border border-gray-200 mb-3">

                        <p class="text-gray-600 mb-3 h-24 overflow-y-auto">{{ product?.description }}</p>

                        <hr class="border border-gray-200 mb-3">

                        <div class="grid grid-cols-1 lg:grid-cols-2 mb-3 gap-1">
                            <p class="text-gray-900"><span class="text-gray-500">Ngày gieo trồng: </span>{{
                                formatDate(product.sown_at) }}</p>
                            <p class="text-gray-900">
                                <span class="text-gray-500">Bắt đầu thu hoạch: </span>
                                {{ formatDate(product.harvested_at ?? '') }}
                            </p>
                        </div>
                        <hr class="border border-gray-200 mb-3">
                        <div class="mt-4 flex flex-col lg:flex-row gap-4 justify-center items-center md:justify-start">
                            <div v-if="ableToAddToCart" class="join">
                                <button @click="decreaseQuantity"
                                    class="btn btn-outline btn-primary join-item rounded-l-full w-[40px] h-[40px] p-0"
                                    :disabled="quantity <= 1">
                                    <Minus />
                                </button>
                                <input v-model.number="quantity" type="number" min="1" :max="product?.stock_quantity"
                                    class="input input-bordered join-item w-[100px] h-[40px] text-center"
                                    :class="{ 'input-error': quantity > product?.stock_quantity }"
                                    :step="product?.unit?.allow_decimal ? 'any' : '1'"
                                    @input="!product?.unit?.allow_decimal && (quantity = Math.floor(quantity))" />
                                <button @click="increaseQuantity"
                                    class="btn btn-outline btn-primary join-item rounded-r-full w-[40px] h-[40px] p-0"
                                    :disabled="quantity >= product?.stock_quantity">
                                    <Plus />
                                </button>
                            </div>
                            <div v-if="!ableToAddToCart" class="w-48">
                                <ProductContactButton />
                            </div>
                            <ProductBuyButton v-else :addProductToCart="addProductToCart" :quantity="quantity"
                                :isAddingToCart="isAddingToCart" :stock-quantity="product.stock_quantity" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="w-11/12 flex flex-col max-w-7xl mt-5 gap-5">
                <div class="w-full">
                    <div class="flex justify-between items-center mb-4 gap-3">
                        <h2 class="text-left text-xl font-bold text-green-800">Xuất xứ</h2>
                        <div class="flex-1 h-[3px] bg-green-500"></div>
                    </div>
                    <div class="flex flex-col md:flex-row gap-8 p-4 border border-gray-200 rounded-xl">
                        <div class="w-full md:w-1/2">
                            <UiMap :latitude="farm?.latitude ?? 0" :longitude="farm?.longitude ?? 0" />
                        </div>

                        <div class="w-full md:w-1/2">
                            <div class="flex flex-col gap-1">
                                <h3 class="text-xl font-semibold text-green-800">Hộ canh tác: {{ product.user?.full_name
                                }}</h3>
                                <p><strong>Địa chỉ:</strong> {{ address }}</p>
                                <p><strong>Kinh độ:</strong> {{ farm?.longitude }} <strong>- Vĩ độ:</strong> {{
                                    farm?.latitude }} </p>
                                <p><strong>Nhà cung cấp giống:</strong> {{ product?.seed_supplier }}</p>
                                <p><strong>Loại đất:</strong> {{ farm?.soil_type }}</p>
                                <p><strong>Diện tích canh tác:</strong> {{ formatNumber(product?.cultivated_area) }} m²
                                </p>
                                <p>{{ farm?.description }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="w-11/12 flex flex-col max-w-7xl mt-5 gap-5">
                <div class="w-full">
                    <ProductLogList :product-id="product.id"/>
                </div>
                <div class="w-full">
                    <div class="flex justify-between items-center mb-4 gap-3">
                        <h2 class="text-left text-xl font-bold text-green-800">Sản phẩm khác</h2>
                        <div class="flex-1 h-[3px] bg-green-500"></div>
                        <NuxtLink to="/products"
                            class="bg-green-600 text-white font-semibold px-5 py-1 rounded-full hover:bg-green-700">
                            Xem tất cả
                        </NuxtLink>
                    </div>
                    <ProductList :products="otherProducts.products" :meta="otherProducts.meta"
                        :links="otherProducts.links" :status="otherProductsStatus"
                        @page-change="handleProductsPageChange" />
                </div>
            </div>
        </div>
        <div v-else class="relative h-screen w-full flex items-center justify-center">
            Sản phẩm không tồn tại
        </div>
    </div>
</template>

<script setup lang="ts">
import { EyeIcon, Plus, Minus } from 'lucide-vue-next'
import type { Farm } from '~/types/farm'
import type { Product } from '~/types/product'
import type { PaginationMeta, PaginationLinks } from '~/types/api'

const route = useRoute()
const slug = String(route.params.slug)
const config = useRuntimeConfig()
const backendUrl = config.public.backendUrl
const { $toast } = useNuxtApp()

const { getProductBySlug, getProducts } = useProducts()
const { getFarmById } = useFarms()
const { getFullAddressName } = useVietnamAddress()
const { addCartItem } = useCart()
const quantity = ref(1)
const isAddingToCart = ref(false)

const { data: productData, status: productStatus, error: productError } = await getProductBySlug(slug)
const product = computed<Product | null>(() => Array.isArray(productData.value?.data) ? productData.value.data[0] : productData.value?.data || null)

const ableToAddToCart = computed(() => {
    return product.value?.status !== 'stopped' && (product.value?.stock_quantity ?? 0) > 0 && product.value?.pricing_type !== 'contact'
})

const farm = ref<Farm | null>(null)
watch(product, async (newProduct) => {
    if (newProduct?.farm_id) {
        const { data } = await getFarmById(Number(newProduct.farm_id))
        farm.value = data.value?.data || null
    }
}, { immediate: true })

const address = ref('')
watch(farm, async (newFarm) => {
    if (newFarm?.address?.ward) {
        address.value = `${newFarm.address.street_address}, ${await getFullAddressName(newFarm.address.ward)}`
    }
})

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


const handleProductsPageChange = (page: number) => {
    getProducts(page)
}

const increaseQuantity = () => {
    if (quantity.value < (product.value?.stock_quantity ?? 0)) {
        quantity.value++;
    }
}

const decreaseQuantity = () => {
    if (quantity.value > 1) {
        quantity.value--;
    }
}

const addProductToCart = async () => {
    try {
        if (product.value && quantity.value <= product.value.stock_quantity) {
            isAddingToCart.value = true
            const { status } = await addCartItem(product.value.id, quantity.value)
            if (status.value === 'success') {
                $toast.success('Đã thêm sản phẩm vào giỏ hàng!')
            } else {
                $toast.error('Có lỗi xảy ra khi thêm sản phẩm vào giỏ hàng!')
            }
        } else {
            $toast.error('Số lượng vượt quá tồn kho!')
        }
    } catch (error) {
        $toast.error('Có lỗi khi thêm sản phẩm vào giỏ hàng!')
    } finally {
        isAddingToCart.value = false
    }
}

// SEO với useHead
useHead({
    title: computed(() => product.value ? `${product.value.name} - Green Future Cooperative` : 'Sản phẩm - Green Future Cooperative'),
    meta: [
        {
            name: 'description',
            content: computed(() => product.value?.description?.slice(0, 160) || 'Mua rau củ organic tươi ngon từ Green Future Cooperative. Giao hàng nhanh tại Hà Nội và TP.HCM.'),
        },
        {
            name: 'keywords',
            content: computed(() => product.value ? `${product.value.name}, rau củ organic, rau sạch, ${product.value.seed_supplier || ''}, Green Future Cooperative` : 'rau củ organic, rau sạch, mua rau online'),
        },
        {
            property: 'og:title',
            content: computed(() => product.value ? `${product.value.name} - Green Future Cooperative` : 'Sản phẩm - Green Future Cooperative'),
        },
        {
            property: 'og:description',
            content: computed(() => product.value?.description?.slice(0, 160) || 'Mua rau củ organic tươi ngon từ Green Future Cooperative.'),
        },
        {
            property: 'og:image',
            content: computed(() => product.value?.images?.[0]?.image_url ? `${backendUrl}${product.value.images[0].image_url}` : config.public.placeholderImage),
        },
        {
            property: 'og:url',
            content: `${config.public.baseUrl}/products/${slug}`,
        },
        {
            name: 'twitter:card',
            content: 'summary_large_image',
        },
        {
            name: 'twitter:title',
            content: computed(() => product.value ? `${product.value.name} - Green Future Cooperative` : 'Sản phẩm - Green Future Cooperative'),
        },
        {
            name: 'twitter:description',
            content: computed(() => product.value?.description?.slice(0, 160) || 'Mua rau củ organic tươi ngon từ Green Future Cooperative.'),
        },
        {
            name: 'twitter:image',
            content: computed(() => product.value?.images?.[0]?.image_url ? `${backendUrl}${product.value.images[0].image_url}` : config.public.placeholderImage),
        },
    ],
    link: [
        {
            rel: 'canonical',
            href: `${config.public.baseUrl}/products/${slug}`,
        },
    ],
    script: [
        {
            type: 'application/ld+json',
            innerHTML: computed(() =>
                JSON.stringify({
                    '@context': 'https://schema.org',
                    '@type': 'Product',
                    name: product.value?.name || 'Sản phẩm rau củ',
                    image: product.value?.images?.[0]?.image_url ? `${backendUrl}${product.value.images[0].image_url}` : config.public.placeholderImage,
                    description: product.value?.description || 'Rau củ organic tươi ngon từ Green Future Cooperative.',
                    sku: product.value?.id || '',
                    brand: {
                        '@type': 'Brand',
                        name: 'Green Future Cooperative',
                    },
                    offers: {
                        '@type': 'Offer',
                        priceCurrency: 'VND',
                        price: product.value?.status === 'stopped' || product.value?.stock_quantity === 0 ? 0 : product.value?.prices?.[0]?.price || 0,
                        availability: (product.value?.stock_quantity ?? 0) > 0 && product.value?.status !== 'stopped' ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
                        url: `${config.public.baseUrl}/products/${slug}`,
                    },
                    additionalProperty: [
                        {
                            '@type': 'PropertyValue',
                            name: 'Sown At',
                            value: product.value?.sown_at ? formatDate(product.value.sown_at) : '',
                        },
                        {
                            '@type': 'PropertyValue',
                            name: 'Harvested At',
                            value: product.value?.harvested_at ? formatDate(product.value.harvested_at) : '',
                        },
                        {
                            '@type': 'PropertyValue',
                            name: 'Cultivated Area',
                            value: product.value?.cultivated_area ? `${formatNumber(product.value.cultivated_area)} m²` : '',
                        },
                        {
                            '@type': 'PropertyValue',
                            name: 'Seed Supplier',
                            value: product.value?.seed_supplier || '',
                        },
                    ],
                })
            ),
        },
    ],
})
</script>