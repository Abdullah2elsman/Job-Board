<script setup>
import { ref } from 'vue'
import api from '../../services/api'

const keyword = ref('')
const location = ref('')
const skill = ref('')
const candidates = ref([])
const loading = ref(false)
const searched = ref(false)

const handleSearch = async () => {
  loading.value = true
  searched.value = true
  try {
    const params = new URLSearchParams()
    if (keyword.value) params.append('keyword', keyword.value)
    if (location.value) params.append('location', location.value)
    if (skill.value) params.append('skill', skill.value)

    const { data } = await api.get(`/api/candidates/search?${params.toString()}`)
    candidates.value = data?.data || []
  } catch (err) {
    console.error('Failed to search candidates', err)
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="layout" style="gap: 2rem;">
    <section class="card" style="padding: 2.5rem;">
      <header style="margin-bottom: 2rem;">
        <h2 style="font-size: 1.75rem; margin-bottom: 0.5rem;">Talent Database</h2>
        <p class="muted">Search through thousands of qualified candidates for your next role</p>
      </header>
      
      <form @submit.prevent="handleSearch" class="form">
        <div class="form__grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
          <div class="form__field">
            <label>Role / Keyword</label>
            <input v-model="keyword" type="text" placeholder="e.g. Backend Developer" />
          </div>
          <div class="form__field">
            <label>Location</label>
            <input v-model="location" type="text" placeholder="e.g. Remote, Cairo" />
          </div>
          <div class="form__field">
            <label>Specific Skill</label>
            <input v-model="skill" type="text" placeholder="e.g. Python, Vue" />
          </div>
        </div>
        <div class="form__actions" style="margin-top: 1.5rem;">
          <button type="submit" class="btn" style="padding: 0.75rem 2rem;" :disabled="loading">
            {{ loading ? 'Searching Database...' : 'Search Candidates' }}
          </button>
        </div>
      </form>
    </section>

    <div v-if="loading" class="loading">Searching talent pool...</div>

    <section v-else-if="searched && candidates.length === 0" class="card text-center" style="padding: 4rem 2rem;">
      <h3 class="muted">No candidates found matching your filters</h3>
      <p class="muted">Try broading your search criteria or checking different skills.</p>
    </section>

    <div v-else-if="candidates.length > 0" class="job-list">
      <article v-for="candidate in candidates" :key="candidate.id" class="card" style="padding: 2rem;">
        <div class="flex-between" style="align-items: flex-start; margin-bottom: 1.5rem;">
          <div style="display: flex; gap: 1.25rem; align-items: center;">
            <div style="width: 56px; height: 56px; border-radius: 12px; background: var(--primary); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1.5rem;">
              {{ candidate.name.charAt(0).toUpperCase() }}
            </div>
            <div>
              <h3 style="margin: 0; font-size: 1.35rem;">{{ candidate.name }}</h3>
              <p class="muted" style="margin-top: 0.25rem;">{{ candidate.location || 'Location not specified' }}</p>
            </div>
          </div>
          <a :href="`mailto:${candidate.email}`" class="btn" style="font-size: 0.85rem; padding: 0.6rem 1.25rem;">Contact Candidate</a>
        </div>
        
        <p v-if="candidate.bio" style="margin-bottom: 1.5rem; color: var(--text-secondary); line-height: 1.6;">{{ candidate.bio }}</p>
        
        <div v-if="candidate.skills && candidate.skills.length" class="flex-wrap" style="gap: 0.5rem; margin-bottom: 1.5rem;">
          <span v-for="(sk, idx) in candidate.skills" :key="idx" class="chip" style="font-size: 0.8rem;">{{ sk }}</span>
        </div>
        
        <div class="flex-wrap" style="gap: 1rem; border-top: 1px solid var(--border-color); padding-top: 1.5rem;">
          <a v-if="candidate.resume_path" :href="`http://127.0.0.1:8000/storage/${candidate.resume_path}`" target="_blank" class="btn btn--ghost" style="font-size: 0.85rem;">
            📄 View Resume
          </a>
          <a v-if="candidate.linkedin_url" :href="candidate.linkedin_url" target="_blank" class="btn btn--ghost" style="font-size: 0.85rem;">
            🔗 LinkedIn
          </a>
        </div>
      </article>
    </div>
  </div>
</template>
