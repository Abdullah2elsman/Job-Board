<script setup>
import { computed, onMounted, reactive, ref } from "vue";
import { useJobsStore } from "../../stores/jobs";

const jobsStore = useJobsStore();

const editingId = ref(null);
const editForm = reactive({
  title: "",
  description: "",
  category_id: "",
  salary: "",
  location: "",
  work_type: "remote",
  deadline: "",
  skills: [],
});

const errors = reactive({});
const skillInput = ref("");
const isLoading = computed(() => jobsStore.loading);

const startEdit = (job) => {
  editingId.value = job.id;
  editForm.title = job.title || "";
  editForm.description = job.description || "";
  editForm.category_id = job.category_id || "";
  editForm.salary = job.salary ?? "";
  editForm.location = job.location || "";
  editForm.work_type = job.work_type || "remote";
  editForm.deadline = job.deadline ? job.deadline.split("T")[0] : "";
  editForm.skills = (job.skills || []).map(
    (skill) => skill.skill_name || skill,
  );
  skillInput.value = "";
  Object.keys(errors).forEach((key) => delete errors[key]);
};

const cancelEdit = () => {
  editingId.value = null;
  Object.keys(errors).forEach((key) => delete errors[key]);
};

const addSkill = () => {
  const value = skillInput.value.trim();
  if (!value) return;
  if (!editForm.skills.includes(value)) {
    editForm.skills.push(value);
  }
  skillInput.value = "";
};

const removeSkill = (skill) => {
  editForm.skills = editForm.skills.filter((item) => item !== skill);
};

const validate = () => {
  Object.keys(errors).forEach((key) => delete errors[key]);
  if (!editForm.title || editForm.title.length < 3)
    errors.title = "Title is required (min 3)";
  if (!editForm.description || editForm.description.length < 10)
    errors.description = "Description is required (min 10)";
  if (!editForm.category_id) errors.category_id = "Category is required";
  if (!editForm.location) errors.location = "Location is required";
  if (!editForm.work_type) errors.work_type = "Work type is required";
  if (editForm.salary && Number.isNaN(Number(editForm.salary)))
    errors.salary = "Salary must be a number";
  return Object.keys(errors).length === 0;
};

const submitEdit = async (jobId) => {
  jobsStore.clearMessages();
  if (!validate()) return;

  const payload = {
    ...editForm,
    salary: editForm.salary === "" ? null : Number(editForm.salary),
    skills: editForm.skills,
  };

  try {
    await jobsStore.updateJob(jobId, payload);
    editingId.value = null;
  } catch (error) {
    const apiErrors = error?.response?.data?.errors || null;
    if (apiErrors) {
      Object.entries(apiErrors).forEach(([key, value]) => {
        errors[key] = Array.isArray(value) ? value[0] : value;
      });
    }
  }
};

const removeJob = async (jobId) => {
  if (!window.confirm("Are you sure you want to delete this job?")) return;
  jobsStore.clearMessages();
  await jobsStore.deleteJob(jobId);
};

const statusClass = (status) => {
  if (status === "approved") return "badge badge--success";
  if (status === "rejected") return "badge badge--danger";
  return "badge badge--warning";
};

onMounted(async () => {
  if (!jobsStore.categories.length) {
    await jobsStore.fetchCategories();
  }
  await jobsStore.fetchEmployerJobs();
});
</script>

<template>
  <section class="card">
    <header class="card__header">
      <div>
        <h2>Employer Dashboard</h2>
        <p class="muted">Track your posted jobs and their approval status.</p>
      </div>
      <button
        class="btn btn--ghost"
        type="button"
        @click="jobsStore.fetchEmployerJobs"
        :disabled="isLoading"
      >
        {{ isLoading ? "Refreshing..." : "Refresh" }}
      </button>
    </header>

    <div v-if="jobsStore.error" class="alert alert--error">
      {{ jobsStore.error }}
    </div>
    <div v-if="jobsStore.successMessage" class="alert alert--success">
      {{ jobsStore.successMessage }}
    </div>

    <div v-if="isLoading" class="loading">Loading jobs...</div>

    <div v-else class="job-list">
      <article v-for="job in jobsStore.jobs" :key="job.id" class="job-card">
        <div class="job-card__header">
          <div>
            <h3>{{ job.title }}</h3>
            <p class="muted">
              Created: {{ new Date(job.created_at).toLocaleDateString() }}
            </p>
          </div>
          <span :class="statusClass(job.status)">{{ job.status }}</span>
        </div>

        <div v-if="editingId !== job.id" class="job-card__actions">
          <router-link :to="`/dashboard/employer/jobs/${job.id}/applications`" class="btn btn--ghost">View Applications</router-link>
          <button class="btn btn--ghost" type="button" @click="startEdit(job)">
            Edit
          </button>
          <button
            class="btn btn--danger"
            type="button"
            @click="removeJob(job.id)"
          >
            Delete
          </button>
        </div>

        <form v-else class="form" @submit.prevent="submitEdit(job.id)">
          <div class="form__grid">
            <div class="form__field">
              <label>Title</label>
              <input v-model="editForm.title" type="text" />
              <span v-if="errors.title" class="form__error">{{
                errors.title
              }}</span>
            </div>

            <div class="form__field">
              <label>Category</label>
              <select v-model="editForm.category_id">
                <option value="" disabled>Select category</option>
                <option
                  v-for="category in jobsStore.categories"
                  :key="category.id"
                  :value="category.id"
                >
                  {{ category.name }}
                </option>
              </select>
              <span v-if="errors.category_id" class="form__error">{{
                errors.category_id
              }}</span>
            </div>

            <div class="form__field form__field--full">
              <label>Description</label>
              <textarea v-model="editForm.description" rows="3"></textarea>
              <span v-if="errors.description" class="form__error">{{
                errors.description
              }}</span>
            </div>

            <div class="form__field">
              <label>Salary</label>
              <input v-model="editForm.salary" type="text" />
              <span v-if="errors.salary" class="form__error">{{
                errors.salary
              }}</span>
            </div>

            <div class="form__field">
              <label>Location</label>
              <input v-model="editForm.location" type="text" />
              <span v-if="errors.location" class="form__error">{{
                errors.location
              }}</span>
            </div>

            <div class="form__field">
              <label>Work Type</label>
              <select v-model="editForm.work_type">
                <option value="remote">Remote</option>
                <option value="onsite">Onsite</option>
                <option value="hybrid">Hybrid</option>
              </select>
              <span v-if="errors.work_type" class="form__error">{{
                errors.work_type
              }}</span>
            </div>

            <div class="form__field">
              <label>Deadline</label>
              <input v-model="editForm.deadline" type="date" />
            </div>

            <div class="form__field form__field--full">
              <label>Skills</label>
              <div class="skills">
                <div class="skills__input">
                  <input
                    v-model="skillInput"
                    type="text"
                    placeholder="Add skill"
                    @keydown.enter.prevent="addSkill"
                  />
                  <button
                    type="button"
                    class="btn btn--ghost"
                    @click="addSkill"
                  >
                    Add
                  </button>
                </div>
                <div class="skills__list">
                  <span
                    v-for="skill in editForm.skills"
                    :key="skill"
                    class="chip"
                  >
                    {{ skill }}
                    <button type="button" @click="removeSkill(skill)">×</button>
                  </span>
                </div>
              </div>
            </div>
          </div>

          <div class="form__actions">
            <button class="btn btn--ghost" type="button" @click="cancelEdit">
              Cancel
            </button>
            <button class="btn" type="submit" :disabled="isLoading">
              {{ isLoading ? "Saving..." : "Save Changes" }}
            </button>
          </div>
        </form>
      </article>

      <p v-if="!jobsStore.jobs.length" class="muted">
        No jobs yet. Create your first job.
      </p>
    </div>
  </section>
</template>
