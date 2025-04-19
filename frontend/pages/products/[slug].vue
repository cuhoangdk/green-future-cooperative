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
                    <div class="max-w-7xl p-3 px-6 border border-gray-200 rounded-xl shadow-sm">
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
                                <span>{{
                                    product?.unit.name }}</span>
                            </p>
                        </div>
                        <div class="mb-3">
                            <p v-for="(price, index) in (product?.prices ?? []).slice().reverse()" :key="index"
                                class="text-3xl font-bold text-green-600">
                                {{ formatCurrency(price.price) }}
                                <span class="text-xl text-gray-500"> / {{ product?.unit.name }}</span>
                                <span  class="text-lg text-gray-500" v-if="price.quantity && (product?.prices?.length ?? 0) > 1">
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
                                <span class="text-gray-500">Ngày thu hoạch: </span>
                                {{ formatDate(product.harvested_at ?? '') }}
                            </p>
                        </div>
                        <hr class="border border-gray-200 mb-3">
                        <div class="mt-4 flex flex-col lg:flex-row gap-4 justify-center items-center md:justify-start">
                            <div class="flex items-center space-x-2">
                                <button @click="decreaseQuantity"
                                    class="btn btn-outline btn-primary w-[40px] h-[40px] p-0">
                                    <Minus />
                                </button>
                                <input v-model.number="quantity" type="number" min="1"
                                    class="text-sm input w-[100px] h-[40px] text-center" />
                                <button @click="increaseQuantity"
                                    class="btn btn-outline btn-primary w-[40px] h-[40px] p-0">
                                    <Plus />
                                </button>
                            </div>
                            <button @click="addProductToCart" :disabled="isAddingToCart"
                                class="bg-green-600 text-white font-semibold w-52 py-2 rounded-full hover:bg-green-700 flex items-center justify-center">
                                <span v-if="!isAddingToCart">Thêm vào giỏ hàng</span>
                                <span v-else class="flex items-center">
                                    <span class="loading loading-spinner loading-sm mr-2"></span> Đang thêm...
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="w-11/12 flex flex-col max-w-7xl mt-5 gap-5">
                <div class="w-full">
                    <div class="flex justify-between items-center mb-4 gap-3">
                        <h2 class="text-left text-xl font-bold text-green-800">Xuất sứ</h2>
                        <div class="flex-1 h-[3px] bg-green-500"></div>
                    </div>
                    <div class="flex flex-col md:flex-row gap-8 p-4 border border-gray-200 rounded-xl">
                        <div class="w-full md:w-1/2">
                            <div class="aspect-video rounded-lg overflow-hidden shadow-lg">
                                <UiMap :latitude="farm?.latitude ?? 0" :longitude="farm?.longitude ?? 0" />
                            </div>
                        </div>

                        <div class="w-full md:w-1/2">
                            <div class="flex flex-col gap-1">
                                <h3 class="text-xl font-semibold text-green-800">Hộ canh tác: {{ product.user?.full_name
                                }}</h3>
                                <p><strong>Địa chỉ:</strong> {{ address }}</p>
                                <p><strong>Kinh độ:</strong> {{ farm?.longitude }} <strong>- Vĩ độ:</strong> {{
                                    farm?.latitude }} </p>
                                <p><strong>Nhà cung cấp giống:</strong> {{ product?.seed_supplier }}</p>
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
                    <div class="flex justify-between items-center mb-4 gap-3">
                        <h2 class="text-left text-xl font-bold text-green-800">Quá trình chăm sóc</h2>
                        <div class="flex-1 h-[3px] bg-green-500"></div>
                    </div>
                    <div class="relative">
                        <div v-if="logs.status === 'pending'"
                            class="absolute inset-0 flex items-center justify-center bg-gray-100/20 z-10">
                            <span class="loading loading-spinner loading-xl"></span>
                        </div>
                    </div>
                    <div v-for="log in logs.logs" :key="log.id">
                        <div class="collapse collapse-arrow bg-base-100 border-green-500 border my-1.5">
                            <input type="checkbox" />
                            <div class="collapse-title font-semibold flex items-center ">
                                {{
                                    log.created_at ? new Date(log.created_at).toLocaleString('vi-VN', {
                                        hour: '2-digit', minute: '2-digit', day: '2-digit', month: '2-digit', year:
                                            'numeric'
                                    })
                                        : ''
                                }} - {{ log.activity }}
                            </div>
                            <div class="collapse-content text-sm">
                                <div class="flex flex-col space-y-2 mb-2">
                                    <div v-if="log.fertilizer_used"><span class="font-semibold">Phân bón:</span> {{
                                        log.fertilizer_used }}
                                    </div>
                                    <div v-if="log.pesticide_used"><span class="font-semibold">Thuốc bảo vệ thực
                                            vật:</span>
                                        {{
                                            log.pesticide_used
                                        }}</div>
                                    <div v-if="log.notes"><span class="font-semibold">Ghi chú:</span> {{ log.notes
                                    }}
                                    </div>
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
                    <UiPagination class="mt-4" :links="logs.links" :meta="logs.meta"
                        @page-change="handleLogsPageChange" />
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
import { EyeIcon, ArrowLeft, ArrowRight, Trash, Plus, Minus } from 'lucide-vue-next'

import type { Farm } from '~/types/farm'
import type { Product, CultivationLog } from '~/types/product'
import type { PaginationMeta, PaginationLinks } from '~/types/api'

const route = useRoute()
const slug = String(route.params.slug)
const config = useRuntimeConfig()
const backendUrl = config.public.backendUrl
const { $toast } = useNuxtApp()

const { getProductBySlug, getProducts } = useProducts()
const { getFarmById } = useFarms()
const { getLogs } = useCultivationLogs()
const { getFullAddressName } = useVietnamAddress()
const { addCartItem } = useCart()
const currentLogPage = ref(1)
const perLogPage = 10
const quantity = ref(1)
const isAddingToCart = ref(false)

const { data: productData, status: productStatus, error: productError } = await getProductBySlug(slug)
const product = computed<Product | null>(() => Array.isArray(productData.value?.data) ? productData.value.data[0] : productData.value?.data || null)

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
        address.value = await getFullAddressName(newFarm.address.ward)
    }
})

const logs = ref<{ logs: CultivationLog[]; meta: PaginationMeta | null; links: PaginationLinks | null; status: string; }>({ logs: [], meta: null, links: null, status: 'pending', })

watch(product, async (newProduct) => {
    if (newProduct) {
        const { data: logsData, status: logsStatus } = await getLogs(newProduct.id ?? '', currentLogPage.value, perLogPage)
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

const handleLogsPageChange = (page: number) => {
    currentLogPage.value = page
    getLogs(product.value?.id ?? '', page, perLogPage)
}

const handleProductsPageChange = (page: number) => {
    getProducts(page)
}

const increaseQuantity = () => {
    quantity.value++;
}

const decreaseQuantity = () => {
    if (quantity.value > 1) {
        quantity.value--;
    }
}

const addProductToCart = async () => {
    try {
        if (product.value) {
            isAddingToCart.value = true
            await addCartItem(product.value.id, quantity.value)
            $toast.success('Đã thêm sản phẩm vào giỏ hàng!')
        } else {
            $toast.error('Sản phẩm không tồn tại!')
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
            price: product.value?.prices?.[0]?.price || 0,
            availability: (product.value?.stock_quantity ?? 0) > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
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