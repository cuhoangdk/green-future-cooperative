<template>
    <div
        class="bg-white border border-green-100 shadow-sm rounded-2xl overflow-hidden hover:shadow-md hover:-translate-y-1 duration-200 relative z-0 h-full flex flex-col">
        <NuxtLink :to="`/products/${product.slug}`" class="flex flex-col flex-grow">
            <img :src="`${img}`" :alt="product.name" class="rounded-lg aspect-[4/3] rounded-b-none object-cover w-full"
                loading="lazy" @error="() => { img = placeholderImage; }" />
            <div class="text-center px-3 flex-grow">
                <h2 class="text-lg font-semibold text-green-800">{{ product.name }}</h2>
                <p v-if="product?.stock_quantity == 0" class="font-bold text-red-600">
                    Hết hàng
                </p>
                <p v-else-if="product.pricing_type == 'contact'" class="font-bold text-red-600">
                    Liên hệ
                </p>
                <p v-else-if="product?.prices?.[0]?.price" class="font-bold text-red-600">
                    {{ formatCurrency(product.prices[0].price) }}
                    <span class="text-sm text-gray-500">/ {{ product.unit.name }}</span>
                </p>
            </div>
        </NuxtLink>

        <div class="px-3 pb-2">
            <button v-if="product?.stock_quantity > 0 && product?.pricing_type != 'contact'" @click="openModal"
                class="btn bg-green-600 text-white w-full rounded-full hover:bg-green-700 flex gap-2">
                <ShoppingCart class="w-5 h-5 mr-1" />
                Mua
            </button>

            <ProductContactButton v-else />
            <!-- <p class="text-xs text-center text-gray-500 mt-1">Người trồng: {{ product.user?.full_name }}</p> -->
        </div>

        <!-- Modal with animations -->
        <Teleport to="body">
            <transition enter-active-class="transition duration-300 ease-out" enter-from-class="opacity-0"
                enter-to-class="opacity-100" leave-active-class="transition duration-200 ease-in"
                leave-from-class="opacity-100" leave-to-class="opacity-0">
                <div v-if="showModal" @click="closeModalOnBackdrop"
                    class="fixed inset-0 flex items-center justify-center z-50" :class="{ 'touch-none': showModal }">
                    <!-- Custom overlay with opacity -->
                    <div class="fixed inset-0 bg-black opacity-60"></div>

                    <!-- Modal content with its own animation -->
                    <transition enter-active-class="transition duration-300 ease-out"
                        enter-from-class="opacity-0 scale-95" enter-to-class="opacity-100 scale-100"
                        leave-active-class="transition duration-200 ease-in" leave-from-class="opacity-100 scale-100"
                        leave-to-class="opacity-0 scale-95">
                        <div v-if="showModal" @click.stop
                            class="bg-white rounded-2xl shadow-2xl p-4 w-full max-w-lg mx-1 relative z-10">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-xl font-semibold text-green-800">{{ product.name }}</h3>
                                <button @click="closeModal" class=" text-gray-500 hover:text-gray-700">
                                    <X class="w-6 h-6" />
                                </button>
                            </div>

                            <div
                                class="flex flex-col md:flex-row justify-center items-center md:justify-start md:items-start mb-6 gap-4">
                                <img :src="img" :alt="product.name" class="w-44 h-44 object-cover rounded-lg" />
                                <div>
                                    <div class="flex justify-between gap-2 mb-2">
                                        <p class="text-sm text-gray-700">
                                            Mã: <span class="font-medium">{{ product.id }}</span>
                                        </p>
                                        <p class="text-sm text-gray-700">
                                            Kho: <span class="font-medium">{{ formatNumber(product.stock_quantity) }} {{
                                                product.unit.name }}</span>
                                        </p>
                                    </div>
                                    <div v-if="product?.prices?.length">
                                        <p v-for="(price, index) in product.prices" :key="index"
                                            class="text-green-600 font-bold">
                                            {{ formatCurrency(price.price) }}
                                            <span class="text-sm text-gray-500">/ {{ product.unit.name }}</span>
                                            <span v-if="price.quantity && product.prices.length > 1"
                                                class="text-sm text-gray-500">
                                                (Từ {{ formatNumber(price.quantity) }} {{ product.unit.name }})
                                            </span>
                                        </p>
                                    </div>
                                    <p v-else class="text-red-600 font-bold">
                                        Liên hệ
                                    </p>
                                </div>
                            </div>

                            <div class="flex flex-col lg:flex-row justify-center items-center gap-4">
                                <template v-if="isAuthenticated">
                                    <div>
                                        <div class="join">
                                            <button @click="decreaseQuantity"
                                                class="btn btn-outline btn-primary join-item rounded-l-full w-[40px] h-[40px] p-0"
                                                :disabled="quantity <= 1">
                                                <Minus />
                                            </button>
                                            <input v-model.number="quantity" type="number" min="1"
                                                :max="product?.stock_quantity"
                                                class="input join-item w-[100px] h-[40px] text-center"
                                                :class="{ 'input-error': quantity > product?.stock_quantity }"
                                                :step="product?.unit?.allow_decimal ? 'any' : '1'"
                                                @input="!product?.unit?.allow_decimal && (quantity = Math.floor(quantity))" />
                                            <button @click="increaseQuantity"
                                                class="btn btn-outline btn-primary join-item rounded-r-full w-[40px] h-[40px] p-0"
                                                :disabled="quantity >= product?.stock_quantity">
                                                <Plus />
                                            </button>
                                        </div>
                                    </div>
                                    <ProductBuyButton :addProductToCart="addProductToCart" :quantity="quantity"
                                        :isAddingToCart="isAddingToCart" :stock-quantity="product.stock_quantity" />
                                </template>
                                <template v-else>
                                    <div class="flex items-center justify-center gap-3 w-full">
                                        <div class="text-red-600 font-bold">Bạn cần đăng nhập để đặt hàng!</div>
                                        <NuxtLink to="/login" class="btn btn-primary rounded-full">
                                            Đăng nhập
                                        </NuxtLink>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </transition>
                </div>
            </transition>
        </Teleport>
    </div>
</template>

<script setup lang="ts">
import { ShoppingCart, Plus, Minus, X } from 'lucide-vue-next'
import type { Product } from "~/types/product";

interface Props {
    product: Product;
}

const { $toast } = useNuxtApp()
const { addCartItem } = useCart();
const { isAuthenticated } = useCustomerAuth();
const config = useRuntimeConfig();
const placeholderImage = config.public.placeholderImage;
const backendUrl = config.public.backendUrl;
const props = defineProps<Props>();
const img = ref(`${backendUrl}${props.product.images?.[0]?.image_url ?? placeholderImage}`);
const isAddingToCart = ref(false);
const showModal = ref(false);

// Ngăn cuộn trang khi modal mở
watch(showModal, (value) => {
    if (value) {
        // document.body.style.overflow = 'hidden';
        document.body.style.touchAction = 'none'; // Chặn touch events cho mobile
    } else {
        // document.body.style.overflow = '';
        document.body.style.touchAction = ''; // Khôi phục touch events
    }
});

const openModal = () => {
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
};

const closeModalOnBackdrop = (event: { target: any; currentTarget: any; }) => {
    if (event.target === event.currentTarget) {
        closeModal();
    }
};

const addProductToCart = async () => {
    try {
        if (!isAuthenticated.value) {
            $toast.error('Vui lòng đăng nhập để thêm sản phẩm vào giỏ hàng!')
            return
        }
        isAddingToCart.value = true;
        await addCartItem(props.product.id, quantity.value);
        $toast.success('Đã thêm sản phẩm vào giỏ hàng!');
        closeModal();
    } catch (error) {
        $toast.error('Có lỗi khi thêm sản phẩm vào giỏ hàng!');
    } finally {
        isAddingToCart.value = false;
    }
};

const quantity = ref(1);

const increaseQuantity = () => {
    quantity.value += 1;
};

const decreaseQuantity = () => {
    if (quantity.value > 1) {
        quantity.value -= 1;
    }
};

// Thêm sự kiện keydown để đóng modal khi nhấn Escape
onMounted(() => {
    window.addEventListener('keydown', handleEscapeKey);
});


const handleEscapeKey = (e: { key: string; }) => {
    if (e.key === 'Escape' && showModal.value) {
        closeModal();
    }
};
</script>