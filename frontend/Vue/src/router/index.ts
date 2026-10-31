import { createRouter, createWebHistory } from 'vue-router'
import SuperAdminLayout from '../layouts/SuperAdminLayout.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),

  scrollBehavior() {
    return { top: 0 }
  },

  routes: [
    {
      path: '/',
      redirect: '/login',
    },
    {
      path: '/login',
      name: 'login',
      component: () => import('../views/auth/LoginView.vue'),
    },
    {
      path: '/superadmin',
      component: SuperAdminLayout,
      children: [
        {
          path: '',
          redirect: '/superadmin/dashboard',
        },
        {
          path: 'dashboard',
          name: 'superadmin-dashboard',
          component: () => import('../views/superadmin/DashboardView.vue'),
        },
        {
          path: 'businesses',
          name: 'superadmin-businesses',
          component: () => import('../views/superadmin/BusinessesView.vue'),
        },
        {
          path: 'businesses/create',
          name: 'superadmin-business-create',
          component: () => import('../views/superadmin/CreateBusinessView.vue'),
        },
        {
          path: 'businesses/:id/edit',
          name: 'superadmin-business-edit',
          component: () => import('../views/superadmin/EditBusinessView.vue'),
        },
      ],
    },
  ],
})

export default router
