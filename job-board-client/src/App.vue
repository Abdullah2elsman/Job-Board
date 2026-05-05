<script setup>
import { useAuthStore } from './stores/auth'
import { useRouter } from 'vue-router'
import NotificationsDropdown from './components/NotificationsDropdown.vue'

const authStore = useAuthStore()
const router = useRouter()

const logout = () => {
  authStore.logout()
  router.push('/')
}
</script>

<template>
  <header class="app-header">
    <div class="app-header__inner">
      <div style="display: flex; flex-direction: column;">
        <h1 style="font-size: 1.5rem; margin: 0; color: var(--primary); letter-spacing: -0.05em;">JobBoard</h1>
        <span class="muted" style="font-size: 0.75rem; letter-spacing: 0.05em; text-transform: uppercase;">Careers & Talent</span>
      </div>
      
      <nav class="app-nav">
        <router-link to="/" class="btn btn--ghost">Home</router-link>
        
        <template v-if="authStore.isAuthenticated">
          <NotificationsDropdown />
          <router-link v-if="authStore.isEmployer" to="/dashboard/employer" class="btn btn--ghost">Dashboard</router-link>
          <router-link v-if="authStore.isEmployer" to="/dashboard/employer/search" class="btn btn--ghost">Search Candidates</router-link>
          <router-link v-if="authStore.isEmployer" to="/dashboard/employer/profile" class="btn btn--ghost">Profile</router-link>
          
          <router-link v-if="authStore.isCandidate" to="/dashboard/candidate" class="btn btn--ghost">My Apps</router-link>
          <router-link v-if="authStore.isCandidate" to="/dashboard/candidate/saved-jobs" class="btn btn--ghost">Saved</router-link>
          <router-link v-if="authStore.isCandidate" to="/dashboard/candidate/profile" class="btn btn--ghost">Profile</router-link>
          
          <router-link v-if="authStore.isAdmin" to="/dashboard/admin" class="btn btn--ghost">Admin</router-link>
          <button @click="logout" class="btn btn--danger">Logout</button>
        </template>
        <template v-else>
          <router-link to="/login" class="btn btn--ghost">Log In</router-link>
          <router-link to="/register" class="btn">Sign Up</router-link>
        </template>
      </nav>
    </div>
  </header>

  <main class="main-container">
    <router-view></router-view>
  </main>
</template>
