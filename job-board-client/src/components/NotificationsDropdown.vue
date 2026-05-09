<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import api from '../services/api'
import { useAuthStore } from '../stores/auth'
import '../assets/notifications.css'

const authStore = useAuthStore()
const notifications = ref([])
const isOpen = ref(false)
const unreadCount = ref(0)
let pollInterval = null

const fetchNotifications = async () => {
  if (!authStore.isAuthenticated) return
  
  try {
    const { data } = await api.get('/api/notifications')
    notifications.value = data?.data || []
    unreadCount.value = notifications.value.filter(n => !n.read_at).length
  } catch (err) {
    console.error('Failed to load notifications')
  }
}

const toggleDropdown = () => {
  isOpen.value = !isOpen.value
  if (isOpen.value && unreadCount.value > 0) {
    markAllAsRead()
  }
}

const markAllAsRead = async () => {
  const unread = notifications.value.filter(n => !n.read_at)
  for (const n of unread) {
    try {
      await api.put(`/api/notifications/${n.id}/read`)
      n.read_at = new Date().toISOString()
    } catch (err) {}
  }
  unreadCount.value = 0
}

onMounted(() => {
  fetchNotifications()
  pollInterval = setInterval(fetchNotifications, 60000) // Poll every minute
})

onUnmounted(() => {
  if (pollInterval) clearInterval(pollInterval)
})
</script>

<template>
  <div class="notifications-dropdown" v-if="authStore.isAuthenticated">
    <button class="notifications-btn" @click="toggleDropdown">
      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
      </svg>
      <span v-if="unreadCount > 0" class="badge-count">{{ unreadCount }}</span>
    </button>
    
    <div v-if="isOpen" class="dropdown-menu">
      <div class="dropdown-header">
        <h4>Notifications</h4>
      </div>
      <div class="dropdown-content">
        <div v-if="notifications.length === 0" class="empty-state">
          No notifications yet.
        </div>
        <div v-else v-for="notif in notifications" :key="notif.id" class="notification-item" :class="{ 'unread': !notif.read_at }">
          <strong>{{ notif.data.title }}</strong>
          <p>{{ notif.data.message }}</p>
          <span class="time">{{ new Date(notif.created_at).toLocaleString() }}</span>
        </div>
      </div>
    </div>
  </div>
</template>
