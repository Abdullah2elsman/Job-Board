<script setup>
import { ref, onMounted } from 'vue'
import api from '../../services/api'
import AnalyticsChart from '../../components/admin/AnalyticsChart.vue'

const analyticsData = ref(null)
const loading = ref(true)
const error = ref(null)

const fetchAnalytics = async () => {
  try {
    const { data } = await api.get('/api/analytics')
    analyticsData.value = data?.data || null
  } catch (err) {
    error.value = 'Failed to load analytics'
  } finally {
    loading.value = false
  }
}

const updateJobStatus = async (jobId, action) => {
  try {
    await api.post(`/api/jobs/${jobId}/${action}`)
    fetchAnalytics()
  } catch (err) {
    alert(err.response?.data?.message || `Failed to ${action} job`)
  }
}

onMounted(() => {
  fetchAnalytics()
})
</script>

<template>
  <div class="layout" style="gap: 2rem;">
    <header class="flex-between">
      <div>
        <h1 style="margin: 0;">Admin Overview</h1>
        <p class="muted">Monitor platform activity and manage job approvals</p>
      </div>
      <button class="btn btn--ghost" @click="fetchAnalytics" :disabled="loading">
        {{ loading ? 'Updating...' : 'Refresh Data' }}
      </button>
    </header>

    <div v-if="loading" class="loading">Loading platform metrics...</div>
    <div v-else-if="error" class="alert alert--error">{{ error }}</div>
    
    <template v-else-if="analyticsData">
      <!-- Stats Grid -->
      <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
        <div class="card text-center" style="padding: 2rem;">
          <h2 style="font-size: 2.5rem; color: var(--primary); margin-bottom: 0.5rem;">{{ analyticsData.totals.jobs }}</h2>
          <p class="muted" style="text-transform: uppercase; font-size: 0.75rem; font-weight: 700; letter-spacing: 0.1em;">Total Jobs</p>
        </div>
        <div class="card text-center" style="padding: 2rem;">
          <h2 style="font-size: 2.5rem; color: var(--success); margin-bottom: 0.5rem;">{{ analyticsData.totals.applications }}</h2>
          <p class="muted" style="text-transform: uppercase; font-size: 0.75rem; font-weight: 700; letter-spacing: 0.1em;">Applications</p>
        </div>
        <div class="card text-center" style="padding: 2rem;">
          <h2 style="font-size: 2.5rem; color: var(--warning); margin-bottom: 0.5rem;">{{ analyticsData.totals.job_views }}</h2>
          <p class="muted" style="text-transform: uppercase; font-size: 0.75rem; font-weight: 700; letter-spacing: 0.1em;">Job Views</p>
        </div>
        <div class="card text-center" style="padding: 2rem;">
          <h2 style="font-size: 2.5rem; color: var(--text-primary); margin-bottom: 0.5rem;">{{ analyticsData.totals.payments }}</h2>
          <p class="muted" style="text-transform: uppercase; font-size: 0.75rem; font-weight: 700; letter-spacing: 0.1em;">Revenue</p>
        </div>
      </div>

      <div class="card" style="padding: 2.5rem;">
        <h3 style="margin-bottom: 1.5rem;">Activity Trends</h3>
        <AnalyticsChart :data="analyticsData" />
      </div>

      <section>
        <h3 style="margin-bottom: 1.5rem;">Pending Approvals</h3>
        <div class="job-list">
          <article 
            v-for="job in analyticsData.jobs.filter(j => j.status === 'pending')" 
            :key="job.id" 
            class="job-card"
            style="background: #ffffff;"
          >
            <div class="job-card__header">
              <div>
                <h4 style="font-size: 1.15rem; margin-bottom: 0.25rem;">{{ job.title }}</h4>
                <p class="muted" style="font-size: 0.9rem;">{{ job.category }} • {{ job.location }}</p>
              </div>
              <span class="badge badge--warning">REVIEW REQUIRED</span>
            </div>
            <div class="flex-wrap" style="margin-top: 1.5rem; justify-content: flex-end;">
              <router-link :to="`/jobs/${job.id}`" class="btn btn--ghost" style="font-size: 0.85rem;">View Details</router-link>
              <button class="btn" style="background: var(--success); font-size: 0.85rem;" @click="updateJobStatus(job.id, 'approve')">Approve Job</button>
              <button class="btn btn--danger" style="font-size: 0.85rem;" @click="updateJobStatus(job.id, 'reject')">Reject</button>
            </div>
          </article>

          <div v-if="!analyticsData.jobs.some(j => j.status === 'pending')" class="card text-center" style="padding: 3rem; background: var(--bg-secondary); border: 1.5px dashed var(--border-color);">
            <p class="muted">All caught up! No pending jobs to review.</p>
          </div>
        </div>
      </section>
    </template>
  </div>
</template>
