<template>
    <div class="p-4 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
        <NuxtLink to="/admin/setting/shipping-fee" class="btn flex items-center space-x-2" v-tooltip="'Quản lý và cập nhật phí vận chuyển cho các đơn hàng'">
            <Truck class="w-5 h-5" />
            <span>Phí vận chuyển</span>
        </NuxtLink>
        <NuxtLink to="/admin/setting/carousel" class="btn flex items-center space-x-2" v-tooltip="'Tùy chỉnh hình ảnh và nội dung hiển thị trên trang bìa'">
            <Image class="w-5 h-5" />
            <span>Trang bìa</span>
        </NuxtLink>
        <NuxtLink to="/admin/setting/logs" class="btn flex items-center space-x-2" v-tooltip="'Xem và quản lý nhật ký hoạt động của ứng dụng'">
            <FileText class="w-5 h-5" />
            <span>Nhật kí ứng dụng</span>
        </NuxtLink>
        <NuxtLink to="/admin/setting/phone-contact" class="btn flex items-center space-x-2" v-tooltip="'Cập nhật số điện thoại liên hệ cho khách hàng'">
            <Phone class="w-5 h-5" />
            <span>Số điện thoại / Zalo</span>
        </NuxtLink>
        <NuxtLink to="/admin/setting/social-link" class="btn flex items-center space-x-2" v-tooltip="'Quản lý các liên kết mạng xã hội của trang web'">
            <Globe class="w-5 h-5" />
            <span>Link mạng xã hội</span>
        </NuxtLink>
    </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'user', title: 'Cài đặt' })
import { Truck, Image, FileText, Phone, Globe } from 'lucide-vue-next'
</script>
