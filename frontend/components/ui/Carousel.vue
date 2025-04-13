<!-- components/Carousel.vue -->
<template>
    <div class="carousel w-full h-80 bg-gray-100">
        <div v-for="(slide, index) in slides" :key="slide.id" :id="`slide${index + 1}`"
            class="carousel-item relative w-full">
            <img :src="backendUrl + slide.image_url" :alt="slide.title" class="w-full h-80 object-cover" />
            <div class="absolute left-1 right-1 top-1/2 flex -translate-y-1/2 transform justify-between">
                <a :href="`#slide${index === 0 ? slides.length : index}`" class="btn btn-circle opacity-30">❮</a>
                <a :href="`#slide${index === slides.length - 1 ? 1 : index + 2}`" class="btn btn-circle opacity-30">❯</a>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import type { CarouselItem } from '~/types/carousel'

const { getAllSlide } = useCarousel()
const config = useRuntimeConfig();
const backendUrl = config.public.backendUrl;

const { data } = await getAllSlide()
const slides = computed<CarouselItem[]>(() =>Array.isArray(data.value?.data)
        ? data.value.data
        : data.value
            ? [data.value.data]
            : []
)
</script>