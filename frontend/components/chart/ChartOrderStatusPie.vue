<template>
    <div class="chart-container">
      <h3 class="text-lg font-semibold mb-2">Trạng thái đơn hàng</h3>
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
    const labels = Object.keys(props.data).map(key => {
      const statusMap = {
        'pending': 'Chờ xử lý',
        'processing': 'Đang xử lý',
        'delivering': 'Đang giao',
        'delivered': 'Đã giao',
        'cancelled': 'Đã hủy'
      }
      return statusMap[key] || key
    })
    const values = Object.values(props.data)
    
    return {
      labels,
      datasets: [
        {
          backgroundColor: ['#FFC107', '#2196F3', '#9C27B0', '#4CAF50', '#F44336'],
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
  