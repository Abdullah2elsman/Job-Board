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
  
  if (!form.name) {
    errors.name = 'Name is required'
  } else if (form.name.length < 2) {
    errors.name = 'Name must be at least 2 characters'
  }
  
  if (form.bio && form.bio.length < 10) {
    errors.bio = 'Bio must be at least 10 characters'
  }
  
  if (!form.location) {
    errors.location = 'Location is required'
  }
  
  return Object.keys(errors).length === 0
}

const addSkill = () => {
  const value = skillInput.value.trim()
  if (!value) return
  if (!form.skills.includes(value)) {
    form.skills.push(value)
  }
  skillInput.value = ''
}

const removeSkill = (skill) => {
  form.skills = form.skills.filter(item => item !== skill)
}

const handleFileUpload = (event) => {
  const file = event.target.files[0]
  if (file && file.type === 'application/pdf') {
    resumeFile.value = file
  } else {
    errors.resume = 'Please upload a PDF file'
  }
}

const handleSubmit = async () => {
  successMessage.value = ''
  
  if (!validateForm()) {
    return
  }
  
  isLoading.value = true
  
  try {
    const formData = new FormData()
    formData.append('name', form.name)
    formData.append('bio', form.bio)
    formData.append('location', form.location)
    formData.append('skills', JSON.stringify(form.skills))
    
    if (resumeFile.value) {
      formData.append('resume', resumeFile.value)
    }
    
    const { data } = await api.post('/api/candidate/profile', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })
    
    const updatedUser = data?.data || data?.user || data
    authStore.user = { ...authStore.user, ...updatedUser }
    localStorage.setItem('user', JSON.stringify(authStore.user))
    successMessage.value = data?.message || 'Profile updated successfully'
    resumeFile.value = null
  } catch (error) {
    const apiErrors = error?.response?.data?.errors || null
    if (apiErrors) {
      Object.entries(apiErrors).forEach(([key, value]) => {
        errors[key] = Array.isArray(value) ? value[0] : value
      })
    } else {
      errors.submit = error?.response?.data?.message || 'Failed to update profile'
    }
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  if (authStore.user) {
    form.name = authStore.user.name || ''
    form.bio = authStore.user.bio || ''
    form.location = authStore.user.location || ''
    form.skills = (authStore.user.skills || []).map(
      (skill) => typeof skill === 'string' ? skill : skill.skill_name || skill
    )
  }
})
</script>

<template>
  <div class="layout">
    <section class="card" style="max-width: 600px; margin: 0 auto; width: 100%;">
      <header class="card__header">
        <div>
          <h2>Edit Your Profile</h2>
          <p class="muted">Update your personal information</p>
        </div>
      </header>

      <div v-if="successMessage" class="alert alert--success">
        {{ successMessage }}
      </div>

      <form @submit.prevent="handleSubmit" class="form">
        <div class="form__grid">
          <div class="form__field form__field--full">
            <label>Full Name</label>
            <input
              v-model="form.name"
              type="text"
              placeholder="John Doe"
              :disabled="isLoading"
            />
            <span v-if="errors.name" class="form__error">{{ errors.name }}</span>
          </div>

          <div class="form__field form__field--full">
            <label>Bio</label>
            <textarea
              v-model="form.bio"
              placeholder="Tell us about yourself..."
              rows="3"
              :disabled="isLoading"
            ></textarea>
            <span v-if="errors.bio" class="form__error">{{ errors.bio }}</span>
          </div>

          <div class="form__field">
            <label>Location</label>
            <input
              v-model="form.location"
              type="text"
              placeholder="City, Country"
              :disabled="isLoading"
            />
            <span v-if="errors.location" class="form__error">{{ errors.location }}</span>
          </div>

          <div class="form__field form__field--full">
            <label>Skills</label>
            <div class="skills">
              <div class="skills__input">
                <input
                  v-model="skillInput"
                  type="text"
                  placeholder="Add a skill"
                  @keydown.enter.prevent="addSkill"
                  :disabled="isLoading"
                />
                <button
                  type="button"
                  class="btn btn--ghost"
                  @click="addSkill"
                  :disabled="isLoading"
                >
                  Add
                </button>
              </div>
              <div class="skills__list">
                <span
                  v-for="skill in form.skills"
                  :key="skill"
                  class="chip"
                >
                  {{ skill }}
                  <button type="button" @click="removeSkill(skill)" :disabled="isLoading">×</button>
                </span>
              </div>
            </div>
          </div>

          <div class="form__field form__field--full">
            <label>Resume (PDF)</label>
            <input
              type="file"
              accept=".pdf"
              @change="handleFileUpload"
              :disabled="isLoading"
            />
            <span v-if="resumeFile" class="muted" style="font-size: 0.85rem; margin-top: 0.25rem;">
              Selected: {{ resumeFile.name }}
            </span>
            <span v-if="errors.resume" class="form__error">{{ errors.resume }}</span>
          </div>
        </div>

        <div v-if="errors.submit" class="alert alert--error">
          {{ errors.submit }}
        </div>

        <div class="form__actions">
          <router-link to="/dashboard/candidate" class="btn btn--ghost">Cancel</router-link>
          <button class="btn" type="submit" :disabled="isLoading">
            {{ isLoading ? 'Saving...' : 'Save Changes' }}
          </button>
        </div>
      </form>
    </section>
  </div>
</template>
