<script setup>
import { computed } from 'vue'
import { Chart as ChartJS, CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend } from 'chart.js'
import { Bar } from 'vue-chartjs'

ChartJS.register(CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend)

const props = defineProps({
  data: {
    type: Object,
    required: true
  }
})

const chartData = computed(() => {
  if (!props.data || !props.data.totals) return { labels: [], datasets: [] }
  
  return {
    labels: ['Total Jobs', 'Approved', 'Pending', 'Rejected'],
    datasets: [
      {
        label: 'Job Status Breakdown',
        backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444'],
        data: [
          props.data.totals.jobs || 0,
          props.data.totals.approved_jobs || 0,
          props.data.totals.pending_jobs || 0,
          props.data.totals.rejected_jobs || 0
        ]
      }
    ]
  }
})

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false
}
</script>

<template>
  <div style="height: 300px; width: 100%;">
    <Bar v-if="chartData.labels.length" :data="chartData" :options="chartOptions" />
    <div v-else class="text-center muted" style="padding: 2rem;">No analytics data available</div>
  </div>
</template>
