<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const form = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  role: 'candidate'
})

const showPassword = ref(false)
const errors = reactive({})
const isLoading = ref(false)

const validateForm = () => {
  Object.keys(errors).forEach(key => delete errors[key])
  
  if (!form.name) errors.name = 'Full name is required'
  if (!form.email) errors.email = 'Email is required'
  if (!form.password) errors.password = 'Password is required'
  else if (form.password.length < 6) errors.password = 'Password must be at least 6 characters'
  if (form.password !== form.password_confirmation) errors.password_confirmation = 'Passwords do not match'
  
  return Object.keys(errors).length === 0
}

const handleSubmit = async () => {
  authStore.error = null
  if (!validateForm()) return
  
  isLoading.value = true
  const success = await authStore.register({
    name: form.name,
    email: form.email,
    password: form.password,
    password_confirmation: form.password_confirmation,
    role: form.role
  })
  isLoading.value = false
  
  if (success) {
    const role = authStore.userRole
    if (role === 'employer') router.push({ name: 'EmployerDashboard' })
    else if (role === 'candidate') router.push({ name: 'CandidateDashboard' })
    else router.push({ name: 'Home' })
  }
}
</script>

<template>
  <div style="display: flex; align-items: center; justify-content: center; min-height: calc(100vh - 100px); padding: 2rem 1rem;">
    <section class="card" style="max-width: 540px; width: 100%; padding: 2.5rem;">
      <header style="text-align: center; margin-bottom: 2rem;">
        <h2 style="font-size: 1.75rem; margin-bottom: 0.5rem;">Join JobBoard</h2>
        <p class="muted">Create your account to start your journey</p>
      </header>

      <div v-if="authStore.error" class="alert alert--error" style="margin-bottom: 1.5rem;">
        {{ authStore.error }}
      </div>

      <form @submit.prevent="handleSubmit" class="form">
        <div class="form__field">
          <label>Full Name</label>
          <input v-model="form.name" type="text" placeholder="e.g. John Doe" :disabled="isLoading" required />
          <span v-if="errors.name" class="form__error">{{ errors.name }}</span>
        </div>

        <div class="form__field">
          <label>Email Address</label>
          <input v-model="form.email" type="email" placeholder="name@example.com" :disabled="isLoading" required />
          <span v-if="errors.email" class="form__error">{{ errors.email }}</span>
        </div>

        <div class="form__grid">
          <div class="form__field">
            <label>Password</label>
            <input v-model="form.password" :type="showPassword ? 'text' : 'password'" placeholder="••••••••" :disabled="isLoading" required />
            <span v-if="errors.password" class="form__error">{{ errors.password }}</span>
          </div>
          <div class="form__field">
            <label>Confirm Password</label>
            <input v-model="form.password_confirmation" :type="showPassword ? 'text' : 'password'" placeholder="••••••••" :disabled="isLoading" required />
            <span v-if="errors.password_confirmation" class="form__error">{{ errors.password_confirmation }}</span>
          </div>
        </div>

        <div class="form__field">
          <label>I am looking to</label>
          <select v-model="form.role" :disabled="isLoading">
            <option value="candidate">Find a Job (Candidate)</option>
            <option value="employer">Hire Talent (Employer)</option>
          </select>
        </div>

        <button type="submit" class="btn" style="width: 100%; margin-top: 1rem; padding: 0.9rem;" :disabled="isLoading">
          {{ isLoading ? 'Creating account...' : 'Create Account' }}
        </button>
      </form>

      <div style="text-align: center; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--border-color);">
        <p class="muted">
          Already have an account? 
          <router-link to="/login" style="color: var(--primary); font-weight: 600; text-decoration: none;">Sign In</router-link>
        </p>
      </div>
    </section>
  </div>
</template>
