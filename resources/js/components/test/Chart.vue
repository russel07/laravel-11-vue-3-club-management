<template>
  <div class="common-layout">
    <el-container class="full-height">
      <el-main class="main-center">
        <Header :pageTitle="'Harjoitettavuustesti Chart'"/>
        <div class="chart-wrapper">
          <div class="test-info">
            <p><strong>Test Date:</strong> {{ formatDate(testInfo.test_date) }} </p>
          </div>
          <canvas ref="radarChart"></canvas>
        </div>
      </el-main>
      <el-footer>
          <Footer/>
      </el-footer>
    </el-container>
  </div>
</template>

<script>
  import { ref, onMounted } from 'vue';
  import { Chart, RadarController, RadialLinearScale, PointElement, LineElement, Filler, Tooltip, Legend } from 'chart.js';
  import { useRoute } from 'vue-router'; 
  import Header from "../Header";
  import Footer from "../Footer";
  import http from "../../http/http-common";
  import { loader } from '../../composables/Loader';

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
            return Math.max(radius, 0);
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
          drawArc(valueToRadius(4), valueToRadius(6), 'rgba(255, 0, 0, 0.2)');   // Red for 4-6
          drawArc(valueToRadius(6), valueToRadius(8), 'rgba(255, 255, 0, 0.2)'); // Yellow for 6-8
          drawArc(valueToRadius(8), valueToRadius(10), 'rgba(0, 255, 0, 0.2)');  // Green for 8-10

          // Restore the original context state
          ctx.restore();
        }
      };

export default {
  name: 'Chart',
  components: {
    Header,
    Footer
  },
  setup() {
    const { startLoading, stopLoading } = loader();
    const route                         = useRoute(); 
    const testInfo                      = ref([]);
    const testId                        = ref(route.params.testId ? route.params.testId : '');
    const gender                        = ref('');
    const app_ready                     = ref(false);
    const radarChart = ref(null);
    const chartInstance = ref(null);
    const test_labels = [
      "Ylävartalon kestovoima (Etunojapunnerrus)",
      "Yläraajojen kestovoima (Leuanveto)",
      "Yläraajojen vetävä voima ja tukilihasten staattinen kesto (Kulmaveto)",
      "Keski- ja ylävartalon voima (Jalkojen nosto riipunnasta)",
      "Alaraajojen voima/hallinta (Yhden jalan kyykky)",
      "Alaraajojen Räjähtävyys (Vauhditon Pituus)",
      "Nilkan liikkuvuus ja puolierot (Nilkan liikkuvuustesti)",
      "Alaraajan lihaksiston liikkuvuus (Aktiivinen suoran jalan nosto selin)",
      "Alaraajojen ojennusliikkuvuus (Reisi, Sääri, Jalkalinja - Thomas1,2&3)",
      "Vartalonkierto (Lying Trunk Rotation)",
      "Liikkuvuus (Valakyykky Kepillä)",
      "Hartiarenkaan liikkuvuus (Olkanivel, Lapa, solisluu, Rintakehä)"
  ];

    const min_max = {
      'test_1': {
        'Male' : {
          'min': 0,
          'max': 42
        },
        'Female' : {
          'min': 0,
          'max': 33
        }
      },
      'test_2': {
        'Male' : {
          'min': 1,
          'max': 17
        },
        'Female' : {
          'min': 0,
          'max': 19
        }
      },
      'test_3': {
        'Male' : {
          'min': 3,
          'max': 26
        },
        'Female' : {
          'min': 3,
          'max': 19
        }
      },
      'test_4': {
        'Male' : {
          'min': 5,
          'max': 21
        },
        'Female' : {
          'min': 2,
          'max': 17
        }
      },
      'test_6': {
        'Male' : {
          'min': 188,
          'max': 264
        },
        'Female' : {
          'min': 169,
          'max': 231
        }
      }
    }

    const chartData = {
      labels: test_labels,
      datasets: []
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

    const formatDate = (dateString) => {
            const date = new Date(dateString);
            const dateOptions = { day: 'numeric', month: 'long', year: 'numeric' };
            const timeOptions = { hour: 'numeric', minute: 'numeric', hour12: true };
            const formattedDate = date.toLocaleDateString('en-US', dateOptions);
            const formattedTime = date.toLocaleTimeString('en-US', timeOptions);
            return `${formattedDate} at ${formattedTime}`;
        }

    const fetchTests = async () => {
      startLoading('Fetching tests...');
      try {
          const response = await http.get(`test/${testId.value}`);
          testInfo.value = response.data.data;
          gender.value = response.data.data.user.gender;
          prepareData();
      } catch (err) {
          //error(err.response.data.message);
      }
      stopLoading();
    }

    const linearTransform = (value, min, max, newMin = 4, newMax = 10) => {
      // Ensure the source range is valid
      if (min === max) {
        return 0;
      }

      // Calculate the transformed value
      const transformedValue = newMin + ((value - min) / (max - min)) * (newMax - newMin);

      // Return the value rounded to one decimal place
      return parseFloat(transformedValue.toFixed(1));
    }

    const prepareData = () => {
      let test_results = JSON.parse(testInfo.value.test_results);
      let data_set = [];
      Object.keys(test_results).forEach((key) => {
        if (['test_1', 'test_2', 'test_3', 'test_4', 'test_6'].includes(key)) {
          let min = min_max[key][gender.value].min;
          let max = min_max[key][gender.value].max;
          let val = linearTransform(test_results[key], min, max);
          data_set.push(val);
        } else {
          data_set.push(test_results[key]);
        }
      });

      const dataset = {
          label: testInfo.value.user.name,
          data: data_set,
          backgroundColor: 'rgba(0, 123, 255, 0.3)',
          borderColor: 'rgba(0, 123, 255, 1)',
          borderWidth: 2,
          fill: true
        }
        chartData.datasets.push(dataset);
      app_ready.value = true;

      if (app_ready.value && radarChart.value) {
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
    }

    onMounted(() => { console.log("here");
      if(testId.value){
        fetchTests();
      }
    });

    return {
      radarChart,
      testInfo,
      formatDate
    };
  }
};
</script>
