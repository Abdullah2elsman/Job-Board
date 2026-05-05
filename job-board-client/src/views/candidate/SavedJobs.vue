<script setup>
import { ref, onMounted } from 'vue'
import api from '../../services/api'
import { useRouter } from 'vue-router'

const router = useRouter()
const savedJobs = ref([])
const loading = ref(true)
const error = ref(null)

const fetchSavedJobs = async () => {
  try {
    const { data } = await api.get('/api/saved-jobs')
    savedJobs.value = data?.data || data || []
  } catch (err) {
    error.value = 'Failed to load saved jobs'
  } finally {
    loading.value = false
  }
}

const unsaveJob = async (id) => {
  try {
    await api.post(`/api/jobs/${id}/save`)
    savedJobs.value = savedJobs.value.filter(job => job.id !== id)
  } catch (err) {
    alert('Failed to remove job')
  }
}

const viewJob = (id) => {
  router.push(`/jobs/${id}`)
}

onMounted(() => {
  fetchSavedJobs()
})
</script>

<template>
  <div class="layout" style="gap: 2rem;">
    <header class="flex-between">
      <div>
        <h1 style="margin: 0;">Saved Jobs</h1>
        <p class="muted">Jobs you've bookmarked for later</p>
      </div>
      <button class="btn btn--ghost" @click="fetchSavedJobs" :disabled="loading">
        {{ loading ? 'Refreshing...' : 'Refresh' }}
      </button>
    </header>

    <div v-if="loading" class="loading">Loading your bookmarks...</div>
    <div v-else-if="error" class="alert alert--error">{{ error }}</div>
    
    <div v-else class="job-list">
      <article v-for="job in savedJobs" :key="job.id" class="card" style="padding: 1.75rem;">
        <div class="flex-between" style="align-items: flex-start;">
          <div style="flex: 1; cursor: pointer;" @click="viewJob(job.id)">
            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.5rem;">
              <h3 style="margin: 0;">{{ job.title }}</h3>
              <span class="badge badge--success" v-if="job.salary">${{ job.salary }}</span>
            </div>
            <p class="muted" style="margin-bottom: 1rem;">
              {{ job.employer?.name }} • {{ job.location }} • {{ job.work_type }}
            </p>
            <div class="flex-wrap" style="gap: 0.5rem;">
              <span v-for="skill in job.skills" :key="skill.id" class="chip" style="font-size: 0.7rem;">
                {{ skill.skill_name }}
              </span>
            </div>
          </div>
          <div class="flex-wrap" style="gap: 0.75rem;">
            <button @click="viewJob(job.id)" class="btn btn--ghost" style="font-size: 0.85rem;">View</button>
            <button @click="unsaveJob(job.id)" class="btn btn--danger" style="font-size: 0.85rem; padding: 0.6rem 1rem;">Remove</button>
          </div>
        </div>
      </article>

      <div v-if="!savedJobs.length" class="card text-center" style="padding: 4rem 2rem;">
        <div style="font-size: 3rem; margin-bottom: 1rem;">⭐</div>
        <h3 class="muted">Your list is empty</h3>
        <p class="muted">Save jobs you're interested in to track them here.</p>
        <router-link to="/" class="btn" style="margin-top: 1.5rem;">Discover Jobs</router-link>
      </div>
    </div>
  </div>
</template>
