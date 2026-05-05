<script setup>
import { reactive, ref, onMounted } from 'vue'
import { useAuthStore } from '../../stores/auth'
import api from '../../services/api'

const authStore = useAuthStore()

const form = reactive({
  company_name: '',
  company_description: '',
  website: '',
  location: ''
})

const errors = reactive({})
const isLoading = ref(false)
const successMessage = ref('')

const validateForm = () => {
  Object.keys(errors).forEach(key => delete errors[key])
  
  if (!form.company_name) {
    errors.company_name = 'Company name is required'
  } else if (form.company_name.length < 2) {
    errors.company_name = 'Company name must be at least 2 characters'
  }
  
  if (!form.company_description) {
    errors.company_description = 'Company description is required'
  } else if (form.company_description.length < 10) {
    errors.company_description = 'Description must be at least 10 characters'
  }
  
  if (form.website && !/^https?:\/\/.+/.test(form.website)) {
    errors.website = 'Please enter a valid URL (e.g., https://example.com)'
  }
  
  if (!form.location) {
    errors.location = 'Location is required'
  }
  
  return Object.keys(errors).length === 0
}

const handleSubmit = async () => {
  successMessage.value = ''
  
  if (!validateForm()) {
    return
  }
  
  isLoading.value = true
  
  try {
    const { data } = await api.put('/api/employer/profile', form)
    const updatedUser = data?.data || data?.user || data
    authStore.user = { ...authStore.user, ...updatedUser }
    localStorage.setItem('user', JSON.stringify(authStore.user))
    successMessage.value = data?.message || 'Profile updated successfully'
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
    form.company_name = authStore.user.company_name || ''
    form.company_description = authStore.user.company_description || ''
    form.website = authStore.user.website || ''
    form.location = authStore.user.location || ''
  }
})
</script>

<template>
  <div class="layout">
    <section class="card" style="max-width: 600px; margin: 0 auto; width: 100%;">
      <header class="card__header">
        <div>
          <h2>Edit Company Profile</h2>
          <p class="muted">Update your company information</p>
        </div>
      </header>

      <div v-if="successMessage" class="alert alert--success">
        {{ successMessage }}
      </div>

      <form @submit.prevent="handleSubmit" class="form">
        <div class="form__grid">
          <div class="form__field form__field--full">
            <label>Company Name</label>
            <input
              v-model="form.company_name"
              type="text"
              placeholder="Your Company Inc."
              :disabled="isLoading"
            />
            <span v-if="errors.company_name" class="form__error">{{ errors.company_name }}</span>
          </div>

          <div class="form__field form__field--full">
            <label>Company Description</label>
            <textarea
              v-model="form.company_description"
              placeholder="Tell us about your company..."
              rows="4"
              :disabled="isLoading"
            ></textarea>
            <span v-if="errors.company_description" class="form__error">{{ errors.company_description }}</span>
          </div>

          <div class="form__field">
            <label>Website</label>
            <input
              v-model="form.website"
              type="url"
              placeholder="https://example.com"
              :disabled="isLoading"
            />
            <span v-if="errors.website" class="form__error">{{ errors.website }}</span>
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
        </div>

        <div v-if="errors.submit" class="alert alert--error">
          {{ errors.submit }}
        </div>

        <div class="form__actions">
          <router-link to="/dashboard/employer" class="btn btn--ghost">Cancel</router-link>
          <button class="btn" type="submit" :disabled="isLoading">
            {{ isLoading ? 'Saving...' : 'Save Changes' }}
          </button>
        </div>
      </form>
    </section>
  </div>
</template>
