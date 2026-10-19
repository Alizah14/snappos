<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'

const router = useRouter()
const route = useRoute()

defineProps({
  storeName: {
    type: String,
    default: 'Lancar barokah'
  }
})

const activeMenu = ref('Dashboard')

onMounted(() => {
  if (route.path.includes('/produk') || route.path.includes('/products')) {
    activeMenu.value = 'Katalog'
  } else if (route.path.includes('/laporan')) {
    activeMenu.value = 'Laporan'
  } else if (route.path.includes('/langganan') || route.path.includes('/pelanggan')) {
    activeMenu.value = 'Langganan'
  } else {
    activeMenu.value = 'Dashboard'
  }
})

const navigateTo = (menu, path) => {
  activeMenu.value = menu
  router.push(path)
}

const handleLogout = () => {
  localStorage.removeItem('role')
  localStorage.removeItem('username')
  router.push('/login')
}
</script>

<template>
  <header class="sticky top-0 z-50 bg-white border-b border-gray-200/80 shadow-[0_12px_25px_-4px_rgba(0,0,0,0.14),0_6px_12px_-4px_rgba(0,0,0,0.08)] transition-all">
    <div class="w-full px-4 sm:px-6 py-3.5 flex items-center justify-between">
      
      <!-- LOGO & NAMA TOKO -->
      <div class="flex items-center gap-3">
        <img 
          src="../assets/pos.png" 
          alt="SnapPOS" 
          class="h-12 sm:h-14 w-auto mix-blend-multiply object-contain cursor-pointer"
          @click="navigateTo('Dashboard', '/dashboard')"
        />
        <div class="flex items-center gap-2 bg-[#f1f5f9] px-3.5 py-1.5 rounded-full border border-gray-200/80">
          <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
          <span class="text-xs font-semibold text-gray-700">{{ storeName }}</span>
        </div>
      </div>

      <!-- MENU ADMIN -->
      <div class="flex items-center gap-2.5 ml-auto">
        <nav class="flex items-center gap-1 bg-[#f1f5f9] p-1.5 rounded-2xl border border-gray-200/80 shadow-inner">
          
          <button 
            @click="navigateTo('Dashboard', '/dashboard')"
            :class="activeMenu === 'Dashboard' ? 'bg-[#295C93] text-white font-semibold shadow-md' : 'text-gray-600 font-semibold hover:text-[#295C93] hover:bg-white/80'"
            class="px-4 py-2 rounded-xl text-xs transition-all duration-200"
          >
            Dashboard
          </button>

          <!-- MENU PRODUK DIGANTI MENJADI KATALOG -->
          <button 
            @click="navigateTo('Katalog', '/products')"
            :class="activeMenu === 'Katalog' ? 'bg-[#295C93] text-white font-semibold shadow-md' : 'text-gray-600 font-semibold hover:text-[#295C93] hover:bg-white/80'"
            class="px-4 py-2 rounded-xl text-xs transition-all duration-200"
          >
            Katalog
          </button>

          <button 
            @click="navigateTo('Laporan', '/laporan')"
            :class="activeMenu === 'Laporan' ? 'bg-[#295C93] text-white font-semibold shadow-md' : 'text-gray-600 font-semibold hover:text-[#295C93] hover:bg-white/80'"
            class="px-4 py-2 rounded-xl text-xs transition-all duration-200"
          >
            Laporan Penjualan
          </button>

          <button 
            @click="navigateTo('Langganan', '/langganan')"
            :class="activeMenu === 'Langganan' ? 'bg-[#295C93] text-white font-semibold shadow-md' : 'text-gray-600 font-semibold hover:text-[#295C93] hover:bg-white/80'"
            class="px-4 py-2 rounded-xl text-xs transition-all duration-200"
          >
            Langganan
          </button>

        </nav>

        <!-- Tombol Mode POS Kasir -->
        <button 
          @click="navigateTo('POS', '/pos')"
          class="bg-gradient-to-r from-[#080C14] via-[#295C93] to-[#4DA6FF] text-white px-4 py-2.5 rounded-xl text-xs font-semibold shadow-[0_6px_18px_rgba(41,92,147,0.35)] hover:opacity-95 hover:scale-[0.98] transition-all flex items-center justify-center ml-1"
        >
          <span>Mode POS (Kasir)</span>
        </button>

        <!-- Tombol Logout -->
        <button 
          @click="handleLogout"
          class="bg-red-50 hover:bg-red-100 text-red-600 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all ml-1 border border-red-100"
        >
          Keluar
        </button>
      </div>

    </div>
  </header>
</template>