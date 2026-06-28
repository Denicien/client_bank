import { createRouter, createWebHistory } from 'vue-router'
import Login from '../components/Login.vue'
import AddClient from '../components/AddClient.vue'
import ClientList from '../components/ClientList.vue'
import Dashboard from '../components/Dashboard.vue'

const routes = [
  { path: '/', redirect: '/login' },
  { path: '/login', component: Login },
  { path: '/add', component: AddClient, meta: { requiresAuth: true } },
  { path: '/clients', component: ClientList, meta: { requiresAuth: true } },
  { path: '/dashboard', component: Dashboard, meta: { requiresAuth: true } }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach((to, from, next) => {
  const isAuth = localStorage.getItem('auth') === 'true'
  
  if (to.meta.requiresAuth && !isAuth) {
    next('/login')
  } else if (to.path === '/login' && isAuth) {
    next('/clients')
  } else {
    next()
  }
})

export default router   // ← IMPORTANT : exporter router