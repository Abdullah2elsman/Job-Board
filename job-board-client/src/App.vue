<script setup>
import { useAuthStore } from './stores/auth'
import { useRouter } from 'vue-router'

const authStore = useAuthStore()
const router = useRouter()

const logout = () => {
  authStore.logout()
  router.push('/')
}
</script>

<template>
  <main class="layout">
    <header class="page-header">
      <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
        <div>
          <h1>Job Board</h1>
          <p class="muted">Find your dream job or post a new one.</p>
        </div>
        <nav style="display: flex; gap: 1rem;">
          <router-link to="/" class="btn btn--ghost">Home</router-link>
          
          <template v-if="authStore.isAuthenticated">
            <router-link v-if="authStore.isEmployer" to="/dashboard/employer" class="btn btn--ghost">Employer Dashboard</router-link>
            <router-link v-if="authStore.isCandidate" to="/dashboard/candidate" class="btn btn--ghost">My Applications</router-link>
            <router-link v-if="authStore.isAdmin" to="/dashboard/admin" class="btn btn--ghost">Admin Panel</router-link>
            <button @click="logout" class="btn btn--danger">Logout</button>
          </template>
          <template v-else>
            <!-- In a real app we'd link to login/register views -->
            <span class="muted">(Login missing)</span>
          </template>
        </nav>
      </div>
    </header>

    <router-view></router-view>
  </main>
</template>

