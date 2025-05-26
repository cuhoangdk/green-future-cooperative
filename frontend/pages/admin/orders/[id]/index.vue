<template>
    <div class="p-4">
        <div v-if="status === 'pending'" class="flex justify-center items-center h-screen">
            <span class="loading loading-spinner loading-lg"></span>
        </div>
        <div v-else-if="order">
            <!-- Header Section -->
            <div class="border-b pb-4 mb-6">
                <div class="flex flex-col md:flex-row md:justify-between gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Mã đơn hàng</p>
                        <div class="flex items-center gap-2 mb-1">
                            <p class="font-semibold">{{ order.id }}</p>
                            <OrderStatus :status="order.status" />
                        </div>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Ngày đặt hàng</p>
                        <p class="font-semibold">{{ formatDateTime(order.created_at) }}</p>
                    </div>
                    <!-- <div>
                        <p class="text-sm text-gray-500">Cập nhật lần cuối</p>
                        <p class="font-semibold">{{ formatDateTime(order.updated_at) }}</p>
                    </div>
                    <div v-if="order.expected_delivery_date">
                        <p class="text-sm text-gray-500">Ngày giao hàng dự kiến</p>
                        <p class="font-semibold">{{ order.expected_delivery_date }}</p>
                    </div> -->
                </div>
                <div>
                    <OrderTimeline :status="order.status" :order-id="order.id" />
                </div>
            </div>

            <!-- Customer Information -->
            <div v-if="order.phone_number" class="grid grid-cols-1 md:grid-cols-3 gap-x-0 gap-y-4 md:gap-4 mb-4">
                <div class="card bg-base-100 border border-gray-200">
                    <div class="p-4">
                        <h2 class="card-title text-lg mb-2">Thông tin khách hàng</h2>
                        <div class="space-y-2">
                            <div class="flex gap-2">
                                <User class="text-primary w-5" />
                                <p>{{ order.full_name }}</p>
                            </div>
                            <div class="flex gap-2">
                                <Mail class="text-primary w-5" />
                                <p>{{ order.email }}</p>
                            </div>
                            <div class="flex gap-2">
                                <Phone class="text-primary w-5" />
                                <p>{{ order.phone_number }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-span-2 card bg-base-100 border border-gray-200 shadow-xs">
                    <div class="p-4">
                        <h2 class="card-title text-lg mb-2">Địa chỉ giao hàng</h2>
                        <div class="flex gap-2 items-start">
                            <MapPin />
                            <div>
                                <p>{{ addressData }}</p>
                                <span class="badge badge-outline mt-1">
                                    {{ order?.address_type === 'home' ? 'Nhà riêng' : order.address_type === 'work' ?
                                        'Nơi làm việc' : ' ' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="card bg-base-100 border border-gray-200 shadow-xs mb-4">
                <div class="p-4">
                    <h2 class="card-title text-lg mb-4">Sản phẩm đã đặt</h2>
                    <div class="w-full max-w-[80vw] sm:max-w-[90vw] overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gray-100 text-gray-700">
                                    <th class="py-2 pl-2 text-left w-[25%] min-w-44">Sản phẩm</th>
                                    <th class="py-2 text-left w-[25%] min-w-44">Số lượng × Đơn giá</th>
                                    <th class="py-2 text-left w-[25%] min-w-44">Thành tiền</th>
                                    <th v-if="currentUser?.is_super_admin === true"
                                        class="py-2 text-left w-[25%] min-w-44">
                                        Thuộc về</th>
                                </tr>
                            </thead>
                            <tbody v-if="currentUser?.is_super_admin === true">
                                <tr v-for="item in order?.items" :key="item.id"
                                    class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="py-2 pl-2">
                                        <span class="text-xs">
                                            {{ item.product_snapshot.id }}
                                        </span>
                                        <br />
                                        {{ item.product_snapshot.product_name }}
                                    </td>
                                    <td>
                                        {{ formatNumber(item.quantity) }} {{ item.product_snapshot.unit }} ×
                                        {{ formatCurrency(item.product_snapshot.price) }}
                                    </td>
                                    <td class="font-semibold">{{ formatCurrency(item.total_item_price) }}</td>
                                    <td>
                                        <span class="text-gray-500">
                                            {{ item.product_snapshot.user_full_name }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                            <tbody v-else>
                                <template v-for="item in order?.items" :key="item.id">
                                    <tr v-if="item.flag == false"
                                        class="border-b border-gray-200 hover:bg-gray-50">
                                        <td class="py-2 pl-2">
                                            <span class="text-xs">
                                                {{ item.product_snapshot.id }}
                                            </span>
                                            <br />
                                            {{ item.product_snapshot.product_name }}
                                        </td>
                                        <td>
                                            {{ formatNumber(item.quantity) }} {{ item.product_snapshot.unit }} ×
                                            {{ formatCurrency(item.product_snapshot.price) }}
                                        </td>
                                        <td class="font-semibold">{{ formatCurrency(item.total_item_price) }}</td>
                                    </tr>
                                </template>
                                <tr class="bg-gray-50 font-semibold">
                                    <td colspan="2" class="py-2 pl-2 text-right">Tổng cộng:</td>
                                    <td class="py-2 font-bold text-primary">
                                        {{ formatCurrency(order.final_total_amount) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div class="md:col-span-2 card bg-base-100 border border-gray-200 shadow-xs">
                    <div class="card-body p-4">
                        <h2 class="card-title text-lg mb-2">Ghi chú</h2>
                        <div v-if="order.notes" class="bg-gray-50 p-3 rounded-lg text-gray-700 mb-4">
                            <p class="whitespace-pre-line">{{ order.notes }}</p>
                        </div>
                        <div v-else class="text-gray-500 italic">Không có ghi chú từ khách hàng</div>

                        <div v-if="order.admin_note" class="mt-4">
                            <h3 class="font-medium mb-1 text-primary">Ghi chú nội bộ:</h3>
                            <div class="bg-blue-50 p-3 rounded-lg text-gray-700">
                                <p class="whitespace-pre-line">{{ order.admin_note }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card bg-base-100 border border-gray-200 shadow-xs">
                    <div class="card-body p-4">
                        <h2 class="card-title text-lg mb-2">Tổng cộng</h2>
                        <div class="space-y-2 divide-y">
                            <div class="flex justify-between py-2">
                                <span>Tạm tính:</span>
                                <span class="font-medium">{{ formatCurrency(order.total_price)
                                    }}</span>
                            </div>
                            <div class="flex justify-between py-2">
                                <span>Phí vận chuyển:</span>
                                <span class="font-medium">{{ formatCurrency(order.shipping_fee) }}</span>
                            </div>
                            <div class="flex justify-between py-2 text-lg">
                                <span class="font-bold">Tổng cộng:</span>
                                <span class="font-bold text-primary">{{ formatCurrency(order.final_total_amount)
                                    }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-between w-full mt-4">
                <UiButtonBack />
                <OrderConfirmButton v-if="currentUser?.is_super_admin" :status="order.status" @click="handleChangeStatus" />
            </div>
        </div>

        <!-- Order Not Found -->
        <div v-else class="flex flex-col items-center justify-center py-12">
            <div class="text-5xl text-gray-300 mb-4">
                <i class="fa-solid fa-box-open"></i>
            </div>
            <p class="text-lg text-gray-600 mb-6">Đơn hàng không tồn tại hoặc đã bị xóa</p>
            <UiButtonBack />
        </div>
    </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'user', title: 'Chi tiết đơn hàng', description: 'Quản lý đơn hàng' })
import type { Order, OrderStatus } from '~/types/order'
import { ArrowLeft, Phone, Mail, User, MapPin, X } from 'lucide-vue-next'


const { getAdminOrderById, updateAdminOrder } = useAdminOrder()
const { getFullAddressName } = useVietnamAddress()
const { currentUser } = useUserAuth()
const route = useRoute()
const router = useRouter()
const { $toast } = useNuxtApp();

const id = String(route.params.id);

const { data, status } = await getAdminOrderById(id)
const addressData = ref<string>('')

const order = computed<Order | null>(() => Array.isArray(data.value?.data) ? data.value.data[0] : data.value?.data || null)
watch(order, async (newOrder) => {
    if (newOrder) {
        addressData.value = `${newOrder.street_address}, ${await getFullAddressName(newOrder.ward)}`;
    }
});


const handleChangeStatus = async () => {
    try {
        if (!order.value) return;

        const statusOrder: OrderStatus[] = ['pending', 'processing', 'delivering', 'delivered'];
        const currentIndex = statusOrder.indexOf(order.value.status);
        if (currentIndex === -1 || currentIndex === statusOrder.length - 1) return;

        const nextStatus = statusOrder[currentIndex + 1];

        status.value = 'pending';
        const formData = new FormData();
        formData.append('status', nextStatus);
        const { error } = await updateAdminOrder(id, formData);

        if (error.value) {
            throw new Error(error.value.message);
        }

        $toast.success(`Cập nhật trạng thái đơn hàng thành công: ${nextStatus}`);
        router.push('/admin/orders');
    } catch (error: any) {
        $toast.error('Có lỗi xảy ra trong quá trình cập nhật trạng thái đơn hàng!');
    } finally {
        status.value = 'idle';
    }
};
</script>