<script setup>
import { reactive, ref, onMounted, computed } from 'vue'
import { useAuthStore } from '../../stores/auth'
import api from '../../services/api'
import '../../assets/profile.css'

const authStore = useAuthStore()

const form = reactive({
  name: '',
  bio: '',
  location: '',
  skills: []
})

const skillInput = ref('')
const resumeFile = ref(null)
const errors = reactive({})
const isLoading = ref(false)
const successMessage = ref('')

const userInitial = computed(() =>
  (form.name || authStore.user?.name || 'C').charAt(0).toUpperCase()
)

const validateForm = () => {
  Object.keys(errors).forEach(key => delete errors[key])
  if (!form.name) errors.name = 'Name is required'
  if (!form.location) errors.location = 'Location is required'
  return Object.keys(errors).length === 0
}

const addSkill = () => {
  const value = skillInput.value.trim()
  if (!value || form.skills.includes(value)) return
  form.skills.push(value)
  skillInput.value = ''
}

const removeSkill = (skill) => {
  form.skills = form.skills.filter(s => s !== skill)
}

const handleFileUpload = (event) => {
  const file = event.target.files[0]
  if (file && file.type === 'application/pdf') {
    resumeFile.value = file
    delete errors.resume
  } else {
    errors.resume = 'Please upload a PDF file'
  }
}

const handleSubmit = async () => {
  successMessage.value = ''
  if (!validateForm()) return
  isLoading.value = true
  try {
    const formData = new FormData()
    formData.append('name', form.name)
    formData.append('bio', form.bio)
    formData.append('location', form.location)
    formData.append('skills', JSON.stringify(form.skills))
    if (resumeFile.value) formData.append('resume', resumeFile.value)

    const { data } = await api.post('/api/candidate/profile', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
    authStore.user = { ...authStore.user, ...data.data }
    localStorage.setItem('user', JSON.stringify(authStore.user))
    successMessage.value = 'Profile updated successfully'
    resumeFile.value = null
  } catch (error) {
    const apiErrors = error?.response?.data?.errors
    if (apiErrors) Object.entries(apiErrors).forEach(([key, val]) => errors[key] = val[0])
    else errors.submit = 'Failed to update profile'
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  if (authStore.user) {
    form.name = authStore.user.name || ''
    form.bio = authStore.user.bio || ''
    form.location = authStore.user.location || ''
    form.skills = (authStore.user.skills || []).map(sk => typeof sk === 'string' ? sk : sk.skill_name || sk)
  }
})
</script>

<template>
  <div class="profile-page">

    <!-- Page header -->
    <div class="profile-page__header">
      <div class="profile-avatar">{{ userInitial }}</div>
      <div>
        <h1 class="profile-page__title">{{ form.name || 'Your Profile' }}</h1>
        <p class="muted">Keep your profile up to date to attract the right opportunities</p>
      </div>
    </div>

    <transition name="fade">
      <div v-if="successMessage" class="alert alert--success profile-alert">{{ successMessage }}</div>
    </transition>
    <div v-if="errors.submit" class="alert alert--error profile-alert">{{ errors.submit }}</div>

    <form @submit.prevent="handleSubmit" class="profile-form">

      <!-- Personal info -->
      <div class="card profile-card">
        <div class="profile-card__heading">
          <span class="profile-card__icon">👤</span>
          <h2>Personal Information</h2>
        </div>
        <div class="profile-fields profile-fields--grid">
          <div class="form__field">
            <label>Full Name <span class="required">*</span></label>
            <input v-model="form.name" type="text" placeholder="John Doe" :disabled="isLoading" />
            <span v-if="errors.name" class="form__error">{{ errors.name }}</span>
          </div>
          <div class="form__field">
            <label>Location <span class="required">*</span></label>
            <input v-model="form.location" type="text" placeholder="e.g. Berlin, Germany" :disabled="isLoading" />
            <span v-if="errors.location" class="form__error">{{ errors.location }}</span>
          </div>
        </div>
        <div class="profile-fields" style="margin-top: 1.25rem;">
          <div class="form__field">
            <label>Professional Bio</label>
            <textarea v-model="form.bio" placeholder="Briefly describe your experience, skills, and career goals..." rows="4" :disabled="isLoading"></textarea>
            <span v-if="errors.bio" class="form__error">{{ errors.bio }}</span>
          </div>
        </div>
      </div>

      <!-- Skills -->
      <div class="card profile-card">
        <div class="profile-card__heading">
          <span class="profile-card__icon">⚡</span>
          <h2>Skills</h2>
        </div>
        <div class="skills-input-row">
          <input
            v-model="skillInput"
            type="text"
            placeholder="e.g. JavaScript, AWS, Docker..."
            @keydown.enter.prevent="addSkill"
            :disabled="isLoading"
          />
          <button type="button" class="btn btn--ghost" @click="addSkill" :disabled="isLoading">Add</button>
        </div>
        <div v-if="form.skills.length" class="skills-list">
          <span v-for="skill in form.skills" :key="skill" class="skill-chip">
            {{ skill }}
            <button type="button" @click="removeSkill(skill)" class="skill-chip__remove" :disabled="isLoading">×</button>
          </span>
        </div>
        <p v-else class="muted" style="margin: 1rem 0 0; font-size: 0.88rem;">No skills added yet. Type a skill and press Enter or click Add.</p>
      </div>

      <!-- Resume -->
      <div class="card profile-card">
        <div class="profile-card__heading">
          <span class="profile-card__icon">📄</span>
          <h2>Resume</h2>
        </div>

        <div v-if="authStore.user?.resume_path" class="resume-current">
          <span class="resume-current__icon">📎</span>
          <span class="resume-current__label">Current resume on file</span>
          <span class="badge badge--success" style="font-size: 0.7rem;">Active</span>
        </div>

        <div class="resume-upload" :class="{ 'resume-upload--has-file': resumeFile }">
          <input type="file" accept=".pdf" @change="handleFileUpload" :disabled="isLoading" id="resume-input" class="resume-upload__input" />
          <label for="resume-input" class="resume-upload__label">
            <span class="resume-upload__icon">⬆️</span>
            <span v-if="resumeFile" class="resume-upload__filename">{{ resumeFile.name }}</span>
            <span v-else>
              <strong>Click to upload</strong> or drag and drop<br />
              <span class="muted" style="font-size: 0.82rem;">PDF only, max 5MB</span>
            </span>
          </label>
        </div>
        <span v-if="errors.resume" class="form__error">{{ errors.resume }}</span>
      </div>

      <!-- Actions -->
      <div class="profile-actions">
        <router-link to="/dashboard/candidate" class="btn btn--ghost">← Back to Dashboard</router-link>
        <button class="btn profile-save-btn" type="submit" :disabled="isLoading">
          {{ isLoading ? 'Saving...' : 'Save Profile' }}
        </button>
      </div>

    </form>
  </div>
</template>
