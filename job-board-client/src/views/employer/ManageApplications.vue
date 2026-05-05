<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import api from '../../services/api'

const route = useRoute()
const applications = ref([])
const jobTitle = ref('')
const loading = ref(true)
const error = ref(null)

const fetchApplications = async () => {
  try {
    const { data } = await api.get(`/api/jobs/${route.params.jobId}/applications`)
    applications.value = data?.data || []
    jobTitle.value = data?.job_title || ''
  } catch (err) {
    error.value = 'Failed to load applications for this job'
  } finally {
    loading.value = false
  }
}

const updateStatus = async (appId, status) => {
  try {
    await api.put(`/api/applications/${appId}/status`, { status })
    // Update local state
    const app = applications.value.find(a => a.id === appId)
    if (app) app.status = status
  } catch (err) {
    alert(err.response?.data?.message || 'Failed to update status')
  }
}

onMounted(() => {
  fetchApplications()
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
      <div>
        <h2>Manage Applications</h2>
        <p class="muted">Job: {{ jobTitle }}</p>
      </div>
      <router-link to="/dashboard/employer" class="btn btn--ghost">Back to Dashboard</router-link>
    </header>

    <div v-if="loading" class="loading">Loading applications...</div>
    <div v-else-if="error" class="alert alert--error">{{ error }}</div>
    <div v-else class="job-list">
      <article v-for="app in applications" :key="app.id" class="job-card">
        <div class="job-card__header">
          <div>
            <h3>{{ app.candidate?.name || 'Unknown Candidate' }}</h3>
            <p class="muted">Email: {{ app.candidate?.email }}</p>
            <p class="muted">Applied: {{ new Date(app.created_at).toLocaleDateString() }}</p>
          </div>
          <span :class="getStatusBadge(app.status)">{{ app.status.toUpperCase() }}</span>
        </div>
        
        <div v-if="app.cover_letter" style="margin-top: 1rem;">
          <h4>Cover Letter</h4>
          <p style="white-space: pre-wrap; font-size: 0.9em; background: #f9f9f9; padding: 1rem; border-radius: 4px;">{{ app.cover_letter }}</p>
        </div>

        <div style="margin-top: 1rem; display: flex; gap: 0.5rem; flex-wrap: wrap;">
          <a v-if="app.resume_path" :href="`http://127.0.0.1:8000/storage/${app.resume_path}`" target="_blank" class="btn btn--ghost">
            Download Resume
          </a>
          
          <div style="margin-left: auto; display: flex; gap: 0.5rem;">
            <button v-if="app.status !== 'interviewing'" class="btn btn--ghost" @click="updateStatus(app.id, 'interviewing')">
              Mark Interviewing
            </button>
            <button v-if="app.status !== 'accepted'" class="btn btn--success" style="background-color: var(--success);" @click="updateStatus(app.id, 'accepted')">
              Accept
            </button>
            <button v-if="app.status !== 'rejected'" class="btn btn--danger" @click="updateStatus(app.id, 'rejected')">
              Reject
            </button>
          </div>
        </div>
      </article>

      <p v-if="!applications.length" class="muted text-center" style="padding: 2rem;">
        No applications received yet for this job.
      </p>
    </div>
  </section>
</template>
