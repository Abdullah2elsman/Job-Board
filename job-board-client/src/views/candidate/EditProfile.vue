<script setup>
import { reactive, ref, onMounted } from 'vue'
import { useAuthStore } from '../../stores/auth'
import api from '../../services/api'

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

const validateForm = () => {
  Object.keys(errors).forEach(key => delete errors[key])
  if (!form.name) errors.name = 'Name is required'
  if (!form.location) errors.location = 'Location is required'
  return Object.keys(errors).length === 0
}

const addSkill = () => {
  const value = skillInput.value.trim()
  if (!value) return
  if (!form.skills.includes(value)) form.skills.push(value)
  skillInput.value = ''
}

const removeSkill = (skill) => {
  form.skills = form.skills.filter(item => item !== skill)
}

const handleFileUpload = (event) => {
  const file = event.target.files[0]
  if (file && file.type === 'application/pdf') resumeFile.value = file
  else errors.resume = 'Please upload a PDF file'
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
  <div style="max-width: 700px; margin: 0 auto; padding: 2rem 1rem;">
    <header style="margin-bottom: 2.5rem; text-align: center;">
      <h1 style="margin-bottom: 0.5rem;">Professional Profile</h1>
      <p class="muted">Update your details to stand out to potential employers</p>
    </header>

    <div v-if="successMessage" class="alert alert--success" style="margin-bottom: 2rem;">
      {{ successMessage }}
    </div>

    <section class="card" style="padding: 2.5rem;">
      <form @submit.prevent="handleSubmit" class="form">
        <div class="form__field">
          <label>Full Name</label>
          <input v-model="form.name" type="text" placeholder="John Doe" :disabled="isLoading" />
          <span v-if="errors.name" class="form__error">{{ errors.name }}</span>
        </div>

        <div class="form__field">
          <label>Professional Bio</label>
          <textarea v-model="form.bio" placeholder="Briefly describe your experience and goals..." rows="4" :disabled="isLoading"></textarea>
          <span v-if="errors.bio" class="form__error">{{ errors.bio }}</span>
        </div>

        <div class="form__field">
          <label>Location</label>
          <input v-model="form.location" type="text" placeholder="e.g. San Francisco, CA" :disabled="isLoading" />
          <span v-if="errors.location" class="form__error">{{ errors.location }}</span>
        </div>

        <div class="form__field">
          <label>Key Skills (Press Enter to add)</label>
          <div class="skills">
            <div class="skills__input">
              <input v-model="skillInput" type="text" placeholder="e.g. JavaScript, AWS" @keydown.enter.prevent="addSkill" :disabled="isLoading" />
              <button type="button" class="btn btn--ghost" @click="addSkill" :disabled="isLoading">Add</button>
            </div>
            <div class="flex-wrap" style="margin-top: 1rem; gap: 0.5rem;">
              <span v-for="skill in form.skills" :key="skill" class="chip">
                {{ skill }}
                <button type="button" @click="removeSkill(skill)" style="border: none; background: none; cursor: pointer; color: var(--danger); margin-left: 0.25rem;">×</button>
              </span>
            </div>
          </div>
        </div>

        <div class="form__field">
          <label>Update Resume (PDF)</label>
          <div style="padding: 1.5rem; border: 2px dashed var(--border-color); border-radius: var(--radius-md); text-align: center; background: var(--bg-secondary);">
            <input type="file" accept=".pdf" @change="handleFileUpload" :disabled="isLoading" style="display: block; margin: 0 auto;" />
            <p v-if="resumeFile" class="muted" style="margin-top: 1rem; font-size: 0.85rem; font-weight: 600;">
              Selected: {{ resumeFile.name }}
            </p>
          </div>
          <span v-if="errors.resume" class="form__error">{{ errors.resume }}</span>
        </div>

        <div v-if="errors.submit" class="alert alert--error" style="margin-top: 1.5rem;">
          {{ errors.submit }}
        </div>

        <div class="form__actions" style="margin-top: 2.5rem; border-top: 1px solid var(--border-color); padding-top: 2rem;">
          <router-link to="/dashboard/candidate" class="btn btn--ghost">Back to Dashboard</router-link>
          <button class="btn" type="submit" :disabled="isLoading" style="padding: 0.75rem 2rem;">
            {{ isLoading ? 'Updating Profile...' : 'Save Profile' }}
          </button>
        </div>
      </form>
    </section>
  </div>
</template>
