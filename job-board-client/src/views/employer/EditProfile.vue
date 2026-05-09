<script setup>
import { reactive, ref, onMounted, computed } from 'vue'
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

const companyInitial = computed(() =>
  (form.company_name || authStore.user?.name || 'C').charAt(0).toUpperCase()
)

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
  <div class="profile-page">

    <!-- Page header -->
    <div class="profile-page__header">
      <div class="profile-avatar">{{ companyInitial }}</div>
      <div>
        <h1 class="profile-page__title">Company Profile</h1>
        <p class="muted">Manage your company information visible to candidates</p>
      </div>
    </div>

    <transition name="fade">
      <div v-if="successMessage" class="alert alert--success profile-alert">{{ successMessage }}</div>
    </transition>
    <div v-if="errors.submit" class="alert alert--error profile-alert">{{ errors.submit }}</div>

    <form @submit.prevent="handleSubmit" class="profile-form">

      <!-- Company identity -->
      <div class="card profile-card">
        <div class="profile-card__heading">
          <span class="profile-card__icon">🏢</span>
          <h2>Company Identity</h2>
        </div>
        <div class="profile-fields">
          <div class="form__field">
            <label>Company Name <span class="required">*</span></label>
            <input v-model="form.company_name" type="text" placeholder="e.g. Acme Corp" :disabled="isLoading" />
            <span v-if="errors.company_name" class="form__error">{{ errors.company_name }}</span>
          </div>
          <div class="form__field">
            <label>About the Company <span class="required">*</span></label>
            <textarea v-model="form.company_description" placeholder="Describe your company mission, culture, and what makes you unique..." rows="5" :disabled="isLoading"></textarea>
            <span v-if="errors.company_description" class="form__error">{{ errors.company_description }}</span>
          </div>
        </div>
      </div>

      <!-- Contact & location -->
      <div class="card profile-card">
        <div class="profile-card__heading">
          <span class="profile-card__icon">📍</span>
          <h2>Contact & Location</h2>
        </div>
        <div class="profile-fields profile-fields--grid">
          <div class="form__field">
            <label>Corporate Website</label>
            <input v-model="form.website" type="url" placeholder="https://acme.com" :disabled="isLoading" />
            <span v-if="errors.website" class="form__error">{{ errors.website }}</span>
          </div>
          <div class="form__field">
            <label>Headquarters Location <span class="required">*</span></label>
            <input v-model="form.location" type="text" placeholder="City, Country" :disabled="isLoading" />
            <span v-if="errors.location" class="form__error">{{ errors.location }}</span>
          </div>
        </div>
      </div>

      <!-- Actions -->
      <div class="profile-actions">
        <router-link to="/dashboard/employer" class="btn btn--ghost">← Back to Dashboard</router-link>
        <button class="btn profile-save-btn" type="submit" :disabled="isLoading">
          {{ isLoading ? 'Saving...' : 'Save Changes' }}
        </button>
      </div>

    </form>
  </div>
</template>

<style scoped>
.profile-page {
  max-width: 760px;
  margin: 0 auto;
}

.profile-page__header {
  display: flex;
  align-items: center;
  gap: 1.25rem;
  margin-bottom: 2rem;
}

.profile-avatar {
  width: 64px;
  height: 64px;
  border-radius: 16px;
  background: var(--primary);
  color: white;
  font-size: 1.75rem;
  font-weight: 800;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.profile-page__title {
  font-size: 1.6rem;
  margin: 0 0 0.25rem;
}

.profile-alert {
  margin-bottom: 1.5rem;
}

.profile-form {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

.profile-card {
  padding: 1.75rem;
}

.profile-card__heading {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  margin-bottom: 1.5rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid var(--border-color);
}

.profile-card__heading h2 {
  font-size: 1rem;
  font-weight: 700;
  margin: 0;
  color: var(--text-primary);
}

.profile-card__icon {
  font-size: 1.1rem;
}

.profile-fields {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

.profile-fields--grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.25rem;
}

@media (max-width: 600px) {
  .profile-fields--grid {
    grid-template-columns: 1fr;
  }
}

.required {
  color: var(--danger);
}

.form__error {
  font-size: 0.8rem;
  color: var(--danger);
  margin-top: 0.25rem;
}

.profile-actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 1rem;
  padding-top: 0.5rem;
}

.profile-save-btn {
  padding: 0.75rem 2.5rem;
}

.fade-enter-active, .fade-leave-active { transition: opacity 0.3s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
