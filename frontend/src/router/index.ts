import { createRouter, createWebHistory, type RouterHistory } from 'vue-router'
import HomeView from '@/views/HomeView.vue'
import LoginView from '@/views/LoginView.vue'
import { useAuthStore } from '@/stores/auth'

export function createAppRouter(history: RouterHistory = createWebHistory()) {
  const router = createRouter({
    history,
    routes: [
      { path: '/', name: 'home', component: HomeView, meta: { requiresAuth: true } },
      { path: '/dashboard', redirect: '/' },
      { path: '/login', name: 'login', component: LoginView, meta: { requiresGuest: true } },
    ],
  })

  router.beforeEach((to) => {
    const auth = useAuthStore()
    if (to.meta.requiresAuth && !auth.isAuthenticated) return { name: 'login' }
    if (to.meta.requiresGuest && auth.isAuthenticated) return { name: 'home' }
  })

  return router
}

const router = createAppRouter()

export default router
