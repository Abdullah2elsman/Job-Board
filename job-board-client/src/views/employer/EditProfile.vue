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
  if (!form.company_name) errors.company_name = 'Company name is required'
  if (!form.company_description) errors.company_description = 'Company description is required'
  if (!form.location) errors.location = 'Location is required'
  return Object.keys(errors).length === 0
}

const handleSubmit = async () => {
  successMessage.value = ''
  if (!validateForm()) return
  
  isLoading.value = true
  try {
    const { data } = await api.put('/api/employer/profile', form)
    authStore.user = { ...authStore.user, ...data.data }
    localStorage.setItem('user', JSON.stringify(authStore.user))
    successMessage.value = 'Company profile updated successfully'
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
    form.company_name = authStore.user.company_name || ''
    form.company_description = authStore.user.company_description || ''
    form.website = authStore.user.website || ''
    form.location = authStore.user.location || ''
  }
})
</script>

<template>
  <div style="max-width: 700px; margin: 0 auto; padding: 2rem 1rem;">
    <header style="margin-bottom: 2.5rem; text-align: center;">
      <h1 style="margin-bottom: 0.5rem;">Company Settings</h1>
      <p class="muted">Showcase your company brand to attract the best candidates</p>
    </header>

    <div v-if="successMessage" class="alert alert--success" style="margin-bottom: 2rem;">
      {{ successMessage }}
    </div>

    <section class="card" style="padding: 2.5rem;">
      <form @submit.prevent="handleSubmit" class="form">
        <div class="form__field">
          <label>Company Name</label>
          <input v-model="form.company_name" type="text" placeholder="e.g. Acme Corp" :disabled="isLoading" />
          <span v-if="errors.company_name" class="form__error">{{ errors.company_name }}</span>
        </div>

        <div class="form__field">
          <label>About the Company</label>
          <textarea v-model="form.company_description" placeholder="Our mission is to..." rows="5" :disabled="isLoading"></textarea>
          <span v-if="errors.company_description" class="form__error">{{ errors.company_description }}</span>
        </div>

        <div class="form__grid" style="grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));">
          <div class="form__field">
            <label>Corporate Website</label>
            <input v-model="form.website" type="url" placeholder="https://acme.com" :disabled="isLoading" />
            <span v-if="errors.website" class="form__error">{{ errors.website }}</span>
          </div>

          <div class="form__field">
            <label>Headquarters Location</label>
            <input v-model="form.location" type="text" placeholder="City, State" :disabled="isLoading" />
            <span v-if="errors.location" class="form__error">{{ errors.location }}</span>
          </div>
        </div>

        <div v-if="errors.submit" class="alert alert--error" style="margin-top: 1.5rem;">
          {{ errors.submit }}
        </div>

        <div class="form__actions" style="margin-top: 2.5rem; border-top: 1px solid var(--border-color); padding-top: 2rem;">
          <router-link to="/dashboard/employer" class="btn btn--ghost">Back to Dashboard</router-link>
          <button class="btn" type="submit" :disabled="isLoading" style="padding: 0.75rem 2rem;">
            {{ isLoading ? 'Saving Changes...' : 'Save Settings' }}
          </button>
        </div>
      </form>
    </section>
  </div>
</template>
