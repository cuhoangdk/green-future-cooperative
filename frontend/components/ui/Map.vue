<template>
    <div class="rounded-lg shadow-lg overflow-hidden">
        <ClientOnly>
            <LMap class="z-1" style="height: 350px" :zoom="12" :center="[latitude, longitude]" :use-global-leaflet="true">
                <LTileLayer url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
                    attribution="© <a href='https://www.openstreetmap.org/'>OpenStreetMap</a> contributors"
                    layer-type="base" name="OpenStreetMap" />
                <LMarker :lat-lng="[latitude, longitude]" />
            </LMap>
        </ClientOnly>
    </div>
</template>

<script setup lang="ts">
defineProps<{
    latitude: number
    longitude: number
}>()
</script>