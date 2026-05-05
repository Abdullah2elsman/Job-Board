import { defineStore } from 'pinia'
import api from '../services/api'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: JSON.parse(localStorage.getItem('user')) || null,
    token: localStorage.getItem('token') || null,
    loading: false,
    error: null,
  }),
  getters: {
    isAuthenticated: (state) => !!state.token,
    userRole: (state) => state.user?.role || null,
    isAdmin: (state) => state.user?.role === 'admin',
    isEmployer: (state) => state.user?.role === 'employer',
    isCandidate: (state) => state.user?.role === 'candidate',
  },
  actions: {
    async login(credentials) {
      this.loading = true
      this.error = null
      try {
        const { data } = await api.post('/api/login', credentials)
        
        // Mock role extraction since it's an array or string
        let roleStr = data.role
        if (Array.isArray(roleStr)) {
            roleStr = roleStr[0]
        }
        const userWithRole = { ...data.user, role: roleStr }

        this.token = data.access_token
        this.user = userWithRole
        localStorage.setItem('token', this.token)
        localStorage.setItem('user', JSON.stringify(this.user))
        return true
      } catch (error) {
        this.error = error.response?.data?.message || 'Login failed'
        return false
      } finally {
        this.loading = false
      }
    },
    async register(credentials) {
      this.loading = true
      this.error = null
      try {
        const { data } = await api.post('/api/register', credentials)
        
        // Mock role extraction since it's an array or string
        let roleStr = data.role
        if (Array.isArray(roleStr)) {
            roleStr = roleStr[0]
        }
        const userWithRole = { ...data.user, role: roleStr }

        this.token = data.access_token
        this.user = userWithRole
        localStorage.setItem('token', this.token)
        localStorage.setItem('user', JSON.stringify(this.user))
        return true
      } catch (error) {
        this.error = error.response?.data?.message || 'Registration failed'
        return false
      } finally {
        this.loading = false
      }
    },
    logout() {
      // Opt-in background request
      api.post('/api/logout').catch(() => {})
      
      this.token = null
      this.user = null
      localStorage.removeItem('token')
      localStorage.removeItem('user')
    }
  }
})
