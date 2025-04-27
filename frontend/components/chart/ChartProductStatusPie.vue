<template>
    <div class="chart-container">
        <h3 class="text-lg font-semibold mb-2">Trạng thái sản phẩm</h3>
        <Pie :data="chartData" :options="chartOptions" />
    </div>
</template>

<script setup>
import { Pie } from 'vue-chartjs'
import { Chart as ChartJS, ArcElement, Tooltip, Legend } from 'chart.js'

ChartJS.register(ArcElement, Tooltip, Legend)

const props = defineProps({
    data: {
        type: Object,
        required: true
    }
})

const chartData = computed(() => {
    const labels = ['Đang phát triển', 'Đang bán', 'Ngừng bán']
    const values = [props.data.growing, props.data.selling, props.data.stopped]

    return {
        labels,
        datasets: [
            {
                backgroundColor: ['#4CAF50', '#2196F3', '#F44336'],
                data: values
            }
        ]
    }
})

const chartOptions = {
  responsive: true,
  plugins: {
    legend: {
      position: 'bottom'
    }
  }
}
</script>