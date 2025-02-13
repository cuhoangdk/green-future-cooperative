<template>
    <div>
        <div class="h-8 bg-gray-200 rounded w-1/4 mb-4"></div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div v-for="j in 3" :key="j" class="space-y-3">
                <div class="bg-gray-200 h-48 rounded"></div>
                <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                <div class="h-4 bg-gray-200 rounded w-1/2"></div>
                <div class="h-4 bg-gray-200 rounded w-1/4"></div>
            </div>
        </div>
    </div>
</template>