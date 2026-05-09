<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '../../services/api'
import { useRouter } from 'vue-router'
import '../../assets/saved-jobs.css'

const router = useRouter()
const savedJobs = ref([])
const loading = ref(true)
const error = ref(null)
const search = ref('')

const filtered = computed(() => {
  const q = search.value.toLowerCase()
  if (!q) return savedJobs.value
  return savedJobs.value.filter(j =>
    j.title?.toLowerCase().includes(q) ||
    j.employer?.name?.toLowerCase().includes(q) ||
    j.location?.toLowerCase().includes(q)
  )
})

const fetchSavedJobs = async () => {
  loading.value = true
  try {
    const { data } = await api.get('/api/saved-jobs')
    savedJobs.value = data?.data || data || []
  } catch {
    error.value = 'Failed to load saved jobs'
  } finally {
    loading.value = false
  }
}

const unsaveJob = async (id) => {
  try {
    await api.post(`/api/jobs/${id}/save`)
    savedJobs.value = savedJobs.value.filter(j => j.id !== id)
  } catch {
    alert('Failed to remove job')
  }
}

onMounted(fetchSavedJobs)
</script>

<template>
  <div class="page">

    <!-- Header -->
    <div class="page-header">
      <div class="page-header__left">
        <div class="page-icon">⭐</div>
        <div>
          <h1 class="page-title">Saved Jobs</h1>
          <p class="muted">{{ savedJobs.length }} job{{ savedJobs.length !== 1 ? 's' : '' }} bookmarked</p>
        </div>
      </div>
      <button class="btn btn--ghost btn--sm" @click="fetchSavedJobs" :disabled="loading">
        {{ loading ? 'Loading...' : '↻ Refresh' }}
      </button>
    </div>

    <div v-if="loading" class="loading">Loading your bookmarks...</div>
    <div v-else-if="error" class="alert alert--error">{{ error }}</div>

    <template v-else>
      <!-- Search -->
      <div v-if="savedJobs.length" class="saved-search">
        <span class="saved-search__icon">🔍</span>
        <input v-model="search" type="text" placeholder="Search saved jobs..." class="saved-search__input" />
      </div>

      <!-- Empty state -->
      <div v-if="!savedJobs.length" class="empty-state">
        <div class="empty-state__icon">⭐</div>
        <h3>No saved jobs yet</h3>
        <p class="muted">Bookmark jobs you're interested in to find them here later.</p>
        <router-link to="/" class="btn" style="margin-top: 1rem;">Discover Jobs</router-link>
      </div>

      <div v-else-if="!filtered.length" class="empty-state">
        <div class="empty-state__icon">🔍</div>
        <h3>No results for "{{ search }}"</h3>
        <p class="muted">Try a different keyword.</p>
      </div>

      <!-- Job grid -->
      <div v-else class="saved-grid">
        <div v-for="job in filtered" :key="job.id" class="saved-card">

          <!-- Card top -->
          <div class="saved-card__top">
            <div class="saved-card__logo">
              {{ (job.employer?.company_name || job.employer?.name || 'C').charAt(0) }}
            </div>
            <div class="saved-card__info">
              <h3 class="saved-card__title" @click="router.push(`/jobs/${job.id}`)">{{ job.title }}</h3>
              <p class="saved-card__company">{{ job.employer?.name || 'Company' }}</p>
            </div>
            <button @click="unsaveJob(job.id)" class="saved-card__unsave" title="Remove from saved">★</button>
          </div>

          <!-- Meta -->
          <div class="saved-card__meta">
            <span>📍 {{ job.location }}</span>
            <span class="saved-card__work-type">{{ job.work_type }}</span>
            <span v-if="job.salary" class="saved-card__salary">${{ Number(job.salary).toLocaleString() }}/yr</span>
          </div>

          <!-- Tags -->
          <div v-if="job.skills?.length" class="saved-card__skills">
            <span v-for="skill in job.skills.slice(0, 4)" :key="skill.id" class="chip saved-chip">
              {{ skill.skill_name }}
            </span>
            <span v-if="job.skills.length > 4" class="chip saved-chip saved-chip--more">
              +{{ job.skills.length - 4 }}
            </span>
          </div>

          <!-- Actions -->
          <div class="saved-card__actions">
            <button @click="router.push(`/jobs/${job.id}`)" class="btn saved-card__view-btn">
              View Job
            </button>
            <button @click="unsaveJob(job.id)" class="btn btn--ghost saved-card__remove-btn">
              Remove
            </button>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>
