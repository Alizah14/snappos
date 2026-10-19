<script setup>
import { useRouter } from 'vue-router'
const router = useRouter()
</script>

<template>
  <!-- Background Layar Utama Menggunakan Kode Warna #EEF0F5 -->
  <div class="min-h-screen bg-[#EEF0F5] flex flex-col items-center justify-center p-4 font-sans">
    
    <!-- Card Utama Clean Modern (Putih Polos dengan Drop Shadow Abu-abu Soft) -->
    <div class="bg-white p-10 rounded-3xl shadow-[0_12px_35px_rgba(0,0,0,0.06)] text-center max-w-md w-full border border-gray-100">
      
      <!-- Logo SnapPOS -->
      <div class="flex justify-center mb-2">
        <img 
          src="../assets/pos.png" 
          alt="SnapPOS" 
          class="h-20 w-auto object-contain mix-blend-multiply" 
        />
      </div>

      <div class="mb-6">
        <h2 class="font-extrabold text-[#080C14] text-xl">Selamat Datang di SnapPOS</h2>
        <p class="text-xs text-gray-500 mt-1 font-medium">Hubungkan Toko Anda untuk mulai beroperasi</p>
      </div>

      <!-- Icon Toko SVG dalam Box Flat Halus -->
      <div class="my-6 flex justify-center">
        <div class="w-20 h-20 bg-[#f1f5f9] rounded-2xl flex items-center justify-center border border-gray-200/60">
          <svg 
            xmlns="http://www.w3.org/2000/svg" 
            class="w-10 h-10 text-[#295C93]" 
            viewBox="0 0 16 16"
          >
            <path d="M0 0h16v16H0z" fill="none" />
            <path 
              fill="currentColor" 
              d="M2.97 1.35A1 1 0 0 1 3.73 1h8.54a1 1 0 0 1 .76.35l2.609 3.044A1.5 1.5 0 0 1 16 5.37v.255a2.375 2.375 0 0 1-4.25 1.458A2.37 2.37 0 0 1 9.875 8A2.37 2.37 0 0 1 8 7.083A2.37 2.37 0 0 1 6.125 8a2.37 2.37 0 0 1-1.875-.917A2.375 2.375 0 0 1 0 5.625V5.37a1.5 1.5 0 0 1 .361-.976zm1.78 4.275a1.375 1.375 0 0 0 2.75 0a.5.5 0 0 1 1 0a1.375 1.375 0 0 0 2.75 0a.5.5 0 0 1 1 0a1.375 1.375 0 1 0 2.75 0V5.37a.5.5 0 0 0-.12-.325L12.27 2H3.73L1.12 5.045A.5.5 0 0 0 1 5.37v.255a1.375 1.375 0 0 0 2.75 0a.5.5 0 0 1 1 0M1.5 8.5A.5.5 0 0 1 2 9v6h1v-5a1 1 0 0 1 1-1h3a1 1 0 0 1 1 1v5h6V9a.5.5 0 0 1 1 0v6h.5a.5.5 0 0 1 0 1H.5a.5.5 0 0 1 0-1H1V9a.5.5 0 0 1 .5-.5M4 15h3v-5H4zm5-5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1zm3 0h-2v3h2z" 
            />
          </svg>
        </div>
      </div>

      <h3 class="font-extrabold text-[#295C93] text-lg mb-8">Mulai Toko Digital Anda</h3>

      <!-- Tombol Gradien Brand SnapPOS -->
      <button 
        @click="router.push('/register-store')"
        class="w-full bg-gradient-to-r from-[#080C14] via-[#295C93] to-[#4DA6FF] text-white font-extrabold text-base py-4 rounded-xl shadow-[0_8px_20px_rgba(41,92,147,0.3)] hover:opacity-95 hover:scale-[0.99] transition-all"
      >
        Daftarkan Toko Baru →
      </button>
    </div>
  </div>
</template>