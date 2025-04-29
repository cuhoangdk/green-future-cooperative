<template>
    <div class="chart-container">
        <h3 class="text-lg font-semibold mb-2">Sản phẩm bán được nhiều nhất</h3>
        <Bar :data="chartData" :options="chartOptions" />
    </div>
</template>

<script setup>
import { Bar } from 'vue-chartjs'
import { Chart as ChartJS, BarElement, CategoryScale, LinearScale, Tooltip, Legend } from 'chart.js'

ChartJS.register(BarElement, CategoryScale, LinearScale, Tooltip, Legend)

const props = defineProps({
    data: {
        type: Array,
        required: true
    }
})

const chartData = computed(() => {
    const labels = props.data.map(item => `${item.id} - ${item.name}`)
    const values = props.data.map(item => parseFloat(item.total_quantity))
    const units = props.data.map(item => item.product_unit)

    return {
        labels,
        datasets: [
            {
                label: 'Số lượng',
                backgroundColor: ['#4CAF50', '#2196F3', '#F44336', '#FFC107', '#9C27B0'],
                data: values
            }
        ]
    }
})

const chartOptions = {
    responsive: true,
    plugins: {
        legend: {
            position: 'top',
            display: false
        },
        tooltip: {
            callbacks: {
                label: function (context) {
                    let label = context.dataset.label || '';
                    if (label) {
                        label += ': ';
                    }
                    let value = context.raw || 0;
                    let unit = props.data[context.dataIndex]?.product_unit || '';
                    label += value;
                    if (unit) {
                        label += ` (${unit})`;
                    }
                    return label;
                }
            }
        }
    },
    scales: {
        x: {
            title: {
                display: true,
                text: 'Sản phẩm'
            }
        },
        y: {
            title: {
                display: true,
                text: 'Số lượng'
            },
            beginAtZero: true
        }
    }
}
</script>