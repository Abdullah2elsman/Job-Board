<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import api from '../services/api'

const route = useRoute()
const authStore = useAuthStore()

const job = ref(null)
const loading = ref(true)
const error = ref(null)

const applyForm = ref({ resume: null, cover_letter: '' })
const applyLoading = ref(false)
const applySuccess = ref(false)

const comments = ref([])
const newComment = ref('')
const commentLoading = ref(false)

const isSaved = ref(false)
const saveLoading = ref(false)

const isCandidate = computed(() => authStore.isCandidate)
const isAdmin = computed(() => authStore.isAdmin)
const isAuthenticated = computed(() => authStore.isAuthenticated)

const fetchJob = async () => {
  try {
    const { data } = await api.get(`/api/jobs/${route.params.id}`)
    job.value = data?.data || data
    comments.value = job.value?.comments || []
  } catch {
    error.value = 'Failed to load job details'
  } finally {
    loading.value = false
  }

  if (isCandidate.value && job.value) {
    try {
      const savedRes = await api.get('/api/saved-jobs')
      const savedJobs = savedRes.data?.data || []
      isSaved.value = savedJobs.some(sj => sj.id === job.value.id)
    } catch { /* non-critical */ }
  }
}

const toggleSaveJob = async () => {
  saveLoading.value = true
  try {
    const { data } = await api.post(`/api/jobs/${job.value.id}/save`)
    isSaved.value = data.status === 'added'
  } catch (err) {
    alert(err.response?.data?.message || 'Failed to save job')
  } finally {
    saveLoading.value = false
  }
}

const handleFileUpload = (e) => { applyForm.value.resume = e.target.files[0] }

const submitApplication = async () => {
  if (!applyForm.value.resume) { alert('Please upload a resume'); return }
  applyLoading.value = true
  const formData = new FormData()
  formData.append('resume', applyForm.value.resume)
  formData.append('cover_letter', applyForm.value.cover_letter)
  try {
    await api.post(`/api/jobs/${job.value.id}/apply`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
    applySuccess.value = true
  } catch (err) {
    alert(err.response?.data?.message || 'Application failed')
  } finally {
    applyLoading.value = false
  }
}

const postComment = async () => {
  if (!newComment.value.trim()) return
  commentLoading.value = true
  try {
    const { data } = await api.post(`/api/jobs/${job.value.id}/comments`, { content: newComment.value })
    comments.value.push(data.data)
    newComment.value = ''
  } catch (err) {
    alert(err.response?.data?.message || 'Failed to post comment')
  } finally {
    commentLoading.value = false
  }
}

const deleteComment = async (commentId) => {
  if (!window.confirm('Delete this comment?')) return
  try {
    await api.delete(`/api/comments/${commentId}`)
    comments.value = comments.value.filter(c => c.id !== commentId)
  } catch (err) {
    alert(err.response?.data?.message || 'Failed to delete comment')
  }
}

onMounted(fetchJob)
</script>

<template>
  <div v-if="loading" class="loading">Loading job details...</div>
  <div v-else-if="error" class="alert alert--error">{{ error }}</div>

  <div v-else-if="job" class="jd-layout">

    <!-- ── Main content ── -->
    <div class="jd-main">

      <!-- Header card -->
      <div class="card jd-header-card">
        <div class="jd-header-top">
          <div class="jd-title-block">
            <h1 class="jd-title">{{ job.title }}</h1>
            <div class="jd-meta">
              <span v-if="job.employer?.name" class="jd-company">{{ job.employer.name }}</span>
              <span class="jd-dot" v-if="job.employer?.name">·</span>
              <span>{{ job.location }}</span>
              <span class="jd-dot">·</span>
              <span class="jd-work-type">{{ job.work_type }}</span>
            </div>
          </div>
          <div class="jd-header-right">
            <div v-if="job.salary" class="jd-salary">${{ Number(job.salary).toLocaleString() }}<span>/yr</span></div>
            <button v-if="isCandidate" @click="toggleSaveJob" class="btn btn--ghost jd-save-btn" :disabled="saveLoading">
              {{ isSaved ? '★ Saved' : '☆ Save' }}
            </button>
          </div>
        </div>

        <!-- Tags row -->
        <div class="jd-tags">
          <span class="badge">{{ job.category?.name || 'Uncategorized' }}</span>
          <span class="badge badge--warning">{{ job.experience_level || 'Any Level' }}</span>
          <span v-for="skill in job.skills" :key="skill.id" class="chip">{{ skill.skill_name }}</span>
        </div>

        <!-- Stats row -->
        <div class="jd-stats">
          <div class="jd-stat">
            <span class="jd-stat__value">{{ job.applications_count || 0 }}</span>
            <span class="jd-stat__label">Applicants</span>
          </div>
          <div class="jd-stat">
            <span class="jd-stat__value">{{ job.views_count || 0 }}</span>
            <span class="jd-stat__label">Views</span>
          </div>
          <div class="jd-stat" v-if="job.deadline">
            <span class="jd-stat__value">{{ new Date(job.deadline).toLocaleDateString() }}</span>
            <span class="jd-stat__label">Deadline</span>
          </div>
        </div>
      </div>

      <!-- Job body -->
      <div class="card jd-body-card">
        <section class="jd-section">
          <h2 class="jd-section__title">About this role</h2>
          <p class="jd-section__text">{{ job.description }}</p>
        </section>

        <section v-if="job.responsibilities" class="jd-section">
          <h2 class="jd-section__title">What you'll do</h2>
          <p class="jd-section__text">{{ job.responsibilities }}</p>
        </section>

        <section v-if="job.requirements" class="jd-section">
          <h2 class="jd-section__title">Requirements</h2>
          <p class="jd-section__text">{{ job.requirements }}</p>
        </section>
      </div>

      <!-- Discussion -->
      <div class="card jd-body-card">
        <h2 class="jd-section__title" style="margin-bottom: 1.5rem;">
          Discussion <span class="jd-comment-count">{{ comments.length }}</span>
        </h2>

        <form v-if="isAuthenticated" @submit.prevent="postComment" class="jd-comment-form">
          <textarea v-model="newComment" rows="3" placeholder="Ask a question or share your thoughts..." required></textarea>
          <div class="jd-comment-form__actions">
            <button type="submit" class="btn" :disabled="commentLoading">
              {{ commentLoading ? 'Posting...' : 'Post Comment' }}
            </button>
          </div>
        </form>
        <div v-else class="alert alert--warning">Log in to join the discussion.</div>

        <div class="jd-comments">
          <div v-for="comment in comments" :key="comment.id" class="jd-comment">
            <div class="jd-comment__avatar">{{ (comment.user?.name || 'U').charAt(0).toUpperCase() }}</div>
            <div class="jd-comment__body">
              <div class="jd-comment__header">
                <span class="jd-comment__name">{{ comment.user?.name || 'User' }}</span>
                <span class="jd-comment__time">{{ new Date(comment.created_at).toLocaleDateString() }}</span>
                <button
                  v-if="isAdmin || authStore.user?.id === comment.user_id"
                  @click="deleteComment(comment.id)"
                  class="btn btn--danger jd-comment__delete"
                >Delete</button>
              </div>
              <p class="jd-comment__text">{{ comment.content }}</p>
            </div>
          </div>
          <p v-if="!comments.length" class="muted text-center" style="padding: 2rem 0;">No comments yet. Be the first!</p>
        </div>
      </div>
    </div>

    <!-- ── Sidebar ── -->
    <aside class="jd-sidebar">
      <div class="card jd-apply-card">
        <h3 class="jd-apply-card__title">Apply for this role</h3>

        <div v-if="!isAuthenticated" class="jd-apply-guest">
          <p class="muted">Sign in as a candidate to apply.</p>
          <router-link to="/login" class="btn" style="width:100%;">Sign In</router-link>
          <router-link to="/register" class="btn btn--ghost" style="width:100%;">Create Account</router-link>
        </div>

        <div v-else-if="!isCandidate" class="alert alert--warning" style="margin:0;">
          Only candidates can apply for jobs.
        </div>

        <div v-else-if="applySuccess" class="alert alert--success" style="margin:0;">
          Application submitted! The employer has been notified.
        </div>

        <form v-else @submit.prevent="submitApplication" class="form">
          <div class="form__field">
            <label>Resume (PDF)</label>
            <input type="file" accept=".pdf" @change="handleFileUpload" required />
          </div>
          <div class="form__field">
            <label>Cover Letter <span class="muted">(optional)</span></label>
            <textarea v-model="applyForm.cover_letter" rows="5" placeholder="Why are you a great fit for this role?"></textarea>
          </div>
          <button type="submit" class="btn jd-submit-btn" :disabled="applyLoading">
            {{ applyLoading ? 'Submitting...' : 'Submit Application' }}
          </button>
        </form>
      </div>

      <!-- Company card -->
      <div v-if="job.employer" class="card jd-company-card">
        <div class="jd-company-header">
          <div class="jd-company-logo">{{ (job.employer.company_name || job.employer.name || 'C').charAt(0) }}</div>
          <div>
            <div class="jd-company-name">{{ job.employer.company_name || job.employer.name }}</div>
            <a v-if="job.employer.website" :href="job.employer.website" target="_blank" class="jd-company-website">Visit website ↗</a>
          </div>
        </div>
        <p v-if="job.employer.company_description" class="jd-company-desc">{{ job.employer.company_description }}</p>
      </div>
    </aside>

  </div>
</template>

<style scoped>
/* Layout */
.jd-layout {
  display: grid;
  grid-template-columns: 1fr;
  gap: 1.5rem;
  align-items: start;
}

@media (min-width: 900px) {
  .jd-layout {
    grid-template-columns: 1fr 340px;
  }
}

.jd-main {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
  min-width: 0;
}

.jd-sidebar {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
  position: sticky;
  top: 5rem;
}

/* Header card */
.jd-header-card {
  padding: 2rem;
}

.jd-header-top {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 1rem;
  margin-bottom: 1.5rem;
  flex-wrap: wrap;
}

.jd-title {
  font-size: clamp(1.5rem, 3vw, 2rem);
  margin: 0 0 0.5rem;
  line-height: 1.2;
}

.jd-meta {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 0.4rem;
  font-size: 0.95rem;
  color: var(--text-secondary);
}

.jd-company {
  font-weight: 700;
  color: var(--text-primary);
}

.jd-dot {
  color: var(--text-muted);
}

.jd-work-type {
  text-transform: capitalize;
}

.jd-header-right {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 0.75rem;
  flex-shrink: 0;
}

.jd-salary {
  font-size: 1.5rem;
  font-weight: 800;
  color: var(--success);
  letter-spacing: -0.03em;
}

.jd-salary span {
  font-size: 0.85rem;
  font-weight: 500;
  color: var(--text-muted);
}

.jd-save-btn {
  font-size: 0.85rem;
  padding: 0.45rem 1rem;
}

/* Tags */
.jd-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  margin-bottom: 1.5rem;
  padding-bottom: 1.5rem;
  border-bottom: 1px solid var(--border-color);
}

/* Stats */
.jd-stats {
  display: flex;
  gap: 2rem;
  flex-wrap: wrap;
}

.jd-stat {
  display: flex;
  flex-direction: column;
  gap: 0.1rem;
}

.jd-stat__value {
  font-size: 1.1rem;
  font-weight: 700;
  color: var(--text-primary);
}

.jd-stat__label {
  font-size: 0.75rem;
  color: var(--text-muted);
  text-transform: uppercase;
  letter-spacing: 0.05em;
  font-weight: 600;
}

/* Body card */
.jd-body-card {
  padding: 2rem;
}

.jd-section {
  margin-bottom: 2rem;
}

.jd-section:last-child {
  margin-bottom: 0;
}

.jd-section__title {
  font-size: 1.15rem;
  font-weight: 700;
  margin: 0 0 0.75rem;
  color: var(--text-primary);
}

.jd-section__text {
  white-space: pre-wrap;
  color: var(--text-secondary);
  font-size: 0.97rem;
  line-height: 1.8;
  margin: 0;
}

/* Comment count badge */
.jd-comment-count {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background: var(--bg-secondary);
  color: var(--text-muted);
  border-radius: var(--radius-pill);
  font-size: 0.8rem;
  font-weight: 700;
  padding: 0.1rem 0.6rem;
  margin-left: 0.4rem;
}

/* Comment form */
.jd-comment-form {
  margin-bottom: 2rem;
}

.jd-comment-form textarea {
  width: 100%;
  box-sizing: border-box;
  resize: vertical;
  margin-bottom: 0.75rem;
}

.jd-comment-form__actions {
  display: flex;
  justify-content: flex-end;
}

/* Comments list */
.jd-comments {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

.jd-comment {
  display: flex;
  gap: 0.85rem;
  align-items: flex-start;
}

.jd-comment__avatar {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: var(--primary);
  color: white;
  font-weight: 700;
  font-size: 0.85rem;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.jd-comment__body {
  flex: 1;
  background: var(--bg-secondary);
  border-radius: var(--radius-sm);
  padding: 0.85rem 1rem;
  min-width: 0;
}

.jd-comment__header {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  margin-bottom: 0.4rem;
  flex-wrap: wrap;
}

.jd-comment__name {
  font-weight: 700;
  font-size: 0.9rem;
}

.jd-comment__time {
  font-size: 0.78rem;
  color: var(--text-muted);
}

.jd-comment__delete {
  margin-left: auto;
  padding: 0.25rem 0.65rem;
  font-size: 0.75rem;
}

.jd-comment__text {
  margin: 0;
  font-size: 0.92rem;
  color: var(--text-secondary);
  line-height: 1.6;
  white-space: pre-wrap;
}

/* Apply card */
.jd-apply-card {
  padding: 1.75rem;
}

.jd-apply-card__title {
  font-size: 1.1rem;
  font-weight: 700;
  margin: 0 0 1.25rem;
}

.jd-apply-guest {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.jd-submit-btn {
  width: 100%;
  padding: 0.85rem;
  font-size: 1rem;
}

/* Company card */
.jd-company-card {
  padding: 1.5rem;
}

.jd-company-header {
  display: flex;
  align-items: center;
  gap: 0.85rem;
  margin-bottom: 0.85rem;
}

.jd-company-logo {
  width: 44px;
  height: 44px;
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

.jd-company-name {
  font-weight: 700;
  font-size: 0.95rem;
  color: var(--text-primary);
}

.jd-company-website {
  font-size: 0.8rem;
  color: var(--primary);
  text-decoration: none;
}

.jd-company-website:hover {
  text-decoration: underline;
}

.jd-company-desc {
  font-size: 0.88rem;
  color: var(--text-secondary);
  line-height: 1.6;
  margin: 0;
}
</style>
