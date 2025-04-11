<template>
    <div class="lg:hidden dock dock-xs">
        <NuxtLink to="/orders" :class="{ 'dock-active': route.path === '/orders' }">
            <ShoppingBag class="size-[1.2em]" color="currentColor" />
        </NuxtLink>

        <NuxtLink to="/account/addresses" :class="{ 'dock-active': route.path === '/account/addresses' }">
            <MapPin class="size-[1.2em]" color="currentColor" />
        </NuxtLink>

        <NuxtLink to="/account" :class="{ 'dock-active': route.path === '/account' }">
            <User class="size-[1.2em]" color="currentColor" />
        </NuxtLink>
    </div>
</template>

<script setup lang="ts">
import { User, MapPin, ShoppingBag } from 'lucide-vue-next';
import { useRoute } from 'vue-router';

const route = useRoute();
</script>
