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
    // Refresh analytics to get updated lists
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
  <div class="grid" style="grid-template-columns: 1fr;">
    <section class="card">
      <header class="card__header">
        <h2>Admin Dashboard</h2>
        <button class="btn btn--ghost" @click="fetchAnalytics" :disabled="loading">
          {{ loading ? 'Refreshing...' : 'Refresh' }}
        </button>
      </header>

      <div v-if="loading" class="loading">Loading dashboard...</div>
      <div v-else-if="error" class="alert alert--error">{{ error }}</div>
      
      <template v-else-if="analyticsData">
        <div class="grid" style="grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 2rem;">
          <div class="card text-center" style="padding: 1.5rem;">
            <h3>{{ analyticsData.totals.jobs }}</h3>
            <p class="muted">Total Jobs</p>
          </div>
          <div class="card text-center" style="padding: 1.5rem;">
            <h3>{{ analyticsData.totals.applications }}</h3>
            <p class="muted">Total Applications</p>
          </div>
          <div class="card text-center" style="padding: 1.5rem;">
            <h3>{{ analyticsData.totals.job_views }}</h3>
            <p class="muted">Total Job Views</p>
          </div>
          <div class="card text-center" style="padding: 1.5rem;">
            <h3>{{ analyticsData.totals.payments }}</h3>
            <p class="muted">Total Payments</p>
          </div>
        </div>

        <div class="card" style="margin-bottom: 2rem; padding: 1.5rem;">
          <h3 style="margin-bottom: 1rem;">Platform Analytics</h3>
          <AnalyticsChart :data="analyticsData" />
        </div>

        <div>
          <h3>Pending Job Approvals</h3>
          <div class="job-list" style="margin-top: 1rem;">
            <article 
              v-for="job in analyticsData.jobs.filter(j => j.status === 'pending')" 
              :key="job.id" 
              class="job-card"
            >
              <div class="job-card__header">
                <div>
                  <h4>{{ job.title }}</h4>
                  <p class="muted">{{ job.category }} • {{ job.location }}</p>
                </div>
                <span class="badge badge--warning">PENDING</span>
              </div>
              <div style="margin-top: 1rem; display: flex; gap: 0.5rem;">
                <router-link :to="`/jobs/${job.id}`" class="btn btn--ghost">View Job</router-link>
                <button class="btn btn--success" style="background-color: var(--success); margin-left: auto;" @click="updateJobStatus(job.id, 'approve')">Approve</button>
                <button class="btn btn--danger" @click="updateJobStatus(job.id, 'reject')">Reject</button>
              </div>
            </article>

            <p v-if="!analyticsData.jobs.some(j => j.status === 'pending')" class="muted text-center" style="padding: 1rem;">
              No pending jobs to review.
            </p>
          </div>
        </div>
      </template>
    </section>
  </div>
</template>
