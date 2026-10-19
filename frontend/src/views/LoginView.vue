<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

const username = ref('')
const password = ref('')
const selectedRole = ref('Admin') // Default pilihan Role: 'Admin' atau 'Kasir'
const rememberMe = ref(false)
const showPassword = ref(false)

const isLoading = ref(false)
const errorMessage = ref('')
const successMessage = ref('')

const handleLogin = async () => {
  if (!username.value.trim() || !password.value.trim()) {
    errorMessage.value = 'Mohon isi username dan kata sandi!'
    return
  }

  isLoading.value = true
  errorMessage.value = ''
  successMessage.value = ''

  try {
    const response = await fetch('http://localhost/snappos-api/login.php', {
      method: 'POST',
      headers: { 
        'Content-Type': 'application/json' 
      },
      body: JSON.stringify({
        username: username.value,
        password: password.value,
        role: selectedRole.value
      })
    })

    const result = await response.json()

    if (result.status === 'success') {
      successMessage.value = 'Login berhasil! Mengalihkan...'

      // Simpan data login dan role ke localStorage
      localStorage.setItem('username', result.user.username)
      localStorage.setItem('nama_lengkap', result.user.nama_lengkap)
      localStorage.setItem('role', result.user.role)
      if (result.user.id) {
        localStorage.setItem('user_id', result.user.id)
      }

      // Pengarahan halaman sesuai Role
      setTimeout(() => {
        if (result.user.role === 'Kasir') {
          router.push('/products')
        } else {
          router.push('/dashboard')
        }
      }, 1000)

    } else {
      errorMessage.value = result.message || 'Username atau password salah.'
    }
  } catch (err) {
    console.error('Error saat login:', err)
    errorMessage.value = 'Gagal terhubung ke server backend API.'
  } finally {
    isLoading.value = false
  }
}

// Navigasi ke pendaftaran akun pengguna sesuai role
const goToRegister = () => {
  router.push({
    path: '/register-user',
    query: { role: selectedRole.value }
  })
}
</script>

<template>
  <div class="h-screen w-screen bg-[#EEF0F5] overflow-hidden flex flex-col items-center justify-center py-2 px-4 font-sans">
    
    <!-- Logo SnapPOS -->
    <div class="mb-3 text-center">
      <img 
        src="../assets/pos.png" 
        alt="SnapPOS" 
        class="h-20 w-auto object-contain mix-blend-multiply mx-auto" 
      />
    </div>

    <!-- Card Form Utama -->
    <div class="bg-white rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.08),0_8px_15px_rgba(41,92,147,0.05)] w-full max-w-md p-6 sm:p-7 border border-white/80">
      
      <!-- Banner Header -->
      <div class="bg-[#f1f5f9] p-3.5 rounded-2xl text-center mb-4 border border-gray-200/50 shadow-inner">
        <h2 class="font-extrabold text-[#080C14] text-lg">Masuk ke SnapPOS</h2>
        <p class="text-[11px] text-gray-500 font-medium mt-0.5">Pilih peran dan masukkan kredensial akun Anda.</p>
      </div>

      <!-- NOTIFIKASI BERHASIL (GREEN ALERT) -->
      <div v-if="successMessage" class="mb-3.5 bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold p-3 rounded-xl flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-emerald-600 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
        </svg>
        <span>{{ successMessage }}</span>
      </div>

      <!-- NOTIFIKASI ERROR (RED ALERT) -->
      <div v-if="errorMessage" class="mb-3.5 bg-red-50 border border-red-200 text-red-600 text-xs font-semibold p-3 rounded-xl flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-500 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
        </svg>
        <span>{{ errorMessage }}</span>
      </div>

      <form @submit.prevent="handleLogin" class="space-y-3.5">
        
        <!-- PILIHAN ROLE -->
        <div>
          <label class="block text-[11px] font-bold text-gray-600 mb-1.5 ml-1">Pilih Peran Akun Anda:</label>
          <div class="grid grid-cols-2 gap-2 bg-[#eef2f7] p-1.5 rounded-2xl border border-gray-200/70">
            
            <!-- Button Admin -->
            <button
              type="button"
              @click="selectedRole = 'Admin'"
              :class="selectedRole === 'Admin' ? 'bg-white text-[#295C93] shadow-md border border-gray-100 font-extrabold' : 'text-gray-500 hover:text-gray-800 font-semibold'"
              class="py-2.5 rounded-xl text-xs transition-all text-center flex items-center justify-center gap-2"
            >
              <svg class="w-4 h-4" viewBox="0 0 24 24">
                <path d="M0 0h24v24H0z" fill="none" />
                <path fill="currentColor" d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12c5.16-1.26 9-6.45 9-12V5Zm0 3.9a3 3 0 1 1-3 3a3 3 0 0 1 3-3m0 7.9c2 0 6 1.09 6 3.08a7.2 7.2 0 0 1-12 0c0-1.99 4-3.08 6-3.08" />
              </svg>
              <span>Admin</span>
            </button>

            <!-- Button Kasir -->
            <button
              type="button"
              @click="selectedRole = 'Kasir'"
              :class="selectedRole === 'Kasir' ? 'bg-white text-[#295C93] shadow-md border border-gray-100 font-extrabold' : 'text-gray-500 hover:text-gray-800 font-semibold'"
              class="py-2.5 rounded-xl text-xs transition-all text-center flex items-center justify-center gap-2"
            >
              <svg class="w-4 h-4" viewBox="0 0 24 24">
                <path d="M0 0h24v24H0z" fill="none" />
                <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5">
                  <circle cx="12" cy="5.25" r="2.5" />
                  <path d="M5.75 14.25a6.25 4 0 0 1 12.5 0Z" />
                  <rect width="18.5" height="7" x="2.75" y="14.25" rx="1.5" />
                  <path d="M9.5 17.75h5" />
                </g>
              </svg>
              <span>Kasir</span>
            </button>

          </div>
        </div>

        <!-- Input Username -->
        <div>
          <label class="block text-[11px] font-bold text-gray-600 mb-1 ml-1">Username Akun</label>
          <div class="relative flex items-center">
            <span class="absolute left-3 text-gray-400">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24">
                <path d="M0 0h24v24H0z" fill="none" />
                <path fill="currentColor" d="M12 12c2.76 0 5-2.24 5-5s-2.24-5-5-5s-5 2.24-5 5s2.24 5 5 5m0-8c1.65 0 3 1.35 3 3s-1.35 3-3 3s-3-1.35-3-3s1.35-3 3-3M4 22h16c.55 0 1-.45 1-1v-1c0-3.86-3.14-7-7-7h-4c-3.86 0-7 3.14-7 7v1c0 .55.45 1 1 1m6-7h4c2.76 0 5 2.24 5 5H5c0-2.76 2.24-5 5-5" />
              </svg>
            </span>
            <input 
              v-model="username" 
              type="text" 
              placeholder="Masukkan username akun Anda"
              class="w-full bg-[#f8fafc] text-gray-800 font-bold pl-9 pr-3.5 py-2.5 rounded-xl border border-gray-200/80 focus:outline-none focus:border-[#295C93] text-xs transition-all shadow-[0_2px_5px_rgba(0,0,0,0.02)] focus:shadow-[0_4px_12px_rgba(41,92,147,0.12)]"
              required 
            />
          </div>
        </div>

        <!-- Input Kata Sandi -->
        <div>
          <label class="block text-[11px] font-bold text-gray-600 mb-1 ml-1">Kata Sandi</label>
          <div class="relative flex items-center">
            <span class="absolute left-3 text-gray-400">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24">
                <path d="M0 0h24v24H0z" fill="none" />
                <path fill="currentColor" d="M6 22h12c1.1 0 2-.9 2-2v-9c0-1.1-.9-2-2-2h-1V7c0-2.76-2.24-5-5-5S7 4.24 7 7v2H6c-1.1 0-2 .9-2 2v9c0 1.1.9 2 2 2M9 7c0-1.65 1.35-3 3-3s3 1.35 3 3v2H9zm-3 4h12v9h-5v-2.28c.59-.35 1-.99 1-1.72c0-1.1-.9-2-2-2s-2 .9-2 2a2 2 0 0 0 1 1.72V20H6z" />
              </svg>
            </span>
            <input 
              v-model="password" 
              :type="showPassword ? 'text' : 'password'" 
              placeholder="••••••••"
              class="w-full bg-[#f8fafc] text-gray-800 font-bold pl-9 pr-9 py-2.5 rounded-xl border border-gray-200/80 focus:outline-none focus:border-[#295C93] text-xs transition-all shadow-[0_2px_5px_rgba(0,0,0,0.02)] focus:shadow-[0_4px_12px_rgba(41,92,147,0.12)]"
              required 
            />
            <button 
              type="button" 
              @click="showPassword = !showPassword" 
              class="absolute right-3 text-gray-400 hover:text-gray-600 flex items-center justify-center"
            >
              <svg v-if="showPassword" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24">
                <path d="M0 0h24v24H0z" fill="none" />
                <path fill="currentColor" d="M12 19c.946 0 1.81-.103 2.598-.281l-1.757-1.757c-.273.021-.55.038-.841.038c-5.351 0-7.424-3.846-7.926-5a8.6 8.6 0 0 1 1.508-2.297L4.184 8.305c-1.538 1.667-2.121 3.346-2.132 3.379a1 1 0 0 0 0 .633C2.073 12.383 4.367 19 12 19m0-14c-1.837 0-3.346.396-4.604.981L3.707 2.293L2.293 3.707l18 18l1.414-1.414l-3.319-3.319c2.614-1.951 3.547-4.615 3.561-4.657a1 1 0 0 0 0-.633C21.927 11.617 19.633 5 12 5m4.972 10.558l-2.28-2.28c.19-.39.308-.819.308-1.278c0-1.641-1.359-3-3-3c-.459 0-.888.118-1.277.309L8.915 7.501A9.3 9.3 0 0 1 12 7c5.351 0 7.424 3.846 7.926 5c-.302.692-1.166 2.342-2.954 3.558" />
              </svg>
              <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 32 32">
                <path d="M0 0h32v32H0z" fill="none" />
                <path fill="currentColor" d="M30.94 15.66A16.69 16.69 0 0 0 16 5A16.69 16.69 0 0 0 1.06 15.66a1 1 0 0 0 0 .68A16.69 16.69 0 0 0 16 27a16.69 16.69 0 0 0 14.94-10.66a1 1 0 0 0 0-.68M16 25c-5.3 0-10.9-3.93-12.93-9C5.1 10.93 10.7 7 16 7s10.9 3.93 12.93 9C26.9 21.07 21.3 25 16 25" />
                <path fill="currentColor" d="M16 10a6 6 0 1 0 6 6a6 6 0 0 0-6-6m0 10a4 4 0 1 1 4-4a4 4 0 0 1-4 4" />
              </svg>
            </button>
          </div>
        </div>

        <!-- Ingat Saya & Lupa Password -->
        <div class="flex items-center justify-between text-[11px] pt-1">
          <label class="flex items-center gap-2 cursor-pointer text-gray-500 font-medium">
            <input v-model="rememberMe" type="checkbox" class="w-3.5 h-3.5 rounded text-[#295C93]" />
            <span>Ingat Saya</span>
          </label>
          <a href="#" class="text-[#295C93] font-bold hover:underline">Lupa Password?</a>
        </div>

        <!-- Tombol Masuk -->
        <button 
          type="submit" 
          :disabled="isLoading"
          class="w-full mt-2 bg-gradient-to-r from-[#080C14] via-[#295C93] to-[#4DA6FF] text-white font-extrabold py-3 rounded-xl shadow-[0_10px_25px_rgba(41,92,147,0.35)] hover:shadow-[0_12px_28px_rgba(41,92,147,0.45)] hover:opacity-95 hover:scale-[0.99] transition-all text-xs active:translate-y-0.5 disabled:opacity-50 flex items-center justify-center gap-2"
        >
          <span v-if="isLoading">Memverifikasi...</span>
          <span v-else>Masuk →</span>
        </button>

      </form>

      <!-- Garis Pemisah Soft -->
      <div class="border-t border-gray-100 my-4"></div>

      <!-- FOOTER LINK DAFTAR AKUN -->
      <p class="text-center text-xs text-gray-500 font-medium">
        Belum punya akun? 
        <button 
          @click="goToRegister" 
          class="text-[#295C93] font-bold hover:underline ml-0.5 focus:outline-none"
        >
          Daftar
        </button>
      </p>

    </div>
  </div>
</template>