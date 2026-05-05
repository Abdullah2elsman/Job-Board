import { defineStore } from "pinia";
import api from "../services/api";

export const useJobsStore = defineStore("jobs", {
  state: () => ({
    jobs: [],
    categories: [],
    loading: false,
    error: null,
    successMessage: null,
    pagination: null,
  }),
  actions: {
    async fetchCategories() {
      try {
        const { data } = await api.get("/api/categories");
        this.categories = data?.data || data || [];
      } catch (error) {
        this.error =
          error?.response?.data?.message || "Failed to load categories";
      }
    },
    async fetchEmployerJobs() {
      this.loading = true;
      this.error = null;
      try {
        const { data } = await api.get("/api/jobs", {
          params: { mine: 1 },
        });
        const payload = data?.data || data;
        this.jobs = payload?.data || payload || [];
        this.pagination = payload?.meta || null;
      } catch (error) {
        this.error = error?.response?.data?.message || "Failed to load jobs";
      } finally {
        this.loading = false;
      }
    },
    async fetchJobs(filters = {}) {
      this.loading = true;
      this.error = null;
      try {
        const { data } = await api.get("/api/jobs/search", { params: filters });
        const payload = data?.data || data;
        this.jobs = payload?.data || payload || [];
        this.pagination = payload?.meta || null;
      } catch (error) {
        this.error = error?.response?.data?.message || "Failed to load jobs";
      } finally {
        this.loading = false;
      }
    },
    async createJob(payload) {
      this.loading = true;
      this.error = null;
      this.successMessage = null;
      try {
        const { data } = await api.post("/api/jobs", payload);
        const job = data?.data || data?.job || data;
        if (job) {
          this.jobs = [job, ...this.jobs];
        }
        this.successMessage = data?.message || "Job created successfully";
        return job;
      } catch (error) {
        this.error = error?.response?.data?.message || "Failed to create job";
        throw error;
      } finally {
        this.loading = false;
      }
    },
    async updateJob(id, payload) {
      this.loading = true;
      this.error = null;
      this.successMessage = null;
      try {
        const { data } = await api.put(`/api/jobs/${id}`, payload);
        const job = data?.data || data?.job || data;
        this.jobs = this.jobs.map((item) => (item.id === id ? job : item));
        this.successMessage = data?.message || "Job updated successfully";
        return job;
      } catch (error) {
        this.error = error?.response?.data?.message || "Failed to update job";
        throw error;
      } finally {
        this.loading = false;
      }
    },
    async deleteJob(id) {
      this.loading = true;
      this.error = null;
      this.successMessage = null;
      try {
        const { data } = await api.delete(`/api/jobs/${id}`);
        this.jobs = this.jobs.filter((item) => item.id !== id);
        this.successMessage = data?.message || "Job deleted successfully";
      } catch (error) {
        this.error = error?.response?.data?.message || "Failed to delete job";
        throw error;
      } finally {
        this.loading = false;
      }
    },
    clearMessages() {
      this.error = null;
      this.successMessage = null;
    },
  },
});
