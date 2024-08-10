<template>
    <div>
      <canvas ref="radarChart"></canvas>
    </div>
  </template>
  
  <script>
  import { ref, onMounted } from 'vue';
  import { Chart, RadarController, RadialLinearScale, PointElement, LineElement, Filler, Tooltip, Legend } from 'chart.js';
  
  Chart.register(RadarController, RadialLinearScale, PointElement, LineElement, Filler, Tooltip, Legend);
  
  export default {
    setup() {
      const radarChart = ref(null);
      const chartInstance = ref(null);
  
      const chartData = {
        labels: ['Eating', 'Drinking', 'Sleeping', 'Designing', 'Coding', 'Cycling', 'Running'],
        datasets: [
          {
            label: 'My First Dataset',
            data: [65, 59, 90, 81, 56, 55, 40],
            fill: true,
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            borderColor: 'rgb(255, 99, 132)',
            pointBackgroundColor: 'rgb(255, 99, 132)',
            pointBorderColor: '#fff',
            pointHoverBackgroundColor: '#fff',
            pointHoverBorderColor: 'rgb(255, 99, 132)'
          }
        ]
      };
  
      const chartOptions = {
        scales: {
          r: {
            ticks: { beginAtZero: true }
          }
        }
      };
  
      onMounted(() => {
        if (radarChart.value) {
          chartInstance.value = new Chart(radarChart.value, {
            type: 'radar',
            data: chartData,
            options: chartOptions
          });
        }
      });
  
      return {
        radarChart
      };
    }
  };
  </script>
  
  <style>
  canvas {
    max-width: 600px;
    max-height: 600px;
  }
  </style>
  