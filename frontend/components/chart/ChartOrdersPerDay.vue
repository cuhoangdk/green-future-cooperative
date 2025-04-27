<template>
    <div class="chart-container">
      <h3 class="text-lg font-semibold mb-2">Đơn hàng theo ngày</h3>
      <Line :data="chartData" :options="chartOptions" />
    </div>
  </template>
  
  <script setup>
  import { Line } from 'vue-chartjs'
  import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend
  } from 'chart.js'
  
  ChartJS.register(
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend
  )
  
  const props = defineProps({
    data: {
      type: Object,
      required: true
    }
  })
  
  const chartData = computed(() => {
    const dates = Object.keys(props.data)
    const orderCounts = dates.map(date => props.data[date].count)
    const totalAmounts = dates.map(date => props.data[date].total_amount)
    
    // Format dates for display
    const formattedDates = dates.map(date => {
      const [year, month, day] = date.split('-')
      return `${day}/${month}`
    })
    
    return {
      labels: formattedDates,
      datasets: [
        {
          label: 'Số đơn hàng',
          backgroundColor: 'rgba(75, 192, 192, 0.2)',
          borderColor: 'rgba(75, 192, 192, 1)',
          borderWidth: 2,
          pointBackgroundColor: 'rgba(75, 192, 192, 1)',
          data: orderCounts,
          yAxisID: 'y'
        },
        {
          label: 'Doanh thu (VNĐ)',
          backgroundColor: 'rgba(153, 102, 255, 0.2)',
          borderColor: 'rgba(153, 102, 255, 1)',
          borderWidth: 2,
          pointBackgroundColor: 'rgba(153, 102, 255, 1)',
          data: totalAmounts,
          yAxisID: 'y1'
        }
      ]
    }
  })
  
  const chartOptions = {
    responsive: true,
    scales: {
      y: {
        type: 'linear',
        display: true,
        position: 'left',
        title: {
          display: true,
          text: 'Số đơn hàng'
        }
      },
      y1: {
        type: 'linear',
        display: true,
        position: 'right',
        grid: {
          drawOnChartArea: false
        },
        title: {
          display: true,
          text: 'Doanh thu (VNĐ)'
        },
        ticks: {
          // Format y1 axis ticks as currency
          callback: function(value) {
            return new Intl.NumberFormat('vi-VN', { 
              style: 'currency', 
              currency: 'VND',
              maximumFractionDigits: 0 
            }).format(value);
          }
        }
      },
      x: {
        title: {
          display: true,
          text: 'Ngày'
        }
      }
    },
    plugins: {
      legend: {
        position: 'top'
      },
      tooltip: {
        callbacks: {
          label: function(context) {
            let label = context.dataset.label || '';
            if (label) {
              label += ': ';
            }
            if (context.datasetIndex === 1) {
              label += new Intl.NumberFormat('vi-VN', { 
                style: 'currency', 
                currency: 'VND' 
              }).format(context.raw);
            } else {
              label += context.raw;
            }
            return label;
          }
        }
      }
    }
  }
  </script>