<script setup>
import FilterSidebar from '../components/jobs/FilterSidebar.vue'
import JobFeed from '../components/jobs/JobFeed.vue'
</script>

<template>
  <div class="grid" style="grid-template-columns: 300px 1fr; align-items: start;">
    <FilterSidebar />
    <JobFeed />
  </div>
</template>
