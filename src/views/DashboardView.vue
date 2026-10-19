<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'

import NavbarAdmin from '../components/NavbarAdmin.vue'
import Footer from '../components/Footer.vue'

const router = useRouter()

// Data State
const adminName = ref('Admin')
const storeName = ref('Toko Anda')
const selectedRange = ref('7Hari')
const isReturningUser = ref(false)

// State Pagination Aktivitas Transaksi
const currentPage = ref(1)

// Mock Data Ringkasan Statistik
const stats = ref([
  { title: 'TOTAL PENDAPATAN HARI INI', value: 'Rp 4.850.000', icon: 'money' },
  { title: 'TOTAL TRANSAKSI', value: '128 Transaksi', icon: 'receipt' },
  { title: 'PRODUK TERDAFTAR', value: '0 Item', icon: 'box' }, // Mengambil riil dari backend MySQL
  { title: 'AKURASI DETEKSI AI', value: '99.4%', icon: 'ai' }
])

onMounted(async () => {
  // 1. Ambil nama admin dan toko dari LocalStorage
  const savedUsername = localStorage.getItem('nama_lengkap') || localStorage.getItem('username')
  const savedStore = localStorage.getItem('nama_toko')
  
  if (savedUsername) adminName.value = savedUsername
  if (savedStore) storeName.value = savedStore

  // 2. Cek histori kunjungan user ke dashboard
  const hasVisited = localStorage.getItem('has_visited_dashboard')
  if (hasVisited) {
    isReturningUser.value = true
  } else {
    localStorage.setItem('has_visited_dashboard', 'true')
    isReturningUser.value = false
  }

  // 3. Ambil data asli jumlah produk dari MySQL via API PHP
  try {
    const res = await fetch('http://localhost/snappos-api/produk.php')
    const data = await res.json()
    if (Array.isArray(data)) {
      stats.value[2].value = `${data.length} Item`
    }
  } catch (err) {
    console.error('Gagal memuat jumlah produk:', err)
  }
})

// Fungsi Navigasi ke Halaman Produk
const goToProducts = () => {
  router.push('/products')
}

// Mock Data Grafik Penjualan (7 Hari)
const chartData = ref([
  { day: 'Sen (24/2)', height: 'h-28', val: 'Rp 3.2M' },
  { day: 'Sel (25/2)', height: 'h-32', val: 'Rp 3.8M' },
  { day: 'Rab (26/2)', height: 'h-36', val: 'Rp 4.1M' },
  { day: 'Kam (27/2)', height: 'h-40', val: 'Rp 4.6M' },
  { day: 'Jum (28/2)', height: 'h-44', val: 'Rp 5.2M' },
  { day: 'Sab (01/3)', height: 'h-52', val: 'Rp 6.1M' },
  { day: 'Min (02/3)', height: 'h-48', val: 'Rp 5.8M' }
])

// Mock Data Transaksi Kasir Terkini
const recentTransactions = ref([
  { time: '14:32 WITA', id: '#TRX-24031', cashier: 'Rina A.', items: '4 Item (Minyak, Beras...)', total: 'Rp 148.500', method: 'QRIS', status: 'Sukses' },
  { time: '14:20 WITA', id: '#TRX-24030', cashier: 'Dimas W.', items: '2 Item (Indomie x2, Telur)', total: 'Rp 43.000', method: 'Tunai', status: 'Sukses' },
  { time: '14:15 WITA', id: '#TRX-24029', cashier: 'Rina A.', items: '5 Item (Kopi Kenangan x3...)', total: 'Rp 87.200', method: 'QRIS', status: 'Sukses' },
  { time: '14:02 WITA', id: '#TRX-24028', cashier: 'Reno S.', items: '1 Item (Susu UHT 1L)', total: 'Rp 19.500', method: 'Tunai', status: 'Sukses' },
  { time: '13:51 WITA', id: '#TRX-24027', cashier: 'Dimas W.', items: '8 Item (Susu UHT, Biskuit...)', total: 'Rp 218.000', method: 'QRIS', status: 'Sukses' }
])

// Mock Data Top Produk & Stok
const topProducts = ref([
  { rank: '#1', name: 'Minyak Goreng Sania 2L', sold: 'Terjual 84 pcs hari ini', stockText: 'Sisa 5 Botol', badgeColor: 'bg-red-100 text-red-600', action: '+ Restock Cepat' },
  { rank: '#2', name: 'Kopi Kenangan Mantancino', sold: 'Terjual 48 botol hari ini', stockText: 'Sisa 14 Botol', badgeColor: 'bg-amber-100 text-amber-700', action: '+ Buat Draft PO' },
  { rank: '#3', name: 'Indomie Goreng Spesial 85g', sold: 'Terjual 120 bungkus hari ini', stockText: 'Aman (3 Karton)', badgeColor: 'bg-emerald-100 text-emerald-700', action: 'Stok Cukup' }
])
</script>

<template>
  <div class="min-h-screen bg-[#FAF8FF] font-sans text-gray-800 flex flex-col justify-between">
    
    <div>
      <!-- NAVBAR ADMIN KHUSUS -->
      <NavbarAdmin :storeName="storeName" />

      <!-- AREA BODY TENGAH -->
      <main class="w-full max-w-[1600px] mx-auto px-6 sm:px-10 lg:px-12 py-6 space-y-6">

        <!-- HERO WELCOME -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-gray-100 shadow-[0_12px_30px_rgba(0,0,0,0.03)]">
          <div>
            <h1 class="text-4xl sm:text-5xl font-bold text-[#080C14] tracking-tight">
              Selamat datang<span v-if="isReturningUser"> kembali</span>, <span class="text-[#295C93]">{{ adminName }}</span>
            </h1>
          </div>

          <div class="flex items-center gap-3">
            <button 
              @click="goToProducts" 
              class="bg-[#295C93]/10 text-[#295C93] hover:bg-[#295C93]/20 px-5 py-3 rounded-2xl text-sm font-semibold transition-all flex items-center gap-2 cursor-pointer"
            >
              <span class="text-base">+</span> Tambah Produk
            </button>
          </div>
        </div>

        <!-- CARDS RINGKASAN STATISTIK -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
          <div 
            v-for="(item, idx) in stats" 
            :key="idx" 
            class="bg-white p-5 rounded-3xl border border-gray-100 shadow-[0_10px_25px_rgba(0,0,0,0.03)] flex items-center justify-between hover:scale-[1.01] transition-transform"
          >
            <div>
              <p class="text-xs font-semibold text-gray-400 tracking-wider mb-1.5">{{ item.title }}</p>
              <h3 class="text-2xl font-semibold text-[#080C14]">{{ item.value }}</h3>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-[#295C93]/10 text-[#295C93] flex items-center justify-center flex-shrink-0 p-2.5">
              
              <svg v-if="item.icon === 'money'" class="w-7 h-7" viewBox="0 0 14 14">
                <path d="M0 0h14v14H0z" fill="none" />
                <path fill="currentColor" fill-rule="evenodd" d="M1.249 7.001a5.751 5.751 0 0 1 10.412-3.37l-.515.515A.5.5 0 0 0 11.5 5h2a.5.5 0 0 0 .5-.5v-2a.5.5 0 0 0-.854-.354l-.592.593A7.001 7.001 0 0 0 0 7a.625.625 0 1 0 1.25 0M7 2.875c.345 0 .625.28.625.625v.557a1.79 1.79 0 0 1 1.453 1.18a.625.625 0 1 1-1.178.416a.54.54 0 0 0-.511-.361h-.903a.416.416 0 0 0-.09.823l1.376.3a1.792 1.792 0 0 1-.147 3.528v.557a.625.625 0 1 1-1.25 0v-.557a1.79 1.79 0 0 1-1.453-1.18a.625.625 0 1 1 1.178-.416a.54.54 0 0 0 .511.361h.778a.542.542 0 0 0 .116-1.071L6.13 7.336a1.666 1.666 0 0 1 .245-3.29V3.5c0-.345.28-.625.625-.625M.309 11.962A.5.5 0 0 1 0 11.5v-2A.5.5 0 0 1 .5 9h2a.5.5 0 0 1 .354.854l-.515.514a5.751 5.751 0 0 0 10.412-3.37a.625.625 0 1 1 1.25.001a7.001 7.001 0 0 1-12.555 4.262l-.592.593a.5.5 0 0 1-.545.108" clip-rule="evenodd" />
              </svg>

              <svg v-else-if="item.icon === 'receipt'" class="w-7 h-7" viewBox="0 0 48 48">
                <path d="M0 0h48v48H0z" fill="none" />
                <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="4">
                  <path d="M39 6H9a3 3 0 0 0-3 3v30a3 3 0 0 0 3 3h30a3 3 0 0 0 3-3V9a3 3 0 0 0-3-3" />
                  <path d="m21 31l5 4l8-10M14 15h20m-20 8h8" />
                </g>
              </svg>

              <svg v-else-if="item.icon === 'box'" class="w-7 h-7" viewBox="0 0 2048 2048">
                <path d="M0 0h2048v2048H0z" fill="none" />
                <path fill="currentColor" d="m960 120l832 416v1040l-832 415l-832-415V536zm625 456L960 264L719 384l621 314zM960 888l238-118l-622-314l-241 120zM256 680v816l640 320v-816zm768 1136l640-320V680l-640 320z" />
              </svg>

              <svg v-else class="w-7 h-7" viewBox="0 0 24 24">
                <path d="M0 0h24v24H0z" fill="none" />
                <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.5 16.5c0 .93 0 1.394.077 1.78a4 4 0 0 0 3.143 3.143c.386.077.85.077 1.78.077m14-5c0 .93 0 1.394-.077 1.78a4 4 0 0 1-3.143 3.143c-.386.077-.85.077-1.78.077m5-14c0-.93 0-1.394-.077-1.78a4 4 0 0 0-3.143-3.143c-.386-.077-.85-.077-1.78-.077m-14 5c0-.93 0-1.394.077-1.78A4 4 0 0 1 5.72 2.577C6.106 2.5 6.57 2.5 7.5 2.5m4.5 6v-2m-2 5v.5m4-.5v.5m-3-3.5h2c1.886 0 2.828 0 3.414.586S17 10.614 17 12.5s0 2.828-.586 3.414s-1.528.586-3.414.586h-2c-1.886 0-2.828 0-3.414-.586S7 14.386 7 12.5s0-2.828.586-3.414S9.114 8.5 11 8.5" />
              </svg>

            </div>
          </div>
        </div>

        <!-- GRAFIK PENJUALAN UTAMA -->
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-[0_12px_30px_rgba(0,0,0,0.03)] space-y-5">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-100 pb-4">
            <div>
              <div class="flex items-center gap-2.5">
                <span class="w-3 h-3 rounded-full bg-[#295C93]"></span>
                <h2 class="font-semibold text-[#080C14] text-lg">Grafik Penjualan</h2>
              </div>
              <p class="text-sm text-gray-500 font-medium mt-1 ml-5">Tren total volume penjualan per hari</p>
            </div>

            <div class="flex bg-[#f1f5f9] p-1.5 rounded-xl border border-gray-200/60 self-start sm:self-auto">
              <button 
                @click="selectedRange = 'HariIni'"
                :class="selectedRange === 'HariIni' ? 'bg-white text-[#295C93] shadow-sm font-semibold' : 'text-gray-500 font-medium hover:text-gray-700'"
                class="px-4 py-2 rounded-lg text-xs transition-all"
              >
                Hari Ini
              </button>
              <button 
                @click="selectedRange = '7Hari'"
                :class="selectedRange === '7Hari' ? 'bg-white text-[#295C93] shadow-sm font-semibold' : 'text-gray-500 font-medium hover:text-gray-700'"
                class="px-4 py-2 rounded-lg text-xs transition-all"
              >
                7 Hari
              </button>
              <button 
                @click="selectedRange = '30Hari'"
                :class="selectedRange === '30Hari' ? 'bg-white text-[#295C93] shadow-sm font-semibold' : 'text-gray-500 font-medium hover:text-gray-700'"
                class="px-4 py-2 rounded-lg text-xs transition-all"
              >
                30 Hari
              </button>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4 max-w-lg">
            <div class="bg-[#f8fafc] p-4 rounded-2xl border border-gray-100">
              <span class="text-xs font-semibold text-gray-400 block">Rata-rata Harian</span>
              <span class="text-lg font-semibold text-[#080C14]">Rp 4.410.000</span>
            </div>
            <div class="bg-[#f8fafc] p-4 rounded-2xl border border-gray-100">
              <span class="text-xs font-semibold text-gray-400 block">Puncak Penjualan</span>
              <span class="text-lg font-semibold text-[#295C93]">Sabtu (Rp 6.1M)</span>
            </div>
          </div>

          <div class="pt-4 pb-2">
            <div class="h-60 flex items-end justify-between gap-4 px-4 border-b border-gray-200/80 pb-2">
              <div 
                v-for="(bar, i) in chartData" 
                :key="i" 
                class="flex-1 flex flex-col items-center gap-2 group relative"
              >
                <span class="opacity-0 group-hover:opacity-100 transition-opacity bg-[#080C14] text-white text-xs font-semibold py-1.5 px-3 rounded-xl absolute -top-10 whitespace-nowrap shadow-lg">
                  {{ bar.val }}
                </span>

                <div 
                  :class="bar.height"
                  class="w-full max-w-[52px] rounded-t-xl transition-all duration-300 cursor-pointer bg-gradient-to-t from-[#080C14] via-[#295C93] to-[#4DA6FF] shadow-[0_6px_15px_rgba(41,92,147,0.3)] hover:opacity-90 hover:scale-[1.02]"
                ></div>
              </div>
            </div>

            <div class="flex justify-between gap-4 px-4 pt-4 text-xs font-semibold text-gray-500">
              <span v-for="(bar, i) in chartData" :key="i" class="flex-1 text-center">
                {{ bar.day }}
              </span>
            </div>
          </div>

          <div class="flex items-center justify-between text-xs pt-1">
            <div class="flex items-center gap-2">
              <span class="w-3.5 h-3.5 rounded-md bg-[#295C93]"></span>
              <span class="font-semibold text-gray-600 text-xs">Volume Penjualan</span>
            </div>
            <a href="#" class="text-[#295C93] font-semibold hover:underline text-xs flex items-center gap-1.5">
              <span>Unduh Laporan (Format XLS/PDF)</span>
              
              <svg class="w-4 h-4 text-[#295C93]" viewBox="0 0 24 24">
                <path d="M0 0h24v24H0z" fill="none" />
                <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15V3m0 12l-4-4m4 4l4-4M2 17l.621 2.485A2 2 0 0 0 4.561 21h14.877a2 2 0 0 0 1.94-1.515L22 17" />
              </svg>
            </a>
          </div>
        </div>

        <!-- GRID BAWAH -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
          
          <!-- TABEL TRANSAKSI KASIR TERKINI -->
          <div class="lg:col-span-2 bg-white p-6 rounded-3xl border border-gray-100 shadow-[0_12px_30px_rgba(0,0,0,0.03)] space-y-4 flex flex-col justify-between">
            <div class="space-y-4">
              <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <div class="flex items-center gap-2.5">
                  <svg class="w-6 h-6 text-[#295C93]" viewBox="0 0 16 16">
                    <path d="M0 0h16v16H0z" fill="none" />
                    <path fill="currentColor" d="M7.357 3.545a.75.75 0 0 0-.212-1.485a6.001 6.001 0 1 0 6.794 6.798a.75.75 0 1 0-1.485-.212a4.501 4.501 0 1 1-5.097-5.1M10 2.341a.75.75 0 0 0-.5 1.415q.453.16.856.408a.75.75 0 1 0 .786-1.277A6 6 0 0 0 10 2.341m3.115 2.52a.75.75 0 1 0-1.277.786q.247.4.406.85a.75.75 0 0 0 1.414-.5a6 6 0 0 0-.543-1.136M8 5.75a.75.75 0 0 0-1.5 0v3c0 .414.336.75.75.75h2a.75.75 0 1 0 0-1.5H8z" />
                  </svg>
                  <h2 class="font-semibold text-[#080C14] text-lg">Aktivitas Transaksi Kasir Terkini</h2>
                </div>
              </div>

              <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                  <thead>
                    <tr class="text-xs font-semibold text-gray-400 border-b border-gray-100 uppercase tracking-wider">
                      <th class="py-3 px-2">Waktu / ID</th>
                      <th class="py-3 px-2">Kasir</th>
                      <th class="py-3 px-2">Item Terjual</th>
                      <th class="py-3 px-2">Total</th>
                      <th class="py-3 px-2">Metode</th>
                      <th class="py-3 px-2 text-right">Status</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-gray-100 font-semibold text-gray-700 text-xs">
                    <tr v-for="(trx, idx) in recentTransactions" :key="idx" class="hover:bg-[#f8fafc] transition-colors">
                      <td class="py-3.5 px-2">
                        <div class="font-semibold text-[#080C14] text-xs">{{ trx.time }}</div>
                        <div class="text-xs text-gray-400 font-medium">{{ trx.id }}</div>
                      </td>
                      <td class="py-3.5 px-2 font-semibold text-[#295C93]">{{ trx.cashier }}</td>
                      <td class="py-3.5 px-2 text-gray-600 truncate max-w-[160px]">{{ trx.items }}</td>
                      <td class="py-3.5 px-2 font-semibold text-[#080C14]">{{ trx.total }}</td>
                      <td class="py-3.5 px-2">
                        <span 
                          :class="trx.method === 'QRIS' ? 'bg-indigo-50 text-indigo-600 border-indigo-200' : 'bg-emerald-50 text-emerald-600 border-emerald-200'"
                          class="px-2.5 py-1 rounded-lg border text-xs font-semibold"
                        >
                          {{ trx.method }}
                        </span>
                      </td>
                      <td class="py-3.5 px-2 text-right">
                        <span class="bg-blue-50 text-[#295C93] px-3 py-1 rounded-full text-xs font-semibold border border-blue-100">
                          ● {{ trx.status }}
                        </span>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- PAGINATION -->
            <div class="pt-3 border-t border-gray-100 flex items-center justify-between">
              <span class="text-xs text-gray-400 font-medium">Menampilkan 5 dari 128 transaksi</span>
              
              <div class="flex items-center gap-1.5 ml-auto">
                <button 
                  @click="currentPage > 1 && currentPage--"
                  :disabled="currentPage === 1"
                  class="px-3 py-1.5 rounded-xl border border-gray-200 text-xs font-semibold text-gray-600 hover:bg-gray-100 disabled:opacity-40 disabled:cursor-not-allowed transition-all cursor-pointer"
                >
                  ← Sebelumnya
                </button>
                
                <button class="w-8 h-8 rounded-xl bg-[#295C93] text-white text-xs font-semibold flex items-center justify-center shadow-sm">
                  {{ currentPage }}
                </button>
                
                <button 
                  @click="currentPage++"
                  class="px-3 py-1.5 rounded-xl border border-gray-200 text-xs font-semibold text-gray-600 hover:bg-gray-100 transition-all cursor-pointer"
                >
                  Selanjutnya →
                </button>
              </div>
            </div>

          </div>

          <!-- PERINGATAN STOK & AI INSIGHT -->
          <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-[0_12px_30px_rgba(0,0,0,0.03)] space-y-4 flex flex-col justify-between">
            <div class="space-y-4">
              <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <div class="flex items-center gap-2">
                  <svg class="w-6 h-6 text-amber-500" viewBox="0 0 24 24">
                    <path d="M0 0h24v24H0z" fill="none" />
                    <g fill="none">
                      <path fill="currentColor" d="m8.47 5.56l-5.21 9.48C1.8 17.68 1.08 19 1.66 19.98s2.09.99 5.1.99h10.46c3.02 0 4.53 0 5.11-.99c.58-.98-.15-2.3-1.61-4.95l-5.24-9.48c-1.56-2.8-2.33-4.21-3.51-4.21S10 2.74 8.47 5.56" opacity=".2" />
                      <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m6.72 8.3l-2.3 4.2c-2.18 3.97-3.27 5.95-2.4 7.42s3.13 1.47 7.65 1.47h4.63c4.53 0 6.8 0 7.67-1.47c.87-1.48-.23-3.46-2.42-7.43l-2.32-4.2C14.9 4.1 13.73 2 11.97 2C10.2 2 9.04 4.1 6.72 8.32m5.28-.2v4.83" />
                      <path fill="currentColor" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12.59 17.07a.59.59 0 1 1-1.18 0a.59.59 0 0 1 1.18 0" />
                    </g>
                  </svg>
                  <h2 class="font-semibold text-[#080C14] text-base">Peringatan Stok & AI Insight</h2>
                </div>
                <span class="bg-blue-100/80 text-[#295C93] text-xs font-semibold px-3 py-1 rounded-full">Auto-PO Siap</span>
              </div>

              <div class="bg-[#f0f7ff] p-4 rounded-2xl border border-blue-100 flex items-start gap-3">
                <div class="w-9 h-9 rounded-xl bg-[#295C93] text-white flex items-center justify-center flex-shrink-0 text-base shadow-sm mt-0.5">
                  💡
                </div>
                <div>
                  <h4 class="text-xs font-semibold text-[#080C14]">Rekomendasi Prediktif SnapPOS</h4>
                  <p class="text-xs text-gray-600 mt-1 leading-relaxed font-medium">
                    Tingkat penjualan <span class="font-semibold text-[#080C14]">Minyak Goreng Sania 2L</span> melonjak 42% menjelang akhir pekan. Disarankan menambah Restock PO 24 botol ke Distributor Utama sebelum Kamis sore.
                  </p>
                </div>
              </div>

              <div class="space-y-3">
                <span class="text-xs font-semibold text-gray-400 tracking-wider block uppercase">Top 3 Produk Terlaris & Status Stok</span>

                <div 
                  v-for="(prod, i) in topProducts" 
                  :key="i"
                  class="bg-[#f8fafc] p-3 rounded-2xl border border-gray-100 flex items-center justify-between"
                >
                  <div class="flex items-center gap-3">
                    <span class="text-xs font-semibold text-[#295C93] bg-white w-7 h-7 rounded-lg flex items-center justify-center border border-gray-100 shadow-sm">
                      {{ prod.rank }}
                    </span>
                    <div>
                      <h5 class="text-xs font-semibold text-[#080C14]">{{ prod.name }}</h5>
                      <p class="text-[11px] text-gray-400 font-medium">{{ prod.sold }}</p>
                    </div>
                  </div>

                  <div class="text-right">
                    <span :class="prod.badgeColor" class="px-2.5 py-1 rounded-md text-[10px] font-semibold block mb-0.5">
                      ● {{ prod.stockText }}
                    </span>
                    <button @click="goToProducts" class="text-xs text-[#295C93] font-semibold hover:underline cursor-pointer">
                      {{ prod.action }}
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <button @click="goToProducts" class="w-full bg-[#f1f5f9] hover:bg-gray-200 text-[#080C14] font-semibold py-3.5 px-4 rounded-2xl text-xs transition-all flex items-center justify-center gap-2 cursor-pointer">
              <svg class="w-5 h-5 text-gray-700" viewBox="0 0 32 32">
                <path d="M0 0h32v32H0z" fill="none" />
                <path fill="currentColor" d="M27 16.76v-1.53l1.92-1.68A2 2 0 0 0 29.3 11l-2.36-4a2 2 0 0 0-1.73-1a2 2 0 0 0-.64.1l-2.43.82a11 11 0 0 0-1.31-.75l-.51-2.52a2 2 0 0 0-2-1.61h-4.68a2 2 0 0 0-2 1.61l-.51 2.52a11.5 11.5 0 0 0-1.32.75l-2.38-.86A2 2 0 0 0 6.79 6a2 2 0 0 0-1.73 1L2.7 11a2 2 0 0 0 .41 2.51L5 15.24v1.53l-1.89 1.68A2 2 0 0 0 2.7 21l2.36 4a2 2 0 0 0 1.73 1a2 2 0 0 0 .64-.1l2.43-.82a11 11 0 0 0 1.31.75l.51 2.52a2 2 0 0 0 2 1.61h4.72a2 2 0 0 0 2-1.61l.51-2.52a11.5 11.5 0 0 0 1.32-.75l2.42.82a2 2 0 0 0 .64.1a2 2 0 0 0 1.73-1l2.28-4a2 2 0 0 0-.41-2.51ZM25.21 24l-3.43-1.16a8.9 8.9 0 0 1-2.71 1.57L18.36 28h-4.72l-.71-3.55a9.4 9.4 0 0 1-2.7-1.57L6.79 24l-2.36-4l2.72-2.4a8.9 8.9 0 0 1 0-3.13L4.43 12l2.36-4l3.43 1.16a8.9 8.9 0 0 1 2.71-1.57L13.64 4h4.72l.71 3.55a9.4 9.4 0 0 1 2.7 1.57L25.21 8l2.36 4l-2.72 2.4a8.9 8.9 0 0 1 0 3.13L27.57 20Z" />
                <path fill="currentColor" d="M16 22a6 6 0 1 1 6-6a5.94 5.94 0 0 1-6 6m0-10a3.91 3.91 0 0 0-4 4a3.91 3.91 0 0 0 4 4a3.91 3.91 0 0 0 4-4a3.91 3.91 0 0 0-4-4" />
              </svg>
              <span>Kelola aturan minimum batas stok</span>
            </button>
          </div>

        </div>

      </main>
    </div>

    <Footer />

  </div>
</template>