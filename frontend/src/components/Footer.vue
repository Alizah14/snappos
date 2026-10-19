<script setup>
</script>

<template>
  <!-- FOOTER BACKGROUND #071C3C -->
  <footer class="w-full bg-[#071C3C] text-white mt-16 py-8 px-6 sm:px-10 border-t border-white/10">
    <div class="w-full flex flex-col md:flex-row items-center justify-between gap-6">
      
      <!-- LOGO TRANSPARAN TANPA KOTAK PUTIH & WARNA ASLI TETAP AMAN -->
      <div class="flex items-center justify-start">
        <div class="bg-white rounded-xl p-1.5 shadow-sm inline-flex items-center justify-center">
          <img 
            src="../assets/pos.png" 
            alt="SnapPOS" 
            class="h-9 w-auto object-contain block" 
          />
        </div>
      </div>

      <!-- HAK CIPTA (FONT SEMIBOLD & UKURAN PAS) -->
      <div class="text-center">
        <p class="text-base sm:text-lg font-semibold tracking-wide text-white">
          © 2026 SnapPOS — Hak Cipta Dilindungi
        </p>
      </div>

      <!-- LINK FOOTER (FONT SEMIBOLD & UKURAN PAS) -->
      <div class="flex items-center justify-end gap-8 text-base font-semibold text-white/90">
        <a href="#" class="hover:text-blue-300 transition-colors hover:underline">Pusat Bantuan</a>
        <a href="#" class="hover:text-blue-300 transition-colors hover:underline">Hubungi Kami</a>
        <a href="#" class="hover:text-blue-300 transition-colors hover:underline">Syarat & Privasi</a>
      </div>

    </div>
  </footer>
</template>