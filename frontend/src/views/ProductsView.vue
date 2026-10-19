<script setup>
import { ref, computed, onMounted } from 'vue'
import NavbarAdmin from '../components/NavbarAdmin.vue'
import NavbarKasir from '../components/NavbarKasir.vue'
import Footer from '../components/Footer.vue'

// Data State
const storeName = ref('Lancar barokah')
const userRole = ref('Admin') // 'Admin' atau 'Kasir'
const searchQuery = ref('')
const selectedCategory = ref('Semua')
const selectedStockStatus = ref('Semua')

// State Produk & Loading
const products = ref([])
const isLoading = ref(true)

// State Pagination
const currentPage = ref(1)
const totalPages = ref(1)

// URL Base Backend API PHP
const API_URL = 'http://localhost/snappos-api/produk.php'

// 1. Function untuk memuat data produk dari MySQL (Sudah Disesuaikan Kolom SQL)
const fetchProducts = async () => {
  isLoading.value = true
  try {
    const res = await fetch(API_URL)
    const data = await res.json()
    
    if (Array.isArray(data)) {
      // Pemetaan disesuaikan dengan kolom tabel 'products' MySQL
      products.value = data.map(item => ({
        id: item.id,
        name: item.name, // Menggunakan 'name' dari database
        sku: item.sku || `SKU-${item.id.toString().padStart(6, '0')}`,
        category: item.category || 'Umum', // Menggunakan 'category' dari database
        modelPhotosCount: parseInt(item.model_photos_count) || 0, // Menggunakan 'model_photos_count'
        price: parseFloat(item.price) || 0, // Menggunakan 'price' dari database
        stock: parseInt(item.stock) || 0, // Menggunakan 'stock' dari database
        unit: item.unit || 'pcs', // Menggunakan 'unit' dari database
        isTrained: Boolean(item.is_trained),
        image: formatImageUrl(item.image) // Menggunakan 'image' dari database
      }))
    }
  } catch (err) {
    console.error('Gagal mengambil data produk dari server:', err)
  } finally {
    isLoading.value = false
  }
}

// Format URL Gambar dari Server / Unsplash / Local
const formatImageUrl = (photoPath) => {
  if (!photoPath) {
    return 'https://images.unsplash.com/photo-1584824486509-112e4181ff6b?q=80&w=600&auto=format&fit=crop'
  }
  if (photoPath.startsWith('http://') || photoPath.startsWith('https://')) {
    return photoPath
  }
  return `http://localhost/snappos-api/uploads/${photoPath}`
}

onMounted(() => {
  const savedStore = localStorage.getItem('nama_toko')
  const savedRole = localStorage.getItem('role')
  if (savedStore) storeName.value = savedStore
  if (savedRole) userRole.value = savedRole

  // Panggil API saat halaman dibuka
  fetchProducts()
})

// 2. Computed Filter Produk Dynamic
const filteredProducts = computed(() => {
  return products.value.filter(product => {
    const matchesSearch = product.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                          product.sku.toLowerCase().includes(searchQuery.value.toLowerCase())
    const matchesCategory = selectedCategory.value === 'Semua' || product.category === selectedCategory.value
    
    let matchesStock = true
    if (selectedStockStatus.value === 'Sedia') matchesStock = product.stock > 10
    if (selectedStockStatus.value === 'Menipis') matchesStock = product.stock <= 10 && product.stock > 0

    return matchesSearch && matchesCategory && matchesStock
  })
})

// 3. Function Hapus Produk dari Database MySQL
const deleteProduct = async (id, name) => {
  if (!confirm(`Apakah Anda yakin ingin menghapus produk "${name}"?`)) return

  try {
    const res = await fetch(`${API_URL}?id=${id}`, {
      method: 'DELETE'
    })
    const result = await res.json()
    
    if (result.status === 'success') {
      alert('Produk berhasil dihapus!')
      fetchProducts() // Refresh list produk
    } else {
      alert(result.message || 'Gagal menghapus produk.')
    }
  } catch (err) {
    console.error('Error saat menghapus produk:', err)
    alert('Terjadi kesalahan koneksi ke server.')
  }
}

const formatCurrency = (val) => {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val)
}
</script>

<template>
  <div class="min-h-screen bg-[#FAF8FF] font-sans text-gray-800 flex flex-col justify-between">
    
    <div>
      <!-- NAVBAR DINAMIS SESUAI ROLE -->
      <NavbarAdmin v-if="userRole === 'Admin'" :storeName="storeName" />
      <NavbarKasir v-else :storeName="storeName" />

      <!-- CONTAINER UTAMA -->
      <main class="w-full max-w-[1600px] mx-auto px-6 sm:px-10 lg:px-12 py-8 space-y-6">

        <!-- JUDUL HALAMAN -->
        <div class="flex items-center justify-between">
          <h1 class="text-4xl sm:text-5xl font-bold text-[#080C14] tracking-tight">
            Katalog Produk
          </h1>
        </div>

        <!-- BAR PENCARIAN & FILTER -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
          
          <!-- Input Cari -->
          <div class="relative flex-1 max-w-md">
            <svg class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-gray-400" viewBox="0 0 24 24">
              <path d="M0 0h24v24H0z" fill="none" />
              <path fill="currentColor" d="M18 10c0-4.41-3.59-8-8-8s-8 3.59-8 8s3.59 8 8 8c1.85 0 3.54-.63 4.9-1.69l5.1 5.1L21.41 20l-5.1-5.1A8 8 0 0 0 18 10M4 10c0-3.31 2.69-6 6-6s6 2.69 6 6s-2.69 6-6 6s-6-2.69-6-6" />
            </svg>
            <input 
              v-model="searchQuery"
              type="text" 
              placeholder="Cari produk atau SKU..."
              class="w-full pl-12 pr-4 py-3 bg-white rounded-2xl border border-gray-200/80 text-sm font-medium focus:outline-none focus:border-[#295C93] focus:ring-1 focus:ring-[#295C93] shadow-sm transition-all"
            />
          </div>

          <!-- Filter Dropdown & Tombol Tambah -->
          <div class="flex items-center gap-3 flex-wrap">
            <div class="flex items-center gap-2 bg-white px-3.5 py-2.5 rounded-2xl border border-gray-200/80 shadow-sm text-xs font-semibold text-gray-700">
              <span class="text-gray-400">Kategori:</span>
              <select v-model="selectedCategory" class="bg-transparent focus:outline-none font-semibold cursor-pointer">
                <option value="Semua">Semua</option>
                <option value="Minuman">Minuman</option>
                <option value="Sembako">Sembako</option>
                <option value="Kebersihan">Kebersihan</option>
                <option value="Makanan">Makanan</option>
              </select>
            </div>

            <div class="flex items-center gap-2 bg-white px-3.5 py-2.5 rounded-2xl border border-gray-200/80 shadow-sm text-xs font-semibold text-gray-700">
              <span class="text-gray-400">Status Stok:</span>
              <select v-model="selectedStockStatus" class="bg-transparent focus:outline-none font-semibold cursor-pointer">
                <option value="Semua">(Semua)</option>
                <option value="Sedia">Sedia</option>
                <option value="Menipis">Menipis</option>
              </select>
            </div>

            <!-- Tombol Tambah Produk Baru -->
            <button class="bg-[#295C93] hover:bg-[#1d426a] text-white px-5 py-3 rounded-2xl text-xs font-semibold shadow-[0_6px_18px_rgba(41,92,147,0.3)] transition-all flex items-center gap-2 cursor-pointer">
              <span>+</span>
              <span>Tambah Produk Baru</span>
            </button>
          </div>

        </div>

        <!-- STATUS INDIKATOR AI & TOTAL PRODUK -->
        <div class="flex items-center justify-between text-xs font-semibold text-gray-500 pt-1">
          <span>Menampilkan {{ filteredProducts.length }} produk siap AI</span>
          
          <div class="flex items-center gap-4">
            <span class="flex items-center gap-1.5">
              <span class="w-2.5 h-2.5 rounded-full bg-[#295C93]"></span>
              Terlatih
            </span>
            <span class="flex items-center gap-1.5">
              <span class="w-2.5 h-2.5 rounded-full bg-sky-400"></span>
              Perlu Update
            </span>
          </div>
        </div>

        <!-- GRID UTAMA: KATALOG PRODUK & WIDGET AI -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
          
          <!-- KARTU KATALOG PRODUK (3 KOLOM) -->
          <div class="lg:col-span-3 space-y-8">
            
            <!-- Indikator Loading -->
            <div v-if="isLoading" class="text-center py-12 bg-white rounded-3xl border border-gray-100 font-semibold text-gray-500">
              Memuat katalog produk dari database...
            </div>

            <!-- Tampilan Jika Produk Kosong -->
            <div v-else-if="filteredProducts.length === 0" class="text-center py-12 bg-white rounded-3xl border border-gray-100 font-semibold text-gray-500">
              Tidak ada produk yang ditemukan.
            </div>

            <div v-else class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
              
              <div 
                v-for="product in filteredProducts" 
                :key="product.id"
                class="bg-white rounded-3xl border border-gray-200/80 shadow-[0_8px_20px_rgba(0,0,0,0.02)] overflow-hidden flex flex-col justify-between transition-all duration-300 transform hover:-translate-y-1.5 hover:shadow-[0_20px_35px_rgba(0,0,0,0.08)] hover:border-[#295C93]/40 cursor-pointer"
              >
                <!-- Gambar & Badge Kategori -->
                <div class="relative h-48 bg-gray-50 overflow-hidden">
                  <img 
                    :src="product.image" 
                    :alt="product.name"
                    class="w-full h-full object-cover transition-transform duration-500 hover:scale-105" 
                  />
                  <span class="absolute top-3 right-3 bg-white/90 backdrop-blur-md text-gray-700 px-3 py-1 rounded-full text-[11px] font-semibold shadow-sm">
                    {{ product.category }}
                  </span>
                </div>

                <!-- Detail Info Produk -->
                <div class="p-5 space-y-3 flex-1 flex flex-col justify-between">
                  <div>
                    <h3 class="font-bold text-[#080C14] text-base leading-snug">
                      {{ product.name }}
                    </h3>
                    
                    <div class="flex items-center gap-2 text-xs text-gray-400 font-medium mt-1">
                      <span>{{ product.sku }}</span>
                      <span>•</span>
                      <span class="text-[#295C93] font-semibold">{{ product.modelPhotosCount }} Foto Model</span>
                    </div>
                  </div>

                  <div class="flex items-end justify-between pt-2">
                    <div>
                      <span class="text-[11px] font-semibold text-gray-400 block">Harga Jual</span>
                      <span class="text-lg font-bold text-[#295C93]">{{ formatCurrency(product.price) }}</span>
                    </div>

                    <div class="text-right">
                      <span class="text-[11px] font-semibold text-gray-400 block">Sisa Stok</span>
                      <span 
                        :class="product.stock <= 10 ? 'bg-red-50 text-red-600 border-red-100' : 'bg-blue-50 text-[#295C93] border-blue-100'"
                        class="px-2.5 py-1 rounded-lg border text-xs font-semibold inline-block mt-0.5"
                      >
                        {{ product.stock }} {{ product.unit }}
                      </span>
                    </div>
                  </div>

                  <!-- TOMBOL AKSI -->
                  <div class="pt-3 border-t border-gray-100 flex items-center justify-between gap-2">
                    
                    <!-- Edit Button -->
                    <button class="flex-1 bg-[#f0f4f9] hover:bg-gray-200 text-[#080C14] font-semibold py-2.5 px-3 rounded-xl text-xs transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                      <svg class="w-4 h-4 text-gray-700" viewBox="0 0 24 24">
                        <path d="M0 0h24v24H0z" fill="none" />
                        <path fill="currentColor" d="M5 21h14c1.1 0 2-.9 2-2v-7h-2v7H5V5h7V3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2" />
                        <path fill="currentColor" d="M7 13v3c0 .55.45 1 1 1h3c.27 0 .52-.11.71-.29l9-9a.996.996 0 0 0 0-1.41l-3-3a.996.996 0 0 0-1.41 0l-9.01 8.99A1 1 0 0 0 7 13m10-7.59L18.59 7L17.5 8.09L15.91 6.5zm-8 8l5.5-5.5l1.59 1.59l-5.5 5.5H9z" />
                      </svg>
                      <span>Edit</span>
                    </button>

                    <!-- Galeri/Update Foto Model AI Button -->
                    <button 
                      class="p-2.5 bg-[#f0f4f9] hover:bg-blue-100 text-[#295C93] rounded-xl transition-all cursor-pointer"
                      title="Kelola/Update Foto Model AI"
                    >
                      <svg class="w-4 h-4" viewBox="0 0 20 20">
                        <path d="M0 0h20v20H0z" fill="none" />
                        <path fill="currentColor" d="M16 4h1.96c.57 0 1.04.47 1.04 1.04v12.92c0 .57-.47 1.04-1.04 1.04H5.04C4.47 19 4 18.53 4 17.96V16H2.04C1.47 16 1 15.53 1 14.96V2.04C1 1.47 1.47 1 2.04 1h12.92c.57 0 1.04.47 1.04 1.04zM3 14h11V3H3zm5-8.5C8 4.67 7.33 4 6.5 4S5 4.67 5 5.5S5.67 7 6.5 7S8 6.33 8 5.5m2 4.5s1-5 3-5v8H4V7c2 0 2 3 2 3s.33-2 2-2s2 2 2 2m7 7V6h-1v8.96c0 .57-.47 1.04-1.04 1.04H6v1z" />
                      </svg>
                    </button>

                    <!-- Hapus Produk Button (Khusus Admin) -->
                    <button 
                      v-if="userRole === 'Admin'" 
                      @click="deleteProduct(product.id, product.name)"
                      class="p-2.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-xl transition-all cursor-pointer"
                      title="Hapus Produk"
                    >
                      <svg class="w-4 h-4" viewBox="0 0 24 24">
                        <path d="M0 0h24v24H0z" fill="none" />
                        <path fill="currentColor" d="M6 19a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V7H6zM8 9h8v10H8zm7.5-5l-1-1h-5l-1 1H5v2h14V4z" />
                      </svg>
                    </button>

                  </div>
                </div>

              </div>

            </div>

            <!-- HALAMAN / PAGINATION -->
            <div v-if="filteredProducts.length > 0" class="flex items-center justify-center gap-2 pt-4">
              <button 
                @click="currentPage > 1 && currentPage--"
                :disabled="currentPage === 1"
                class="w-10 h-10 rounded-2xl bg-white border border-gray-200/80 text-gray-400 font-semibold flex items-center justify-center hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed shadow-sm transition-all cursor-pointer"
              >
                ‹
              </button>

              <button 
                v-for="page in totalPages" 
                :key="page"
                @click="currentPage = page"
                :class="currentPage === page ? 'bg-[#295C93] text-white shadow-md' : 'bg-white border border-gray-200/80 text-gray-700 hover:bg-gray-50'"
                class="w-10 h-10 rounded-2xl text-xs font-bold flex items-center justify-center transition-all shadow-sm cursor-pointer"
              >
                {{ page }}
              </button>

              <button 
                @click="currentPage < totalPages && currentPage++"
                :disabled="currentPage === totalPages"
                class="w-10 h-10 rounded-2xl bg-white border border-gray-200/80 text-gray-700 font-semibold flex items-center justify-center hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed shadow-sm transition-all cursor-pointer"
              >
                ›
              </button>
            </div>

          </div>

          <!-- SIDEBAR WIDGET AI & OPERASI MASSAL -->
          <div class="space-y-5">
            
            <!-- Widget Kualitas Data AI -->
            <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-[0_12px_28px_rgba(0,0,0,0.03)] space-y-5">
              <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <h3 class="font-bold text-[#080C14] text-base">Kualitas Data AI</h3>
                <span class="p-1.5 rounded-xl bg-blue-50 text-[#295C93]">
                  <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 2a10 10 0 100 20 10 10 0 000-20zm0 18a8 8 0 110-16 8 8 0 010 16z"/>
                  </svg>
                </span>
              </div>

              <!-- Indikator Lingkaran -->
              <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-full border-4 border-[#295C93] border-t-transparent flex items-center justify-center font-bold text-sm text-[#080C14]">
                  94%
                </div>
                <div>
                  <h4 class="font-bold text-xs text-[#080C14]">Tingkat Pengenalan</h4>
                  <p class="text-[11px] text-gray-400 font-medium">Model deteksi produk sangat akurat</p>
                </div>
              </div>

              <!-- Bar Progres -->
              <div class="space-y-3 pt-2 text-xs font-semibold">
                <div>
                  <div class="flex justify-between text-gray-600 mb-1">
                    <span>Foto Tampak Depan</span>
                    <span class="text-[#295C93]">100% Siap</span>
                  </div>
                  <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
                    <div class="bg-[#295C93] h-full w-[100%] rounded-full"></div>
                  </div>
                </div>

                <div>
                  <div class="flex justify-between text-gray-600 mb-1">
                    <span>Foto Tampak Samping</span>
                    <span class="text-[#295C93]">92% Siap</span>
                  </div>
                  <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
                    <div class="bg-[#295C93] h-full w-[92%] rounded-full"></div>
                  </div>
                </div>

                <div>
                  <div class="flex justify-between text-gray-600 mb-1">
                    <span>Foto Kemasan Lipat</span>
                    <span class="text-[#295C93]">86% Cukup</span>
                  </div>
                  <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
                    <div class="bg-[#4DA6FF] h-full w-[86%] rounded-full"></div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Panel Operasi Massal (Khusus Admin) -->
            <div v-if="userRole === 'Admin'" class="bg-white p-6 rounded-3xl border border-gray-100 shadow-[0_12px_28px_rgba(0,0,0,0.03)] space-y-4">
              <h3 class="font-bold text-[#080C14] text-base border-b border-gray-100 pb-3">Operasi Massal</h3>

              <div class="space-y-2.5">
                <button class="w-full bg-[#f8fafc] hover:bg-gray-100 text-[#080C14] font-semibold p-3.5 rounded-2xl text-xs transition-all flex items-center justify-between group cursor-pointer">
                  <div class="flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-[#295C93]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    <span>Import CSV Inventaris</span>
                  </div>
                  <span class="text-gray-400 group-hover:translate-x-1 transition-transform">›</span>
                </button>

                <button class="w-full bg-[#f8fafc] hover:bg-gray-100 text-[#080C14] font-semibold p-3.5 rounded-2xl text-xs transition-all flex items-center justify-between group cursor-pointer">
                  <div class="flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-[#295C93]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    <span>Export Data Latihan AI</span>
                  </div>
                  <span class="text-gray-400 group-hover:translate-x-1 transition-transform">›</span>
                </button>
              </div>
            </div>

          </div>

        </div>

      </main>
    </div>

    <Footer />

  </div>
</template>