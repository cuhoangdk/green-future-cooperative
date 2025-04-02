<template>
    <div> <!-- Ảnh lớn -->
        <div class="relative">
            <img :src="activeImageUrl" :alt="images[activeImageIndex]?.title || 'Product Image'"
                class="w-full aspect-[4/3] object-cover rounded-xl border border-gray-200"
                @error="event => { const target = event.target as HTMLImageElement; if (target) target.src = placeholderImage as string; }" />
            <!-- Nút điều hướng -->
            <button v-if="images && images.length > 1" @click="prevImage"
                class="absolute left-2 top-1/2 transform -translate-y-1/2 bg-gray-600 text-white p-2 rounded-full opacity-50">
                <ArrowLeft />
            </button>
            <button v-if="images && images.length > 1" @click="nextImage"
                class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-gray-600 text-white p-2 rounded-full opacity-50">
                <ArrowRight />
            </button>
        </div>
        <!-- Danh sách ảnh nhỏ -->
        <div class="flex gap-2 mt-2 overflow-x-auto">
            <img v-for="(image, index) in images" :key="index" :src="`${backendUrl}${image.image_url}`"
                :alt="image.title || 'Product Image'"
                class="w-20 h-20 object-cover rounded border border-gray-200 flex-shrink-0 cursor-pointer"
                :class="{ 'border-green-600 border-2': activeImageIndex === index }" @click="activeImageIndex = index"
                @error="event => { const target = event.target as HTMLImageElement; if (target) target.src = placeholderImage as string; }" />
        </div>
    </div>
</template>

<script setup lang="ts">
import { ArrowLeft, ArrowRight } from 'lucide-vue-next'
import type { ProductImage } from '~/types/product';

const config = useRuntimeConfig()
const placeholderImage = config.public.placeholderImage
const backendUrl = config.public.backendUrl

interface Props {
    images: ProductImage[];
}

const props = defineProps<Props>()

// Quản lý ảnh đang chọn
const activeImageIndex = ref(0)
const activeImageUrl = computed(() => {
    const images = props.images
    if (!images || images.length === 0) return placeholderImage
    return `${backendUrl}${images[activeImageIndex.value]?.image_url}` || placeholderImage
})

// Chuyển ảnh trước/sau
function prevImage() {
    if (props.images && activeImageIndex.value > 0) {
        activeImageIndex.value--
    }
}

function nextImage() {
    if (props.images && activeImageIndex.value < props.images.length - 1) {
        activeImageIndex.value++
    }
}
</script>