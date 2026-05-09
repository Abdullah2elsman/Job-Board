<script setup>
import { reactive, watch, onMounted } from 'vue'
import { useJobsStore } from '../../stores/jobs'
import '../../assets/filter-sidebar.css'

const jobsStore = useJobsStore()

const filters = reactive({
  keyword: '',
  category_id: '',
  location: '',
  work_type: '',
  min_salary: '',
  max_salary: '',
  experience_level: '',
  sort_by: 'latest'
})

const applyFilters = () => {
  jobsStore.fetchJobs(filters)
}

const clearFilters = () => {
  filters.keyword = ''
  filters.category_id = ''
  filters.location = ''
  filters.work_type = ''
  filters.min_salary = ''
  filters.max_salary = ''
  filters.experience_level = ''
  filters.sort_by = 'latest'
  applyFilters()
}

onMounted(() => {
  if (!jobsStore.categories.length) {
    jobsStore.fetchCategories()
  }
})
</script>

<template>
  <div class="filter-card">
    <div class="filter-card__title">
      <h3>Filters</h3>
    </div>
    <form @submit.prevent="applyFilters" class="form">
      <div class="form__field">
        <label>Keyword</label>
        <input v-model="filters.keyword" type="text" placeholder="e.g. Laravel" />
      </div>

      <div class="form__field">
        <label>Category</label>
        <select v-model="filters.category_id">
          <option value="">All Categories</option>
          <option v-for="cat in jobsStore.categories" :key="cat.id" :value="cat.id">
            {{ cat.name }}
          </option>
        </select>
      </div>

      <div class="form__field">
        <label>Location</label>
        <input v-model="filters.location" type="text" placeholder="City or Country" />
      </div>

      <div class="form__field">
        <label>Work Type</label>
        <select v-model="filters.work_type">
          <option value="">Any</option>
          <option value="remote">Remote</option>
          <option value="hybrid">Hybrid</option>
          <option value="onsite">On-site</option>
        </select>
      </div>

      <div class="form__field">
        <label>Experience</label>
        <select v-model="filters.experience_level">
          <option value="">Any</option>
          <option value="entry">Entry Level</option>
          <option value="mid">Mid Level</option>
          <option value="senior">Senior</option>
          <option value="director">Director</option>
        </select>
      </div>

      <div class="form__field">
        <label>Salary Range</label>
        <div class="salary-inputs">
          <input v-model="filters.min_salary" type="number" placeholder="Min" />
          <input v-model="filters.max_salary" type="number" placeholder="Max" />
        </div>
      </div>

      <div class="form__field">
        <label>Sort By</label>
        <select v-model="filters.sort_by">
          <option value="latest">Most Recent</option>
          <option value="oldest">Oldest</option>
          <option value="salary_desc">Salary (High to Low)</option>
          <option value="salary_asc">Salary (Low to High)</option>
        </select>
      </div>

      <div class="form__actions">
        <button type="submit" class="btn" :disabled="jobsStore.loading">Apply Filters</button>
        <button type="button" class="btn btn--ghost" @click="clearFilters">Clear</button>
      </div>
    </form>
  </div>
</template>
