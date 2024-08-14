<template>
  <div>
    <canvas ref="radarChart"></canvas>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import { Chart, RadarController, RadialLinearScale, PointElement, LineElement, Filler, Tooltip, Legend } from 'chart.js';

// Register the Chart.js components globally
Chart.register(RadarController, RadialLinearScale, PointElement, LineElement, Filler, Tooltip, Legend);


const rotatedBackgroundPlugin = {
        id: 'rotatedBackgroundPlugin',
        beforeDatasetsDraw: function(chart) {
          const ctx = chart.ctx;
          const chartArea = chart.chartArea;
          const centerX = (chartArea.left + chartArea.right) / 2;
          const centerY = (chartArea.top + chartArea.bottom) / 2;
          const scale = chart.scales.r;

          const valueToRadius = value => {
            const radius = scale.getDistanceFromCenterForValue(value);
            return Math.max(radius, 0); // Ensure the radius is non-negative
          };

          const drawArc = (innerRadius, outerRadius, color) => {
            if (outerRadius > 0 && innerRadius >= 0 && outerRadius !== innerRadius) {
              ctx.beginPath();
              ctx.arc(centerX, centerY, outerRadius, 0, 2 * Math.PI);
              ctx.lineTo(centerX, centerY);
              ctx.arc(centerX, centerY, innerRadius, 2 * Math.PI, 0, true);
              ctx.closePath();
              ctx.fillStyle = color;
              ctx.fill();
            }
          };

          // Save the current context state
          ctx.save();
          
          // Rotate the canvas
          ctx.translate(centerX, centerY);
          ctx.rotate(-Math.PI / 4); // Rotate by -45 degrees
          ctx.translate(-centerX, -centerY);

          // Draw the colored background regions
          drawArc(0, valueToRadius(6), 'rgba(255, 0, 0, 0.2)');   // Red for 0-6
          drawArc(valueToRadius(6), valueToRadius(8), 'rgba(255, 255, 0, 0.2)'); // Yellow for 6-8
          drawArc(valueToRadius(8), valueToRadius(10), 'rgba(0, 255, 0, 0.2)');  // Green for 8-10

          // Restore the original context state
          ctx.restore();
        }
      };

export default {
  setup() {
    const radarChart = ref(null);
    const chartInstance = ref(null);

    const chartData = {
            labels: ['Test 1', 'Test 2', 'Test 3', 'Test 4', 'Test 5', 'Test 6', 'Test 7', 'Test 8', 'Test 9', 'Test 10', 'Test 11', 'Test 12'],
            datasets: [
                {
                    label: 'Dataset 1',
                    data: [5, 6, 7, 8, 6, 7, 8, 9, 7, 8, 9, 10],
                    backgroundColor: 'rgba(0, 123, 255, 0.3)', // Light blue fill for visibility
                    borderColor: 'rgba(0, 123, 255, 1)', // Blue border
                    borderWidth: 2,
                    fill: true // Ensure the dataset is filled
                }
            ]
        };

    const chartOptions = {
      scales: {
                    r: {
                        beginAtZero: true,
                        suggestedMin: 0,
                        suggestedMax: 10,
                        ticks: {
                            stepSize: 1,
                            display: true
                        }
                    }
                }
    };

    onMounted(() => {

      if (radarChart.value) {
        // Register the plugin before creating the chart
        Chart.register(rotatedBackgroundPlugin);

        // Create the chart instance
        chartInstance.value = new Chart(radarChart.value, {
          type: 'radar',
          data: chartData,
          options: chartOptions,
          plugins: [rotatedBackgroundPlugin]
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
