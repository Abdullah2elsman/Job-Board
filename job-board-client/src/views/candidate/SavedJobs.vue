<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '../../services/api'
import { useRouter } from 'vue-router'

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

<style scoped>
.page { display: flex; flex-direction: column; gap: 1.5rem; }

.page-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  flex-wrap: wrap;
}

.page-header__left {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.page-icon {
  font-size: 2rem;
  width: 52px;
  height: 52px;
  background: var(--bg-secondary);
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.page-title { font-size: 1.6rem; margin: 0 0 0.2rem; }

.btn--sm { padding: 0.45rem 1rem; font-size: 0.85rem; }

/* Search bar */
.saved-search {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  background: white;
  border: 1.5px solid var(--border-color);
  border-radius: var(--radius-pill);
  padding: 0.6rem 1.25rem;
  max-width: 420px;
  transition: border-color 0.2s;
}

.saved-search:focus-within {
  border-color: var(--border-color-focus);
  box-shadow: 0 0 0 4px hsla(var(--hue), 85%, 55%, 0.1);
}

.saved-search__icon { font-size: 1rem; flex-shrink: 0; }

.saved-search__input {
  border: none;
  outline: none;
  background: none;
  font-size: 0.9rem;
  width: 100%;
  padding: 0;
  box-shadow: none;
}

/* Grid */
.saved-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 1.25rem;
}

/* Saved card */
.saved-card {
  background: white;
  border: 1px solid var(--border-color);
  border-radius: var(--radius-md);
  padding: 1.25rem;
  display: flex;
  flex-direction: column;
  gap: 0.85rem;
  transition: box-shadow 0.2s, transform 0.2s;
}

.saved-card:hover {
  box-shadow: var(--shadow-md);
  transform: translateY(-3px);
}

.saved-card__top {
  display: flex;
  align-items: flex-start;
  gap: 0.75rem;
}

.saved-card__logo {
  width: 42px;
  height: 42px;
  border-radius: 10px;
  background: var(--bg-secondary);
  border: 1px solid var(--border-color);
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 800;
  font-size: 1.1rem;
  color: var(--primary);
  flex-shrink: 0;
}

.saved-card__info { flex: 1; min-width: 0; }

.saved-card__title {
  font-size: 1rem;
  font-weight: 700;
  margin: 0 0 0.2rem;
  cursor: pointer;
  color: var(--text-primary);
  transition: color 0.15s;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.saved-card__title:hover { color: var(--primary); }

.saved-card__company {
  font-size: 0.82rem;
  color: var(--text-muted);
  margin: 0;
}

.saved-card__unsave {
  background: none;
  border: none;
  cursor: pointer;
  font-size: 1.2rem;
  color: hsl(40, 90%, 50%);
  padding: 0.2rem;
  line-height: 1;
  flex-shrink: 0;
  transition: transform 0.15s;
}

.saved-card__unsave:hover { transform: scale(1.2); }

/* Meta */
.saved-card__meta {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  font-size: 0.8rem;
  color: var(--text-muted);
  align-items: center;
}

.saved-card__work-type {
  text-transform: capitalize;
  background: var(--bg-secondary);
  padding: 0.1rem 0.5rem;
  border-radius: var(--radius-pill);
  font-weight: 600;
  font-size: 0.75rem;
}

.saved-card__salary {
  font-weight: 700;
  color: var(--success);
  font-size: 0.82rem;
}

/* Skills */
.saved-card__skills {
  display: flex;
  flex-wrap: wrap;
  gap: 0.4rem;
}

.saved-chip {
  font-size: 0.72rem;
  padding: 0.2rem 0.6rem;
}

.saved-chip--more {
  background: var(--primary);
  color: white;
  border-color: var(--primary);
}

/* Actions */
.saved-card__actions {
  display: flex;
  gap: 0.6rem;
  margin-top: auto;
  padding-top: 0.25rem;
  border-top: 1px solid var(--border-color);
}

.saved-card__view-btn {
  flex: 1;
  padding: 0.55rem;
  font-size: 0.85rem;
}

.saved-card__remove-btn {
  padding: 0.55rem 0.85rem;
  font-size: 0.85rem;
}

/* Empty state */
.empty-state {
  text-align: center;
  padding: 4rem 2rem;
  background: white;
  border: 1px solid var(--border-color);
  border-radius: var(--radius-md);
}

.empty-state__icon { font-size: 3rem; margin-bottom: 1rem; }
.empty-state h3 { margin: 0 0 0.5rem; }
</style>
