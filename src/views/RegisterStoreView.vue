<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const fileInputRef = ref(null)
const previewImage = ref(null)

const storeData = ref({
  nama_toko: '',
  kategori_usaha: '',
  no_telepon: '',
  alamat: '',
  deskripsi: ''
})

const isLoading = ref(false)
const errorMessage = ref('')
const successMessage = ref('')

const triggerFileUpload = () => {
  fileInputRef.value.click()
}

const handleFileUpload = (event) => {
  const file = event.target.files[0]
  if (file) {
    previewImage.value = URL.createObjectURL(file)
  }
}

const handleRegister = async () => {
  if (!storeData.value.nama_toko.trim()) {
    errorMessage.value = 'Nama toko wajib diisi.'
    return
  }

  isLoading.value = true
  errorMessage.value = ''
  successMessage.value = ''

  try {
    const response = await fetch('http://localhost/snappos-api/register_store.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        nama_toko: storeData.value.nama_toko,
        kategori_toko: storeData.value.kategori_usaha,
        alamat_toko: storeData.value.alamat
      })
    })

    const result = await response.json()

    if (result.status === 'success') {
      // Tampilkan Notifikasi Berhasil
      successMessage.value = `Toko "${result.nama_toko}" berhasil didaftarkan dan tersimpan!`

      // Simpan data di LocalStorage
      localStorage.setItem('nama_toko', result.nama_toko)
      if (result.store_id) {
        localStorage.setItem('store_id', result.store_id)
      }

      // Berikan delay 1.5 detik agar pengguna sempat melihat notifikasi sebelum redirect
      setTimeout(() => {
        router.push('/register-user')
      }, 1500)

    } else {
      errorMessage.value = result.message || 'Gagal mendaftarkan toko.'
    }
  } catch (error) {
    console.error('Error register store:', error)
    errorMessage.value = 'Gagal terhubung ke server backend API.'
  } finally {
    isLoading.value = false
  }
}

const handleCancel = () => {
  router.push('/')
}
</script>

<template>
  <div class="h-screen w-screen bg-[#EEF0F5] overflow-hidden flex flex-col items-center justify-center py-2 px-4 font-sans">
    
    <!-- Logo SnapPOS -->
    <div class="mb-2 text-center">
      <img 
        src="../assets/pos.png" 
        alt="SnapPOS" 
        class="h-20 w-auto object-contain mix-blend-multiply mx-auto" 
      />
    </div>

    <!-- Header Banner -->
    <div class="bg-white py-3.5 px-6 rounded-2xl shadow-[0_8px_25px_rgba(0,0,0,0.04)] border border-gray-100/80 flex items-center justify-center gap-3 mb-3 w-full max-w-2xl">
      <div class="w-9 h-9 rounded-xl bg-[#295C93]/10 text-[#295C93] flex items-center justify-center flex-shrink-0">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 16 16">
          <path fill="currentColor" d="M2.97 1.35A1 1 0 0 1 3.73 1h8.54a1 1 0 0 1 .76.35l2.609 3.044A1.5 1.5 0 0 1 16 5.37v.255a2.375 2.375 0 0 1-4.25 1.458A2.37 2.37 0 0 1 9.875 8A2.37 2.37 0 0 1 8 7.083A2.37 2.37 0 0 1 6.125 8a2.37 2.37 0 0 1-1.875-.917A2.375 2.375 0 0 1 0 5.625V5.37a1.5 1.5 0 0 1 .361-.976zm1.78 4.275a1.375 1.375 0 0 0 2.75 0a.5.5 0 0 1 1 0a1.375 1.375 0 0 0 2.75 0a.5.5 0 0 1 1 0a1.375 1.375 0 1 0 2.75 0V5.37a.5.5 0 0 0-.12-.325L12.27 2H3.73L1.12 5.045A.5.5 0 0 0 1 5.37v.255a1.375 1.375 0 0 0 2.75 0a.5.5 0 0 1 1 0M1.5 8.5A.5.5 0 0 1 2 9v6h1v-5a1 1 0 0 1 1-1h3a1 1 0 0 1 1 1v5h6V9a.5.5 0 0 1 1 0v6h.5a.5.5 0 0 1 0 1H.5a.5.5 0 0 1 0-1H1V9a.5.5 0 0 1 .5-.5M4 15h3v-5H4zm5-5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1zm3 0h-2v3h2z"/>
        </svg>
      </div>
      <h2 class="font-extrabold text-[#080C14] text-xl tracking-wide">Pendaftaran Toko Baru</h2>
    </div>

    <!-- Card Form Utama -->
    <div class="bg-white rounded-3xl shadow-[0_12px_30px_rgba(0,0,0,0.06)] w-full max-w-2xl p-7 border border-gray-100/80">
      
      <!-- NOTIFIKASI BERHASIL (GREEN ALERT) -->
      <div v-if="successMessage" class="mb-3.5 bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold p-3.5 rounded-xl flex items-center gap-2 animate-bounce">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-emerald-600 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
        </svg>
        <span>{{ successMessage }}</span>
      </div>

      <!-- NOTIFIKASI ERROR (RED ALERT) -->
      <div v-if="errorMessage" class="mb-3.5 bg-red-50 border border-red-200 text-red-600 text-xs font-semibold p-3.5 rounded-xl flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-500 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
        </svg>
        <span>{{ errorMessage }}</span>
      </div>

      <form @submit.prevent="handleRegister" class="space-y-3.5">
        
        <!-- Row Atas: Input Kiri & Foto Kanan -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 items-center">
          
          <div class="space-y-2.5">
            <div>
              <label class="block text-xs font-bold text-gray-500 mb-1 ml-1">Nama Toko <span class="text-red-500">*</span></label>
              <input 
                v-model="storeData.nama_toko" 
                type="text" 
                placeholder="Contoh: Berkah Sentosa Retail"
                class="w-full bg-[#f8fafc] text-gray-800 font-bold p-3 rounded-xl border border-gray-200/80 focus:outline-none focus:border-[#295C93] text-sm transition-all" 
                required 
              />
            </div>

            <div>
              <label class="block text-xs font-bold text-gray-500 mb-1 ml-1">Kategori Usaha <span class="text-red-500">*</span></label>
              <input 
                v-model="storeData.kategori_usaha" 
                type="text"
                placeholder="Contoh: Minimarket"
                class="w-full bg-[#f8fafc] text-gray-800 font-bold p-3 rounded-xl border border-gray-200/80 focus:outline-none focus:border-[#295C93] text-sm transition-all"
                required
              />
            </div>

            <div>
              <label class="block text-xs font-bold text-gray-500 mb-1 ml-1">Nomor Telepon Toko</label>
              <input 
                v-model="storeData.no_telepon" 
                type="text" 
                placeholder="0812-3456-7890"
                class="w-full bg-[#f8fafc] text-gray-800 font-bold p-3 rounded-xl border border-gray-200/80 focus:outline-none focus:border-[#295C93] text-sm transition-all" 
              />
            </div>
          </div>

          <!-- Area Upload Foto -->
          <div class="flex justify-center items-center h-full">
            <input 
              ref="fileInputRef" 
              type="file" 
              accept="image/*" 
              class="hidden" 
              @change="handleFileUpload" 
            />

            <div 
              @click="triggerFileUpload"
              class="w-full h-full min-h-[195px] bg-[#f8fafc] rounded-2xl border-2 border-dashed border-[#4DA6FF]/50 hover:border-[#295C93] flex flex-col items-center justify-center cursor-pointer transition-all overflow-hidden relative group"
            >
              <img 
                v-if="previewImage" 
                :src="previewImage" 
                alt="Foto Toko" 
                class="w-full h-full object-cover rounded-2xl" 
              />

              <template v-else>
                <svg xmlns="http://www.w3.org/2000/svg" class="w-9 h-9 text-[#295C93] mb-1.5 group-hover:scale-110 transition-transform" viewBox="0 0 1024 1024">
                  <path d="M0 0h1024v1024H0z" fill="none" />
                  <path fill="currentColor" d="M864 248H728l-32.4-90.8a32.07 32.07 0 0 0-30.2-21.2H358.6c-13.5 0-25.6 8.5-30.1 21.2L296 248H160c-44.2 0-80 35.8-80 80v456c0 44.2 35.8 80 80 80h704c44.2 0 80-35.8 80-80V328c0-44.2-35.8-80-80-80m8 536c0 4.4-3.6 8-8 8H160c-4.4 0-8-3.6-8-8V328c0-4.4 3.6-8 8-8h186.7l17.1-47.8l22.9-64.2h250.5l22.9 64.2l17.1 47.8H864c4.4 0 8 3.6 8 8zM512 384c-88.4 0-160 71.6-160 160s71.6 160 160 160s160-71.6 160-160s-71.6-160-160-160m0 256c-53 0-96-43-96-96s43-96 96-96s96 43 96 96s-43 96-96 96" />
                </svg>
                <span class="text-xs font-bold text-[#295C93]">Upload Foto</span>
              </template>
            </div>
          </div>

        </div>

        <!-- Alamat Lengkap Toko -->
        <div>
          <label class="block text-xs font-bold text-gray-500 mb-1 ml-1">Alamat Lengkap Toko</label>
          <input 
            v-model="storeData.alamat" 
            type="text" 
            placeholder="Jl. Sudirman No. 128, Lantai 1, Jakarta"
            class="w-full bg-[#f8fafc] text-gray-800 font-bold p-3 rounded-xl border border-gray-200/80 focus:outline-none focus:border-[#295C93] text-sm transition-all" 
          />
        </div>

        <!-- Deskripsi Singkat Toko -->
        <div>
          <div class="flex justify-between items-center mb-1 ml-1">
            <label class="text-xs font-bold text-gray-500">Deskripsi Singkat</label>
            <span class="text-gray-400 text-xs font-normal">Opsional</span>
          </div>
          <textarea 
            v-model="storeData.deskripsi" 
            rows="2" 
            placeholder="Penyedia kebutuhan harian retail & grosir"
            class="w-full bg-[#f8fafc] text-gray-800 font-bold p-3 rounded-xl border border-gray-200/80 focus:outline-none focus:border-[#295C93] text-sm transition-all resize-none"
          ></textarea>
        </div>

        <!-- Tombol Aksi -->
        <div class="flex gap-4 pt-1">
          <button 
            @click="handleCancel" 
            type="button" 
            class="w-1/3 bg-[#f1f5f9] text-gray-600 font-extrabold py-3.5 rounded-xl hover:bg-gray-200 transition-all text-sm"
          >
            Batal
          </button>
          <button 
            type="submit" 
            :disabled="isLoading"
            class="w-2/3 bg-gradient-to-r from-[#080C14] via-[#295C93] to-[#4DA6FF] text-white font-extrabold py-3.5 rounded-xl shadow-[0_8px_20px_rgba(41,92,147,0.3)] hover:opacity-95 hover:scale-[0.99] transition-all text-sm disabled:opacity-50 flex items-center justify-center gap-2"
          >
            <span v-if="isLoading">Menyimpan...</span>
            <span v-else>Selesai</span>
          </button>
        </div>

      </form>
    </div>
  </div>
</template>