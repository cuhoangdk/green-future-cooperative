<!-- components/Carousel.vue -->
<template>
    <div class="relative w-full aspect-[5/3] max-h-[500px]">
        <!-- Carousel container -->
        <div class="carousel-container w-full h-full overflow-hidden relative">
            <transition-group name="slide" tag="div" class="flex h-full"
                :style="{ transform: `translateX(-${currentIndex * 100}%)`, transition: 'transform 0.5s ease-in-out' }">
                <div v-for="(slide, index) in slides" :key="slide.id" class="w-full h-full flex-shrink-0 flex-grow-0"
                    :class="{ 'active': index === currentIndex }">
                    <img :src="backendUrl + slide.image_url" :alt="slide.title" class="w-full h-full object-cover" />

                    <!-- Optional caption overlay -->
                    <!-- <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-50 text-white p-4">
                        <h3 class="text-xl font-bold">{{ slide.title }}</h3>
                    </div> -->
                </div>
            </transition-group>
        </div>

        <!-- Navigation arrows -->
        <div class="absolute inset-y-0 left-3 flex items-center">
            <button @click="prevSlide"
                class="btn btn-square btn-soft rounded-full opacity-70" >
                <span class="sr-only">Previous</span>
                <ChevronLeft class="h-6 w-6" />
            </button>
        </div>

        <div class="absolute inset-y-0 right-3 flex items-center">
            <button @click="nextSlide"
            class="btn btn-square btn-soft rounded-full opacity-70" >
            <span class="sr-only">Next</span>
                <ChevronRight class="h-6 w-6" />
            </button>
        </div>

        <!-- Dots indicators -->
        <div class="absolute bottom-4 left-0 right-0 flex justify-center space-x-2">
            <button v-for="(slide, index) in slides" :key="`dot-${slide.id}`" @click="setCurrentSlide(index)"
                class="w-3 h-3 rounded-full focus:outline-none transition-colors"
                :class="index === currentIndex ? 'bg-gray-200' : 'bg-gray-100'">
                <span class="sr-only">Slide {{ index + 1 }}</span>
            </button>
        </div>

        <!-- Play/Pause button -->
        <button @click="toggleAutoplay"
            class="absolute btn btn-sm btn-ghost btn-square top-4 right-4 ">
            <span class="sr-only">{{ isPlaying ? 'Pause' : 'Play' }}</span>
            <StopCircle v-if="isPlaying" class="h-5 w-5" />
            <PlayCircle v-else class="h-5 w-5" />
        </button>
    </div>
</template>

<script setup lang="ts">
import type { CarouselItem } from '~/types/carousel'
import { StopCircle, PlayCircle, ChevronLeft, ChevronRight } from 'lucide-vue-next'


const props = defineProps({
    autoplaySpeed: {
        type: Number,
        default: 3000 // 5 seconds
    },
    autoplay: {
        type: Boolean,
        default: true
    }
})

const { getAllSlide } = useCarousel()
const config = useRuntimeConfig()
const backendUrl = config.public.backendUrl

// Fetch carousel data
const { data } = await getAllSlide()
const slides = computed<CarouselItem[]>(() =>
    Array.isArray(data.value?.data)
        ? data.value.data
        : data.value
            ? [data.value.data]
            : []
)

// Control the current slide
const currentIndex = ref(0)
const isPlaying = ref(props.autoplay)
let intervalId: number | null = null

// Auto-play functionality
const startAutoplay = () => {
    if (intervalId) clearInterval(intervalId)
    intervalId = window.setInterval(() => {
        nextSlide()
    }, props.autoplaySpeed)
}

const stopAutoplay = () => {
    if (intervalId) {
        clearInterval(intervalId)
        intervalId = null
    }
}

const toggleAutoplay = () => {
    isPlaying.value = !isPlaying.value
    if (isPlaying.value) {
        startAutoplay()
    } else {
        stopAutoplay()
    }
}

// Navigation functions
const nextSlide = () => {
    if (slides.value.length <= 1) return
    currentIndex.value = (currentIndex.value + 1) % slides.value.length
}

const prevSlide = () => {
    if (slides.value.length <= 1) return
    currentIndex.value = (currentIndex.value - 1 + slides.value.length) % slides.value.length
}

const setCurrentSlide = (index: number) => {
    currentIndex.value = index
}

// Pause on hover (optional)
const handleMouseEnter = () => {
    if (isPlaying.value) {
        stopAutoplay()
    }
}

const handleMouseLeave = () => {
    if (isPlaying.value) {
        startAutoplay()
    }
}

// Watch for changes in carousel data
watch(() => slides.value.length, (newLength) => {
    if (newLength > 0 && currentIndex.value >= newLength) {
        currentIndex.value = 0
    }
})

// Lifecycle hooks
onMounted(() => {
    if (isPlaying.value && slides.value.length > 1) {
        startAutoplay()
    }
})

onUnmounted(() => {
    stopAutoplay()
})
</script>

<style scoped>
.slide-enter-active,
.slide-leave-active {
    transition: opacity 0.5s ease;
}

.slide-enter-from,
.slide-leave-to {
    opacity: 0;
}
</style>