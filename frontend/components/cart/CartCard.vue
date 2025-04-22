<template>
    <div v-if="isVisible" class="bg-white border border-gray-300 rounded-xl flex flex-col md:flex-row md:items-center">
        <!-- Product Image -->
        <div class="w-full md:w-34 md:h-34 flex-shrink-0">
            <img v-if="cartItem.product.images?.[0]?.image_url" :src="backendUrl + cartItem.product.images[0].image_url"
                alt="Product Image" class="w-full h-34 object-cover rounded-t-xl md:rounded-t-none md:rounded-l-xl" />
            <div v-else class="w-full h-34 bg-green-100 flex items-center justify-center rounded-t-xl md:rounded-t-none md:rounded-l-xl">
                <span class="text-green-500">No Image</span>
            </div>
        </div>

        <!-- Product Details -->
        <div class="flex-grow p-3">
            <div class="flex justify-between items-center">
                <NuxtLink :to="`/products/${cartItem.product.slug}`" class="text-lg md:text-xl font-bold text-green-800 hover:underline">
                    {{ cartItem.product.name }}
                </NuxtLink>
                <button @click="removeItem" class="btn btn-accent text-white rounded-full w-8 h-8 p-1.5 flex-shrink-0">
                    <Trash class="w-5 h-5" />
                </button>
            </div>

            <!-- <p class="text-sm text-gray-600">{{ cartItem.product.product_code }}</p> -->
            <p class="my-1 font-semibold text-green-700">
                {{ formatPrice(cartItem.purchase_price) }} / {{ cartItem.product.unit.name }}
            </p>
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center">
                <div class="join">
                    <button @click="decreaseQuantity" class="btn btn-outline btn-sm btn-primary btn-square join-item rounded-l-full">
                        <Minus class="w-5 h-5" />
                    </button>
                    <input v-model.number="localQuantity" @change="debouncedUpdateQuantity" type="number" min="1"
                        class="text-sm input input-sm w-24 text-center join-item" />
                    <button @click="increaseQuantity" class="btn btn-outline btn-sm btn-primary btn-square join-item rounded-r-full">
                        <Plus class="w-5 h-5" />
                    </button>
                </div>
                <p class="text-lg md:text-xl font-bold text-green-800">
                    {{ formatPrice(cartItem.purchase_price * localQuantity) }}
                </p>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { Trash, Plus, Minus } from 'lucide-vue-next'
import type { CartItem } from "~/types/cart";
import { debounce } from 'lodash' // Import debounce từ lodash

interface Props {
    cartItem: CartItem;
}

const emit = defineEmits(['item-removed', 'quantity-updated']);
const { $toast } = useNuxtApp()
const { deleteCartItem, updateCartItem } = useCart();
const config = useRuntimeConfig();
const backendUrl = config.public.backendUrl;
const props = defineProps<Props>();
const img = ref(`${backendUrl}${props.cartItem.product.images?.[0]?.image_url ?? config.public.placeholderImage}`);
const isVisible = ref(true);
const localQuantity = ref(props.cartItem.quantity);

const formatPrice = (price: number) => {
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(price);
}

const removeItem = async () => {
    try {
        await deleteCartItem(props.cartItem.id);
        isVisible.value = false;
        $toast.success('Sản phẩm đã được xóa!');
        emit('item-removed');
    } catch (error) {
        $toast.error('Có lỗi xảy ra khi xóa sản phẩm!');
    }
}

const increaseQuantity = () => {
    localQuantity.value++;
    debouncedUpdateQuantity(); // Gọi phiên bản debounce
}

const decreaseQuantity = () => {
    if (localQuantity.value > 1) {
        localQuantity.value--;
        debouncedUpdateQuantity(); // Gọi phiên bản debounce
    }
}

// Hàm updateQuantity được debounce với thời gian chờ 500ms
const debouncedUpdateQuantity = debounce(async () => {
    try {
        if (localQuantity.value < 1) {
            localQuantity.value = 1;
        }
        const { error } = await updateCartItem(props.cartItem.id, localQuantity.value);
        if (error.value) throw new Error('Thông tin sản phẩm không hợp lệ');

        $toast.success('Đã cập nhật số lượng!');
        emit('quantity-updated');
    } catch (error) {
        $toast.error('Có lỗi khi cập nhật số lượng!');
        localQuantity.value = props.cartItem.quantity; // Khôi phục giá trị cũ nếu lỗi
    }
}, 500); // Chờ 500ms sau lần nhấp cuối cùng mới gọi API
</script>