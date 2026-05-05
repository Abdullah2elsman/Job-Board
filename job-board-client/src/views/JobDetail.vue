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

const applyForm = ref({
  resume: null,
  cover_letter: ''
})
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
    
    if (isCandidate.value) {
      const savedRes = await api.get('/api/saved-jobs')
      const savedJobs = savedRes.data?.data || []
      isSaved.value = savedJobs.some(sj => sj.id === job.value.id)
    }
  } catch (err) {
    error.value = 'Failed to load job details'
  } finally {
    loading.value = false
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

const handleFileUpload = (event) => {
  applyForm.value.resume = event.target.files[0]
}

const submitApplication = async () => {
  if (!applyForm.value.resume) {
    alert('Please upload a resume')
    return
  }
  
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
    const { data } = await api.post(`/api/jobs/${job.value.id}/comments`, {
      content: newComment.value
    })
    comments.value.push(data.data)
    newComment.value = ''
  } catch (err) {
    alert(err.response?.data?.message || 'Failed to post comment')
  } finally {
    commentLoading.value = false
  }
}

const deleteComment = async (commentId) => {
  if (!window.confirm('Are you sure you want to delete this comment?')) return
  
  try {
    await api.delete(`/api/comments/${commentId}`)
    comments.value = comments.value.filter(c => c.id !== commentId)
  } catch (err) {
    alert(err.response?.data?.message || 'Failed to delete comment')
  }
}

onMounted(() => {
  fetchJob()
})
</script>

<template>
  <div v-if="loading" class="loading">Loading job details...</div>
  <div v-else-if="error" class="alert alert--error">{{ error }}</div>
  <div v-else-if="job" class="sidebar-layout">
    
    <div class="layout" style="gap: 2rem;">
      <article class="card" style="padding: 2.5rem;">
        <header class="flex-between" style="margin-bottom: 2rem; align-items: flex-start;">
          <div style="flex: 1;">
            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.5rem;">
              <h1 style="font-size: 2.25rem; margin: 0;">{{ job.title }}</h1>
              <span class="badge badge--success">${{ job.salary || 'N/A' }}</span>
            </div>
            <p class="muted" style="font-size: 1.1rem;">
              <span v-if="job.employer?.name" style="font-weight: 600; color: var(--text-primary);">{{ job.employer.name }}</span>
              <span v-if="job.employer?.name"> • </span>
              {{ job.location }} • {{ job.work_type }}
            </p>
          </div>
          <button v-if="isCandidate" @click="toggleSaveJob" class="btn btn--ghost" :disabled="saveLoading" style="padding: 0.6rem 1rem;">
            <span v-if="isSaved">★ Saved</span>
            <span v-else>☆ Save</span>
          </button>
        </header>

        <div style="display: flex; gap: 0.75rem; flex-wrap: wrap; margin-bottom: 2.5rem; padding-bottom: 2rem; border-bottom: 1px solid var(--border-color);">
          <span class="badge">{{ job.category?.name || 'Uncategorized' }}</span>
          <span class="badge">{{ job.experience_level || 'Any Experience' }}</span>
          <span v-for="skill in job.skills" :key="skill.id" class="chip">
            {{ skill.skill_name }}
          </span>
        </div>

        <section style="margin-bottom: 2.5rem;">
          <h3 style="margin-bottom: 1rem; font-size: 1.4rem;">About this role</h3>
          <p style="white-space: pre-wrap; color: var(--text-secondary); font-size: 1.05rem; line-height: 1.8;">{{ job.description }}</p>
        </section>

        <section v-if="job.responsibilities" style="margin-bottom: 2.5rem;">
          <h3 style="margin-bottom: 1rem; font-size: 1.4rem;">What you'll do</h3>
          <p style="white-space: pre-wrap; color: var(--text-secondary); font-size: 1.05rem; line-height: 1.8;">{{ job.responsibilities }}</p>
        </section>

        <section v-if="job.requirements" style="margin-bottom: 2.5rem;">
          <h3 style="margin-bottom: 1rem; font-size: 1.4rem;">Requirements</h3>
          <p style="white-space: pre-wrap; color: var(--text-secondary); font-size: 1.05rem; line-height: 1.8;">{{ job.requirements }}</p>
        </section>
      </article>

      <!-- Discussion Section -->
      <section class="card" style="padding: 2.5rem;">
        <h3 style="margin-bottom: 1.5rem; font-size: 1.4rem;">Discussion ({{ comments.length }})</h3>
        
        <form v-if="isAuthenticated" @submit.prevent="postComment" class="form" style="margin-bottom: 2.5rem;">
          <div class="form__field">
            <textarea v-model="newComment" rows="3" placeholder="Ask a question or share your thoughts..." required></textarea>
          </div>
          <div style="display: flex; justify-content: flex-end;">
            <button type="submit" class="btn" :disabled="commentLoading">
              {{ commentLoading ? 'Posting...' : 'Post Comment' }}
            </button>
          </div>
        </form>
        <div v-else class="alert alert--warning" style="margin-bottom: 2.5rem;">
          Please log in to participate in the discussion.
        </div>

        <div class="layout" style="gap: 1.25rem;">
          <div v-for="comment in comments" :key="comment.id" class="job-card" style="padding: 1.25rem; background: var(--bg-secondary); border: none;">
            <div class="flex-between" style="margin-bottom: 0.5rem;">
              <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--primary); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.8rem;">
                  {{ (comment.user?.name || 'U').charAt(0).toUpperCase() }}
                </div>
                <div>
                  <h4 style="margin: 0; font-size: 0.95rem;">{{ comment.user?.name || 'User' }}</h4>
                  <span class="muted" style="font-size: 0.8rem;">{{ new Date(comment.created_at).toLocaleDateString() }}</span>
                </div>
              </div>
              <button 
                v-if="isAdmin || (authStore.user?.id === comment.user_id)" 
                @click="deleteComment(comment.id)"
                class="btn btn--danger" 
                style="padding: 0.4rem 0.8rem; font-size: 0.75rem;"
              >
                Delete
              </button>
            </div>
            <p style="margin: 0; white-space: pre-wrap; font-size: 0.95rem; color: var(--text-secondary);">{{ comment.content }}</p>
          </div>
          <p v-if="!comments.length" class="muted text-center" style="padding: 2rem;">No comments yet. Be the first to start the discussion!</p>
        </div>
      </section>
    </div>

    <!-- Sticky Sidebar -->
    <aside style="display: flex; flex-direction: column; gap: 1.5rem;">
      <div class="card" style="padding: 2rem; position: sticky; top: 6rem;">
        <h3 style="margin-bottom: 1.25rem;">Apply Now</h3>
        
        <div v-if="!isAuthenticated" class="layout" style="gap: 1rem;">
          <p class="muted">You need to be logged in as a candidate to apply for this job.</p>
          <router-link to="/login" class="btn" style="width: 100%;">Sign In</router-link>
        </div>

        <div v-else-if="!isCandidate" class="alert alert--warning">
          Please log in as a candidate to apply for this job.
        </div>
        
        <div v-else-if="applySuccess" class="alert alert--success">
          <p style="margin: 0;">Application submitted! We've notified the employer.</p>
        </div>
        
        <form v-else @submit.prevent="submitApplication" class="form">
          <div class="form__field">
            <label>Upload Resume (PDF)</label>
            <input type="file" accept=".pdf" @change="handleFileUpload" required />
          </div>
          
          <div class="form__field">
            <label>Cover Letter</label>
            <textarea v-model="applyForm.cover_letter" rows="5" placeholder="Why are you a good fit?"></textarea>
          </div>

          <button type="submit" class="btn" :disabled="applyLoading" style="width: 100%; padding: 0.9rem;">
            {{ applyLoading ? 'Submitting...' : 'Submit Application' }}
          </button>
        </form>

        <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--border-color);">
          <div class="flex-between" style="margin-bottom: 1rem;">
            <span class="muted">Applications</span>
            <span style="font-weight: 600;">{{ job.applications_count || 0 }}</span>
          </div>
          <div class="flex-between">
            <span class="muted">Views</span>
            <span style="font-weight: 600;">{{ job.views_count || 0 }}</span>
          </div>
        </div>
      </div>
    </aside>

  </div>
</template>
