<template>
    <div class="lg:hidden dock dock-xs z-50">
        <template v-if="!isAuthenticated">
            <NuxtLink to="/login" :class="{ 'dock-active': route.path === '/login' }">
                 Đăng nhập
            </NuxtLink>
        </template>
        <template v-else>
            <NuxtLink to="/orders" :class="{ 'dock-active': route.path === '/orders' }">
                <ShoppingBag class="size-[1.2em]" color="currentColor" />
            </NuxtLink>
            
            <NuxtLink to="/account/addresses" :class="{ 'dock-active': route.path === '/account/addresses' }">
                <MapPin class="size-[1.2em]" color="currentColor" />
            </NuxtLink>

            <NuxtLink to="/account" :class="{ 'dock-active': route.path === '/account' }">
                <User class="size-[1.2em]" color="currentColor" />
            </NuxtLink>
        </template>
    </div>
</template>

<script setup lang="ts">
import { User, MapPin, ShoppingBag } from 'lucide-vue-next';

const { isAuthenticated } = useCustomerAuth();
const route = useRoute();
</script>
