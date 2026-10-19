<script setup>
import { ref, onMounted } from 'vue'

const products = ref([])
const loading = ref(true)

const newProduct = ref({
  nama_produk: '',
  harga_jual: '',
  stok: '',
  kategori: 'Makanan & Minuman'
})

const fetchProducts = async () => {
  try {
    const res = await fetch('http://localhost/snappos-api/produk.php')
    const data = await res.json()
    products.value = data
  } catch (err) {
    console.error('Gagal mengambil data:', err)
  } finally {
    loading.value = false
  }
}

const addProduct = async () => {
  if (!newProduct.value.nama_produk || !newProduct.value.harga_jual) return alert('Lengkapi nama dan harga!')
  
  try {
    await fetch('http://localhost/snappos-api/produk.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(newProduct.value)
    })
    
    newProduct.value = { nama_produk: '', harga_jual: '', stok: '', kategori: 'Makanan & Minuman' }
    fetchProducts()
  } catch (err) {
    console.error('Gagal menambah produk:', err)
  }
}

onMounted(() => {
  fetchProducts()
})
</script>

<template>
  <div class="min-h-screen bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto space-y-6">
      <!-- Form Tambah Produk -->
      <div class="bg-white rounded-xl shadow-md p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Tambah Produk Baru</h2>
        <form @submit.prevent="addProduct" class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <input v-model="newProduct.nama_produk" type="text" placeholder="Nama Produk" class="border p-2 rounded w-full" required />
          <input v-model="newProduct.harga_jual" type="number" placeholder="Harga Jual" class="border p-2 rounded w-full" required />
          <input v-model="newProduct.stok" type="number" placeholder="Stok" class="border p-2 rounded w-full" required />
          <button type="submit" class="bg-emerald-600 text-white font-bold p-2 rounded hover:bg-emerald-700 transition">
            Simpan
          </button>
        </form>
      </div>

      <!-- Daftar Produk -->
      <div class="bg-white rounded-xl shadow-md p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">SnapPOS - Daftar Produk</h1>

        <div v-if="loading" class="text-gray-500">Memuat data produk...</div>

        <div v-else-if="products.length === 0" class="text-amber-600 bg-amber-50 p-4 rounded-lg">
          Belum ada produk di database.
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div 
            v-for="item in products" 
            :key="item.id" 
            class="border border-gray-200 rounded-lg p-4 hover:shadow-lg transition bg-white"
          >
            <h2 class="font-semibold text-lg text-gray-900">{{ item.nama_produk }}</h2>
            <p class="text-emerald-600 font-bold mt-1">
              Rp {{ Number(item.harga_jual).toLocaleString('id-ID') }}
            </p>
            <div class="flex justify-between items-center text-sm text-gray-500 mt-3 pt-2 border-t border-gray-100">
              <span>Stok: {{ item.stok }}</span>
              <span class="bg-blue-100 text-blue-800 px-2 py-0.5 rounded text-xs">
                {{ item.kategori || 'Umum' }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>