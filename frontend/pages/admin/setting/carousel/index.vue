<template>
    <div class="flex flex-col gap-4 p-4">
        <div>
            <button @click="$router.push('/admin/setting/carousel/create')"
                class=" float-end btn btn-sm btn-primary w-full md:w-auto">
                <Plus class="w-5 h-5" /> Thêm
            </button>
        </div>
        <div>
            <div v-if="slides.length" class="grid gap-2">
                <div @click="$router.push(`carousel/${slide.id}`)" v-for="slide in slides" :key="slide.id"
                    class="flex items-center p-4 bg-white shadow rounded-lg border border-gray-300">
                    <img :src="backendUrl + slide.image_url" :alt="slide.title"
                        class="h-24 aspect-video object-cover rounded mr-4" />
                    <div class="flex-1">
                        <h3 class="text-lg font-medium">{{ slide.title }}</h3>
                    </div>
                    <UiEditButton :to="`carousel/${slide.id}`" class="mr-2" />
                    <UiDeleteButton :on-click="() => handleDelete(slide.id)" />
                </div>
            </div>
        </div>
        <UiButtonBack/>
    </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'user', title: 'Trang bìa' })
import { Plus } from 'lucide-vue-next'
import type { CarouselItem } from '~/types/carousel'
import { useSwal } from '~/composables/useSwal'

const { getAllSlide, deleteSlide } = useCarousel()
const config = useRuntimeConfig()
const backendUrl = config.public.backendUrl
const { $toast } = useNuxtApp()
const swal = useSwal()

const { data, refresh } = await getAllSlide(AuthType.User)
const slides = computed<CarouselItem[]>(() =>
    Array.isArray(data.value?.data) ? data.value.data : data.value ? [data.value.data] : []
)

// Handle slide deletion
const handleDelete = async (id: number) => {
    const result = await swal.fire({
        title: 'Xác nhận xóa',
        text: 'Bạn có chắc muốn xóa slide này không?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Xóa',
        cancelButtonText: 'Hủy',
    })

    if (result.isConfirmed) {
        try {
            await deleteSlide(id)
            $toast.success('Xóa slide thành công')
            await refresh()
        } catch (error) {
            $toast.error('Xóa slide thất bại')
        }
    }
}
</script>