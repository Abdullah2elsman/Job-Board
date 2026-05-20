<script setup>
import { computed, onMounted, reactive, ref } from "vue";
import api from "../../services/api";
import { useJobsStore } from "../../stores/jobs";

const jobsStore = useJobsStore();

const activeTab = ref("overview");
const analyticsLoading = ref(false);
const analyticsData = ref(null);
const analyticsError = ref(null);

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

const createForm = reactive({
  title: "",
  description: "",
  responsibilities: "",
  requirements: "",
  category_id: "",
  salary: "",
  location: "",
  work_type: "remote",
  deadline: "",
  skills: [],
});

const errors = reactive({});
const createErrors = reactive({});
const skillInput = ref("");
const createSkillInput = ref("");
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

const addCreateSkill = () => {
  const value = createSkillInput.value.trim();
  if (!value) return;
  if (!createForm.skills.includes(value)) {
    createForm.skills.push(value);
  }
  createSkillInput.value = "";
};

const removeCreateSkill = (skill) => {
  createForm.skills = createForm.skills.filter((item) => item !== skill);
};

const validate = (form, errorObj) => {
  Object.keys(errorObj).forEach((key) => delete errorObj[key]);
  if (!form.title || form.title.length < 3)
    errorObj.title = "Title is required (min 3)";
  if (!form.description || form.description.length < 10)
    errorObj.description = "Description is required (min 10)";
  if (!form.category_id) errorObj.category_id = "Category is required";
  if (!form.location) errorObj.location = "Location is required";
  if (!form.work_type) errorObj.work_type = "Work type is required";
  if (form.salary && Number.isNaN(Number(form.salary)))
    errorObj.salary = "Salary must be a number";
  return Object.keys(errorObj).length === 0;
};

const submitEdit = async (jobId) => {
  jobsStore.clearMessages();
  if (!validate(editForm, errors)) return;

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

const submitCreate = async () => {
  jobsStore.clearMessages();
  if (!validate(createForm, createErrors)) return;

  const payload = {
    ...createForm,
    salary: createForm.salary === "" ? null : Number(createForm.salary),
    skills: createForm.skills,
  };

  try {
    await jobsStore.createJob(payload);
    // Reset form
    Object.keys(createForm).forEach((key) => {
      if (key === "skills") createForm[key] = [];
      else if (key === "work_type") createForm[key] = "remote";
      else createForm[key] = "";
    });
    createSkillInput.value = "";
    activeTab.value = "jobs";
  } catch (error) {
    const apiErrors = error?.response?.data?.errors || null;
    if (apiErrors) {
      Object.entries(apiErrors).forEach(([key, value]) => {
        createErrors[key] = Array.isArray(value) ? value[0] : value;
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

const fetchAnalytics = async () => {
  analyticsLoading.value = true;
  analyticsError.value = null;
  try {
    const { data } = await api.get("/api/analytics");
    analyticsData.value = data.data;
  } catch (error) {
    analyticsError.value =
      error?.response?.data?.message || "Failed to load analytics";
  } finally {
    analyticsLoading.value = false;
  }
};

onMounted(async () => {
  if (!jobsStore.categories.length) {
    await jobsStore.fetchCategories();
  }
  await jobsStore.fetchEmployerJobs();
  await fetchAnalytics();
});
</script>

<template>
  <div class="layout" style="gap: 2rem">
    <header class="flex-between">
      <div>
        <h1 style="margin: 0">Employer Dashboard</h1>
        <p class="muted">Manage your job listings and track performance</p>
      </div>
      <button
        class="btn btn--ghost"
        type="button"
        @click="
          jobsStore.fetchEmployerJobs();
          fetchAnalytics();
        "
        :disabled="isLoading"
      >
        {{ isLoading ? "Refreshing..." : "Refresh" }}
      </button>
    </header>

    <!-- Tabs -->
    <div class="tabs">
      <button
        class="tab"
        :class="{ 'tab--active': activeTab === 'overview' }"
        @click="activeTab = 'overview'"
      >
        Overview
      </button>
      <button
        class="tab"
        :class="{ 'tab--active': activeTab === 'jobs' }"
        @click="activeTab = 'jobs'"
      >
        My Jobs ({{ jobsStore.jobs.length }})
      </button>
      <button
        class="tab"
        :class="{ 'tab--active': activeTab === 'create' }"
        @click="activeTab = 'create'"
      >
        Create Job
      </button>
    </div>

    <!-- Overview Tab -->
    <div v-if="activeTab === 'overview'" class="overview-grid">
      <div v-if="analyticsLoading" class="loading">Loading analytics...</div>
      <div v-else-if="analyticsError" class="alert alert--error">
        {{ analyticsError }}
      </div>
      <div v-else-if="analyticsData">
        <div class="stats-grid">
          <div class="stat-card">
            <div class="stat-number">{{ analyticsData.totals.jobs }}</div>
            <div class="stat-label">Total Jobs</div>
          </div>
          <div class="stat-card">
            <div class="stat-number">
              {{ analyticsData.totals.approved_jobs }}
            </div>
            <div class="stat-label">Approved Jobs</div>
          </div>
          <div class="stat-card">
            <div class="stat-number">
              {{ analyticsData.totals.applications }}
            </div>
            <div class="stat-label">Total Applications</div>
          </div>
          <div class="stat-card">
            <div class="stat-number">{{ analyticsData.totals.job_views }}</div>
            <div class="stat-label">Job Views</div>
          </div>
          <div class="stat-card">
            <div class="stat-number">{{ analyticsData.totals.payments }}</div>
            <div class="stat-label">Payments</div>
          </div>
        </div>

        <div class="recent-jobs">
          <h3>Recent Jobs</h3>
          <div v-if="!analyticsData.jobs.length" class="text-center muted">
            No jobs posted yet.
          </div>
          <div v-else class="job-summary-list">
            <div
              v-for="job in analyticsData.jobs.slice(0, 5)"
              :key="job.id"
              class="job-summary"
            >
              <div class="job-summary__content">
                <h4>{{ job.title }}</h4>
                <p class="muted">
                  {{ job.location }} • {{ job.applications_count }} applications
                  • {{ job.views_count }} views
                </p>
              </div>
              <span
                :class="statusClass(job.status)"
                style="font-size: 0.7rem"
                >{{ job.status }}</span
              >
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Jobs Tab -->
    <div v-if="activeTab === 'jobs'">
      <div v-if="jobsStore.error" class="alert alert--error">
        {{ jobsStore.error }}
      </div>
      <div v-if="jobsStore.successMessage" class="alert alert--success">
        {{ jobsStore.successMessage }}
      </div>

      <div v-if="isLoading" class="loading">Loading your listings...</div>

      <div v-else class="job-list">
        <article
          v-for="job in jobsStore.jobs"
          :key="job.id"
          class="card job-card"
        >
          <div class="job-card__header">
            <div class="job-card__info">
              <h3>{{ job.title }}</h3>
              <p class="muted">
                {{ job.location }} • {{ job.work_type }} • Posted
                {{ new Date(job.created_at).toLocaleDateString() }}
              </p>
              <div class="job-card__meta">
                <span :class="statusClass(job.status)">{{ job.status }}</span>
                <span class="meta-item"
                  >{{ job.applications_count || 0 }} applications</span
                >
                <span class="meta-item">{{ job.views_count || 0 }} views</span>
              </div>
            </div>
            <div class="job-card__actions">
              <router-link
                :to="`/dashboard/employer/jobs/${job.id}/applications`"
                class="btn btn--outline"
              >
                View Applications
              </router-link>
              <button class="btn btn--ghost" @click="startEdit(job)">
                Edit
              </button>
              <button class="btn btn--danger" @click="removeJob(job.id)">
                Delete
              </button>
            </div>
          </div>

          <div class="job-card__skills">
            <span v-for="skill in job.skills" :key="skill.id" class="chip">
              {{ skill.skill_name }}
            </span>
          </div>
        </article>

        <div
          v-if="!jobsStore.jobs.length"
          class="card text-center"
          style="padding: 4rem 2rem"
        >
          <h3 class="muted">You haven't posted any jobs yet.</h3>
          <p class="muted">Start hiring by creating your first job listing.</p>
          <button class="btn" @click="activeTab = 'create'">
            Create Your First Job
          </button>
        </div>
      </div>
    </div>

    <!-- Create Job Tab -->
    <div v-if="activeTab === 'create'" class="create-job">
      <div class="card">
        <h3>Create New Job Listing</h3>
        <form class="form" @submit.prevent="submitCreate">
          <div class="form__grid">
            <div class="form__field">
              <label>Job Title *</label>
              <input
                v-model="createForm.title"
                type="text"
                placeholder="e.g. Senior Software Engineer"
              />
              <span v-if="createErrors.title" class="form__error">{{
                createErrors.title
              }}</span>
            </div>

            <div class="form__field">
              <label>Category *</label>
              <select v-model="createForm.category_id">
                <option value="" disabled>Select category</option>
                <option
                  v-for="category in jobsStore.categories"
                  :key="category.id"
                  :value="category.id"
                >
                  {{ category.name }}
                </option>
              </select>
              <span v-if="createErrors.category_id" class="form__error">{{
                createErrors.category_id
              }}</span>
            </div>

            <div class="form__field">
              <label>Location *</label>
              <input
                v-model="createForm.location"
                type="text"
                placeholder="City, State or Remote"
              />
              <span v-if="createErrors.location" class="form__error">{{
                createErrors.location
              }}</span>
            </div>

            <div class="form__field">
              <label>Work Type *</label>
              <select v-model="createForm.work_type">
                <option value="remote">Remote</option>
                <option value="onsite">Onsite</option>
                <option value="hybrid">Hybrid</option>
              </select>
            </div>

            <div class="form__field">
              <label>Salary (Annual)</label>
              <input
                v-model="createForm.salary"
                type="number"
                placeholder="80000"
              />
            </div>

            <div class="form__field">
              <label>Application Deadline</label>
              <input v-model="createForm.deadline" type="date" />
            </div>

            <div class="form__field form__field--full">
              <label>Job Description *</label>
              <textarea
                v-model="createForm.description"
                rows="4"
                placeholder="Describe the role, responsibilities, and what you're looking for..."
              ></textarea>
              <span v-if="createErrors.description" class="form__error">{{
                createErrors.description
              }}</span>
            </div>

            <div class="form__field form__field--full">
              <label>Responsibilities</label>
              <textarea
                v-model="createForm.responsibilities"
                rows="3"
                placeholder="Key responsibilities and duties..."
              ></textarea>
            </div>

            <div class="form__field form__field--full">
              <label>Requirements</label>
              <textarea
                v-model="createForm.requirements"
                rows="3"
                placeholder="Required skills, experience, qualifications..."
              ></textarea>
            </div>

            <div class="form__field form__field--full">
              <label>Required Skills</label>
              <div class="skills">
                <div class="skills__input">
                  <input
                    v-model="createSkillInput"
                    type="text"
                    placeholder="Add a skill..."
                    @keydown.enter.prevent="addCreateSkill"
                  />
                  <button
                    type="button"
                    class="btn btn--ghost"
                    @click="addCreateSkill"
                  >
                    Add
                  </button>
                </div>
                <div class="skills__list">
                  <span
                    v-for="skill in createForm.skills"
                    :key="skill"
                    class="chip"
                  >
                    {{ skill }}
                    <button type="button" @click="removeCreateSkill(skill)">
                      ×
                    </button>
                  </span>
                </div>
              </div>
            </div>
          </div>

          <div class="form__actions">
            <button class="btn" type="submit" :disabled="isLoading">
              {{ isLoading ? "Creating..." : "Create Job Listing" }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Edit Modal -->
    <div v-if="editingId" class="modal-overlay" @click="cancelEdit">
      <div class="modal" @click.stop>
        <div class="modal__header">
          <h3>Edit Job Listing</h3>
          <button class="modal__close" @click="cancelEdit">×</button>
        </div>
        <form class="form" @submit.prevent="submitEdit(editingId)">
          <div class="form__grid">
            <div class="form__field">
              <label>Job Title</label>
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
            </div>

            <div class="form__field">
              <label>Salary</label>
              <input v-model="editForm.salary" type="number" />
              <span v-if="errors.salary" class="form__error">{{
                errors.salary
              }}</span>
            </div>

            <div class="form__field">
              <label>Deadline</label>
              <input v-model="editForm.deadline" type="date" />
            </div>

            <div class="form__field form__field--full">
              <label>Description</label>
              <textarea v-model="editForm.description" rows="4"></textarea>
              <span v-if="errors.description" class="form__error">{{
                errors.description
              }}</span>
            </div>

            <div class="form__field form__field--full">
              <label>Skills</label>
              <div class="skills">
                <div class="skills__input">
                  <input
                    v-model="skillInput"
                    type="text"
                    placeholder="Add a skill..."
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
              {{ isLoading ? "Saving..." : "Update" }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>
