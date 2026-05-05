<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const form = reactive({
  email: '',
  password: ''
})

const showPassword = ref(false)
const errors = reactive({})
const isLoading = ref(false)

const validateForm = () => {
  Object.keys(errors).forEach(key => delete errors[key])
  
  if (!form.email) {
    errors.email = 'Email is required'
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) {
    errors.email = 'Please enter a valid email'
  }
  
  if (!form.password) {
    errors.password = 'Password is required'
  }
  
  return Object.keys(errors).length === 0
}

const handleSubmit = async () => {
  authStore.error = null
  if (!validateForm()) return
  
  isLoading.value = true
  const success = await authStore.login({
    email: form.email,
    password: form.password
  })
  isLoading.value = false
  
  if (success) {
    const role = authStore.userRole
    if (role === 'employer') {
      router.push({ name: 'EmployerDashboard' })
    } else if (role === 'candidate') {
      router.push({ name: 'CandidateDashboard' })
    } else {
      router.push({ name: 'Home' })
    }
  }
}
</script>

<template>
  <div style="display: flex; align-items: center; justify-content: center; min-height: calc(100vh - 200px); padding: 2rem 1rem;">
    <section class="card" style="max-width: 440px; width: 100%; padding: 2.5rem;">
      <header style="text-align: center; margin-bottom: 2rem;">
        <h2 style="font-size: 1.75rem; margin-bottom: 0.5rem;">Welcome Back</h2>
        <p class="muted">Enter your credentials to access your account</p>
      </header>

      <div v-if="authStore.error" class="alert alert--error" style="margin-bottom: 1.5rem;">
        {{ authStore.error }}
      </div>

      <form @submit.prevent="handleSubmit" class="form">
        <div class="form__field">
          <label>Email Address</label>
          <input
            v-model="form.email"
            type="email"
            placeholder="name@company.com"
            :disabled="isLoading"
            required
          />
          <span v-if="errors.email" class="form__error">{{ errors.email }}</span>
        </div>

        <div class="form__field">
          <label>Password</label>
          <div style="position: relative;">
            <input
              v-model="form.password"
              :type="showPassword ? 'text' : 'password'"
              placeholder="••••••••"
              :disabled="isLoading"
              style="padding-right: 3rem;"
              required
            />
            <button
              type="button"
              @click="showPassword = !showPassword"
              style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--text-muted);"
            >
              <span v-if="showPassword">🙈</span>
              <span v-else>👁️</span>
            </button>
          </div>
          <span v-if="errors.password" class="form__error">{{ errors.password }}</span>
        </div>

        <button
          type="submit"
          class="btn"
          style="width: 100%; margin-top: 1rem; padding: 0.9rem;"
          :disabled="isLoading"
        >
          {{ isLoading ? 'Signing in...' : 'Sign In' }}
        </button>
      </form>

      <div style="text-align: center; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--border-color);">
        <p class="muted">
          Don't have an account? 
          <router-link to="/register" style="color: var(--primary); font-weight: 600; text-decoration: none;">Create Account</router-link>
        </p>
      </div>
    </section>
  </div>
</template>
