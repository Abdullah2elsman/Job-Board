<script setup>
import { onMounted } from 'vue'
import { useJobsStore } from '../../stores/jobs'
import { useRouter } from 'vue-router'

const jobsStore = useJobsStore()
const router = useRouter()

onMounted(() => {
  jobsStore.fetchJobs()
})

const viewJob = (id) => {
  router.push(`/jobs/${id}`)
}
</script>

<template>
  <div class="job-list">
    <div v-if="jobsStore.loading" class="loading">Loading jobs...</div>
    <div v-else-if="jobsStore.error" class="alert alert--error">{{ jobsStore.error }}</div>
    
    <template v-else>
      <article v-for="job in jobsStore.jobs" :key="job.id" class="job-card" @click="viewJob(job.id)" style="cursor: pointer; transition: transform 0.2s;">
        <div class="job-card__header">
          <div>
            <h3>{{ job.title }}</h3>
            <p class="muted">
              <span v-if="job.employer?.name">{{ job.employer.name }} • </span>
              {{ job.location }} • {{ job.work_type }}
            </p>
          </div>
          <span class="badge badge--success">{{ job.salary ? '$' + job.salary : 'Salary config pending' }}</span>
        </div>
        
        <div style="margin-top: 1rem;">
          <p>{{ job.description ? job.description.substring(0, 120) + '...' : '' }}</p>
        </div>

        <div style="margin-top: 1rem; display: flex; gap: 0.5rem; flex-wrap: wrap;">
          <span v-for="skill in job.skills" :key="skill.id" class="chip">
            {{ skill.skill_name }}
          </span>
        </div>
      </article>

      <div v-if="!jobsStore.jobs.length" class="card" style="text-align: center; padding: 3rem;">
        <h3 class="muted">No jobs found matching your criteria</h3>
      </div>
    </template>
  </div>
</template>

<style scoped>
.job-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
</style>
