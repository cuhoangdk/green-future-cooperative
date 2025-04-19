<template>
    <div class="p-4">
        <div v-if="status === 'pending'" class="flex justify-center items-center h-screen">
            <span class="loading loading-spinner loading-lg"></span>
        </div>
        <div v-if="order">
            <!-- Header Section -->
            <div class="border-b pb-4 mb-6">
                <div class="flex flex-col md:flex-row md:justify-between gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Mã đơn hàng</p>
                        <p class="font-semibold">{{ order.id }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Ngày đặt hàng</p>
                        <p class="font-semibold">{{ formatDateTime(order.created_at) }}</p>
                    </div>
                    <div v-if="order.expected_delivery_date">
                        <p class="text-sm text-gray-500">Ngày giao hàng dự kiến</p>
                        <p class="font-semibold">{{ order.expected_delivery_date }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Cập nhật lần cuối</p>
                        <p class="font-semibold">{{ formatDateTime(order.updated_at) }}</p>
                    </div>
                    <div class="badge badge-lg" :class="{
                        'badge-warning': order.status === 'processing',
                        'badge-error': order.status === 'cancelled',
                        'badge-info': order.status === 'pending'
                    }">
                        {{ order.status === 'processing' ? 'Đang xử lý' : order.status === 'cancelled' ? 'Đã hủy' :
                            order.status === 'pending' ? 'Chờ xác nhận' : '' }}
                    </div>
                </div>
            </div>

            <!-- Customer Information -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
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
                                    <th class="py-2 pl-2 text-left w-[25%] min-w-44">Sản phẩm</th>
                                    <th class="py-2 text-left w-[25%] min-w-44">Số lượng × Đơn giá</th>
                                    <th class="py-2 text-left w-[25%] min-w-44">Thành tiền</th>
                                    <th v-if="currentUser?.is_super_admin === true"
                                        class="py-2 text-left w-[25%] min-w-44">Thuộc về</th>
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
                                        {{ formatNumber(item.quantity) }} {{ item.product_snapshot.unit }} × {{formatCurrency(item.product_snapshot.price) }}
                                            </td>
                                    <td class="font-semibold">{{ formatCurrency(item.total_item_price) }}</td>
                                    <td v-if="item.flag === false && currentUser?.is_super_admin === true">
                                        <span class="text-gray-500">
                                            {{ item.product_snapshot.user_full_name }}
                                        </span>
                                    </td>
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
                                <span class="font-bold text-primary">{{ formatCurrency(order.final_total_amount) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cancellation Information -->
            <div v-if="order.cancelled_at" class="card bg-red-200 bg-opacity-10 border border-gray-200 shadow-xs mb-6">
                <div class="card-body p-4">
                    <h2 class="card-title text-lg">Thông tin hủy đơn</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <p>Lý do hủy</p>
                            <p>{{ order.cancelled_reason }}</p>
                        </div>
                        <div>
                            <p>Thời gian hủy</p>
                            <p>{{ formatDateTime(order.cancelled_at) }}</p>
                        </div>
                        <div>
                            <p>Người hủy</p>
                            <p>{{ order.cancelled_by }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-between  w-full mt-6">
                <UiButtonBack />
                <!-- <div class="space-x-2">
                    <button class="btn btn-primary">
                        In 
                    </button>
                    <button class="btn btn-outline btn-primary">
                        Lưu
                    </button>
                </div> -->
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
import type { Order } from '~/types/order'
import { ArrowLeft, Phone, Mail, User, MapPin , X } from 'lucide-vue-next'

const { getAdminOrderById } = useAdminOrder()
const { getFullAddressName } = useVietnamAddress()
const { currentUser } = useUserAuth()
const route = useRoute()

const id = String(route.params.id);

const { data, status } = await getAdminOrderById(id)
const addressData = ref<string>('')

const order = computed<Order | null>(() => Array.isArray(data.value?.data) ? data.value.data[0] : data.value?.data || null)
watch(order, async (newOrder) => {
    if (newOrder) {
        addressData.value = `${newOrder.street_address}, ${await getFullAddressName(newOrder.ward)}`;
    }
});

</script>