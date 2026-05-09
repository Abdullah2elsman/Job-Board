<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue'
import { useAuthStore } from '../stores/auth'
import { useJobsStore } from '../stores/jobs'
import FilterSidebar from '../components/jobs/FilterSidebar.vue'
import JobFeed from '../components/jobs/JobFeed.vue'
import '../assets/home.css'

const authStore = useAuthStore()
const jobsStore = useJobsStore()

const searchKeyword = ref('')
let debounceTimer = null

const totalJobs = computed(() => jobsStore.pagination?.total ?? jobsStore.jobs.length)
const totalCategories = computed(() => jobsStore.categories.length)

watch(searchKeyword, (val) => {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(() => {
    jobsStore.fetchJobs({ keyword: val.trim() })
  }, 400)
})

const heroSearch = () => {
  clearTimeout(debounceTimer)
  jobsStore.fetchJobs({ keyword: searchKeyword.value.trim() })
}

onMounted(() => {
  if (!jobsStore.categories.length) jobsStore.fetchCategories()
})

onUnmounted(() => clearTimeout(debounceTimer))
</script>

<template>
  <!-- Hero -->
  <section class="hero">
    <div class="hero__content">
      <div class="hero__eyebrow">
        <span class="badge badge--success" style="font-size: 0.75rem;">Now Hiring</span>
        <span class="muted" style="font-size: 0.85rem;">Thousands of opportunities waiting</span>
      </div>
      <h1 class="hero__title">
        Find Your Next<br />
        <span class="hero__title--accent">Dream Job</span>
      </h1>
      <p class="hero__subtitle">
        Browse curated roles from top companies. Filter by location, salary, and experience — then apply in minutes.
      </p>

      <form class="hero__search" @submit.prevent="heroSearch">
        <input v-model="searchKeyword" type="text" placeholder="Job title, skill, or keyword..." />
        <button type="submit" class="btn">Search Jobs</button>
      </form>

      <div class="hero__stats">
        <div class="hero__stat">
          <span class="hero__stat-value">{{ totalJobs || '100+' }}</span>
          <span class="hero__stat-label">Open Roles</span>
        </div>
        <div class="hero__stat-divider"></div>
        <div class="hero__stat">
          <span class="hero__stat-value">{{ totalCategories || '10+' }}</span>
          <span class="hero__stat-label">Categories</span>
        </div>
        <div class="hero__stat-divider"></div>
        <div class="hero__stat">
          <span class="hero__stat-value">Remote</span>
          <span class="hero__stat-label">Friendly</span>
        </div>
      </div>
    </div>

    <div class="hero__visual" aria-hidden="true">
      <div class="hero__blob hero__blob--1"></div>
      <div class="hero__blob hero__blob--2"></div>
      <div class="hero__card-preview">
        <div class="preview-card">
          <div class="preview-card__dot" style="background: var(--primary);"></div>
          <div>
            <div class="preview-card__line preview-card__line--title"></div>
            <div class="preview-card__line preview-card__line--sub"></div>
          </div>
        </div>
        <div class="preview-card preview-card--offset">
          <div class="preview-card__dot" style="background: var(--success);"></div>
          <div>
            <div class="preview-card__line preview-card__line--title"></div>
            <div class="preview-card__line preview-card__line--sub" style="width: 55%;"></div>
          </div>
        </div>
        <div class="preview-card preview-card--offset2">
          <div class="preview-card__dot" style="background: hsl(280, 70%, 60%);"></div>
          <div>
            <div class="preview-card__line preview-card__line--title" style="width: 70%;"></div>
            <div class="preview-card__line preview-card__line--sub" style="width: 40%;"></div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA strip for guests -->
  <div v-if="!authStore.isAuthenticated" class="cta-strip">
    <p>Ready to get hired? Create a free account and start applying today.</p>
    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
      <router-link to="/register" class="btn">Get Started Free</router-link>
      <router-link to="/login" class="btn btn--ghost">Sign In</router-link>
    </div>
  </div>

  <!-- Job Listings -->
  <div id="job-listings" style="margin-top: 2rem;">
    <div class="listings-header">
      <h2 style="font-size: 1.35rem; margin: 0;">Latest Opportunities</h2>
      <span v-if="totalJobs" class="badge">{{ totalJobs }} jobs</span>
    </div>
    <div class="filter-bar">
      <FilterSidebar />
    </div>
    <JobFeed />
  </div>
</template>

