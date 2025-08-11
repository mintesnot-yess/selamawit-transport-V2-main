<!-- src/components/SalesChart.vue -->
<template>
  <canvas ref="chartCanvas"></canvas>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, watch } from "vue";
import { Chart } from "chart.js/auto";

const props = defineProps({
  labels: {
    type: Array,
    required: true,
  },
  incomeData: {
    type: Array,
    required: true,
  },
  expenseData: {
    type: Array,
    required: true,
  },
});

const chartCanvas = ref(null);
let chartInstance = null;

const initChart = () => {
  if (!chartCanvas.value) return;

  const ctx = chartCanvas.value.getContext("2d");

  chartInstance = new Chart(ctx, {
    type: "line",
    data: {
      labels: props.labels,
      datasets: [
        {
          label: "Income",
          data: props.incomeData,
          borderColor: "#6366f1",
          backgroundColor: "rgba(99, 102, 241, 0.1)",
          borderWidth: 2,
          tension: 0.3,
          fill: true,
        },
        {
          label: "Expenses",
          data: props.expenseData,
          borderColor: "#cbd5e1",
          backgroundColor: "rgba(203, 213, 225, 0.1)",
          borderWidth: 2,
          tension: 0.3,
          fill: true,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false,
        },
        tooltip: {
          mode: "index",
          intersect: false,
          callbacks: {
            label: (context) => `ETB ${context.parsed.y.toLocaleString()}`,
          },
        },
      },
      scales: {
        y: {
          beginAtZero: false,
          grid: {
            drawBorder: false,
            color: "#e2e8f0",
          },
          ticks: {
            callback: (value) => `${value.toLocaleString()}`,
          },
        },
        x: {
          grid: {
            display: false,
            drawBorder: false,
          },
        },
      },
    },
  });
};

onMounted(() => {
  initChart();
});

onBeforeUnmount(() => {
  if (chartInstance) {
    chartInstance.destroy();
  }
});

// Re-render chart if data changes
watch(
  () => [props.labels, props.incomeData, props.expenseData],
  () => {
    if (chartInstance) {
      chartInstance.destroy();
    }
    initChart();
  },
  { deep: true }
);
</script>

<style scoped>
canvas {
  height: 350px;
  width: 100%;
}
</style>

 