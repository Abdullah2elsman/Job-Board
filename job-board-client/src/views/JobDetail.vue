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

const isCandidate = computed(() => authStore.isCandidate)
const isAdmin = computed(() => authStore.isAdmin)
const isAuthenticated = computed(() => authStore.isAuthenticated)

const fetchJob = async () => {
  try {
    const { data } = await api.get(`/api/jobs/${route.params.id}`)
    job.value = data?.data || data
    comments.value = job.value?.comments || []
  } catch (err) {
    error.value = 'Failed to load job details'
  } finally {
    loading.value = false
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
  <div v-else-if="job" class="grid" style="grid-template-columns: 2fr 1fr; align-items: start;">
    
    <article class="card">
      <header class="card__header" style="flex-direction: column; align-items: flex-start; gap: 1rem;">
        <div>
          <h2>{{ job.title }}</h2>
          <p class="muted">
            <span v-if="job.employer?.name">Company: {{ job.employer.name }}</span>
          </p>
        </div>
        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
          <span class="badge badge--success">${{ job.salary || 'N/A' }}</span>
          <span class="badge">{{ job.work_type }}</span>
          <span class="badge">{{ job.location }}</span>
        </div>
      </header>

      <div style="margin-top: 2rem;">
        <h3>Description</h3>
        <p style="white-space: pre-wrap;">{{ job.description }}</p>
      </div>

      <div v-if="job.responsibilities" style="margin-top: 2rem;">
        <h3>Responsibilities</h3>
        <p style="white-space: pre-wrap;">{{ job.responsibilities }}</p>
      </div>

      <div v-if="job.requirements" style="margin-top: 2rem;">
        <h3>Requirements</h3>
        <p style="white-space: pre-wrap;">{{ job.requirements }}</p>
      </div>

      <div style="margin-top: 2rem;">
        <h3>Skills</h3>
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
          <span v-for="skill in job.skills" :key="skill.id" class="chip">
            {{ skill.skill_name }}
          </span>
        </div>
      </div>

      <div style="margin-top: 3rem; padding-top: 2rem; border-top: 1px solid var(--border);">
        <h3>Discussion ({{ comments.length }})</h3>
        
        <form v-if="isAuthenticated" @submit.prevent="postComment" class="form" style="margin-top: 1rem; margin-bottom: 2rem;">
          <div class="form__field">
            <textarea v-model="newComment" rows="3" placeholder="Add a comment..." required></textarea>
          </div>
          <button type="submit" class="btn" :disabled="commentLoading">
            {{ commentLoading ? 'Posting...' : 'Post Comment' }}
          </button>
        </form>
        <div v-else class="alert alert--warning" style="margin-top: 1rem; margin-bottom: 2rem;">
          Please log in to participate in the discussion.
        </div>

        <div class="comments-list">
          <div v-for="comment in comments" :key="comment.id" class="card" style="padding: 1rem; margin-bottom: 1rem; background: var(--bg-body);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
              <div>
                <strong>{{ comment.user?.name || 'User' }}</strong>
                <span class="muted" style="margin-left: 0.5rem; font-size: 0.85em;">{{ new Date(comment.created_at).toLocaleDateString() }}</span>
              </div>
              <button 
                v-if="isAdmin || (authStore.user?.id === comment.user_id)" 
                @click="deleteComment(comment.id)"
                class="btn btn--danger" 
                style="padding: 0.25rem 0.5rem; font-size: 0.8em;"
              >
                Delete
              </button>
            </div>
            <p style="margin-top: 0.5rem; white-space: pre-wrap;">{{ comment.content }}</p>
          </div>
          <p v-if="!comments.length" class="muted text-center">No comments yet. Be the first to start the discussion!</p>
        </div>
      </div>
    </article>

    <aside class="card" style="position: sticky; top: 1rem;">
      <header class="card__header">
        <h3>Apply Now</h3>
      </header>

      <div v-if="!isCandidate" class="alert alert--warning">
        Please log in as a candidate to apply for this job.
      </div>
      
      <div v-else-if="applySuccess" class="alert alert--success">
        Your application has been submitted successfully!
      </div>
      
      <form v-else @submit.prevent="submitApplication" class="form">
        <div class="form__field">
          <label>Resume (PDF)</label>
          <input type="file" accept=".pdf" @change="handleFileUpload" required />
        </div>
        
        <div class="form__field">
          <label>Cover Letter (Optional)</label>
          <textarea v-model="applyForm.cover_letter" rows="4"></textarea>
        </div>

        <button type="submit" class="btn" :disabled="applyLoading" style="width: 100%;">
          {{ applyLoading ? 'Submitting...' : 'Submit Application' }}
        </button>
      </form>
    </aside>

  </div>
</template>
