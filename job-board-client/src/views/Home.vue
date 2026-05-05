<script setup>
import FilterSidebar from '../components/jobs/FilterSidebar.vue'
import JobFeed from '../components/jobs/JobFeed.vue'
</script>

<template>
  <div class="sidebar-layout">
    <FilterSidebar />
    <JobFeed />
  </div>
</template>

<style scoped>
.sidebar-layout {
  display: grid;
  grid-template-columns: 1fr;
  gap: 2rem;
  align-items: start;
}

@media (min-width: 768px) {
  .sidebar-layout {
    grid-template-columns: 280px 1fr;
  }
}
</style>
