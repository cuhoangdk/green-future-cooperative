<template>
    <div class="chart-container">
      <h3 class="text-lg font-semibold mb-2">Thành viên bán nhiều nhất</h3>
      <Bar :data="chartData" :options="chartOptions" />
    </div>
  </template>
  
  <script setup>
  import { Bar } from 'vue-chartjs'
  import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    BarElement,
    Title,
    Tooltip,
    Legend
  } from 'chart.js'
  
  ChartJS.register(
    CategoryScale,
    LinearScale,
    BarElement,
    Title,
    Tooltip,
    Legend
  )
  
  const props = defineProps({
    data: {
      type: Array,
      required: true
    }
  })
  
  const chartData = computed(() => {
    const labels = props.data.map(item => item.full_name)
    const values = props.data.map(item => parseFloat(item.total_revenue))
    
    return {
      labels,
      datasets: [
        {
          label: 'Doanh thu',
          backgroundColor: 'rgba(75, 192, 192, 0.8)',
          data: values
        }
      ]
    }
  })
  
  const chartOptions = {
    responsive: true,
    indexAxis: 'y', // Horizontal bar chart
    scales: {
      x: {
        title: {
          display: true,
          text: 'Doanh thu (VNĐ)'
        },
        ticks: {
          callback: function(value) {
            return formatCurrency(value);
          }
        }
      }
    },
    plugins: {
      legend: {
        display: false
      },
      tooltip: {
        callbacks: {
          label: function(context) {
            let label = context.dataset.label || '';
            if (label) {
              label += ': ';
            }
            label += formatCurrency(context.raw);
            return label;
          }
        }
      }
    }
  }
  </script>