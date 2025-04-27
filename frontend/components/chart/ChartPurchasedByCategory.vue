<template>
    <div class="chart-container">
      <h3 class="text-lg font-semibold mb-2">Doanh thu theo danh mục</h3>
      <Pie :data="chartData" :options="chartOptions" />
    </div>
  </template>
  
  <script setup>
  import { Pie } from 'vue-chartjs'
  import { Chart as ChartJS, ArcElement, Tooltip, Legend } from 'chart.js'
  
  ChartJS.register(ArcElement, Tooltip, Legend)
  
  const props = defineProps({
    data: {
      type: Array,
      required: true
    }
  })
  
  const chartData = computed(() => {
    const labels = props.data.map(item => item.name)
    const values = props.data.map(item => parseFloat(item.total_revenue))
    
    // Generate dynamic colors
    const colors = getColors(labels.length)
    
    return {
      labels,
      datasets: [
        {
          backgroundColor: colors,
          data: values
        }
      ]
    }
  })
  
  // Function to generate colors
  const getColors = (count) => {
    const baseColors = ['#4CAF50', '#2196F3', '#F44336', '#FFC107', '#9C27B0', '#00BCD4', '#FF9800', '#795548']
    
    if (count <= baseColors.length) {
      return baseColors.slice(0, count)
    }
    
    // If we need more colors, generate them
    const colors = [...baseColors]
    for (let i = baseColors.length; i < count; i++) {
      const r = Math.floor(Math.random() * 255)
      const g = Math.floor(Math.random() * 255)
      const b = Math.floor(Math.random() * 255)
      colors.push(`rgb(${r}, ${g}, ${b})`)
    }
    
    return colors
  }
  
  const chartOptions = {
    responsive: true,
    plugins: {
      legend: {
        position: 'bottom',
        labels: {
          boxWidth: 12
        }
      },
      tooltip: {
        callbacks: {
          label: function(context) {
            let label = context.label || '';
            if (label) {
              label += ': ';
            }
            let value = context.raw || 0;
            const percentage = props.data[context.dataIndex].percentage;
            label += new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value);
            label += ` (${percentage}%)`;
            return label;
          }
        }
      }
    }
  }
  </script>