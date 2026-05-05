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
    <div v-if="jobsStore.loading" class="loading">
      <div style="margin-bottom: 1rem;">⏳</div>
      Loading latest opportunities...
    </div>
    
    <div v-else-if="jobsStore.error" class="alert alert--error">
      {{ jobsStore.error }}
    </div>
    
    <template v-else>
      <article 
        v-for="job in jobsStore.jobs" 
        :key="job.id" 
        class="job-card" 
        @click="viewJob(job.id)" 
        style="cursor: pointer;"
      >
        <div class="job-card__header">
          <div style="flex: 1;">
            <div class="flex-between" style="margin-bottom: 0.5rem; justify-content: flex-start; gap: 0.75rem;">
              <h3 style="margin: 0; font-size: 1.25rem;">{{ job.title }}</h3>
              <span v-if="job.salary" class="badge badge--success" style="font-size: 0.75rem;">
                ${{ job.salary }}
              </span>
            </div>
            <p class="muted" style="margin-bottom: 0.75rem; font-weight: 500;">
              <span v-if="job.employer?.name" style="color: var(--text-primary);">{{ job.employer.name }}</span>
              <span v-if="job.employer?.name"> • </span>
              {{ job.location }} • {{ job.work_type }}
            </p>
          </div>
          <div class="badge" style="background: var(--bg-secondary); color: var(--text-muted); font-size: 0.7rem;">
            {{ new Date(job.created_at).toLocaleDateString() }}
          </div>
        </div>
        
        <div style="margin-bottom: 1.25rem;">
          <p style="font-size: 0.95rem; color: var(--text-secondary); line-height: 1.6;">
            {{ job.description ? job.description.substring(0, 160) + '...' : 'No description provided.' }}
          </p>
        </div>

        <div class="flex-wrap" style="gap: 0.5rem;">
          <span v-if="job.category" class="badge" style="font-size: 0.7rem;">{{ job.category.name }}</span>
          <span v-for="skill in job.skills" :key="skill.id" class="chip" style="font-size: 0.75rem; padding: 0.25rem 0.75rem;">
            {{ skill.skill_name }}
          </span>
        </div>
      </article>

      <div v-if="!jobsStore.jobs.length" class="card" style="text-align: center; padding: 4rem 2rem;">
        <div style="font-size: 3rem; margin-bottom: 1rem;">🔍</div>
        <h3 style="color: var(--text-primary);">No jobs found</h3>
        <p class="muted">Try adjusting your filters or check back later for new opportunities.</p>
      </div>
    </template>
  </div>
</template>
