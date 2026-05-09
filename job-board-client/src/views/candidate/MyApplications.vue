<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '../../services/api'

const applications = ref([])
const loading = ref(true)
const error = ref(null)
const filterStatus = ref('all')

const statusConfig = {
  applied:      { label: 'Applied',      cls: 'badge',              icon: '📨' },
  interviewing: { label: 'Interviewing', cls: 'badge badge--warning', icon: '🗓️' },
  accepted:     { label: 'Accepted',     cls: 'badge badge--success', icon: '✅' },
  rejected:     { label: 'Rejected',     cls: 'badge badge--danger',  icon: '❌' },
}

const statusFilters = ['all', 'applied', 'interviewing', 'accepted', 'rejected']

const filtered = computed(() =>
  filterStatus.value === 'all'
    ? applications.value
    : applications.value.filter(a => a.status === filterStatus.value)
)

const counts = computed(() => {
  const c = { all: applications.value.length }
  statusFilters.slice(1).forEach(s => {
    c[s] = applications.value.filter(a => a.status === s).length
  })
  return c
})

const fetchMyApplications = async () => {
  loading.value = true
  try {
    const { data } = await api.get('/api/my-applications')
    applications.value = data?.data || data || []
  } catch {
    error.value = 'Failed to load applications'
  } finally {
    loading.value = false
  }
}

const cancelApplication = async (id) => {
  if (!confirm('Cancel this application?')) return
  try {
    await api.delete(`/api/applications/${id}`)
    applications.value = applications.value.filter(a => a.id !== id)
  } catch (err) {
    alert(err.response?.data?.message || 'Failed to cancel application')
  }
}

onMounted(fetchMyApplications)
</script>

<template>
  <div class="page">

    <!-- Header -->
    <div class="page-header">
      <div class="page-header__left">
        <div class="page-icon">📝</div>
        <div>
          <h1 class="page-title">My Applications</h1>
          <p class="muted">Track every job you've applied to</p>
        </div>
      </div>
      <button class="btn btn--ghost btn--sm" @click="fetchMyApplications" :disabled="loading">
        {{ loading ? 'Loading...' : '↻ Refresh' }}
      </button>
    </div>

    <!-- Status filter tabs -->
    <div class="filter-tabs">
      <button
        v-for="s in statusFilters"
        :key="s"
        class="filter-tab"
        :class="{ active: filterStatus === s }"
        @click="filterStatus = s"
      >
        {{ s === 'all' ? 'All' : statusConfig[s].label }}
        <span class="filter-tab__count">{{ counts[s] }}</span>
      </button>
    </div>

    <div v-if="loading" class="loading">Fetching your applications...</div>
    <div v-else-if="error" class="alert alert--error">{{ error }}</div>

    <div v-else class="app-list">

      <!-- Empty state -->
      <div v-if="!filtered.length" class="empty-state">
        <div class="empty-state__icon">{{ filterStatus === 'all' ? '📭' : statusConfig[filterStatus]?.icon }}</div>
        <h3>{{ filterStatus === 'all' ? 'No applications yet' : `No ${filterStatus} applications` }}</h3>
        <p class="muted">{{ filterStatus === 'all' ? 'Start applying to jobs to track them here.' : 'Nothing in this category yet.' }}</p>
        <router-link v-if="filterStatus === 'all'" to="/" class="btn" style="margin-top: 1rem;">Browse Jobs</router-link>
      </div>

      <!-- Application cards -->
      <div v-for="app in filtered" :key="app.id" class="app-card">
        <div class="app-card__status-bar" :data-status="app.status"></div>

        <div class="app-card__body">
          <div class="app-card__top">
            <div class="app-card__info">
              <div class="app-card__title-row">
                <h3 class="app-card__title">{{ app.job?.title || 'Job Title' }}</h3>
                <span :class="statusConfig[app.status]?.cls || 'badge'">
                  {{ statusConfig[app.status]?.icon }} {{ statusConfig[app.status]?.label || app.status }}
                </span>
              </div>
              <div class="app-card__meta">
                <span v-if="app.job?.employer?.name">🏢 {{ app.job.employer.name }}</span>
                <span v-if="app.job?.location">📍 {{ app.job.location }}</span>
                <span v-if="app.job?.work_type" class="app-card__work-type">{{ app.job.work_type }}</span>
                <span>🗓 Applied {{ new Date(app.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) }}</span>
              </div>
            </div>

            <div class="app-card__actions">
              <router-link :to="`/jobs/${app.job_id}`" class="btn btn--ghost btn--sm">View Job</router-link>
              <a
                v-if="app.resume_path"
                :href="`http://127.0.0.1:8000/storage/${app.resume_path}`"
                target="_blank"
                class="btn btn--ghost btn--sm"
              >Resume ↗</a>
              <button
                v-if="app.status === 'applied'"
                @click="cancelApplication(app.id)"
                class="btn btn--danger btn--sm"
              >Cancel</button>
            </div>
          </div>

          <div v-if="app.cover_letter" class="app-card__cover">
            <p class="app-card__cover-label">Cover Letter</p>
            <p class="app-card__cover-text">{{ app.cover_letter.substring(0, 220) }}{{ app.cover_letter.length > 220 ? '...' : '' }}</p>
          </div>
        </div>
      </div>

    </div>
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

/* Filter tabs */
.filter-tabs {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
  background: var(--bg-secondary);
  padding: 0.4rem;
  border-radius: var(--radius-pill);
  width: fit-content;
}

.filter-tab {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  padding: 0.4rem 1rem;
  border: none;
  border-radius: var(--radius-pill);
  background: none;
  font-size: 0.85rem;
  font-weight: 600;
  color: var(--text-muted);
  cursor: pointer;
  transition: all 0.2s;
  white-space: nowrap;
}

.filter-tab:hover { color: var(--text-primary); }

.filter-tab.active {
  background: white;
  color: var(--text-primary);
  box-shadow: var(--shadow-sm);
}

.filter-tab__count {
  background: var(--bg-secondary);
  border-radius: var(--radius-pill);
  padding: 0.05rem 0.45rem;
  font-size: 0.75rem;
  font-weight: 700;
}

.filter-tab.active .filter-tab__count {
  background: var(--primary);
  color: white;
}

/* App list */
.app-list { display: flex; flex-direction: column; gap: 1rem; }

/* App card */
.app-card {
  display: flex;
  background: white;
  border: 1px solid var(--border-color);
  border-radius: var(--radius-md);
  overflow: hidden;
  transition: box-shadow 0.2s, transform 0.2s;
}

.app-card:hover {
  box-shadow: var(--shadow-md);
  transform: translateY(-2px);
}

.app-card__status-bar {
  width: 5px;
  flex-shrink: 0;
  background: var(--border-color);
}

.app-card__status-bar[data-status="applied"]      { background: var(--primary); }
.app-card__status-bar[data-status="interviewing"] { background: var(--warning); }
.app-card__status-bar[data-status="accepted"]     { background: var(--success); }
.app-card__status-bar[data-status="rejected"]     { background: var(--danger); }

.app-card__body { flex: 1; padding: 1.25rem 1.5rem; min-width: 0; }

.app-card__top {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 1rem;
  flex-wrap: wrap;
}

.app-card__info { flex: 1; min-width: 0; }

.app-card__title-row {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  flex-wrap: wrap;
  margin-bottom: 0.5rem;
}

.app-card__title { margin: 0; font-size: 1.05rem; }

.app-card__meta {
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
  font-size: 0.82rem;
  color: var(--text-muted);
}

.app-card__work-type {
  text-transform: capitalize;
  background: var(--bg-secondary);
  padding: 0.1rem 0.5rem;
  border-radius: var(--radius-pill);
  font-weight: 600;
}

.app-card__actions {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
  flex-shrink: 0;
}

.app-card__cover {
  margin-top: 1rem;
  padding: 0.85rem 1rem;
  background: var(--bg-secondary);
  border-radius: var(--radius-sm);
  border-left: 3px solid var(--border-color-focus);
}

.app-card__cover-label {
  font-size: 0.72rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  color: var(--text-muted);
  margin: 0 0 0.35rem;
}

.app-card__cover-text {
  margin: 0;
  font-size: 0.88rem;
  color: var(--text-secondary);
  line-height: 1.6;
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
.empty-state h3 { margin: 0 0 0.5rem; color: var(--text-primary); }
</style>
