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

const cancelApplication = async (id) => {
  if (!confirm('Are you sure you want to cancel this application?')) return
  try {
    await api.delete(`/api/applications/${id}`)
    applications.value = applications.value.filter(app => app.id !== id)
  } catch (err) {
    alert(err.response?.data?.message || 'Failed to cancel application')
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
  <div class="layout" style="gap: 2rem;">
    <header class="flex-between">
      <div>
        <h1 style="margin: 0;">My Applications</h1>
        <p class="muted">Track the status of your job applications</p>
      </div>
      <button class="btn btn--ghost" @click="fetchMyApplications" :disabled="loading">
        {{ loading ? 'Refreshing...' : 'Refresh' }}
      </button>
    </header>

    <div v-if="loading" class="loading">Fetching your applications...</div>
    <div v-else-if="error" class="alert alert--error">{{ error }}</div>
    
    <div v-else class="job-list">
      <article v-for="app in applications" :key="app.id" class="card" style="padding: 1.75rem;">
        <div class="flex-between" style="margin-bottom: 1.5rem; align-items: flex-start;">
          <div style="flex: 1;">
            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.5rem;">
              <h3 style="margin: 0;">{{ app.job?.title || 'Job Title' }}</h3>
              <span :class="getStatusBadge(app.status)">{{ app.status.toUpperCase() }}</span>
            </div>
            <p class="muted" style="font-size: 0.9rem;">
              Applied on {{ new Date(app.created_at).toLocaleDateString() }} • {{ app.job?.location || 'Location' }}
            </p>
          </div>
          <div class="flex-wrap" style="gap: 0.75rem;">
            <router-link :to="`/jobs/${app.job_id}`" class="btn btn--ghost" style="font-size: 0.85rem;">View Job</router-link>
            <a v-if="app.resume_path" :href="`http://127.0.0.1:8000/storage/${app.resume_path}`" target="_blank" class="btn btn--ghost" style="font-size: 0.85rem;">
              My Resume
            </a>
            <button @click="cancelApplication(app.id)" class="btn btn--danger" style="font-size: 0.85rem; padding: 0.6rem 1rem;">Cancel</button>
          </div>
        </div>
        
        <div v-if="app.cover_letter" style="background: var(--bg-secondary); padding: 1rem; border-radius: var(--radius-sm);">
          <p class="muted" style="font-size: 0.8rem; font-weight: 700; text-transform: uppercase; margin-bottom: 0.5rem;">Cover Letter Snippet</p>
          <p style="margin: 0; font-size: 0.9rem; color: var(--text-secondary);">{{ app.cover_letter }}</p>
        </div>
      </article>

      <div v-if="!applications.length" class="card text-center" style="padding: 4rem 2rem;">
        <div style="font-size: 3rem; margin-bottom: 1rem;">📝</div>
        <h3 class="muted">No applications found</h3>
        <p class="muted">You haven't applied to any jobs yet. Browse the feed to find your next opportunity.</p>
        <router-link to="/" class="btn" style="margin-top: 1.5rem;">Browse Jobs</router-link>
      </div>
    </div>
  </div>
</template>
