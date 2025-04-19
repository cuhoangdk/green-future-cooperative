<template>
    <div class="min-h-screen p-2">
        <div class="flex item-center justify-center gap-5 mb-3">
            <h1 class="w-full lg:w-10/12 text-2xl font-bold text-green-800">Giỏ hàng</h1>
        </div>
        <div v-if="isFirstLoad && status === 'pending'" class="text-center text-gray-500 h-96 flex flex-col justify-center items-center">
            <span class="loading loading-spinner loading-xl"></span>
        </div>
        <div v-else-if="status === 'error'" class="text-center text-gray-500 h-96 flex flex-col justify-center items-center">
            <p class="text-xl text-red-500">Có lỗi xảy ra khi tải giỏ hàng của bạn.</p>
        </div>
        <div v-else-if="cartItems && cartItems.length > 0" class="flex flex-col justify-center lg:flex-row gap-5">
            <div class="w-full lg:w-5/12 flex flex-col gap-2">
                <CartCard v-for="cartItem in cartItems" :cartItem="cartItem" :key="cartItem.id"
                    @item-removed="handleItemRemoved" @quantity-updated="handleQuantityUpdated" />
            </div>
            <div class="w-full lg:w-5/12">
                <div class="flex flex-col gap-1">
                    <div class="bg-green-200 font-bold p-3 rounded-t-xl flex justify-between items-center">
                        <p>Tiền hàng:</p><span>{{ formatCurrency(totalPrice) }}</span>
                    </div>
                    <div class="bg-green-300 font-bold p-3 flex justify-between items-center">
                        <div>
                            <p>Vận chuyển (COD): </p>
                            <p class="text-sm text-gray-500">(Nội tỉnh Bạc Liêu)</p>
                        </div>
                        <p>{{ formatCurrency(Number(shippingFe?.value)) }}</p>
                    </div>
                    <div class="bg-green-500 rounded-b-xl font-bold p-5">
                        <div class="flex justify-between items-center">
                            <p>Tổng cộng: </p>
                            <span>{{ formatCurrency(totalPrice + 5000) }}</span>
                        </div>
                        <button @click="navigateToShippingInformation" class="text-center bg-white font-semibold text-green-600 px-4 py-2 rounded-full hover:bg-green-100 transition-colors duration-200 w-full mt-10">Tiếp tục thanh
                                toán
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div v-else class="text-center text-gray-500 h-96 flex flex-col justify-center items-center">
            <ShoppingCart class="w-20 h-20 mx-auto mb-4" />
            <p>Giỏ hàng của bạn đang trống.</p>
            <NuxtLink to="/products" class="text-green-600 hover:underline">Tiếp tục mua sắm</NuxtLink>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ShoppingCart } from 'lucide-vue-next';
import type { CartItem } from '~/types/cart';
import type { Parameter } from '~/types/parameter';

const { getCartItems } = useCart();
const { getShippingFee } = useShippingFee();

// Thêm biến theo dõi lần tải đầu tiên
const isFirstLoad = ref(true);

const { data, status, refresh } = await getCartItems();
const cartItems = computed<CartItem[]>(() => 
    Array.isArray(data.value?.data) ? data.value.data : data.value ? [data.value.data] : []
);

const { data: dataShippingFee } = await getShippingFee()
const shippingFe = computed<Parameter | null>(() => Array.isArray(dataShippingFee.value?.data) ? dataShippingFee.value.data[0] : dataShippingFee.value?.data || null)

const totalPrice = computed(() => {
    return cartItems.value.reduce((sum, item) => sum + item.purchase_price * item.quantity, 0);
});


// Xử lý khi sản phẩm bị xóa
const handleItemRemoved = () => {
    refresh();
};

// Xử lý khi số lượng được cập nhật
const handleQuantityUpdated = () => {
    refresh();
};

// Khi dữ liệu tải xong lần đầu tiên
watch(status, (newStatus) => {
    if (newStatus !== 'pending') {
        isFirstLoad.value = false;
    }
});

const navigateToShippingInformation = () => {
    const router = useRouter();
    router.push('/shipping-information');
};
</script>