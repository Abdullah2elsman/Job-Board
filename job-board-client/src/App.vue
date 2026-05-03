<script setup>
import JobCreate from "./components/jobs/JobCreate.vue";
import EmployerDashboard from "./components/jobs/EmployerDashboard.vue";
</script>

<template>
  <main class="layout">
    <header class="page-header">
      <div>
        <h1>Job Management</h1>
        <p class="muted">
          Create new job postings and manage their approval status.
        </p>
      </div>
    </header>

    <section class="grid">
      <JobCreate />
      <EmployerDashboard />
    </section>
  </main>
</template>
