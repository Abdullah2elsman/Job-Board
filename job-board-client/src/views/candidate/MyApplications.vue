<script setup>
import { ref, onMounted } from 'vue'
import api from '../../services/api'

const applications = ref([])
const loading = ref(true)
const error = ref(null)

const fetchMyApplications = async () => {
  try {
    const { data } = await api.get('/api/my-applications')
    applications.value = data?.data || data || []
  } catch (err) {
    error.value = 'Failed to load applications'
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchMyApplications()
})

const getStatusBadge = (status) => {
  switch (status) {
    case 'accepted': return 'badge badge--success'
    case 'rejected': return 'badge badge--danger'
    case 'interviewing': return 'badge badge--warning'
    default: return 'badge'
  }
}
</script>

<template>
  <section class="card">
    <header class="card__header">
      <h2>My Applications</h2>
      <button class="btn btn--ghost" @click="fetchMyApplications" :disabled="loading">
        {{ loading ? 'Refreshing...' : 'Refresh' }}
      </button>
    </header>

    <div v-if="loading" class="loading">Loading your applications...</div>
    <div v-else-if="error" class="alert alert--error">{{ error }}</div>
    <div v-else class="job-list">
      <article v-for="app in applications" :key="app.id" class="job-card">
        <div class="job-card__header">
          <div>
            <h3>{{ app.job?.title || 'Unknown Job' }}</h3>
            <p class="muted">Applied on: {{ new Date(app.created_at).toLocaleDateString() }}</p>
          </div>
          <span :class="getStatusBadge(app.status)">{{ app.status.toUpperCase() }}</span>
        </div>
        
        <div style="margin-top: 1rem; display: flex; gap: 1rem;">
          <router-link :to="`/jobs/${app.job_id}`" class="btn btn--ghost">View Job</router-link>
          <a v-if="app.resume_path" :href="`http://127.0.0.1:8000/storage/${app.resume_path}`" target="_blank" class="btn btn--ghost">
            View My Resume
          </a>
        </div>
      </article>

      <p v-if="!applications.length" class="muted text-center" style="padding: 2rem;">
        You haven't applied to any jobs yet.
      </p>
    </div>
  </section>
</template>
