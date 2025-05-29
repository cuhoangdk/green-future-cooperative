<template>
    <div class="w-full rounded-2xl">
        <div class="relative">
            <div ref="carousel" class="carousel carousel-center rounded-box w-full space-x-4 py-4">
                <div v-for="(product, index) in products" :key="product.id" :id="`item${index}`"
                    class="carousel-item flex-shrink-0 w-48 sm:w-56 md:w-64">
                    <ProductCard :product="product" class="w-full" />
                </div>
            </div>
            
            <button v-if="products.length > 0" @click="scrollLeft"
                class="absolute left-2 top-1/2 -translate-y-1/2 btn btn-circle btn-active btn-sm bg-white/90 hover:bg-white border-0 shadow-lg z-10">
                    <ChevronLeft class="h-4 w-4" />
            </button>

            <button v-if="products.length > 0" @click="scrollRight"
                class="absolute right-2 top-1/2 -translate-y-1/2 btn btn-circle btn-active btn-sm bg-white/90 hover:bg-white border-0 shadow-lg z-10">
                    <ChevronRight class="h-4 w-4" />
            </button>

            <div v-if="status === 'pending'"
                class="absolute inset-0 flex items-center justify-center bg-base-100/50 z-20 rounded-box">
                <span class="loading loading-spinner loading-lg"></span>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ChevronLeft, ChevronRight } from 'lucide-vue-next';
import type { Product } from '~/types/product';

interface Props {
    products: Product[];
    status?: string;
    autoPlay?: boolean;
    autoPlayInterval?: number;
}

const props = withDefaults(defineProps<Props>(), {
    status: 'idle',
    autoPlay: false,
    autoPlayInterval: 5000
});

const carousel = ref<HTMLElement | null>(null);

// Auto play
let autoPlayTimer: NodeJS.Timeout | null = null;

const scrollLeft = () => {
    if (carousel.value) {
        const scrollAmount = 250; // Scroll 250px mỗi lần
        carousel.value.scrollBy({
            left: -scrollAmount,
            behavior: 'smooth'
        });
        resetAutoPlay();
    }
};

const scrollRight = () => {
    if (carousel.value) {
        const scrollAmount = 250; // Scroll 250px mỗi lần
        carousel.value.scrollBy({
            left: scrollAmount,
            behavior: 'smooth'
        });
        resetAutoPlay();
    }
};

// Auto play functionality
const startAutoPlay = () => {
    if (!props.autoPlay || props.products.length <= 1) return;

    autoPlayTimer = setInterval(() => {
        scrollRight();
    }, props.autoPlayInterval);
};

const stopAutoPlay = () => {
    if (autoPlayTimer) {
        clearInterval(autoPlayTimer);
        autoPlayTimer = null;
    }
};

const resetAutoPlay = () => {
    stopAutoPlay();
    startAutoPlay();
};

// Keyboard navigation
const handleKeydown = (e: KeyboardEvent) => {
    if (e.key === 'ArrowLeft') {
        scrollLeft();
    } else if (e.key === 'ArrowRight') {
        scrollRight();
    }
};

onMounted(() => {
    startAutoPlay();
    document.addEventListener('keydown', handleKeydown);
});

onUnmounted(() => {
    stopAutoPlay();
    document.removeEventListener('keydown', handleKeydown);
});
</script>