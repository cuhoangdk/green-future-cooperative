<template>
    <div class="card bg-base-100 shadow-sm border border-gray-300 w-full lg:w-10/12 mx-auto mt-16 lg:my-5 p-4">
        <div v-if="status === 'pending'" class="flex justify-center items-center h-screen">
            <span class="loading loading-spinner loading-lg"></span>
        </div>
        <div v-if="order">
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
                </div>
                <OrderTimeline :order-id="order.id"/>

            </div>

            <!-- Customer Information -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-x-0 gap-y-6 md:gap-6 mb-6">
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
            <div class="card bg-base-100 border border-gray-200 shadow-xs mb-6">
                <div class="p-4">
                    <h2 class="card-title text-lg mb-4">Sản phẩm đã đặt</h2>
                    <div class="w-full max-w-[80vw] sm:max-w-[90vw] overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gray-100 text-gray-700">
                                    <th class="py-2 pl-2 text-left w-[34%] min-w-44">Sản phẩm</th>
                                    <th class="py-2 text-left w-[33%] min-w-44">Số lượng × Đơn giá</th>
                                    <th class="py-2 text-left w-[33%] min-w-44">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
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
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="md:col-span-2 card bg-base-100 border border-gray-200 shadow-xs">
                    <div class="card-body p-4">
                        <h2 class="card-title text-lg mb-2">Ghi chú</h2>
                        <div v-if="order.notes" class="bg-gray-50 p-3 rounded-lg text-gray-700 mb-4">
                            <p class="whitespace-pre-line">{{ order.notes }}</p>
                        </div>
                        <div v-else class="text-gray-500 italic">Không có ghi chú</div>

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
                                <span class="font-bold text-primary">{{ formatCurrency(order.final_total_amount) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-between  w-full mt-6">
                <button @click="$router.back()" class="btn btn-ghost">
                    Quay lại
                </button>
                <button v-if="order && order.status !== 'cancelled'" @click="$router.push(`/orders/${order.id}/cancel`)" class="btn btn-error">
                    Hủy đơn
                </button>
            </div>
        </div>

        <!-- Order Not Found -->
        <div v-else class="flex flex-col items-center justify-center py-12">
            <div class="text-5xl text-gray-300 mb-4">
                <i class="fa-solid fa-box-open"></i>
            </div>
            <p class="text-lg text-gray-600 mb-6">Đơn hàng không tồn tại hoặc đã bị xóa</p>
            <button @click="$router.back()" class="btn btn-primary">
                <ArrowLeft /> Quay lại
            </button>
        </div>
    </div>
</template>

<script setup lang="ts">
import type { Order } from '~/types/order'
import { ArrowLeft, Phone, Mail, User, MapPin, X } from 'lucide-vue-next'

const { getOrderById } = useOrder()
const { getFullAddressName } = useVietnamAddress()
const route = useRoute()
const { currentUser } = useUserAuth()

const id = String(route.params.id)

const { data, status } = await getOrderById(id)
const addressData = ref<string>('')

const order = computed<Order | null>(() => Array.isArray(data.value?.data) ? data.value.data[0] : data.value?.data || null)
watch(order, async (newOrder) => {
    if (newOrder) {
        addressData.value = `${newOrder.street_address}, ${await getFullAddressName(newOrder.ward)}`;
    }
});

</script>