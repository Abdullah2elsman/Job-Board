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
const showPasswordConfirm = ref(false)
const errors = reactive({})
const isLoading = ref(false)

const validateForm = () => {
  Object.keys(errors).forEach(key => delete errors[key])
  
  if (!form.name) {
    errors.name = 'Name is required'
  } else if (form.name.length < 2) {
    errors.name = 'Name must be at least 2 characters'
  }
  
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
  
  if (!form.password_confirmation) {
    errors.password_confirmation = 'Password confirmation is required'
  } else if (form.password !== form.password_confirmation) {
    errors.password_confirmation = 'Passwords do not match'
  }
  
  if (!form.role) {
    errors.role = 'Please select a role'
  }
  
  return Object.keys(errors).length === 0
}

const handleSubmit = async () => {
  authStore.error = null
  
  if (!validateForm()) {
    return
  }
  
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

const togglePasswordConfirmVisibility = () => {
  showPasswordConfirm.value = !showPasswordConfirm.value
}
</script>

<template>
  <div class="layout">
    <section class="card" style="max-width: 500px; margin: 2rem auto; width: 100%;">
      <header class="card__header">
        <h2>Register</h2>
      </header>

      <div v-if="authStore.error" class="alert alert--error">
        {{ authStore.error }}
      </div>

      <form @submit.prevent="handleSubmit" class="form">
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

        <div class="form__field form__field--full">
          <label>Confirm Password</label>
          <div style="position: relative;">
            <input
              v-model="form.password_confirmation"
              :type="showPasswordConfirm ? 'text' : 'password'"
              placeholder="••••••••"
              :disabled="isLoading"
            />
            <button
              type="button"
              @click="togglePasswordConfirmVisibility"
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
              {{ showPasswordConfirm ? '✕' : '●' }}
            </button>
          </div>
          <span v-if="errors.password_confirmation" class="form__error">{{ errors.password_confirmation }}</span>
        </div>

        <div class="form__field form__field--full">
          <label>I am a</label>
          <select v-model="form.role" :disabled="isLoading">
            <option value="candidate">Candidate (Job Seeker)</option>
            <option value="employer">Employer (Hiring)</option>
          </select>
          <span v-if="errors.role" class="form__error">{{ errors.role }}</span>
        </div>

        <div class="form__actions" style="justify-content: flex-end;">
          <button
            type="submit"
            class="btn"
            :disabled="isLoading"
          >
            {{ isLoading ? 'Creating account...' : 'Register' }}
          </button>
        </div>
      </form>

      <p class="muted" style="text-align: center; margin-top: 1.5rem; font-size: 0.9rem;">
        Already have an account? <router-link to="/login" style="color: #2563eb; text-decoration: none;">Login</router-link>
      </p>
    </section>
  </div>
</template>
