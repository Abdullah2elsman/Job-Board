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
  <div class="layout" style="gap: 2rem;">
    <header class="flex-between">
      <div>
        <h1 style="margin: 0;">My Jobs</h1>
        <p class="muted">Manage your listings and track candidate applications</p>
      </div>
      <button
        class="btn btn--ghost"
        type="button"
        @click="jobsStore.fetchEmployerJobs"
        :disabled="isLoading"
      >
        {{ isLoading ? "Refreshing..." : "Refresh List" }}
      </button>
    </header>

    <div v-if="jobsStore.error" class="alert alert--error">
      {{ jobsStore.error }}
    </div>
    <div v-if="jobsStore.successMessage" class="alert alert--success">
      {{ jobsStore.successMessage }}
    </div>

    <div v-if="isLoading" class="loading">Loading your listings...</div>

    <div v-else class="job-list">
      <article v-for="job in jobsStore.jobs" :key="job.id" class="card" style="padding: 1.75rem;">
        <div v-if="editingId !== job.id">
          <div class="job-card__header" style="margin-bottom: 1.5rem;">
            <div style="flex: 1;">
              <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.5rem;">
                <h3 style="margin: 0;">{{ job.title }}</h3>
                <span :class="statusClass(job.status)" style="font-size: 0.7rem;">{{ job.status.toUpperCase() }}</span>
              </div>
              <p class="muted" style="font-size: 0.9rem;">
                Posted on {{ new Date(job.created_at).toLocaleDateString() }} • {{ job.location }} • {{ job.work_type }}
              </p>
            </div>
            <div class="flex-wrap" style="justify-content: flex-end; gap: 0.75rem;">
              <router-link :to="`/dashboard/employer/jobs/${job.id}/applications`" class="btn btn--ghost" style="font-size: 0.85rem;">
                Applications ({{ job.applications_count || 0 }})
              </router-link>
              <button class="btn btn--ghost" style="font-size: 0.85rem;" @click="startEdit(job)">Edit</button>
              <button class="btn btn--danger" style="font-size: 0.85rem; padding: 0.6rem 1rem;" @click="removeJob(job.id)">Delete</button>
            </div>
          </div>

          <div class="flex-wrap" style="gap: 0.5rem; margin-top: 1rem;">
            <span v-for="skill in job.skills" :key="skill.id" class="chip" style="font-size: 0.75rem;">
              {{ skill.skill_name }}
            </span>
          </div>
        </div>

        <!-- Edit Form -->
        <form v-else class="form" @submit.prevent="submitEdit(job.id)">
          <div class="form__grid" style="grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));">
            <div class="form__field">
              <label>Job Title</label>
              <input v-model="editForm.title" type="text" />
              <span v-if="errors.title" class="form__error">{{ errors.title }}</span>
            </div>

            <div class="form__field">
              <label>Category</label>
              <select v-model="editForm.category_id">
                <option value="" disabled>Select category</option>
                <option v-for="category in jobsStore.categories" :key="category.id" :value="category.id">
                  {{ category.name }}
                </option>
              </select>
              <span v-if="errors.category_id" class="form__error">{{ errors.category_id }}</span>
            </div>

            <div class="form__field form__field--full">
              <label>Description</label>
              <textarea v-model="editForm.description" rows="4"></textarea>
              <span v-if="errors.description" class="form__error">{{ errors.description }}</span>
            </div>

            <div class="form__field">
              <label>Salary (Annual)</label>
              <input v-model="editForm.salary" type="text" placeholder="e.g. 80000" />
              <span v-if="errors.salary" class="form__error">{{ errors.salary }}</span>
            </div>

            <div class="form__field">
              <label>Location</label>
              <input v-model="editForm.location" type="text" placeholder="City, State" />
              <span v-if="errors.location" class="form__error">{{ errors.location }}</span>
            </div>

            <div class="form__field">
              <label>Work Type</label>
              <select v-model="editForm.work_type">
                <option value="remote">Remote</option>
                <option value="onsite">Onsite</option>
                <option value="hybrid">Hybrid</option>
              </select>
            </div>

            <div class="form__field">
              <label>Deadline</label>
              <input v-model="editForm.deadline" type="date" />
            </div>

            <div class="form__field form__field--full">
              <label>Required Skills (Press Enter to add)</label>
              <div class="skills">
                <div class="skills__input">
                  <input v-model="skillInput" type="text" placeholder="Add a skill..." @keydown.enter.prevent="addSkill" />
                  <button type="button" class="btn btn--ghost" @click="addSkill">Add</button>
                </div>
                <div class="skills__list" style="margin-top: 0.75rem;">
                  <span v-for="skill in editForm.skills" :key="skill" class="chip">
                    {{ skill }}
                    <button type="button" @click="removeSkill(skill)" style="border: none; background: none; cursor: pointer; color: var(--danger); margin-left: 0.25rem;">×</button>
                  </span>
                </div>
              </div>
            </div>
          </div>

          <div class="form__actions" style="margin-top: 2rem;">
            <button class="btn btn--ghost" type="button" @click="cancelEdit">Cancel</button>
            <button class="btn" type="submit" :disabled="isLoading">
              {{ isLoading ? "Saving..." : "Update Listing" }}
            </button>
          </div>
        </form>
      </article>

      <div v-if="!jobsStore.jobs.length" class="card text-center" style="padding: 4rem 2rem;">
        <h3 class="muted">You haven't posted any jobs yet.</h3>
        <p class="muted">Start hiring by creating your first job listing.</p>
      </div>
    </div>
  </div>
</template>
