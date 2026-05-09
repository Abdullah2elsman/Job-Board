<script setup>
import { ref, computed } from 'vue'
import { useAuthStore } from './stores/auth'
import { useRouter } from 'vue-router'
import NotificationsDropdown from './components/NotificationsDropdown.vue'

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

<style scoped>
/* Logo */
.app-logo {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  text-decoration: none;
  flex-shrink: 0;
}

.app-logo__mark {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  background: var(--primary);
  color: white;
  font-weight: 800;
  font-size: 1.1rem;
  display: flex;
  align-items: center;
  justify-content: center;
  letter-spacing: -0.05em;
}

.app-logo__text {
  display: flex;
  flex-direction: column;
  line-height: 1.1;
}

.app-logo__name {
  font-size: 1.1rem;
  font-weight: 800;
  color: var(--text-primary);
  letter-spacing: -0.04em;
}

.app-logo__tagline {
  font-size: 0.65rem;
  color: var(--text-muted);
  text-transform: uppercase;
  letter-spacing: 0.08em;
  font-weight: 600;
}

/* Nav links */
.app-nav {
  display: none;
  align-items: center;
  gap: 0.25rem;
}

@media (min-width: 768px) {
  .app-nav {
    display: flex;
  }
}

.nav-link {
  padding: 0.45rem 0.85rem;
  border-radius: var(--radius-pill);
  font-size: 0.9rem;
  font-weight: 600;
  color: var(--text-secondary);
  text-decoration: none;
  transition: all 0.2s;
  white-space: nowrap;
}

.nav-link:hover {
  background: var(--bg-secondary);
  color: var(--text-primary);
}

.nav-link.router-link-active {
  background: var(--bg-secondary);
  color: var(--primary);
}

/* Right actions */
.app-actions {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  flex-shrink: 0;
}

.btn--sm {
  padding: 0.5rem 1rem;
  font-size: 0.85rem;
}

/* Avatar */
.app-avatar {
  width: 34px;
  height: 34px;
  border-radius: 50%;
  background: var(--primary);
  color: white;
  font-weight: 700;
  font-size: 0.85rem;
  display: flex;
  align-items: center;
  justify-content: center;
  text-decoration: none;
  flex-shrink: 0;
  transition: opacity 0.2s;
}

.app-avatar:hover {
  opacity: 0.85;
}

/* Hamburger */
.hamburger {
  display: flex;
  flex-direction: column;
  justify-content: center;
  gap: 5px;
  width: 36px;
  height: 36px;
  background: none;
  border: none;
  cursor: pointer;
  padding: 0.4rem;
  border-radius: var(--radius-sm);
}

.hamburger:hover {
  background: var(--bg-secondary);
}

.hamburger span {
  display: block;
  height: 2px;
  background: var(--text-primary);
  border-radius: 2px;
  transition: all 0.25s;
}

.hamburger span.open:nth-child(1) {
  transform: translateY(7px) rotate(45deg);
}

.hamburger span.open:nth-child(2) {
  opacity: 0;
}

.hamburger span.open:nth-child(3) {
  transform: translateY(-7px) rotate(-45deg);
}

@media (min-width: 768px) {
  .hamburger {
    display: none;
  }
}

/* Mobile menu */
.mobile-menu {
  display: flex;
  flex-direction: column;
  border-top: 1px solid var(--border-color);
  padding: 0.75rem 1.5rem 1rem;
  gap: 0.25rem;
}

@media (min-width: 768px) {
  .mobile-menu {
    display: none;
  }
}

.mobile-link {
  display: block;
  padding: 0.7rem 0.75rem;
  border-radius: var(--radius-sm);
  font-size: 0.95rem;
  font-weight: 600;
  color: var(--text-secondary);
  text-decoration: none;
  background: none;
  border: none;
  cursor: pointer;
  text-align: left;
  transition: background 0.2s;
}

.mobile-link:hover {
  background: var(--bg-secondary);
  color: var(--text-primary);
}

.mobile-link.router-link-active {
  color: var(--primary);
  background: var(--bg-secondary);
}

.mobile-link--danger {
  color: var(--danger);
}

.mobile-link--primary {
  color: var(--primary);
}
</style>
