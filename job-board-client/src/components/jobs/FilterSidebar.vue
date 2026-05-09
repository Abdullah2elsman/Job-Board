<script setup>
import { reactive, watch, onMounted } from 'vue'
import { useJobsStore } from '../../stores/jobs'

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

<style scoped>
.filter-card {
  background: rgba(255, 255, 255, 0.9);
  -webkit-backdrop-filter: blur(8px);
  backdrop-filter: blur(8px);
  border: 1px solid rgba(255, 255, 255, 0.6);
  border-radius: var(--radius-md);
  box-shadow: var(--shadow-sm);
  padding: 1.5rem 1.5rem 1.25rem;
  margin-bottom: 2rem;
}

.filter-card__title {
  margin-bottom: 1.25rem;
}

.filter-card__title h3 {
  margin: 0;
  font-size: 0.85rem;
  color: var(--text-muted);
  text-transform: uppercase;
  letter-spacing: 0.08em;
  font-weight: 700;
}

/* Override global .form flex with grid */
.filter-card .form {
  display: grid !important;
  flex-direction: unset !important;
  grid-template-columns: repeat(3, 1fr);
  gap: 1rem 1.5rem;
  align-items: end;
}

.filter-card .form__field {
  display: flex;
  flex-direction: column;
  gap: 0.35rem;
  min-width: 0; /* prevent overflow */
}

.filter-card .form__field label {
  font-size: 0.75rem;
  font-weight: 700;
  color: var(--text-muted);
  text-transform: uppercase;
  letter-spacing: 0.06em;
  white-space: nowrap;
}

.filter-card .form__field input,
.filter-card .form__field select {
  height: 40px;
  padding: 0 0.85rem;
  font-size: 0.9rem;
  width: 100%;
  box-sizing: border-box;
}

/* Salary range: two inputs side by side inside one grid cell */
.salary-inputs {
  display: flex;
  gap: 0.5rem;
}

.salary-inputs input {
  flex: 1;
  min-width: 0;
  height: 40px;
  padding: 0 0.75rem;
  font-size: 0.9rem;
  box-sizing: border-box;
}

/* Actions row spans full width */
.form__actions {
  grid-column: 1 / -1;
  display: flex;
  gap: 0.75rem;
  justify-content: flex-end;
  margin-top: 0.25rem;
}

.form__actions .btn {
  height: 40px;
  padding: 0 1.5rem;
  font-size: 0.9rem;
}

@media (max-width: 900px) {
  .filter-card .form {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 540px) {
  .filter-card .form {
    grid-template-columns: 1fr;
  }
}
</style>
