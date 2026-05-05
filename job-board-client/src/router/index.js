import { createRouter, createWebHistory } from 'vue-router'

const routes = [
  {
    path: '/',
    name: 'Home',
    component: () => import('../views/Home.vue')
  },
  {
    path: '/login',
    name: 'Login',
    component: () => import('../views/LoginView.vue')
  },
  {
    path: '/register',
    name: 'Register',
    component: () => import('../views/RegisterView.vue')
  },
  {
    path: '/jobs/:id',
    name: 'JobDetail',
    component: () => import('../views/JobDetail.vue')
  },
  {
    path: '/dashboard/employer',
    name: 'EmployerDashboard',
    component: () => import('../components/jobs/EmployerDashboard.vue'),
    meta: { requiresAuth: true, role: 'employer' }
  },
  {
    path: '/dashboard/employer/profile',
    name: 'EmployerEditProfile',
    component: () => import('../views/employer/EditProfile.vue'),
    meta: { requiresAuth: true, role: 'employer' }
  },
  {
    path: '/dashboard/employer/jobs/:jobId/applications',
    name: 'ManageApplications',
    component: () => import('../views/employer/ManageApplications.vue'),
    meta: { requiresAuth: true, role: 'employer' }
  },
  {
    path: '/dashboard/candidate',
    name: 'CandidateDashboard',
    component: () => import('../views/candidate/MyApplications.vue'),
    meta: { requiresAuth: true, role: 'candidate' }
  },
  {
    path: '/dashboard/candidate/profile',
    name: 'CandidateEditProfile',
    component: () => import('../views/candidate/EditProfile.vue'),
    meta: { requiresAuth: true, role: 'candidate' }
  },
  {
    path: '/dashboard/admin',
    name: 'AdminDashboard',
    component: () => import('../views/admin/AdminDashboard.vue'),
    meta: { requiresAuth: true, role: 'admin' }
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token')
  const userStr = localStorage.getItem('user')
  const user = userStr ? JSON.parse(userStr) : null
  const role = user?.role || null

  if (to.meta.requiresAuth && !token) {
    next({ name: 'Login' })
  } else if (to.meta.role && to.meta.role !== role) {
    next({ name: 'Home' })
  } else {
    next()
  }
})

export default router
