<template>
    <div class="min-h-screen p-2 mb-10">
        <div v-if="status === 'pending'"
            class="text-center text-gray-500 h-96 flex flex-col justify-center items-center">
            <span class="loading loading-spinner loading-xl"></span>
        </div>
        <div v-else-if="status === 'error'"
            class="text-center text-gray-500 h-96 flex flex-col justify-center items-center">
            <p class="text-xl text-red-500">Có lỗi xảy ra khi tải dữ liệu</p>
        </div>
        <form @submit.prevent="handleSubmit" v-else-if="cartItems && cartItems.length > 0"
            class="flex flex-col justify-center lg:flex-row gap-5">
            <div class="w-full lg:w-5/12 flex flex-col gap-3">
                <div class="flex justify-between items-center">
                    <h1 class="w-full text-2xl font-bold text-green-800">Thông tin giao hàng</h1>
                    <div class="btn btn-sm" onclick="my_modal_1.showModal()">Chọn địa chỉ</div>
                </div>
                <div class="border border-gray-300 rounded-xl p-5 bg-white">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <input v-model="form.full_name" class="input input-bordered input-primary w-full"
                                placeholder="Họ và tên" required />
                        </div>

                        <div class="sm:col-span-2">
                            <input v-model="form.phone_number" type="tel"
                                class="input input-bordered input-primary w-full" placeholder="Số điện thoại"
                                required />
                        </div>
                        <!-- Province -->
                        <div class="col-span-2">
                            <select v-model="form.province"
                                @change="(event) => fetchDistricts((event.target as HTMLSelectElement).value)"
                                class="select select-bordered select-primary w-full" required>
                                <option value="" disabled>Tỉnh/thành phố</option>
                                <option v-for="p in provinces" :key="p.id" :value="p.id">
                                    {{ p.full_name }}
                                </option>
                            </select>
                        </div>

                        <!-- District -->
                        <div>
                            <select v-model="form.district"
                                @change="(event) => fetchWards((event.target as HTMLSelectElement).value)"
                                class="select select-bordered select-primary w-full" required>
                                <option value="" disabled>Quận/huyện</option>
                                <option v-for="d in districts" :key="d.id" :value="d.id">
                                    {{ d.full_name }}
                                </option>
                            </select>
                        </div>

                        <!-- Ward -->
                        <div>
                            <select v-model="form.ward" class="select select-bordered select-primary w-full" required>
                                <option value="" disabled>Phường/xã</option>
                                <option v-for="w in wards" :key="w.id" :value="w.id">
                                    {{ w.full_name }}
                                </option>
                            </select>
                        </div>

                        <!-- Street Address (Full Width) -->
                        <div class="sm:col-span-2">
                            <input v-model="form.street_address" class="input input-bordered input-primary w-full"
                                placeholder="Số nhà, tên đường..." required />
                        </div>
                        <div class="sm:col-span-2">
                            <select v-model="form.address_type" class="select select-bordered select-primary w-full"
                                required>
                                <option value="" disabled>Loại địa chỉ</option>
                                <option value="home">Nhà riêng</option>
                                <option value="work">Cơ quan</option>
                                <option value="other">Khác</option>
                            </select>
                        </div>

                        <div class="sm:col-span-2">
                            <textarea v-model="form.notes" class="textarea textarea-bordered textarea-primary w-full"
                                placeholder="Ghi chú thêm về đơn hàng"></textarea>
                        </div>
                    </div>
                </div>
                <h1 class="w-full text-2xl font-bold text-green-800">Phương thức giao hàng</h1>
                <div class="flex justify-between border border-gray-300 rounded-xl p-5 bg-white">
                    <div class="tooltip"
                        data-tip="Sản phẩm sẽ được thu hoạch và giao cho bạn trong khoảng thời gian từ 1-2 ngày">
                        <label class="flex gap-2"><input type="checkbox" checked class="checkbox" disabled />Tốc độ tiêu
                            chuẩn (từ 1 - 2 ngày)</label>
                    </div>
                    <div>{{ formatCurrency(Number(shippingFe?.value)) }}</div>
                </div>
                <h1 class="w-full text-2xl font-bold text-green-800">Phương thức thanh toán</h1>
                <div class="flex justify-between border border-gray-300 rounded-xl p-5 bg-white">
                    <div class="tooltip" data-tip="Là phương thức thanh toán bằng tiền mặt trực tiếp khi nhận hàng">
                        <label class="flex gap-2"><input type="checkbox" checked class="checkbox" disabled />Thanh toán
                            trực tiếp khi giao hàng</label>
                    </div>
                </div>
            </div>
            <div class="w-full lg:w-5/12">
                <h1 class="w-full text-2xl font-bold text-green-800 mb-3">Đơn hàng</h1>

                <div class="flex flex-col gap-1">
                    <div class="bg-green-200 font-bold p-3 rounded-t-xl">
                        <div class="border-b border-gray-400 border-dashed">
                            <div v-for="cartItem in cartItems" :key="cartItem.id"
                                class="flex justify-between items-center my-1 pb-2">
                                <div class="font-semibold">{{ cartItem.product.name }}</div>
                                <div class="text-gray-700 "> {{ cartItem.quantity }} {{ cartItem.product.unit.name }} x
                                    {{ formatCurrency(cartItem.purchase_price) }}</div>
                            </div>
                        </div>
                        <div class=" flex justify-between items-center mt-3">
                            <p>Tiền hàng:</p><span>{{ formatCurrency(totalPrice) }}</span>
                        </div>
                    </div>
                    <div class="bg-green-300 font-bold p-3 flex justify-between items-center">
                        <div>
                            <p>Vận chuyển (COD): </p>
                            <p class="text-sm text-gray-500">(Nội tỉnh Bạc Liêu)</p>
                        </div>
                        <div>{{ formatCurrency(Number(shippingFe?.value)) }}</div>
                    </div>
                    <div class="bg-green-500 rounded-b-xl font-bold p-3">
                        <div class="flex justify-between items-center">
                            <p>Tổng cộng: </p>
                            <span>{{ formatCurrency(totalPrice + Number(shippingFe?.value || 0)) }}</span>
                        </div>
                        <button type="submit" :disabled="submit"
                            class="text-center bg-white font-semibold text-green-600 px-4 py-2 rounded-full hover:bg-green-100 transition-colors duration-200 w-full mt-10">
                            <span v-if="submit" class="loading loading-spinner loading-sm ml-2"></span>
                            <span v-else>Hoàn tất đặt hàng</span>
                        </button>
                    </div>
                </div>
            </div>
        </form>
        <div v-else class="text-center text-gray-500 h-96 flex flex-col justify-center items-center">
            <ShoppingCart class="w-20 h-20 mx-auto mb-4" />
            <p>Giỏ hàng của bạn đang trống.</p>
            <nuxt-link to="/products" class="text-green-600 hover:underline">Tiếp tục mua sắm</nuxt-link>
        </div>

        <dialog id="my_modal_1" class="modal">
            <div class="modal-box">
                <h3 class="text-lg font-bold mb-2">Danh sách địa chỉ</h3>
                <div v-for="address in addresses" :key="address.id">
                    <form method="dialog">
                        <!-- if there is a button in form, it will close the modal -->
                        <button class="w-full">                    
                            <UiAddressCard @click="handleAddressChange(address)" :address="address"
                            :addressDetailId="address.address.ward" /></button>
                    </form>

                </div>
                <div class="modal-action">
                    <form method="dialog">
                        <!-- if there is a button in form, it will close the modal -->
                        <button class="btn">Close</button>
                    </form>
                </div>
            </div>
        </dialog>
    </div>
</template>

<script setup lang="ts">
import type { CartItem } from '../types/cart';
import { ShoppingCart } from 'lucide-vue-next';
import type { CustomerAddress } from '~/types/customer';
import type { Parameter } from '~/types/parameter'; 
const { getCustomerAddress } = useCustomerAddress();

const { provinces, districts, wards, fetchProvinces, fetchDistricts, fetchWards } = useVietnamAddress();
const { getCartItems } = useCart();
const { addOrder } = useOrder();
const { getShippingFee } = useShippingFee()

const { $toast } = useNuxtApp()
const router = useRouter();
const submit = ref(false);

// Khởi tạo form với các giá trị mặc định
const form = ref({
    full_name: '',
    phone_number: '',
    province: '',
    district: '',
    ward: '',
    street_address: '',
    notes: '',
    address_type: '',
})


const { data, status, refresh } = await getCartItems();
const cartItems = computed<CartItem[]>(() =>
    Array.isArray(data.value?.data) ? data.value.data : data.value ? [data.value.data] : []
);
const totalPrice = computed(() => {
    return cartItems.value.reduce((sum, item) => sum + item.purchase_price * item.quantity, 0);
});

const { data: dataShippingFee } = await getShippingFee()
const shippingFe = computed<Parameter | null>(() => Array.isArray(dataShippingFee.value?.data) ? dataShippingFee.value.data[0] : dataShippingFee.value?.data || null)

const { data: dataAddresses } = await getCustomerAddress();
const addresses = computed<CustomerAddress[]>(() => Array.isArray(dataAddresses.value?.data) ? dataAddresses.value.data : dataAddresses.value ? [dataAddresses.value.data] : [])

// Lấy danh sách tỉnh/thành phố khi component được tạo
await fetchProvinces();

const handleAddressChange = async (address: CustomerAddress) => {
    form.value.full_name = address.full_name;
    form.value.phone_number = address.phone_number;
    form.value.address_type = address.address_type;
    form.value.street_address = address.address.street_address;
    form.value.province = address.address.province;
    await fetchDistricts(address.address.province);
    form.value.district = address.address.district;
    await fetchWards(address.address.district);
    form.value.ward = address.address.ward;
};

// Xử lý submit form
const handleSubmit = async () => {
    try {
        submit.value = true;
        // Tạo FormData để gửi dữ liệu multipart
        const formData = new FormData();
        formData.append('full_name', form.value.full_name);
        formData.append('phone_number', form.value.phone_number);
        formData.append('province', form.value.province);
        formData.append('district', form.value.district);
        formData.append('ward', form.value.ward);
        formData.append('street_address', form.value.street_address);
        formData.append('notes', form.value.notes);
        formData.append('address_type', form.value.address_type);
        formData.append('cart_items', JSON.stringify(cartItems.value));
        formData.append('total_price', totalPrice.value.toString());
        formData.append('shipping_fee', shippingFe.value?.value.toString() || '0');

        // Gửi request với FormData
        const { error } = await addOrder(formData);

        if (error.value) {
            throw new Error(error.value.message);
        }

        $toast.success('Đặt hàng thành công!');
        // Chuyển hướng đến trang cảm ơn hoặc trang khác
        router.push('/');
    } catch (error: any) {
        $toast.error(error.message || 'Đặt hàng thất bại!');
    } finally {
        submit.value = false;
    }
};
</script>