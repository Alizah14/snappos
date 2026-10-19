import { createRouter, createWebHistory } from 'vue-router'
import WelcomeView from '../views/WelcomeView.vue'
import RegisterStoreView from '../views/RegisterStoreView.vue'
import RegisterUserView from '../views/RegisterUserView.vue'
import LoginView from '../views/LoginView.vue'
import DashboardView from '../views/DashboardView.vue'
import ProductsView from '../views/ProductsView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    // 1. Jalur Awal -> Pendaftaran Toko
    {
      path: '/',
      redirect: '/welcome'
    },
    {
      path: '/welcome',
      name: 'welcome',
      component: WelcomeView
    },
    {
      path: '/register-store',
      name: 'register-store',
      component: RegisterStoreView
    },

    // 2. Jalur Registrasi User (Admin / Kasir)
    {
      path: '/register-user',
      name: 'register-user',
      component: RegisterUserView
    },

    // 3. Jalur Login
    {
      path: '/login',
      name: 'login',
      component: LoginView
    },

    // 4. Dashboard Khusus Admin
    {
      path: '/dashboard',
      name: 'dashboard',
      component: DashboardView,
      beforeEnter: (to, from, next) => {
        const userRole = localStorage.getItem('role')
        // Jika Kasir mencoba akses URL dashboard, lempar langsung ke katalog
        if (userRole === 'Kasir') {
          next('/products')
        } else {
          next()
        }
      }
    },

    // 5. Katalog Produk (Landing Kasir & Tempat Lihat Stok)
    {
      path: '/products',
      name: 'products',
      component: ProductsView
    },
    {
      path: '/produk',
      redirect: '/products'
    },

    // Fallback jika URL nyasar
    {
      path: '/:pathMatch(.*)*',
      redirect: '/welcome'
    }
  ]
})

export default router