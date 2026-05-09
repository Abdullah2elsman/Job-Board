<script setup>
import { ref, computed } from 'vue'
import { useAuthStore } from './stores/auth'
import { useRouter } from 'vue-router'
import NotificationsDropdown from './components/NotificationsDropdown.vue'
import './assets/app.css'

const authStore = useAuthStore()
const router = useRouter()
const mobileMenuOpen = ref(false)

const logout = () => {
  authStore.logout()
  mobileMenuOpen.value = false
  router.push('/')
}

const closeMenu = () => {
  mobileMenuOpen.value = false
}

const userInitial = computed(() => (authStore.user?.name || 'U').charAt(0).toUpperCase())
</script>

<template>
  <header class="app-header">
    <div class="app-header__inner">

      <!-- Logo -->
      <router-link to="/" class="app-logo" @click="closeMenu">
        <span class="app-logo__mark">J</span>
        <div class="app-logo__text">
          <span class="app-logo__name">JobBoard</span>
          <span class="app-logo__tagline">Careers & Talent</span>
        </div>
      </router-link>

      <!-- Desktop nav -->
      <nav class="app-nav">
        <router-link to="/" class="nav-link">Home</router-link>

        <template v-if="authStore.isAuthenticated">
          <!-- Employer links -->
          <template v-if="authStore.isEmployer">
            <router-link to="/dashboard/employer" class="nav-link">Dashboard</router-link>
            <router-link to="/dashboard/employer/search" class="nav-link">Find Candidates</router-link>
          </template>

          <!-- Candidate links -->
          <template v-if="authStore.isCandidate">
            <router-link to="/dashboard/candidate" class="nav-link">My Applications</router-link>
            <router-link to="/dashboard/candidate/saved-jobs" class="nav-link">Saved Jobs</router-link>
          </template>

          <!-- Admin links -->
          <template v-if="authStore.isAdmin">
            <router-link to="/dashboard/admin" class="nav-link">Admin Panel</router-link>
          </template>
        </template>
      </nav>

      <!-- Right side actions -->
      <div class="app-actions">
        <template v-if="authStore.isAuthenticated">
          <NotificationsDropdown />

          <!-- Profile link -->
          <router-link v-if="authStore.isEmployer" to="/dashboard/employer/profile" class="app-avatar" title="Profile">{{ userInitial }}</router-link>
          <router-link v-else-if="authStore.isCandidate" to="/dashboard/candidate/profile" class="app-avatar" title="Profile">{{ userInitial }}</router-link>
          <div v-else class="app-avatar">{{ userInitial }}</div>

          <button @click="logout" class="btn btn--ghost btn--sm">Logout</button>
        </template>

        <template v-else>
          <router-link to="/login" class="btn btn--ghost btn--sm">Log In</router-link>
          <router-link to="/register" class="btn btn--sm">Sign Up</router-link>
        </template>

        <!-- Mobile hamburger -->
        <button class="hamburger" @click="mobileMenuOpen = !mobileMenuOpen" aria-label="Toggle menu">
          <span :class="{ open: mobileMenuOpen }"></span>
          <span :class="{ open: mobileMenuOpen }"></span>
          <span :class="{ open: mobileMenuOpen }"></span>
        </button>
      </div>
    </div>

    <!-- Mobile menu -->
    <div v-if="mobileMenuOpen" class="mobile-menu">
      <router-link to="/" class="mobile-link" @click="closeMenu">Home</router-link>

      <template v-if="authStore.isAuthenticated">
        <template v-if="authStore.isEmployer">
          <router-link to="/dashboard/employer" class="mobile-link" @click="closeMenu">Dashboard</router-link>
          <router-link to="/dashboard/employer/search" class="mobile-link" @click="closeMenu">Find Candidates</router-link>
          <router-link to="/dashboard/employer/profile" class="mobile-link" @click="closeMenu">Profile</router-link>
        </template>
        <template v-if="authStore.isCandidate">
          <router-link to="/dashboard/candidate" class="mobile-link" @click="closeMenu">My Applications</router-link>
          <router-link to="/dashboard/candidate/saved-jobs" class="mobile-link" @click="closeMenu">Saved Jobs</router-link>
          <router-link to="/dashboard/candidate/profile" class="mobile-link" @click="closeMenu">Profile</router-link>
        </template>
        <template v-if="authStore.isAdmin">
          <router-link to="/dashboard/admin" class="mobile-link" @click="closeMenu">Admin Panel</router-link>
        </template>
        <button @click="logout" class="mobile-link mobile-link--danger">Logout</button>
      </template>

      <template v-else>
        <router-link to="/login" class="mobile-link" @click="closeMenu">Log In</router-link>
        <router-link to="/register" class="mobile-link mobile-link--primary" @click="closeMenu">Sign Up</router-link>
      </template>
    </div>
  </header>

  <main class="main-container">
    <router-view></router-view>
  </main>
</template>
