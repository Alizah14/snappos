<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'

const router = useRouter()
const route = useRoute()

// Default role Admin jika pendaftaran pertama dari pendaftaran toko
const role = ref('Admin')

const showPassword = ref(false)
const showConfirmPassword = ref(false)

// Inisialisasi data form dalam keadaan kosong
const userData = ref({
  nama_lengkap: '',
  username: '',
  no_hp: '',
  password: '',
  confirm_password: '',
  setuju_syarat: false
})

const isLoading = ref(false)
const errorMessage = ref('')
const successMessage = ref('')

onMounted(() => {
  if (route.query.role) {
    role.value = route.query.role
  }

  // RESET / KOSONGKAN FORM SETIAP KALI HALAMAN DIBUKA
  userData.value = {
    nama_lengkap: '',
    username: '',
    no_hp: '',
    password: '',
    confirm_password: '',
    setuju_syarat: false
  }
})

const handleRegisterUser = async () => {
  if (userData.value.password !== userData.value.confirm_password) {
    errorMessage.value = 'Konfirmasi Kata Sandi tidak cocok!'
    return
  }

  if (!userData.value.setuju_syarat) {
    errorMessage.value = 'Anda harus menyetujui Ketentuan Layanan & Kebijakan Privasi.'
    return
  }

  isLoading.value = true
  errorMessage.value = ''
  successMessage.value = ''

  try {
    const response = await fetch('http://localhost/snappos-api/users.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        nama_lengkap: userData.value.nama_lengkap,
        username: userData.value.username,
        no_hp: userData.value.no_hp,
        password: userData.value.password,
        role: role.value
      })
    })

    const result = await response.json()

    if (result.status === 'success') {
      successMessage.value = `Akun ${role.value} "${userData.value.username}" berhasil terdaftar! Mengalihkan ke halaman Login...`
      
      // Kosongkan form kembali setelah sukses
      userData.value = {
        nama_lengkap: '',
        username: '',
        no_hp: '',
        password: '',
        confirm_password: '',
        setuju_syarat: false
      }

      setTimeout(() => {
        router.push('/login')
      }, 1500)
    } else {
      errorMessage.value = result.message || 'Gagal mendaftarkan pengguna.'
    }
  } catch (err) {
    console.error('Error register user:', err)
    errorMessage.value = 'Gagal terhubung ke server backend API.'
  } finally {
    isLoading.value = false
  }
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
    <div class="bg-white rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.08),0_8px_15px_rgba(41,92,147,0.05)] w-full max-w-md p-6 border border-white/80">
      
      <!-- Banner Header Dinamis -->
      <div class="bg-[#f1f5f9] p-3.5 rounded-2xl text-center mb-4 border border-gray-200/50 shadow-inner">
        <h2 class="font-extrabold text-[#080C14] text-lg">Daftar {{ role }} Baru</h2>
        <p class="text-[11px] text-gray-500 font-medium mt-0.5">Lengkapi data di bawah untuk mendaftarkan akun {{ role.toLowerCase() }} baru.</p>
      </div>

      <!-- NOTIFIKASI BERHASIL -->
      <div v-if="successMessage" class="mb-3.5 bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold p-3 rounded-xl flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-emerald-600 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
        </svg>
        <span>{{ successMessage }}</span>
      </div>

      <!-- NOTIFIKASI ERROR -->
      <div v-if="errorMessage" class="mb-3.5 bg-red-50 border border-red-200 text-red-600 text-xs font-semibold p-3 rounded-xl flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-500 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
        </svg>
        <span>{{ errorMessage }}</span>
      </div>

      <!-- Tambahkan autocomplete="off" pada tag form -->
      <form @submit.prevent="handleRegisterUser" autocomplete="off" class="space-y-3">
        
        <!-- Input Nama Lengkap -->
        <div>
          <label class="block text-[11px] font-bold text-gray-600 mb-1 ml-1">Nama Lengkap</label>
          <div class="relative flex items-center">
            <span class="absolute left-3 text-gray-400">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24">
                <path d="M0 0h24v24H0z" fill="none" />
                <path fill="currentColor" d="M12 12c2.76 0 5-2.24 5-5s-2.24-5-5-5s-5 2.24-5 5s2.24 5 5 5m0-8c1.65 0 3 1.35 3 3s-1.35 3-3 3s-3-1.35-3-3s1.35-3 3-3M4 22h16c.55 0 1-.45 1-1v-1c0-3.86-3.14-7-7-7h-4c-3.86 0-7 3.14-7 7v1c0 .55.45 1 1 1m6-7h4c2.76 0 5 2.24 5 5H5c0-2.76 2.24-5 5-5" />
              </svg>
            </span>
            <input 
              v-model="userData.nama_lengkap" 
              type="text" 
              autocomplete="off"
              placeholder="Masukkan nama lengkap Anda"
              class="w-full bg-[#f8fafc] text-gray-800 font-bold pl-9 pr-3.5 py-2.5 rounded-xl border border-gray-200/80 focus:outline-none focus:border-[#295C93] text-xs transition-all shadow-[0_2px_5px_rgba(0,0,0,0.02)] focus:shadow-[0_4px_12px_rgba(41,92,147,0.12)]"
              required 
            />
          </div>
        </div>

        <!-- Input Username -->
        <div>
          <div class="flex justify-between items-center mb-1 ml-1">
            <label class="text-[11px] font-bold text-gray-600">Username</label>
            <span class="text-[10px] text-gray-400 font-semibold">Wajib</span>
          </div>
          <div class="relative flex items-center">
            <span class="absolute left-3 text-gray-400">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24">
                <path d="M0 0h24v24H0z" fill="none" />
                <path fill="currentColor" d="M12 12c2.76 0 5-2.24 5-5s-2.24-5-5-5s-5 2.24-5 5s2.24 5 5 5m0-8c1.65 0 3 1.35 3 3s-1.35 3-3 3s-3-1.35-3-3s1.35-3 3-3M4 22h16c.55 0 1-.45 1-1v-1c0-3.86-3.14-7-7-7h-4c-3.86 0-7 3.14-7 7v1c0 .55.45 1 1 1m6-7h4c2.76 0 5 2.24 5 5H5c0-2.76 2.24-5 5-5" />
              </svg>
            </span>
            <input 
              v-model="userData.username" 
              type="text" 
              autocomplete="off"
              placeholder="Masukkan username"
              class="w-full bg-[#f8fafc] text-gray-800 font-bold pl-9 pr-3.5 py-2.5 rounded-xl border border-gray-200/80 focus:outline-none focus:border-[#295C93] text-xs transition-all shadow-[0_2px_5px_rgba(0,0,0,0.02)] focus:shadow-[0_4px_12px_rgba(41,92,147,0.12)]"
              required 
            />
          </div>
        </div>

        <!-- Input Nomor HP / WhatsApp -->
        <div>
          <div class="flex justify-between items-center mb-1 ml-1">
            <label class="text-[11px] font-bold text-gray-600">Nomor HP / WhatsApp</label>
            <span class="text-[10px] text-gray-400 font-semibold">Aktif</span>
          </div>
          <div class="relative flex items-center">
            <span class="absolute left-3 text-gray-400">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 16 16">
                <path d="M0 0h16v16H0z" fill="none" />
                <path fill="currentColor" d="M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.6 17.6 0 0 0 4.168 6.608a17.6 17.6 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.68.68 0 0 0-.58-.122l-2.19.547a1.75 1.75 0 0 1-1.657-.459L5.482 8.062a1.75 1.75 0 0 1-.46-1.657l.548-2.19a.68.68 0 0 0-.122-.58zM1.884.511a1.745 1.745 0 0 1 2.612.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.68.68 0 0 0 .178.643l2.457 2.457a.68.68 0 0 0 .644.178l2.189-.547a1.75 1.75 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.6 18.6 0 0 1-7.01-4.42a18.6 18.6 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877z" />
              </svg>
            </span>
            <input 
              v-model="userData.no_hp" 
              type="text" 
              autocomplete="off"
              placeholder="0812-3456-7890"
              class="w-full bg-[#f8fafc] text-gray-800 font-bold pl-9 pr-3.5 py-2.5 rounded-xl border border-gray-200/80 focus:outline-none focus:border-[#295C93] text-xs transition-all shadow-[0_2px_5px_rgba(0,0,0,0.02)] focus:shadow-[0_4px_12px_rgba(41,92,147,0.12)]"
              required 
            />
          </div>
        </div>

        <!-- Input Kata Sandi -->
        <div>
          <label class="block text-[11px] font-bold text-gray-600 mb-1 ml-1">Kata Sandi (Password)</label>
          <div class="relative flex items-center">
            <span class="absolute left-3 text-gray-400">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24">
                <path d="M0 0h24v24H0z" fill="none" />
                <path fill="currentColor" d="M6 22h12c1.1 0 2-.9 2-2v-9c0-1.1-.9-2-2-2h-1V7c0-2.76-2.24-5-5-5S7 4.24 7 7v2H6c-1.1 0-2 .9-2 2v9c0 1.1.9 2 2 2M9 7c0-1.65 1.35-3 3-3s3 1.35 3 3v2H9zm-3 4h12v9h-5v-2.28c.59-.35 1-.99 1-1.72c0-1.1-.9-2-2-2s-2 .9-2 2a2 2 0 0 0 1 1.72V20H6z" />
              </svg>
            </span>
            <input 
              v-model="userData.password" 
              :type="showPassword ? 'text' : 'password'" 
              autocomplete="new-password"
              placeholder="Minimal 8 karakter"
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
          <p class="text-[10px] text-gray-400 font-medium mt-1 ml-1">Minimal 8 karakter dengan kombinasi angka</p>
        </div>

        <!-- Input Konfirmasi Kata Sandi -->
        <div>
          <label class="block text-[11px] font-bold text-gray-600 mb-1 ml-1">Konfirmasi Kata Sandi</label>
          <div class="relative flex items-center">
            <span class="absolute left-3 text-gray-400">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24">
                <path d="M0 0h24v24H0z" fill="none" />
                <path fill="currentColor" d="M6 22h12c1.1 0 2-.9 2-2v-9c0-1.1-.9-2-2-2h-1V7c0-2.76-2.24-5-5-5S7 4.24 7 7v2H6c-1.1 0-2 .9-2 2v9c0 1.1.9 2 2 2M9 7c0-1.65 1.35-3 3-3s3 1.35 3 3v2H9zm-3 4h12v9h-5v-2.28c.59-.35 1-.99 1-1.72c0-1.1-.9-2-2-2s-2 .9-2 2a2 2 0 0 0 1 1.72V20H6z" />
              </svg>
            </span>
            <input 
              v-model="userData.confirm_password" 
              :type="showConfirmPassword ? 'text' : 'password'" 
              autocomplete="new-password"
              placeholder="Ulangi kata sandi Anda"
              class="w-full bg-[#f8fafc] text-gray-800 font-bold pl-9 pr-9 py-2.5 rounded-xl border border-gray-200/80 focus:outline-none focus:border-[#295C93] text-xs transition-all shadow-[0_2px_5px_rgba(0,0,0,0.02)] focus:shadow-[0_4px_12px_rgba(41,92,147,0.12)]"
              required 
            />
            <button 
              type="button" 
              @click="showConfirmPassword = !showConfirmPassword" 
              class="absolute right-3 text-gray-400 hover:text-gray-600 flex items-center justify-center"
            >
              <svg v-if="showConfirmPassword" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24">
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

        <!-- Checkbox Syarat & Ketentuan -->
        <div class="flex items-start gap-2 pt-1">
          <input 
            v-model="userData.setuju_syarat" 
            type="checkbox" 
            id="terms" 
            class="mt-0.5 w-3.5 h-3.5 rounded text-[#295C93] cursor-pointer" 
            required 
          />
          <label for="terms" class="text-[10px] text-gray-500 font-medium leading-tight cursor-pointer">
            Dengan mendaftar, Anda menyetujui <a href="#" class="text-[#295C93] font-bold hover:underline">Ketentuan Layanan</a> & <a href="#" class="text-[#295C93] font-bold hover:underline">Kebijakan Privasi</a> SnapPOS.
          </label>
        </div>

        <!-- Tombol Daftar Sekarang -->
        <button 
          type="submit" 
          :disabled="isLoading"
          class="w-full mt-2 bg-gradient-to-r from-[#080C14] via-[#295C93] to-[#4DA6FF] text-white font-extrabold py-3 rounded-xl shadow-[0_10px_25px_rgba(41,92,147,0.35)] hover:shadow-[0_12px_28px_rgba(41,92,147,0.45)] hover:opacity-95 hover:scale-[0.99] transition-all text-xs active:translate-y-0.5 disabled:opacity-50 flex items-center justify-center gap-2"
        >
          <span v-if="isLoading">Mendaftarkan...</span>
          <span v-else>Daftar Sekarang →</span>
        </button>

      </form>

      <!-- Footer Link Masuk Ke Halaman Login -->
      <p class="text-center text-xs text-gray-500 font-medium mt-3">
        Sudah punya akun? 
        <router-link to="/login" class="text-[#295C93] font-black hover:underline">Login</router-link>
      </p>

    </div>
  </div>
</template>