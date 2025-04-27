<template>
    <div>
        <pre>{{ statistic }}</pre>
    </div>
</template>

<script setup lang="ts">
definePageMeta({title: 'Trang chủ',layout: 'user',})
import type { Statistics } from '~/types/statistics'
const input = reactive({
    startDate: "2025-04-21",
    endDate: "2025-04-27",
})

const { getStatistics } = useStatistics()
const { data } = await getStatistics(input.startDate, input.endDate)

const statistic = computed<Statistics>(() => data.value?.data ?? {} as Statistics)
</script>