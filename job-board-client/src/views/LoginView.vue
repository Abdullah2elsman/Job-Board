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
  } else if (form.password.length < 6) {
    errors.password = 'Password must be at least 6 characters'
  }
  
  return Object.keys(errors).length === 0
}

const handleSubmit = async () => {
  authStore.error = null
  
  if (!validateForm()) {
    return
  }
  
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

const togglePasswordVisibility = () => {
  showPassword.value = !showPassword.value
}
</script>

<template>
  <div class="layout">
    <section class="card" style="max-width: 400px; margin: 2rem auto; width: 100%;">
      <header class="card__header">
        <h2>Login</h2>
      </header>

      <div v-if="authStore.error" class="alert alert--error">
        {{ authStore.error }}
      </div>

      <form @submit.prevent="handleSubmit" class="form">
        <div class="form__field form__field--full">
          <label>Email</label>
          <input
            v-model="form.email"
            type="email"
            placeholder="you@example.com"
            :disabled="isLoading"
          />
          <span v-if="errors.email" class="form__error">{{ errors.email }}</span>
        </div>

        <div class="form__field form__field--full">
          <label>Password</label>
          <div style="position: relative;">
            <input
              v-model="form.password"
              :type="showPassword ? 'text' : 'password'"
              placeholder="••••••••"
              :disabled="isLoading"
            />
            <button
              type="button"
              @click="togglePasswordVisibility"
              :disabled="isLoading"
              style="
                position: absolute;
                right: 0.75rem;
                top: 50%;
                transform: translateY(-50%);
                background: none;
                border: none;
                cursor: pointer;
                color: #64748b;
                font-size: 0.95rem;
              "
            >
              {{ showPassword ? '✕' : '●' }}
            </button>
          </div>
          <span v-if="errors.password" class="form__error">{{ errors.password }}</span>
        </div>

        <div class="form__actions" style="justify-content: flex-end;">
          <button
            type="submit"
            class="btn"
            :disabled="isLoading"
          >
            {{ isLoading ? 'Logging in...' : 'Login' }}
          </button>
        </div>
      </form>

      <p class="muted" style="text-align: center; margin-top: 1.5rem; font-size: 0.9rem;">
        Don't have an account? <router-link to="/" style="color: #2563eb; text-decoration: none;">Sign up</router-link>
      </p>
    </section>
  </div>
</template>
