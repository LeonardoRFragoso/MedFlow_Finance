import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const routes = [
  {
    path: '/',
    name: 'Landing',
    component: () => import('@/pages/Landing.vue'),
    meta: { requiresAuth: false, isPublic: true },
  },
  {
    path: '/login',
    name: 'Login',
    component: () => import('@/pages/Login.vue'),
    meta: { requiresAuth: false },
  },
  {
    path: '/register',
    name: 'Register',
    component: () => import('@/pages/Register.vue'),
    meta: { requiresAuth: false },
  },
  {
    path: '/app',
    name: 'Dashboard',
    component: () => import('@/pages/Dashboard.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/uploads',
    name: 'Uploads',
    component: () => import('@/pages/Uploads.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/uploads/:id',
    name: 'UploadDetail',
    component: () => import('@/pages/UploadDetail.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/records',
    name: 'Records',
    component: () => import('@/pages/Records.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/reports',
    name: 'Reports',
    component: () => import('@/pages/Reports.vue'),
    meta: { requiresAuth: true },
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()
  authStore.loadFromStorage()

  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next('/login')
  } else if ((to.path === '/login' || to.path === '/register') && authStore.isAuthenticated) {
    next('/app')
  } else {
    next()
  }
})

export default router
