<template>
    <div class="chart-container">
        <h3 class="text-lg font-semibold mb-2">Sản phẩm có doanh thu cao</h3>
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
    const labels = props.data.map(item => item.name)
    const values = props.data.map(item => parseFloat(item.total_revenue))

    return {
        labels,
        datasets: [
            {
                label: 'Doanh thu',
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
            position: 'top'
        },
        tooltip: {
            callbacks: {
                label: function (context) {
                    let label = context.dataset.label || '';
                    if (label) {
                        label += ': ';
                    }
                    let value = context.raw || 0;
                    label += new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value);
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
                text: 'Doanh thu (VND)'
            },
            beginAtZero: true
        }
    }
}
</script>