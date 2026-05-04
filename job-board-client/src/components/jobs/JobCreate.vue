<script setup>
import { onMounted, reactive, ref, computed } from "vue";
import { useJobsStore } from "../../stores/jobs";

const jobsStore = useJobsStore();

const form = reactive({
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
const isSubmitting = computed(() => jobsStore.loading);

const resetForm = () => {
  form.title = "";
  form.description = "";
  form.category_id = "";
  form.salary = "";
  form.location = "";
  form.work_type = "remote";
  form.deadline = "";
  form.skills = [];
  skillInput.value = "";
  Object.keys(errors).forEach((key) => delete errors[key]);
};

const validate = () => {
  Object.keys(errors).forEach((key) => delete errors[key]);
  if (!form.title || form.title.length < 3)
    errors.title = "Title is required (min 3)";
  if (!form.description || form.description.length < 10)
    errors.description = "Description is required (min 10)";
  if (!form.category_id) errors.category_id = "Category is required";
  if (!form.location) errors.location = "Location is required";
  if (!form.work_type) errors.work_type = "Work type is required";
  if (form.salary && Number.isNaN(Number(form.salary)))
    errors.salary = "Salary must be a number";
  return Object.keys(errors).length === 0;
};

const addSkill = () => {
  const value = skillInput.value.trim();
  if (!value) return;
  if (!form.skills.includes(value)) {
    form.skills.push(value);
  }
  skillInput.value = "";
};

const removeSkill = (skill) => {
  form.skills = form.skills.filter((item) => item !== skill);
};

const submit = async () => {
  jobsStore.clearMessages();
  if (!validate()) return;

  const payload = {
    ...form,
    salary: form.salary === "" ? null : Number(form.salary),
    skills: form.skills,
  };

  try {
    const job = await jobsStore.createJob(payload);
    if (job) {
      resetForm();
    }
  } catch (error) {
    const apiErrors = error?.response?.data?.errors || null;
    if (apiErrors) {
      Object.entries(apiErrors).forEach(([key, value]) => {
        errors[key] = Array.isArray(value) ? value[0] : value;
      });
    }
  }
};

onMounted(async () => {
  if (!jobsStore.categories.length) {
    await jobsStore.fetchCategories();
  }
});
</script>

<template>
  <section class="card">
    <header class="card__header">
      <div>
        <h2>Create Job</h2>
        <p class="muted">Post a new job and wait for approval.</p>
      </div>
      <span v-if="jobsStore.successMessage" class="badge badge--success">{{
        jobsStore.successMessage
      }}</span>
    </header>

    <form class="form" @submit.prevent="submit">
      <div class="form__grid">
        <div class="form__field">
          <label>Title</label>
          <input v-model="form.title" type="text" placeholder="Job title" />
          <span v-if="errors.title" class="form__error">{{
            errors.title
          }}</span>
        </div>

        <div class="form__field">
          <label>Category</label>
          <select v-model="form.category_id">
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
          <textarea
            v-model="form.description"
            rows="4"
            placeholder="Job description"
          ></textarea>
          <span v-if="errors.description" class="form__error">{{
            errors.description
          }}</span>
        </div>

        <div class="form__field">
          <label>Salary</label>
          <input v-model="form.salary" type="text" placeholder="e.g. 5000" />
          <span v-if="errors.salary" class="form__error">{{
            errors.salary
          }}</span>
        </div>

        <div class="form__field">
          <label>Location</label>
          <input
            v-model="form.location"
            type="text"
            placeholder="City, Country"
          />
          <span v-if="errors.location" class="form__error">{{
            errors.location
          }}</span>
        </div>

        <div class="form__field">
          <label>Work Type</label>
          <select v-model="form.work_type">
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
          <input v-model="form.deadline" type="date" />
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
              <button type="button" class="btn btn--ghost" @click="addSkill">
                Add
              </button>
            </div>
            <div class="skills__list">
              <span v-for="skill in form.skills" :key="skill" class="chip">
                {{ skill }}
                <button type="button" @click="removeSkill(skill)">×</button>
              </span>
            </div>
          </div>
        </div>
      </div>

      <div class="form__actions">
        <span v-if="jobsStore.error" class="form__error">{{
          jobsStore.error
        }}</span>
        <button class="btn" type="submit" :disabled="isSubmitting">
          {{ isSubmitting ? "Posting..." : "Create Job" }}
        </button>
      </div>
    </form>
  </section>
</template>
