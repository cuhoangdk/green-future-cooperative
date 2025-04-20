<template>
    <div class="rounded-lg shadow-lg overflow-hidden">
        <LMap style="height: 350px" :zoom="12" :center="[latitude, longitude]" :use-global-leaflet="true">
            <LTileLayer url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
                attribution="&amp;copy; <a href=&quot;https://www.openstreetmap.org/&quot;>OpenStreetMap</a> contributors"
                layer-type="base" name="OpenStreetMap" />
            <LMarker :lat-lng="[latitude, longitude]" draggable />

        </LMap>
    </div>
</template>
<script setup lang="ts">
defineProps<{
    latitude: number
    longitude: number
}>()
</script>